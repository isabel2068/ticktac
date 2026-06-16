<?php
require 'db.php';

if (isset($_POST['username'])) {
    $username = trim($_POST['username']);

    $stmt = $pdo->prepare("SELECT id FROM tbl_users WHERE username = ?");
    $stmt->execute([$username]);

    echo json_encode([
        "exists" => $stmt->rowCount() > 0
    ]);
}
?>