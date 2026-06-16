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

date_default_timezone_set('Asia/Manila');


// =========================
// GET DISTINCT YEARS FROM DB
// =========================
$yearStmt = $pdo->query("
    SELECT DISTINCT YEAR(event_date) AS year
    FROM tbl_tasks
    WHERE event_date IS NOT NULL
    ORDER BY year DESC
");

$years = $yearStmt->fetchAll(PDO::FETCH_COLUMN);

$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

$stmt = $pdo->prepare("
    SELECT 
        id,
        event_title,
        event_type,
        event_date,
        start_time,
        end_time,
        location,
        venue,
        person_in_charge,
        status,
        created_at,
        checklist,
        MONTH(event_date) AS event_month
    FROM tbl_tasks
    WHERE YEAR(event_date) = ?
    ORDER BY event_date ASC
");

$stmt->execute([$currentYear]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   GROUP BY MONTH
========================= */
$grouped = [];

for ($i = 1; $i <= 12; $i++) {
    $grouped[$i] = [];
}

foreach ($events as $row) {
    $month = (int)$row['event_month'];
    $grouped[$month][] = $row;
}

/* =========================
   FORMATTERS
========================= */
function formatDate($date) {
    return date("m/d/Y", strtotime($date));
}

function formatTime($time) {
    return date("h:i A", strtotime($time));
}

function getPersonInCharge($pdo, $json) {
    if (!$json || $json === "[]" || $json === "") return [];

    $ids = json_decode($json, true);
    if (!is_array($ids) || empty($ids)) return [];

    // ensure integers only
    $ids = array_map('intval', $ids);

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, profile_pic
        FROM tbl_users
        WHERE id IN ($placeholders)
    ");

    $stmt->execute($ids);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$months = [
    1 => "Jan", 2 => "Feb", 3 => "Mar",
    4 => "Apr", 5 => "May", 6 => "Jun",
    7 => "Jul", 8 => "Aug", 9 => "Sep",
    10 => "Oct", 11 => "Nov", 12 => "Dec"
];

$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n');

$num = $currentMonth; // ✅ SAFE
$name = $months[$num];

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
</head>
<body>
<style>
select.form-select.selected.rounded-1.w-auto {
    padding: 6.5px 30px 6.5px 20px;
}

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
    font-weight: 600;
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

.form-select {
    height: auto;
}

/* Example fixed widths per column */
.col-no { width: 60px; }
.col-event { width: 220px; }
.col-date { width: 120px; }
.col-time { width: 160px; }
.col-venue { width: 200px; }
.col-actions { width: 140px; }

.flatpickr-current-month .numInputWrapper span.arrowUp,
.flatpickr-current-month .numInputWrapper span.arrowDown {
    display: none !important;
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
                            <a class="nav-link" href="managetask.php"><i class="bi bi-list-check me-2"></i>Events</a>
                        </li>
                    </ul>

                    <p class="d-flex justify-content-between align-items-center mt-3 px-3 mb-1 overview">
                        History
                    </p>              
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link active" href="#"><i class="bi bi-calendar2-minus me-2"></i> Records</a>
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
                <h3 class="mb-0">All Events</h3>

                <div class="d-flex justify-content-end align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" 
                                type="button" 
                                id="dateDropdown<?= $num ?>" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            Date
                        </button>
                        
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dateDropdown<?= $num ?>">
                            <li>
                                <button class="dropdown-item" type="button" onclick="filterDay('all', <?= $num ?>)">
                                    All Dates
                                </button>
                            </li>

                            <?php
                            // Collect dates from your grouped array
                            $dates = [];
                            if (!empty($grouped[$num])) {
                                foreach ($grouped[$num] as $row) {
                                    $d = date("Y-m-d", strtotime($row['event_date']));
                                    $dates[$d] = true;
                                }
                            }

                            // Check if we actually found any dates
                            if (empty($dates)): 
                            ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <span class="dropdown-item-text text-muted small text-center px-0">
                                        No dates available
                                    </span>
                                </li>
                            <?php else: ?>
                                <li><hr class="dropdown-divider"></li>
                                <?php
                                foreach ($dates as $date => $_):
                                    $dayValue = date('d', strtotime($date));
                                    $formattedDate = date("M d", strtotime($date));
                                ?>
                                    <li>
                                        <button class="dropdown-item" type="button" onclick="filterDay('<?= $dayValue ?>', <?= $num ?>)">
                                            <?= $formattedDate ?>
                                        </button>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
                <!-- MONTH DROPDOWN -->
                <div class="d-flex justify-content-between border-bottom">
                    <div class="d-flex justify-content-start gap-2">

                        <div>
                            <form method="GET" class="mb-3 d-flex gap-2">
                                
                                <!-- KEEP YEAR -->
                                <input type="hidden" name="year" value="<?= $currentYear ?>">

                                <!-- MONTH DROPDOWN -->
                                <select name="month" class="form-select selected rounded-1 w-auto" onchange="this.form.submit()">
                                <?php foreach ($months as $mNum => $mName): ?>
                                    <option value="<?= $mNum ?>" <?= $mNum == $currentMonth ? 'selected' : '' ?>>
                                        <?= $mName ?>
                                    </option>
                                <?php endforeach; ?>

                                </select>

                            </form>
                        </div>

                        <div>
                            <form method="GET" class="mb-2">
                            <select name="year" class="form-select selected rounded-1 w-auto" onchange="this.form.submit()">
                            <?php foreach ($years as $y): ?>
                                    <option value="<?= $y ?>" <?= $y == $currentYear ? 'selected' : '' ?>>
                                        <?= $y ?>
                                    </option>
                            <?php endforeach; ?>
                            </select>
                            </form>                           
                        </div>     
                    </div>

                    <div class="modern-pagination d-flex align-items-center gap-2 ms-auto" id="pagination-<?= $num ?>">

                        <button class="btn btn-sm btn-light shadow-sm"
                            onclick="changePage(<?= $num ?>, -1)">
                            <i class="bi bi-chevron-left"></i>
                        </button>

                        <span class="page-indicator px-3 py-1 rounded-pill" id="pageInfo-<?= $num ?>">
                            Page 1
                        </span>

                        <button class="btn btn-sm btn-light shadow-sm"
                            onclick="changePage(<?= $num ?>, 1)">
                            <i class="bi bi-chevron-right"></i>
                        </button>

                    </div>
                </div>

                <!-- TAB CONTENT -->
                <div class="tab-content bg-white border-top-0">                                   
                    <div class="tab-pane fade show active">
                        <div class="table-responsive">
                            <table class="table table-borderless table-hover align-middle">

                                <thead class="table-color">
                                    <tr>
                                        <th class="mid col-no">No.</th>
                                        <th>Event</th>
                                        <th class="mid">Date</th>
                                        <th class="mid">Venue</th>
                                        <th class="mid">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="tbody-<?= $num ?>">

                                <?php if (empty($grouped[$num])): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            No events this month
                                        </td>
                                    </tr>
                                <?php else: ?>

                                    <?php $no = 1; foreach ($grouped[$num] as $row): ?>

                                    <tr data-day="<?= date('d', strtotime($row['event_date'])) ?>">

                                        <td class="mid row-num"><?= $no++ ?></td>

                                        <td class="truncate"><?= $row['event_title'] ?></td>

                                        <td class="mid"><?= formatDate($row['event_date']) ?></td>

                                        <td class="mid truncate"><?= $row['venue'] ?></td>

                                        <td class="mid">
                                            <button 
                                                class="btn btn-sm light-blue viewBtn"
                                                data-title="<?= htmlspecialchars($row['event_title']) ?>"
                                                data-type="<?= $row['event_type'] ?>"
                                                data-date="<?= formatDate($row['event_date']) ?>"
                                                data-time="<?= formatTime($row['start_time']) ?> - <?= formatTime($row['end_time']) ?>"
                                                data-venue="<?= $row['venue'] ?>"
                                                data-pic='<?= htmlspecialchars(json_encode(getPersonInCharge($pdo, $row["person_in_charge"]))) ?>'
                                                data-status="<?= $row['status'] ?>"
                                                data-created="<?= $row['created_at'] ?>"
                                                data-checklist="<?= htmlspecialchars($row['checklist']) ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#eventModal"
                                            >
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>

                                    </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
        </main>
    </div>

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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

let filterState = {};

function formatCreatedDate(datetime) {
    if (!datetime) return '';

    // convert MySQL datetime to JS-compatible format
    let date = new Date(datetime.replace(' ', 'T'));

    const options = {
        month: '2-digit',
        day: '2-digit',
        year: '2-digit',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    };

    return date.toLocaleString('en-US', options).replace(',', ' |');
}

/* FILTER + FIX NUMBERING */
function filterDay(day, month) {

filterState[month] = day;

paginationState[month].page = 1; // reset page

paginate(month);
}


/* MODAL */
document.querySelectorAll(".viewBtn").forEach(btn => {
    btn.addEventListener("click", function () {

        document.getElementById("mTitle").innerText = this.dataset.title;
        document.getElementById("mType").innerText = this.dataset.type;
        document.getElementById("mDate").innerText = this.dataset.date;
        document.getElementById("mTime").innerText = this.dataset.time;
        document.getElementById("mVenue").innerText = this.dataset.venue;

        let picData = JSON.parse(this.dataset.pic || "[]");

        if (picData.length === 0) {
            document.getElementById("mPIC").innerHTML =
                `<span class="text-muted spanner" >No person assigned</span>`;
        } else {

            let html = "";

            picData.forEach(user => {

                let fullName = `${user.first_name} ${user.last_name}`;
                let initial = user.first_name.charAt(0).toUpperCase();

                html += `
                    <div class="pic-wrapper" data-name="${fullName}">

                        ${
                            user.profile_pic
                            ? `<img src="uploads/${user.profile_pic}" class="rounded-circle" width="34" height="34">`
                            : `<div class="pic-circle">
                                    ${initial}
                               </div>`
                        }

                    </div>
                `;
            });

            document.getElementById("mPIC").innerHTML = html;
        }

        document.getElementById("mStatus").innerText = this.dataset.status;
        document.getElementById("mCreated").innerText = formatCreatedDate(this.dataset.created);

        document.getElementById("mChecklist").innerHTML =
            renderChecklist(this.dataset.checklist);

    });
});


function renderChecklist(json) {
    if (!json) {
        return "<span class='text-muted'>No checklist available</span>";
    }

    try {
        let data = JSON.parse(json);

        if (!Array.isArray(data)) {
            return "<span class='text-muted'>Invalid checklist format</span>";
        }

        let html = `<div class="list-group">`;

        data.forEach(item => {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    
                    <div class="d-flex align-items-center">
                        <span>${item.title}</span>
                    </div>

                    <span class="badge ${item.done == 1 ? 'bg-success' : 'bg-danger'}">
                        ${item.done == 1 ? 'Done' : 'Pending'}
                    </span>

                </div>
            `;
        });

        html += `</div>`;

        return html;

    } catch (e) {
        return "<span class='text-danger'>Checklist data corrupted</span>";
    }
}

const rowsPerPage = 10;
let paginationState = {};

/* INIT PAGINATION FOR ALL MONTHS */
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll("tbody[id^='tbody-']").forEach(tbody => {
        let month = tbody.id.replace("tbody-", "");

        paginationState[month] = {
            page: 1
        };

        paginate(month);
    });
});

function getFilteredRows(tbody, month) {

let rows = Array.from(tbody.querySelectorAll("tr"));

let selectedDay = filterState[month] || "all";

return rows.filter(row => {

    let rowDay = parseInt(row.getAttribute("data-day"));

    return selectedDay === "all" || rowDay === parseInt(selectedDay);
});
}

/* PAGINATE FUNCTION */
function paginate(month) {

let tbody = document.getElementById("tbody-" + month);
let rows = Array.from(tbody.querySelectorAll("tr:not(.no-data)"));

let filteredRows = getFilteredRows(tbody, month);

let totalPages = Math.ceil(filteredRows.length / rowsPerPage);
let currentPage = paginationState[month].page;

if (totalPages === 0) {
    document.getElementById("pagination-" + month).innerHTML = "";
    return;
}

if (currentPage > totalPages) currentPage = 1;
if (currentPage < 1) currentPage = 1;

paginationState[month].page = currentPage;

rows.forEach(r => r.style.display = "none");

let start = (currentPage - 1) * rowsPerPage;
let end = start + rowsPerPage;

filteredRows.slice(start, end).forEach((row, index) => {
    row.style.display = "";
    row.querySelector(".row-num").innerText = start + index + 1;
});

renderPagination(month, totalPages);
}

/* RENDER PAGINATION BUTTONS */
function renderPagination(month, totalPages) {

let container = document.getElementById("pagination-" + month);
let pageInfo = document.getElementById("pageInfo-" + month);

let currentPage = paginationState[month].page;

if (totalPages <= 1) {
    container.style.display = "none";
    return;
}

container.style.display = "flex";

// update page indicator
pageInfo.innerText = `Page ${currentPage} of ${totalPages}`;

// disable prev/next buttons
let prevBtn = container.querySelector("button:nth-child(1)");
let nextBtn = container.querySelector("button:nth-child(3)");

prevBtn.disabled = currentPage === 1;
nextBtn.disabled = currentPage === totalPages;
}

/* NAVIGATION */
function goToPage(month, page) {
    paginationState[month].page = page;
    paginate(month);
}

function changePage(month, direction) {

let current = paginationState[month].page;

let tbody = document.getElementById("tbody-" + month);
let filteredRows = getFilteredRows(tbody, month);

let totalPages = Math.ceil(filteredRows.length / rowsPerPage);

let newPage = current + direction;

if (newPage < 1) newPage = 1;
if (newPage > totalPages) newPage = totalPages;

paginationState[month].page = newPage;

paginate(month);
}

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


