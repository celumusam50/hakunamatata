<?php
// ================================================================
//  ROUTE: /api/settings
// ================================================================
$m    = method();

if ($m === 'GET') {
    // Public settings (subset safe to expose)
    $public = ['business_name','business_tagline','business_phone','business_email',
               'business_address','currency_symbol','currency_code','tax_rate',
               'delivery_fee_std','delivery_fee_exp','free_delivery_min','logo_url'];
    $auth = optionalAuth();
    if ($auth && isManagement($auth)) {
        // Management gets all settings
        $stmt = db()->query("SELECT setting_key, setting_value, label, description FROM system_setting ORDER BY setting_key");
    } else {
        $in   = implode(',', array_fill(0, count($public), '?'));
        $stmt = db()->prepare("SELECT setting_key, setting_value FROM system_setting WHERE setting_key IN ($in)");
        $stmt->execute($public);
    }
    $rows   = $stmt->fetchAll();
    $result = [];
    foreach ($rows as $r) $result[$r['setting_key']] = $r['setting_value'];
    respondOk($result);
}

if ($m === 'PUT' || $m === 'PATCH') {
    $auth = requireAuth(['super_admin']);
    $b    = body();
    foreach ($b as $key => $value) {
        db()->prepare(
            "UPDATE system_setting SET setting_value=?, updated_by=? WHERE setting_key=?"
        )->execute([trim((string)$value), $auth['user_id'], $key]);
    }
    auditLog($auth['user_id'], 'settings.update', null, null, null, $b);
    respondOk([], 'Settings updated');
}

respondError('Settings route not found', 404);
