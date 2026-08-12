<?php
// FIX: OTP expiry was 120 seconds (2 min) but register.php showed a 5-minute (300s) countdown.
// Changed expiry to 300 seconds to match the UI timer.
session_start();

if(isset($_POST['otp'])){

    if(!isset($_SESSION['otp'])){
        echo "expired";
        exit;
    }

    // FIX: was 120 (2 min) — now 300 (5 min) to match the countdown timer on register.php
    if(time() - $_SESSION['otp_time'] > 300){
        echo "expired";
        exit;
    }

    if($_POST['otp'] == $_SESSION['otp']){
        $_SESSION['verified'] = true;
        echo "success";
    } else {
        echo "wrong";
    }
}
?>
