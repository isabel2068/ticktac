<?php
$newPassword = '0004dmin@dti'; // Define a new password
$newHash = password_hash($newPassword, PASSWORD_BCRYPT); // Generate a new hash
echo $newHash; // Use this hash to replace the old one in the database

?>
