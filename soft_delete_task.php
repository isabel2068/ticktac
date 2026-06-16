<?php

session_start();
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode([
        'success' => false,
        'message' => 'Task ID missing'
    ]);
    exit;
}

try {

    $stmt = $pdo->prepare("
        UPDATE tbl_tasks
        SET 
            soft_delete = 1,
            deleted_at = NOW()
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    echo json_encode([
        'success' => true
    ]);

} catch (PDOException $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);

}