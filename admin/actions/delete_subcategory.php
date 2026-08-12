<?php
include("../includes/db.php");

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM subcategories WHERE id='$id'");

header("Location: ../Pages/subcategory_list.php");