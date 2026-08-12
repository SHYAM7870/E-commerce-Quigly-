<?php

include '../../function.php';

if (
    isset($_GET['id']) &&
    isset($_GET['status'])
) {

    $id = (int)$_GET['id'];

    $status =
        $_GET['status'] === 'blocked'
        ? 'blocked'
        : 'active';

    mysqli_query(
        $conn,
        "UPDATE quigly_table
         SET status='$status'
         WHERE id='$id'"
    );
}

header("Location: ../Pages/user_list.php");

exit;

?>