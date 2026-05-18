<?php
// ================================================================
//  ROUTE: /api/deliveries
//  GET    /deliveries             manager/super_admin: all
//  GET    /deliveries?driver_id=N driver: own
//  PATCH  /deliveries/{id}/assign manager: assign driver
//  PATCH  /deliveries/{id}/status driver: update status
//  PATCH  /deliveries/{id}/otp    driver: submit OTP to confirm
// ================================================================

$m    = method();
$auth = requireAuth();

if ($m === 'GET') {
    $driverId = queryParam('driver_id');
    $status   = queryParam('status');
    $where    = ['1=1']; $params = [];

    if ($auth['role'] === 'driver') {
        $where[] = 'd.driver_id=?'; $params[] = $auth['user_id'];
    } elseif ($driverId) {
        requireAuth(['super_admin', 'manager']);
        $where[] = 'd.driver_id=?'; $params[] = (int)$driverId;
    } else {
        requireAuth(['super_admin', 'manager', 'cashier']);
    }

    if ($status) { $where[] = 'd.delivery_status=?'; $params[] = $status; }

    $stmt = db()->prepare("
        SELECT d.*, so.order_ref, so.street, so.city, so.region,
               so.special_notes, so.order_status,
               u.full_name AS customer_name, u.phone AS customer_phone,
               dr.full_name AS driver_name
        FROM delivery d
        JOIN sale_order so ON so.order_id = d.order_id
        JOIN user u        ON u.user_id   = so.user_id
        LEFT JOIN user dr  ON dr.user_id  = d.driver_id
        WHERE " . implode(' AND ', $where) . " AND DATE(d.created_at) = CURDATE() ORDER BY d.created_at DESC
    ");
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    // Strip OTP hash from response
    foreach ($rows as &$r) unset($r['delivery_otp_hash']);
    respondOk($rows);
}

// Assign driver
if ($m === 'PATCH' && $id && $action === 'assign') {
    requireAuth(['super_admin', 'manager']);
    $b        = body();
    $driverId = (int)($b['driver_id'] ?? 0);

    if ($driverId) {
        $chk = db()->prepare("SELECT 1 FROM user WHERE user_id=? AND role='driver' AND status='active'");
        $chk->execute([$driverId]);
        if (!$chk->fetch()) respondError('Driver not found or inactive.', 422);
    }

    db()->prepare(
        "UPDATE delivery SET driver_id=?,
         delivery_status=IF(? IS NOT NULL,'assigned',delivery_status)
         WHERE order_id=?"
    )->execute([$driverId ?: null, $driverId ?: null, $id]);

    if ($driverId) {
        db()->prepare(
            "UPDATE sale_order SET order_status='confirmed'
             WHERE order_id=? AND order_status='pending'"
        )->execute([$id]);
        // Notify driver
        $order = db()->prepare("SELECT order_ref FROM sale_order WHERE order_id=? LIMIT 1");
        $order->execute([$id]);
        $order = $order->fetch();
        pushNotification($driverId, 'delivery',
            '🚗 New Delivery Assigned',
            "You have been assigned delivery for order {$order['order_ref']}.",
            ['order_id' => $id]
        );
    }
    respondOk([], 'Driver assigned');
}

// Driver updates status
if ($m === 'PATCH' && $id && $action === 'status') {
    $auth = requireAuth(['driver', 'manager', 'super_admin']);
    $b    = body();
    $v    = new Validator($b);
    $v->required('delivery_status')
      ->inList('delivery_status', Validator::DEL_STATUS, 'Delivery status');
    if ($v->fails()) respondError($v->first(), 422);

    // Driver can only update their own deliveries
    if ($auth['role'] === 'driver') {
        $chk = db()->prepare("SELECT 1 FROM delivery WHERE order_id=? AND driver_id=?");
        $chk->execute([$id, $auth['user_id']]);
        if (!$chk->fetch()) respondError('Not your delivery.', 403);
    }

    $st  = $b['delivery_status'];
    $now = date('Y-m-d H:i:s');

    db()->prepare("
        UPDATE delivery SET delivery_status=?,
          dispatched_at = IF(?='en_route', ?, dispatched_at),
          arrived_at    = IF(?='arrived',  ?, arrived_at)
        WHERE order_id=?
    ")->execute([$st, $st, $now, $st, $now, $id]);

    if ($st === 'en_route') {
        // Update sale_order status
        db()->prepare("UPDATE sale_order SET order_status='out_for_delivery' WHERE order_id=?")
            ->execute([$id]);

        // Generate OTP and send to customer
        $custRow = db()->prepare("SELECT user_id, order_ref FROM sale_order WHERE order_id=? LIMIT 1");
        $custRow->execute([$id]);
        $custRow = $custRow->fetch();
        $custId  = (int)($custRow['user_id'] ?? 0);
        $ref     = $custRow['order_ref'] ?? '';

        $otp     = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $otpHash = password_hash($otp, PASSWORD_BCRYPT);
        db()->prepare(
            "UPDATE delivery SET delivery_otp_hash=?, delivery_otp=?,
             dispatched_at=COALESCE(dispatched_at, NOW()) WHERE order_id=?"
        )->execute([$otpHash, $otp, $id]);

        if ($custId) pushNotification($custId, 'order_update',
            '🚗 Driver On The Way!',
            "Your driver is heading to you! Your delivery OTP is: $otp. Share it with the driver on arrival.",
            ['order_id' => $id, 'otp' => $otp]
        );
    }

    // Generate OTP when driver arrives
    if ($st === 'arrived') {
        // Update sale_order status to arrived so OTP is exposed and tracking updates
        db()->prepare("UPDATE sale_order SET order_status='arrived' WHERE order_id=?")
            ->execute([$id]);

        $otp     = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $otpHash = password_hash($otp, PASSWORD_BCRYPT);
        db()->prepare("UPDATE delivery SET delivery_otp_hash=?, delivery_otp=? WHERE order_id=?")
            ->execute([$otpHash, $otp, $id]);

        // Notify customer with OTP
        $custRow2 = db()->prepare("SELECT user_id FROM sale_order WHERE order_id=? LIMIT 1");
        $custRow2->execute([$id]);
        $custId = (int)$custRow2->fetchColumn();
        pushNotification((int)$custId, 'delivery',
            '🚗 Driver Has Arrived!',
            "Your driver is outside. Your delivery OTP is: $otp. Share it with the driver.",
            ['order_id' => $id, 'otp' => $otp]
        );
        respondOk(['otp_sent_to_customer' => true], 'Arrived — OTP sent to customer');
    }

    respondOk([], "Delivery status updated to '$st'");
}

// Confirm delivery with OTP
if ($m === 'PATCH' && $id && $action === 'confirm') {
    $auth = requireAuth(['driver']);
    $b    = body();
    $otp  = trim($b['otp'] ?? '');

    if (!preg_match('/^\d{4,6}$/', $otp)) respondError('OTP must be a 4 or 6-digit number.', 400);

    $del = db()->prepare(
        "SELECT delivery_otp_hash, driver_id FROM delivery WHERE order_id=? LIMIT 1"
    );
    $del->execute([$id]);
    $del = $del->fetch();

    if (!$del) respondError('Delivery not found.', 404);
    if ($del['driver_id'] != $auth['user_id']) respondError('Not your delivery.', 403);
    if (!$del['delivery_otp_hash'] || !password_verify($otp, $del['delivery_otp_hash']))
        respondError('Incorrect OTP — please try again.', 400);

    $now = date('Y-m-d H:i:s');
    db()->prepare(
        "UPDATE delivery SET delivery_status='confirmed', otp_verified=1, confirmed_at=?
         WHERE order_id=?"
    )->execute([$now, $id]);
    db()->prepare("UPDATE sale_order SET order_status='delivered' WHERE order_id=?")
        ->execute([$id]);
    db()->prepare(
        "UPDATE payment SET receipt_no=COALESCE(receipt_no,?),
         paid_at=COALESCE(paid_at,NOW()), payment_status='paid' WHERE order_id=?"
    )->execute([generateReceiptNo(), $id]);

    $confirmRow = db()->prepare("SELECT user_id, order_ref FROM sale_order WHERE order_id=? LIMIT 1");
    $confirmRow->execute([$id]);
    $confirmRow = $confirmRow->fetch();
    $custId = (int)($confirmRow['user_id'] ?? 0);
    $ref    = $confirmRow['order_ref'] ?? '';
    if ($custId) pushNotification($custId, 'delivery',
        '📦 Delivery Confirmed!',
        "Your order $ref has been delivered. Thank you!",
        ['order_id' => $id]
    );

    // ── Driver is now available — check queue for next pending delivery ────
    // Find oldest delivery that is still pending (kitchen ready but no driver assigned)
    try {
        $nextDelivery = db()->prepare(
            "SELECT d.order_id, so.order_ref, so.user_id
             FROM delivery d
             JOIN sale_order so ON so.order_id = d.order_id
             WHERE d.delivery_status = 'pending'
               AND d.driver_id IS NULL
               AND so.order_status = 'preparing'
               AND DATE(d.created_at) = CURDATE()
             ORDER BY d.created_at ASC
             LIMIT 1"
        );
        $nextDelivery->execute();
        $next = $nextDelivery->fetch();

        if ($next) {
            $driverId = $del['driver_id'];
            db()->prepare(
                "UPDATE delivery SET driver_id=?, delivery_status='assigned' WHERE order_id=?"
            )->execute([$driverId, $next['order_id']]);

            pushNotification((int)$driverId, 'delivery',
                '📦 New Delivery Assigned',
                "Order {$next['order_ref']} is ready for pickup. Head to the restaurant.",
                ['order_id' => $next['order_id']]
            );
        }
    } catch (Throwable $e) {
        // Queue check failure must not break delivery confirmation
    }

    respondOk([], 'Delivery confirmed — order marked as delivered');
}

respondError('Deliveries route not found', 404);
