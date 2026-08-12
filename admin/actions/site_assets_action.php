<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/feature_utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Pages/site_assets.php');
    exit;
}

$type   = strtolower(trim($_POST['type']   ?? ''));
$action = strtolower(trim($_POST['action'] ?? 'save'));
$id     = (int)($_POST['id'] ?? 0);

// Map type to homepage_media type column
$allowedTypes = ['banner', 'brand', 'logo'];
if (!in_array($type, $allowedTypes, true)) {
    header('Location: ../Pages/site_assets.php?error=Invalid+type');
    exit;
}

try {
    // ── DELETE ──────────────────────────────────────────────
    if ($action === 'delete' && $id > 0) {
        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM homepage_media WHERE id={$id}"));
        if ($row && $row['image']) {
            $abs = dirname(__DIR__, 2) . '/' . ltrim($row['image'], '/');
            if (is_file($abs)) @unlink($abs);
        }
        mysqli_query($conn, "DELETE FROM homepage_media WHERE id={$id}");
        header("Location: ../Pages/site_assets.php?tab={$type}s&msg=deleted");
        exit;
    }

    // ── SAVE / UPDATE ────────────────────────────────────────
    $title      = mysqli_real_escape_string($conn, trim($_POST['title']       ?? ''));
    $subtitle   = mysqli_real_escape_string($conn, trim($_POST['subtitle']    ?? ''));
    $link       = mysqli_real_escape_string($conn, trim($_POST['cta_url']     ?? 'index.php'));
    $btnText    = mysqli_real_escape_string($conn, trim($_POST['cta_text']    ?? 'Shop Now'));
    $sortOrder  = (int)($_POST['sort_order'] ?? 0);
    $isActive   = isset($_POST['is_active']) ? 1 : 0;

    // For brands, name goes into title column
    if ($type === 'brand') {
        $title  = mysqli_real_escape_string($conn, trim($_POST['name']    ?? ''));
        $link   = mysqli_real_escape_string($conn, trim($_POST['website'] ?? ''));
    }

    if ($title === '' && $type !== 'logo') {
        throw new RuntimeException($type === 'brand' ? 'Brand name is required.' : 'Title is required.');
    }

    // Upload image
    $imageSql = null;
    $fileKey  = ($type === 'brand') ? 'logo' : 'image';
    if (!empty($_FILES[$fileKey]['name']) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp','gif','svg'], true)) {
            throw new RuntimeException('Only JPG, PNG, WEBP, GIF or SVG files are allowed.');
        }
        $folder    = 'uploads/homepage_media';
        $uploadDir = dirname(__DIR__, 2) . '/' . $folder;
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
        $filename  = 'hm_' . preg_replace('/[^a-z0-9]/', '_', strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_FILENAME))) . '_' . substr(uniqid(), -9) . '.' . $ext;
        $target    = $uploadDir . '/' . $filename;
        if (!move_uploaded_file($_FILES[$fileKey]['tmp_name'], $target)) {
            throw new RuntimeException('Could not move uploaded file.');
        }
        $imageSql = mysqli_real_escape_string($conn, $folder . '/' . $filename);
    }

    if ($id > 0) {
        // Update existing row
        $imgPart = $imageSql !== null ? ", image='{$imageSql}'" : '';
        mysqli_query($conn, "
            UPDATE homepage_media SET
                title='{$title}',
                subtitle='{$subtitle}',
                link='{$link}',
                button_text='{$btnText}',
                sort_order={$sortOrder},
                status={$isActive}
                {$imgPart}
            WHERE id={$id}
        ");
        header("Location: ../Pages/site_assets.php?tab={$type}s&msg=updated");
        exit;
    }

    // Insert new row
    if ($imageSql === null) $imageSql = '';
    mysqli_query($conn, "
        INSERT INTO homepage_media (type, title, subtitle, image, link, button_text, sort_order, status)
        VALUES ('{$type}', '{$title}', '{$subtitle}', '{$imageSql}', '{$link}', '{$btnText}', {$sortOrder}, {$isActive})
    ");
    header("Location: ../Pages/site_assets.php?tab={$type}s&msg=created");
    exit;

} catch (Throwable $e) {
    $msg = urlencode($e->getMessage());
    header("Location: ../Pages/site_assets.php?error={$msg}");
    exit;
}
?>
