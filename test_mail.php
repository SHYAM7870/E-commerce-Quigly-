<?php
require 'mail_function.php';

$error = '';
if (sendOTP('your_test_email@gmail.com', '123456', $error)) {
    echo "Mail sent successfully";
} else {
    echo "Mail failed: " . $error;
}
?>