<?php
// ================================================================
//  HAKUNA MATATA — API Configuration
//  On Railway: values come from environment variables.
//  Locally: set these in a .env file or export them in your shell.
// ================================================================

// ── Database ─────────────────────────────────────────────────────
define('DB_HOST',    getenv('DB_HOST')    ?: 'localhost');
define('DB_NAME',    getenv('DB_NAME')    ?: 'hakunamatata_v3');
define('DB_USER',    getenv('DB_USER')    ?: 'root');
define('DB_PASS',    getenv('DB_PASS')    ?: '');
define('DB_CHARSET', 'utf8mb4');

// ── JWT ──────────────────────────────────────────────────────────
define('JWT_SECRET',    getenv('JWT_SECRET')    ?: 'change-this-secret');
define('JWT_ACCESS_TTL',  900);     // 15 minutes
define('JWT_REFRESH_TTL', 2592000); // 30 days
define('JWT_ALGORITHM',   'HS256');

// ── App ──────────────────────────────────────────────────────────
define('APP_ENV',   getenv('APP_ENV')   ?: 'production');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');
define('APP_URL',   getenv('APP_URL')   ?: 'http://localhost/api');

// ── File uploads ─────────────────────────────────────────────────
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', '/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// ── CORS ─────────────────────────────────────────────────────────
$allowedOrigins = getenv('CORS_ORIGINS')
    ? explode(',', getenv('CORS_ORIGINS'))
    : ['http://localhost', 'http://localhost:3000'];

define('CORS_ORIGINS', $allowedOrigins);
