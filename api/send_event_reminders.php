<?php

require __DIR__ . '/db.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Manila');

$gmail = "ticktackreminder@gmail.com";
$appPassword = "lfap vjir rocf kahn";

$now = new DateTime();
$currentTimestamp = $now->getTimestamp();

/*
|--------------------------------------------------------------------------
| CRON WINDOW
|--------------------------------------------------------------------------
| If cron runs every 5 minutes,
| reminder will trigger once within 5 minutes.
*/

$window = 300; // 5 minutes

/*
|--------------------------------------------------------------------------
| GET ACTIVE EVENTS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        event_title,
        event_date,
        start_time,
        end_time,
        event_type,
        venue,
        location,
        person_in_charge
    FROM tbl_tasks
    WHERE soft_delete = 0
      AND status != 'Completed'
");

$stmt->execute();

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| PROCESS EVENTS
|--------------------------------------------------------------------------
*/

foreach ($events as $event) {

    if (empty($event['person_in_charge'])) {
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD EVENT DATETIME
    |--------------------------------------------------------------------------
    */

    try {

        $eventDateTime = new DateTime(
            trim($event['event_date']) . ' ' . trim($event['start_time'])
        );

    } catch (Exception $e) {

        error_log(
            "Invalid event datetime for task ID {$event['id']}"
        );

        continue;
    }

    $eventTimestamp = $eventDateTime->getTimestamp();

    /*
    |--------------------------------------------------------------------------
    | SKIP PAST EVENTS
    |--------------------------------------------------------------------------
    */

    if ($eventTimestamp <= $currentTimestamp) {
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | REMINDER TIMES
    |--------------------------------------------------------------------------
    */

    $threeDaysBefore = $eventTimestamp - (3 * 24 * 60 * 60);
    $oneDayBefore    = $eventTimestamp - (1 * 24 * 60 * 60);
    $twoHoursBefore  = $eventTimestamp - (2 * 60 * 60);

    $reminderType = null;

    /*
    |--------------------------------------------------------------------------
    | DETERMINE REMINDER TYPE
    |--------------------------------------------------------------------------
    */

    if (
        $currentTimestamp >= $threeDaysBefore &&
        $currentTimestamp <= ($threeDaysBefore + $window)
    ) {

        $reminderType = '3_days';

    } elseif (

        $currentTimestamp >= $oneDayBefore &&
        $currentTimestamp <= ($oneDayBefore + $window)

    ) {

        $reminderType = '1_day';

    } elseif (

        $currentTimestamp >= $twoHoursBefore &&
        $currentTimestamp <= ($twoHoursBefore + $window)

    ) {

        $reminderType = '2_hours';
    }

    if (!$reminderType) {
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | GET USERS
    |--------------------------------------------------------------------------
    */

    $userIds = json_decode($event['person_in_charge'], true);

    if (!is_array($userIds) || empty($userIds)) {
        continue;
    }

    $userIds = array_map('intval', $userIds);

    $placeholders = implode(
        ',',
        array_fill(0, count($userIds), '?')
    );

    $userStmt = $pdo->prepare("
        SELECT
            id,
            email,
            first_name,
            last_name
        FROM tbl_users
        WHERE id IN ($placeholders)
    ");

    $userStmt->execute($userIds);

    $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$users) {
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | REMINDER TABLE CHECK
    |--------------------------------------------------------------------------
    */

    $check = $pdo->prepare("
        SELECT id
        FROM tbl_event_reminders
        WHERE task_id = ?
          AND user_id = ?
          AND reminder_type = ?
        LIMIT 1
    ");

    $insert = $pdo->prepare("
        INSERT INTO tbl_event_reminders
        (
            task_id,
            user_id,
            reminder_type,
            channel
        )
        VALUES
        (
            ?,
            ?,
            ?,
            'email'
        )
    ");

    /*
    |--------------------------------------------------------------------------
    | SEND EMAIL
    |--------------------------------------------------------------------------
    */

    foreach ($users as $user) {

        $check->execute([
            $event['id'],
            $user['id'],
            $reminderType
        ]);

        if ($check->fetchColumn()) {
            continue;
        }

        try {

            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $gmail;
            $mail->Password = $appPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->CharSet = 'UTF-8';

            $mail->setFrom(
                $gmail,
                'TickTack Event Management System'
            );

            $mail->addAddress(
                $user['email'],
                $user['first_name'] . ' ' . $user['last_name']
            );

            $dateFormatted = date(
                'F d, Y',
                strtotime($event['event_date'])
            );

            $startTimeFormatted = date(
                'g:i A',
                strtotime($event['start_time'])
            );

            $endTimeFormatted = date(
                'g:i A',
                strtotime($event['end_time'])
            );

            $mail->isHTML(true);

            $mail->Subject =
                "[{$reminderType}] Reminder - {$event['event_title']}";

            $mail->Body = "
            <div style='font-family:Arial,sans-serif;background:#f4f6f9;padding:20px;'>

                <div style='max-width:600px;margin:auto;background:#fff;border-radius:10px;overflow:hidden;'>

                    <div style='background:#0d6efd;color:#fff;padding:20px;text-align:center;'>
                        <h2 style='margin:0;'>Event Reminder</h2>
                    </div>

                    <div style='padding:25px;'>

                        <p>Hello <b>{$user['first_name']}</b>,</p>

                        <p>
                            This is your <b>{$reminderType}</b>
                            reminder for an upcoming event.
                        </p>

                        <div style='background:#f8f9fa;padding:15px;border-radius:8px;'>

                            <p><b>Event:</b> {$event['event_title']}</p>
                            <p><b>Date:</b> {$dateFormatted}</p>
                            <p><b>Start:</b> {$startTimeFormatted}</p>
                            <p><b>End:</b> {$endTimeFormatted}</p>
                            <p><b>Venue:</b> {$event['venue']}</p>

                        </div>

                    </div>

                    <div style='background:#f1f1f1;padding:12px;text-align:center;font-size:12px;'>
                        © " . date('Y') . " TickTack Event Management System
                    </div>

                </div>

            </div>
            ";

            $mail->send();

            $insert->execute([
                $event['id'],
                $user['id'],
                $reminderType
            ]);

            echo "Email sent to {$user['email']} ({$reminderType})<br>";

        } catch (Exception $e) {

            echo "FAILED: {$user['email']}<br>";
            echo $mail->ErrorInfo . "<br><br>";

            error_log($mail->ErrorInfo);
        }
    }
}

echo "Reminder job completed.";
