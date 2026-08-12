<?php
session_start();
header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request";
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email address";
    exit;
}

require_once __DIR__ . '/admin/includes/db.php';
require_once __DIR__ . '/mail_function.php';

$checkStmt = $conn->prepare("SELECT id FROM quigly_table WHERE email = ? LIMIT 1");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    $checkStmt->close();
    echo "Email already registered. Please login instead.";
    exit;
}
$checkStmt->close();

$otp = random_int(100000, 999999);

$_SESSION['otp'] = $otp;
$_SESSION['email'] = $email;
$_SESSION['otp_time'] = time();
session_regenerate_id(true);

$errorMsg = '';
if (sendOTP($email, $otp, $errorMsg)) {
    echo "success";
} else {
    echo "Failed to send OTP: " . $errorMsg;
}
?>