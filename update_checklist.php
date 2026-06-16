<?php
require 'db.php';

$task_id = $_POST['task_id'];
$index   = $_POST['index'];
$done    = $_POST['done'];

$stmt = $pdo->prepare("SELECT checklist FROM tbl_tasks WHERE id = ?");
$stmt->execute([$task_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$checklist = json_decode($row['checklist'], true);

if (isset($checklist[$index])) {
    $checklist[$index]['done'] = (int)$done;
}

$updated = json_encode($checklist);

$update = $pdo->prepare("UPDATE tbl_tasks SET checklist = ? WHERE id = ?");
$update->execute([$updated, $task_id]);

echo json_encode(["success" => true]);