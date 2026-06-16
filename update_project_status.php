<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? '';

    $allowed = ['pending', 'ongoing', 'completed'];

    if (!$id || !in_array($status, $allowed)) {
        exit('Invalid data');
    }

    $stmt = $pdo->prepare("
        UPDATE tbl_tasks
        SET project_status = :status
        WHERE id = :id
    ");

    $success = $stmt->execute([
        ':status' => $status,
        ':id'     => $id
    ]);

    echo $success ? 'success' : 'failed';
}
?>