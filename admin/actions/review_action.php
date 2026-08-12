<?php
// admin/actions/review_action.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['email'])) {
    header("Location: ../../login.php");
    exit;
}

$id     = isset($_GET['id'])     ? (int)$_GET['id']          : 0;
$action = isset($_GET['action']) ? trim($_GET['action'])      : '';
$redir  = isset($_GET['redirect']) ? trim($_GET['redirect']) : '';

if ($id <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    header("Location: ../Pages/reviews_list.php?msg=invalid");
    exit;
}

$status = $action === 'approve' ? 'approved' : 'rejected';

$stmt = $conn->prepare("UPDATE reviews SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);

// Decide where to redirect
function safeRedirect($redir, $fallback) {
    // Only allow relative URLs within the app (no external redirects)
    if ($redir !== '' && preg_match('#^(/|reviews_list|../Pages)#', $redir)) {
        header("Location: " . $redir . (strpos($redir, '?') !== false ? '&' : '?') . "msg=success");
    } else {
        header("Location: " . $fallback . "?msg=success");
    }
    exit;
}

if ($stmt->execute()) {
    safeRedirect($redir, '../Pages/reviews_list.php');
}

header("Location: ../Pages/reviews_list.php?msg=error");
exit;
?>
