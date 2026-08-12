<?php
// admin/actions/get_product_variants.php
// Returns sizes, colors, variants, and gallery images for a product

header('Content-Type: application/json');
include_once __DIR__ . '/../includes/db.php';

$product_id = (int)($_GET['product_id'] ?? 0);
if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
    exit;
}

// AUTO-CREATE tables if missing
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

$conn->query("ALTER TABLE `product_colors` ADD COLUMN IF NOT EXISTS `color_hex` VARCHAR(20) DEFAULT NULL");

$conn->query("CREATE TABLE IF NOT EXISTS `product_variants` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `size_id` INT DEFAULT NULL,
  `color_id` INT DEFAULT NULL,
  `stock_qty` INT DEFAULT 0,
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS `product_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `image` VARCHAR(300) NOT NULL,
  `sort_order` TINYINT DEFAULT 0,
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Fetch sizes
$sizes = [];
$res = $conn->query("SELECT id, size_name FROM product_sizes WHERE product_id = $product_id ORDER BY id ASC");
if ($res) while ($r = $res->fetch_assoc()) $sizes[] = $r;

// Fetch colors (with hex)
$colors = [];
$res = $conn->query("SELECT id, color_name, COALESCE(color_hex,'') AS color_hex FROM product_colors WHERE product_id = $product_id ORDER BY id ASC");
if ($res) while ($r = $res->fetch_assoc()) $colors[] = $r;

// Fetch variants
$variants = [];
$res = $conn->query("SELECT id, size_id, color_id, stock_qty FROM product_variants WHERE product_id = $product_id ORDER BY id ASC");
if ($res) while ($r = $res->fetch_assoc()) $variants[] = $r;

// Fetch gallery images
$gallery = [];
$res = $conn->query("SELECT image, sort_order FROM product_images WHERE product_id = $product_id ORDER BY sort_order ASC, id ASC");
if ($res) while ($r = $res->fetch_assoc()) $gallery[] = $r;

echo json_encode([
    'status'   => 'ok',
    'sizes'    => $sizes,
    'colors'   => $colors,
    'variants' => $variants,
    'gallery'  => $gallery,
]);
?>
