<?php
// =============================================
// FIX 4: function.php
// Bug 1: delete_data() returned false on BOTH success and failure
//         was: if($run){ return false; } else { return false; }
//         fix: if($run){ return true; }
// Bug 2: delete_data() had raw $id (no cast) — fixed with (int)
// =============================================
include_once('admin/includes/db.php');
global $conn;

function data($table)
{
    global $conn;
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table); // whitelist table name
    $sql = "SELECT * FROM `$table` ORDER BY id DESC";
    $run = mysqli_query($conn, $sql);
    if ($run) {
        if (mysqli_num_rows($run)) {
            return mysqli_fetch_all($run, MYSQLI_ASSOC);
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function delete_data($table, $id)
{
    global $conn;
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table); // whitelist table name
    $id    = (int) $id;                                     // FIX: cast to int — prevents SQL injection
    $sql   = "DELETE FROM `$table` WHERE id = $id";
    $run   = mysqli_query($conn, $sql);
    if ($run) {
        return true;  // FIX: was returning false on success
    } else {
        return false;
    }
}
?>
