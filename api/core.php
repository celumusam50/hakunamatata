<?php
// Suppress PHP warnings/notices from leaking into JSON responses
ini_set('display_errors', '0');
error_reporting(E_ERROR);
ob_start();
// ================================================================
//  HAKUNA MATATA — Core Bootstrap
//  Loaded by index.php before every request
// ================================================================
require_once __DIR__ . '/config.php';

// ── Database ─────────────────────────────────────────────────────
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        respond(['error' => 'Database connection failed'], 503);
    }
    return $pdo;
}

// ── Response ─────────────────────────────────────────────────────
function respond(mixed $data, int $code = 200): never {
    if (ob_get_level()) ob_clean(); // Clear any PHP warnings before JSON
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

function respondOk(mixed $data = [], string $message = 'OK'): never {
    respond(['success' => true, 'message' => $message, 'data' => $data]);
}

function respondCreated(mixed $data = [], string $message = 'Created'): never {
    respond(['success' => true, 'message' => $message, 'data' => $data], 201);
}

function respondError(string $message, int $code = 400, array $errors = []): never {
    $body = ['success' => false, 'error' => $message];
    if ($errors) $body['errors'] = $errors;
    respond($body, $code);
}

// ── Request ──────────────────────────────────────────────────────
function body(): array {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

function method(): string {
    return strtoupper($_SERVER['REQUEST_METHOD']);
}

function queryParam(string $key, mixed $default = null): mixed {
    return $_GET[$key] ?? $default;
}

// ── CORS ─────────────────────────────────────────────────────────
function applyCors(): void {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json; charset=utf-8');

    if (method() === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

// ── Pagination ───────────────────────────────────────────────────
function paginate(PDO $db, string $sql, array $params, int $page, int $perPage): array {
    $offset = ($page - 1) * $perPage;
    $countSql = preg_replace('/SELECT .+ FROM/is', 'SELECT COUNT(*) FROM', $sql);
    $countSql = preg_replace('/ORDER BY .+/i', '', $countSql);

    $countStmt = $db->prepare($countSql);
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $stmt = $db->prepare($sql . " LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $data = $stmt->fetchAll();

    return [
        'data'       => $data,
        'pagination' => [
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int)ceil($total / $perPage),
            'from'         => $offset + 1,
            'to'           => min($offset + $perPage, $total),
        ],
    ];
}

// ── Audit log helper ─────────────────────────────────────────────
function auditLog(int $userId = null, string $action = '', string $entityType = null,
                  int $entityId = null, mixed $old = null, mixed $new = null): void {
    try {
        db()->prepare(
            "INSERT INTO audit_log (user_id, action, entity_type, entity_id,
             old_value, new_value, ip_address, user_agent)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        )->execute([
            $userId, $action, $entityType, $entityId,
            $old ? json_encode($old) : null,
            $new  ? json_encode($new)  : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);
    } catch (Throwable) { /* never break the main request */ }
}

// ── Notification helper ──────────────────────────────────────────
function pushNotification(int $userId, string $type, string $title, string $body,
                           array $data = []): void {
    try {
        db()->prepare(
            "INSERT INTO notification (user_id, type, title, body, data)
             VALUES (?, ?, ?, ?, ?)"
        )->execute([$userId, $type, $title, $body, $data ? json_encode($data) : null]);
    } catch (Throwable) {}
}

// ── Sequence helper (atomic — uses daily_sequence table) ─────────
// INSERT … ON DUPLICATE KEY UPDATE is atomic in MySQL.
// Two simultaneous requests will never receive the same number.
function nextDailySeq(string $type): int {
    $today = date('Y-m-d');
    db()->prepare(
        "INSERT INTO daily_sequence (seq_date, seq_type, last_val)
         VALUES (?, ?, 1)
         ON DUPLICATE KEY UPDATE last_val = last_val + 1"
    )->execute([$today, $type]);

    $stmt = db()->prepare(
        "SELECT last_val FROM daily_sequence WHERE seq_date = ? AND seq_type = ?"
    );
    $stmt->execute([$today, $type]);
    return (int)$stmt->fetchColumn();
}

// ── Order ref generator ──────────────────────────────────────────
// Delivery / dine-in: HM-20250513-0001  (resets daily)
function generateOrderRef(): string {
    $prefix = db()->query(
        "SELECT setting_value FROM system_setting WHERE setting_key='order_ref_prefix'"
    )->fetchColumn() ?: 'HM';
    $date = date('Ymd');
    $seq  = str_pad(nextDailySeq('order'), 4, '0', STR_PAD_LEFT);
    return "$prefix-$date-$seq";
}

// ── Collection number generator ───────────────────────────────────
// Pickup orders only: A0001 – A9999, resets every day at midnight
function generateCollectionNo(): string {
    $seq = nextDailySeq('collection');
    if ($seq > 9999) {
        $seq = $seq % 9999 ?: 9999; // wrap gracefully if ever exceeded
    }
    return 'A' . str_pad($seq, 4, '0', STR_PAD_LEFT);
}

// ── Receipt number generator ─────────────────────────────────────
// RCP-HM-20250513-0001  (resets daily)
function generateReceiptNo(): string {
    $prefix = db()->query(
        "SELECT setting_value FROM system_setting WHERE setting_key='receipt_prefix'"
    )->fetchColumn() ?: 'RCP-HM';
    $date = date('Ymd');
    $seq  = str_pad(nextDailySeq('receipt'), 4, '0', STR_PAD_LEFT);
    return "$prefix-$date-$seq";
}

// ── Validation class ─────────────────────────────────────────────
class Validator {
    private array $data;
    private array $errors = [];

    const ROLES         = ['super_admin','manager','cashier','driver','customer'];
    const ORDER_STATUS  = ['pending','confirmed','preparing','ready','dispatched',
                           'out_for_delivery','delivered','completed','cancelled','refunded'];
    const PAY_METHODS   = ['Mobile Money','Credit Card','Bank Transfer','Cash on Delivery'];
    const PAY_STATUS    = ['pending','paid','failed','refunded','partial'];
    const DEL_STATUS    = ['pending','assigned','out_for_delivery','en_route','arrived','confirmed','failed','cancelled'];
    const ORDER_TYPES   = ['delivery','pickup','dine_in'];
    const DELIVERY_TYPES = ['Standard','Express','Scheduled','Pickup'];
    const CHANNELS      = ['web','android','desktop','kiosk'];
    const CITIES        = ['Mbabane','Manzini','Lobamba','Matsapha','Siteki',
                           'Nhlangano',"Pigg's Peak",'Big Bend','Mankayane','Hluti'];
    const BADGES        = ['fresh','new','premium','offer','seasonal','popular'];
    const CAT_TYPES     = ['Butchery','Restaurant','Liquor'];

    public function __construct(array $data) { $this->data = $data; }

    private function get(string $f): mixed { return $this->data[$f] ?? null; }
    public  function errors(): array  { return $this->errors; }
    public  function first():  string { return $this->errors[0] ?? ''; }
    public  function fails():  bool   { return !empty($this->errors); }

    public function required(string $f, string $label = ''): static {
        $v = $this->get($f);
        $l = $label ?: ucfirst($f);
        if ($v === null || $v === '' || (is_string($v) && trim($v) === ''))
            $this->errors[] = "$l is required.";
        return $this;
    }

    public function minLen(string $f, int $min, string $label = ''): static {
        $v = $this->get($f); $l = $label ?: ucfirst($f);
        if (is_string($v) && mb_strlen(trim($v)) < $min)
            $this->errors[] = "$l must be at least $min characters.";
        return $this;
    }

    public function maxLen(string $f, int $max, string $label = ''): static {
        $v = $this->get($f); $l = $label ?: ucfirst($f);
        if (is_string($v) && mb_strlen(trim($v)) > $max)
            $this->errors[] = "$l must not exceed $max characters.";
        return $this;
    }

    public function email(string $f, string $label = 'Email'): static {
        $v = $this->get($f);
        if ($v !== null && $v !== '' && !filter_var(trim($v), FILTER_VALIDATE_EMAIL))
            $this->errors[] = "$label must be a valid email address.";
        return $this;
    }

    public function phone(string $f, string $label = 'Phone'): static {
        $v = $this->get($f);
        if ($v !== null && $v !== '') {
            $norm = preg_replace('/\s/', '', trim($v));
            // Accept: +268XXXXXXXX (with +), 268XXXXXXXX (11 digits no +), or any +country format
            $valid = preg_match('/^\+\d{7,15}$/', $norm)
                  || preg_match('/^268\d{8}$/', $norm);
            if (!$valid)
                $this->errors[] = "$label must be a valid number (e.g. 26876000000 or +26876000000).";
        }
        return $this;
    }

    public function numeric(string $f, string $label = ''): static {
        $v = $this->get($f); $l = $label ?: ucfirst($f);
        if ($v !== null && $v !== '' && !is_numeric($v))
            $this->errors[] = "$l must be a number.";
        return $this;
    }

    public function min(string $f, float $min, string $label = ''): static {
        $v = $this->get($f); $l = $label ?: ucfirst($f);
        if (is_numeric($v) && (float)$v < $min)
            $this->errors[] = "$l must be at least $min.";
        return $this;
    }

    public function max(string $f, float $max, string $label = ''): static {
        $v = $this->get($f); $l = $label ?: ucfirst($f);
        if (is_numeric($v) && (float)$v > $max)
            $this->errors[] = "$l must not exceed $max.";
        return $this;
    }

    public function integer(string $f, string $label = ''): static {
        $v = $this->get($f); $l = $label ?: ucfirst($f);
        if ($v !== null && $v !== '' && filter_var($v, FILTER_VALIDATE_INT) === false)
            $this->errors[] = "$l must be a whole number.";
        return $this;
    }

    public function inList(string $f, array $list, string $label = ''): static {
        $v = $this->get($f); $l = $label ?: ucfirst($f);
        if ($v !== null && $v !== '' && !in_array($v, $list, true))
            $this->errors[] = "$l must be one of: " . implode(', ', $list) . ".";
        return $this;
    }

    public function lettersOnly(string $f, string $label = ''): static {
        $v = $this->get($f); $l = $label ?: ucfirst($f);
        if ($v !== null && $v !== '' && !preg_match("/^[\p{L}\s\-']+$/u", trim($v)))
            $this->errors[] = "$l may only contain letters, spaces, hyphens or apostrophes.";
        return $this;
    }

    public function password(string $f, string $label = 'Password'): static {
        $v = $this->get($f);
        if (!$v) return $this;
        if (strlen($v) < 8)
            $this->errors[] = "$label must be at least 8 characters.";
        elseif (strlen($v) > 72)
            $this->errors[] = "$label must not exceed 72 characters.";
        elseif (!preg_match('/[A-Z]/', $v))
            $this->errors[] = "$label must contain at least one uppercase letter.";
        elseif (!preg_match('/[0-9]/', $v))
            $this->errors[] = "$label must contain at least one number.";
        elseif (!preg_match('/[^A-Za-z0-9]/', $v))
            $this->errors[] = "$label must include at least one special character (e.g. @, #, !).";
        return $this;
    }

    public function uniqueEmail(PDO $db, string $f, int $excludeId = 0): static {
        $v = $this->get($f);
        if (!$v) return $this;
        $email = strtolower(trim($v));
        $stmt  = $db->prepare("SELECT user_id FROM user WHERE email=? AND user_id!=? LIMIT 1");
        $stmt->execute([$email, $excludeId]);
        if ($stmt->fetch()) {
            $masked = preg_replace('/^(.{2})(.*)(@.*)$/', '$1***$3', $email);
            $this->errors[] = "An account already exists for $masked — please sign in instead.";
        }
        return $this;
    }

    public function uniquePhone(PDO $db, string $f, int $excludeId = 0): static {
        $v = $this->get($f);
        if (!$v) return $this;
        $norm = preg_replace('/\s/', '', trim($v));
        $stmt = $db->prepare(
            "SELECT user_id FROM user WHERE REPLACE(phone,' ','')=? AND user_id!=? LIMIT 1"
        );
        $stmt->execute([$norm, $excludeId]);
        if ($stmt->fetch())
            $this->errors[] = "That phone number is already registered.";
        return $this;
    }

    public function exists(PDO $db, string $table, string $col, string $f, string $label = ''): static {
        $v = $this->get($f); $l = $label ?: ucfirst($f);
        if ($v === null || $v === '') return $this;
        $stmt = $db->prepare("SELECT 1 FROM `$table` WHERE `$col`=? LIMIT 1");
        $stmt->execute([$v]);
        if (!$stmt->fetch()) $this->errors[] = "$l not found.";
        return $this;
    }
}
