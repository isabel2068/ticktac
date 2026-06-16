<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: loginv2.php");
    exit();
}

require 'db.php';


// FETCH LOGGED-IN USER
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT first_name, last_name, profile_pic
    FROM tbl_users
    WHERE id = :id
");

$stmt->execute(['id' => $userId]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// DEFAULT VALUES
$fullName = $user['first_name'] . ' ' . $user['last_name'];

$profileImage = !empty($user['profile_pic'])
    ? 'uploads/' . $user['profile_pic']
    : 'default_profile/user.png';

updateCompletedTasks($pdo);

function updateCompletedTasks($pdo) {
    date_default_timezone_set('Asia/Manila');

    $now = date("Y-m-d H:i:s");

    $sql = "
        UPDATE tbl_tasks
        SET status = 'completed'
        WHERE CONCAT(event_date, ' ', end_time) <= :now
        AND status != 'completed'
        AND soft_delete = 0
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':now' => $now]);
}

function getTasksByStatus($pdo, $status) {
    $stmt = $pdo->prepare("SELECT * FROM tbl_tasks WHERE status = ? AND soft_delete = 0 ORDER BY event_date ASC");
    $stmt->execute([$status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUsers($pdo) {
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, profile_pic 
        FROM tbl_users 
        ORDER BY first_name ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$users = getUsers($pdo);

?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tasks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="ticktac.css">
    <link rel="stylesheet" href="alert.css">
</head>
<body>
<style>
.table {
    border-collapse: separate;
    border-spacing: 0 15px;

    table-layout: fixed;
    width: 100%;
}

.table tbody tr {
    background-color: #fff;
    transition: all 0.2s ease-in-out;
}

.table tbody td {
    border: none;
    padding: 15px;
    vertical-align: middle;
}

.table tbody tr td:first-child {
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}

.table tbody tr td:last-child {
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
}

body {
    font-family: 'Poppins', sans-serif;
    font-weight: 400;
}

h1, h2, h3, h4, h5 {
    font-weight: 500;
}

small, .text-muted {
    font-weight: 400;
}

/* STRIPED ROW (PER ROW, NOT COLUMN) */
.table tbody tr:nth-child(even) {
    background: #f8fafc;
}

/* HOVER EFFECT */
.table tbody tr:hover {
    background: #e0f2fe !important;
}

/* CENTER ALIGN HELPER */
.mid {
    text-align: center;
}

/* ACTION BUTTON CLEAN LOOK */
.table .btn {
    border-radius: 8px;
    padding: 4px 8px;
}

.page-indicator {
    font-size: 16px;
    font-weight: 500;
    background: #f1f5f9;
    color: #475569;
}

.table td.truncate {
    max-width: 180px;      /* adjust width as needed */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Example fixed widths per column */
.col-no { width: 60px; }
.col-event { width: 220px; }
.col-date { width: 120px; }
.col-time { width: 160px; }
.col-venue { width: 200px; }
.col-actions { width: 140px; }

/* REMOVE FLATPICKR YEAR INPUT ARROWS */
.flatpickr-current-month input.cur-year {
    appearance: textfield;
    -moz-appearance: textfield;
}

.flatpickr-current-month input.cur-year::-webkit-outer-spin-button,
.flatpickr-current-month input.cur-year::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* REMOVE FLATPICKR CUSTOM ARROWS */
.flatpickr-current-month .numInputWrapper span.arrowUp,
.flatpickr-current-month .numInputWrapper span.arrowDown {
    display: none !important;
}

/* REMOVE FLATPICKR CALENDAR ARROW ICON */
.flatpickr-input[readonly] {
    background-image: none !important;
}

/* REMOVE DEFAULT INPUT ARROWS / ICONS */
input[type="date"]::-webkit-calendar-picker-indicator {
    display: none;
    -webkit-appearance: none;
}

/* REMOVE EXTRA FLATPICKR BUTTONS */
.flatpickr-calendar .flatpickr-prev-month,
.flatpickr-calendar .flatpickr-next-month {
    user-select: none;
}

/* REMOVE YEAR INPUT SPINNERS */
.flatpickr-current-month .numInputWrapper input {
    appearance: textfield !important;
    -moz-appearance: textfield !important;
}

.flatpickr-current-month .numInputWrapper input::-webkit-outer-spin-button,
.flatpickr-current-month .numInputWrapper input::-webkit-inner-spin-button {
    -webkit-appearance: none !important;
    margin: 0 !important;
}

/* REMOVE FLATPICKR CUSTOM UP/DOWN ARROWS */
.flatpickr-current-month .numInputWrapper span {
    display: none !important;
}

/* OPTIONAL: REMOVE HOVER AREA TOO */
.flatpickr-current-month .numInputWrapper:hover span {
    display: none !important;
}

.flatpickr-current-month .numInputWrapper {
    width: auto !important;
}

/* REMOVE SMALL ARROW / TRIANGLE ON SELECTED DATE */
.flatpickr-day.selected::before,
.flatpickr-day.startRange::before,
.flatpickr-day.endRange::before,
.flatpickr-day.selected:hover::before,
.flatpickr-day.startRange:hover::before,
.flatpickr-day.endRange:hover::before {
    display: none !important;
    content: none !important;
}

/* FORCE REMOVE ALL TRIANGLE INDICATORS */
.flatpickr-day.selected,
.flatpickr-day.startRange,
.flatpickr-day.endRange {
    background-image: none !important;
}

.flatpickr-day.selected::after,
.flatpickr-day.startRange::after,
.flatpickr-day.endRange::after {
    display: none !important;
    content: none !important;
}
/* =========================
   PROJECT TOGGLE SWITCH
========================= */

.project-toggle-wrapper{
    display:flex;
    align-items:center;
    gap:12px;
    margin-top:10px;
}

.toggle-switch{
    position:relative;
    width:52px;
    height:28px;
    display:inline-block;
}

.toggle-switch input{
    opacity:0;
    width:0;
    height:0;
}

.slider{
    position:absolute;
    cursor:pointer;
    inset:0;
    background:#d1d5db;
    transition:.3s ease;
    border-radius:50px;
}

.slider::before{
    content:"";
    position:absolute;
    height:22px;
    width:22px;
    left:3px;
    top:3px;
    background:white;
    transition:.3s ease;
    border-radius:50%;
    box-shadow:0 2px 6px rgba(0,0,0,0.2);
}

/* ACTIVE */
.toggle-switch input:checked + .slider{
    background:#0d6efd;
}

.toggle-switch input:checked + .slider::before{
    transform:translateX(24px);
}

/* LABEL */
.toggle-label{
    font-weight:600;
    color:#334155;
    cursor:pointer;
    user-select:none;
}

</style>

<nav class="navbar navbar-expand-lg border-body" data-bs-theme="light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><img src="finallogo.png" class="profile-pic me-1" alt="Profile" style="width:3rem; height: 3rem;"><b>TICKTAC</b></a>
        <ul class="navbar-nav">
            <li class="nav-item">
               <a class="d-flex align-items-center btn-door-animation" href="#" id="logoutBtn">
                    <div class="icon-wrapper me-2">
                        <i class="bi bi-door-closed-fill icon-closed fs-3"></i>
                        <i class="bi bi-door-open-fill icon-open fs-3" style="display: none;"></i>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse px-0 sticky-top" style="height: calc(100vh - 56px); top: 56px;">
            
            <div class="d-flex flex-column h-100" style="padding-top: 5.5px;">
                
                <div class="pt-3">
                    <p class="d-flex justify-content-between align-items-center px-3 mb-1 overview">
                        Overview
                    </p>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php"><i class="bi bi-pie-chart me-2"></i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="calendar.php"><i class="bi bi-calendar3 me-2"></i>Calendar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#"><i class="bi bi-list-check me-2"></i>Events</a>
                        </li>
                    </ul>

                    <p class="d-flex justify-content-between align-items-center mt-3 px-3 mb-1 overview">
                        History
                    </p>              
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link" href="year_summary.php"><i class="bi bi-calendar2-minus me-2"></i> Records</a>
                        </li>
                    </ul>
                </div>

                <div class="profile-section p-3 mt-auto">
                    <div class="d-flex align-items-center gap-2">

                        <img src="<?= htmlspecialchars($profileImage); ?>" 
                            alt="Profile"
                            width="40"
                            height="40"
                            style="object-fit: cover; border-radius: 50%;">

                        <div class="overflow-hidden">
                            <p class="mb-0 fw-bold text-dark text-truncate" style="font-size: 0.85rem;">
                                <?= htmlspecialchars($fullName); ?>
                            </p>

                            <small class="text-muted d-block text-truncate" style="font-size: 0.7rem;">
                                DTI PERSONNEL
                            </small>
                        </div>

                    </div>
                </div>

            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center p-cus mb-1">
                <h3 class="mb-0">Manage Events</h3>

                <!-- ADD TASK BUTTON -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    <i class="bi bi-plus-circle"></i> Task
                </button>
            </div>

                <!-- TABS + PAGINATION HEADER -->
                <div class="d-flex justify-content-between align-items-center border-bottom">

                    <!-- TABS (LEFT SIDE) -->
                    <ul class="nav nav-tabs tabletabs mb-3" id="taskTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-links tablee task active link d-flex align-items-center gap-1 btn btn-sm"
                                data-bs-toggle="tab" data-bs-target="#pending" type="button">
                                <ion-icon name="hourglass-outline"></ion-icon> Pending
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-links tablee task link d-flex align-items-center gap-1 btn btn-sm"
                                data-bs-toggle="tab" data-bs-target="#completed" type="button">
                                <ion-icon name="checkmark-circle-outline"></ion-icon> Completed
                            </button>
                        </li>
                    </ul>

                    <!-- PAGINATION (RIGHT SIDE) -->
                    <div class="modern-pagination d-flex align-items-center gap-2 ms-auto">

                    <button id="prevBtn"
                        class="btn btn-sm btn-light shadow-sm"
                        onclick="changePage(-1)">
                        <i class="bi bi-chevron-left"></i>
                    </button>

                    <span class="page-indicator px-3 py-1 rounded-pill" id="pageInfo">
                        Page 1
                    </span>

                    <button id="paginationNextBtn"
                        class="btn btn-sm btn-light shadow-sm"
                        onclick="changePage(1)">
                        <i class="bi bi-chevron-right"></i>
                    </button>

                    </div>

                </div>

                <!-- TAB CONTENT -->
                <div class="tab-content bg-white border-top-0">

                    <!-- PENDING -->
                    <div class="tab-pane fade show active" id="pending">
                        <div class="table-responsive">
                        <table class="table table-borderless table-hover align-middle">
                        <thead class="table-color">
                            <tr>
                                <th class="mid col-no">No.</th>
                                <th>Event</th>
                                <th class="mid">Date</th>
                                <th class="mid">Time</th>
                                <th class="mid">Venue</th>
                                <th class="mid">Actions</th>
                            </tr>
                        </thead>
                                <tbody class="gap-2" id="pendingBody">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- COMPLETED -->
                    <div class="tab-pane fade" id="completed">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                    <th class="mid col-no">No.</th>
                                    <th>Event</th>
                                    <th class="mid">Date</th>
                                    <th class="mid">Time</th>
                                    <th class="mid">Venue</th>
                                    <th class="mid">Actions</th>
                                    </tr>
                                </thead>
                                <tbody  id="completedBody">
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
        </main>


                    <!-- ADD TASK MODAL -->
                    <div class="modal fade" id="addTaskModal" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 shadow-sm rounded-4">

                                <!-- HEADER -->
                                <div class="modal-header border-0 pb-2 p-4">
                                    <div class="w-100">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5 class="fw-semibold mb-1">Create Event</h5>
                                            </div>

                                            <div class="col-md-6 d-flex justify-content-end">
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>                        
                                        </div>
                                        <div class="row">
                                            <small class="text-muted">Step <span id="stepLabel">1</span> of 3</small>
                                        </div>
                                        <div class="progress mt-2" style="height: 4px;">
                                            <div class="progress-bar bg-primary" id="stepProgress" style="width: 50%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BODY -->
                                <div class="modal-body pt-2 p-4">
                                    <form method="POST" action="add_task.php" id="taskForm">

                                        <!-- STEP 1 -->
                                        <div id="step1">

                                            <!-- BASIC INFO -->
                                            <div class="mb-3">
                                                <label class="form-label small text-muted">Event Title</label>
                                                <input type="text" name="event_title" class="form-control" placeholder="Enter event title" required>
                                            </div>

                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label small text-muted">Event Type</label>
                                                    <select class="form-select" name="event_type" required>
                                                        <option value="" disabled selected>Select type</option>
                                                        <option>Training</option>
                                                        <option>Seminar</option>
                                                        <option>Meeting</option>
                                                        <option>Workshop</option>
                                                        <option>Other</option>
                                                    </select>
                                                </div>

                                            <!-- VENUE -->
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Venue</label>
                                                <input type="text" name="venue" class="form-control" placeholder="Enter venue" required>
                                            </div>
                                            </div>

                                            <!-- DATE & TIME -->
                                            <div class="row g-2 mt-2">
                                                <div class="col-md-4">
                                                    <label class="form-label small text-muted">Date</label>
                                                    <input type="text" id="event_date" name="event_date"
                                                    class="form-control"
                                                    placeholder="MM-DD-YYYY" required>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label small text-muted">Starts at</label>
                                                    <input type="text" id="start_time" name="start_time" class="form-control" required>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label small text-muted">Ends at</label>
                                                    <input type="text" id="end_time" name="end_time" class="form-control" required>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-3">
                                            <div class="project-toggle-wrapper">
                                                
                                                <label class="toggle-switch">
                                                    <input 
                                                        type="checkbox"
                                                        id="is_project"
                                                        name="is_project"
                                                        value="1">

                                                    <span class="slider"></span>
                                                </label>

                                                <label for="is_project" class="toggle-label mb-0">
                                                    Project
                                                </label>

                                            </div>
                                        </div>

                                        </div>

                                        <div id="step2" style="display:none;">

                                            <!-- DEFAULT CARD -->
                                            <div class="card border-0 shadow-sm rounded-4 mb-3">
                                                <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                
                                                    <h6 class="fw-semibold mb-3">
                                                        <i class="bi bi-check2-square text-primary me-2"></i>Default Tasks
                                                    </h6>

                                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none mb-3" onclick="toggleExtraTasks(this)">
                                                        <i class="bi bi-chevron-down"></i> 
                                                    </button>

                                                </div>

                                                <div class="form-check mb-3">
                                                    <input type="checkbox" class="form-check-input" id="selectAllDefaults" checked>
                                                    <label class="form-check-label fw-semibold" for="selectAllDefaults">
                                                        Select All Default Tasks
                                                    </label>
                                                </div>

                                                    <div id="defaultChecklist"></div>
                                                </div>
                                            </div>                                            

                                            <!-- DYNAMIC CARDS -->
                                            <div id="checklistCards"></div>

                                            <!-- ADD CARD BUTTON -->
                                            <div class="text-center mb-3">
                                                <button type="button" class="btn section-card w-100 py-2 fw-bold rounded-3 mt-2" onclick="addChecklistCard()">
                                                    <i class="bi bi-plus-circle"></i> Add checklist
                                                </button>
                                            </div>

                                            <input type="hidden" name="checklist" id="checklist">

                                            </div>

                                            <!-- STEP 3 -->
                                            <div id="step3" style="display:none;">

                                            <div class="card border-0 shadow-sm rounded-4 mb-3">
                                                <div class="card-body">

                                                    <h6 class="fw-semibold mb-3">
                                                        <i class="bi bi-people text-primary me-2"></i>
                                                        Assign Person in Charge
                                                    </h6>

                                                    <div id="personChecklist">

                                                    <?php foreach ($users as $user): ?>

                                                        <label class="d-flex align-items-center gap-2 mb-2 p-2 rounded-3 border">

                                                            <input 
                                                                class="form-check-input person-checkbox" 
                                                                type="checkbox" 
                                                                value="<?= $user['id']; ?> ">

                                                                <?php
                                                                $img = !empty($user['profile_pic']) 
                                                                    ? $user['profile_pic'] 
                                                                    : "default_profile/user.png";
                                                                ?>

                                                                <img 
                                                                    src="uploads/<?= $img ?>"
                                                                    style="width:35px;height:35px;border-radius:50%;object-fit:cover;">

                                                            <div class="ms-1">
                                                                <div class="fw-semibold">
                                                                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                                                </div>
                                                            </div>

                                                        </label>

                                                    <?php endforeach; ?>

                                                    </div>

                                                </div>
                                            </div>

                                            <input type="hidden" name="persons" id="persons">

                                            </div>
                                        <!-- FOOTER -->
                                        <div class="d-flex justify-content-between align-items-center mt-4">

                                            <button type="button" class="btn btn-secondary" onclick="prevStep()" id="backBtn" style="display:none;">
                                                Back
                                            </button>

                                            <div class="ms-auto">
                                                <button type="button" class="btn btn-primary px-4" onclick="nextStep()" id="nextBtn">
                                                    Next
                                                </button>

                                                <button type="submit" class="btn btn-success px-4" id="saveBtn" style="display:none;">
                                                    Save
                                                </button>
                                            </div>

                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                  <!-- EVENT VIEW MODAL -->
                <div class="modal fade" id="eventModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                            <div class="modal-header border-0 fs-6 bg-transparent">
                                <div class="d-flex align-items-center size">
                                    <i class="bi bi-calendar-event-fill"></i><h6 class="mb-0 mx-4" id="mTitle"></h6>
                                </div>
                                <button class="btn-close" data-bs-dismiss="modal">                                </button>
                            </div>

                            <!-- EXACT SAME LAYOUT -->
                            <div class="modal-body p-4 pt-0">

                                <div class="row g-3">

                                    <div class="d-flex py-2 bg-light">

                                    <!-- BASIC INFO -->
                                    <div class="col-md-6">
                                        <div class="p-3 py-0 pt-3 h-100">
                                            <div class="border p-3 pb-2 rounded-3 bg-white mb-2">
                                                <h6 class="d-flex size mb-3 text-secondary align-items-center"><i class="bi bi-info-circle-fill" style="font-size: 12px; margin-right: 10px;"></i> Basic Info</h6>
                                                <p class="border-pad my-0"><span>Type : </span><span id="mType"></span></p>
                                                <p class="border-pad my-0"><span>Date : </span> <span id="mDate" style="margin-left: 10px;"></span></p>
                                                <p class="border-pad my-0"><span>Time : </span><span id="mTime" style="margin-left: 10px;"></span></p>
                                                <p class="border-pad my-0"><span>Status : </span><span id="mStatus" style="margin-left: 10px;"></span></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- LOCATION -->
                                    <div class="col-md-6">
                                        <div class="p-3 py-0 pt-3 h-100">
                                            <div class="border p-3 rounded-3 bg-white mb-2">
                                                <h6 class="d-flex size mb-3 text-secondary align-items-center"><i class="bi bi-geo-alt-fill" style="font-size: 12px; margin-right: 10px;"></i>Location</h6>
                                                <p class="border-pad my-0"><span>Venue :</span><span id="mVenue" style="margin-left: 10px;"></span></p>
                                            </div>
                                            <div class="border p-3 pb-0 rounded-3 bg-white mb-2">
                                                <h6 class="d-flex size text-secondary align-items-center mb-0"><i class="bi bi-person-fill" style="font-size: 12px; margin-right: 10px;"></i>Person/s In Charge<h6>
                                                <div id="mPIC" class="d-flex align-items-center gap-2 my-2 flex-wrap"></div>
                                            </div>
                                            <div class="d-flex pt-3 justify-content-end">
                                                <p class="mb-0 text-secondary size"><span>Created At :</span><span id="mCreated" style="margin-left: 10px;"></span></p>
                                            </div>
                                        </div>
                                    </div>

                                    </div>

                                    <!-- CHECKLIST -->
                                    <div class="col-12 px-0">
                                        <div class="p-3 bg-light">
                                            <h6 class="d-flex mb-3 text-secondary align-items-center"><i class="bi bi-clipboard-check-fill" style="font-size: 12px; margin-right: 10px;"></i>Checklist</h6>
                                            <div id="mChecklist"></div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <!-- EDIT TASK MODAL -->
                <div class="modal fade" id="editTaskModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                            <div class="modal-header border-0">
                                <!-- <h5 class="fw-semibold mb-1 ms-2 fs-5 text-secondary">Update event</h5> -->
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body p-4 pt-0">
                                <form id="editTaskForm">

                                    <input type="hidden" id="edit_id" name="id">

                                    <div class="mb-3">
                                        <label>Event Title</label>
                                        <input type="text" id="edit_title" name="event_title" class="form-control">
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label>Event Type</label>
                                            <select id="edit_type" name="event_type" class="form-select">
                                            <option value="Training">Training</option>
                                            <option value="Seminar">Seminar</option>
                                            <option value="Meeting">Meeting</option>
                                            <option value="Workshop">Workshop</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Venue</label>
                                            <input type="text" id="edit_venue" name="venue" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row g-2 mt-2">
                                        <div class="col-md-4">
                                            <label>Date</label>
                                            <input type="text" id="edit_date" name="event_date" class="form-control">
                                        </div>

                                        <div class="col-md-4">
                                            <label>Start</label>
                                            <input type="text" id="edit_start" name="start_time" class="form-control">
                                        </div>

                                        <div class="col-md-4">
                                            <label>End</label>
                                            <input type="text" id="edit_end" name="end_time" class="form-control">
                                        </div>
                                    </div>

                                    <div class="text-end mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            Update
                                        </button>
                                    </div>

                                </form>
                            </div>

                        </div>
                    </div>
                </div>

    </div>
</div>    


                     
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="event_modal.js"></script>
<script src="edit_task.js"></script>
<script>

let step = 1;
let checklistCards = {};
let cardIndex = 0;

let editDatePicker;
let editStartPicker;
let editEndPicker;

let defaultChecklist = [
    "Key Persons or Partners",
    "Resource Speakers",
    "Availability of the Venue",
    "Participants",
    "Supplies",
    "Food/Snacks Coordination with GSO",
    "Certificates for the Speakers",
    "Certificates for the Partners",
    "Certificates for the Participants",
    "CSF for the participants",
    "2 copies of Attendance Sheet"
];

window.totalPages = 1;
/* =========================
   SHOW STEPS
========================= */
function showStep() {
    document.getElementById("step1").style.display = step === 1 ? "block" : "none";
    document.getElementById("step2").style.display = step === 2 ? "block" : "none";
    document.getElementById("step3").style.display = step === 3 ? "block" : "none";

    document.getElementById("backBtn").style.display = step > 1 ? "inline-block" : "none";
    document.getElementById("nextBtn").style.display = step < 3 ? "inline-block" : "none";
    document.getElementById("saveBtn").style.display = step === 3 ? "inline-block" : "none";

    document.getElementById("stepLabel").innerText = step;

    // progress: 33%, 66%, 100%
    let progress = (step / 3) * 100;
    document.getElementById("stepProgress").style.width = progress + "%";

    if (step === 2) {
        renderChecklist();
        renderDefaultChecklist();
    }
}

/* =========================
   DATE (FIXED VERSION)
========================= */

// make sure DOM is ready (VERY IMPORTANT)
document.addEventListener("DOMContentLoaded", function () {

flatpickr("#event_date", {
    dateFormat: "m-d-Y",
    allowInput: true
});

});

document.getElementById("event_date").addEventListener("input", function () {
    let v = this.value.replace(/[^0-9]/g, "");

    if (v.length > 2 && v.length <= 4) {
        this.value = v.slice(0,2) + "-" + v.slice(2);
    } 
    else if (v.length > 4) {
        this.value = v.slice(0,2) + "-" + v.slice(2,4) + "-" + v.slice(4,8);
    } 
    else {
        this.value = v;
    }
});

document.getElementById("event_date").addEventListener("blur", function () {

let val = this.value;

let regex = /^(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])-\d{4}$/;

if (!val) return;

if (!regex.test(val)) {
    Swal.fire({
        icon: 'error',
        title: 'Invalid Date Format',
        text: 'Use format MM-DD-YYYY'
    });
    this.value = "";
    return;
}

// ✅ EXTRACT YEAR
let year = parseInt(val.split("-")[2]);
let currentYear = new Date().getFullYear();

// ❌ BLOCK WRONG YEARS
if (year !== currentYear) {
    Swal.fire({
        icon: 'error',
        title: 'Invalid Year',
        text: `Only dates in ${currentYear} are allowed`
    });
    this.value = "";
}
});
/* =========================
   time
========================= */
flatpickr("#start_time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "h:i K",   //  THIS is the key (12-hour with AM/PM)
    time_24hr: false,      //  ensures AM/PM mode
    allowInput: true,      //  allows typing
    defaultHour: 8,
    defaultMinute: 0
});

flatpickr("#end_time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "h:i K",
    time_24hr: false,
    allowInput: true,
    defaultHour: 9,
    defaultMinute: 0
});

/* =========================
   NEXT STEP (WITH VALIDATION)
========================= */
function nextStep() {

// ✅ ONLY validate when coming from STEP 1
if (step === 1) {

    let step1 = document.getElementById("step1");
    let inputs = step1.querySelectorAll("input[required], select[required]");

    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add("is-invalid");
        } else {
            input.classList.remove("is-invalid");
        }
    });

    // Time validation
        let start = document.querySelector('[name="start_time"]').value;
        let end = document.querySelector('[name="end_time"]').value;

        if (start && end) {

            function convertTo24(timeStr) {
                let [time, modifier] = timeStr.split(" ");
                let [hours, minutes] = time.split(":");

                hours = parseInt(hours);

                if (modifier === "PM" && hours !== 12) hours += 12;
                if (modifier === "AM" && hours === 12) hours = 0;

                return hours * 60 + parseInt(minutes); // convert to minutes
            }

            let startMinutes = convertTo24(start);
            let endMinutes = convertTo24(end);

            if (endMinutes <= startMinutes) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Time',
                    text: 'End time must be after start time'
                });
                return;
            }
        }

    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Fields',
            text: 'Please complete all required fields'
        });
        return;
    }
}


