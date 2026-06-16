<?php
session_start();

// store flash message
$_SESSION['logout_success'] = true;

// destroy login session data
$_SESSION = [];
session_destroy();

// redirect
header("Location: loginv2.php");
exit();
?>