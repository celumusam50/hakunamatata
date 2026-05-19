<?php
// ================================================================
//  ROUTE: /api/users
//  GET    /users                  super_admin, manager
//  GET    /users/{id}             self or management
//  POST   /users                  super_admin (create any role)
//  PUT    /users/{id}             self (profile) or management
//  PATCH  /users/{id}/status      super_admin
//  PATCH  /users/{id}/password    self
//  DELETE /users/{id}             super_admin
// ================================================================

$m = method();

// ── LIST USERS ───────────────────────────────────────────────────
if ($m === 'GET' && !$id) {
    $user = requireAuth(['super_admin', 'manager']);
    $role   = queryParam('role');
    $status = queryParam('status', '');
    $search = queryParam('search', '');
    $page   = max(1, (int)queryParam('page', 1));
    $perPage= min(100, max(10, (int)queryParam('per_page', 20)));

    // Only use real (non-generated) columns in WHERE to avoid MySQL issues
    $where  = ['u.user_id > 0'];
    $params = [];

    if ($role && in_array($role, Validator::ROLES, true)) {
        $where[] = 'u.role = ?'; $params[] = $role;
    }
    if ($status) {
        $where[] = 'u.status = ?'; $params[] = $status;
    }
    if ($search) {
        $where[] = '(u.first_name LIKE ? OR u.last_name LIKE ? OR u.phone LIKE ?)';
        $s = "%$search%";
        array_push($params, $s, $s, $s);
    }

    $whereStr = implode(' AND ', $where);

    try {
        // Count without JOIN
        $countStmt = db()->prepare("SELECT COUNT(*) FROM user u WHERE $whereStr");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Data with LEFT JOIN for order stats
        $offset = ($page - 1) * $perPage;
        $stmt = db()->prepare("
            SELECT u.user_id, u.first_name, u.last_name,
                   CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                   u.phone, u.role, u.status,
                   u.last_login_at, u.created_at,
                   COUNT(so.order_id)               AS order_count,
                   COALESCE(SUM(so.total_amount),0) AS total_spent,
                   MAX(so.created_at)               AS last_order_at
            FROM user u
            LEFT JOIN sale_order so
                   ON so.user_id = u.user_id
                  AND so.order_status NOT IN ('cancelled','refunded')
            WHERE $whereStr
            GROUP BY u.user_id, u.first_name, u.last_name, u.phone,
                     u.role, u.status,
                     u.last_login_at, u.created_at
            ORDER BY u.created_at DESC
            LIMIT $perPage OFFSET $offset
        ");
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        respondOk([
            'data'       => $data,
            'pagination' => [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => (int)ceil($total / $perPage),
                'from'         => $offset + 1,
                'to'           => min($offset + $perPage, $total),
            ],
        ]);
    } catch (Exception $e) {
        respondError('Database error: ' . $e->getMessage(), 500);
    }
}

// ── GET SINGLE USER ──────────────────────────────────────────────
if ($m === 'GET' && $id) {
    $auth = requireAuth();
    // Self or management only
    if ($auth['user_id'] !== $id && !isManagement($auth))
        respondError('Forbidden', 403);

    $stmt = db()->prepare(
        "SELECT user_id, first_name, last_name, full_name, phone, role, status,
                last_login_at, created_at
         FROM user WHERE user_id=? LIMIT 1"
    );
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if (!$user) respondError('User not found', 404);
    respondOk($user);
}

// ── CREATE USER (admin/manager creates staff or customer) ─────────
if ($m === 'POST' && !$id) {
    $auth = requireAuth(['super_admin', 'manager']);
    $b    = body();

    $v = new Validator($b);
    $v->required('first_name', 'First name')
      ->minLen('first_name', 2, 'First name')
      ->maxLen('first_name', 75, 'First name')
      ->lettersOnly('first_name', 'First name')
      ->required('last_name', 'Last name')
      ->minLen('last_name', 2, 'Last name')
      ->maxLen('last_name', 75, 'Last name')
      ->lettersOnly('last_name', 'Last name')
      ->required('password', 'Password')
      ->password('password')
      ->phone('phone')
      ->required('role', 'Role')
      ->inList('role', Validator::ROLES, 'Role')
      ->uniquePhone(db(), 'phone');

    // Managers can only create customers/drivers/cashiers
    if ($auth['role'] === 'manager' && in_array($b['role'] ?? '', ['super_admin', 'manager']))
        respondError('Managers cannot create admin or manager accounts.', 403);

    if ($v->fails()) respondError($v->first(), 422, $v->errors());

    $firstName = ucfirst(strtolower(trim($b['first_name'])));
    $lastName  = ucfirst(strtolower(trim($b['last_name'])));
    $hash = password_hash($b['password'], PASSWORD_BCRYPT);

    db()->prepare(
        "INSERT INTO user (first_name, last_name, password_hash, phone, role, status)
         VALUES (?, ?, ?, ?, ?, 'active')"
    )->execute([
        $firstName,
        $lastName,
        $hash,
        trim($b['phone'] ?? ''),
        $b['role'],
    ]);

    $newId = (int)db()->lastInsertId();
    auditLog($auth['user_id'], 'user.create', 'user', $newId, null,
             ['role' => $b['role'], ]);

    respondCreated(['id' => $newId], 'User created successfully');
}

// ── UPDATE USER PROFILE ──────────────────────────────────────────
if ($m === 'PUT' && $id) {
    $auth = requireAuth();
    if ($auth['user_id'] !== $id && !isManagement($auth))
        respondError('Forbidden', 403);

    $b = body();
    $v = new Validator($b);
    $v->required('first_name', 'First name')
      ->minLen('first_name', 2, 'First name')
      ->lettersOnly('first_name', 'First name')
      ->required('last_name', 'Last name')
      ->minLen('last_name', 2, 'Last name')
      ->lettersOnly('last_name', 'Last name')
      ->phone('phone')
      ->uniquePhone(db(), 'phone', $id);

    if ($v->fails()) respondError($v->first(), 422, $v->errors());

    $firstName = ucfirst(strtolower(trim($b['first_name'])));
    $lastName  = ucfirst(strtolower(trim($b['last_name'])));

    db()->prepare(
        "UPDATE user SET first_name=?, last_name=?, phone=?
         WHERE user_id=?"
    )->execute([
        $firstName,
        $lastName,
        trim($b['phone'] ?? ''),
        $id,
    ]);

    respondOk([], 'Profile updated');
}

// ── PATCH STATUS ─────────────────────────────────────────────────
if ($m === 'PATCH' && $id && $action === 'status') {
    $auth = requireAuth(['super_admin', 'manager']);
    $b    = body();
    $v    = new Validator($b);
    $v->required('status')->inList('status', ['active','inactive','suspended'], 'Status');
    if ($v->fails()) respondError($v->first(), 422);

    // Cannot change your own status
    if ($auth['user_id'] === $id) respondError('Cannot change your own account status.', 400);

    // Managers can only suspend/activate cashiers and drivers
    if ($auth['role'] === 'manager') {
        $targetStmt = db()->prepare("SELECT role FROM user WHERE user_id=?");
        $targetStmt->execute([$id]);
        $targetUser = $targetStmt->fetch();
        if (!$targetUser || in_array($targetUser['role'], ['super_admin', 'manager']))
            respondError('Managers can only suspend cashiers and drivers.', 403);
    }

    db()->prepare("UPDATE user SET status=? WHERE user_id=?")->execute([$b['status'], $id]);
    auditLog($auth['user_id'], 'user.status_change', 'user', $id,
             null, ['status' => $b['status']]);

    respondOk([], 'Status updated');
}

// ── PATCH PASSWORD (self-service) ─────────────────────────────────
if ($m === 'PATCH' && $id && $action === 'password') {
    $auth = requireAuth();
    if ($auth['user_id'] !== $id) respondError('Forbidden', 403);

    $b = body();
    if (!isset($b['current_password'], $b['new_password']))
        respondError('current_password and new_password are required.', 400);

    $stmt = db()->prepare("SELECT password_hash FROM user WHERE user_id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!password_verify($b['current_password'], $row['password_hash']))
        respondError('Current password is incorrect.', 400);

    $v = new Validator(['password' => $b['new_password']]);
    $v->password('password');
    if ($v->fails()) respondError($v->first(), 422);

    $hash = password_hash($b['new_password'], PASSWORD_BCRYPT);
    db()->prepare("UPDATE user SET password_hash=? WHERE user_id=?")->execute([$hash, $id]);

    // Revoke all other refresh tokens
    db()->prepare(
        "UPDATE auth_token SET revoked_at=NOW()
         WHERE user_id=? AND revoked_at IS NULL"
    )->execute([$id]);

    respondOk([], 'Password changed. Please sign in again on other devices.');
}

// ── DELETE USER (soft — suspend) ──────────────────────────────────
if ($m === 'DELETE' && $id) {
    $auth = requireAuth(['super_admin']);
    if ($auth['user_id'] === $id) respondError('Cannot delete your own account.', 400);

    db()->prepare("UPDATE user SET status='inactive' WHERE user_id=?")->execute([$id]);
    db()->prepare("UPDATE auth_token SET revoked_at=NOW() WHERE user_id=?")->execute([$id]);
    auditLog($auth['user_id'], 'user.delete', 'user', $id);

    respondOk([], 'User deactivated');
}

respondError('Users route not found', 404);
