<?php

session_start();

header('Content-Type: text/plain; charset=utf-8');


// ==========================================
// ONLY POST REQUEST ALLOWED
// ==========================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    echo "Invalid request";
    exit;
}


// ==========================================
// GET EMAIL
// ==========================================

$email = trim($_POST['email'] ?? '');


// ==========================================
// VALIDATE EMAIL
// ==========================================

if (
    empty($email) ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {

    echo "Invalid email address";
    exit;
}


// ==========================================
// DATABASE + MAIL
// ==========================================

require_once __DIR__ . '/admin/includes/db.php';
require_once __DIR__ . '/mail_function.php';


// ==========================================
// CHECK DATABASE CONNECTION
// ==========================================

if (!isset($conn) || $conn->connect_error) {

    echo "Database connection failed.";
    exit;
}


// ==========================================
// CHECK IF EMAIL ALREADY EXISTS
// ==========================================

$checkStmt = $conn->prepare(
    "SELECT id FROM quigly_table WHERE email = ? LIMIT 1"
);

if (!$checkStmt) {

    echo "Database query error.";
    exit;
}

$checkStmt->bind_param(
    "s",
    $email
);

$checkStmt->execute();

$checkStmt->store_result();


if ($checkStmt->num_rows > 0) {

    $checkStmt->close();

    echo "Email already registered. Please login instead.";
    exit;
}

$checkStmt->close();


// ==========================================
// GENERATE OTP
// ==========================================

try {

    $otp = random_int(
        100000,
        999999
    );

} catch (Throwable $e) {

    echo "Unable to generate OTP.";
    exit;
}


// ==========================================
// SEND EMAIL FIRST
// ==========================================

$errorMsg = '';

$mailSent = sendOTP(
    $email,
    $otp,
    $errorMsg
);


// ==========================================
// EMAIL FAILED
// ==========================================

if (!$mailSent) {

    /*
     * IMPORTANT:
     * Do NOT store OTP in session
     * if email was not sent.
     */

    error_log(
        "Quigly OTP failed for {$email}: {$errorMsg}"
    );

    echo "Failed to send OTP: " . $errorMsg;

    exit;
}


// ==========================================
// EMAIL SUCCESS
// ==========================================

// Store OTP
$_SESSION['otp'] = (string) $otp;

$_SESSION['email'] = $email;

$_SESSION['otp_time'] = time();

$_SESSION['verified'] = false;


// ==========================================
// SUCCESS
// ==========================================

echo "success";

exit;

?>