<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT id FROM tbl_users WHERE email = ?");
    $stmt->execute([$email]);

    echo json_encode([
        "exists" => $stmt->rowCount() > 0
    ]);
}