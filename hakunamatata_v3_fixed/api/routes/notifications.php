<?php
// ================================================================
//  ROUTE: /api/notifications
// ================================================================
$m    = method();
$auth = requireAuth();

if ($m === 'GET') {
    $page    = max(1, (int)queryParam('page', 1));
    $perPage = min(50, (int)queryParam('per_page', 20));
    $unread  = queryParam('unread');
    $where   = ['user_id=?']; $params = [$auth['user_id']];
    if ($unread === '1') { $where[] = 'is_read=0'; }
    $sql = "SELECT * FROM notification WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC";
    respond(paginate(db(), $sql, $params, $page, $perPage));
}

// Mark as read
if ($m === 'PATCH' && $id && $action === 'read') {
    db()->prepare(
        "UPDATE notification SET is_read=1, read_at=NOW() WHERE notif_id=? AND user_id=?"
    )->execute([$id, $auth['user_id']]);
    respondOk([], 'Marked as read');
}

// Mark all read
if ($m === 'PATCH' && !$id) {
    db()->prepare(
        "UPDATE notification SET is_read=1, read_at=NOW() WHERE user_id=? AND is_read=0"
    )->execute([$auth['user_id']]);
    respondOk([], 'All notifications marked as read');
}

if ($m === 'DELETE' && $id) {
    db()->prepare("DELETE FROM notification WHERE notif_id=? AND user_id=?")->execute([$id, $auth['user_id']]);
    respondOk([], 'Notification deleted');
}

respondError('Notifications route not found', 404);
