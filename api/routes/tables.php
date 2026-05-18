<?php
// ================================================================
//  ROUTE: /api/tables  (restaurant dine-in tables)
// ================================================================
$m = method();

if ($m === 'GET') {
    $status = queryParam('status');
    $where  = ['1=1']; $params = [];
    if ($status) { $where[] = 'status=?'; $params[] = $status; }
    $stmt = db()->prepare(
        "SELECT * FROM restaurant_table WHERE " . implode(' AND ', $where) . " ORDER BY table_number"
    );
    $stmt->execute($params);
    respondOk($stmt->fetchAll());
}

if ($m === 'POST') {
    requireAuth(['super_admin', 'manager']);
    $b = body();
    db()->prepare(
        "INSERT INTO restaurant_table (table_number, capacity, location, status)
         VALUES (?, ?, ?, 'available')"
    )->execute([trim($b['table_number']), (int)($b['capacity'] ?? 4), trim($b['location'] ?? '')]);
    respondCreated(['id' => (int)db()->lastInsertId()], 'Table added');
}

if ($m === 'PATCH' && $id) {
    requireAuth(['super_admin', 'manager', 'cashier']);
    $b = body();
    $v = new Validator($b);
    $v->required('status')->inList('status', ['available','occupied','reserved','cleaning'], 'Status');
    if ($v->fails()) respondError($v->first(), 422);
    db()->prepare("UPDATE restaurant_table SET status=? WHERE table_id=?")->execute([$b['status'], $id]);
    respondOk([], 'Table status updated');
}

if ($m === 'DELETE' && $id) {
    requireAuth(['super_admin']);
    db()->prepare("DELETE FROM restaurant_table WHERE table_id=?")->execute([$id]);
    respondOk([], 'Table removed');
}

respondError('Tables route not found', 404);
