<?php
session_start();
include("../includes/db.php");

// Auth: only admin can add subcategories
if (!isset($_SESSION['email']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../Pages/add_subcategory.php?msg=unauthorized");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Pages/add_subcategory.php?msg=invalid");
    exit;
}

$name        = trim($_POST['name']        ?? '');
$category_id = (int)($_POST['category_id'] ?? 0);

if ($name === '' || $category_id <= 0) {
    header("Location: ../Pages/add_subcategory.php?msg=fields_required");
    exit;
}

// FIX: prepared statement — no SQL injection
$stmt = $conn->prepare("INSERT INTO subcategories (name, category_id) VALUES (?, ?)");
$stmt->bind_param("si", $name, $category_id);
$stmt->execute();
$stmt->close();

header("Location: ../Pages/add_subcategory.php?msg=success");
