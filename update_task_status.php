<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($id && $status) {
        $stmt = $pdo->prepare("UPDATE tbl_tasks SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false]);
    }
}