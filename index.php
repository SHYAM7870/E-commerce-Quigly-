<?php
session_start();
include("admin/includes/db.php");

if (!isset($_SESSION['email'])) {
    header("Location: landing.php");
    exit;
}

$email = mysqli_real_escape_string($conn, $_SESSION['email']);
$sql = "SELECT * FROM quigly_table WHERE email='$email'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);
} else {
    session_destroy();
    header("Location: landing.php");
    exit;
}

$cat_filter = "";
$isFiltered = false;
$initialSection = 'home';

// Allow ?section= param to jump directly to a section after page reload
$allowedSections = ['home', 'products', 'orders', 'categories', 'cart', 'favorites', 'profile', 'deals', 'offers', 'support', 'return_refund'];
if (isset($_GET['section']) && in_array($_GET['section'], $allowedSections)) {
    $initialSection = $_GET['section'];
}

if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
    $cat_id = (int) $_GET['category_id'];
    $cat_filter = "WHERE p.category_id = $cat_id";
    $isFiltered = true;
    $initialSection = 'products';
}

// 🔥 FETCH SITE LOGO from homepage_media table
$logoRow = null;
$_logoResult = mysqli_query($conn, "SELECT image FROM homepage_media WHERE type='logo' AND status=1 ORDER BY id DESC LIMIT 1");
if ($_logoResult && mysqli_num_rows($_logoResult) > 0) {
    $logoRow = mysqli_fetch_assoc($_logoResult);
}

