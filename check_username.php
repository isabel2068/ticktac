<?php
require 'db.php';

$username = $_POST['username'];

$stmt = $pdo->prepare("SELECT id FROM tbl_users WHERE username = ?");
$stmt->execute([$username]);

echo $stmt->rowCount() > 0 ? "taken" : "available";
?>