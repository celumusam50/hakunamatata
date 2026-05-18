<?php
// ================================================================
//  ROUTE: /api/auth
//  POST /auth/login
//  POST /auth/register
//  POST /auth/refresh
//  POST /auth/logout
//  POST /auth/request-reset
//  POST /auth/verify-otp
//  POST /auth/reset-password
// ================================================================

$sub = $action ?? $parts[1] ?? '';

// ── LOGIN ────────────────────────────────────────────────────────
if (method() === 'POST' && $sub === 'login') {
    $b = body();
    $phone = trim($b['phone'] ?? '');
    $pass  = $b['password'] ?? '';

    if (!$phone || !$pass)
        respondError('Phone and password are required.', 400);

    $norm = preg_replace('/\s/', '', $phone);
    $stmt = db()->prepare(
        "SELECT * FROM user WHERE REPLACE(phone,' ','')=? AND status='active' LIMIT 1"
    );
    $stmt->execute([$norm]);

    $user = $stmt->fetch();
    if (!$user || !password_verify($pass, $user['password_hash']))
        respondError('Incorrect credentials. Please try again.', 401);

    // Update last login
    db()->prepare("UPDATE user SET last_login_at=NOW() WHERE user_id=?")
        ->execute([$user['user_id']]);

    $access  = generateAccessToken($user);
    $refresh = generateRefreshToken($user);

    respondOk([
        'access_token'  => $access,
        'refresh_token' => $refresh,
        'token_type'    => 'Bearer',
        'expires_in'    => JWT_ACCESS_TTL,
        'user' => [
            'id'         => $user['user_id'],
            'first_name' => $user['first_name'],
            'last_name'  => $user['last_name'],
            'full_name'  => $user['full_name'],
                        'phone'      => $user['phone'],
            'role'       => $user['role'],
            'city'       => $user['city'],
            'region'     => $user['region'],
            'avatar_url' => $user['avatar_url'],
        ],
    ], 'Login successful');
}

// ── REGISTER (customer self-service) ─────────────────────────────
if (method() === 'POST' && $sub === 'register') {
    $b = body();
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
      ->required('phone', 'Phone')
      ->phone('phone')
      ->uniquePhone(db(), 'phone');

    if ($v->fails()) respondError($v->first(), 422, $v->errors());

    $first = ucfirst(strtolower(trim($b['first_name'])));
    $last  = ucfirst(strtolower(trim($b['last_name'])));
    $hash  = password_hash($b['password'], PASSWORD_BCRYPT);

    db()->prepare(
        "INSERT INTO user (first_name, last_name, password_hash, phone, role, status)
         VALUES (?, ?, ?, ?, 'customer', 'active')"
    )->execute([$first, $last, $hash, trim($b['phone'])]);

    $userId = (int)db()->lastInsertId();
    $user   = db()->prepare("SELECT * FROM user WHERE user_id=? LIMIT 1");
    $user->execute([$userId]);
    $user = $user->fetch();

    $access  = generateAccessToken($user);
    $refresh = generateRefreshToken($user);

    respondCreated([
        'access_token'  => $access,
        'refresh_token' => $refresh,
        'token_type'    => 'Bearer',
        'expires_in'    => JWT_ACCESS_TTL,
        'user' => [
            'id'         => $user['user_id'],
            'first_name' => $user['first_name'],
            'last_name'  => $user['last_name'],
            'full_name'  => $user['full_name'],
                        'phone'      => $user['phone'],
            'role'       => $user['role'],
            'city'       => $user['city'],
        ],
    ], 'Account created successfully');
}

// ── REFRESH TOKEN ────────────────────────────────────────────────
if (method() === 'POST' && $sub === 'refresh') {
    $b     = body();
    $token = trim($b['refresh_token'] ?? '');
    if (!$token) respondError('Refresh token required.', 400);

    $hash  = hash('sha256', $token);
    $stmt  = db()->prepare(
        "SELECT at.*, u.* FROM auth_token at
         JOIN user u ON u.user_id = at.user_id
         WHERE at.token_hash=? AND at.revoked_at IS NULL
           AND at.expires_at > NOW() AND u.status='active' LIMIT 1"
    );
    $stmt->execute([$hash]);
    $row = $stmt->fetch();

    if (!$row) respondError('Invalid or expired refresh token.', 401);

    // Rotate: revoke old, issue new
    db()->prepare("UPDATE auth_token SET revoked_at=NOW() WHERE token_hash=?")
        ->execute([$hash]);

    $user    = ['user_id' => $row['user_id'], 'full_name' => $row['full_name'],
                'phone' => $row['phone'], 'role' => $row['role']];
    $access  = generateAccessToken($user);
    $refresh = generateRefreshToken($user);

    respondOk([
        'access_token'  => $access,
        'refresh_token' => $refresh,
        'token_type'    => 'Bearer',
        'expires_in'    => JWT_ACCESS_TTL,
    ], 'Token refreshed');
}

