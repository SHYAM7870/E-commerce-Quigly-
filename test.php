<?php

require_once __DIR__ . '/mail_function.php';


// ==========================================
// TEST EMAIL
// ==========================================

// CHANGE THIS TO THE EMAIL WHERE
// YOU WANT TO RECEIVE THE TEST OTP.

$testEmail = 'YOUR_TEST_EMAIL@gmail.com';


// ==========================================
// TEST OTP
// ==========================================

$testOtp = '123456';


// ==========================================
// SEND
// ==========================================

$error = '';

$result = sendOTP(
    $testEmail,
    $testOtp,
    $error
);


// ==========================================
// RESULT
// ==========================================

if ($result) {

    echo '<h2 style="color:green;">
            Mail sent successfully!
          </h2>';

    echo '<p>
            Check the inbox and spam folder.
          </p>';

} else {

    echo '<h2 style="color:red;">
            Mail failed!
          </h2>';

    echo '<p>
            <strong>Error:</strong>
          </p>';

    echo '<pre style="
        background:#f5f5f5;
        padding:15px;
        border-radius:8px;
        white-space:pre-wrap;
    ">' .
        htmlspecialchars($error) .
        '</pre>';
}

?>