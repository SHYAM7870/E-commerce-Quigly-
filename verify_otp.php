<?php

session_start();

header('Content-Type: text/plain; charset=utf-8');


// ==========================================
// ONLY POST REQUEST
// ==========================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    echo "Invalid request";
    exit;
}


// ==========================================
// GET OTP
// ==========================================

$enteredOtp = trim(
    $_POST['otp'] ?? ''
);


// ==========================================
// VALIDATE OTP FORMAT
// ==========================================

if (
    empty($enteredOtp) ||
    !preg_match('/^\d{6}$/', $enteredOtp)
) {

    echo "wrong";
    exit;
}


// ==========================================
// CHECK SESSION OTP
// ==========================================

if (
    !isset($_SESSION['otp']) ||
    !isset($_SESSION['otp_time']) ||
    !isset($_SESSION['email'])
) {

    echo "expired";
    exit;
}


// ==========================================
// OTP EXPIRATION
// ==========================================

// 5 minutes = 300 seconds

$otpAge = time() - $_SESSION['otp_time'];


if ($otpAge > 300) {

    unset(
        $_SESSION['otp'],
        $_SESSION['otp_time']
    );

    echo "expired";
    exit;
}


// ==========================================
// VERIFY OTP
// ==========================================

$storedOtp = (string) $_SESSION['otp'];


if (hash_equals($storedOtp, $enteredOtp)) {

    // OTP correct
    $_SESSION['verified'] = true;


    // OTP should not be reusable
    unset($_SESSION['otp']);


    echo "success";

    exit;
}


// ==========================================
// WRONG OTP
// ==========================================

echo "wrong";

exit;

?>