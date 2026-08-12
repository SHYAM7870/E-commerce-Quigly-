<?php
include '../../function.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Pages/add_products_bulk.php?msg=invalid_request");
    exit;
}

$names = $_POST['name'] ?? [];
$descriptions = $_POST['description'] ?? [];
$prices = $_POST['price'] ?? [];
$original_prices = $_POST['original_price'] ?? [];
$discounts = $_POST['discount'] ?? [];
$category_ids = $_POST['category_id'] ?? [];
$subcategory_ids = $_POST['subcategory_id'] ?? [];
$featured = $_POST['featured'] ?? [];
$trending = $_POST['trending'] ?? [];
$stock_qty = $_POST['stock_qty'] ?? [];
$stock_status = $_POST['stock_status'] ?? [];

$totalInserted = 0;

for ($i = 0; $i < count($names); $i++) {
    $name = trim($names[$i] ?? '');
    if ($name === '') continue;

    $price = (float)($prices[$i] ?? 0);
    if ($price <= 0) continue;

    $cat_id = (int)($category_ids[$i] ?? 0);
    $sub_id = (int)($subcategory_ids[$i] ?? 0);

    $desc = trim($descriptions[$i] ?? '');
    $orig = (float)($original_prices[$i] ?? 0);
    $discount = (int)($discounts[$i] ?? 0);
    $isFeatured = isset($featured[$i]) ? 1 : 0;
    $isTrending = isset($trending[$i]) ? 1 : 0;
    $qty = (int)($stock_qty[$i] ?? 0);
    $status = isset($stock_status[$i]) ? 1 : 0;

    $imageName = '';
    if (isset($_FILES['image']['name'][$i]) && $_FILES['image']['error'][$i] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['image']['tmp_name'][$i];
        $ext = strtolower(pathinfo($_FILES['image']['name'][$i], PATHINFO_EXTENSION));
        $imageName = time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($tmp, "../../upload/" . $imageName);
    }

    $stmt = $conn->prepare("INSERT INTO products
        (name, description, price, original_price, discount, image, category_id, subcategory_id, featured, trending, stock_qty, stock_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "ssddisiiiiii",
        $name,
        $desc,
        $price,
        $orig,
        $discount,
        $imageName,
        $cat_id,
        $sub_id,
        $isFeatured,
        $isTrending,
        $qty,
        $status
    );

    if ($stmt->execute()) {
        $totalInserted++;
    }
}

header("Location: ../Pages/add_products_bulk.php?msg=success&count=" . $totalInserted);
exit;