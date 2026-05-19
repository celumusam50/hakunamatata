<?php
// ================================================================
//  ROUTE: /api/upload
//  POST /upload/product           manager, super_admin
//  POST /upload/avatar            any authenticated user
// ================================================================

$auth = requireAuth();
$sub  = $action ?? $parts[1] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    respondError('POST only', 405);

if (!isset($_FILES['file']))
    respondError('No file uploaded. Use multipart/form-data with key "file".', 400);

$file     = $_FILES['file'];
$allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$maxSize  = MAX_UPLOAD_SIZE;

if ($file['error'] !== UPLOAD_ERR_OK)
    respondError('Upload failed with error code: ' . $file['error'], 400);

if ($file['size'] > $maxSize)
    respondError('File too large. Maximum size is 5MB.', 400);

// Validate MIME from content (not extension)
$finfo    = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowed))
    respondError('Only JPEG, PNG, WebP, and GIF images are allowed.', 400);

$ext      = match($mimeType) {
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
};

if ($sub === 'product') {
    if (!isManagement($auth)) respondError('Forbidden', 403);
    $dir = UPLOAD_DIR . 'products/';
    $urlBase = UPLOAD_URL . 'products/';
} elseif ($sub === 'avatar') {
    $dir = UPLOAD_DIR . 'avatars/';
    $urlBase = UPLOAD_URL . 'avatars/';
} elseif ($sub === 'logo') {
    if (!in_array($auth['role'], ['super_admin'])) respondError('Forbidden', 403);
    $dir = UPLOAD_DIR . 'brand/';
    $urlBase = UPLOAD_URL . 'brand/';
} else {
    respondError('Upload type not found. Use: product, avatar, logo', 404);
}

if (!is_dir($dir)) mkdir($dir, 0755, true);

$filename = uniqid('img_', true) . '.' . $ext;
$dest     = $dir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest))
    respondError('Failed to save file. Check server permissions.', 500);

$url = $urlBase . $filename;

// If product upload, optionally update product record
if ($sub === 'product' && ($productId = $_POST['product_id'] ?? null)) {
    db()->prepare("UPDATE product SET img_url=? WHERE product_id=?")->execute([$url, (int)$productId]);
}

// If avatar upload, update user record
if ($sub === 'avatar') {
    db()->prepare("UPDATE user SET avatar_url=? WHERE user_id=?")->execute([$url, $auth['user_id']]);
}

// If logo, update setting
if ($sub === 'logo') {
    db()->prepare("UPDATE system_setting SET setting_value=? WHERE setting_key='logo_url'")->execute([$url]);
}

respondOk(['url' => $url, 'filename' => $filename], 'File uploaded successfully');
