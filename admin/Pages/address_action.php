<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['email'])) {
    header('Location: /Quigly/login.php');
    exit;
}

$email = mysqli_real_escape_string($conn, $_SESSION['email']);
$userQ = mysqli_query($conn, "SELECT id FROM quigly_table WHERE email='{$email}' LIMIT 1");
$user = mysqli_fetch_assoc($userQ);
if (!$user) {
    header('Location: /Quigly/login.php');
    exit;
}
$userId = (int)$user['id'];

if (isset($_GET['delete'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM saved_addresses WHERE id={$id} AND user_id={$userId}");
    header('Location: ../../saved_addresses.php');
    exit;
}

if (isset($_GET['set_default'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "UPDATE saved_addresses SET is_default=0 WHERE user_id={$userId}");
    mysqli_query($conn, "UPDATE saved_addresses SET is_default=1 WHERE id={$id} AND user_id={$userId}");
    header('Location: ../../saved_addresses.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../saved_addresses.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$label = mysqli_real_escape_string($conn, trim($_POST['label'] ?? 'Home'));
$recipient = mysqli_real_escape_string($conn, trim($_POST['recipient_name'] ?? ''));
$phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
$line1 = mysqli_real_escape_string($conn, trim($_POST['address_line1'] ?? ''));
$line2 = mysqli_real_escape_string($conn, trim($_POST['address_line2'] ?? ''));
$city = mysqli_real_escape_string($conn, trim($_POST['city'] ?? ''));
$state = mysqli_real_escape_string($conn, trim($_POST['state'] ?? ''));
$pincode = mysqli_real_escape_string($conn, trim($_POST['pincode'] ?? ''));
$country = mysqli_real_escape_string($conn, trim($_POST['country'] ?? 'India'));
$landmark = mysqli_real_escape_string($conn, trim($_POST['landmark'] ?? ''));
$isDefault = isset($_POST['is_default']) ? 1 : 0;

if ($recipient === '' || $phone === '' || $line1 === '' || $city === '' || $state === '' || $pincode === '') {
    header('Location: saved_addresses.php?error=1');
    exit;
}

if ($isDefault === 1) {
    mysqli_query($conn, "UPDATE saved_addresses SET is_default=0 WHERE user_id={$userId}");
}

if ($id > 0) {
    mysqli_query($conn, "
        UPDATE saved_addresses SET
            label='{$label}',
            recipient_name='{$recipient}',
            phone='{$phone}',
            address_line1='{$line1}',
            address_line2='{$line2}',
            city='{$city}',
            state='{$state}',
            pincode='{$pincode}',
            country='{$country}',
            landmark='{$landmark}',
            is_default={$isDefault}
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

header('Location: ../../saved_addresses.php');
exit;
?>
