<?php
// ================================================================
//  ROUTE: /api/reports
//  GET /reports/sales             manager, super_admin
//  GET /reports/products          manager, super_admin
//  GET /reports/customers         manager, super_admin
//  GET /reports/drivers           manager, super_admin
//  GET /reports/cashiers          manager, super_admin
//  GET /reports/payments          manager, super_admin
//  GET /reports/stock-alerts      manager, super_admin, cashier
//  GET /reports/summary           manager, super_admin   ← NEW
//  GET /reports/dashboard         manager, super_admin
// ================================================================

requireAuth(['super_admin', 'manager']);

$report   = $action ?? $parts[1] ?? '';
$dateFrom = queryParam('date_from', date('Y-m-01'));   // default: first of month
$dateTo   = queryParam('date_to',   date('Y-m-d'));    // default: today
$channel  = queryParam('channel');
$type     = queryParam('order_type');


// ================================================================
//  GET /api/reports/dashboard
// ================================================================
if ($report === 'dashboard') {
    $today = date('Y-m-d');
    $db    = db();

    $revRow = $db->prepare(
        "SELECT COALESCE(SUM(so.total_amount),0) AS rev, COUNT(*) AS cnt
         FROM sale_order so
         WHERE DATE(so.created_at)=?
           AND so.order_status NOT IN ('cancelled','refunded')"
    );
    $revRow->execute([$today]);
    $revRow = $revRow->fetch();

    $pending = $db->prepare(
        "SELECT COUNT(*) FROM sale_order
         WHERE DATE(created_at)=? AND order_status IN ('pending','confirmed')"
    );
    $pending->execute([$today]);

    $ready = $db->prepare(
        "SELECT COUNT(*) FROM sale_order
         WHERE DATE(created_at)=? AND order_status='preparing'"
    );
    $ready->execute([$today]);

    $alerts = $db->prepare(
        "SELECT product_id, name, stock_qty, low_stock_threshold
         FROM product
         WHERE status='active' AND stock_qty >= 0 AND stock_qty <= low_stock_threshold
         ORDER BY stock_qty ASC LIMIT 20"
    );
    $alerts->execute();

    $recent = $db->prepare(
        "SELECT so.order_id, so.order_ref, so.order_status, so.total_amount,
                so.order_type, so.created_at,
                u.full_name AS customer_name
         FROM sale_order so
         JOIN user u ON u.user_id = so.user_id
         ORDER BY so.created_at DESC LIMIT 10"
    );
    $recent->execute();

    respondOk([
        'today_revenue'  => (float)$revRow['rev'],
        'today_orders'   => (int)$revRow['cnt'],
        'pending_orders' => (int)$pending->fetchColumn(),
        'ready_orders'   => (int)$ready->fetchColumn(),
        'stock_alerts'   => $alerts->fetchAll(),
        'recent_orders'  => $recent->fetchAll(),
    ]);
}


