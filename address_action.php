<?php
session_start();
require_once __DIR__ . '/admin/includes/db.php';
if (!function_exists('qf_escape')) {
    function qf_escape($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('qf_int')) {
    function qf_int($v): int { return (int)($v ?? 0); }
}

// Auto-create saved_addresses table if it doesn't exist
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS saved_addresses (
        id             INT AUTO_INCREMENT PRIMARY KEY,
        user_id        INT          NOT NULL,
        label          VARCHAR(50)  NOT NULL DEFAULT 'Home',
        recipient_name VARCHAR(150) NOT NULL,
        phone          VARCHAR(20)  NOT NULL,
        address_line1  VARCHAR(250) NOT NULL,
        address_line2  VARCHAR(250) DEFAULT '',
        city           VARCHAR(100) NOT NULL,
        state          VARCHAR(100) NOT NULL,
        pincode        VARCHAR(10)  NOT NULL,
        country        VARCHAR(100) NOT NULL DEFAULT 'India',
        landmark       VARCHAR(200) DEFAULT '',
        is_default     TINYINT(1)  DEFAULT 0,
        is_active      TINYINT(1)  DEFAULT 1,
        created_at     TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
        INDEX(user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$email  = mysqli_real_escape_string($conn, $_SESSION['email']);
$userQ  = mysqli_query($conn, "SELECT id FROM quigly_table WHERE email='{$email}' LIMIT 1");
$user   = mysqli_fetch_assoc($userQ);
if (!$user) {
    header('Location: login.php');
    exit;
}
$userId = (int)$user['id'];

// DELETE
if (isset($_GET['delete'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM saved_addresses WHERE id={$id} AND user_id={$userId}");
    header('Location: saved_addresses.php');
    exit;
}

// SET DEFAULT
if (isset($_GET['set_default'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "UPDATE saved_addresses SET is_default=0 WHERE user_id={$userId}");
    mysqli_query($conn, "UPDATE saved_addresses SET is_default=1 WHERE id={$id} AND user_id={$userId}");
    header('Location: saved_addresses.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: saved_addresses.php');
    exit;
}

$id        = (int)($_POST['id'] ?? 0);
$label     = mysqli_real_escape_string($conn, trim($_POST['label']          ?? 'Home'));
$recipient = mysqli_real_escape_string($conn, trim($_POST['recipient_name'] ?? ''));
$phone     = mysqli_real_escape_string($conn, trim($_POST['phone']          ?? ''));
$line1     = mysqli_real_escape_string($conn, trim($_POST['address_line1']  ?? ''));
$line2     = mysqli_real_escape_string($conn, trim($_POST['address_line2']  ?? ''));
$city      = mysqli_real_escape_string($conn, trim($_POST['city']           ?? ''));
$state     = mysqli_real_escape_string($conn, trim($_POST['state']          ?? ''));
$pincode   = mysqli_real_escape_string($conn, trim($_POST['pincode']        ?? ''));
$country   = mysqli_real_escape_string($conn, trim($_POST['country']        ?? 'India'));
$landmark  = mysqli_real_escape_string($conn, trim($_POST['landmark']       ?? ''));
$isDefault = isset($_POST['is_default']) ? 1 : 0;

if ($recipient === '' || $phone === '' || $line1 === '' || $city === '' || $state === '' || $pincode === '') {
    header('Location: saved_addresses.php?error=missing_fields');
    exit;
}

if ($isDefault === 1) {
    mysqli_query($conn, "UPDATE saved_addresses SET is_default=0 WHERE user_id={$userId}");
}

if ($id > 0) {
    mysqli_query($conn, "
        UPDATE saved_addresses SET
            label='{$label}', recipient_name='{$recipient}', phone='{$phone}',
            address_line1='{$line1}', address_line2='{$line2}', city='{$city}',
            state='{$state}', pincode='{$pincode}', country='{$country}',
            landmark='{$landmark}', is_default={$isDefault}
        WHERE id={$id} AND user_id={$userId}
    ");
} else {
    mysqli_query($conn, "
        INSERT INTO saved_addresses
        (user_id, label, recipient_name, phone, address_line1, address_line2, city, state, pincode, country, landmark, is_default, is_active)
        VALUES
        ({$userId}, '{$label}', '{$recipient}', '{$phone}', '{$line1}', '{$line2}', '{$city}', '{$state}', '{$pincode}', '{$country}', '{$landmark}', {$isDefault}, 1)
    ");
}

header('Location: saved_addresses.php');
exit;
?>
