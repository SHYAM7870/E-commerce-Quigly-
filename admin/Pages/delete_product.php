<?php

include '../includes/db.php';

if(isset($_GET['id'])){

    $id = (int) $_GET['id'];

    $query = "
    
        SELECT image 
        FROM products 
        WHERE id = '$id'
    
    ";

    $result = mysqli_query($conn, $query);

    $product = mysqli_fetch_assoc($result);

    if($product){

        $image_path = "../../upload/" . $product['image'];

        if(file_exists($image_path)){

            unlink($image_path);

        }

    }

    mysqli_query($conn, "
    
        DELETE FROM products 
        WHERE id = '$id'
    
    ");

    header("Location: product_list.php");

    exit();

}

?>