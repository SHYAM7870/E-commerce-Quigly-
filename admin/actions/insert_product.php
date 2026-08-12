<?php
include '../../function.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Pages/add_product.php?msg=invalid_input");
    exit;
}

function redirectBack($msg, $count = 0) {
    $url = "../Pages/add_product.php?msg=" . urlencode($msg);
    if ($count > 0) $url .= "&count=" . (int)$count;
    header("Location: " . $url);
    exit;
}

function normalizeArray($value) {
    if (is_array($value)) return $value;
    if ($value === null || $value === '') return [];
    return [$value];
}

function uploadImage($fileArray, $index) {
    if (!isset($fileArray['name'][$index]) || $fileArray['name'][$index] === '') return [false, null];
    if (!isset($fileArray['error'][$index]) || $fileArray['error'][$index] !== UPLOAD_ERR_OK) return [false, null];
    $tmp  = $fileArray['tmp_name'][$index];
    $size = (int)($fileArray['size'][$index] ?? 0);
    $name = $fileArray['name'][$index];
    if ($size > 4 * 1024 * 1024) return [false, 'size_error'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $tmp);
    finfo_close($finfo);
    $allowedMime = ['image/jpeg','image/png','image/webp'];
    if (!in_array($mime, $allowedMime, true)) return [false, 'invalid_type'];
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) return [false, 'invalid_type'];
    $imageName  = time() . '_' . uniqid() . '.' . $ext;
    $uploadPath = '../../upload/' . $imageName;
    if (!move_uploaded_file($tmp, $uploadPath)) return [false, 'upload_failed'];
    return [true, $imageName];
}

