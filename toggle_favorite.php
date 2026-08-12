<?php
require_once __DIR__ . '/includes/app.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$userId    = (int) $_SESSION['user_id'];
$listingId = (int) ($_POST['listing_id'] ?? 0);

if ($listingId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid listing']);
    exit;
}

// Ensure table exists
admin_execute("CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    listing_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_listing (user_id, listing_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$existing = admin_query_one(
    'SELECT id FROM favorites WHERE user_id = ? AND listing_id = ?',
    'ii',
    [$userId, $listingId]
);

if ($existing) {
    admin_execute('DELETE FROM favorites WHERE user_id = ? AND listing_id = ?', 'ii', [$userId, $listingId]);
    $favorited = false;
} else {
    admin_execute('INSERT IGNORE INTO favorites (user_id, listing_id) VALUES (?, ?)', 'ii', [$userId, $listingId]);
    $favorited = true;
}

$countRow = admin_query_one(
    'SELECT COUNT(*) AS cnt FROM favorites WHERE listing_id = ?',
    'i',
    [$listingId]
);

echo json_encode([
    'success'   => true,
    'favorited' => $favorited,
    'count'     => (int) ($countRow['cnt'] ?? 0),
]);
