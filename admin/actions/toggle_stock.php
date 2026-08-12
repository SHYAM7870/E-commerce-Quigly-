<?php
include("../includes/db.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    mysqli_query($conn, "
        UPDATE products
        SET stock_status = IF(stock_status = 1, 0, 1)
        WHERE id = $id
    ");
}

header("Location: ../Pages/product_list.php");
exit;
?>