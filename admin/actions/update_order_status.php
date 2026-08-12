<?php
session_start();
include_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['email']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: /Quigly/login.php?error=Unauthorized");
    exit;
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: ../Pages/orders_list.php?msg=invalid");
    exit;
}

$orderId = (int) $_GET['id'];
$status  = $_GET['status'];

$allowedStatuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

if (!in_array($status, $allowedStatuses, true)) {
    header("Location: ../Pages/orders_list.php?msg=invalid_status");
    exit;
}

$status = mysqli_real_escape_string($conn, $status);
$update = mysqli_query($conn, "UPDATE orders SET status = '$status' WHERE id = $orderId");

if ($update) {
    // Send delivery email notification
    if ($status === 'Delivered') {
        $orderInfo = mysqli_query($conn, "
            SELECT o.*, u.email AS user_email, u.name AS user_name
            FROM orders o
            INNER JOIN quigly_table u ON u.id = o.user_id
            WHERE o.id = $orderId
            LIMIT 1
        ");
        $orderRow = mysqli_fetch_assoc($orderInfo);
        if ($orderRow) {
            require_once __DIR__ . '/../../mail_function.php';
            sendDeliveryEmail($orderRow['user_email'], $orderRow['user_name'], $orderId, $orderRow['total'] ?? '0');
        }
    }
    header("Location: ../Pages/orders_list.php?msg=updated");
    exit;
}

header("Location: ../Pages/orders_list.php?msg=error");
exit;
?>
