<?php
require 'db.php';

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$status = $_GET['status'] ?? 'pending';

// TOTAL
$count = $pdo->prepare("
    SELECT COUNT(*)
    FROM tbl_tasks
    WHERE is_project = 1
    AND project_status = :status
    AND soft_delete = 0
");

$count->execute([
    ':status' => $status
]);

$total = $count->fetchColumn();

// DATA
$stmt = $pdo->prepare("
    SELECT id, event_title, event_date, project_status
    FROM tbl_tasks
    WHERE is_project = 1
    AND project_status = :status
    AND soft_delete = 0
    ORDER BY event_date DESC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':status', $status, PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "data" => $data,
    "total" => $total,
    "page" => $page,
    "limit" => $limit
]);