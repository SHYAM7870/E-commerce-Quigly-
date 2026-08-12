<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../Pages/user_list.php");
    exit;
}

include_once("../includes/db.php");

// Auth: only admin can update user records
if (!isset($_SESSION['email']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: /Quigly/login.php?error=Unauthorized");
    exit;
}

$id     = (int)($_POST['id']     ?? 0);
$name   = trim($_POST['name']   ?? '');
$email  = trim($_POST['email']  ?? '');
$number = trim($_POST['number'] ?? '');
$rawPw  = $_POST['password'] ?? '';

if ($id <= 0 || $name === '' || $email === '') {
    header("Location: ../Pages/user_list.php?msg=invalid");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../Pages/user_list.php?msg=invalid_email");
    exit;
}

if ($rawPw !== '') {
    // FIX: use password_hash — md5 is cryptographically broken
    $hashed = password_hash($rawPw, PASSWORD_DEFAULT);
    $stmt   = $conn->prepare(
        "UPDATE quigly_table SET name=?, email=?, number=?, password=? WHERE id=?"
    );
    $stmt->bind_param("ssssi", $name, $email, $number, $hashed, $id);
} else {
    $stmt = $conn->prepare(
        "UPDATE quigly_table SET name=?, email=?, number=? WHERE id=?"
    );
    $stmt->bind_param("sssi", $name, $email, $number, $id);
}

if ($stmt->execute()) {
    $stmt->close();
    header("Location: ../Pages/user_list.php?msg=updated");
    exit;
} else {
    $stmt->close();
    header("Location: ../Pages/user_list.php?msg=error");
    exit;
}
?>
