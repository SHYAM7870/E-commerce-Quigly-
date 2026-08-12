<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){

    include_once("admin/includes/db.php");

    $id=$_POST['id'];
    $name=$_POST['name'];
    $email=$_POST['email'];
    $number=$_POST['number'];
    $password = md5($_POST['password']);

    if(empty($password)){
        $sql="UPDATE quigly_table SET name='$name',email='$email',number='$number' where id = '$id' ";
    }
    else{
        
        $sql="UPDATE quigly_table SET name= '$name',email= '$email' ,number= '$number',password= '$password' where id = '$id' ";
    }

    $run=mysqli_query($conn,$sql);
    if($run){
        header("location: index.php");
        exit;
    }
    else{
        echo "<script> alert('Update Failed');window.location.href='index.php';</script>";
    }
}

?>