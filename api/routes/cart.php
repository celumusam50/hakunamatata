<?php
// ================================================================
//  ROUTE: /api/cart
//  GET    /cart                   customer (own cart)
//  POST   /cart                   customer (add/update item)
//  DELETE /cart/{id}              customer (remove item)
//  DELETE /cart                   customer (clear cart)
// ================================================================
$m = method();
$user = requireAuth(['customer']);

if ($m === 'GET') {
    $stmt = db()->prepare("
        SELECT c.cart_id, c.product_id, c.quantity, c.addon_ids, c.notes, c.updated_at,
               p.name, p.price, p.img_url, p.emoji, p.is_available, p.stock_qty,
               cat.name AS category_name, cat.type AS category_type
        FROM cart c
        JOIN product p  ON p.product_id  = c.product_id
        JOIN category cat ON cat.category_id = p.category_id
        WHERE c.user_id=? ORDER BY c.updated_at DESC
    ");
    $stmt->execute([$user['user_id']]);
    $items = $stmt->fetchAll();
    foreach ($items as &$i) {
        $i['quantity']    = (int)$i['quantity'];
        $i['price']       = (float)$i['price'];
        $i['subtotal']    = round($i['price'] * $i['quantity'], 2);
        $i['is_available']= (bool)$i['is_available'];
        $i['addon_ids']   = json_decode($i['addon_ids'] ?? '[]');
    }
    $total = array_sum(array_column($items, 'subtotal'));
    respondOk(['items' => $items, 'total' => round($total, 2), 'count' => count($items)]);
}

if ($m === 'POST') {
    $b = body();
    $v = new Validator($b);
    $v->required('product_id')->integer('product_id')->min('product_id', 1)
      ->exists(db(), 'product', 'product_id', 'product_id', 'Product')
      ->required('quantity')->integer('quantity')->min('quantity', 1)->max('quantity', 50);
    if ($v->fails()) respondError($v->first(), 422, $v->errors());

    $pid = (int)$b['product_id'];
    $qty = (int)$b['quantity'];

    // Check product available
    $prod = db()->prepare("SELECT stock_qty, is_available, status FROM product WHERE product_id=?");
    $prod->execute([$pid]);
    $prod = $prod->fetch();
    if (!$prod || $prod['status'] !== 'active') respondError('Product not found.', 404);
    if (!$prod['is_available']) respondError('This product is currently unavailable.', 400);
    if ($prod['stock_qty'] >= 0 && $prod['stock_qty'] < $qty)
        respondError('Insufficient stock. Available: ' . $prod['stock_qty'], 400);

    $addonIds = json_encode($b['addon_ids'] ?? []);

    db()->prepare(
        "INSERT INTO cart (user_id, product_id, addon_ids, quantity, notes)
         VALUES (?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE quantity=?, addon_ids=?, notes=?, updated_at=NOW()"
    )->execute([
        $user['user_id'], $pid, $addonIds, $qty, trim($b['notes'] ?? ''),
        $qty, $addonIds, trim($b['notes'] ?? ''),
    ]);
    respondOk([], 'Cart updated');
}

if ($m === 'DELETE' && $id) {
    db()->prepare("DELETE FROM cart WHERE cart_id=? AND user_id=?")->execute([$id, $user['user_id']]);
    respondOk([], 'Item removed from cart');
}

if ($m === 'DELETE' && !$id) {
    db()->prepare("DELETE FROM cart WHERE user_id=?")->execute([$user['user_id']]);
    respondOk([], 'Cart cleared');
}

respondError('Cart route not found', 404);
