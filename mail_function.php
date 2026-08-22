<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/vendor/PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/vendor/PHPMailer-master/src/Exception.php';


function sendOTP($email, $otp, &$errorMsg = '')
{
    $mail = new PHPMailer(true);

    try {

        // =========================
        // SMTP CONFIGURATION
        // =========================

        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';

        $mail->SMTPAuth = true;

        // YOUR GMAIL ADDRESS
        $mail->Username = 'kamilashyamsankar@gmail.com';

        /*
         * IMPORTANT:
         * Use a GOOGLE APP PASSWORD here.
         *
         * DO NOT use your normal Gmail password.
         *
         * Example:
         * $mail->Password = 'abcdefghijklmnop';
         */
        $mail->Password = 'uwlmttpgyivjgcdo';

        // Gmail SMTP using STARTTLS
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port = 587;

        // Character encoding
        $mail->CharSet = 'UTF-8';

        // Timeout
        $mail->Timeout = 30;

        // Automatically use TLS
        $mail->SMTPAutoTLS = true;


        // =========================
        // SSL OPTIONS
        // =========================

        /*
         * These settings are useful for
         * localhost/XAMPP testing.
         */
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];


        // =========================
        // SENDER / RECEIVER
        // =========================

        $mail->setFrom(
            'kamilashyamsankar@gmail.com',
            'Quigly'
        );

        $mail->addAddress($email);


        // =========================
        // EMAIL CONTENT
        // =========================

        $mail->isHTML(true);

        $mail->Subject = 'Quigly - Email Verification OTP';


        // OTP template
        $templatePath = __DIR__ . '/otp_template.html';

        if (!file_exists($templatePath)) {

            throw new Exception(
                'otp_template.html file not found.'
            );
        }


        $template = file_get_contents($templatePath);

        if ($template === false) {

            throw new Exception(
                'Unable to read otp_template.html.'
            );
        }


        // Replace OTP placeholder
        $mail->Body = str_replace(
            '{{OTP_CODE}}',
            htmlspecialchars((string) $otp, ENT_QUOTES, 'UTF-8'),
            $template
        );


        // Plain text fallback
        $mail->AltBody =
            "Your Quigly verification OTP is: " .
            $otp .
            "\n\nThis OTP is valid for 5 minutes.";


        // =========================
        // SEND EMAIL
        // =========================

        $mail->send();

        return true;

    } catch (\Throwable $e) {

        $errorMsg = $e->getMessage();

        // Save error in PHP error log
        error_log(
            'Quigly OTP Mail Error: ' .
            $e->getMessage()
        );

        return false;
    }
}

function sendPaymentSuccessEmail($email, $customer_name, $order_id, $product_name, $total, $payment_method, $address, &$errorMsg = '')
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kamilashyamsankar@gmail.com';
        $mail->Password = 'uwlmttpgyivjgcdo';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->Timeout = 30;
        $mail->SMTPAutoTLS = true;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom('kamilashyamsankar@gmail.com', 'Quigly');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Quigly - Payment Successful & Order Confirmed! (#' . $order_id . ')';

        // Load the HTML template
        $templatePath = __DIR__ . '/payment_success_template.html';
        if (!file_exists($templatePath)) {
            throw new Exception('payment_success_template.html not found.');
        }

        $template = file_get_contents($templatePath);
        if ($template === false) {
            throw new Exception('Unable to read payment_success_template.html.');
        }

        // Replace placeholders
        $replacements = [
            '{{CUSTOMER_NAME}}'  => htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8'),
            '{{ORDER_ID}}'       => htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'),
            '{{PRODUCT_NAME}}'   => htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8'),
            '{{ORDER_TOTAL}}'    => htmlspecialchars(number_format((float)$total, 2), ENT_QUOTES, 'UTF-8'),
            '{{PAYMENT_METHOD}}' => htmlspecialchars($payment_method, ENT_QUOTES, 'UTF-8'),
            '{{SHIPPING_ADDRESS}}' => htmlspecialchars($address, ENT_QUOTES, 'UTF-8')
        ];

        $mail->Body = strtr($template, $replacements);

        // AltBody plain text fallback
        $mail->AltBody = "Hi " . $customer_name . ",\n\n" .
                         "Your payment for order #" . $order_id . " of " . $product_name . " was successful.\n" .
                         "Total Paid: $" . number_format((float)$total, 2) . "\n" .
                         "Payment Method: " . $payment_method . "\n" .
                         "Shipping Address: " . $address . "\n\n" .
                         "Thank you for shopping with Quigly!";

        $mail->send();
        return true;

    } catch (\Throwable $e) {
        $errorMsg = $e->getMessage();
        error_log('Quigly Payment Mail Error: ' . $e->getMessage());
        return false;
    }
}
?>