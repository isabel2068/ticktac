<?php
require 'db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM tbl_tasks WHERE id = ? AND soft_delete = 0");
$stmt->execute([$id]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

/* =========================
   FORCE DATE FORMAT
   OUTPUT: 2026-05-29
========================= */
if (!empty($row['event_date'])) {
    $row['event_date'] = date('Y-m-d', strtotime($row['event_date']));
}

echo json_encode($row);