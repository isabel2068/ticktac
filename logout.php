<?php
session_start();

// destroy session
$_SESSION = [];
session_unset();
session_destroy();

// redirect with flag
header("Location: loginv2.php?logout=1");
exit();