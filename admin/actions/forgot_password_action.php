<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';
include_once('mail_function.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    exit;
}

$stmt = $conn->prepare("SELECT id, name, email FROM quigly_table WHERE email = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

$genericMessage = 'If this email exists, a reset link has been sent to your inbox.';

if ($row = $result->fetch_assoc()) {
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expiresAt = date('Y-m-d H:i:s', time() + 1800);

    $clear = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $clear->bind_param('s', $email);
    $clear->execute();
    $clear->close();

    $insert = $conn->prepare("INSERT INTO password_resets (email, token_hash, expires_at) VALUES (?, ?, ?)");
    $insert->bind_param('sss', $email, $tokenHash, $expiresAt);

    if ($insert->execute()) {
        $resetLink = rtrim(SITE_URL, '/') . '/admin/Pages/reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($email);
        sendPasswordResetEmail($email, $row['name'] ?? $email, $resetLink);
    }

    $insert->close();
}

$stmt->close();

echo json_encode(['status' => 'success', 'message' => $genericMessage]);
exit;