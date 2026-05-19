<?php
// ================================================================
//  ROUTE: /api/orders
//  GET    /orders                 staff: all | customer: own
//  GET    /orders/{id}            staff or owner
//  GET    /orders/{id}/items      staff or owner
//  POST   /orders                 customer, cashier (CoD)
//  PATCH  /orders/{id}/status     staff (role-gated)
//  PATCH  /orders/{id}/cancel     customer (own, before preparing)
// ================================================================

$m    = method();
$auth = requireAuth();

function fetchOrder(int $orderId): ?array {
    $stmt = db()->prepare("
        SELECT so.*, u.full_name AS customer_name,
               u.phone AS customer_phone,
               cb.full_name AS cashier_name,
               p.payment_id, p.method AS payment_method, p.payment_status,
               p.reference_no, p.receipt_no, p.paid_at, p.amount AS paid_amount,
               d.delivery_id, d.delivery_status, d.dispatched_at,
               d.arrived_at, d.confirmed_at, d.otp_verified,
               dr.full_name AS driver_name, dr.phone AS driver_phone,
               t.table_number
        FROM sale_order so
        JOIN user u          ON u.user_id    = so.user_id
        LEFT JOIN user cb    ON cb.user_id   = so.served_by
        LEFT JOIN payment p  ON p.order_id   = so.order_id
        LEFT JOIN delivery d ON d.order_id   = so.order_id
        LEFT JOIN user dr    ON dr.user_id   = d.driver_id
        LEFT JOIN restaurant_table t ON t.table_id = so.table_id
        WHERE so.order_id=? LIMIT 1
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) return null;

    $items = db()->prepare("
        SELECT oi.*, GROUP_CONCAT(
            JSON_OBJECT('name', oia.addon_name, 'price', oia.addon_price)
        ) AS addons_json
        FROM order_item oi
        LEFT JOIN order_item_addon oia ON oia.item_id = oi.item_id
        WHERE oi.order_id=? GROUP BY oi.item_id
    ");
    $items->execute([$orderId]);
    $order['items'] = $items->fetchAll();
    $order['otp_verified'] = (bool)$order['otp_verified'];
    return $order;
}

// ── LIST ORDERS ───────────────────────────────────────────────────
if ($m === 'GET' && !$id) {
    $where  = ['1=1']; $params = [];

    // Customers see only their own orders
    if ($auth['role'] === 'customer') {
        $where[] = 'so.user_id=?'; $params[] = $auth['user_id'];
    } else {
        // Staff filters
        $customerId = queryParam('customer_id');
        $driverId   = queryParam('driver_id');
        $channel    = queryParam('channel');
        if ($customerId) { $where[] = 'so.user_id=?';        $params[] = (int)$customerId; }
        if ($driverId)   { $where[] = 'd.driver_id=?';       $params[] = (int)$driverId; }
        if ($channel)    { $where[] = 'so.channel=?';        $params[] = $channel; }
    }

    $status    = queryParam('status');
    $orderType = queryParam('order_type');
    $dateFrom  = queryParam('date_from');
    $dateTo    = queryParam('date_to');
    $ref       = queryParam('ref');

    if ($status)    { $where[] = 'so.order_status=?';  $params[] = $status; }
    if ($orderType) { $where[] = 'so.order_type=?';    $params[] = $orderType; }
    if ($dateFrom)  { $where[] = 'so.created_at>=?';   $params[] = $dateFrom . ' 00:00:00'; }
    if ($dateTo)    { $where[] = 'so.created_at<=?';   $params[] = $dateTo . ' 23:59:59'; }
    if ($ref)       { $where[] = 'so.order_ref LIKE ?';$params[] = "%$ref%"; }

    $page    = max(1, (int)queryParam('page', 1));
    $perPage = min(100, max(10, (int)queryParam('per_page', 20)));

    $sql = "SELECT so.order_id, so.order_ref, so.collection_no, so.order_type, so.channel,
                   so.order_status, so.total_amount, so.created_at,
                   u.full_name AS customer_name,
                   p.method AS payment_method, p.payment_status, p.receipt_no,
                   d.delivery_status, dr.full_name AS driver_name
            FROM sale_order so
            JOIN user u          ON u.user_id    = so.user_id
            LEFT JOIN payment p  ON p.order_id   = so.order_id
            LEFT JOIN delivery d ON d.order_id   = so.order_id
            LEFT JOIN user dr    ON dr.user_id   = d.driver_id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY so.created_at DESC";

    respond(paginate(db(), $sql, $params, $page, $perPage));
}

// ── GET SINGLE ORDER ──────────────────────────────────────────────
if ($m === 'GET' && $id && !$action) {
    $order = fetchOrder($id);
    if (!$order) respondError('Order not found', 404);
    // Customers can only see their own
    if ($auth['role'] === 'customer' && $order['user_id'] != $auth['user_id'])
        respondError('Forbidden', 403);
    respondOk($order);
}

// ── PLACE ORDER ───────────────────────────────────────────────────
if ($m === 'POST' && !$id && $action !== 'checkout') {
    $b = body();
    $v = new Validator($b);
    $v->required('order_type')->inList('order_type', Validator::ORDER_TYPES, 'Order type')
      ->required('channel')->inList('channel', Validator::CHANNELS, 'Channel')
      ->required('payment_method')->inList('payment_method', Validator::PAY_METHODS, 'Payment method');

    // CoD only for cashier
    if (($b['payment_method'] ?? '') === 'Cash on Delivery' && $auth['role'] !== 'cashier')
        respondError('Cash on Delivery is only available at the counter.', 403);

    // Delivery requires address
    if (($b['order_type'] ?? '') === 'delivery') {
        $v->required('city', 'City')->inList('city', Validator::CITIES, 'City')
          ->required('street', 'Street address')->minLen('street', 3)
          ->required('delivery_type')->inList('delivery_type', Validator::DELIVERY_TYPES, 'Delivery type');
    }

    if (empty($b['items']) || !is_array($b['items']))
        respondError('Order must contain at least one item.', 422);

    foreach ($b['items'] as $i => $item) {
        $n = $i + 1;
        if (empty($item['product_id']) || !is_numeric($item['product_id']))
            respondError("Item $n: product_id is required.", 422);
        if (empty($item['quantity']) || (int)$item['quantity'] < 1)
            respondError("Item $n: quantity must be at least 1.", 422);
        if (!isset($item['unit_price']) || (float)$item['unit_price'] <= 0)
            respondError("Item $n: unit_price must be greater than 0.", 422);
    }

    if ($v->fails()) respondError($v->first(), 422, $v->errors());

    // Check and apply discount
    $discountAmount = 0;
    $discountId     = null;
    if (!empty($b['discount_code'])) {
        $ds = db()->prepare(
            "SELECT * FROM discount WHERE code=? AND is_active=1
             AND (valid_until IS NULL OR valid_until > NOW())
             AND (max_uses IS NULL OR uses_count < max_uses) LIMIT 1"
        );
        $ds->execute([trim($b['discount_code'])]);
        $disc = $ds->fetch();
        if ($disc) {
            $sub = (float)($b['subtotal'] ?? 0);
            if ($sub >= $disc['min_order_amount']) {
                $discountId = $disc['discount_id'];
                if ($disc['type'] === 'percentage')
                    $discountAmount = round($sub * $disc['value'] / 100, 2);
                elseif ($disc['type'] === 'fixed')
                    $discountAmount = min($disc['value'], $sub);
                elseif ($disc['type'] === 'free_delivery')
                    $discountAmount = (float)($b['delivery_fee'] ?? 0);
                if ($disc['max_discount_amount'] && $discountAmount > $disc['max_discount_amount'])
                    $discountAmount = $disc['max_discount_amount'];
            }
        }
    }

    $db = db();
    $db->beginTransaction();
    try {
        $isPickup     = $b['order_type'] === 'pickup';
        $ref          = generateOrderRef();
        $collectionNo = $isPickup ? generateCollectionNo() : null;
        $subtotal = (float)($b['subtotal'] ?? 0);
        $delFee   = (float)($b['delivery_fee'] ?? 0);
        $taxRate  = (float)(db()->query(
            "SELECT setting_value FROM system_setting WHERE setting_key='tax_rate'"
        )->fetchColumn() ?: 0);
        $taxAmount = round(($subtotal - $discountAmount) * $taxRate / 100, 2);
        $total     = round($subtotal + $delFee - $discountAmount + $taxAmount, 2);

        $db->prepare(
            "INSERT INTO sale_order (order_ref, collection_no, user_id, served_by, table_id,
             order_type, channel, subtotal, delivery_fee, discount_amount, discount_id,
             tax_amount, tax_rate, total_amount, delivery_type, street, city, region,
             scheduled_at, special_notes, order_status)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        )->execute([
            $ref,
            $collectionNo,
            (int)($b['customer_id'] ?? $auth['user_id']),
            $auth['role'] === 'cashier' ? $auth['user_id'] : null,
            ($b['table_id'] ?? null) ?: null,
            $b['order_type'],
            $b['channel'],
            $subtotal, $delFee, $discountAmount, $discountId,
            $taxAmount, $taxRate, $total,
            ($b['delivery_type'] ?? null) ?: null,
            trim($b['street'] ?? ''), trim($b['city'] ?? ''), trim($b['region'] ?? ''),
            ($b['scheduled_at'] ?? null) ?: null,
            trim($b['special_notes'] ?? ''),
            'confirmed',
        ]);
        $orderId = (int)$db->lastInsertId();

        // Insert items + stock decrement
        foreach ($b['items'] as $item) {
            $pid   = (int)$item['product_id'];
            $qty   = (int)$item['quantity'];
            $price = (float)$item['unit_price'];
            $aTotal= (float)($item['addon_total'] ?? 0);

            // Stock check and decrement (skip if unlimited: stock_qty = -1)
            $prod = $db->prepare(
                "SELECT stock_qty, name FROM product WHERE product_id=? AND status='active' FOR UPDATE"
            );
            $prod->execute([$pid]);
            $prod = $prod->fetch();
            if (!$prod) throw new Exception("Product #$pid not found.");
            if ($prod['stock_qty'] >= 0) {
                if ($prod['stock_qty'] < $qty)
                    throw new Exception("'{$prod['name']}' only has {$prod['stock_qty']} in stock.");
                $db->prepare("UPDATE product SET stock_qty = stock_qty - ? WHERE product_id=?")
                   ->execute([$qty, $pid]);
                $db->prepare(
                    "INSERT INTO stock_log (product_id, changed_by, change_type,
                     qty_before, qty_change, qty_after, reference_id)
                     VALUES (?, ?, 'sale', ?, ?, ?, ?)"
                )->execute([
                    $pid, $auth['user_id'],
                    $prod['stock_qty'], -$qty, $prod['stock_qty'] - $qty, $orderId,
                ]);
            }

            $db->prepare(
                "INSERT INTO order_item (order_id, product_id, product_name_snap,
                 product_img_snap, unit_price_snap, quantity, addon_total, line_total, item_notes)
                 VALUES (?,?,?,?,?,?,?,?,?)"
            )->execute([
                $orderId, $pid, $item['name'] ?? $prod['name'],
                trim($item['img_url'] ?? '') ?: null,
                $price, $qty, $aTotal,
                round(($price + $aTotal) * $qty, 2),
                trim($item['notes'] ?? ''),
            ]);

            $itemId = (int)$db->lastInsertId();

            // Add-ons
            if (!empty($item['addons']) && is_array($item['addons'])) {
                foreach ($item['addons'] as $addon) {
                    $db->prepare(
                        "INSERT INTO order_item_addon (item_id, addon_id, addon_name, addon_price)
                         VALUES (?,?,?,?)"
                    )->execute([
                        $itemId, (int)$addon['addon_id'],
                        $addon['name'] ?? '', (float)($addon['price'] ?? 0),
                    ]);
                }
            }
        }

        // Payment record
        $isCod    = $b['payment_method'] === 'Cash on Delivery';
        $payStatus= $isCod ? 'pending' : 'paid';
        $receiptNo= $isCod ? null : generateReceiptNo();

        $db->prepare(
            "INSERT INTO payment (order_id, amount, method, reference_no, mobile_number,
             payment_status, receipt_no, paid_at, processed_by)
             VALUES (?,?,?,?,?,?,?,?,?)"
        )->execute([
            $orderId, $total,
            $b['payment_method'],
            trim($b['reference_no'] ?? ''),
            trim($b['mobile_number'] ?? ''),
            $payStatus, $receiptNo,
            $isCod ? null : date('Y-m-d H:i:s'),
            $isCod ? $auth['user_id'] : null,
        ]);

        // Delivery record (for delivery orders)
        if ($b['order_type'] === 'delivery') {
            $db->prepare(
                "INSERT INTO delivery (order_id, delivery_status) VALUES (?, 'pending')"
            )->execute([$orderId]);
        }

        // Discount usage tracking
        if ($discountId) {
            $db->prepare(
                "INSERT INTO discount_usage (discount_id, user_id, order_id, amount_saved)
                 VALUES (?,?,?,?)"
            )->execute([$discountId, $auth['user_id'], $orderId, $discountAmount]);
            $db->prepare("UPDATE discount SET uses_count=uses_count+1 WHERE discount_id=?")
               ->execute([$discountId]);
        }

        // Order status log
        $db->prepare(
            "INSERT INTO order_status_log (order_id, changed_by, from_status, to_status, notes)
             VALUES (?,?,NULL,'confirmed','Order placed')"
        )->execute([$orderId, $auth['user_id']]);

        // Clear cart
        $db->prepare("DELETE FROM cart WHERE user_id=?")->execute([$auth['user_id']]);

        $db->commit();

        // Notify customer
        pushNotification($auth['user_id'], 'order_update',
            '✅ Order Confirmed',
            "Your order $ref has been confirmed. We're preparing it now!",
            ['order_id' => $orderId]
        );

        respondCreated([
            'order_id'       => $orderId,
            'order_ref'      => $ref,
            'collection_no'  => $collectionNo,
            'receipt_no'     => $receiptNo,
            'payment_status' => $payStatus,
            'total'          => $total,
        ], 'Order placed successfully');

    } catch (Exception $e) {
        $db->rollBack();
        respondError($e->getMessage(), 422);
    }
}

