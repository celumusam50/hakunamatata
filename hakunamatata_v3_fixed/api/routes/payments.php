<?php
// ================================================================
//  ROUTE: /api/payments
// ================================================================
$m    = method();
$auth = requireAuth();

// ================================================================
//  POST /api/payments/verify
//  Check demo account exists and has enough balance.
//
//  MTN body:  { method:"mtn_mobile_money", phone:"+26876XXXXXX", amount:287.50 }
//  Bank body: { method:"bank_card", card_number:"4111...",
//               cardholder_name:"John Doe", expiry:"12/26", cvv:"123", amount:287.50 }
// ================================================================
if ($m === 'POST' && !$id && $action === 'verify') {
    $b      = body();
    $method = trim($b['method'] ?? '');
    $amount = (float)($b['amount'] ?? 0);

    if ($amount <= 0)
        respondError('Amount must be greater than 0.', 422);
    if (!in_array($method, ['mtn_mobile_money', 'bank_card']))
        respondError('Invalid payment method. Use mtn_mobile_money or bank_card.', 422);

    /* ── MTN Mobile Money ─────────────────────────────────────── */
    if ($method === 'mtn_mobile_money') {
        $phone = preg_replace('/\s+/', '', trim($b['phone'] ?? ''));
        if (!$phone) respondError('Phone number is required.', 422);

        $stmt = db()->prepare(
            "SELECT holder_name, balance
               FROM demo_mobile_money
              WHERE REPLACE(phone,' ','') = ?
                AND is_active = 1
              LIMIT 1"
        );
        $stmt->execute([$phone]);
        $acc = $stmt->fetch();

        if (!$acc)
            respondError('Mobile Money account not found. Please check the number.', 404);

        $bal = (float)$acc['balance'];
        if ($bal < $amount) {
            respond([
                'success' => false,
                'error'   => 'Insufficient balance. Available: E' . number_format($bal, 2)
                           . ', Required: E' . number_format($amount, 2) . '.',
                'data'    => ['holder_name' => $acc['holder_name'],
                              'balance' => $bal, 'sufficient' => false],
            ], 422);
        }
        respondOk(['holder_name' => $acc['holder_name'],
                   'balance' => $bal, 'sufficient' => true], 'Balance verified');
    }

    /* ── Bank Card ────────────────────────────────────────────── */
    $cardNo   = preg_replace('/\s+/', '', trim($b['card_number'] ?? ''));
    $cardName = trim($b['cardholder_name'] ?? '');
    $expiry   = preg_replace('/\s+/', '', trim($b['expiry'] ?? ''));
    $cvv      = trim($b['cvv'] ?? '');

    if (!$cardNo || !$cardName || !$expiry || !$cvv)
        respondError('All card fields are required.', 422);

    $stmt = db()->prepare(
        "SELECT cardholder_name, balance, card_type
           FROM demo_bank_card
          WHERE REPLACE(card_number,' ','') = ?
            AND LOWER(cardholder_name)      = LOWER(?)
            AND REPLACE(REPLACE(expiry_date,'/',''),' ','') = ?
            AND cvv = ?
            AND is_active = 1
          LIMIT 1"
    );
    $stmt->execute([$cardNo, $cardName, $expiry, $cvv]);
    $card = $stmt->fetch();

    if (!$card)
        respondError('Card not found. Please check your card details.', 404);

    $bal = (float)$card['balance'];
    if ($bal < $amount) {
        respond([
            'success' => false,
            'error'   => 'Insufficient balance. Available: E' . number_format($bal, 2)
                       . ', Required: E' . number_format($amount, 2) . '.',
            'data'    => ['holder_name' => $card['cardholder_name'],
                          'balance' => $bal, 'sufficient' => false],
        ], 422);
    }
    respondOk(['holder_name' => $card['cardholder_name'],
               'card_type'   => $card['card_type'],
               'balance'     => $bal,
               'sufficient'  => true], 'Card verified');
}



if ($m === 'GET' && !$id) {
    requireAuth(['super_admin', 'manager', 'cashier']);
    $page    = max(1, (int)queryParam('page', 1));
    $perPage = min(100, (int)queryParam('per_page', 20));
    $method  = queryParam('method');
    $status  = queryParam('status');
    $where   = ['1=1']; $params = [];

    // Map frontend filter values to actual DB enum values:
    // DB stores: 'Cash', 'Mobile Money', 'Credit Card', 'Bank Transfer'
    $methodMap = [
        'cash'         => ['Cash'],
        'mobile_money' => ['Mobile Money'],
        'card'         => ['Credit Card', 'Bank Transfer'],
    ];
    if ($method && isset($methodMap[$method])) {
        $dbMethods = $methodMap[$method];
        $placeholders = implode(',', array_fill(0, count($dbMethods), '?'));
        $where[] = "p.method IN ($placeholders)";
        $params = array_merge($params, $dbMethods);
    } elseif ($method) {
        // fallback: pass raw value
        $where[] = 'p.method=?';
        $params[] = $method;
    }
    if ($status) { $where[] = 'p.payment_status=?';  $params[] = $status; }

    $dateFrom = queryParam('date_from');
    $dateTo   = queryParam('date_to');
    if ($dateFrom) { $where[] = 'p.created_at >= ?'; $params[] = $dateFrom . ' 00:00:00'; }
    if ($dateTo)   { $where[] = 'p.created_at <= ?'; $params[] = $dateTo   . ' 23:59:59'; }

    $sql = "SELECT p.*, so.order_ref, u.full_name AS customer_name
            FROM payment p
            JOIN sale_order so ON so.order_id = p.order_id
            JOIN user u        ON u.user_id   = so.user_id
            WHERE " . implode(' AND ', $where) . " ORDER BY p.created_at DESC";
    respond(paginate(db(), $sql, $params, $page, $perPage));
}

if ($m === 'GET' && $id) {
    $stmt = db()->prepare(
        "SELECT p.*, so.order_ref, u.full_name AS customer_name
         FROM payment p JOIN sale_order so ON so.order_id=p.order_id
         JOIN user u ON u.user_id=so.user_id WHERE p.payment_id=? LIMIT 1"
    );
    $stmt->execute([$id]);
    $pay = $stmt->fetch();
    if (!$pay) respondError('Payment not found', 404);
    respondOk($pay);
}

// Cashier marks CoD as paid
if ($m === 'PATCH' && $id && $action === 'pay') {
    requireAuth(['cashier', 'manager', 'super_admin']);
    $b = body();
    db()->prepare(
        "UPDATE payment SET payment_status='paid', paid_at=NOW(),
         receipt_no=COALESCE(receipt_no,?) WHERE payment_id=?"
    )->execute([generateReceiptNo(), $id]);
    db()->prepare(
        "UPDATE sale_order so JOIN payment p ON p.order_id=so.order_id
         SET so.order_status='confirmed' WHERE p.payment_id=? AND so.order_status='pending'"
    )->execute([$id]);
    respondOk([], 'Payment marked as paid');
}

respondError('Payments route not found', 404);
