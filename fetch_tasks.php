<?php
require 'db.php';

date_default_timezone_set('Asia/Manila');

$status = $_GET['status'] ?? 'pending';
$page = max(1, (int)($_GET['page'] ?? 1));

$limit = 10;
$offset = ($page - 1) * $limit;

// CURRENT MONTH + YEAR
$currentMonth = date('m');
$currentYear  = date('Y');


/* =========================
   FUNCTION: PERSON IN CHARGE
========================= */
function getPersonInCharge($pdo, $jsonIds) {

    $ids = json_decode($jsonIds, true);

    if (!is_array($ids) || empty($ids)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, profile_pic
        FROM tbl_users
        WHERE id IN ($placeholders)
    ");

    $stmt->execute($ids);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/* =========================
   FUNCTION: CHECKLIST FIX (ADDED)
========================= */
function getChecklist($jsonChecklist) {

    if (!$jsonChecklist) return [];

    $data = json_decode($jsonChecklist, true);

    if (!is_array($data)) return [];

    return $data;
}


/* =========================
   COMPLETED = CURRENT MONTH ONLY
========================= */

if ($status === 'completed') {

    $stmt = $pdo->prepare("
        SELECT * 
        FROM tbl_tasks
        WHERE status = ?
        AND soft_delete = 0
        AND MONTH(event_date) = ?
        AND YEAR(event_date) = ?
        ORDER BY event_date ASC
        LIMIT $limit OFFSET $offset
    ");

    $stmt->execute([
        $status,
        $currentMonth,
        $currentYear
    ]);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as &$row) {

        // PERSON IN CHARGE
        $row['pic_data'] = getPersonInCharge($pdo, $row['person_in_charge'] ?? '[]');

        // ✅ CHECKLIST ADDED HERE
        $row['checklist'] = getChecklist($row['checklist'] ?? '[]');
    }

    $countStmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM tbl_tasks
        WHERE status = ?
        AND soft_delete = 0
        AND MONTH(event_date) = ?
        AND YEAR(event_date) = ?
    ");

    $countStmt->execute([
        $status,
        $currentMonth,
        $currentYear
    ]);

} else {

    $stmt = $pdo->prepare("
        SELECT * 
        FROM tbl_tasks
        WHERE status = ?
        AND soft_delete = 0
        ORDER BY event_date ASC
        LIMIT $limit OFFSET $offset
    ");

    $stmt->execute([$status]);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as &$row) {

        // PERSON IN CHARGE
        $row['pic_data'] = getPersonInCharge($pdo, $row['person_in_charge'] ?? '[]');

        // ✅ CHECKLIST ADDED HERE
        $row['checklist'] = getChecklist($row['checklist'] ?? '[]');
    }

    $countStmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM tbl_tasks
        WHERE status = ?
    ");

    $countStmt->execute([$status]);
}

$total = $countStmt->fetchColumn();

echo json_encode([
    "data" => $data,
    "totalPages" => ceil($total / $limit)
]);