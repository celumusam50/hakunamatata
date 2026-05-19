<?php
// ================================================================
//  HAKUNA MATATA — API Router
// ================================================================
require_once __DIR__ . '/core.php';

// ── Fix: Apache silently drops the Authorization header ─────────
if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
    if (function_exists('getallheaders')) {
        foreach (getallheaders() as $k => $v) {
            if (strtolower($k) === 'authorization') {
                $_SERVER['HTTP_AUTHORIZATION'] = $v;
                break;
            }
        }
    }
    if (empty($_SERVER['HTTP_AUTHORIZATION']) &&
        !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }
}

require_once __DIR__ . '/middleware/auth.php';

applyCors();

// ── Parse URL ────────────────────────────────────────────────────
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Strip any known base prefix dynamically
foreach (['/hakunamatata_v3/api', '/api'] as $base) {
    if (str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base));
        break;
    }
}

$path   = ltrim($uri, '/');
$parts  = array_values(array_filter(explode('/', $path)));

$resource = $parts[0] ?? '';
$id       = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : null;
$action   = $id ? ($parts[2] ?? null) : ($parts[1] ?? null);

// ── Route dispatch ───────────────────────────────────────────────
match ($resource) {
    'auth'         => require __DIR__ . '/routes/auth.php',
    'users'        => require __DIR__ . '/routes/users.php',
    'categories'   => require __DIR__ . '/routes/categories.php',
    'products'     => require __DIR__ . '/routes/products.php',
    'cart'         => require __DIR__ . '/routes/cart.php',
    'orders'       => require __DIR__ . '/routes/orders.php',
    'payments'     => require __DIR__ . '/routes/payments.php',
    'deliveries'   => require __DIR__ . '/routes/deliveries.php',
    'discounts'    => require __DIR__ . '/routes/discounts.php',
    'tables'       => require __DIR__ . '/routes/tables.php',
    'reports'      => require __DIR__ . '/routes/reports.php',
    'notifications'=> require __DIR__ . '/routes/notifications.php',
    'settings'     => require __DIR__ . '/routes/settings.php',
    'upload'       => require __DIR__ . '/routes/upload.php',
    'ping'         => respond(['status' => 'ok', 'time' => date('c'), 'api' => 'Hakuna Matata v3']),
    default        => respondError('Route not found: ' . $resource . ' | URI: ' . $_SERVER['REQUEST_URI'], 404),
};