// ── UPDATE STATUS (staff) ─────────────────────────────────────────
if ($m === 'PATCH' && $id && $action === 'status') {
    $auth = requireAuth(['super_admin', 'manager', 'cashier', 'driver']);
    $b    = body();
    $v    = new Validator($b);
    $v->required('status')->inList('status', Validator::ORDER_STATUS, 'Status');
    if ($v->fails()) respondError($v->first(), 422);

    $order = db()->prepare(
        "SELECT order_id, order_status, user_id FROM sale_order WHERE order_id=? LIMIT 1"
    );
    $order->execute([$id]);
    $order = $order->fetch();
    if (!$order) respondError('Order not found', 404);

    $old = $order['order_status'];
    $new = $b['status'];

    db()->prepare("UPDATE sale_order SET order_status=? WHERE order_id=?")->execute([$new, $id]);
    db()->prepare(
        "INSERT INTO order_status_log (order_id, changed_by, from_status, to_status, notes)
         VALUES (?,?,?,?,?)"
    )->execute([$id, $auth['user_id'], $old, $new, trim($b['notes'] ?? '')]);

    // Auto-generate receipt on delivered/completed
    if (in_array($new, ['delivered', 'completed'])) {
        db()->prepare(
            "UPDATE payment SET receipt_no=COALESCE(receipt_no,?), paid_at=COALESCE(paid_at,NOW()),
             payment_status='paid' WHERE order_id=?"
        )->execute([generateReceiptNo(), $id]);
    }

    // Notify customer
    $messages = [
        'preparing'  => ['🍳 Order Being Prepared', 'Your order is being prepared!'],
        'ready'      => ['✅ Order Ready', 'Your order is ready for pickup/dispatch!'],
        'dispatched' => ['🚗 Order On Its Way', 'Your driver is heading to you now!'],
        'delivered'  => ['📦 Order Delivered', 'Your order has been delivered. Enjoy!'],
        'completed'  => ['🎉 Order Completed', 'Thank you for dining with us!'],
        'cancelled'  => ['❌ Order Cancelled', 'Your order has been cancelled.'],
    ];
    if (isset($messages[$new]))
        pushNotification($order['user_id'], 'order_update', ...$messages[$new],
                         ['order_id' => $id]);

    respondOk([], "Order status updated to '$new'");
}

