<?php
// =============================================
// place_order.php — JSON API with Razorpay support
// Accepts razorpay_payment_id for CARD payments
// =============================================
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

include(__DIR__ . "/../includes/db.php");

$email = mysqli_real_escape_string($conn, $_SESSION['email']);

$userQuery = mysqli_query($conn, "SELECT id FROM quigly_table WHERE email='$email' LIMIT 1");
$user = mysqli_fetch_assoc($userQuery);

if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

$user_id  = (int) $user['id'];
$product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$name     = trim($_POST['name']     ?? '');
$phone    = trim($_POST['phone']    ?? '');
$address  = trim($_POST['address']  ?? '');
$payment_method = trim($_POST['payment_method'] ?? 'COD');
$razorpay_payment_id = trim($_POST['razorpay_payment_id'] ?? '');

// Validate
if ($product_id <= 0 || $name === '' || $phone === '' || $address === '') {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

$allowed_payments = ['COD', 'UPI', 'CARD'];
if (!in_array($payment_method, $allowed_payments, true)) {
    $payment_method = 'COD';
}

$productQuery = mysqli_query($conn,
    "SELECT id, name, image, price FROM products WHERE id = $product_id LIMIT 1"
);
$product = mysqli_fetch_assoc($productQuery);

if (!$product) {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit;
}

$quantity = 1;
$total    = (float) $product['price'] * $quantity;

// Sanitize text fields
$name           = mysqli_real_escape_string($conn, $name);
$phone          = mysqli_real_escape_string($conn, $phone);
$address        = mysqli_real_escape_string($conn, $address);
$payment_method = mysqli_real_escape_string($conn, $payment_method);
$razorpay_payment_id = mysqli_real_escape_string($conn, $razorpay_payment_id);

// If CARD payment with a valid Razorpay ID, mark as Paid
$payment_status = 'Pending';
if ($payment_method === 'CARD' && $razorpay_payment_id !== '') {
    $payment_status = 'Paid';
}

// Auto-add razorpay_payment_id column if it doesn't exist
$colCheck = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'razorpay_payment_id'");
if ($colCheck && mysqli_num_rows($colCheck) === 0) {
    mysqli_query($conn, "ALTER TABLE orders ADD COLUMN razorpay_payment_id VARCHAR(100) DEFAULT NULL AFTER payment_status");
}

mysqli_begin_transaction($conn);

try {
    $orderSql = "
        INSERT INTO orders (
            user_id, total, status,
            customer_name, customer_email, customer_phone,
            customer_address, payment_method, payment_status, razorpay_payment_id, product_id
        ) VALUES (
            '$user_id', '$total', 'Processing',
            '$name', '$email', '$phone',
            '$address', '$payment_method', '$payment_status', '$razorpay_payment_id', '$product_id'
        )
    ";

    if (!mysqli_query($conn, $orderSql)) {
        throw new Exception(mysqli_error($conn));
    }

    $order_id = mysqli_insert_id($conn);

    $itemSql = "
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES ('$order_id', '$product_id', '$quantity', '{$product['price']}')
    ";

    if (!mysqli_query($conn, $itemSql)) {
        throw new Exception(mysqli_error($conn));
    }

    mysqli_commit($conn);

    // Send confirmation email to user
    try {
        require_once __DIR__ . '/../../mail_function.php';
        $errorMsg = '';
        sendPaymentSuccessEmail(
            $email,
            $_POST['name'] ?? 'Customer',
            $order_id,
            $product['name'] ?? 'Product',
            $total,
            $payment_method,
            $_POST['address'] ?? 'Shipping Address',
            $errorMsg
        );
    } catch (Exception $e) {
        error_log("Order confirmation email failed: " . $e->getMessage());
    }

    echo json_encode([
        'status'   => 'success',
        'message'  => 'Order placed successfully',
        'order_id' => $order_id
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

