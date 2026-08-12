<?php
include("../includes/db.php");

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM categories WHERE id='$id'");

header("Location: ../Pages/category_list.php?msg=delete");