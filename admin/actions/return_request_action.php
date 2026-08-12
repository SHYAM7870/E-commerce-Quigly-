<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/db.php';

// FIX: JS reads data.error for failures, data.status === 'ok' for success
// Old code sent data.message — JS could never read the error code → always "Something went wrong"
function respond(string $status, string $code, array $extra = []): void {
    $payload = ['status' => $status];
    if ($status === 'ok') {
        $payload['message'] = $code;
    } else {
        $payload['error'] = $code; // JS reads data.error
    }
    // Include any extra debug keys (e.g. 'msg' for db_error)
    echo json_encode(array_merge($payload, $extra));
    exit;
}

if (!isset($_SESSION['email'])) {
    respond('error', 'not_logged_in');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond('error', 'invalid_method');
}

$email = mysqli_real_escape_string($conn, $_SESSION['email']);
$userQ = mysqli_query($conn, "SELECT id FROM quigly_table WHERE email='{$email}' LIMIT 1");
$user  = $userQ ? mysqli_fetch_assoc($userQ) : null;

if (!$user) {
    respond('error', 'not_logged_in');
}

$userId = (int)$user['id'];

$orderId       = (int)($_POST['order_id'] ?? 0);
$orderItemId   = (int)($_POST['order_item_id'] ?? 0);
$requestType   = strtolower(trim($_POST['request_type'] ?? 'return'));
$preferred     = strtolower(trim($_POST['preferred_resolution'] ?? 'full_refund'));
$reason        = trim($_POST['reason'] ?? '');
$details       = trim($_POST['details'] ?? '');
$pickupAddress = trim($_POST['pickup_address'] ?? '');

$allowedType      = ['return', 'refund', 'replacement', 'exchange'];
$allowedPreferred = ['full_refund', 'replacement_item', 'store_credit'];

// Validate required fields
if ($orderId <= 0 || $reason === '' || $pickupAddress === '') {
    respond('error', 'missing_fields', [
        'debug' => [
            'order_id'       => $orderId,
            'reason_empty'   => ($reason === ''),
            'address_empty'  => ($pickupAddress === ''),
        ]
    ]);
}

if (!in_array($requestType, $allowedType, true)) {
    $requestType = 'return';
}
if (!in_array($preferred, $allowedPreferred, true)) {
    $preferred = 'full_refund';
}

/* Check order ownership + delivery status */
$orderStmt = mysqli_prepare($conn, "SELECT id, status FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
mysqli_stmt_bind_param($orderStmt, 'ii', $orderId, $userId);
mysqli_stmt_execute($orderStmt);
$orderRes = mysqli_stmt_get_result($orderStmt);
$order    = $orderRes ? mysqli_fetch_assoc($orderRes) : null;

if (!$order || strtolower(trim((string)$order['status'])) !== 'delivered') {
    respond('error', 'not_eligible');
}

/* If order_item_id exists, make sure it belongs to this order */
$productId = 0;
if ($orderItemId > 0) {
    $itemStmt = mysqli_prepare($conn, "
        SELECT id, product_id
        FROM order_items
        WHERE id = ? AND order_id = ?
        LIMIT 1
    ");
    mysqli_stmt_bind_param($itemStmt, 'ii', $orderItemId, $orderId);
    mysqli_stmt_execute($itemStmt);
    $itemRes = mysqli_stmt_get_result($itemStmt);
    $item    = $itemRes ? mysqli_fetch_assoc($itemRes) : null;

    if (!$item) {
        respond('error', 'invalid_order_item');
    }

    $productId = (int)$item['product_id'];
} else {
    $fallbackStmt = mysqli_prepare($conn, "
        SELECT id, product_id
        FROM order_items
        WHERE order_id = ?
        ORDER BY id ASC
        LIMIT 1
    ");
    mysqli_stmt_bind_param($fallbackStmt, 'i', $orderId);
    mysqli_stmt_execute($fallbackStmt);
    $fallbackRes = mysqli_stmt_get_result($fallbackStmt);
    $fallback    = $fallbackRes ? mysqli_fetch_assoc($fallbackRes) : null;

    if ($fallback) {
        $orderItemId = (int)$fallback['id'];
        $productId   = (int)$fallback['product_id'];
    }
}

/* Duplicate check */
if ($orderItemId > 0) {
    $dupStmt = mysqli_prepare($conn, "
        SELECT id
        FROM return_requests
        WHERE order_item_id = ? AND user_id = ?
          AND status NOT IN ('rejected','completed')
        LIMIT 1
    ");
    mysqli_stmt_bind_param($dupStmt, 'ii', $orderItemId, $userId);
} else {
    $dupStmt = mysqli_prepare($conn, "
        SELECT id
        FROM return_requests
        WHERE order_id = ? AND user_id = ?
          AND order_item_id IS NULL
          AND status NOT IN ('rejected','completed')
        LIMIT 1
    ");
    mysqli_stmt_bind_param($dupStmt, 'ii', $orderId, $userId);
}

mysqli_stmt_execute($dupStmt);
$dupRes = mysqli_stmt_get_result($dupStmt);

if ($dupRes && mysqli_fetch_assoc($dupRes)) {
    respond('error', 'duplicate');
}

/* Proof uploads */
$proofImagePaths = [];

if (isset($_FILES['proof_images']) && is_array($_FILES['proof_images']['name'] ?? null) && !empty($_FILES['proof_images']['name'][0])) {
    $uploadDir = __DIR__ . '/../../uploads/return_proofs/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxSize     = 5 * 1024 * 1024;
    $maxFiles    = 5;

    $files = $_FILES['proof_images'];
    $count = min(count($files['name']), $maxFiles);

    for ($i = 0; $i < $count; $i++) {
        if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) continue;
        if (($files['size'][$i] ?? 0) > $maxSize) continue;

        $tmpPath  = $files['tmp_name'][$i] ?? '';
        $imgInfo  = @getimagesize($tmpPath);
        $realMime = $imgInfo['mime'] ?? '';

        if (!in_array($realMime, $allowedMime, true)) continue;

        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];

        $ext      = $mimeToExt[$realMime] ?? 'jpg';
        $filename = 'rp_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dest     = $uploadDir . $filename;

        if (move_uploaded_file($tmpPath, $dest)) {
            $proofImagePaths[] = 'uploads/return_proofs/' . $filename;
        }
    }
}

$proofImagesJson = json_encode($proofImagePaths);

/* Insert using prepared statement — no manual escaping needed */
$insertStmt = mysqli_prepare($conn, "
    INSERT INTO return_requests
        (order_id, order_item_id, user_id, product_id, request_type, preferred_resolution,
         reason, details, pickup_address, proof_images, status)
    VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
");

if (!$insertStmt) {
    respond('error', 'db_error', ['msg' => mysqli_error($conn)]);
}

mysqli_stmt_bind_param(
    $insertStmt,
    'iiiissssss',
    $orderId,
    $orderItemId,
    $userId,
    $productId,
    $requestType,
    $preferred,
    $reason,
    $details,
    $pickupAddress,
    $proofImagesJson
);

if (!mysqli_stmt_execute($insertStmt)) {
    respond('error', 'db_error', ['msg' => mysqli_stmt_error($insertStmt)]);
}

respond('ok', 'submitted');