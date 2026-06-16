<?php
require 'db.php';

date_default_timezone_set('Asia/Manila');

$now = new DateTime();

$stmt = $pdo->prepare("
    SELECT
        id,
        event_title,
        event_date,
        start_time,
        end_time,
        venue
    FROM tbl_tasks
    WHERE soft_delete = 0
      AND status != 'Completed'
");

$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$alerts = [];

foreach ($events as $event) {

    $eventTime = new DateTime(
        $event['event_date'] . ' ' . $event['start_time']
    );

    $diff = $eventTime->getTimestamp() - $now->getTimestamp();

    $dateFormatted = date(
        "F d, Y",
        strtotime($event['event_date'])
    );

    $startTime = date(
        "g:i A",
        strtotime($event['start_time'])
    );

    $endTime = date(
        "g:i A",
        strtotime($event['end_time'])
    );

    /*
    |--------------------------------------------------------------------------
    | 3 DAYS BEFORE
    |--------------------------------------------------------------------------
    */
    if ($diff > 172800 && $diff <= 259200) {

        $check = $pdo->prepare("
            SELECT id
            FROM tbl_event_reminders
            WHERE task_id = ?
              AND reminder_type = '3_days'
              AND channel = 'browser'
            LIMIT 1
        ");

        $check->execute([$event['id']]);

        if (!$check->fetch()) {

            $alerts[] = [
                "title" => "📅 Event in 3 Days",
                "body" =>
                    "📌 {$event['event_title']}\n" .
                    "📅 Date: {$dateFormatted}\n" .
                    "⏰ Time: {$startTime} - {$endTime}\n" .
                    "📍 Venue: {$event['venue']}"
            ];

            $insert = $pdo->prepare("
                INSERT INTO tbl_event_reminders
                (
                    task_id,
                    user_id,
                    reminder_type,
                    sent_at,
                    channel
                )
                VALUES (?, 0, '3_days', NOW(), 'browser')
            ");

            $insert->execute([$event['id']]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 1 DAY BEFORE
    |--------------------------------------------------------------------------
    */
    // 1 day reminder
    if ($diff > 7200 && $diff <= 86400) {

        $check = $pdo->prepare("
            SELECT id
            FROM tbl_event_reminders
            WHERE task_id = ?
              AND reminder_type = '1_day'
              AND channel = 'browser'
            LIMIT 1
        ");

        $check->execute([$event['id']]);

        if (!$check->fetch()) {

            $alerts[] = [
                "title" => "🔔 Event Tomorrow",
                "body" =>
                    "📌 {$event['event_title']}\n" .
                    "📅 Date: {$dateFormatted}\n" .
                    "⏰ Time: {$startTime} - {$endTime}\n" .
                    "📍 Venue: {$event['venue']}"
            ];

            $insert = $pdo->prepare("
                INSERT INTO tbl_event_reminders
                (
                    task_id,
                    user_id,
                    reminder_type,
                    sent_at,
                    channel
                )
                VALUES (?, 0, '1_day', NOW(), 'browser')
            ");

            $insert->execute([$event['id']]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 2 HOURS BEFORE
    |--------------------------------------------------------------------------
    */
    if ($diff > 0 && $diff <= 7200) {

        $check = $pdo->prepare("
            SELECT id
            FROM tbl_event_reminders
            WHERE task_id = ?
              AND reminder_type = '2_hours'
              AND channel = 'browser'
            LIMIT 1
        ");

        $check->execute([$event['id']]);

        if (!$check->fetch()) {

            $alerts[] = [
                "title" => "⏰ Event Starts in 2 Hours",
                "body" =>
                    "📌 {$event['event_title']}\n" .
                    "📅 Date: {$dateFormatted}\n" .
                    "⏰ Time: {$startTime} - {$endTime}\n" .
                    "📍 Venue: {$event['venue']}"
            ];

            $insert = $pdo->prepare("
                INSERT INTO tbl_event_reminders
                (
                    task_id,
                    user_id,
                    reminder_type,
                    sent_at,
                    channel
                )
                VALUES (?, 0, '2_hours', NOW(), 'browser')
            ");

            $insert->execute([$event['id']]);
        }
    }
}

header('Content-Type: application/json');
echo json_encode($alerts);