// 🔥 FETCH USER WISHLIST DIRECTLY IN PHP (Prevents Flash/Delay)
$userWishlistProducts = [];
$userWishlistIds = [];
$userIdVal = (int) ($data['id'] ?? 0);
if ($userIdVal > 0) {
    $wRes = mysqli_query($conn, "
        SELECT p.id, p.name, p.price, p.original_price, p.image, p.description, c.name AS category
        FROM wishlists w
        INNER JOIN products p ON p.id = w.product_id
        LEFT JOIN categories c ON c.id = p.category_id
        WHERE w.user_id = $userIdVal
        ORDER BY w.created_at DESC
    ");
    if ($wRes) {
        while ($wRow = mysqli_fetch_assoc($wRes)) {
            $wImg = trim($wRow['image'] ?? '');
            if ($wImg !== '' && !preg_match('#^(https?://|upload/)#i', $wImg)) {
                $wImg = 'upload/' . $wImg;
            }
            $wRow['image'] = $wImg ?: 'assets/images/no-image.png';
            $userWishlistProducts[] = $wRow;
            $userWishlistIds[] = (int) $wRow['id'];
        }
    }
}

// 🔥 FETCH ALL PRODUCTS (category filtering done client-side via JS)
$productQuery = "
SELECT p.*, c.name AS category 
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
ORDER BY p.id DESC
";

$productResult = mysqli_query($conn, $productQuery);

$products = [];

while ($row = mysqli_fetch_assoc($productResult)) {
    // 🔥 FIX IMAGE PATH
    $img = trim($row['image'] ?? '');

    if ($img === '') {
        $row['image'] = 'assets/images/no-image.png';
    } elseif (preg_match('#^(https?://|upload/)#i', $img)) {
        $row['image'] = $img;
    } else {
        $row['image'] = 'upload/' . $img;
    }

    // 🔥 MATCH YOUR JS STRUCTURE
    $row['originalPrice'] = $row['original_price'];

    // 🔥 CAST TO INT so JS truthy filtering (p.featured, p.trending) works correctly
    $row['featured'] = (int) ($row['featured'] ?? 0);
    $row['trending'] = (int) ($row['trending'] ?? 0);
    $row['stock_status'] = (int) ($row['stock_status'] ?? 0);
    $row['stock_qty'] = (int) ($row['stock_qty'] ?? 0);
    $row['is_wishlisted'] = in_array((int) $row['id'], $userWishlistIds);

    $products[] = $row;
}

// 🔥 FEATURED
$featuredProducts = array_values(array_filter($products, function ($p) {
    return isset($p['featured']) && $p['featured'] == 1;
}));

// 🔥 TRENDING
$trendingProducts = array_values(array_filter($products, function ($p) {
    return isset($p['trending']) && $p['trending'] == 1;
}));
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quigly — Premium Electronics & Fashion</title>
    <!-- Open Graph / Social sharing -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Quigly — Premium Electronics & Fashion">
    <meta property="og:description"
        content="Discover the latest cameras, laptops, phones, headphones, and trendy clothing at unbeatable prices. Fast delivery guaranteed.">
    <meta property="og:image" content="assets/images/photo-1556656793-08538906a9f8.jpg">
    <meta property="og:site_name" content="Quigly">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Quigly — Premium Electronics & Fashion">
    <meta name="twitter:description" content="Shop premium electronics & fashion with fast delivery.">
    <meta name="description"
        content="Quigly — Premium online store for electronics, fashion & accessories. Best prices, fast delivery, secure checkout.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&family=Space+Grotesk:wght@600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="assets/css/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        body {
            background: var(--bg-color);
            color: var(--text-color);
        }

        body.light-mode {
            --bg-color: #ffffff;
            --text-color: #111111;
            --card-bg: #ffffff;
            --bg-secondary: #f8f9fa;
            --border-color: #dee2e6;
        }

        body.dark-mode {
            --bg-color: #0f1115;
            --text-color: #f1f1f1;
            --card-bg: #1a1d23;
            --bg-secondary: #171a20;
            --border-color: #2a2f38;
        }

        .navbar {
            background: var(--bg-color) !important;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
            box-shadow: 0 6px 24px rgba(10, 10, 10, 1);
        }

        body.light-mode .navbar {
            background: #ffffff !important;
        }

        body.dark-mode .navbar {
            background: #041a46 !important;
        }

        body.light-mode .navbar .nav-link,
        body.light-mode .navbar .navbar-brand {
            color: #041a46 !important;
        }

        body.dark-mode .navbar .nav-link,
        body.dark-mode .navbar .navbar-brand {
            color: #f1f1f1 !important;
        }

        body.light-mode .navbar .dropdown-menu {
            background: #ffffff;
            border: 1px solid #dee2e6;
        }

        body.dark-mode .navbar .dropdown-menu {
            background: #082358;
            border: 1px solid #2a2f38;
        }

        body.light-mode .navbar .dropdown-item {
            color: #0b2461;
        }

        body.dark-mode .navbar .dropdown-item {
            color: #f1f1f1;
        }

        body.light-mode .navbar .dropdown-item:hover {
            background: #f1f3f5;
        }

        body.dark-mode .navbar .dropdown-item:hover {
            background: #0d2045;
        }

        .product-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(5, 5, 5, 0.12);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.28);
        }

        .product-img {
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
            opacity: 1 !important;
            visibility: visible !important;
            transition: transform 0.35s ease, opacity 0.2s ease;
        }

        /* Placeholder shimmer while image loads */
        .product-img::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: imgShimmer 1.2s infinite;
            z-index: 0;
            border-radius: 4px;
        }

        .product-img img.loaded {
            position: relative;
            z-index: 1;
        }

        @keyframes imgShimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        /* Dark mode shimmer */
        body.dark-mode .product-img,
        body:not(.light-mode) .product-img {
            background: #2a2a3a;
        }

        body.dark-mode .product-img::before,
        body:not(.light-mode) .product-img::before {
            background: linear-gradient(90deg, #2a2a3a 25%, #3a3a4a 50%, #2a2a3a 75%);
            background-size: 200% 100%;
        }

        .product-card:hover img {
            transform: scale(1.05);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            letter-spacing: -0.01em;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .navbar-brand,
        .section-title,
        .card-title,
        .hero-section h1,
        .hero-section .display-4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            letter-spacing: -0.02em
        }

        .navbar-brand,
        .section-title.gradient-text,
        .price-tag,
        .flash-sale,
        .membership-badge,
        .discount-badge,
        .offer-code,
        .countdown-timer,
        .btn-quigly,
        .btn-quigly-outline {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            letter-spacing: 0.02em
        }

        .product-card .card-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.1rem;
            line-height: 1.4
        }

        .product-card .card-text,
        .category-card .card-text,
        .card-text {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            line-height: 1.5
        }

        .hero-section h1.display-4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 3.2rem;
            line-height: 1.1;
            letter-spacing: -0.03em;
            text-shadow: 0 2px 10px rgba(0, 0, 0, .2)
        }

        .hero-section .lead {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            font-size: 1.2rem;
            line-height: 1.6
        }

        .nav-link {
            font-family: 'Inter', sans-serif;
            font-weight: 500
        }

        .nav-link.active {
            font-weight: 600
        }

        .price-tag {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1.4rem
        }

        .btn-quigly,
        .btn-quigly-outline {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            text-transform: none
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 2.5rem;
            line-height: 1.2
        }

        .category-card .card-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.2rem
        }

        .brand-logo {
            filter: grayscale(100%) contrast(1.2)
        }

        body.dark-mode .product-card .card-title,
        body.dark-mode .category-card .card-title,
        body.dark-mode .card-title,
        body.dark-mode .section-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700
        }

        .cart-badge,
        .favorites-badge {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700
        }

        .form-control,
        .form-select,
        .form-label {
            font-family: 'Inter', sans-serif
        }

        .toast-header {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600
        }

        .toast-body {
            font-family: 'Inter', sans-serif
        }

        #profileName {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.5rem
        }

        .user-stats-card h5,
        .membership-badge {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600
        }

        .feature-icon+h5 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.1rem
        }

        @media (max-width:768px) {
            .hero-section h1.display-4 {
                font-size: 2.5rem
            }

            .section-title {
                font-size: 2rem
            }

            .navbar-brand {
                font-family: 'Space Grotesk', sans-serif;
                font-weight: 700;
                font-size: 1.6rem
            }
        }

        @media (max-width:576px) {
            .hero-section h1.display-4 {
                font-size: 2rem
            }

            .section-title {
                font-size: 1.7rem
            }

            .price-tag {
                font-size: 1.2rem
            }
        }

        /* FAVORITES BOX (MAIN CARD) */
        .favorites-box {
            background: linear-gradient(145deg, #111827, #1f2937);
            border: 1px dashed rgba(255, 255, 255, 0.08);
            border-radius: 25px;
            padding: 80px 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            max-width: 100%;
        }

        /* ICON */
        .fav-icon {
            font-size: 60px;
            color: #9ca3af;
            opacity: 0.9;
        }

        /* LIGHT MODE SUPPORT */
        body.light-mode .favorites-box {
            background: #ffffff;
            border: 1px dashed #dee2e6;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        body.light-mode .fav-icon {
            color: #6c757d;
        }

        #pageLoader {

            position: fixed;

            top: 0;

            left: 0;

            width: 100vw;

            height: 100vh;

            background: rgba(8, 12, 25, 0.82);

            backdrop-filter: blur(10px);

            z-index: 999999;

            display: flex;

            align-items: center;

            justify-content: center;

            overflow: hidden;

            opacity: 0;

            visibility: hidden;

            transition:
                opacity 0.35s ease,
                visibility 0.35s ease;
        }

        #pageLoader.active {

            opacity: 1;

            visibility: visible;
        }

        .loader-wrapper {

            position: relative;

            width: 180px;

            height: 180px;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            text-align: center;
        }

        .loader-ring {

            position: absolute;

            width: 120px;

            height: 120px;

            border-radius: 50%;

            border: 5px solid rgba(255, 255, 255, 0.08);

            border-top: 5px solid #7c3aed;

            animation: spinLoader 1s linear infinite;
        }

        .loader-logo {

            width: 72px;

            height: 72px;

            border-radius: 50%;

            background: linear-gradient(135deg,
                    #7c3aed,
                    #2563eb);

            display: flex;

            align-items: center;

            justify-content: center;

            color: white;

            font-size: 28px;

            z-index: 2;

            box-shadow:
                0 0 35px rgba(124, 58, 237, 0.5);
        }

        .loader-wrapper h3 {

            margin-top: 18px;

            margin-bottom: 5px;

            color: white;

            font-size: 34px;

            font-weight: 700;
        }

        .loader-wrapper p {

            color: rgba(255, 255, 255, 0.7);
            padding-top: 150px;
            margin: 0;
            font-size: 15px;
        }

        @keyframes spinLoader {

            100% {

                transform: rotate(360deg);
            }
        }

        /* ===== PREMIUM PRODUCT DETAILS ===== */
        .product-detail-shell {
            max-width: 1100px;
            padding: 40px 20px 60px;
        }

        .detail-hero-card {
            background: var(--card-bg);
            border-radius: 28px;
            padding: 40px;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        }

        /* Image side */
        .premium-image-card {
            position: relative;
            border-radius: 22px;
            overflow: hidden;
            background: linear-gradient(135deg, #1a1d2e 0%, #0f1115 100%);
            aspect-ratio: 1 / 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(124, 58, 237, 0.25);
            box-shadow: 0 0 60px rgba(124, 58, 237, 0.12), inset 0 0 40px rgba(0, 0, 0, 0.2);
        }

        body.light-mode .premium-image-card {
            background: linear-gradient(135deg, #f0f0ff 0%, #e8ecff 100%);
        }

        .premium-image-card img {
            width: 85%;
            height: 85%;
            object-fit: contain;
            position: relative;
            z-index: 2;
            transition: transform 0.4s ease;
            filter: drop-shadow(0 12px 30px rgba(0, 0, 0, 0.4));
        }

        .premium-image-card:hover img {
            transform: scale(1.05);
        }

        .image-glow {
            position: absolute;
            width: 60%;
            height: 60%;
            background: radial-gradient(ellipse, rgba(124, 58, 237, 0.18) 0%, transparent 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
        }

        .detail-discount {
            position: absolute;
            top: 18px;
            left: 18px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 50px;
            z-index: 5;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4);
        }

        .image-floating-badge {
            position: absolute;
            bottom: 16px;
            right: 16px;
            background: rgba(124, 58, 237, 0.85);
            backdrop-filter: blur(8px);
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 50px;
            z-index: 5;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Info side */
        .detail-info-card {
            padding: 8px 0;
        }

        .top-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .detail-category {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #7c3aed;
            text-transform: uppercase;
            background: rgba(124, 58, 237, 0.1);
            padding: 5px 14px;
            border-radius: 50px;
            border: 1px solid rgba(124, 58, 237, 0.25);
        }

        .stock-live {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #10b981;
        }

        .stock-live i {
            font-size: 8px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: 0.3
            }
        }

        .detail-title {
            font-size: clamp(1.5rem, 3vw, 2.1rem);
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 14px;
            color: var(--text-color);
        }

        .detail-rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
        }

        .detail-rating .stars i {
            color: #f59e0b;
            font-size: 15px;
        }

        .detail-rating span {
            font-size: 13px;
            color: #9ca3af;
        }

        .detail-price-row {
            display: flex;
            align-items: baseline;
            gap: 14px;
            margin-bottom: 16px;
        }

        .detail-price {
            font-size: 2.2rem;
            font-weight: 900;
            background: linear-gradient(135deg, #7c3aed, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .detail-old-price {
            font-size: 1.1rem;
            color: #6b7280;
            text-decoration: line-through;
        }

        .detail-description {
            font-size: 14px;
            line-height: 1.75;
            color: #9ca3af;
            margin-bottom: 22px;
        }

        /* Delivery box */
        .detail-delivery-box {
            background: rgba(124, 58, 237, 0.06);
            border: 1px solid rgba(124, 58, 237, 0.18);
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 20px;
        }

        body.light-mode .detail-delivery-box {
            background: rgba(124, 58, 237, 0.04);
        }

        .delivery-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .delivery-label {
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .delivery-text {
            font-size: 15px;
            font-weight: 700;
            margin: 4px 0 0;
            color: var(--text-color);
        }

        .delivery-badge {
            background: linear-gradient(135deg, #7c3aed, #2563eb);
            color: #fff;
            border-radius: 14px;
            padding: 10px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            font-size: 11px;
            font-weight: 700;
        }

        .delivery-badge i {
            font-size: 18px;
            margin-bottom: 2px;
        }

        .delivery-bar {
            height: 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        body.light-mode .delivery-bar {
            background: rgba(0, 0, 0, 0.08);
        }

        .delivery-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #7c3aed, #2563eb);
            border-radius: 10px;
            width: 60%;
            transition: width 1s ease;
        }

        /* Highlight grid */
        .detail-highlight-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 22px;
        }

        .detail-highlight-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 14px 10px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .detail-highlight-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .detail-highlight-item i {
            font-size: 20px;
            color: #7c3aed;
        }

        .detail-highlight-item strong {
            font-size: 12px;
            color: var(--text-color);
        }

        .detail-highlight-item span {
            font-size: 11px;
            color: #9ca3af;
        }

        /* Action buttons */
        .detail-actions {
            display: flex;
            gap: 14px;
            margin-bottom: 20px;
        }

        .detail-cart-btn {
            flex: 1;
            padding: 14px 24px;
            background: rgba(124, 58, 237, 0.12);
            color: #7c3aed;
            border: 2px solid #7c3aed;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .detail-cart-btn:hover {
            background: #7c3aed;
            color: #fff;
            box-shadow: 0 8px 24px rgba(124, 58, 237, 0.35);
        }

        .detail-buy-btn {
            flex: 1;
            padding: 14px 24px;
            background: linear-gradient(135deg, #7c3aed, #2563eb);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.3);
        }

        .detail-buy-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(124, 58, 237, 0.45);
        }

        /* Meta strip */
        .detail-meta {
            display: flex;
            gap: 0;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 28px;
        }

        .detail-meta>div {
            flex: 1;
            padding: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            border-right: 1px solid var(--border-color);
            background: var(--bg-secondary);
        }

        .detail-meta>div:last-child {
            border-right: none;
        }

        .detail-meta span {
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .detail-meta strong {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-color);
        }

        /* Reviews box */
        .reviews-box {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 24px;
            margin-top: 4px;
        }

        .review-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .review-head h4 {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-color);
        }

        .review-head h4 i {
            color: #f59e0b;
        }

        .verified-review {
            font-size: 11px;
            font-weight: 600;
            color: #10b981;
            background: rgba(16, 185, 129, 0.1);
            padding: 5px 12px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 5px;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .review-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            background: var(--card-bg);
            border-radius: 14px;
            border: 1px solid var(--border-color);
            margin-bottom: 18px;
        }

        .review-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .review-score>span {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(135deg, #f59e0b, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }

        .review-score>div i {
            color: #f59e0b;
            font-size: 13px;
        }

        .review-total span {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-color);
            display: block;
        }

        .review-total small {
            font-size: 11px;
            color: #9ca3af;
        }

        .review-badge {
            font-size: 12px;
            font-weight: 600;
            color: #7c3aed;
            background: rgba(124, 58, 237, 0.1);
            padding: 8px 16px;
            border-radius: 50px;
            border: 1px solid rgba(124, 58, 237, 0.2);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .premium-review-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .review-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 18px 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .review-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .review-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .review-user {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-color);
        }

        .review-date {
            font-size: 11px;
            color: #9ca3af;
        }

        .review-rating {
            margin-bottom: 8px;
        }

        .review-rating i {
            color: #f59e0b;
            font-size: 13px;
        }

        .review-text {
            font-size: 13px;
            line-height: 1.65;
            color: #9ca3af;
            margin: 0;
        }

        .reviews-empty {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
            font-size: 14px;
            border: 2px dashed var(--border-color);
            border-radius: 14px;
        }

        /* review success toast extra type */
        .review-toast {
            background: linear-gradient(135deg, #7c3aed, #2563eb);
        }

        .review-toast .toast-icon {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>


<!-- Razorpay Checkout JS (Demo/Test Mode) -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

</head>
<script>
    setTimeout(() => {

        const toast =
            document.querySelector(
                '.premium-toast'
            );

        if (toast) {

            toast.style.opacity = '0';

            toast.style.transform =
                'translateX(50px)';

            setTimeout(() => {

                toast.remove();

            }, 400);
        }

    }, 3500);
</script>

<body class="dark-mode">
    <?php if (
        isset($_GET['success']) &&
        $_GET['success'] === 'order_placed'
    ) { ?>
        <div class="premium-toast success-toast">
            <div class="toast-icon">
                <i class="fa fa-check"></i>
            </div>
            <div>
                <div class="toast-title">
                    Order Successful
                </div>
                <div class="toast-message">
                    Your order has been placed successfully.
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'review_success') { ?>
        <div class="premium-toast review-toast" style="background:linear-gradient(135deg,#7c3aed,#2563eb);">
            <div class="toast-icon"
                style="background:rgba(255,255,255,0.2);border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-star" style="color:#f59e0b;"></i>
            </div>
            <div>
                <div class="toast-title">Review Submitted!</div>
                <div class="toast-message">Your review is pending admin approval.</div>
            </div>
        </div>
    <?php } ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'already_reviewed') { ?>
        <div class="premium-toast" style="background:linear-gradient(135deg,#f59e0b,#f97316);">
            <div class="toast-icon"
                style="background:rgba(255,255,255,0.2);border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-exclamation" style="color:#fff;"></i>
            </div>
            <div>
                <div class="toast-title">Already Reviewed</div>
                <div class="toast-message">You have already reviewed this product.</div>
            </div>
        </div>
    <?php } ?>
    <div id="pageLoader">

        <div class="loader-wrapper">

            <div class="loader-ring"></div>

            <div class="loader-logo">

                <i class="fas fa-bolt"></i>

            </div>

            <!-- <h3>Loading</h3> -->

            <p>Please wait...</p>

        </div>

    </div>
    <main id="mainContent">

        <?php include __DIR__ . '/admin/includes/navbar.php'; ?>

        <?php include __DIR__ . '/admin/includes/sections/home.php'; ?>
        <?php include __DIR__ . '/admin/includes/sections/products.php'; ?>

        <!-- Floating "Show All Products" button — visible when category filter is active -->
        <button id="showAllProductsBtn" onclick="showAllProducts()" style="display:none;position:fixed;bottom:24px;right:24px;z-index:1200;
                       height:48px;padding:0 20px;border-radius:999px;border:none;
                       background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;
                       font-size:13px;font-weight:800;gap:9px;align-items:center;
                       box-shadow:0 8px 28px rgba(124,58,237,.35);cursor:pointer;
                       transition:.25s;">
            <i class="fas fa-th"></i> Show All Products
        </button>

        <?php include __DIR__ . '/admin/includes/sections/categories.php'; ?>
        <?php include __DIR__ . '/admin/includes/sections/product_details.php'; ?>
        <?php include __DIR__ . '/admin/includes/sections/favorites.php'; ?>
        <?php include __DIR__ . '/admin/includes/sections/deals.php'; ?>
        <?php include __DIR__ . '/admin/includes/sections/cart.php'; ?>
        <?php include __DIR__ . '/admin/includes/sections/checkout.php'; ?>
        <?php include __DIR__ . '/admin/includes/sections/orders.php'; ?>
        <?php include __DIR__ . '/admin/includes/sections/return_refund.php'; ?>
        <?php include __DIR__ . '/admin/includes/sections/offers.php'; ?>
        <?php include __DIR__ . '/admin/includes/sections/profile.php'; ?>
        <?php include 'admin/includes/sections/support.php'; ?>

    </main>

    <?php include __DIR__ . '/admin/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let checkoutItems = [];
        const currentUserId = '<?= $data['id']; ?>';
        let cart = [];
        let products = <?= json_encode($products, JSON_UNESCAPED_SLASHES); ?>;
        let favorites = <?= json_encode($userWishlistProducts, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP); ?>;
        let isDarkMode = false;
        let countdownTimerId = null;

        // ========== HELPER: ESCAPE HTML ==========
        function escapeHtml(str) {
            return String(str || '').replace(/[&<>]/g, function (m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // ========== RATING HELPERS ==========
        function getRatingDetails(productId) {
            let hash = 0;
            for (let i = 0; i < String(productId).length; i++) {
                hash = ((hash << 5) - hash) + String(productId).charCodeAt(i);
                hash |= 0;
            }
            const ratingBase = 4.0 + (Math.abs(hash) % 10) / 10;
            const rating = Math.min(4.9, Math.max(4.0, ratingBase));
            const reviewCount = 80 + (Math.abs(hash) % 180);
            return {
                rating: rating.toFixed(1),
                reviews: reviewCount
            };
        }

        function getStarsHtml(rating) {
            const full = Math.floor(rating);
            const half = (rating - full) >= 0.5;
            let stars = '';
            for (let i = 0; i < full; i++) stars += '<i class="fas fa-star"></i>';
            if (half) stars += '<i class="fas fa-star-half-alt"></i>';
            const empty = 5 - full - (half ? 1 : 0);
            for (let i = 0; i < empty; i++) stars += '<i class="far fa-star"></i>';
            return stars;
        }

        // ========== MODERN PRODUCT CARD (pixel-perfect, like 3rd image) ==========
        function getProductCardHTML(product) {

            const discount = Number(product.discount || 0);

            const price = Number(product.price || 0);

            const orig = product.originalPrice || product.original_price;

            const originalPrice = orig ? Number(orig) : null;

            let finalOriginal =
                (originalPrice && originalPrice > price) ?
                    originalPrice :
                    null;

            if (!finalOriginal && discount > 0) {

                finalOriginal = price / (1 - discount / 100);
            }

            const discountPercent =
                discount > 0 ?
                    discount :
                    (
                        finalOriginal ?
                            Math.round(
                                ((finalOriginal - price) / finalOriginal) * 100
                            ) :
                            0
                    );

            const isFav =
                favorites.some(
                    f => String(f.id) === String(product.id)
                );

            const ratingInfo =
                getRatingDetails(product.id);

            const starsHtml =
                getStarsHtml(parseFloat(ratingInfo.rating));

            const categoryName =
                product.category || 'GENERAL';

            const isFeatured =
                parseInt(product.featured, 10) === 1;

            const isTrending =
                parseInt(product.trending, 10) === 1;

            return `

            <div class="product-card product-clickable"
                onclick="openProductPage('${product.id}')">

                ${discountPercent > 0
                    ? `
                    <div class="badge-discount">
                        ${discountPercent}% OFF
                    </div>
                    `
                    : ''
                }

                <button class="favorite-btn ${isFav ? 'active' : ''}"

                    onclick="event.stopPropagation(); toggleFavorite('${product.id}')"

                    data-id="${product.id}"
                    data-name="${escapeHtml(product.name)}"
                    data-price="${price}"
                    data-original-price="${finalOriginal || ''}"
                    data-image="${product.image}"
                    data-category="${categoryName}">

                    <i class="${isFav ? 'fa-solid' : 'fa-regular'} fa-heart"></i>

                </button>

                <div class="product-img">

                    <img src="${product.image}"
                        alt="${product.name}"
                        onload="this.classList.add('loaded')"
                        onerror="this.onerror=null;this.src='assets/images/no-image.png';this.classList.add('loaded')">

                </div>

                <div class="product-body">

                    <h5 class="product-title">
                        ${escapeHtml(product.name)}
                    </h5>

                    <div class="rating-stars">

                        <div class="stars">
                            ${starsHtml}
                        </div>

                        <span class="rating-value">
                            ${ratingInfo.rating}
                            (${ratingInfo.reviews})
                        </span>

                    </div>

                    <div class="price-row">

                        <span class="current-price">
                            ₹${price.toFixed(2)}
                        </span>

                        ${finalOriginal
                    ? `
                            <span class="old-price">
                                ₹${finalOriginal.toFixed(2)}
                            </span>
                            `
                    : ''
                }

                    </div>

                    <div class="tags-group">

                        <span class="category-tag">
                            ${escapeHtml(categoryName.toUpperCase())}
                        </span>

                        ${isFeatured
                    ? '<span class="featured-tag">Featured</span>'
                    : ''
                }

                        ${isTrending
                    ? '<span class="trending-tag">Trending</span>'
                    : ''
                }

                    </div>

                    <div class="product-actions">

                        <!-- ADD -->
                        <button class="product-action-btn add-cart-btn"

                            onclick="
                                event.stopPropagation();
                                addToCart('${product.id}')
                            ">

                            <i class="fas fa-cart-plus"></i>
                            Add

                        </button>

                        <!-- BUY -->
                        <button class="product-action-btn buy-now-btn"

                            onclick="
                                event.stopPropagation();
                                directBuy('${product.id}')
                            ">
                            Buy Now

                        </button>

                    </div>

                </div>

            </div>
        `;
        }

        // ========== RENDER FUNCTIONS (all use modern card) ==========
        function getSkeletonCardHTML() {
            return `
            <div class="product-card skeleton-card">
                <div class="skeleton-img skeleton-pulse"></div>
                <div class="product-body" style="gap:0.6rem;">
                    <div class="skeleton-line skeleton-pulse" style="width:80%;height:14px;border-radius:6px;"></div>
                    <div class="skeleton-line skeleton-pulse" style="width:55%;height:12px;border-radius:6px;"></div>
                    <div class="skeleton-line skeleton-pulse" style="width:45%;height:16px;border-radius:6px;margin-top:4px;"></div>
                    <div style="display:flex;gap:8px;margin-top:6px;">
                        <div class="skeleton-line skeleton-pulse" style="flex:1;height:36px;border-radius:10px;"></div>
                        <div class="skeleton-line skeleton-pulse" style="flex:1;height:36px;border-radius:10px;"></div>
                    </div>
                </div>
            </div>`;
        }

        function showSkeletons(containerId, count = 8) {
            const container = document.getElementById(containerId);
            if (!container) return;
            container.innerHTML = Array(count).fill('').map(() => getSkeletonCardHTML()).join('');
        }

        function renderProducts(productsArray, containerId) {
            const container = document.getElementById(containerId);
            if (!container) return;
            if (!Array.isArray(productsArray) || productsArray.length === 0) {
                container.innerHTML = '<div class="text-center py-5 w-100">No products found</div>';
                return;
            }
            container.innerHTML = '';
            productsArray.forEach(product => {
                container.innerHTML += getProductCardHTML(product);
            });
            syncFavoriteButtons();
        }

        function renderMoreProducts(productsArray) {
            const container = document.getElementById('productsContainer');
            if (!container) return;
            productsArray.forEach(product => {
                container.innerHTML += getProductCardHTML(product);
            });
            syncFavoriteButtons();
        }

        function renderTrendingProducts(productsArray) {
            const container = document.getElementById('trendingProducts');
            if (!container) return;
            container.innerHTML = '';
            productsArray.forEach(product => {
                container.innerHTML += getProductCardHTML(product);
            });
            syncFavoriteButtons();
        }

        function renderDealProducts(productsArray) {
            const container = document.getElementById('dealsProducts');
            if (!container) return;
            if (!productsArray.length) {
                container.innerHTML = '<div class="text-center py-5 w-100">No deals today</div>';
                return;
            }
            container.innerHTML = '';
            productsArray.forEach(product => {
                container.innerHTML += getProductCardHTML(product);
            });
            syncFavoriteButtons();
        }

        function renderFavorites() {
            const container = document.getElementById('favoritesContainer');
            if (!container) return;
            if (favorites.length === 0) {
                container.innerHTML = `
                <div class="favorites-box text-center" style="grid-column: 1/-1;">
                    <i class="fas fa-heart-broken fav-icon"></i>
                    <h4 class="mt-4 gradient-text fw-bold">No favorites yet</h4>
                    <p class="text-muted mb-4">Click the ❤️ on any product to save it here.</p>
                    <button class="btn btn-quigly px-4 py-2" onclick="showSection('products')">Browse Products</button>
                </div>
            `;
                return;
            }
            let html = '';
            favorites.forEach(product => {
                html += getProductCardHTML(product);
            });
            container.innerHTML = html;
            syncFavoriteButtons();
        }

        // ========== FAVORITES LOGIC ==========
        function loadFavorites() {
            fetch('admin/actions/wishlist_action.php?action=fetch')
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'ok') {
                        favorites = data.items.map(item => normalizeFavoriteObject(item)).filter(Boolean);
                    } else {
                        favorites = [];
                    }
                    updateFavoritesCount();
                    syncFavoriteButtons();
                    renderFavorites();
                    // Re-render category rows so heart icons reflect correct wishlist state
                    if (typeof renderCategoryRows === 'function') renderCategoryRows();
                })
                .catch(() => {
                    // Fallback to localStorage if fetch fails (e.g. offline)
                    const saved = localStorage.getItem('quigly_favorites_' + currentUserId);
                    if (saved) {
                        try {
                            const parsed = JSON.parse(saved);
                            favorites = Array.isArray(parsed) ?
                                parsed.map(item => normalizeFavoriteObject(item)).filter(Boolean) :
                                [];
                        } catch (e) {
                            favorites = [];
                        }
                    }
                    updateFavoritesCount();
                    syncFavoriteButtons();
                });
        }

        function normalizeFavoriteObject(product) {
            return {
                id: String(product.id || ''),
                name: product.name || '',
                price: Number(product.price || 0),
                originalPrice: (product.original_price ?? product.originalPrice) != null ?
                    Number(product.original_price ?? product.originalPrice) :
                    null,
                image: product.image || '',
                description: product.description || '',
                category: product.category || ''
            };
        }

        function saveFavorites() {
            // Keep localStorage as an offline backup
            localStorage.setItem('quigly_favorites_' + currentUserId, JSON.stringify(favorites));
        }

        function updateFavoritesCount() {
            const el = document.getElementById('favoritesCount');
            if (el) el.textContent = favorites.length;
        }

        function getFavoriteProductData(button) {
            return {
                id: String(button.dataset.id || ''),
                name: button.dataset.name || '',
                price: Number(button.dataset.price || 0),
                originalPrice: button.dataset.originalPrice ? Number(button.dataset.originalPrice) : null,
                image: button.dataset.image || '',
                description: button.dataset.description || '',
                category: button.dataset.category || ''
            };
        }

        function syncFavoriteButtons() {
            document.querySelectorAll('.favorite-btn').forEach(btn => {
                const productId = btn.getAttribute('data-id');
                const isFavorite = favorites.some(fav => String(fav.id) === String(productId));
                if (isFavorite) {
                    btn.classList.add('active');
                    btn.innerHTML = '<i class="fa-solid fa-heart"></i>';
                } else {
                    btn.classList.remove('active');
                    btn.innerHTML = '<i class="fa-regular fa-heart"></i>';
                }
            });
        }

        function toggleFavoriteCard(button) {
            const product = getFavoriteProductData(button);
            if (!product.id) return;
            const index = favorites.findIndex(item => String(item.id) === String(product.id));
            if (index === -1) {
                favorites.push(product);
                showToast('Saved for Later', `${product.name} added to favorites`, 'success');
            } else {
                const removed = favorites[index];
                favorites.splice(index, 1);
                showToast('Removed', `${removed.name || product.name} removed from favorites`, 'info');
            }
            saveFavorites();
            updateFavoritesCount();
            syncFavoriteButtons();
            if (document.getElementById('favorites') && document.getElementById('favorites').style.display === 'block') {
                renderFavorites();
            }
        }

        function toggleFavorite(productId) {
            const product = products.find(p => String(p.id) === String(productId));
            if (!product) return;

            const fd = new FormData();
            fd.append('action', 'toggle');
            fd.append('product_id', productId);

            fetch('admin/actions/wishlist_action.php', {
                method: 'POST',
                body: fd
            })
                .then(r => r.json())
                .then(data => {
                    if (data.status !== 'ok') {
                        showToast('Error', data.message || 'Could not update wishlist', 'error');
                        return;
                    }
                    if (data.wishlisted) {
                        const norm = normalizeFavoriteObject(product);
                        if (!favorites.some(f => String(f.id) === String(productId))) {
                            favorites.push(norm);
                        }
                        showToast('Saved ❤️', `${product.name} added to wishlist`, 'success');
                    } else {
                        favorites = favorites.filter(f => String(f.id) !== String(productId));
                        showToast('Removed', `${product.name} removed from wishlist`, 'info');
                    }
                    saveFavorites();
                    updateFavoritesCount();
                    syncFavoriteButtons();
                    // Update wishlist button on detail page if open
                    const wishBtn = document.getElementById('detailWishBtn');
                    if (wishBtn) {
                        wishBtn.dataset.productId = product.id;
                        const isWishlisted = favorites.some(f => String(f.id) === String(product.id));
                        setWishlistBtnState(wishBtn, isWishlisted);
                        wishBtn.onclick = () => toggleFavorite(product.id);
                    }
                    const savingsEl = document.getElementById('detailSavings');
                    if (savingsEl) {
                        const price = Number(product.price || 0);
                        const origP = Number(product.originalPrice || product.original_price || 0);
                        if (origP > price) {
                            const saved = origP - price;
                            savingsEl.textContent = `Save ₹${saved.toFixed(0)}`;
                            savingsEl.classList.remove('d-none');
                        } else {
                            savingsEl.classList.add('d-none');
                        }
                    }
                    const progressEl = document.getElementById('detailDeliveryProgress');
                    const deliveryTextEl = document.getElementById('detailDeliveryText');
                    const countdownEl = document.getElementById('detailDeliveryCountdown');
                    if (progressEl) {
                        const delivDays = Math.floor(Math.random() * 5) + 1;
                        const pct = Math.max(20, 100 - (delivDays * 14));
                        deliveryTextEl && (deliveryTextEl.textContent = delivDays <= 1 ? 'Today' : `${delivDays} Days`);
                        countdownEl && (countdownEl.textContent = delivDays <= 1 ? 'Today' : `${delivDays}d`);
                        setTimeout(() => {
                            progressEl.style.width = pct + '%';
                        }, 200);
                    }
                    if (document.getElementById('favorites') &&
                        document.getElementById('favorites').style.display === 'block') {
                        renderFavorites();
                    }
                })
                .catch(() => showToast('Error', 'Network error — try again', 'error'));
        }

        function setWishlistBtnState(btn, isWishlisted) {
            if (!btn) return;
            if (isWishlisted) {
                btn.classList.add('wishlisted');
                btn.title = 'Remove from Wishlist';
                btn.innerHTML = '<i class="fa-solid fa-heart"></i>';
            } else {
                btn.classList.remove('wishlisted');
                btn.title = 'Add to Wishlist';
                btn.innerHTML = '<i class="fa-regular fa-heart"></i>';
            }
        }

        function addToCart(productId, qty) {
            qty = Math.max(1, parseInt(qty, 10) || 1);
            const product = products.find(p => String(p.id) === String(productId));
            if (!product) return;

            const cartItem = cart.find(item => String(item.id) === String(productId));

            if (cartItem) {
                // Already in cart — just bump qty
                cartItem.quantity += qty;
                saveCart();
                updateCartCount();
                renderCart();
                showToast('Cart Updated', `${product.name} qty updated to ${cartItem.quantity}`, 'success');
                return;
            }

            cart.push({
                id: String(product.id),
                name: product.name,
                price: Number(product.price || 0),
                originalPrice: product.originalPrice || product.original_price || null,
                image: product.image,
                category: product.category || '',
                quantity: qty
            });

            saveCart();
            updateCartCount();
            renderCart();
            showToast('Added to Cart', `${product.name} ×${qty} added to cart!`, 'success');
        }

        function increaseQty(productId) {
            const item = cart.find(x => String(x.id) === String(productId));
            if (!item) return;
            item.quantity += 1;
            saveCart();
            updateCartCount();
            renderCart();
        }

        function decreaseQty(productId) {
            const index = cart.findIndex(x => String(x.id) === String(productId));
            if (index === -1) return;

            cart[index].quantity -= 1;

            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }

            saveCart();
            updateCartCount();
            renderCart();
        }

        function removeFromCart(productId) {
            cart = cart.filter(item => String(item.id) !== String(productId));
            saveCart();
            updateCartCount();
            renderCart();
        }

        function getCartCardHTML(item) {
            const price = Number(item.price || 0);
            const originalPrice = item.originalPrice ? Number(item.originalPrice) : null;
            const total = price * Number(item.quantity || 0);
            const categoryName = item.category || 'GENERAL';

            return `
            <div class="cart-item-card">
                <div class="cart-item-media">
                    <img src="${item.image}" alt="${escapeHtml(item.name)}">
                </div>

                <div class="cart-item-content">
                    <div class="cart-item-top">
                        <div>
                            <h5 class="cart-item-title">${escapeHtml(item.name)}</h5>
                            <span class="cart-item-category">${escapeHtml(categoryName.toUpperCase())}</span>
                        </div>

                        <button class="cart-remove-btn" onclick="removeFromCart('${item.id}')" title="Remove item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="cart-item-pricing">
                        <span class="cart-current-price">₹${price.toFixed(2)}</span>
                        ${originalPrice && originalPrice > price ? `<span class="cart-old-price">₹${originalPrice.toFixed(2)}</span>` : ''}
                    </div>

                    <div class="cart-item-bottom">
                        <div class="qty-control">
                            <button type="button" onclick="decreaseQty('${item.id}')">−</button>
                            <span>${item.quantity}</span>
                            <button type="button" onclick="increaseQty('${item.id}')">+</button>
                        </div>

                        <div class="cart-item-total">
                            ₹${total.toFixed(2)}
                        </div>
                    </div>
                </div>
            </div>
        `;
        }

        function updateOrderSummary() {
            const subtotal = cart.reduce((sum, item) => sum + (Number(item.price || 0) * Number(item.quantity || 0)), 0);
            const discount = 0;
            const shipping = subtotal > 0 ? 5 : 0;
            const tax = 0;
            const total = subtotal - discount + shipping + tax;

            const subtotalEl = document.getElementById('subtotalAmount');
            const discountEl = document.getElementById('discountAmount');
            const shippingEl = document.getElementById('shippingAmount');
            const taxEl = document.getElementById('taxAmount');
            const totalEl = document.getElementById('totalAmount');

            if (subtotalEl) subtotalEl.textContent = `₹${subtotal.toFixed(2)}`;
            if (discountEl) discountEl.textContent = `-₹${discount.toFixed(2)}`;
            if (shippingEl) shippingEl.textContent = `₹${shipping.toFixed(2)}`;
            if (taxEl) taxEl.textContent = `₹${tax.toFixed(2)}`;
            if (totalEl) totalEl.textContent = `₹${total.toFixed(2)}`;
        }

        function renderCart() {
            const container = document.getElementById('cartItems');
            const emptyMessage = document.getElementById('emptyCartMessage');

            if (!container) return;

            if (!cart.length) {
                container.innerHTML = '';
                if (emptyMessage) emptyMessage.style.display = 'block';
                updateOrderSummary();
                return;
            }

            if (emptyMessage) emptyMessage.style.display = 'none';
            container.innerHTML = cart.map(item => getCartCardHTML(item)).join('');
            updateOrderSummary();
        }

        function renderCheckout() {

            const container = document.getElementById('checkoutItems');

            if (!container) return;

            let subtotal = 0;

            container.innerHTML = '';

            checkoutItems.forEach(item => {

                const itemTotal = item.price * item.quantity;

                subtotal += itemTotal;

                container.innerHTML += `
                <div class="checkout-product">

                    <img src="${item.image}" class="checkout-product-img">

                    <div class="checkout-product-info">
                        <h6>${item.name}</h6>
                        <span>Qty: ${item.quantity}</span>
                    </div>

                    <div class="checkout-product-price">
                        ₹${itemTotal.toFixed(2)}
                    </div>

                </div>
            `;
            });

            document.getElementById('checkoutSubtotal').innerText =
                `₹${subtotal.toFixed(2)}`;

            document.getElementById('checkoutTotal').innerText =
                `₹${(subtotal + 5).toFixed(2)}`;
        }

        function placeOrder() {

            const name =
                document.getElementById('checkoutName')?.value.trim();

            const phone =
                document.getElementById('checkoutPhone')?.value.trim();

            const address =
                document.getElementById('checkoutAddress')?.value.trim();

            const payment =
                document.getElementById('checkoutPayment')?.value;

            if (!name || !phone || !address || !payment) {

                showToast(
                    'Error',
                    'All fields are required',
                    'error'
                );

                return;
            }

            if (checkoutItems.length === 0) {

                showToast(
                    'Error',
                    'No products selected',
                    'error'
                );

                return;
            }

            const total =
                checkoutItems.reduce(
                    (sum, item) =>
                        sum + (item.price * item.quantity),
                    0
                );

            // ── CARD payment → Launch Razorpay ──
            if (payment === 'CARD') {
                launchRazorpay(name, phone, address, total);
                return;
            }

            // ── COD / UPI → Direct order ──
            submitOrder(name, phone, address, payment, total, '');
        }

        // ── Razorpay Integration (Simulated for Demo Mode) ──
        function launchRazorpay(name, phone, address, total) {
            if (typeof openRzpMockModal === 'function') {
                openRzpMockModal(total);
            } else {
                console.error('openRzpMockModal is not defined.');
            }
        }

        // ── Submit order to backend ──
        function submitOrder(name, phone, address, payment, total, razorpayPaymentId) {
            const formData = new FormData();

            formData.append(
                'product_id',
                checkoutItems[0].id
            );

            formData.append(
                'total',
                total
            );

            formData.append(
                'name',
                name
            );

            formData.append(
                'phone',
                phone
            );

            formData.append(
                'address',
                address
            );

            formData.append(
                'payment_method',
                payment
            );

            if (razorpayPaymentId) {
                formData.append('razorpay_payment_id', razorpayPaymentId);
            }

            fetch(
                'admin/actions/place_order.php', {
                method: 'POST',
                body: formData
            }
            )

                .then(res => res.json())

                .then(data => {

                    if (data.status === 'success') {

                        showToast(
                            'Order Placed! 🎉',
                            'Your order has been placed successfully',
                            'success'
                        );

                        cart = [];
                        checkoutItems = [];
                        saveCart();
                        renderCart();

                        // Reload to orders section immediately — shows new order without extra navigation
                        setTimeout(() => {
                            window.location.href = 'index.php?section=orders';
                        }, 1200);

                    } else {

                        showToast(
                            'Error',
                            data.message || 'Order failed',
                            'error'
                        );
                    }
                })

                .catch(error => {

                    console.log(error);

                    showToast(
                        'Error',
                        'Server error',
                        'error'
                    );
                });
        }

        function updateCartCount() {
            const cartCount = cart.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
            const el = document.getElementById('cartCount');
            if (el) el.textContent = cartCount;
            // Sync floating cart badge + content
            syncFloatingCart();
        }

        /* ---- FLOATING CART ---- */
        let _fcOpen = false;

        function syncFloatingCart() {
            const cartCount = cart.reduce((sum, item) => sum + Number(item.quantity || 0), 0);

            // Badge
            const badge = document.getElementById('floatingCartBadge');
            if (badge) {
                badge.textContent = cartCount;
                badge.classList.remove('pop');
                void badge.offsetWidth;
                badge.classList.add('pop');
            }

            // Show/hide the whole widget (hide if cart is section active & on large screen)
            const widget = document.getElementById('floatingCart');
            if (widget) widget.style.display = cartCount > 0 ? 'flex' : 'none';

            // Drawer items
            const fcItems = document.getElementById('fcItems');
            const fcTotal = document.getElementById('fcTotal');
            if (!fcItems) return;

            if (!cart.length) {
                fcItems.innerHTML = '<div class="fc-empty"><i class="fas fa-shopping-bag"></i>Your cart is empty</div>';
                if (fcTotal) fcTotal.textContent = '₹0.00';
                return;
            }

            let total = 0;
            fcItems.innerHTML = cart.map(item => {
                const qty = Number(item.quantity || 1);
                const price = Number(item.price || 0);
                const lineTotal = qty * price;
                total += lineTotal;
                return `<div class="fc-item">
                    <img src="${item.image}" alt="${escapeHtml(item.name)}" onerror="this.src='assets/images/no-image.png'">
                    <div class="fc-item-info">
                        <div class="fc-item-name">${escapeHtml(item.name)}</div>
                        <div class="fc-item-price">₹${price.toFixed(2)} × ${qty}</div>
                    </div>
                    <span class="fc-item-qty">₹${lineTotal.toFixed(2)}</span>
                </div>`;
            }).join('');

            if (fcTotal) fcTotal.textContent = '₹' + total.toFixed(2);
        }

        function toggleFloatingCart(forceState) {
            const drawer = document.getElementById('floatingCartDrawer');
            if (!drawer) return;
            _fcOpen = forceState !== undefined ? forceState : !_fcOpen;
            drawer.classList.toggle('open', _fcOpen);
        }

        // Close drawer when clicking outside
        document.addEventListener('click', function (e) {
            if (_fcOpen) {
                const widget = document.getElementById('floatingCart');
                if (widget && !widget.contains(e.target)) {
                    toggleFloatingCart(false);
                }
            }
        });

        function saveCart() {
            // Sync to DB
            const payload = cart.map(item => ({
                product_id: parseInt(item.id, 10),
                quantity: Math.max(1, parseInt(item.quantity, 10) || 1)
            }));
            fetch('admin/actions/cart_action.php?action=sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    items: payload
                })
            }).catch(() => { }); // Silent — also save to localStorage as fallback
            localStorage.setItem('quigly_cart_' + currentUserId, JSON.stringify(cart));
        }

        function loadCart() {
            fetch('admin/actions/cart_action.php?action=fetch')
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'ok' && Array.isArray(data.items) && data.items.length > 0) {
                        cart = data.items.map(item => ({
                            id: String(item.id),
                            name: item.name || '',
                            price: Number(item.price || 0),
                            originalPrice: item.original_price ? Number(item.original_price) : null,
                            image: item.image || '',
                            category: item.category || '',
                            quantity: Math.max(1, parseInt(item.quantity, 10) || 1)
                        }));
                    } else {
                        // Fallback to localStorage
                        const savedCart = localStorage.getItem('quigly_cart_' + currentUserId);
                        if (savedCart) {
                            try {
                                cart = JSON.parse(savedCart);
                                if (!Array.isArray(cart)) cart = [];
                            } catch (e) {
                                cart = [];
                            }
                        } else {
                            cart = [];
                        }
                    }
                    updateCartCount();
                    renderCart();
                })
                .catch(() => {
                    const savedCart = localStorage.getItem('quigly_cart_' + currentUserId);
                    if (savedCart) {
                        try {
                            cart = JSON.parse(savedCart);
                            if (!Array.isArray(cart)) cart = [];
                        } catch (e) {
                            cart = [];
                        }
                    } else {
                        cart = [];
                    }
                    updateCartCount();
                    renderCart();
                });
        }
        // ========== UI / THEME / COUNTDOWN ==========
        function loadThemePreference() {
            const savedTheme = localStorage.getItem('theme');
            const themeIcon = document.getElementById('themeIcon');
            if (savedTheme === 'light') {
                document.body.classList.add('light-mode');
                document.body.classList.remove('dark-mode');
                if (themeIcon) themeIcon.className = 'fas fa-moon';
                isDarkMode = false;
            } else {
                document.body.classList.add('dark-mode');
                document.body.classList.remove('light-mode');
                if (themeIcon) themeIcon.className = 'fas fa-sun';
                isDarkMode = true;
            }
        }

        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('themeIcon');
            if (body.classList.contains('dark-mode')) {
                body.classList.remove('dark-mode');
                body.classList.add('light-mode');
                if (themeIcon) themeIcon.className = 'fas fa-moon';
                isDarkMode = false;
                showToast('Theme Changed', 'Switched to Light Mode', 'info');
            } else {
                body.classList.remove('light-mode');
                body.classList.add('dark-mode');
                if (themeIcon) themeIcon.className = 'fas fa-sun';
                isDarkMode = true;
                showToast('Theme Changed', 'Switched to Dark Mode', 'info');
            }
            localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
        }

        function startCountdown() {
            let countdownTime = 24 * 60 * 60;
            if (countdownTimerId) clearInterval(countdownTimerId);

            function updateCountdown() {
                const hours = Math.floor(countdownTime / 3600);
                const minutes = Math.floor((countdownTime % 3600) / 60);
                const seconds = countdownTime % 60;
                const formattedTime =
                    `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                const countdownEl = document.getElementById('countdown');
                const dealsCountdownEl = document.getElementById('dealsCountdown');
                if (countdownEl) countdownEl.textContent = formattedTime;
                if (dealsCountdownEl) dealsCountdownEl.textContent = formattedTime;
                countdownTime--;
                if (countdownTime < 0) countdownTime = 24 * 60 * 60;
            }
            updateCountdown();
            countdownTimerId = setInterval(updateCountdown, 1000);
        }

        function loadProducts() {
            showLoading(true);
            setTimeout(() => {
                renderProducts(products.filter(p => parseInt(p.featured, 10) === 1).slice(0, 8),
                    'featuredProducts');
                // Check if there's an initial category filter from URL
                <?php if ($isFiltered && !empty($cat_filter)):
                    preg_match('/category_id\s*=\s*(\d+)/', $cat_filter, $m);
                    $initCatId = !empty($m[1]) ? (int) $m[1] : 0;
                    ?>
                    currentCategoryFilter = <?= $initCatId ?>;
                    currentFilteredProducts = products.filter(p => String(p.category_id) === '<?= $initCatId ?>');
                <?php else: ?>
                    currentCategoryFilter = null;
                    currentFilteredProducts = products;
                <?php endif; ?>
                const initial = currentFilteredProducts.slice(0, PRODUCTS_PER_PAGE);
                renderProducts(initial, 'productsContainer');
                updateProductsCount(currentFilteredProducts.length, initial.length);
                updateLoadMoreBtn(currentFilteredProducts.length, initial.length);
                renderTrendingProducts(products.filter(p => parseInt(p.trending, 10) === 1).slice(0, 3));
                renderDealProducts(products.filter(p => (p.discount || 0) > 15));
                showLoading(false);
            }, 300);
        }

        function loadMoreProducts() {
            const container = document.getElementById('productsContainer');
            if (!container) return;
            const pool = currentFilteredProducts.length > 0 ? currentFilteredProducts : products;
            showLoading(true);
            setTimeout(() => {
                const currentCount = container.children.length;
                const moreProducts = pool.slice(currentCount, currentCount + PRODUCTS_PER_PAGE);
                if (moreProducts.length > 0) {
                    renderMoreProducts(moreProducts);
                    const newShown = currentCount + moreProducts.length;
                    updateProductsCount(pool.length, newShown);
                    updateLoadMoreBtn(pool.length, newShown);
                    showToast('Products Loaded', `Loaded ${moreProducts.length} more products`, 'success');
                } else {
                    showToast('No More Products', 'All products are loaded', 'info');
                    const wrapper = document.getElementById('loadMoreWrapper');
                    if (wrapper) wrapper.style.display = 'none';
                }
                showLoading(false);
            }, 400);
        }

        function filterProducts() {
            const searchInput = document.getElementById('searchProduct');
            if (!searchInput) return;
            const searchTerm = searchInput.value.toLowerCase();
            const pool = currentCategoryFilter ?
                products.filter(p => String(p.category_id) === String(currentCategoryFilter)) :
                products;
            const filtered = pool.filter(product => {
                const name = String(product.name || '').toLowerCase();
                const description = String(product.description || '').toLowerCase();
                return name.includes(searchTerm) || description.includes(searchTerm);
            });
            currentFilteredProducts = filtered;
            const initial = filtered.slice(0, PRODUCTS_PER_PAGE);
            renderProducts(initial, 'productsContainer');
            updateProductsCount(filtered.length, initial.length);
            updateLoadMoreBtn(filtered.length, initial.length);
            const noMsg = document.getElementById('noProductsMsg');
            if (noMsg) noMsg.style.display = filtered.length === 0 ? 'block' : 'none';
        }

        function sortProducts() {
            const sortEl = document.getElementById('sortProducts');
            const sortBy = sortEl ? sortEl.value : 'name';
            const pool = currentFilteredProducts.length > 0 ? [...currentFilteredProducts] : [...products];
            switch (sortBy) {
                case 'priceLow':
                    pool.sort((a, b) => Number(a.price || 0) - Number(b.price || 0));
                    break;
                case 'priceHigh':
                    pool.sort((a, b) => Number(b.price || 0) - Number(a.price || 0));
                    break;
                case 'discount':
                    pool.sort((a, b) => Number(b.discount || 0) - Number(a.discount || 0));
                    break;
                default:
                    pool.sort((a, b) => String(a.name || '').localeCompare(String(b.name || '')));
            }
            currentFilteredProducts = pool;
            const initial = pool.slice(0, PRODUCTS_PER_PAGE);
            renderProducts(initial, 'productsContainer');
            updateProductsCount(pool.length, initial.length);
            updateLoadMoreBtn(pool.length, initial.length);
        }

        function showPageLoader() {

            document
                .getElementById('pageLoader')
                .classList.add('active');
        }

        function hidePageLoader() {

            document
                .getElementById('pageLoader')
                .classList.remove('active');
        }

        function showSection(sectionId, event) {
            if (event && typeof event.preventDefault === 'function') {
                event.preventDefault();
            }

            const current = document.querySelector(
                '.content-section[style*="block"], .content-section:not([style*="none"])');

            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
                section.classList.remove('section-enter');
            });

            const activeSection = document.getElementById(sectionId);
            if (activeSection) {
                activeSection.style.display = 'block';
                // Trigger enter animation
                void activeSection.offsetWidth; // force reflow
                activeSection.classList.add('section-enter');
            }

            document.querySelectorAll('.nav-link')
                .forEach(link => {
                    link.classList.remove('active');
                });

            const activeLink = document.querySelector(`.nav-link[data-section="${sectionId}"]`);
            if (activeLink) activeLink.classList.add('active');

            // Save to sessionStorage so back/forward works (skip on order-redirect)
            if (sectionId !== 'product_details') {
                sessionStorage.setItem('activeSection', sectionId);
            }

            // Hide floating show-all btn when leaving products section
            const showAllBtn = document.getElementById('showAllProductsBtn');
            if (showAllBtn && sectionId !== 'products') {
                showAllBtn.style.display = 'none';
            } else if (showAllBtn && sectionId === 'products' && currentCategoryFilter) {
                showAllBtn.style.display = 'flex';
            }

            sessionStorage.setItem(
                'activeSection',
                sectionId
            );

            if (sectionId === 'favorites') {

                renderFavorites();

            }

            if (sectionId === 'cart') {
                renderCart();
            }

            // Hide floating cart btn when viewing cart or checkout (already there)
            const fcWidget = document.getElementById('floatingCart');
            if (fcWidget) {
                const hideOnSections = ['cart', 'checkout'];
                const cartCount = cart.reduce((s, i) => s + Number(i.quantity || 0), 0);
                fcWidget.style.display = (hideOnSections.includes(sectionId) || cartCount === 0) ? 'none' : 'flex';
            }

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function showLoading(show) {
            const loadingEl = document.getElementById('loading');
            if (loadingEl) loadingEl.classList.toggle('active', !!show);
        }

        function showToast(title, message, type) {
            // ── New Quigly toast system ──
            const container = document.getElementById('quigly-toast-container');
            if (!container) return;

            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                info: 'fa-info-circle',
                warning: 'fa-exclamation-triangle'
            };

            const toast = document.createElement('div');
            toast.className = `qtoast ${type || 'info'}`;
            toast.innerHTML = `
                <div class="qtoast-icon"><i class="fas ${icons[type] || icons.info}"></i></div>
                <div class="qtoast-body">
                    <div class="qtoast-title">${title}</div>
                    <div class="qtoast-msg">${message}</div>
                </div>
                <button class="qtoast-close" onclick="dismissToast(this.parentElement)">
                    <i class="fas fa-times"></i>
                </button>
                <div class="qtoast-bar"></div>
            `;

            container.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                requestAnimationFrame(() => toast.classList.add('show'));
            });

            // Animate bar shrink
            const bar = toast.querySelector('.qtoast-bar');
            if (bar) {
                bar.style.transition = 'transform 3s linear';
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        bar.style.transform = 'scaleX(0)';
                    });
                });
            }

            // Auto dismiss
            const timer = setTimeout(() => dismissToast(toast), 3200);
            toast.addEventListener('click', () => {
                clearTimeout(timer);
                dismissToast(toast);
            });

            // Keep max 4 toasts
            const all = container.querySelectorAll('.qtoast');
            if (all.length > 4) dismissToast(all[0]);
        }

        function dismissToast(toast) {
            if (!toast || !toast.parentElement) return;
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 380);
        }

        function updateCartDisplay() {
            const emptyMessage = document.getElementById('emptyCartMessage');
            const checkoutBtn = document.getElementById('checkoutBtn');
            if (cart.length === 0) {
                if (emptyMessage) emptyMessage.style.display = '';
                if (checkoutBtn) checkoutBtn.disabled = true;
            } else {
                if (emptyMessage) emptyMessage.style.display = 'none';
                if (checkoutBtn) checkoutBtn.disabled = false;
            }
        }

        // Track current category filter
        let currentCategoryFilter = null;
        let currentFilteredProducts = [];
        const PRODUCTS_PER_PAGE = 20;

        function showCategoryProducts(catId, catData) {
            // Show bottom loading bar (it's at body level, always visible)
            const bar = document.getElementById('categoryLoadingBar');
            const txt = document.getElementById('categoryLoadingText');
            if (bar) {
                const catName = getCategoryNameById(catId);
                if (txt) txt.textContent = catName ? 'Loading ' + catName + ' products...' : 'Loading products...';
                bar.style.display = 'flex';
            }

            // Navigate to products section
            showSection('products');
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            // Show skeleton cards immediately
            showSkeletons('productsContainer', 8);

            // Filter and render after short delay (lets the loader show)
            setTimeout(() => {
                currentCategoryFilter = catId;
                const filtered = products.filter(p => String(p.category_id) === String(catId));
                currentFilteredProducts = filtered;
                const initial = filtered.slice(0, PRODUCTS_PER_PAGE);
                renderProducts(initial, 'productsContainer');
                updateProductsCount(filtered.length, initial.length);
                updateLoadMoreBtn(filtered.length, initial.length);
                updateShowAllBtn(catId);

                // Hide loading bar with slide-down
                if (bar) {
                    bar.style.transition = 'transform .3s ease';
                    bar.style.transform = 'translateY(100%)';
                    setTimeout(() => {
                        bar.style.display = 'none';
                        bar.style.transform = '';
                        bar.style.transition = '';
                    }, 300);
                }
            }, 600);
        }

        function getCategoryNameById(catId) {
            const p = products.find(p => String(p.category_id) === String(catId));
            return p ? (p.category || p.category_name || '') : '';
        }

        function showAllProducts() {
            currentCategoryFilter = null;
            currentFilteredProducts = products;
            const initial = products.slice(0, PRODUCTS_PER_PAGE);
            renderProducts(initial, 'productsContainer');
            updateProductsCount(products.length, initial.length);
            updateLoadMoreBtn(products.length, initial.length);
            updateShowAllBtn(null);
            showToast('All Products', 'Showing all ' + products.length + ' products', 'info');
        }

        function updateShowAllBtn(activeCat) {
            let btn = document.getElementById('showAllProductsBtn');
            if (!btn) return;
            if (activeCat) {
                btn.innerHTML = `<i class="fas fa-th"></i> Show All Products`;
                btn.style.display = 'flex';
            } else {
                btn.style.display = 'none';
            }
        }

        function updateProductsCount(total, shown) {
            const el = document.getElementById('productsCount');
            if (!el) return;
            if (shown < total) {
                el.textContent = `Showing ${shown} of ${total} items`;
            } else {
                el.textContent = `${total} item${total !== 1 ? 's' : ''}`;
            }
        }

        function updateLoadMoreBtn(total, shown) {
            const wrapper = document.getElementById('loadMoreWrapper');
            const btn = document.getElementById('loadMoreProducts');
            const countEl = document.getElementById('loadMoreCount');
            if (!wrapper) return;
            const remaining = total - shown;
            if (remaining > 0) {
                wrapper.style.display = 'flex';
                if (btn) btn.disabled = false;
                if (countEl) countEl.textContent = '+' + Math.min(remaining, PRODUCTS_PER_PAGE) + ' more';
            } else {
                wrapper.style.display = 'none';
            }
        }

        function showLoginModal() {
            showToast('Login', 'Login feature would open here', 'info');
        }

        function showRegisterModal() {
            showToast('Register', 'Registration feature would open here', 'info');
        }

        function logout() {
            window.location.href = 'logout.php';
        }

        function checkout() {
            // FIX: was a stub — now properly populates checkoutItems from cart and navigates to checkout
            if (!cart || cart.length === 0) {
                showToast('Cart Empty', 'Please add items to your cart first', 'warning');
                return;
            }
            checkoutItems = cart.map(item => ({
                ...item
            }));
            renderCheckout();
            showSection('checkout');
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function applyCoupon() {
            showToast('Coupon', 'Coupon would be applied here', 'info');
        }

        function openProductDetails(card, event) {
            if (event && event.target.closest('.favorite-btn, .add-cart-btn, .cart-btn')) return;

            const productId = card.dataset.id;
            const product = products.find(p => String(p.id) === String(productId));
            if (!product) return;

            openProductPage(productId);
            showSection('product_details')
            window.scrollTo(0, 0);
        }

        function directBuy(productId, qty) {
            qty = Math.max(1, parseInt(qty, 10) || 1);
            const product = products.find(p => String(p.id) === String(productId));
            if (!product) {
                showToast('Error', 'Product not found', 'error');
                return;
            }

            checkoutItems = [{
                id: String(product.id),
                name: product.name,
                price: Number(product.price || 0),
                originalPrice: product.originalPrice || product.original_price || null,
                image: product.image,
                category: product.category || '',
                quantity: qty
            }];

            renderCheckout();
            showSection('checkout');
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            showToast('Buy Now', `Proceeding to checkout for ${qty} item${qty > 1 ? 's' : ''}`, 'success');
        }

        function openProductPage(productId) {
            const product = products.find(p => String(p.id) === String(productId));
            if (!product) return;

            // Reset quantity to 1 every time a new product is opened
            window._detailQty = 1;
            const qtyEl = document.getElementById('detailQtyValue');
            const qtyTotal = document.getElementById('detailQtyTotal');
            if (qtyEl) qtyEl.textContent = '1';
            if (qtyTotal) {
                const price = Number(product.price || 0);
                qtyTotal.textContent = `= ₹${price.toFixed(2)}`;
            }

            const ratingInfo = getRatingDetails(product.id);

            const detailImage = document.getElementById('detailImage');
            const imagePath = String(product.image || '').trim();

            if (detailImage) {
                detailImage.style.opacity = '1';
                detailImage.style.visibility = 'visible';
                // Force fresh load: clear src first, then set (fixes hidden section lazy load issue)
                detailImage.src = '';
                detailImage.onerror = function () {
                    this.src = 'assets/images/no-image.png';
                };
                // Use setTimeout to ensure section is visible before setting src
                setTimeout(() => {
                    detailImage.src = imagePath ? imagePath : 'assets/images/no-image.png';
                }, 10);
            }

            const titleEl = document.getElementById('detailTitle');
            const categoryEl = document.getElementById('detailCategory');
            const starsEl = document.getElementById('detailStars');
            const ratingTextEl = document.getElementById('detailRatingText');
            const priceEl = document.getElementById('detailPrice');
            const oldPriceEl = document.getElementById('detailOldPrice');
            const descEl = document.getElementById('detailDescription');
            const deliveryEl = document.getElementById('deliveryTime');
            const savingsEl = document.getElementById('detailSavings');
            const addBtn = document.getElementById('detailAddBtn');
            const buyBtn = document.getElementById('detailBuyBtn');

            if (titleEl) titleEl.innerText = product.name || '';
            if (categoryEl) categoryEl.innerText = product.category || 'CATEGORY';
            if (starsEl) starsEl.innerHTML = getStarsHtml(parseFloat(ratingInfo.rating));
            if (ratingTextEl) ratingTextEl.innerText = `${ratingInfo.rating} (${ratingInfo.reviews} reviews)`;
            if (priceEl) priceEl.innerText = `₹${Number(product.price || 0).toFixed(2)}`;
            if (oldPriceEl) oldPriceEl.innerText = product.originalPrice ? `₹${Number(product.originalPrice).toFixed(2)}` :
                '';
            if (descEl) descEl.innerText = product.description || 'Premium quality product';

            // Savings badge
            const price = Number(product.price || 0);
            const origP = Number(product.originalPrice || product.original_price || 0);
            if (savingsEl) {
                if (origP > price) {
                    savingsEl.textContent = `Save ₹${(origP - price).toFixed(0)}`;
                    savingsEl.classList.remove('d-none');
                } else {
                    savingsEl.classList.add('d-none');
                }
            }

            const deliveryDate = new Date();
            deliveryDate.setDate(deliveryDate.getDate() + Math.floor(Math.random() * 7));
            const diff = Math.ceil((deliveryDate - new Date()) / (1000 * 60 * 60 * 24));
            if (deliveryEl) deliveryEl.innerHTML = diff <= 1 ? 'Today Delivery' : diff + ' days delivery';

            // Wire buttons using current qty
            if (addBtn) addBtn.onclick = () => {
                const q = parseInt(document.getElementById('detailQtyValue')?.textContent || '1', 10);
                addToCart(product.id, q);
            };
            if (buyBtn) buyBtn.onclick = () => {
                const q = parseInt(document.getElementById('detailQtyValue')?.textContent || '1', 10);
                directBuy(product.id, q);
            };

            // ── STOCK STATUS (dynamic) ────────────────────────────────
            const inStock = (parseInt(product.stock_status, 10) === 1) && (parseInt(product.stock_qty, 10) > 0);
            const stockBadge = document.getElementById('detailStockBadge');
            const stockIcon = document.getElementById('detailStockIcon');
            const stockText = document.getElementById('detailStockText');
            const stockBtn = document.getElementById('detailStockBtn');
            const availEl = document.getElementById('detailAvailability');

            if (stockIcon) {
                stockIcon.style.color = inStock ? '#22c55e' : '#ef4444';
            }
            if (stockText) {
                stockText.textContent = inStock ? 'In Stock' : 'Out of Stock';
            }
            if (stockBtn) {
                stockBtn.classList.toggle('d-none', inStock);
            }
            if (addBtn) {
                addBtn.disabled = !inStock;
                addBtn.style.opacity = inStock ? '1' : '0.5';
                addBtn.style.cursor = inStock ? 'pointer' : 'not-allowed';
            }
            if (buyBtn) {
                buyBtn.disabled = !inStock;
                buyBtn.style.opacity = inStock ? '1' : '0.5';
                buyBtn.style.cursor = inStock ? 'pointer' : 'not-allowed';
            }
            if (availEl) {
                availEl.textContent = inStock ? 'In Stock' : 'Out of Stock';
                availEl.style.color = inStock ? '#22c55e' : '#ef4444';
            }

            // Wishlist button
            const wishBtn = document.getElementById('detailWishBtn');
            if (wishBtn) {
                const isWishlisted = favorites.some(f => String(f.id) === String(product.id));
                wishBtn.dataset.productId = product.id;
                setWishlistBtnState(wishBtn, isWishlisted);
                wishBtn.onclick = () => toggleFavorite(product.id);
            }

            // Store product price for qty total updates
            window._detailProductPrice = price;

            loadProductReviews(productId);
            loadProductGalleryAndVariants(productId, product);
            renderRelatedProducts(product);
            showSection('product_details');
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        /* ════════════════════════════════════════════
           RELATED PRODUCTS
        ════════════════════════════════════════════ */
        function renderRelatedProducts(product) {
            const grid = document.getElementById('relatedProductsGrid');
            const emptyEl = document.getElementById('relatedProductsEmpty');
            const catNameEl = document.getElementById('relatedCategoryName');
            if (!grid) return;

            const category = (product.category || '').trim();
            if (catNameEl) catNameEl.textContent = category || 'this Category';

            // Only same-category matches (excluding the current product) — real e-commerce style
            let related = products.filter(p =>
                String(p.id) !== String(product.id) &&
                (p.category || '').trim().toLowerCase() === category.toLowerCase()
            );

            // Shuffle so it doesn't feel static, then cap the count
            related = related.sort(() => Math.random() - 0.5).slice(0, 8);

            if (related.length === 0) {
                grid.innerHTML = '';
                grid.classList.add('d-none');
                if (emptyEl) emptyEl.classList.remove('d-none');
                return;
            }

            grid.classList.remove('d-none');
            if (emptyEl) emptyEl.classList.add('d-none');
            grid.innerHTML = related.map(p => getProductCardHTML(p)).join('');
            syncFavoriteButtons();
        }

        // Called by + / − buttons on product detail page
        function changeDetailQty(delta) {
            const qtyEl = document.getElementById('detailQtyValue');
            const totalEl = document.getElementById('detailQtyTotal');
            if (!qtyEl) return;
            let qty = Math.max(1, (parseInt(qtyEl.textContent, 10) || 1) + delta);
            if (qty > 99) qty = 99;
            qtyEl.textContent = qty;
            const price = Number(window._detailProductPrice || 0);
            if (totalEl) totalEl.textContent = price > 0 ? `= ₹${(price * qty).toFixed(2)}` : '';
        }

        /* ════════════════════════════════════════════
           GALLERY + VARIANTS
        ════════════════════════════════════════════ */
        function loadProductGalleryAndVariants(productId, product) {
            // Reset variant state
            document.getElementById('selectedVariantId').value = '';
            document.getElementById('selectedSizeId').value = '';
            document.getElementById('selectedColorId').value = '';

            fetch('admin/actions/get_product_variants.php?product_id=' + encodeURIComponent(productId))
                .then(r => r.json())
                .then(data => {
                    if (data.status !== 'ok') return;
                    renderGallery(product, data.gallery || []);
                    renderVariants(data.sizes || [], data.colors || [], data.variants || []);
                })
                .catch(() => {
                    renderGallery(product, []);
                    renderVariants([], [], []);
                });
        }

        function renderGallery(product, gallery) {
            const mainImg = document.getElementById('detailImage');
            const thumbStrip = document.getElementById('thumbStrip');
            if (!mainImg || !thumbStrip) return;

            // Build images array: gallery first, fallback to product.image
            let images = gallery.map(g => 'upload/' + g.image);
            if (images.length === 0 && product.image) {
                // If the product image is relative path without upload/, append upload/
                const isRelative = !product.image.startsWith('upload/') && !product.image.startsWith('http');
                const srcPath = isRelative ? 'upload/' + product.image : product.image;
                images = [srcPath];
            }

            // For demo purposes: if we only have 1 image, populate some dummy copies to demonstrate the vertical list look
            if (images.length === 1) {
                images = [
                    images[0],
                    images[0],
                    images[0],
                    images[0],
                    images[0],
                    images[0],
                    images[0]
                ];
            }

            thumbStrip.innerHTML = '';
            thumbStrip.className = 'thumb-strip-vertical';
            thumbStrip.style.display = 'flex';

            const maxVisible = 6;
            const hasMore = images.length > maxVisible;

            images.forEach((src, idx) => {
                if (idx >= maxVisible) return;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'thumb-btn-vertical' + (idx === 0 ? ' active' : '');
                btn.dataset.src = src;

                if (idx === maxVisible - 1 && hasMore) {
                    btn.classList.add('more-indicator');
                    btn.setAttribute('data-more', `5+`);
                }

                btn.innerHTML =
                    `<img src="${src}" alt="View ${idx + 1}" onerror="this.src='assets/images/no-image.png'">`;
                btn.addEventListener('click', () => switchMainImage(src, btn));
                thumbStrip.appendChild(btn);
            });

            // Reset main image source to first image
            if (images.length > 0) {
                mainImg.src = images[0];
            }
        }

        function switchMainImage(src, clickedBtn) {
            const mainImg = document.getElementById('detailImage');
            if (!mainImg) return;
            mainImg.classList.add('switching');
            setTimeout(() => {
                mainImg.src = src;
                mainImg.classList.remove('switching');
            }, 220);
            
            document.querySelectorAll('.thumb-btn-vertical').forEach(b => b.classList.remove('active'));
            if (clickedBtn) clickedBtn.classList.add('active');
        }

        // ── Share & Full View Helpers ──
        window.shareProduct = function() {
            const title = document.getElementById('detailTitle')?.innerText || 'Product details';
            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: window.location.href
                }).catch(err => console.log('Share failed:', err));
            } else {
                navigator.clipboard.writeText(window.location.href);
                showToast('Link Copied! 🔗', 'Product link copied to your clipboard.', 'success');
            }
        };

        window.openFullViewImage = function(e) {
            if (e) e.preventDefault();
            const mainImg = document.getElementById('detailImage');
            if (mainImg && mainImg.src) {
                window.open(mainImg.src, '_blank');
            }
        };

        function renderVariants(sizes, colors, variants) {
            const variantSection = document.getElementById('variantSection');
            const sizeGroup = document.getElementById('sizeGroup');
            const colorGroup = document.getElementById('colorGroup');
            const sizeContainer = document.getElementById('sizeContainer');
            const colorContainer = document.getElementById('colorContainer');
            const variantStockEl = document.getElementById('variantStock');
            if (!variantSection) return;

            const hasSizes = sizes.length > 0;
            const hasColors = colors.length > 0;

            if (!hasSizes && !hasColors) {
                variantSection.style.display = 'none';
                return;
            }

            variantSection.style.display = 'block';
            sizeGroup.style.display = hasSizes ? 'block' : 'none';
            colorGroup.style.display = hasColors ? 'block' : 'none';

            // Build size buttons
            sizeContainer.innerHTML = '';
            sizes.forEach(s => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'variant-option-btn';
                btn.textContent = s.size_name;
                btn.dataset.sizeId = s.id;
                btn.addEventListener('click', () => {
                    document.querySelectorAll('#sizeContainer .variant-option-btn').forEach(b => b.classList
                        .remove('active'));
                    btn.classList.add('active');
                    document.getElementById('selectedSizeId').value = s.id;
                    const lbl = document.getElementById('selectedSizeLabel');
                    if (lbl) lbl.textContent = '— ' + s.size_name;
                    updateVariantStock(sizes, colors, variants);
                });
                sizeContainer.appendChild(btn);
            });

            // Build color swatches
            colorContainer.innerHTML = '';
            colors.forEach(c => {
                const wrap = document.createElement('div');
                wrap.className = 'color-swatch-wrap';

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'variant-option-btn color-option-btn';
                btn.title = c.color_name;
                btn.dataset.colorId = c.id;
                const hex = c.color_hex || '#888888';
                btn.style.background = hex;

                const label = document.createElement('span');
                label.className = 'color-swatch-label';
                label.textContent = c.color_name;

                btn.addEventListener('click', () => {
                    document.querySelectorAll('#colorContainer .color-option-btn').forEach(b => b.classList
                        .remove('active'));
                    btn.classList.add('active');
                    document.getElementById('selectedColorId').value = c.id;
                    const lbl = document.getElementById('selectedColorLabel');
                    if (lbl) lbl.textContent = '— ' + c.color_name;
                    updateVariantStock(sizes, colors, variants);
                });

                wrap.appendChild(btn);
                wrap.appendChild(label);
                colorContainer.appendChild(wrap);
            });

            // Reset labels
            const sl = document.getElementById('selectedSizeLabel');
            const cl = document.getElementById('selectedColorLabel');
            if (sl) sl.textContent = '';
            if (cl) cl.textContent = '';
            if (variantStockEl) variantStockEl.textContent = '';

            window._variantData = {
                sizes,
                colors,
                variants
            };
        }

        function updateVariantStock(sizes, colors, variants) {
            const sizeId = document.getElementById('selectedSizeId')?.value;
            const colorId = document.getElementById('selectedColorId')?.value;
            const stockEl = document.getElementById('variantStock');

            const match = variants.find(v =>
                (sizeId ? String(v.size_id) === String(sizeId) : !v.size_id || sizes.length === 0) &&
                (colorId ? String(v.color_id) === String(colorId) : !v.color_id || colors.length === 0)
            );

            if (stockEl) {
                if (match) {
                    const qty = parseInt(match.stock_qty, 10);
                    if (qty > 0) {
                        stockEl.innerHTML =
                            `<span style="color:#059669;"><i class="fas fa-check-circle me-1"></i>${qty} in stock</span>`;
                    } else {
                        stockEl.innerHTML =
                            `<span style="color:#ef4444;"><i class="fas fa-times-circle me-1"></i>Out of stock for this option</span>`;
                    }
                    document.getElementById('selectedVariantId').value = match.id;
                } else if (sizeId || colorId) {
                    stockEl.textContent = '';
                }
            }
        }

        /* ════════════════════════════════════════════
           REVIEWS
        ════════════════════════════════════════════ */
        function loadProductReviews(productId) {
            const listEl = document.getElementById('productReviewsList');
            const avgRatingEl = document.getElementById('reviewAvgRating');
            const avgStarsEl = document.getElementById('reviewAvgStars');
            const reviewCountEl = document.getElementById('reviewCountText');
            const toggleBtn = document.getElementById('reviewToggleBtn');
            if (!listEl) return;
            listEl.innerHTML =
                '<div style="text-align:center;padding:20px;color:#9ca3af;font-size:13px;"><i class="fas fa-spinner fa-spin"></i> Loading reviews...</div>';
            if (toggleBtn) {
                toggleBtn.classList.add('d-none');
                toggleBtn.onclick = null;
            }

            fetch('admin/actions/get_product_reviews.php?product_id=' + encodeURIComponent(productId))
                .then(r => r.json())
                .then(data => {
                    if (avgRatingEl) avgRatingEl.textContent = data.avg_rating > 0 ? parseFloat(data.avg_rating)
                        .toFixed(1) : '0.0';
                    if (avgStarsEl) avgStarsEl.innerHTML = getStarsHtml(parseFloat(data.avg_rating || 0));
                    if (reviewCountEl) reviewCountEl.textContent = (data.review_count || 0) + ' Reviews';
                    listEl.innerHTML = data.reviews_html || '<div class="reviews-empty">No approved reviews yet.</div>';

                    // Setup "View more" toggle
                    const collapsed = listEl.querySelectorAll('.review-collapsed');
                    if (toggleBtn && collapsed.length > 0) {
                        toggleBtn.classList.remove('d-none');
                        let expanded = false;
                        toggleBtn.innerHTML =
                            `<i class="fas fa-chevron-down me-1"></i> View All ${collapsed.length + 1} Reviews`;
                        toggleBtn.onclick = () => {
                            expanded = !expanded;
                            collapsed.forEach(el => el.classList.toggle('review-collapsed', !expanded));
                            toggleBtn.innerHTML = expanded ?
                                '<i class="fas fa-chevron-up me-1"></i> Show Less' :
                                `<i class="fas fa-chevron-down me-1"></i> View All ${collapsed.length + 1} Reviews`;
                        };
                    }
                })
                .catch(() => {
                    listEl.innerHTML = '<div class="reviews-empty">Could not load reviews.</div>';
                });
        }

        function cancelOrder(orderId) {

            if (confirm("Are you sure you want to cancel this order?")) {

                window.location.href =
                    "admin/actions/cancel_order.php?id=" + orderId;

            }
        }
        document.addEventListener('DOMContentLoaded', function () {
            hidePageLoader();
            loadThemePreference();
            loadCart();
            renderCart();
            updateCartCount();
            syncFloatingCart();
            loadFavorites();
            loadProducts();
            startCountdown();

            // Populate navbar account user name & role options
            const userNameEl = document.getElementById('userName');
            if (userNameEl) {
                userNameEl.textContent = '<?= htmlspecialchars($data['name'] ?? $data['email'] ?? 'Account'); ?>';
            }
            <?php if (($data['role'] ?? '') === 'admin'): ?>
                const adminLink = document.getElementById('adminLink');
                if (adminLink) adminLink.style.display = 'block';
            <?php endif; ?>
            <?php if (($data['role'] ?? '') === 'seller'): ?>
                const sellerLink = document.getElementById('sellerLink');
                if (sellerLink) sellerLink.style.display = 'block';
            <?php endif; ?>

            // ?section=orders (or any section) from PHP takes top priority (e.g. after order placed)
            const phpSection = '<?php echo $initialSection; ?>';
            const savedSection = sessionStorage.getItem('activeSection');

            if (phpSection && phpSection !== 'home') {
                // PHP explicitly told us where to go (e.g. after order redirect)
                showSection(phpSection);
                sessionStorage.removeItem('activeSection'); // clear stale saved state
            } else if (savedSection) {
                showSection(savedSection);
            } else {
                showSection(phpSection || 'home');
            }

            const searchProduct =
                document.getElementById(
                    'searchProduct'
                );

            if (searchProduct) {

                searchProduct.addEventListener(
                    'input',
                    filterProducts
                );
            }

            const sortProductsEl =
                document.getElementById(
                    'sortProducts'
                );

            if (sortProductsEl) {

                sortProductsEl.addEventListener(
                    'change',
                    sortProducts
                );
            }

            const loadMoreBtn =
                document.getElementById(
                    'loadMoreProducts'
                );

            if (loadMoreBtn) {

                loadMoreBtn.addEventListener(
                    'click',
                    loadMoreProducts
                );
            }
            showLoading(false);
        });
    </script>

    <!-- Category loading bar — must be at body level (position:fixed needs visible ancestor) -->
    <div id="categoryLoadingBar" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:9999;
     background:linear-gradient(135deg,#7c3aed,#4f46e5);padding:14px 24px;justify-content:center;
     animation:slideUpBar .3s ease;">
        <div style="display:flex;align-items:center;gap:12px;color:#fff;font-size:14px;font-weight:700;">
            <div style="width:20px;height:20px;border:3px solid rgba(255,255,255,.3);border-top-color:#fff;
             border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0;"></div>
            <span id="categoryLoadingText">Loading products...</span>
        </div>
    </div>
    <style>
        @keyframes slideUpBar {
            from {
                transform: translateY(100%);
            }

            to {
                transform: translateY(0);
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>


    <!-- =============================================
     FLOATING CART WIDGET
     ============================================= -->
    <div id="floatingCart">
        <div id="floatingCartDrawer">
            <div class="fc-header">
                <span class="fc-title"><i class="fas fa-shopping-cart me-2"></i>Cart</span>
                <button class="fc-close" onclick="toggleFloatingCart(false)" aria-label="Close cart">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="fc-items" id="fcItems">
                <div class="fc-empty">
                    <i class="fas fa-shopping-bag"></i>
                    Your cart is empty
                </div>
            </div>
            <div class="fc-footer">
                <div class="fc-total-row">
                    <span class="fc-total-label">Total</span>
                    <span class="fc-total-value" id="fcTotal">₹0.00</span>
                </div>
                <button class="fc-view-btn" onclick="toggleFloatingCart(false); showSection('cart')">
                    <i class="fas fa-arrow-right me-2"></i>View Full Cart
                </button>
            </div>
        </div>
        <button id="floatingCartBtn" onclick="toggleFloatingCart()" aria-label="Open cart">
            <i class="fas fa-shopping-cart"></i>
            <span id="floatingCartBadge">0</span>
        </button>
    </div>

    <!-- ── TOAST CONTAINER (Point 10) ── -->
    <div id="quigly-toast-container"></div>

    <!-- ── RECENTLY VIEWED SECTION (Point 12) — injected into home section ── -->

    <script>
        // ── POINT 12: Recently Viewed ─────────────────────────────
        const RV_KEY = 'quigly_rv_' + (typeof currentUserId !== 'undefined' ? currentUserId : 'guest');
        const RV_MAX = 8;

        function addToRecentlyViewed(productId) {
            const product = (typeof products !== 'undefined' ? products : []).find(p => String(p.id) === String(productId));
            if (!product) return;
            try {
                let rv = JSON.parse(localStorage.getItem(RV_KEY) || '[]');
                rv = rv.filter(p => String(p.id) !== String(productId));
                rv.unshift({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    image: product.image
                });
                if (rv.length > RV_MAX) rv = rv.slice(0, RV_MAX);
                localStorage.setItem(RV_KEY, JSON.stringify(rv));
                renderRecentlyViewed();
            } catch (e) { }
        }

        function renderRecentlyViewed() {
            try {
                const rv = JSON.parse(localStorage.getItem(RV_KEY) || '[]');
                const section = document.getElementById('recentlyViewedSection');
                const scroll = document.getElementById('rvScroll');
                if (!section || !scroll) return;

                if (rv.length < 2) {
                    section.style.display = 'none';
                    return;
                }
                section.style.display = 'block';

                scroll.innerHTML = rv.map(p => `
            <div class="rv-card" onclick="openProductPage('${p.id}')">
                <img src="${p.image || 'assets/images/no-image.png'}" class="rv-card-img" alt="${p.name || ''}" onload="this.classList.add('loaded')" onerror="this.onerror=null;this.src='assets/images/no-image.png';this.classList.add('loaded')">
                <div class="rv-card-body">
                    <div class="rv-card-name">${p.name || ''}</div>
                    <div class="rv-card-price">₹${Number(p.price || 0).toFixed(0)}</div>
                </div>
            </div>
        `).join('');
            } catch (e) { }
        }

        function clearRecentlyViewed() {
            localStorage.removeItem(RV_KEY);
            const section = document.getElementById('recentlyViewedSection');
            if (section) section.style.display = 'none';
        }

        // Patch openProductPage to track views
        const _origOpenProductPage = typeof openProductPage === 'function' ? openProductPage : null;
        if (window.openProductPage) {
            const _orig = window.openProductPage;
            window.openProductPage = function (productId) {
                addToRecentlyViewed(productId);
                return _orig(productId);
            };
        }

        // Render on page load
        document.addEventListener('DOMContentLoaded', function () {
            // Inject the recently viewed HTML into home section
            const homeSection = document.getElementById('home');
            if (homeSection) {
                const rvHtml = `
        <div id="recentlyViewedSection" style="display:none;">
            <div class="container" style="padding-bottom:48px;">
                <div class="recently-viewed-header">
                    <div class="recently-viewed-title">
                        <i class="fas fa-history"></i>Recently Viewed
                    </div>
                    <button class="recently-viewed-clear" onclick="clearRecentlyViewed()">
                        <i class="fas fa-trash-alt me-1"></i>Clear
                    </button>
                </div>
                <div class="recently-viewed-scroll" id="rvScroll"></div>
            </div>
        </div>`;
                homeSection.insertAdjacentHTML('beforeend', rvHtml);
                renderRecentlyViewed();
            }
        });


        // ── POINT 18: Ensure all product images are always visible ────────────────────
        document.addEventListener('DOMContentLoaded', function () {
            // Mark already-loaded images immediately
            function markLoadedImages() {
                document.querySelectorAll('.product-img img').forEach(img => {
                    if (img.complete && img.naturalWidth > 0) {
                        img.classList.add('loaded');
                    } else {
                        img.addEventListener('load', function () {
                            this.classList.add('loaded');
                        }, {
                            once: true
                        });
                        img.addEventListener('error', function () {
                            this.classList.add('loaded');
                        }, {
                            once: true
                        });
                    }
                });
            }

            markLoadedImages();

            // Re-run when new cards are rendered into any product container
            const productContainers = ['productsContainer', 'featuredProducts', 'trendingProducts', 'dealsProducts',
                'favoritesContainer'
            ];
            productContainers.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                new MutationObserver(markLoadedImages).observe(el, {
                    childList: true,
                    subtree: true
                });
            });
        });
    </script>
</body>

</html>