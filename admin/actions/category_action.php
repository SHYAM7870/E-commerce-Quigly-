<?php
session_start();
include("../includes/db.php");

// Auth: only admin can add categories
if (!isset($_SESSION['email']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../Pages/add_category.php?msg=unauthorized");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Pages/add_category.php?msg=invalid");
    exit;
}

$name = trim($_POST['name'] ?? '');
if ($name === '') {
    header("Location: ../Pages/add_category.php?msg=name_required");
    exit;
}

$imageName = '';
if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tmp  = $_FILES['image']['tmp_name'];
    $size = (int)$_FILES['image']['size'];
    $ext  = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    $allowedExt  = ['jpg', 'jpeg', 'png', 'webp'];
    $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $tmp);
    finfo_close($finfo);

    if ($size > 2 * 1024 * 1024 || !in_array($ext, $allowedExt, true) || !in_array($mime, $allowedMime, true)) {
        header("Location: ../Pages/add_category.php?msg=invalid_image");
        exit;
    }

    $imageName = time() . '_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($tmp, "../../upload/" . $imageName)) {
        header("Location: ../Pages/add_category.php?msg=upload_failed");
        exit;
    }
} else {
    header("Location: ../Pages/add_category.php?msg=image_required");
    exit;
}

// FIX: prepared statement — no SQL injection
$stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $imageName);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: ../Pages/add_category.php?msg=success");
} else {
    $stmt->close();
    header("Location: ../Pages/add_category.php?msg=error");
}
