<?php
session_start();
include("../includes/db.php");

// Auth: only admin
if (!isset($_SESSION['email']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../Pages/category_list.php?msg=unauthorized");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Pages/category_list.php");
    exit;
}

$id   = (int)($_POST['id']   ?? 0);
$name = trim($_POST['name']  ?? '');

if ($id <= 0 || $name === '') {
    header("Location: ../Pages/category_list.php?msg=invalid");
    exit;
}

// Fetch old image using prepared statement
$sel = $conn->prepare("SELECT image FROM categories WHERE id = ? LIMIT 1");
$sel->bind_param("i", $id);
$sel->execute();
$oldData  = $sel->get_result()->fetch_assoc();
$sel->close();
$oldImage = $oldData['image'] ?? '';

$newImageName = null;
$hasNewImage  = !empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK;

if ($hasNewImage) {
    $tmp  = $_FILES['image']['tmp_name'];
    $size = (int)$_FILES['image']['size'];
    $ext  = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    $allowedExt  = ['jpg', 'jpeg', 'png', 'webp'];
    $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $tmp);
    finfo_close($finfo);

    if ($size > 2 * 1024 * 1024 || !in_array($ext, $allowedExt, true) || !in_array($mime, $allowedMime, true)) {
        header("Location: ../Pages/category_list.php?msg=invalid_image");
        exit;
    }

    $newImageName = time() . '_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($tmp, "../../upload/" . $newImageName)) {
        header("Location: ../Pages/category_list.php?msg=upload_failed");
        exit;
    }

    // Remove old image
    if (!empty($oldImage) && file_exists("../../upload/" . $oldImage)) {
        unlink("../../upload/" . $oldImage);
    }

    // FIX: prepared statement with new image
    $stmt = $conn->prepare("UPDATE categories SET name=?, image=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $newImageName, $id);
} else {
    // FIX: prepared statement without image change
    $stmt = $conn->prepare("UPDATE categories SET name=? WHERE id=?");
    $stmt->bind_param("si", $name, $id);
}

$stmt->execute();
$stmt->close();

header("Location: ../Pages/category_list.php");
exit;
