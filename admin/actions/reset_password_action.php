<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$token = trim($_POST['token'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($token === '' || $email === '' || $password === '' || $confirm === '') {
    header('Location: reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($email) . '&error=All+fields+required');
    exit;
}

if ($password !== $confirm) {
    header('Location: reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($email) . '&error=Password+mismatch');
    exit;
}

$tokenHash = hash('sha256', $token);
$stmt = $conn->prepare("SELECT id, expires_at, used_at FROM password_resets WHERE email = ? AND token_hash = ? LIMIT 1");
$stmt->bind_param('ss', $email, $tokenHash);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    header('Location: forgot_password.php?error=Invalid+reset+link');
    exit;
}

if (!empty($row['used_at']) || strtotime($row['expires_at']) < time()) {
    header('Location: forgot_password.php?error=Reset+link+expired');
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE quigly_table SET password = ? WHERE email = ? LIMIT 1");
$update->bind_param('ss', $hashedPassword, $email);

if ($update->execute()) {
    $update->close();

    $mark = $conn->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ? LIMIT 1");
    $mark->bind_param('i', $row['id']);
    $mark->execute();
    $mark->close();

    $delete = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $delete->bind_param('s', $email);
    $delete->execute();
    $delete->close();

    header('Location: login.php?success=Password+updated+successfully');
    exit;
}

$update->close();
header('Location: reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($email) . '&error=Could+not+update+password');
exit;
