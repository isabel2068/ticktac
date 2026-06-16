<?php

$host = 'localhost';  // Database host
$dbname = 'db_calendar';  // Database name
$username = 'super_admin';  // Database username
$password = 'sfjOP163*/;';  // Database password

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Optional: Print a message to indicate successful connection
   // echo "Connected to the database successfully.";
} catch (PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
}
?>