// Auto-create product_images, product_sizes, product_colors, product_variants tables
$conn->query("CREATE TABLE IF NOT EXISTS `product_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `image` VARCHAR(300) NOT NULL,
  `sort_order` TINYINT DEFAULT 0,
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS `product_sizes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `size_name` VARCHAR(50) NOT NULL,
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS `product_colors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `color_name` VARCHAR(50) NOT NULL,
  `color_hex` VARCHAR(20) DEFAULT NULL,
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS `product_variants` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `size_id` INT DEFAULT NULL,
  `color_id` INT DEFAULT NULL,
  `stock_qty` INT DEFAULT 0,
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Add color_hex column if it doesn't exist yet
$conn->query("ALTER TABLE `product_colors` ADD COLUMN IF NOT EXISTS `color_hex` VARCHAR(20) DEFAULT NULL");

$names        = normalizeArray($_POST['name']          ?? []);
$descriptions = normalizeArray($_POST['description']   ?? []);
$prices       = normalizeArray($_POST['price']         ?? []);
$originalPrices = normalizeArray($_POST['original_price'] ?? []);
$discounts    = normalizeArray($_POST['discount']      ?? []);
$categoryIds  = normalizeArray($_POST['category_id']   ?? []);
$subcategoryIds = normalizeArray($_POST['subcategory_id'] ?? []);
$featured     = normalizeArray($_POST['featured']      ?? []);
$trending     = normalizeArray($_POST['trending']      ?? []);
$stockQty     = normalizeArray($_POST['stock_qty']     ?? []);
$stockStatus  = normalizeArray($_POST['stock_status']  ?? []);
$sizesJson    = normalizeArray($_POST['sizes_json']    ?? []);
$colorsJson   = normalizeArray($_POST['colors_json']   ?? []);

$total    = min(8, count($names));
$inserted = 0;

$stmt = $conn->prepare("
    INSERT INTO products
    (name, description, price, original_price, discount, image, category_id, subcategory_id, featured, trending, stock_qty, stock_status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
if (!$stmt) redirectBack('invalid_input');

$name = $description = $imageName = '';
$price = $originalPrice = 0.0;
$discount = 0;
$categoryId = $subcategoryId = 0;
$isFeatured = $isTrending = 0;
$qty = 0;
$status = 1;

$stmt->bind_param("ssddisiiiiii",
    $name, $description, $price, $originalPrice, $discount, $imageName,
    $categoryId, $subcategoryId, $isFeatured, $isTrending, $qty, $status);

for ($i = 0; $i < $total; $i++) {
    $name = trim($names[$i] ?? '');
    if ($name === '') continue;

    $price = isset($prices[$i]) ? (float)$prices[$i] : 0;
    if ($price <= 0) continue;

    $categoryId   = isset($categoryIds[$i]) ? (int)$categoryIds[$i] : 0;
    $subcategoryId = isset($subcategoryIds[$i]) ? (int)$subcategoryIds[$i] : 0;
    if ($categoryId <= 0 || $subcategoryId <= 0) continue;

    $checkCat = $conn->prepare("SELECT id FROM categories WHERE id = ?");
    $checkCat->bind_param("i", $categoryId);
    $checkCat->execute();
    if ($checkCat->get_result()->num_rows === 0) continue;

    $checkSub = $conn->prepare("SELECT id FROM subcategories WHERE id = ? AND category_id = ?");
    $checkSub->bind_param("ii", $subcategoryId, $categoryId);
    $checkSub->execute();
    if ($checkSub->get_result()->num_rows === 0) continue;

    $originalPrice = isset($originalPrices[$i]) && $originalPrices[$i] !== '' ? (float)$originalPrices[$i] : $price;
    $discount = 0;
    if ($originalPrice > $price && $originalPrice > 0)
        $discount = (int)round((($originalPrice - $price) / $originalPrice) * 100);

    $isFeatured = isset($featured[$i]) ? (int)$featured[$i] : 0;
    $isTrending = isset($trending[$i]) ? (int)$trending[$i] : 0;
    $qty        = isset($stockQty[$i]) ? max(0, (int)$stockQty[$i]) : 0;
    $status     = isset($stockStatus[$i]) ? ((int)$stockStatus[$i] ? 1 : 0) : 1;
    $description = trim($descriptions[$i] ?? '');

    // Upload image slot 1 (main image)
    $img1Files = $_FILES['images_1'] ?? [];
    $mainUpload = uploadImage($img1Files, $i);
    if ($mainUpload[0] !== true) continue; // main image required

    $imageName = $mainUpload[1];

    if (!$stmt->execute()) continue;
    $productId = (int)$conn->insert_id;
    $inserted++;

    // Save main image to product_images table (sort_order=0)
    $imgStmt = $conn->prepare("INSERT INTO product_images (product_id, image, sort_order) VALUES (?, ?, ?)");
    $so = 0;
    $imgStmt->bind_param("isi", $productId, $imageName, $so);
    $imgStmt->execute();

    // Upload extra image slots 2-4
    foreach ([2, 3, 4] as $slotN) {
        $extraFiles = $_FILES['images_' . $slotN] ?? [];
        $up = uploadImage($extraFiles, $i);
        if ($up[0] === true) {
            $imgStmt->bind_param("isi", $productId, $up[1], $slotN);
            $imgStmt->execute();
        }
    }
    $imgStmt->close();

    // Save sizes
    $sizesArr = json_decode($sizesJson[$i] ?? '[]', true) ?: [];
    if (!empty($sizesArr)) {
        $sizeStmt = $conn->prepare("INSERT INTO product_sizes (product_id, size_name) VALUES (?, ?)");
        foreach ($sizesArr as $sz) {
            $sz = trim($sz);
            if ($sz === '') continue;
            $sizeStmt->bind_param("is", $productId, $sz);
            $sizeStmt->execute();
        }
        $sizeStmt->close();
    }

    // Save colors
    $colorsArr = json_decode($colorsJson[$i] ?? '[]', true) ?: [];
    if (!empty($colorsArr)) {
        $colStmt = $conn->prepare("INSERT INTO product_colors (product_id, color_name, color_hex) VALUES (?, ?, ?)");
        foreach ($colorsArr as $cl) {
            $cn = trim($cl['name'] ?? '');
            $ch = trim($cl['hex'] ?? '');
            if ($cn === '') continue;
            $colStmt->bind_param("iss", $productId, $cn, $ch);
            $colStmt->execute();
        }
        $colStmt->close();
    }
}

if ($inserted === 0) redirectBack('no_valid_products');
redirectBack($inserted < $total ? 'partial' : 'success', $inserted);
?>
