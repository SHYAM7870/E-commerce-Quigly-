<?php
// FIX: session_start() must come BEFORE session_destroy()
// Previous code called session_destroy() twice with session_start() in between
// which caused session data to not be cleared properly on some PHP configs.

session_start();
session_unset();      // clear all session variables first
session_destroy();    // then destroy the session
header("Location: login.php");
exit;
?>
