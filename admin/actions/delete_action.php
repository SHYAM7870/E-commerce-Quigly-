<?php
session_start();

// Auth: only admin
if (!isset($_SESSION['email']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: /Quigly/login.php?error=Unauthorized");
    exit;
}

include_once('../../function.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['btn'] ?? '') === 'quigly_table') {
    $id   = (int)($_GET['id'] ?? 0);
    // FIX: logic was inverted — delete_data() now returns true on success
    $call = delete_data('quigly_table', $id);
    if ($call) {
        echo "<script>alert('User Deleted Successfully');window.location.href='../pages/user_list.php';</script>";
    } else {
        echo "<script>alert('Failed to Delete User');window.location.href='../pages/user_list.php';</script>";
    }
}
?>
