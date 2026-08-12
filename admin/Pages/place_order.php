<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $customer_email = mysqli_real_escape_string($conn, $_POST['customer_email']);
    $customer_phone = mysqli_real_escape_string($conn, $_POST['customer_phone']);
    $customer_address = mysqli_real_escape_string($conn, $_POST['customer_address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $product_id = (int) $_POST['product_id'];
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_image = mysqli_real_escape_string($conn, $_POST['product_image']);
    $quantity = (int) $_POST['quantity'];
    $price = str_replace(['₹', ','], '', $_POST['price']);
    $price = (float)$price;
    $total = $price * $quantity;

    $query = "
        INSERT INTO orders(
            customer_name,
            customer_email,
            customer_phone,
            customer_address,
            payment_method,
            total,
            product_id
        )
        VALUES(
            '$customer_name',
            '$customer_email',
            '$customer_phone',
            '$customer_address',
            '$payment_method',
            '$total',
            '$product_id'
        )
    ";
    $order_result = mysqli_query($conn, $query);
    if (!$order_result) {
        die("Order Insert Failed : " . mysqli_error($conn));
    }
    $order_id = mysqli_insert_id($conn);
    $item_query = "
        INSERT INTO order_items(
            order_id,
            product_id,
            quantity,
            price,
            product_name,
            product_image
        )
        VALUES(
            '$order_id',
            '$product_id',
            '$quantity',
            '$price',
            '$product_name',
            '$product_image'
        )
    ";
    $item_result = mysqli_query($conn, $item_query);
    if (!$item_result) {
        die("Order Item Insert Failed : " . mysqli_error($conn));
    }
    $message =
    "New order placed by " .
    $customer_name;

    mysqli_query(
    $conn,
    "INSERT INTO notifications
    (message,type,is_read)
    VALUES(
    '$message',
    'order',
    0
    )"
    );
    echo json_encode(['status' => 'success']);
    exit();    
} else {
    header('Location:index.php');
    exit();
}
?>