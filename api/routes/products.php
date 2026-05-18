<?php
// ================================================================
//  ROUTE: /api/products
//  GET    /products               public
//  GET    /products/{id}          public
//  POST   /products               manager, super_admin
//  PUT    /products/{id}          manager, super_admin
//  PATCH  /products/{id}/stock    manager, super_admin, cashier
//  PATCH  /products/{id}/toggle   manager, super_admin
//  DELETE /products/{id}          super_admin, manager
// ================================================================

$m = method();

// Helper: save a base64-encoded image sent from the frontend
function saveBase64Image(string $base64, string $mime): ?string {
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    if (!isset($allowed[$mime])) return null;
    $ext  = $allowed[$mime];
    $dir  = __DIR__ . '/../uploads/products/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $file = uniqid('prod_', true) . '.' . $ext;
    $path = $dir . $file;
    file_put_contents($path, base64_decode($base64));
    return '/uploads/products/' . $file;
}

function fetchProducts(array $where = ['p.status=\'active\''], array $params = []): array {
    $stmt = db()->prepare("
        SELECT p.product_id AS id, p.name, p.description, p.price, p.unit,
               p.stock_qty, p.low_stock_threshold, p.emoji, p.badge,
               p.img_url, p.barcode, p.prep_time_minutes, p.is_available, p.status,
               p.category_id,
               c.name AS category_name, c.type AS category_type, c.slug AS category_slug,
               CASE WHEN p.stock_qty = 0 AND p.stock_qty >= 0 THEN 'out_of_stock'
                    WHEN p.stock_qty > 0 AND p.stock_qty <= p.low_stock_threshold THEN 'low_stock'
                    WHEN p.stock_qty < 0 THEN 'unlimited'
                    ELSE 'in_stock' END AS stock_status
        FROM product p
        JOIN category c ON c.category_id = p.category_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY c.sort_order, p.name
    ");
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    // Attach add-ons
    foreach ($products as &$p) {
        $addons = db()->prepare(
            "SELECT addon_id, name, price, is_required FROM product_addon
             WHERE product_id=? AND status='active'"
        );
        $addons->execute([$p['id']]);
        $p['addons']       = $addons->fetchAll();
        $p['price']        = (float)$p['price'];
        $p['stock_qty']    = (int)$p['stock_qty'];
        $p['is_available'] = (bool)$p['is_available'];
    }
    return $products;
}

if ($m === 'GET' && !$id) {
    $where  = ['p.status=\'active\''];
    $params = [];
    $cat    = queryParam('category_id');
    $type   = queryParam('type');
    $search = queryParam('search', '');
    $badge  = queryParam('badge');
    $avail  = queryParam('available');
    $low    = queryParam('low_stock');   // staff only filter

    if ($cat)   { $where[] = 'p.category_id=?'; $params[] = (int)$cat; }
    if ($type && in_array($type, Validator::CAT_TYPES, true))
                { $where[] = 'c.type=?';         $params[] = $type; }
    if ($badge && in_array($badge, Validator::BADGES, true))
                { $where[] = 'p.badge=?';         $params[] = $badge; }
    if ($search){ $where[] = 'p.name LIKE ?';     $params[] = "%$search%"; }
    if ($avail === '1') { $where[] = 'p.is_available=1'; }
    if ($low === '1')   { $where[] = 'p.stock_qty > 0 AND p.stock_qty <= p.low_stock_threshold'; }

    respondOk(fetchProducts($where, $params));
}

if ($m === 'GET' && $id) {
    $products = fetchProducts(['p.product_id=?'], [$id]);
    if (!$products) respondError('Product not found', 404);
    respondOk($products[0]);
}

if ($m === 'POST') {
    $auth = requireAuth(['super_admin', 'manager']);
    $b    = body();
    $v    = new Validator($b);
    $v->required('category_id', 'Category')
      ->integer('category_id', 'Category')
      ->exists(db(), 'category', 'category_id', 'category_id', 'Category')
      ->required('name', 'Product name')
      ->minLen('name', 2, 'Product name')
      ->maxLen('name', 255, 'Product name')
      ->required('price', 'Price')
      ->numeric('price')->min('price', 0.01)->max('price', 99999)
      ->numeric('stock_qty', 'Stock quantity')->min('stock_qty', -1);
    if (isset($b['badge']) && $b['badge']) $v->inList('badge', Validator::BADGES, 'Badge');
    if ($v->fails()) respondError($v->first(), 422, $v->errors());

    // Handle base64 image upload from admin.php
    $imgUrl = trim($b['img_url'] ?? '') ?: null;
    if (!empty($b['img_base64']) && !empty($b['img_mime'])) {
        $imgUrl = saveBase64Image($b['img_base64'], $b['img_mime']);
    }

    db()->prepare(
        "INSERT INTO product (category_id, name, description, price, unit, stock_qty,
         low_stock_threshold, emoji, badge, img_url, barcode, prep_time_minutes,
         is_available, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'active')"
    )->execute([
        (int)$b['category_id'],
        trim($b['name']),
        trim($b['description'] ?? ''),
        (float)$b['price'],
        trim($b['unit'] ?? 'each'),
        (int)($b['stock_qty'] ?? 0),
        (int)($b['low_stock_threshold'] ?? 5),
        trim($b['emoji'] ?? ''),
        ($b['badge'] ?? null) ?: null,
        $imgUrl,
        trim($b['barcode'] ?? '') ?: null,
        ($b['prep_time_minutes'] ?? null) ?: null,
    ]);

    $newId = (int)db()->lastInsertId();
    auditLog($auth['user_id'], 'product.create', 'product', $newId);
    respondCreated(['id' => $newId], 'Product created');
}

