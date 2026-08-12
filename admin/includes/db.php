<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servername = getenv('DB_HOST') ?: '127.0.0.1';
$username   = getenv('DB_USER') ?: 'root';
$password   = getenv('DB_PASS') ?: '';
$database   = getenv('DB_NAME') ?: 'college_db';
$port       = (int)(getenv('DB_PORT') ?: 3306);

$conn = null;
$portAttempts = array_values(array_unique(array_filter([
    $port,
    3306,
    3307,
], static fn($v) => is_int($v) && $v > 0)));

$lastError = null;
foreach ($portAttempts as $tryPort) {
    try {
        $conn = mysqli_connect($servername, $username, $password, $database, $tryPort);
        if ($conn instanceof mysqli) {
            break;
        }
    } catch (mysqli_sql_exception $e) {
        $lastError = $e->getMessage();
        $conn = null;
    }
}

if (!$conn instanceof mysqli) {
    die('Database connection failed. Check MySQL is running, the database name is correct, and the port is right. Last error: ' . ($lastError ?? mysqli_connect_error()));
}

mysqli_set_charset($conn, 'utf8mb4');
$connect = $conn; // legacy alias used by older files
?>
