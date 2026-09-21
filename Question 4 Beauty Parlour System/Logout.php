<?php
session_start();

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

if (isset($_COOKIE['remember_client'])) {
    setcookie('remember_client', '', time() - 3600, '/');
}

session_destroy();
header("Location: BP_Login.php");
exit();
?>
