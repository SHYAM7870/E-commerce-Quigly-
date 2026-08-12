<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['email'])) {
    header('Location: /Quigly/login.php');
    exit;
}

$email   = mysqli_real_escape_string($conn, $_SESSION['email']);
$subject = mysqli_real_escape_string($conn, trim($_POST['subject'] ?? ''));
$message = mysqli_real_escape_string($conn, trim($_POST['message'] ?? ''));

if ($subject === '' || $message === '') {
    header('Location: ../../index.php?section=support&error=empty_fields');
    exit;
}

$sql = "INSERT INTO support_tickets (user_email, subject, message) VALUES ('{$email}', '{$subject}', '{$message}')";
mysqli_query($conn, $sql);

header('Location: ../../index.php?section=support&ticket_sent=1');
exit;
?>
