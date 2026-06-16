<?php
require 'db.php';

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$month = $_GET['month'] ?? date('Y-m');

$stmt = $pdo->prepare("
    SELECT 
        event_title,
        event_date,
        start_time,
        end_time,
        location,
        venue,
        person_in_charge,
        status,
        checklist
    FROM tbl_tasks
    WHERE DATE_FORMAT(event_date, '%Y-%m') = ?
    AND soft_delete = 0
    ORDER BY event_date ASC, start_time ASC
");

$stmt->execute([$month]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// PROCESS PERSON IN CHARGE → GET USER DATA
foreach ($tasks as &$task) {

    // ensure checklist is never null
    $task['checklist'] = $task['checklist'] ?? '[]';

    if ($task['checklist'] === null || $task['checklist'] === '') {
        $task['checklist'] = '[]';
    }

    $userIds = json_decode($task['person_in_charge'], true);

    if (!empty($userIds) && is_array($userIds)) {

        $placeholders = implode(',', array_fill(0, count($userIds), '?'));

        $userStmt = $pdo->prepare("
            SELECT id, first_name, last_name, profile_pic
            FROM tbl_users
            WHERE id IN ($placeholders)
        ");

        $userStmt->execute($userIds);
        $task['personnel'] = $userStmt->fetchAll(PDO::FETCH_ASSOC);

    } else {
        $task['personnel'] = [];
    }
}

echo json_encode($tasks);


