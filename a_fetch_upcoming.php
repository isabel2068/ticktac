<?php
require 'db.php';

date_default_timezone_set('Asia/Manila');

$limit = 2;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// TOTAL
$totalStmt = $pdo->query("
    SELECT COUNT(*) 
    FROM tbl_tasks
    WHERE event_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
");
$totalEvents = $totalStmt->fetchColumn();
$totalPages = ceil($totalEvents / $limit);

// FETCH
$stmt = $pdo->prepare("
    SELECT event_title, event_date, start_time, end_time, venue 
    FROM tbl_tasks
    WHERE event_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY event_date ASC, start_time ASC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// BUILD HTML
$html = "";

if ($events) {
    foreach ($events as $event) {
        $html .= "
        <div class='d-flex align-items-start mb-3 cards glass'>
            <i class='bi bi-circle-fill text-primary me-2' style='font-size: 0.5rem; margin-top: 0.5rem;'></i>
            <h6 class='card-title mb-0' style='line-height: 1.8; font-size:13.5px;'>
                {$event['event_title']}" . "<br> ". " • ".
                date("F d, Y", strtotime($event['event_date'])) . " • ".
                date("g:i A", strtotime($event['start_time'])) . " - " .
                date("g:i A", strtotime($event['end_time']))  . " | ".
                $event['venue'] .
            "</h6>
        </div>";
    }
} else {
    $html = "<p class='cards text-muted text-center kulayred' style='margin-top:5rem;'><i class='bi bi-exclamation-circle-fill me-1'></i>No upcoming events this week.</p>";
}

// RETURN JSON
echo json_encode([
    "html" => $html,
    "page" => $page,
    "totalPages" => $totalPages
]);