// ================================================================
//  GET /api/reports/summary
//  Returns KPI snapshot for the selected date range — used by the
//  report header cards.
// ================================================================
if ($report === 'summary') {
    $db     = db();
    $params = [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'];

    // Revenue & orders
    $rev = $db->prepare(
        "SELECT
            COUNT(DISTINCT so.order_id)                              AS total_orders,
            COALESCE(SUM(so.total_amount),0)                        AS net_revenue,
            COALESCE(SUM(so.subtotal),0)                            AS gross_sales,
            COALESCE(SUM(so.discount_amount),0)                     AS total_discounts,
            COALESCE(SUM(so.delivery_fee),0)                        AS delivery_revenue,
            COUNT(DISTINCT CASE WHEN so.order_status IN ('delivered','completed')
                                THEN so.order_id END)               AS completed_orders,
            COUNT(DISTINCT CASE WHEN so.order_status='cancelled'
                                THEN so.order_id END)               AS cancelled_orders,
            COUNT(DISTINCT CASE WHEN so.order_type='delivery'
                                THEN so.order_id END)               AS delivery_orders,
            COUNT(DISTINCT CASE WHEN so.order_type='pickup'
                                THEN so.order_id END)               AS pickup_orders,
            COUNT(DISTINCT CASE WHEN so.order_type='dine_in'
                                THEN so.order_id END)               AS dine_in_orders
         FROM sale_order so
         WHERE so.created_at BETWEEN ? AND ?
           AND so.order_status NOT IN ('cancelled','refunded')"
    );
    $rev->execute($params);
    $kpi = $rev->fetch();

    // Payment method breakdown
    $pay = $db->prepare(
        "SELECT p.method, COUNT(*) AS txn_count, COALESCE(SUM(p.amount),0) AS total
         FROM payment p
         JOIN sale_order so ON so.order_id = p.order_id
         WHERE p.created_at BETWEEN ? AND ?
           AND p.payment_status = 'paid'
         GROUP BY p.method"
    );
    $pay->execute($params);
    $payBreakdown = $pay->fetchAll();

    // Top product
    $topProd = $db->prepare(
        "SELECT oi.product_name_snap AS name, SUM(oi.line_total) AS revenue
         FROM order_item oi
         JOIN sale_order so ON so.order_id = oi.order_id
         WHERE so.created_at BETWEEN ? AND ?
           AND so.order_status NOT IN ('cancelled','refunded')
         GROUP BY oi.product_id, oi.product_name_snap
         ORDER BY revenue DESC LIMIT 1"
    );
    $topProd->execute($params);
    $topProduct = $topProd->fetch();

    // New customers in period
    $newCust = $db->prepare(
        "SELECT COUNT(*) FROM user WHERE role='customer' AND created_at BETWEEN ? AND ?"
    );
    $newCust->execute($params);

    respondOk(array_merge($kpi, [
        'new_customers'   => (int)$newCust->fetchColumn(),
        'payment_methods' => $payBreakdown,
        'top_product'     => $topProduct ?: null,
        'avg_order_value' => $kpi['total_orders'] > 0
            ? round($kpi['net_revenue'] / $kpi['total_orders'], 2) : 0,
    ]));
}


// ================================================================
//  GET /api/reports/sales
// ================================================================
if ($report === 'sales') {
    $where  = ['sale_date BETWEEN ? AND ?'];
    $params = [$dateFrom, $dateTo];
    if ($channel) { $where[] = 'channel=?'; $params[] = $channel; }
    if ($type)    { $where[] = 'order_type=?'; $params[] = $type; }
    $stmt = db()->prepare(
        "SELECT * FROM v_sales_report WHERE " . implode(' AND ', $where) . " ORDER BY sale_date DESC"
    );
    $stmt->execute($params);
    respondOk($stmt->fetchAll());
}


// ================================================================
//  GET /api/reports/products
//  Now supports date range filtering
// ================================================================
if ($report === 'products') {
    $catType = queryParam('category_type');
    $where   = ['so.created_at BETWEEN ? AND ?',
                "so.order_status NOT IN ('cancelled','refunded')"];
    $params  = [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'];
    if ($catType) { $where[] = 'c.type=?'; $params[] = $catType; }

    $stmt = db()->prepare(
        "SELECT oi.product_id,
                oi.product_name_snap             AS product_name,
                c.type                           AS category_type,
                c.name                           AS category_name,
                SUM(oi.quantity)                 AS units_sold,
                SUM(oi.line_total)               AS total_revenue,
                COUNT(DISTINCT oi.order_id)      AS order_count,
                AVG(oi.unit_price_snap)          AS avg_price
         FROM order_item oi
         JOIN sale_order so  ON so.order_id  = oi.order_id
         JOIN product p      ON p.product_id = oi.product_id
         JOIN category c     ON c.category_id = p.category_id
         WHERE " . implode(' AND ', $where) . "
         GROUP BY oi.product_id, oi.product_name_snap, c.type, c.name
         ORDER BY total_revenue DESC
         LIMIT 50"
    );
    $stmt->execute($params);
    respondOk($stmt->fetchAll());
}


// ================================================================
//  GET /api/reports/customers
//  Top customers by spend in the selected period
// ================================================================
if ($report === 'customers') {
    $stmt = db()->prepare(
        "SELECT u.user_id, u.full_name, u.phone,
                COUNT(DISTINCT so.order_id)      AS order_count,
                COALESCE(SUM(so.total_amount),0) AS total_spent,
                MAX(so.created_at)               AS last_order_at,
                u.created_at                     AS member_since,
                u.status
         FROM user u
         LEFT JOIN sale_order so
               ON so.user_id = u.user_id
              AND so.created_at BETWEEN ? AND ?
              AND so.order_status NOT IN ('cancelled','refunded')
         WHERE u.role = 'customer'
         GROUP BY u.user_id, u.full_name, u.phone, u.created_at, u.status
         ORDER BY total_spent DESC
         LIMIT 100"
    );
    $stmt->execute([$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
    respondOk($stmt->fetchAll());
}


// ================================================================
//  GET /api/reports/drivers
//  Now supports date range filtering
// ================================================================
if ($report === 'drivers') {
    $stmt = db()->prepare(
        "SELECT u.user_id                                            AS driver_id,
                CONCAT(u.first_name,' ',u.last_name)               AS driver_name,
                u.phone                                            AS driver_phone,
                COUNT(d.delivery_id)                               AS total_assigned,
                COUNT(CASE WHEN d.delivery_status='confirmed' THEN 1 END)  AS completed,
                COUNT(CASE WHEN d.delivery_status='failed'    THEN 1 END)  AS failed,
                AVG(TIMESTAMPDIFF(MINUTE, d.dispatched_at, d.confirmed_at)) AS avg_delivery_min
         FROM delivery d
         JOIN user u ON u.user_id = d.driver_id
         WHERE d.created_at BETWEEN ? AND ?
         GROUP BY u.user_id, u.first_name, u.last_name, u.phone
         ORDER BY completed DESC"
    );
    $stmt->execute([$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
    respondOk($stmt->fetchAll());
}


// ================================================================
//  GET /api/reports/cashiers
// ================================================================
if ($report === 'cashiers') {
    $stmt = db()->prepare(
        "SELECT * FROM v_cashier_performance
         WHERE work_date BETWEEN ? AND ?
         ORDER BY work_date DESC"
    );
    $stmt->execute([$dateFrom, $dateTo]);
    respondOk($stmt->fetchAll());
}


// ================================================================
//  GET /api/reports/payments
//  Payment method breakdown for the period
// ================================================================
if ($report === 'payments') {
    $where  = ['p.created_at BETWEEN ? AND ?'];
    $params = [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'];
    $method = queryParam('method');
    $status = queryParam('status');

    $methodMap = [
        'cash'         => ['Cash'],
        'mobile_money' => ['Mobile Money'],
        'card'         => ['Credit Card', 'Bank Transfer'],
    ];
    if ($method && isset($methodMap[$method])) {
        $ph = implode(',', array_fill(0, count($methodMap[$method]), '?'));
        $where[]  = "p.method IN ($ph)";
        $params   = array_merge($params, $methodMap[$method]);
    }
    if ($status) { $where[] = 'p.payment_status=?'; $params[] = $status; }

    $stmt = db()->prepare(
        "SELECT p.payment_id, p.amount, p.method, p.payment_status,
                p.receipt_no, p.reference_no, p.paid_at, p.created_at,
                so.order_ref, u.full_name AS customer_name
         FROM payment p
         JOIN sale_order so ON so.order_id = p.order_id
         JOIN user u        ON u.user_id   = so.user_id
         WHERE " . implode(' AND ', $where) . "
         ORDER BY p.created_at DESC"
    );
    $stmt->execute($params);
    respondOk($stmt->fetchAll());
}


// ================================================================
//  GET /api/reports/stock-alerts
// ================================================================
if ($report === 'stock-alerts') {
    requireAuth(['super_admin', 'manager', 'cashier']);
    $status = queryParam('stock_status');
    $sql    = "SELECT * FROM v_stock_alerts";
    if ($status) $sql .= " WHERE stock_status=?";
    $stmt = db()->prepare($sql);
    $status ? $stmt->execute([$status]) : $stmt->execute();
    respondOk($stmt->fetchAll());
}


respondError('Report not found. Valid: sales, products, customers, drivers, cashiers, payments, stock-alerts, summary, dashboard', 404);
