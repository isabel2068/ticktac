<?php
session_start();
require 'db.php';

date_default_timezone_set('Asia/Manila');

// Logged-in user
$userId = $_SESSION['user_id'];

// Today
$today = new DateTime('now', new DateTimeZone('Asia/Manila'));

// Monday of current week
$start = clone $today;
$start->setISODate((int)$today->format('o'), (int)$today->format('W'), 1);

// Sunday
$end = clone $start;
$end->modify('+6 days');

$startDate = $start->format('Y-m-d');
$endDate   = $end->format('Y-m-d');

$stmt = $pdo->prepare("
    SELECT * 
    FROM tbl_tasks 
    WHERE event_date BETWEEN :start AND :end
    AND soft_delete = 0
    ORDER BY event_date ASC, start_time ASC
");

$stmt->execute([
    ':start' => $startDate,
    ':end' => $endDate
]);

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add permission checker
foreach ($tasks as &$task) {

$assignedUsers = json_decode($task['person_in_charge'], true) ?? [];

// remove spaces + convert to integers
$assignedUsers = array_map(function($id) {
    return (int) trim($id);
}, $assignedUsers);

$task['can_edit'] = in_array((int)$userId, $assignedUsers, true);

}


echo json_encode($tasks);
?>