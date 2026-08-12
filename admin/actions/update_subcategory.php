<?php
session_start();
include("../includes/db.php");

// Auth: only admin
if (!isset($_SESSION['email']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../Pages/subcategory_list.php?msg=unauthorized");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Pages/subcategory_list.php");
    exit;
}

$id          = (int)($_POST['id']          ?? 0);
$name        = trim($_POST['name']         ?? '');
$category_id = (int)($_POST['category_id'] ?? 0);

if ($id <= 0 || $name === '' || $category_id <= 0) {
    header("Location: ../Pages/subcategory_list.php?msg=invalid");
    exit;
}

// FIX: prepared statement — no SQL injection
$stmt = $conn->prepare("UPDATE subcategories SET name=?, category_id=? WHERE id=?");
$stmt->bind_param("sii", $name, $category_id, $id);
$stmt->execute();
$stmt->close();

header("Location: ../Pages/subcategory_list.php");
