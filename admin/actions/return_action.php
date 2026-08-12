<?php
// =============================================
// FIX: return_action.php
// Bug 1: No session/auth check — any unauthenticated user
//         could POST to this endpoint and change return request statuses.
// Fix:  Start session, verify admin role before processing.
// Bug 2: $adminNote was escaped but then embedded via interpolation
//         which still risks second-order injection. Use prepared statement.
// Bug 3: $status was whitelisted but not escaped — now uses
//         whitelist check + prepared statement for consistency.
// =============================================
session_start();
require_once __DIR__ . '/../includes/db.php';

// Auth check: only admins may update return requests
if (empty($_SESSION['email']) || (($_SESSION['role'] ?? '') !== 'admin')) {
    http_response_code(403);
    header('Location: ../Pages/return_requests.php?error=unauthorized');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Pages/return_requests.php');
    exit;
}

$id        = (int)($_POST['id']          ?? 0);
$status    = strtolower(trim($_POST['status']      ?? 'pending'));
$adminNote = trim($_POST['admin_note']   ?? '');

$allowed = ['pending','approved','rejected','pickup_scheduled','received','refunded','completed'];
if (!in_array($status, $allowed, true)) $status = 'pending';

if ($id > 0) {
    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn,
        "UPDATE return_requests SET status = ?, admin_note = ? WHERE id = ?"
    );
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssi', $status, $adminNote, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header('Location: ../Pages/return_requests.php?status=' . urlencode($status) . '&updated=1');
exit;
