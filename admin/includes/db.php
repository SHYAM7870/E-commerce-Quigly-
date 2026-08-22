<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servername =  '127.0.0.1';
$username   =  'root';
$password   =  '';
$database   = 'college_db';
$conn = mysqli_connect($servername, $username, $password, $database);
 

if (!$conn instanceof mysqli) {
    die('Database connection failed. Check MySQL is running, the database name is correct, and the port is right. Last error: ' . ($lastError ?? mysqli_connect_error()));
}

mysqli_set_charset($conn, 'utf8mb4');
$connect = $conn; 
?>