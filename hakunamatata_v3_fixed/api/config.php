<?php
// ================================================================
//  HAKUNA MATATA — API Configuration
//
//  Railway: set these in your service's Variables tab.
//  Local (XAMPP): the fallback values below are used automatically.
// ================================================================

// ── Database ─────────────────────────────────────────────────────
// Railway injects MYSQLHOST, MYSQLPORT, MYSQLDATABASE, MYSQLUSER,
// MYSQLPASSWORD automatically when you add a MySQL plugin.
// The DB_* aliases let you override them explicitly if needed.
define('DB_HOST',    getenv('DB_HOST')    ?: getenv('MYSQLHOST')     ?: 'localhost');
define('DB_PORT',    getenv('DB_PORT')    ?: getenv('MYSQLPORT')     ?: '3306');
define('DB_NAME',    getenv('DB_NAME')    ?: getenv('MYSQLDATABASE') ?: 'hakunamatata_v3');
define('DB_USER',    getenv('DB_USER')    ?: getenv('MYSQLUSER')     ?: 'root');
define('DB_PASS',    getenv('DB_PASS')    ?: getenv('MYSQLPASSWORD') ?: '');
define('DB_CHARSET', 'utf8mb4');

// ── JWT ──────────────────────────────────────────────────────────
define('JWT_SECRET',    getenv('JWT_SECRET')    ?: 'change-this-secret');
define('JWT_ACCESS_TTL',  900);     // 15 minutes
define('JWT_REFRESH_TTL', 2592000); // 30 days
define('JWT_ALGORITHM',   'HS256');

// ── App ──────────────────────────────────────────────────────────
define('APP_ENV',   getenv('APP_ENV')   ?: 'production');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');
define('APP_URL',   getenv('APP_URL')   ?: 'https://hakunamatatastore.up.railway.app/api');

// ── File uploads ─────────────────────────────────────────────────
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', '/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// ── CORS ─────────────────────────────────────────────────────────
$allowedOrigins = getenv('CORS_ORIGINS')
    ? explode(',', getenv('CORS_ORIGINS'))
    : ['https://hakunamatatastore.up.railway.app'];

define('CORS_ORIGINS', $allowedOrigins);
