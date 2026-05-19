<?php
// ================================================================
//  ROUTE: /api/discounts
// ================================================================
$m = method();

if ($m === 'GET' && !$id) {
    requireAuth(['super_admin', 'manager']);
    $stmt = db()->query("SELECT * FROM discount ORDER BY created_at DESC");
    respondOk($stmt->fetchAll());
}

if ($m === 'GET' && $id) {
    requireAuth(['super_admin', 'manager']);
    $stmt = db()->prepare("SELECT * FROM discount WHERE discount_id=? LIMIT 1");
    $stmt->execute([$id]);
    $d = $stmt->fetch();
    if (!$d) respondError('Discount not found', 404);
    respondOk($d);
}

// Validate a promo code (public — used at checkout)
if ($m === 'POST' && $action === 'validate') {
    $auth = optionalAuth();
    $b    = body();
    $code = trim($b['code'] ?? '');
    if (!$code) respondError('Promo code is required.', 400);
    $stmt = db()->prepare(
        "SELECT * FROM discount WHERE code=? AND is_active=1
         AND (valid_until IS NULL OR valid_until > NOW())
         AND (max_uses IS NULL OR uses_count < max_uses) LIMIT 1"
    );
    $stmt->execute([$code]);
    $disc = $stmt->fetch();
    if (!$disc) respondError('Invalid or expired promo code.', 404);
    // Check per-user limit
    if ($auth && $disc['max_uses_per_user']) {
        $used = db()->prepare(
            "SELECT COUNT(*) FROM discount_usage WHERE discount_id=? AND user_id=?"
        );
        $used->execute([$disc['discount_id'], $auth['user_id']]);
        if ((int)$used->fetchColumn() >= $disc['max_uses_per_user'])
            respondError('You have already used this promo code the maximum number of times.', 400);
    }
    respondOk([
        'discount_id'  => $disc['discount_id'],
        'name'         => $disc['name'],
        'type'         => $disc['type'],
        'value'        => $disc['value'],
        'applies_to'   => $disc['applies_to'],
        'min_order_amount' => $disc['min_order_amount'],
        'max_discount_amount' => $disc['max_discount_amount'],
    ], 'Promo code applied');
}

if ($m === 'POST' && !$id) {
    $auth = requireAuth(['super_admin', 'manager']);
    $b    = body();
    $v    = new Validator($b);
    $v->required('name')->minLen('name', 2)
      ->required('type')->inList('type', ['percentage','fixed','free_delivery'], 'Type')
      ->numeric('value')->min('value', 0)
      ->inList('applies_to', ['all','category','product','role'], 'Applies to');
    if ($v->fails()) respondError($v->first(), 422, $v->errors());
    db()->prepare(
        "INSERT INTO discount (name, code, description, type, value, applies_to,
         target_category_id, target_product_id, target_role, min_order_amount,
         max_discount_amount, max_uses, max_uses_per_user, valid_from, valid_until,
         is_active, created_by)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,?)"
    )->execute([
        trim($b['name']), trim($b['code'] ?? '') ?: null,
        trim($b['description'] ?? ''), $b['type'], (float)$b['value'],
        $b['applies_to'] ?? 'all',
        ($b['target_category_id'] ?? null) ?: null,
        ($b['target_product_id'] ?? null) ?: null,
        ($b['target_role'] ?? null) ?: null,
        (float)($b['min_order_amount'] ?? 0),
        ($b['max_discount_amount'] ?? null) ?: null,
        ($b['max_uses'] ?? null) ?: null,
        ($b['max_uses_per_user'] ?? null) ?: null,
        $b['valid_from'] ?? date('Y-m-d H:i:s'),
        ($b['valid_until'] ?? null) ?: null,
        $auth['user_id'],
    ]);
    respondCreated(['id' => (int)db()->lastInsertId()], 'Discount created');
}

if ($m === 'PUT' && $id) {
    requireAuth(['super_admin', 'manager']);
    $b = body();
    db()->prepare(
        "UPDATE discount SET name=?, code=?, description=?, type=?, value=?,
         applies_to=?, min_order_amount=?, max_uses=?, valid_from=?, valid_until=?,
         is_active=? WHERE discount_id=?"
    )->execute([
        trim($b['name']), trim($b['code'] ?? '') ?: null,
        trim($b['description'] ?? ''), $b['type'], (float)$b['value'],
        $b['applies_to'] ?? 'all', (float)($b['min_order_amount'] ?? 0),
        ($b['max_uses'] ?? null) ?: null,
        $b['valid_from'] ?? date('Y-m-d H:i:s'),
        ($b['valid_until'] ?? null) ?: null,
        (int)($b['is_active'] ?? 1), $id,
    ]);
    respondOk([], 'Discount updated');
}

if ($m === 'DELETE' && $id) {
    requireAuth(['super_admin']);
    db()->prepare("UPDATE discount SET is_active=0 WHERE discount_id=?")->execute([$id]);
    respondOk([], 'Discount deactivated');
}

respondError('Discounts route not found', 404);
