<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $event_title = $_POST['event_title'];
    $event_type  = $_POST['event_type'];

    // DATE: MM-DD-YYYY → YYYY-MM-DD
    $event_date = $_POST['event_date'];
    $dateObj = DateTime::createFromFormat('m-d-Y', $event_date);

    if ($dateObj) {
        $event_date = $dateObj->format('Y-m-d');
    } else {
        die("Invalid date format");
    }

    // =========================
    // FIX TIME FORMAT (IMPORTANT)
    // =========================
    $start_time = date("H:i:s", strtotime($_POST['start_time']));
    $end_time   = date("H:i:s", strtotime($_POST['end_time']));

    $venue    = $_POST['venue'];
    $person_in_charge = $_POST['persons'] ?? '[]';
    
    // PROJECT CHECKBOX
    $is_project = isset($_POST['is_project']) ? 1 : 0;

    // =========================
    // CHECKLIST
    // =========================
    $checklist = json_decode($_POST['checklist'] ?? '[]', true);
    if (!is_array($checklist)) {
        $checklist = [];
    }

    // =========================
    // STATUS LOGIC (FIXED PROPERLY)
    // =========================
    function computeStatus($checklist, $eventDate, $endTime) {

        $doneCount = 0;

        foreach ($checklist as $item) {
            if (is_array($item) && !empty($item['done'])) {
                $doneCount++;
            }
        }

        // combine properly (NOW SAFE because time is 24-hour format)
        $eventTimestamp = strtotime($eventDate . ' ' . $endTime);
        $now = time();

        $eventHasPassed = ($eventTimestamp <= $now);

        // RULES:
        // 1. If event already ended → completed
        if ($eventHasPassed) {
            return "completed";
        }

        // 2. If some checklist done → preparing
        if ($doneCount > 0) {
            return "preparing";
        }

        // 3. Otherwise
        return "pending";
    }

    $status = computeStatus($checklist, $event_date, $end_time);

    $checklist_json = json_encode($checklist);

    try {
        $sql = "INSERT INTO tbl_tasks 
            (event_title, event_type, event_date, start_time, end_time, venue, person_in_charge, status, checklist, is_project)
            VALUES
            (:event_title, :event_type, :event_date, :start_time, :end_time, :venue, :person_in_charge, :status, :checklist, :is_project)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':event_title' => $event_title,
            ':event_type'  => $event_type,
            ':event_date'  => $event_date,
            ':start_time'  => $start_time,
            ':end_time'    => $end_time,
            ':venue'       => $venue,
            ':person_in_charge' => $person_in_charge,
            ':status'      => $status,
            ':checklist'   => $checklist_json,
            ':is_project'  => $is_project
        ]);

        header("Location: managetask.php?success=1");
        exit();

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>