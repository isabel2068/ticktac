<?php
require 'db.php';
date_default_timezone_set('Asia/Manila');

$today = new DateTime();

// First day of month
$start = new DateTime($today->format('Y-m-01'));

// Last day of month
$end = new DateTime($today->format('Y-m-t'));

$stmt = $pdo->prepare("
    SELECT 
        id,
        event_title,
        event_date,
        start_time,
        end_time,
        status,
        checklist,
        person_in_charge
    FROM tbl_tasks
    WHERE event_date BETWEEN :start AND :end
    AND soft_delete = 0
    ORDER BY event_date ASC, start_time ASC
");

$stmt->execute([
    ':start' => $start->format('Y-m-d'),
    ':end' => $end->format('Y-m-d')
]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));