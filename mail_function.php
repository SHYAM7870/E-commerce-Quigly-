<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/vendor/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/vendor/PHPMailer-master/src/Exception.php';

function sendOTP($email, $otp, &$errorMsg = '')
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        // Put your real Gmail address here
        $mail->Username   = 'kamilashyamsankar@gmail.com';

        // IMPORTANT: this must be a Gmail APP PASSWORD, not your normal Gmail password
        $mail->Password   = 'maxosrmfwsgkipwj';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        $mail->Timeout    = 30;
        $mail->SMTPAutoTLS = true;

        // Good for localhost testing
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ]
        ];

        $mail->setFrom('kamilashyamsankar@gmail.com', 'Quigly');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'OTP Verification';

        $templatePath = __DIR__ . '/otp_template.html';
        if (!file_exists($templatePath)) {
            throw new Exception('OTP template file not found');
        }

        $template = file_get_contents($templatePath);
        if ($template === false) {
            throw new Exception('Could not read OTP template');
        }

        $mail->Body = str_replace('{{OTP_CODE}}', $otp, $template);

        $mail->send();
        return true;

    } catch (\Throwable $e) {
        $errorMsg = $e->getMessage();
        error_log('OTP mail error: ' . $errorMsg);
        return false;
    }
}
?>