// ── LOGOUT ───────────────────────────────────────────────────────
if (method() === 'POST' && $sub === 'logout') {
    $b     = body();
    $token = trim($b['refresh_token'] ?? '');
    if ($token) {
        $hash = hash('sha256', $token);
        db()->prepare("UPDATE auth_token SET revoked_at=NOW() WHERE token_hash=?")
            ->execute([$hash]);
    }
    respondOk([], 'Logged out successfully');
}

// ── REQUEST PASSWORD RESET ───────────────────────────────────────
if (method() === 'POST' && $sub === 'request-reset') {
    $b     = body();
    $email = strtolower(trim($b['email'] ?? ''));

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
        respondError('Please enter a valid email address.', 400);

    $stmt = db()->prepare("SELECT user_id FROM user WHERE email=? AND status='active' LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Always respond the same way to prevent user enumeration
    $otp    = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $result = ['message' => 'If that email exists, a reset code has been sent.'];

    if ($user) {
        $hash    = password_hash($otp, PASSWORD_BCRYPT);
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Invalidate any existing unused tokens
        db()->prepare(
            "UPDATE password_reset_token SET used_at=NOW()
             WHERE user_id=? AND used_at IS NULL"
        )->execute([$user['user_id']]);

        db()->prepare(
            "INSERT INTO password_reset_token (user_id, otp_hash, expires_at)
             VALUES (?, ?, ?)"
        )->execute([$user['user_id'], $hash, $expires]);

        // In production: send via email/SMS. For dev, return OTP.
        if (APP_ENV === 'development') $result['otp'] = $otp;
    }

    respondOk($result);
}

// ── VERIFY RESET OTP ─────────────────────────────────────────────
if (method() === 'POST' && $sub === 'verify-otp') {
    $b     = body();
    $email = strtolower(trim($b['email'] ?? ''));
    $otp   = trim($b['otp'] ?? '');

    if (!$email) respondError('Email is required.', 400);
    if (!preg_match('/^\d{6}$/', $otp)) respondError('OTP must be a 6-digit code.', 400);

    $stmt = db()->prepare(
        "SELECT prt.reset_id, prt.otp_hash FROM password_reset_token prt
         JOIN user u ON u.user_id = prt.user_id
         WHERE u.email=? AND prt.used_at IS NULL AND prt.expires_at > NOW()
         ORDER BY prt.created_at DESC LIMIT 1"
    );
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($otp, $row['otp_hash']))
        respondError('Invalid or expired reset code.', 400);

    respondOk([], 'Code verified successfully');
}

// ── RESET PASSWORD ───────────────────────────────────────────────
if (method() === 'POST' && $sub === 'reset-password') {
    $b        = body();
    $email    = strtolower(trim($b['email'] ?? ''));
    $otp      = trim($b['otp'] ?? '');
    $newPass  = $b['new_password'] ?? '';

    if (!$email) respondError('Email is required.', 400);
    if (!preg_match('/^\d{6}$/', $otp)) respondError('Invalid OTP format.', 400);

    $v = new Validator(['password' => $newPass]);
    $v->password('password');
    if ($v->fails()) respondError($v->first(), 422);

    $stmt = db()->prepare(
        "SELECT prt.reset_id, prt.otp_hash, prt.user_id FROM password_reset_token prt
         JOIN user u ON u.user_id = prt.user_id
         WHERE u.email=? AND prt.used_at IS NULL AND prt.expires_at > NOW()
         ORDER BY prt.created_at DESC LIMIT 1"
    );
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($otp, $row['otp_hash']))
        respondError('Invalid or expired reset code.', 400);

    $hash = password_hash($newPass, PASSWORD_BCRYPT);
    db()->prepare("UPDATE user SET password_hash=? WHERE user_id=?")
        ->execute([$hash, $row['user_id']]);

    db()->prepare("UPDATE password_reset_token SET used_at=NOW() WHERE reset_id=?")
        ->execute([$row['reset_id']]);

    // Revoke all refresh tokens (force re-login on all devices)
    db()->prepare("UPDATE auth_token SET revoked_at=NOW() WHERE user_id=? AND revoked_at IS NULL")
        ->execute([$row['user_id']]);

    respondOk([], 'Password reset successfully. Please sign in with your new password.');
}

// ── ME (get current user profile) ────────────────────────────────
if (method() === 'GET' && $sub === 'me') {
    $user = requireAuth();
    respondOk($user);
}

respondError('Auth route not found', 404);
