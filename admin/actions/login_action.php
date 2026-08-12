<?php
// =============================================
// FIX 5: login_action.php
// Bug: raw $email in SELECT query — SQL injection
// Fix: use prepared statement with bind_param
// =============================================
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include("../includes/db.php");

    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        header("Location: /Quigly/login.php?error=All+fields+required");
        exit;
    }

    // FIX: prepared statement — no more SQL injection
    $stmt = $conn->prepare("SELECT * FROM quigly_table WHERE email = ? AND status = 'active' LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {

            $_SESSION['email']   = $row['email'];
            $_SESSION['user_id'] = $row['id'];
            // FIX: close statement before redirect
            $stmt->close();

            if (isset($row['admin']) && $row['admin'] == 1) {
                $_SESSION['role'] = "admin";
                header("Location: /Quigly/admin/index.php");
                exit;
            } else {
                $_SESSION['role'] = "user";
                header("Location: /Quigly/index.php");
                exit;
            }

        } else {
            // FIX: close statement before redirect
            $stmt->close();
            header("Location: /Quigly/login.php?error=Wrong+Password");
            exit;
        }

    } else {

        $checkStmt = $conn->prepare("
            SELECT id
            FROM quigly_table
            WHERE email = ?
            AND status = 'blocked'
            LIMIT 1
        ");

        $checkStmt->bind_param("s", $email);

        $checkStmt->execute();

        $blockedResult =
            $checkStmt->get_result();

        if ($blockedResult->num_rows > 0) {

            header(
                "Location: /Quigly/login.php?error=Your+account+has+been+blocked"
            );

            exit;

        } else {

            header(
                "Location: /Quigly/login.php?error=User+Not+Found"
            );

            exit;
        }
    }

}
?>