if (step === 2) {

    // CHECK DEFAULT CHECKBOXES
    let defaultChecked = document.querySelectorAll(".default-check:checked").length;

    // CHECK DYNAMIC CARDS
    let hasDynamicChecklist = Object.keys(checklistCards).length > 0;

    let hasDynamicItems = false;

    for (let id in checklistCards) {
        if (checklistCards[id].length > 0) {
            hasDynamicItems = true;
            break;
        }
    }

    // ❌ BLOCK IF NOTHING SELECTED/CREATED
    if (defaultChecked === 0 && !hasDynamicItems) {

        Swal.fire({
            icon: 'warning',
            title: 'Checklist Required',
            text: 'Please select at least one checklist item or create a checklist.'
        });

        return;
    }
}

// ✅ THIS IS THE FIX
step++;   // instead of step = 2
showStep();
}

/* =========================
   PREVIOUS STEP
========================= */
function prevStep() {
    step--;
    showStep();
}

/* =========================
   CHECKLIST ADD
========================= */
function addChecklistItem() {
    let input = document.getElementById("customChecklistInput");
    let value = input.value.trim();

    if (value !== "") {
        checklist.push(value);
        input.value = "";
        renderChecklist(); // always refresh UI
    }
}

/* =========================
   RENDER CHECKLIST (IMPORTANT FIX)
========================= */
function renderChecklist(json) {
    if (!json) return "<span class='text-muted'>No checklist available</span>";

    try {
        let data = JSON.parse(decodeURIComponent(json));

        if (!Array.isArray(data)) {
            return "<span class='text-muted'>Invalid checklist format</span>";
        }

        let html = `<div class="list-group">`;

        data.forEach(item => {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${item?.title ?? "Untitled"}</span>
                    <span class="badge ${item?.done == 1 ? 'bg-success' : 'bg-danger'}">
                        ${item?.done == 1 ? 'Done' : 'Pending'}
                    </span>
                </div>
            `;
        });

        html += `</div>`;
        return html;

    } catch (e) {
        console.error("Checklist error:", e, json);
        return "<span class='text-danger'>Checklist corrupted</span>";
    }
}

function renderDefaultChecklist() {
    let container = document.getElementById("defaultChecklist");
    container.innerHTML = "";

    let visibleItems = "";
    let hiddenItems = "";

    defaultChecklist.forEach((item, index) => {

        let html = `
            <div class="form-check mb-2">
                <input class="form-check-input default-check"
                       type="checkbox"
                       id="default_${index}"
                       value="${item}"
                       checked>

                <label class="form-check-label ms-2" for="default_${index}">
                    ${item}
                </label>
            </div>
        `;

        if (index < 3) visibleItems += html;
        else hiddenItems += html;
    });

    container.innerHTML = `
        <div id="visible-tasks">
            ${visibleItems}
        </div>

        <div id="extra-tasks">
            ${hiddenItems}
        </div>
    `;

    // IMPORTANT: auto-sync Select All AFTER rendering
    syncSelectAllDefaults();
}

// Function to handle the icon and visibility toggle
function toggleExtraTasks(btn) {
    const extraSection = document.getElementById('extra-tasks');
    
    // This one line toggles the "open" state
    extraSection.classList.toggle('show-list');

    // Update the button text and icon
    if (extraSection.classList.contains('show-list')) {
        btn.innerHTML = '<i class="bi bi-chevron-up"></i>';
    } else {
        btn.innerHTML = '<i class="bi bi-chevron-down"></i>';
    }
}

/* =========================
   REMOVE ITEM
========================= */
function removeChecklist(index) {
    checklist.splice(index, 1);
    renderChecklist();
}
/* =========================
   REMOVE INVALID ON TYPING
========================= */
document.querySelectorAll("#step1 input, #step1 select").forEach(input => {
    input.addEventListener("input", function () {
        if (this.value.trim() !== "") {
            this.classList.remove("is-invalid");
        }
    });
});

// COLLECT PERSON IN CHARGE
let selectedPersons = [];

document.querySelectorAll(".person-checkbox:checked").forEach(cb => {
    selectedPersons.push(cb.value);
});

// SAVE INTO HIDDEN INPUT
document.getElementById("persons").value = JSON.stringify(selectedPersons);

/* =========================
   FORM SUBMIT
========================= */
document.getElementById("taskForm").addEventListener("submit", function (e) {
    e.preventDefault();

    // CHECKBOX VALIDATION (ADD THIS)
    const checkboxes = document.querySelectorAll(".person-checkbox:checked");

    if (checkboxes.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Selection',
            text: 'Please select at least one person in charge.',
            confirmButtonColor: '#3085d6'
        });
        return; // stop everything here
    }

    let form = this;
    let checklistItems = [];

    // DEFAULT CHECKLIST
    document.querySelectorAll(".default-check:checked").forEach(cb => {
        checklistItems.push({
            title: cb.value,
            done: 0
        });
    });

    // DYNAMIC CHECKLIST
    for (let id in checklistCards) {
        checklistCards[id].forEach(item => {
            checklistItems.push({
                title: item,
                done: 0
            });
        });
    }

    // SAVE CHECKLIST
    document.getElementById("checklist").value = JSON.stringify(checklistItems);

    // ADD THIS BLOCK (FIX)
    let selectedPersons = [];

    document.querySelectorAll(".person-checkbox:checked").forEach(cb => {
        selectedPersons.push(cb.value);
    });

    document.getElementById("persons").value = JSON.stringify(selectedPersons);
    // END FIX

    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to add this task?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {

const urlParams = new URLSearchParams(window.location.search);

if (urlParams.get("success") === "1") {

    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Task successfully added!',
        timer: 2000,
        showConfirmButton: false
    });

    // remove param AFTER showing alert
    window.history.replaceState({}, document.title, "managetask.php");
}
});

/* =========================
   INIT STEP
========================= */
showStep();



/* =========================
   ADD CHECKLIST CARD
========================= */
function addChecklistCard() {

let container = document.getElementById("checklistCards");

let id = "card_" + cardIndex;

checklistCards[id] = [];

let card = document.createElement("div");
card.className = "card border-0 shadow-sm rounded-4 mb-3";
card.setAttribute("data-id", id);

card.innerHTML = `
    <div class="card-body">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-semibold mb-0">
                <i class="bi bi-list-check text-success me-2"></i>List
            </h6>

            <button type="button" class="btn close btn-lg text-danger px-0" onclick="removeCard('${id}')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- CHECKLIST ITEMS -->
        <div id="list_${id}" class="mb-3"></div>

        <!-- INPUT + ADD BUTTON (NOW AT BOTTOM) -->
        <div class="input-group">
            <input type="text"
                class="form-control"
                id="input_${id}"
                placeholder="Add item...">

            <button type="button"
                class="btn btn-primary addb"
                onclick="addItem('${id}')">
                Add
            </button>
        </div>

    </div>
`;

container.appendChild(card);

cardIndex++;
}

function addItem(id) {

let input = document.getElementById("input_" + id);
let value = input.value.trim();

if (value === "") return;

checklistCards[id].push(value);
input.value = "";

renderItems(id);
}

function renderItems(id) {

let list = document.getElementById("list_" + id);
list.innerHTML = "";

checklistCards[id].forEach((item, index) => {

    let div = document.createElement("div");
    div.className = "d-flex justify-content-between align-items-center mb-2";

    div.innerHTML = `
        <div class="d-flex align-items-center flex-grow-1">

            <input class="form-check-input readonly-check checkbox-disabled" type="checkbox">

            <label class="form-check-label ms-2 flex-grow-1"
                   id="label_${id}_${index}">
                ${item}
            </label>

            <input type="text"
                class="form-control form-control-sm ms-2 d-none flex-grow-1"
                id="edit_${id}_${index}"
                value="${item}">
        </div>

        <div class="ms-2">

            <!-- EDIT BUTTON (will hide on click) -->
            <button type="button"
                class="btn btn-sm btn-outline-warning"
                id="editBtn_${id}_${index}"
                onclick="editItem('${id}', ${index})">
                <i class="bi bi-pencil"></i>
            </button>

            <!-- SAVE BUTTON (hidden initially) -->
            <button type="button"
                class="btn btn-sm btn-success d-none"
                id="saveBtn_${id}_${index}"
                onclick="saveEdit('${id}', ${index})">
                <i class="bi bi-check"></i>
            </button>

            <!-- DELETE -->
            <button type="button"
                class="btn btn-sm btn-outline-danger"
                onclick="removeItem('${id}', ${index})">
                <i class="bi bi-trash"></i>
            </button>

        </div>
    `;

    list.appendChild(div);
});
}

document.getElementById("selectAllDefaults").addEventListener("change", function () {
    let isChecked = this.checked;

    document.querySelectorAll(".default-check").forEach(cb => {
        cb.checked = isChecked;
    });
});


document.addEventListener("change", function (e) {
    if (e.target.classList.contains("default-check")) {
        syncSelectAllDefaults();
    }
});

function removeItem(id, index) {
    checklistCards[id].splice(index, 1);
    renderItems(id);
}

function removeCard(id) {

delete checklistCards[id];

let card = document.querySelector(`[data-id="${id}"]`);
if (card) card.remove();
}

function editItem(id, index) {

let label = document.getElementById(`label_${id}_${index}`);
let input = document.getElementById(`edit_${id}_${index}`);
let editBtn = document.getElementById(`editBtn_${id}_${index}`);
let saveBtn = document.getElementById(`saveBtn_${id}_${index}`);

label.classList.add("d-none");
input.classList.remove("d-none");

editBtn.classList.add("d-none");   // hide edit
saveBtn.classList.remove("d-none"); // show save

input.focus();
}

function saveEdit(id, index) {

let input = document.getElementById(`edit_${id}_${index}`);
let newValue = input.value.trim();

if (newValue !== "") {
    checklistCards[id][index] = newValue;
}

renderItems(id); // resets everything cleanly
}

/* =========================
   FIX: ENTER KEY FOR CHECKLIST (STEP 2)
========================= */
document.addEventListener("keydown", function (e) {

if (e.key === "Enter") {

    let active = document.activeElement;

    // If typing inside checklist input (Step 2 dynamic cards)
    if (active && active.id && active.id.startsWith("input_")) {

        e.preventDefault(); // stop form submit

        let id = active.id.replace("input_", "");

        addItem(id); // trigger Add button
    }

    // Optional: prevent accidental submit inside step2 generally
    if (step === 2) {
        e.preventDefault();
    }
}
});

/* =========================
   FIX: ENTER KEY SUBMIT (STEP 3)
========================= */
document.getElementById("taskForm").addEventListener("keydown", function (e) {
    if (e.key === "Enter") {

        // ❌ prevent accidental submit anywhere except step 3 save button
        if (step !== 3) {
            e.preventDefault();
        }
    }
});

document.addEventListener("DOMContentLoaded", () => {
    currentStatus = "pending";
    currentPage = 1;
    loadTasks(currentStatus, currentPage);
});

let currentPage = 1;

document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
    tab.addEventListener("shown.bs.tab", function (e) {

        let target = e.target.getAttribute("data-bs-target");

        if (target === "#pending") currentStatus = "pending";
        if (target === "#preparing") currentStatus = "preparing";
        if (target === "#completed") currentStatus = "completed";

        currentPage = 1;
        loadTasks(currentStatus, currentPage);
    });
});

function loadTasks(status, page) {

if (!status) status = "pending";

fetch(`fetch_tasks.php?status=${encodeURIComponent(status)}&page=${page}`)
    .then(res => res.json())
    .then(res => {

        currentPage = page;
        window.totalPages = res.totalPages ?? 1;

        renderTable(status, res.data);

        document.getElementById("pageInfo").innerText =
         `Page ${currentPage} of ${window.totalPages || 1}`;

        updatePaginationButtons(); // ADD THIS
    });
}

function renderTable(status, data) {

let tbodyId =
    status === "pending" ? "pendingBody" :
    status === "preparing" ? "preparingBody" :
    "completedBody";

let tbody = document.getElementById(tbodyId);
tbody.innerHTML = "";

if (data.length === 0) {
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center text-muted">No tasks found</td>
        </tr>`;
    return;
}

const limit = 10;
let no = (currentPage - 1) * limit + 1;

data.forEach(row => {
    tbody.innerHTML += `
        <tr class="rounded-circle" id="shadow-cus">
            <td class="mid">${no++}</td>
            <td class="truncate" title="${row.event_title}">
                ${row.event_title}
            </td>
            <td class="mid">${formatDate(row.event_date)}</td>
            <td class="mid">
            ${row.start_time && row.end_time 
                ? `${formatTime(row.start_time)} - ${formatTime(row.end_time)}`
                : "N/A"}
            </td>
            <td class="mid truncate" title="${row.venue}">
                ${row.venue}
            </td>
            <td class="mid">
                <button
                    class="btn btn-sm light-blue viewBtn"
                    data-title="${row.event_title}"
                    data-type="${row.event_type}"
                    data-date="${row.event_date}"
                    data-start="${row.start_time}"
                    data-end="${row.end_time}"
                    data-location="${row.location}"
                    data-venue="${row.venue}"
                    data-status="${row.status}"
                    data-created="${row.created_at}"
                    data-pic='${JSON.stringify(row.pic_data ?? [])}'
                    data-checklist='${encodeURIComponent(JSON.stringify(row.checklist ?? []))}'
                    data-bs-toggle="modal"
                    data-bs-target="#eventModal"
                >
                    <i class="bi bi-eye"></i>
                </button>

                ${status !== "completed" ? `
                ` : ""}
                <button 
                        class="btn btn-sm light-red deleteBtn"
                        data-id="${row.id}">
                        <i class="bi bi-trash"></i>
                    </button>
            </td>
        </tr>
    `;
});
}

// connect eye icon to view_task.php
function viewTask(id) {
    window.location.href = "view_task.php?id=" + id;
}

function formatDate(dateStr) {
    if (!dateStr) return "N/A";

    let parts = dateStr.split("-"); // YYYY-MM-DD

    if (parts.length !== 3) return dateStr;

    let year = parts[0];
    let month = parts[1];
    let day = parts[2];

    return `${month}/${day}/${year}`;
}

function formatTime(time) {
    if (!time || typeof time !== "string") return "N/A";

    let parts = time.split(":");
    if (parts.length < 2) return "N/A";

    let [h, m] = parts;
    let hour = h % 12 || 12;
    let ampm = h >= 12 ? "PM" : "AM";

    return `${hour}:${m} ${ampm}`;
}

function changePage(direction) {
    let newPage = currentPage + direction;

    if (newPage < 1) return;
    if (newPage > window.totalPages) return;

    loadTasks(currentStatus, newPage);
}

function updatePaginationButtons() {
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("paginationNextBtn");

    if (!prevBtn || !nextBtn) return;

    prevBtn.disabled = currentPage <= 1;
    nextBtn.disabled = currentPage >= window.totalPages;
}

function safe(value) {
    return (value && value !== "null") ? value : "None";
}


function removeCard(id) {

    // 1. delete from memory object
    if (checklistCards[id]) {
        delete checklistCards[id];
    }

    // 2. remove DOM element
    const card = document.querySelector(`[data-id="${id}"]`);
    if (card) {
        card.remove();
    }

    // 3. optional: reindex UI (prevents gaps visually)
    console.log(`Card ${id} removed`);
}

function removeCard(id) {

    Swal.fire({
        title: 'Remove checklist?',
        text: "This will delete all items inside this checklist.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, remove it'
    }).then((result) => {

        if (result.isConfirmed) {

            delete checklistCards[id];

            const card = document.querySelector(`[data-id="${id}"]`);
            if (card) card.remove();

            Swal.fire({
                icon: 'success',
                title: 'Removed!',
                timer: 1200,
                showConfirmButton: false
            });
        }
    });
}



document.addEventListener("click", function (e) {

    const btn = e.target.closest(".deleteBtn");

    if (!btn) return;

    let id = btn.dataset.id;

    Swal.fire({
        title: 'Delete Task?',
        text: "Are you sure you want to delete this task?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes'
    }).then((result) => {

        if (result.isConfirmed) {

            fetch("soft_delete_task.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `id=${id}`
            })
            .then(res => res.json())
            .then(data => {

                if (data.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Task successfully deleted.',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    loadTasks(currentStatus, currentPage);

                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to delete task'
                    });

                }

            })
            .catch(err => {

                console.error(err);

                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Something went wrong'
                });

            });

        }

    });

});


document.getElementById("logoutBtn").addEventListener("click", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "Logout Confirmation",
        text: "Are you sure you want to logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "logout.php";
        }
    });
});


</script>
</body>
</html>

