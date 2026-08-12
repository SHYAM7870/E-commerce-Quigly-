<?php
include '../../function.php';

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM products WHERE id='$id'");

header("Location: ../Pages/product_list.php");