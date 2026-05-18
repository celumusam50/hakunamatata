<?php
// ================================================================
//  ROUTE: /api/categories
//  GET    /categories             public
//  POST   /categories             manager, super_admin
//  PUT    /categories/{id}        manager, super_admin
//  DELETE /categories/{id}        super_admin
// ================================================================

$m = method();

if ($m === 'GET' && !$id) {
    $type   = queryParam('type');
    $status = queryParam('status', 'active');
    $where  = ['1=1']; $params = [];
    if ($type && in_array($type, Validator::CAT_TYPES, true)) {
        $where[] = 'type=?'; $params[] = $type;
    }
    if ($status) { $where[] = 'status=?'; $params[] = $status; }
    $stmt = db()->prepare(
        "SELECT * FROM category WHERE " . implode(' AND ', $where) . " ORDER BY sort_order, name"
    );
    $stmt->execute($params);
    respondOk($stmt->fetchAll());
}

if ($m === 'GET' && $id) {
    $stmt = db()->prepare("SELECT * FROM category WHERE category_id=? LIMIT 1");
    $stmt->execute([$id]);
    $cat = $stmt->fetch();
    if (!$cat) respondError('Category not found', 404);
    respondOk($cat);
}

if ($m === 'POST') {
    $auth = requireAuth(['super_admin', 'manager']);
    $b = body();
    $v = new Validator($b);
    $v->required('name')->minLen('name', 2)->maxLen('name', 100)
      ->required('type')->inList('type', Validator::CAT_TYPES, 'Type')
      ->required('slug')->minLen('slug', 2)->maxLen('slug', 100);
    if ($v->fails()) respondError($v->first(), 422, $v->errors());

    // Check slug unique
    $chk = db()->prepare("SELECT 1 FROM category WHERE slug=? LIMIT 1");
    $chk->execute([trim($b['slug'])]);
    if ($chk->fetch()) respondError('That slug is already in use.', 422);

    db()->prepare(
        "INSERT INTO category (name, slug, type, description, emoji, sort_order, status)
         VALUES (?, ?, ?, ?, ?, ?, 'active')"
    )->execute([
        trim($b['name']), trim($b['slug']), $b['type'],
        trim($b['description'] ?? ''),
        trim($b['emoji'] ?? '🥩'),
        (int)($b['sort_order'] ?? 0),
    ]);
    respondCreated(['id' => (int)db()->lastInsertId()], 'Category created');
}

if ($m === 'PUT' && $id) {
    $auth = requireAuth(['super_admin', 'manager']);
    $b = body();
    $v = new Validator($b);
    $v->required('name')->minLen('name', 2)
      ->required('type')->inList('type', Validator::CAT_TYPES, 'Type')
      ->inList('status', ['active','inactive'], 'Status');
    if ($v->fails()) respondError($v->first(), 422, $v->errors());
    db()->prepare(
        "UPDATE category SET name=?, type=?, description=?, emoji=?, sort_order=?, status=?
         WHERE category_id=?"
    )->execute([
        trim($b['name']), $b['type'],
        trim($b['description'] ?? ''),
        trim($b['emoji'] ?? '🥩'),
        (int)($b['sort_order'] ?? 0),
        $b['status'] ?? 'active', $id,
    ]);
    respondOk([], 'Category updated');
}

if ($m === 'DELETE' && $id) {
    requireAuth(['super_admin']);
    // Check no products reference it
    $chk = db()->prepare("SELECT COUNT(*) FROM product WHERE category_id=? AND status='active'");
    $chk->execute([$id]);
    if ((int)$chk->fetchColumn() > 0)
        respondError('Cannot delete — this category has active products.', 409);
    db()->prepare("UPDATE category SET status='inactive' WHERE category_id=?")->execute([$id]);
    respondOk([], 'Category deactivated');
}

respondError('Categories route not found', 404);
