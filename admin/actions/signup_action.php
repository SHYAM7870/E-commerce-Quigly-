<?php
// =============================================
// FIX 6: signup_action.php
// Bug: raw $name, $number in INSERT — SQL injection
// Fix: prepared statement for INSERT
// Also cleaned up commented-out session cleanup code
// =============================================
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include("../includes/db.php");

    if (!isset($_SESSION['verified']) || $_SESSION['verified'] !== true) {
        header("Location: /Quigly/register.php?error=Email+not+verified");
        exit;
    }

    $name             = trim($_POST['name']             ?? '');
    $email            = $_SESSION['email'];
    $number           = trim($_POST['number']           ?? '');
    $password         = $_POST['password']         ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($number) || empty($password)) {
        header("Location: /Quigly/register.php?error=All+fields+required");
        exit;
    }

    if ($password !== $confirm_password) {
        header("Location: /Quigly/register.php?error=Password+mismatch");
        exit;
    }

    // FIX: use prepared statement to check duplicate email
    $checkStmt = $conn->prepare("SELECT id FROM quigly_table WHERE email = ? LIMIT 1");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $checkStmt->close();
        header("Location: /Quigly/register.php?error=Email+already+exists");
        exit;
    }
    $checkStmt->close();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $image = "";

    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $image = time() . "_" . uniqid() . "." . $ext;
            $tmp   = $_FILES['image']['tmp_name'];

            if (!move_uploaded_file($tmp, "../../upload/" . $image)) {
                header("Location: /Quigly/register.php?error=Image+upload+failed");
                exit;
            }
        } else {
            header("Location: /Quigly/register.php?error=Invalid+image+format");
            exit;
        }
    }

    // FIX: prepared statement for INSERT — no SQL injection
    $stmt = $conn->prepare(
        "INSERT INTO quigly_table (name, email, number, password, image) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssss", $name, $email, $number, $hashed_password, $image);

    if ($stmt->execute()) {
        // Clean up OTP session data
        $message =
        "New user registered: " .
        $name;

        mysqli_query(
        $conn,
        "INSERT INTO notifications
        (message,type,is_read)
        VALUES(
        '$message',
        'user',
        0
        )"
        );
        // Get the new user id for session
        $newId = $conn->insert_id;
        $stmt->close();
        // Clean up OTP session data and auto-login the new user
        unset($_SESSION['verified'], $_SESSION['otp'], $_SESSION['otp_time']);
        // Set login session so user goes directly to main page
        $_SESSION['email']   = $email;
        $_SESSION['user_id'] = $newId;
        $_SESSION['role']    = 'user';
        header("Location: /Quigly/index.php");
        exit;
    } else {
        $stmt->close();
        header("Location: /Quigly/register.php?error=Registration+failed");
        exit;
    }
}
?>
