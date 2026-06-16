<?php

require 'db.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Manila');

$gmail = "ticktackreminder@gmail.com";
$appPassword = "lfap vjir rocf kahn";

/*
|--------------------------------------------------------------------------
| DEBUG MODE (TURN ON WHEN TESTING)
|--------------------------------------------------------------------------
*/
$debug = true;

/*
|--------------------------------------------------------------------------
| Fetch active events
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM tbl_tasks
    WHERE soft_delete = 0
      AND status != 'Completed'
");

$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$now = new DateTime();

/*
|--------------------------------------------------------------------------
| LOOP EVENTS
|--------------------------------------------------------------------------
*/

foreach ($events as $event) {

    $eventDateTime = new DateTime($event['event_date'] . ' ' . $event['start_time']);

    $secondsRemaining = $eventDateTime->getTimestamp() - $now->getTimestamp();

    if ($debug) {
        echo "<hr>";
        echo "Event ID: {$event['id']}<br>";
        echo "Title: {$event['event_title']}<br>";
        echo "Event Time: " . $eventDateTime->format('Y-m-d H:i:s') . "<br>";
        echo "Now: " . $now->format('Y-m-d H:i:s') . "<br>";
        echo "Seconds Remaining: {$secondsRemaining}<br>";
    }

    if ($secondsRemaining <= 0) {
        if ($debug) echo "SKIPPED (event already started/finished)<br>";
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | FIXED REMINDER LOGIC (ACCURATE RANGE BASED)
    |--------------------------------------------------------------------------
    */

    if ($secondsRemaining <= 3 * 86400 && $secondsRemaining > 2 * 86400) {
        $reminderType = '3_days';
    }
    elseif ($secondsRemaining <= 1 * 86400 && $secondsRemaining > 2 * 3600) {
        $reminderType = '1_day';
    }
    elseif ($secondsRemaining <= 7200 && $secondsRemaining > 0) {
        $reminderType = '2_hours';
    }
    else {
        if ($debug) echo "NO REMINDER MATCH<br>";
        continue;
    }

    if ($debug) {
        echo "TRIGGERED: {$reminderType}<br>";
    }

    /*
    |--------------------------------------------------------------------------
    | USERS CHECK
    |--------------------------------------------------------------------------
    */

    if (empty($event['person_in_charge'])) {
        if ($debug) echo "NO USERS<br>";
        continue;
    }

    $userIds = json_decode($event['person_in_charge'], true);

    if (!is_array($userIds)) {
        if ($debug) echo "INVALID USER JSON<br>";
        continue;
    }

    $userIds = array_map('trim', $userIds);

    if (empty($userIds)) {
        continue;
    }

    $placeholders = implode(',', array_fill(0, count($userIds), '?'));

    $userStmt = $pdo->prepare("
        SELECT id, email, first_name, last_name
        FROM tbl_users
        WHERE id IN ($placeholders)
    ");

    $userStmt->execute($userIds);
    $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$users) {
        if ($debug) echo "NO USERS FOUND IN DB<br>";
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | LOG + EMAIL PREP
    |--------------------------------------------------------------------------
    */

    $check = $pdo->prepare("
        SELECT id
        FROM tbl_event_reminders
        WHERE task_id = ?
          AND user_id = ?
          AND reminder_type = ?
    ");

    $insert = $pdo->prepare("
        INSERT INTO tbl_event_reminders
        (task_id, user_id, reminder_type)
        VALUES (?, ?, ?)
    ");

    /*
    |--------------------------------------------------------------------------
    | SEND EMAILS
    |--------------------------------------------------------------------------
    */

    foreach ($users as $user) {

        $check->execute([
            $event['id'],
            $user['id'],
            $reminderType
        ]);

        if ($check->fetch()) {
            if ($debug) echo "ALREADY SENT to {$user['email']}<br>";
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

            $mail->setFrom($gmail, 'Event Management System');

            $mail->addAddress(
                $user['email'],
                $user['first_name'] . ' ' . $user['last_name']
            );

            $mail->isHTML(true);
            $mail->Subject = "[Reminder] " . $event['event_title'];

            $startTimeFormatted = date("g:i A", strtotime($event['start_time']));
            $endTimeFormatted   = date("g:i A", strtotime($event['end_time']));
            $dateFormatted = date("F d, Y", strtotime($event['event_date']));

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; background:#f4f6f9; padding:20px;'>

                <div style='max-width:600px; margin:auto; background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.08);'>

                    <!-- HEADER -->
                    <div style='background:#0d6efd; color:#fff; padding:20px; text-align:center;'>
                        <h2 style='margin:0;'>Event Reminder</h2>
                        <p style='margin:5px 0 0; font-size:14px;'>TickTack Notification System</p>
                    </div>

                    <!-- BODY -->
                    <div style='padding:25px;'>

                        <p style='font-size:16px; margin-bottom:10px;'>
                            Hello <b>{$user['first_name']}</b>,
                        </p>

                        <p style='color:#555; font-size:14px; line-height:1.6;'>
                            This is a friendly reminder about your upcoming scheduled event. Please review the details below and prepare accordingly.
                        </p>


                        <!-- EVENT CARD -->
                        <div style='background:#f8f9fa; padding:15px; border-radius:8px; border:1px solid #e0e0e0;'>

                            <p style='margin:5px 0;'><b>📌 Event:</b> {$event['event_title']}</p>
                            <p style='margin:5px 0;'><b>📅 Date:</b> {$dateFormatted}</p>
                            <p><b>⏰ Start Time:</b> {$startTimeFormatted}</p>
                            <p><b>⏳ End Time:</b> {$endTimeFormatted}</p>
                            <p style='margin:5px 0;'><b>📍 Venue:</b> {$event['venue']}</p>
                        </div>

                        <p style='margin-top:20px; font-size:13px; color:#777; line-height:1.5;'>
                            Please make sure to arrive on time and complete any required preparations.
                        </p>

                    </div>

                    <!-- FOOTER -->
                    <div style='background:#f1f1f1; text-align:center; padding:12px; font-size:12px; color:#777;'>
                        © " . date('Y') . " TickTack Event Management System • Automated Reminder
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

            echo "SENT ✔ {$reminderType} → {$user['email']} (Event {$event['id']})<br>";

        } catch (Exception $e) {

            echo "FAILED ✖ {$user['email']} : {$mail->ErrorInfo}<br>";
        }
    }
}