if ($m === 'PUT' && $id) {
    $auth = requireAuth(['super_admin', 'manager']);
    $b    = body();
    $v    = new Validator($b);
    $v->required('category_id', 'Category')->exists(db(), 'category', 'category_id', 'category_id', 'Category')
      ->required('name', 'Product name')->minLen('name', 2)->maxLen('name', 255)
      ->required('price', 'Price')->numeric('price')->min('price', 0.01)
      ->numeric('stock_qty')->min('stock_qty', -1);
    if (isset($b['badge']) && $b['badge']) $v->inList('badge', Validator::BADGES, 'Badge');
    if ($v->fails()) respondError($v->first(), 422, $v->errors());

    // Keep existing img_url unless a new base64 image is provided
    $existing = db()->prepare("SELECT unit, img_url FROM product WHERE product_id=? LIMIT 1");
    $existing->execute([$id]);
    $existingRow = $existing->fetch();
    $imgUrl = trim($b['img_url'] ?? '') ?: ($existingRow['img_url'] ?? null);
    if (!empty($b['img_base64']) && !empty($b['img_mime'])) {
        $imgUrl = saveBase64Image($b['img_base64'], $b['img_mime']);
    }

    db()->prepare(
        "UPDATE product SET category_id=?, name=?, description=?, price=?, unit=?,
         stock_qty=?, low_stock_threshold=?, emoji=?, badge=?, img_url=?,
         barcode=?, prep_time_minutes=?, is_available=?, status=?
         WHERE product_id=?"
    )->execute([
        (int)$b['category_id'], trim($b['name']), trim($b['description'] ?? ''),
        (float)$b['price'], trim($b['unit'] ?? $existingRow['unit'] ?? 'each'),
        (int)($b['stock_qty'] ?? 0),
        (int)($b['low_stock_threshold'] ?? 5), trim($b['emoji'] ?? ''),
        ($b['badge'] ?? null) ?: null,
        $imgUrl,
        trim($b['barcode'] ?? '') ?: null,
        ($b['prep_time_minutes'] ?? null) ?: null,
        (int)($b['is_available'] ?? 1),
        $b['status'] ?? 'active', $id,
    ]);
    auditLog($auth['user_id'], 'product.update', 'product', $id);
    respondOk([], 'Product updated');
}

// Stock adjustment
if ($m === 'PATCH' && $id && $action === 'stock') {
    $auth = requireAuth(['super_admin', 'manager', 'cashier']);
    $b    = body();
    $v    = new Validator($b);
    $v->required('qty_change', 'Quantity change')->integer('qty_change')
      ->required('change_type')->inList('change_type',
        ['sale','restock','adjustment','damage','return','waste'], 'Change type');
    if ($v->fails()) respondError($v->first(), 422, $v->errors());

    $stmt = db()->prepare("SELECT stock_qty FROM product WHERE product_id=? LIMIT 1");
    $stmt->execute([$id]);
    $prod = $stmt->fetch();
    if (!$prod) respondError('Product not found', 404);

    $before = (int)$prod['stock_qty'];
    $change = (int)$b['qty_change'];
    $after  = $before + $change;

    if ($after < 0 && $before >= 0) respondError('Insufficient stock. Available: ' . $before, 400);

    db()->prepare("UPDATE product SET stock_qty=? WHERE product_id=?")->execute([$after, $id]);
    db()->prepare(
        "INSERT INTO stock_log (product_id, changed_by, change_type, qty_before, qty_change,
         qty_after, reference_id, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    )->execute([
        $id, $auth['user_id'], $b['change_type'],
        $before, $change, $after,
        (int)($b['reference_id'] ?? 0) ?: null,
        trim($b['notes'] ?? ''),
    ]);
    respondOk(['qty_before' => $before, 'qty_after' => $after], 'Stock updated');
}

// Toggle availability (restaurant: quick on/off)
if ($m === 'PATCH' && $id && $action === 'toggle') {
    requireAuth(['super_admin', 'manager', 'cashier']);
    $stmt = db()->prepare("SELECT is_available FROM product WHERE product_id=?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch();
    if (!$prod) respondError('Product not found', 404);
    $new = $prod['is_available'] ? 0 : 1;
    db()->prepare("UPDATE product SET is_available=? WHERE product_id=?")->execute([$new, $id]);
    respondOk(['is_available' => (bool)$new], $new ? 'Product marked available' : 'Product marked unavailable');
}

if ($m === 'DELETE' && $id) {
    $auth = requireAuth(['super_admin', 'manager']);
    db()->prepare("UPDATE product SET status='inactive' WHERE product_id=?")->execute([$id]);
    auditLog($auth['user_id'], 'product.delete', 'product', $id);
    respondOk([], 'Product deactivated');
}

respondError('Products route not found', 404);
