<?php
session_start();
include("../includes/db.php");

function goBack($msg) {
    header("Location: ../../index.php?msg=" . urlencode($msg));
    exit;
}

if (!isset($_SESSION['email'])) {
    header("Location: ../../login.php");
    exit;
}

$email = trim($_SESSION['email']);

$userStmt = $conn->prepare("SELECT id FROM quigly_table WHERE email = ? LIMIT 1");
$userStmt->bind_param("s", $email);
$userStmt->execute();
$userResult = $userStmt->get_result();

if (!$userResult || $userResult->num_rows === 0) {
    session_destroy();
    header("Location: ../../login.php");
    exit;
}

$user = $userResult->fetch_assoc();
$user_id = (int)$user['id'];

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$review_text = trim($_POST['review_text'] ?? '');

if ($order_id <= 0 || $product_id <= 0 || $rating < 1 || $rating > 5 || $review_text === '') {
    goBack('invalid_review');
}

if (mb_strlen($review_text) > 1000) {
    goBack('review_too_long');
}

$checkStmt = $conn->prepare("
    SELECT o.id
    FROM orders o
    WHERE o.id = ?
      AND o.user_id = ?
      AND LOWER(o.status) = 'delivered'
      AND (
            o.product_id = ?
            OR EXISTS (
                SELECT 1
                FROM order_items oi
                WHERE oi.order_id = o.id
                  AND oi.product_id = ?
            )
      )
    LIMIT 1
");
$checkStmt->bind_param("iiii", $order_id, $user_id, $product_id, $product_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if (!$checkResult || $checkResult->num_rows === 0) {
    goBack('not_allowed');
}

$alreadyStmt = $conn->prepare("
    SELECT id
    FROM reviews
    WHERE order_id = ?
      AND product_id = ?
      AND user_id = ?
    LIMIT 1
");
$alreadyStmt->bind_param("iii", $order_id, $product_id, $user_id);
$alreadyStmt->execute();
$alreadyResult = $alreadyStmt->get_result();

if ($alreadyResult && $alreadyResult->num_rows > 0) {
    goBack('already_reviewed');
}

$reviewStmt = $conn->prepare("
    INSERT INTO reviews (order_id, product_id, user_id, rating, review_text, status)
    VALUES (?, ?, ?, ?, ?, 'pending')
");
$reviewStmt->bind_param("iiiis", $order_id, $product_id, $user_id, $rating, $review_text);

if ($reviewStmt->execute()) {
    goBack('review_success');
}

goBack('review_error');
?>