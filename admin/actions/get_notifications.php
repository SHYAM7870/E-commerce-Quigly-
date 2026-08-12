<?php
// admin/actions/get_notifications.php
// Returns JSON so the header JS can update the bell + list in real-time.

include_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$unreadCount = (int)(mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM notifications WHERE is_read = 0")
)['total'] ?? 0);

$res = mysqli_query($conn, "SELECT * FROM notifications ORDER BY id DESC LIMIT 8");

$notifications = [];
while ($row = mysqli_fetch_assoc($res)) {
    $notifications[] = [
        'id'         => (int)$row['id'],
        'message'    => $row['message'],
        'type'       => $row['type'] ?? 'default',
        'is_read'    => (int)$row['is_read'],
        'created_at' => !empty($row['created_at'])
                          ? date('d M Y, h:i A', strtotime($row['created_at']))
                          : '',
    ];
}

echo json_encode([
    'unread_count'  => $unreadCount,
    'notifications' => $notifications,
]);
?>
