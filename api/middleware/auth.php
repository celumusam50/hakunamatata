<?php
// ================================================================
//  HAKUNA MATATA — JWT Auth Middleware
//  Usage:
//    $user = requireAuth();              // any authenticated user
//    $user = requireAuth('manager');     // single role
//    $user = requireAuth(['manager','super_admin']); // multiple roles
// ================================================================
require_once __DIR__ . '/../core.php';

// ── Pure JWT functions (no library dependency) ────────────────────
function jwtEncode(array $payload): string {
    $header  = base64UrlEncode(json_encode(['alg' => JWT_ALGORITHM, 'typ' => 'JWT']));
    $payload = base64UrlEncode(json_encode($payload));
    $sig     = base64UrlEncode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
    return "$header.$payload.$sig";
}

function jwtDecode(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;
    [$header, $payload, $sig] = $parts;
    $expected = base64UrlEncode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
    if (!hash_equals($expected, $sig)) return null;
    $data = json_decode(base64UrlDecode($payload), true);
    if (!$data || !isset($data['exp']) || $data['exp'] < time()) return null;
    return $data;
}

function base64UrlEncode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode(string $data): string {
    return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 4 - strlen($data) % 4));
}

// ── Token generation ─────────────────────────────────────────────
function generateAccessToken(array $user): string {
    return jwtEncode([
        'sub'  => $user['user_id'],
        'name' => $user['full_name'],
        'role' => $user['role'],
        'iat'  => time(),
        'exp'  => time() + JWT_ACCESS_TTL,
        'type' => 'access',
    ]);
}

function generateRefreshToken(array $user): string {
    $token = bin2hex(random_bytes(40));
    $hash  = hash('sha256', $token);
    $device = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $ip     = $_SERVER['REMOTE_ADDR']    ?? null;

    db()->prepare(
        "INSERT INTO auth_token (user_id, token_hash, device_info, ip_address, expires_at)
         VALUES (?, ?, ?, ?, ?)"
    )->execute([
        $user['user_id'], $hash,
        substr($device, 0, 255), $ip,
        date('Y-m-d H:i:s', time() + JWT_REFRESH_TTL),
    ]);

    return $token;
}

// ── Main auth guard ───────────────────────────────────────────────
function requireAuth(string|array $roles = []): array {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!str_starts_with($header, 'Bearer '))
        respondError('Unauthorized — missing or malformed token', 401);

    $token = substr($header, 7);
    $data  = jwtDecode($token);

    if (!$data || ($data['type'] ?? '') !== 'access')
        respondError('Unauthorized — token invalid or expired', 401);

    // Check user still active
    $stmt = db()->prepare(
        "SELECT user_id, full_name, phone, role, status
         FROM user WHERE user_id=? AND status='active' LIMIT 1"
    );
    $stmt->execute([$data['sub']]);
    $user = $stmt->fetch();

    if (!$user) respondError('Unauthorized — account not found or suspended', 401);

    // Role check
    if ($roles) {
        $allowed = is_array($roles) ? $roles : [$roles];
        if (!in_array($user['role'], $allowed, true))
            respondError('Forbidden — insufficient permissions', 403);
    }

    return $user;
}

// ── Optional auth (returns user or null, no error) ────────────────
function optionalAuth(): ?array {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!str_starts_with($header, 'Bearer ')) return null;
    $token = substr($header, 7);
    $data  = jwtDecode($token);
    if (!$data || ($data['type'] ?? '') !== 'access') return null;
    $stmt = db()->prepare(
        "SELECT user_id, full_name, phone, role, status FROM user
         WHERE user_id=? AND status='active' LIMIT 1"
    );
    $stmt->execute([$data['sub']]);
    return $stmt->fetch() ?: null;
}

// ── Role helpers ─────────────────────────────────────────────────
function isStaff(array $user): bool {
    return in_array($user['role'], ['super_admin','manager','cashier','driver'], true);
}

function isManagement(array $user): bool {
    return in_array($user['role'], ['super_admin','manager'], true);
}