// ── CANCEL ORDER (customer self-service) ──────────────────────────
if ($m === 'PATCH' && $id && $action === 'cancel') {
    $auth = requireAuth(['customer']);
    $order = db()->prepare(
        "SELECT * FROM sale_order WHERE order_id=? AND user_id=? LIMIT 1"
    );
    $order->execute([$id, $auth['user_id']]);
    $order = $order->fetch();
    if (!$order) respondError('Order not found', 404);

    if (!in_array($order['order_status'], ['pending','confirmed']))
        respondError('Order cannot be cancelled — it is already being prepared.', 400);

    db()->prepare("UPDATE sale_order SET order_status='cancelled' WHERE order_id=?")->execute([$id]);
    db()->prepare(
        "UPDATE payment SET payment_status='refunded' WHERE order_id=?"
    )->execute([$id]);
    db()->prepare(
        "INSERT INTO order_status_log (order_id, changed_by, from_status, to_status, notes)
         VALUES (?,?,'confirmed','cancelled','Customer cancelled')"
    )->execute([$id, $auth['user_id']]);

    respondOk([], 'Order cancelled successfully');
}


// ================================================================
//  POST /api/orders/checkout
//  Web checkout that reads the user's cart from DB.
//
//  Required body fields:
//    delivery_address   string
//    payment_method     "mtn_mobile_money" | "bank_card"
//
//  MTN:   mtn_number  "+26876XXXXXX"
//  Bank:  card_number, card_name, card_expiry, card_cvv
//
//  Optional:
//    delivery_instructions  string
// ================================================================
if ($m === 'POST' && !$id && $action === 'checkout') {
    try {
    $b             = body();
    $deliveryAddr  = trim($b['delivery_address'] ?? '');
    $payMethod     = trim($b['payment_method'] ?? '');
    $deliveryInstr = trim($b['delivery_instructions'] ?? '');

    if (strlen($deliveryAddr) < 4)
        respondError('Delivery address is required (min 4 characters).', 422);
    if (!in_array($payMethod, ['mtn_mobile_money', 'bank_card']))
        respondError('payment_method must be mtn_mobile_money or bank_card.', 422);

    /* ── Load cart ─────────────────────────────────────────────── */
    $cartStmt = db()->prepare(
        "SELECT c.cart_id, c.product_id, c.quantity, c.addon_ids, c.notes,
                p.name, p.price, p.stock_qty, p.is_available, p.img_url
           FROM cart c
           JOIN product p ON p.product_id = c.product_id
          WHERE c.user_id = ?
            AND p.status  = 'active'
          ORDER BY c.added_at ASC"
    );
    $cartStmt->execute([$auth['user_id']]);
    $cartRows = $cartStmt->fetchAll();

    if (empty($cartRows))
        respondError('Your cart is empty.', 422);

    foreach ($cartRows as $row)
        if (!$row['is_available'])
            respondError("'{$row['name']}' is currently unavailable.", 422);

    /* ── Calculate totals ──────────────────────────────────────── */
    $taxRate  = (float)(db()->query(
        "SELECT setting_value FROM system_setting WHERE setting_key='tax_rate'"
    )->fetchColumn() ?: 15);

    $subtotal = 0.0;
    $itemData = [];

    foreach ($cartRows as $row) {
        $addonAmt = 0.0;
        $addons   = [];
        if (!empty($row['addon_ids'])) {
            $ids = json_decode($row['addon_ids'], true) ?? [];
            if ($ids) {
                $ph    = implode(',', array_fill(0, count($ids), '?'));
                $aStmt = db()->prepare(
                    "SELECT addon_id, name, price FROM product_addon
                      WHERE addon_id IN ($ph) AND status='active'"
                );
                $aStmt->execute($ids);
                $addons = $aStmt->fetchAll();
                foreach ($addons as $a) $addonAmt += (float)$a['price'];
            }
        }
        $subtotal  += ($row['price'] + $addonAmt) * $row['quantity'];
        $itemData[] = array_merge($row, ['addonAmt' => $addonAmt, 'addons' => $addons]);
    }

    $taxAmt = round($subtotal * $taxRate / 100, 2);
    $total  = round($subtotal + $taxAmt, 2);

    $dbMethodMap = ['mtn_mobile_money' => 'Mobile Money', 'bank_card' => 'Credit Card'];
    $dbMethod    = $dbMethodMap[$payMethod];

    /* ── Transaction ───────────────────────────────────────────── */
    $db = db();
    $db->beginTransaction();
    try {

        // 1. Stock check
        foreach ($itemData as $row) {
            if ($row['stock_qty'] >= 0) {
                $s = $db->prepare(
                    "SELECT stock_qty FROM product WHERE product_id=? FOR UPDATE"
                );
                $s->execute([(int)$row['product_id']]);
                $stock = (int)$s->fetchColumn();
                if ($stock < $row['quantity'])
                    throw new Exception(
                        "'{$row['name']}' only has {$stock} in stock."
                    );
            }
        }

        // 2. Create sale_order
        $ref = generateOrderRef();
        $db->prepare(
            "INSERT INTO sale_order
             (order_ref, user_id, order_type, channel,
              subtotal, tax_amount, tax_rate, total_amount,
              delivery_type, street, special_notes, order_status)
             VALUES (?,?,'delivery','web',?,?,?,?,'Standard',?,?,'confirmed')"
        )->execute([
            $ref, $auth['user_id'],
            round($subtotal, 2), $taxAmt, $taxRate, $total,
            $deliveryAddr, $deliveryInstr,
        ]);
        $orderId = (int)$db->lastInsertId();

        // 3. Insert order items + decrement stock
        foreach ($itemData as $row) {
            $pid   = (int)$row['product_id'];
            $qty   = (int)$row['quantity'];
            $price = (float)$row['price'];
            $aTot  = (float)$row['addonAmt'];

            if ($row['stock_qty'] >= 0) {
                $db->prepare(
                    "UPDATE product SET stock_qty = stock_qty - ? WHERE product_id=?"
                )->execute([$qty, $pid]);
                $db->prepare(
                    "INSERT INTO stock_log
                     (product_id,changed_by,change_type,qty_before,qty_change,qty_after,reference_id)
                     VALUES (?,?,'sale',?,?,?,?)"
                )->execute([
                    $pid, $auth['user_id'],
                    $row['stock_qty'], -$qty, $row['stock_qty'] - $qty, $orderId,
                ]);
            }

            $db->prepare(
                "INSERT INTO order_item
                 (order_id,product_id,product_name_snap,product_img_snap,
                  unit_price_snap,quantity,addon_total,line_total,item_notes)
                 VALUES (?,?,?,?,?,?,?,?,?)"
            )->execute([
                $orderId, $pid, $row['name'], $row['img_url'] ?: null,
                $price, $qty, $aTot, round(($price + $aTot) * $qty, 2),
                trim($row['notes'] ?? ''),
            ]);
            $itemId = (int)$db->lastInsertId();

            foreach ($row['addons'] as $a)
                $db->prepare(
                    "INSERT INTO order_item_addon
                     (item_id,addon_id,addon_name,addon_price) VALUES (?,?,?,?)"
                )->execute([$itemId, $a['addon_id'], $a['name'], $a['price']]);
        }

        // 4. Payment record
        $receiptNo = generateReceiptNo();
        $db->prepare(
            "INSERT INTO payment
             (order_id,amount,method,mobile_number,payment_status,receipt_no,paid_at)
             VALUES (?,?,?,?,'paid',?,NOW())"
        )->execute([
            $orderId, $total, $dbMethod,
            $payMethod === 'mtn_mobile_money'
                ? preg_replace('/\s+/', '', trim($b['mtn_number'] ?? ''))
                : null,
            $receiptNo,
        ]);

        // 5. Delivery record
        $db->prepare(
            "INSERT INTO delivery (order_id,delivery_status) VALUES (?,'pending')"
        )->execute([$orderId]);

        // 6. Demo debit (customer balance goes DOWN)
        $txnBase = 'TXN-' . date('Ymd') . '-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);

        if ($payMethod === 'mtn_mobile_money') {
            $phone = preg_replace('/\s+/', '', trim($b['mtn_number'] ?? ''));
            $acc   = $db->prepare(
                "SELECT momo_id, balance FROM demo_mobile_money
                  WHERE REPLACE(phone,' ','') = ? AND is_active=1 LIMIT 1 FOR UPDATE"
            );
            $acc->execute([$phone]);
            $acc = $acc->fetch();

            if (!$acc || (float)$acc['balance'] < $total)
                throw new Exception(
                    'Insufficient Mobile Money balance. Available: E'
                    . number_format((float)($acc['balance'] ?? 0), 2)
                    . ', Required: E' . number_format($total, 2) . '.'
                );

            $bBefore = (float)$acc['balance'];
            $bAfter  = round($bBefore - $total, 2);
            $db->prepare(
                "UPDATE demo_mobile_money SET balance=? WHERE momo_id=?"
            )->execute([$bAfter, $acc['momo_id']]);
            $db->prepare(
                "INSERT INTO payment_txn_log
                 (order_id,txn_ref,method,account_ref,amount,direction,balance_before,balance_after)
                 VALUES (?,?,'momo',?,?,'debit',?,?)"
            )->execute([
                $orderId, $txnBase . '-D', substr($phone, -4),
                $total, $bBefore, $bAfter,
            ]);

        } else {
            $cardNo   = preg_replace('/\s+/', '', trim($b['card_number'] ?? ''));
            $cardName = trim($b['card_name'] ?? '');
            $expiry   = preg_replace('/\s+/', '', trim($b['card_expiry'] ?? ''));
            $cvv      = trim($b['card_cvv'] ?? '');

            $card = $db->prepare(
                "SELECT card_number, balance FROM demo_bank_card
                  WHERE REPLACE(card_number,' ','') = ?
                    AND LOWER(cardholder_name) = LOWER(?)
                    AND REPLACE(expiry_date,' ','') = ?
                    AND cvv = ?
                    AND is_active = 1
                  LIMIT 1 FOR UPDATE"
            );
            $card->execute([$cardNo, $cardName, $expiry, $cvv]);
            $card = $card->fetch();

            if (!$card || (float)$card['balance'] < $total)
                throw new Exception(
                    'Insufficient card balance. Available: E'
                    . number_format((float)($card['balance'] ?? 0), 2)
                    . ', Required: E' . number_format($total, 2) . '.'
                );

            $bBefore = (float)$card['balance'];
            $bAfter  = round($bBefore - $total, 2);
            $db->prepare(
                "UPDATE demo_bank_card SET balance=? WHERE card_number=?"
            )->execute([$bAfter, $card['card_number']]);
            $db->prepare(
                "INSERT INTO payment_txn_log
                 (order_id,txn_ref,method,account_ref,amount,direction,balance_before,balance_after)
                 VALUES (?,?,'bank',?,?,'debit',?,?)"
            )->execute([
                $orderId, $txnBase . '-D',
                'xxxx' . substr($cardNo, -4),
                $total, $bBefore, $bAfter,
            ]);
        }

        // 7. Credit company wallet (company balance goes UP)
        $wallet = $db->prepare(
            "SELECT wallet_id, balance FROM company_wallet WHERE wallet_id=1 FOR UPDATE"
        );
        $wallet->execute();
        $wallet = $wallet->fetch();
        if ($wallet) {
            $wBefore = (float)$wallet['balance'];
            $wAfter  = round($wBefore + $total, 2);
            $db->prepare(
                "UPDATE company_wallet SET balance=? WHERE wallet_id=1"
            )->execute([$wAfter]);
            $db->prepare(
                "INSERT INTO payment_txn_log
                 (order_id,txn_ref,method,account_ref,amount,direction,balance_before,balance_after)
                 VALUES (?,?,?,?,?,'credit',?,?)"
            )->execute([
                $orderId, $txnBase . '-C',
                $payMethod === 'mtn_mobile_money' ? 'momo' : 'bank',
                'company-wallet',
                $total, $wBefore, $wAfter,
            ]);
        }

        // 8. Status log + clear cart
        $db->prepare(
            "INSERT INTO order_status_log
             (order_id,changed_by,from_status,to_status,notes)
             VALUES (?,?,NULL,'confirmed','Web checkout — demo payment')"
        )->execute([$orderId, $auth['user_id']]);

        $db->prepare("DELETE FROM cart WHERE user_id=?")->execute([$auth['user_id']]);
        $db->commit();

        pushNotification(
            $auth['user_id'], 'order_update',
            '✅ Order Confirmed',
            "Your order $ref has been confirmed and payment processed!",
            ['order_id' => $orderId]
        );

        respondCreated([
            'order_id'   => $orderId,
            'order_ref'  => $ref,
            'receipt_no' => $receiptNo,
            'total'      => $total,
        ], 'Order placed and payment processed successfully');

    } catch (Throwable $e) {
        try { $db->rollBack(); } catch (Throwable $ignored) { /* already rolled back by MySQL */ }
        throw $e;  // re-throw to outer catch which returns JSON
    }

    } catch (Throwable $outerErr) {
        respondError($outerErr->getMessage() ?: 'Checkout failed — please try again.', 422);
    }
}

respondError('Orders route not found', 404);
