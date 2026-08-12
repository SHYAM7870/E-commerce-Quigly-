<?php
include("../includes/db.php");

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    header("Location: ../Pages/product_list.php");
    exit;
}

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
$original_price_input = trim($_POST['original_price'] ?? '');
$discount_input = isset($_POST['discount']) ? (int)$_POST['discount'] : 0;
$category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
$subcategory_id = isset($_POST['subcategory_id']) ? (int)$_POST['subcategory_id'] : 0;
$featured = isset($_POST['featured']) ? (int)$_POST['featured'] : 0;
$trending = isset($_POST['trending']) ? (int)$_POST['trending'] : 0;
$stock_qty = isset($_POST['stock_qty']) ? max(0, (int)$_POST['stock_qty']) : 0;
$stock_status = isset($_POST['stock_status']) ? (int)$_POST['stock_status'] : 1;
$stock_status = $stock_status ? 1 : 0;

if ($name === '' || $price <= 0 || $category_id <= 0 || $subcategory_id <= 0) {
    header("Location: ../Pages/edit_product.php?id=$id");
    exit;
}

$original_price = $price;
if ($original_price_input !== '' && is_numeric($original_price_input)) {
    $original_price = (float)$original_price_input;
}

$discount = $discount_input;
if ($original_price > $price && $original_price > 0) {
    $discount = (int)round((($original_price - $price) / $original_price) * 100);
}

$get = mysqli_query($conn, "SELECT image FROM products WHERE id='$id'");
$data = mysqli_fetch_assoc($get);
$oldImage = $data['image'] ?? '';

$newImageName = null;
$hasNewImage = isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK && !empty($_FILES['image']['name']);

if ($hasNewImage) {
    $tmp = $_FILES['image']['tmp_name'];
    $size = (int)$_FILES['image']['size'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if ($size > 2 * 1024 * 1024) {
        header("Location: ../Pages/edit_product.php?id=$id");
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmp);
    finfo_close($finfo);

    $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($mime, $allowedMime, true) || !in_array($ext, $allowedExt, true)) {
        header("Location: ../Pages/edit_product.php?id=$id");
        exit;
    }

    $newImageName = time() . "_" . uniqid() . "." . $ext;
    if (!move_uploaded_file($tmp, "../../upload/" . $newImageName)) {
        header("Location: ../Pages/edit_product.php?id=$id");
        exit;
    }

    if (!empty($oldImage) && file_exists("../../upload/" . $oldImage)) {
        unlink("../../upload/" . $oldImage);
    }

    $query = "UPDATE products
              SET name=?,
                  description=?,
                  price=?,
                  original_price=?,
                  discount=?,
                  image=?,
                  category_id=?,
                  subcategory_id=?,
                  featured=?,
                  trending=?,
                  stock_qty=?,
                  stock_status=?
              WHERE id=?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "ssddisiiiiiii",
        $name,
        $description,
        $price,
        $original_price,
        $discount,
        $newImageName,
        $category_id,
        $subcategory_id,
        $featured,
        $trending,
        $stock_qty,
        $stock_status,
        $id
    );
} else {
    $query = "UPDATE products
              SET name=?,
                  description=?,
                  price=?,
                  original_price=?,
                  discount=?,
                  category_id=?,
                  subcategory_id=?,
                  featured=?,
                  trending=?,
                  stock_qty=?,
                  stock_status=?
              WHERE id=?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "ssddiiiiiiii",
        $name,
        $description,
        $price,
        $original_price,
        $discount,
        $category_id,
        $subcategory_id,
        $featured,
        $trending,
        $stock_qty,
        $stock_status,
        $id
    );
}

$stmt->execute();

header("Location: ../Pages/product_list.php");
exit;
?>