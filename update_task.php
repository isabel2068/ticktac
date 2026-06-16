<?php
require 'db.php';

$id = $_POST['id'];

// safe date parsing
$date = DateTime::createFromFormat('m-d-Y', $_POST['event_date']);

$formattedDate = $date ? $date->format('Y-m-d') : null;

$stmt = $pdo->prepare("
    UPDATE tbl_tasks 
    SET event_title=?,
        event_type=?,
        venue=?,
        event_date=?,
        start_time=?,
        end_time=?,
        updated_at=NOW()
    WHERE id=?
");

$stmt->execute([
    $_POST['event_title'],
    $_POST['event_type'],
    $_POST['venue'],
    $formattedDate,  
    $_POST['start_time'],
    $_POST['end_time'],
    $id
]);

echo "success";