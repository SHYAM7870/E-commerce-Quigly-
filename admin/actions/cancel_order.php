<?php
// =============================================
// FIX 7: cancel_order.php
// Bug: No session/auth check — anyone could cancel any order
// Fix: Check session, verify order belongs to the requesting user
//      Admin can cancel any order; user can only cancel their own
// =============================================
session_start();
include_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['email'])) {
    header("Location: /Quigly/login.php?error=Please+login");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: /Quigly/index.php?error=invalid");
    exit;
}

$orderId = (int) $_GET['id'];
$role    = $_SESSION['role'] ?? 'user';

if ($role === 'admin') {
    // Admin can cancel any order
    $check = mysqli_query($conn, "SELECT id FROM orders WHERE id = $orderId LIMIT 1");
} else {
    // User can only cancel their own orders
    $email    = mysqli_real_escape_string($conn, $_SESSION['email']);
    $userRow  = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT id FROM quigly_table WHERE email='$email' LIMIT 1")
    );
    $user_id  = (int) ($userRow['id'] ?? 0);
    $check    = mysqli_query($conn,
        "SELECT id FROM orders WHERE id = $orderId AND user_id = $user_id LIMIT 1"
    );
}

if ($check && mysqli_num_rows($check) > 0) {
    $update = mysqli_query($conn, "UPDATE orders SET status = 'Cancelled' WHERE id = $orderId");

    if ($update) {
        if ($role === 'admin') {
            header("Location: ../Pages/orders_list.php?msg=cancelled");
        } else {
            header("Location: /Quigly/index.php#orders");
        }
        exit;
    }
}

// Fallback
if ($role === 'admin') {
    header("Location: ../Pages/orders_list.php?msg=error");
} else {
    header("Location: /Quigly/index.php?msg=error");
}
exit;
?>
