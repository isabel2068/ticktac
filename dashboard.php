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


// SET TIMEZONE FIRST (important for correct month)
date_default_timezone_set('Asia/Manila');

// CURRENT MONTH FILTER
$currentMonth = date('Y-m'); // e.g. 2026-04

// COUNT PENDING (THIS MONTH ONLY)
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM tbl_tasks 
    WHERE status = 'pending'
    AND soft_delete = 0
    AND DATE_FORMAT(event_date, '%Y-%m') = :month
");
$stmt->execute(['month' => $currentMonth]);
$pendingCount = $stmt->fetchColumn();


// COUNT ONGOING (BASED ON CURRENT TIME)
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM tbl_tasks 
    WHERE soft_delete = 0
    AND NOW() BETWEEN 
        CONCAT(event_date, ' ', start_time) 
        AND 
        CONCAT(event_date, ' ', end_time)
");
$stmt->execute();
$ongoingCount = $stmt->fetchColumn();


// COUNT COMPLETED (THIS MONTH ONLY)
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM tbl_tasks 
    WHERE status = 'completed'
    AND soft_delete = 0
    AND DATE_FORMAT(event_date, '%Y-%m') = :month
");
$stmt->execute(['month' => $currentMonth]);
$completedCount = $stmt->fetchColumn();


date_default_timezone_set('Asia/Manila');

// GET TODAY
$today = new DateTime('now', new DateTimeZone('Asia/Manila'));

// MONDAY START
$start = clone $today;
$start->setISODate((int)$today->format('o'), (int)$today->format('W'), 1);

// SUNDAY END
$end = clone $start;
$end->modify('+6 days');

$startDate = $start->format('Y-m-d');
$endDate   = $end->format('Y-m-d');

// QUERY WEEK RANGE
$stmt = $pdo->prepare("
    SELECT event_title, event_date, start_time, end_time, venue, status, checklist
    FROM tbl_tasks
    WHERE status = 'pending'
    AND soft_delete = 0
    AND event_date BETWEEN :start AND :end
    ORDER BY event_date ASC, start_time ASC
");

$stmt->execute([
    ':start' => $startDate,
    ':end' => $endDate
]);

$upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ==============================
// ACTIVITY PROGRESS (PENDING & COMPLETED ONLY)
// ==============================

$startOfMonth = date('Y-m-01');
$endOfMonth   = date('Y-m-t');

$stmt = $pdo->prepare("
    SELECT 
        DATE(event_date) as day,
        status
    FROM tbl_tasks
    WHERE soft_delete = 0
    AND event_date BETWEEN :start AND :end
");

$stmt->execute([
    ':start' => $startOfMonth,
    ':end' => $endOfMonth
]);

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$days = [];
$pending = [];
$completed = [];

foreach ($tasks as $task) {

    $day = $task['day'];

    if (!isset($days[$day])) {
        $days[$day] = true;
        $pending[$day] = 0;
        $completed[$day] = 0;
    }

    if ($task['status'] === 'pending') {
        $pending[$day]++;
    }

    if ($task['status'] === 'completed') {
        $completed[$day]++;
    }
}

// sort by date (IMPORTANT for graph order)
ksort($pending);
ksort($completed);

$stmt = $pdo->prepare("
    SELECT id, event_date, event_title, project_status
    FROM tbl_tasks
    WHERE soft_delete = 0
    ORDER BY event_date DESC
    LIMIT 10
");

$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="ticktac.css">
    <link rel="stylesheet" href="alert.css">
</head>
<body>

<style>
th,td {
    background-color: transparent !important;
    vertical-align: middle;
}

.mid .form-select {
    max-width: 150px;
    margin: 0 auto; 
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

.form-select {
    font-size: 14px;
}

.form-control, .form-select {
    height: 40px;
}
</style>

<nav class="navbar navbar-expand-lg sticky-top border-body" data-bs-theme="light">
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
                            <a class="nav-link active" href="#"><i class="bi bi-pie-chart me-2"></i>Dashboard</a>
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
        <div class="border-bottom">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h3 class="mb-0">Dashboard</h3>
            </div>

            <div class="row">
                <div class="col-12 col-md-4 col-lg-4 mb-3">
                    <div class="card pencard text-bg-light h-100">
                        <div class="card-header">
                            <i class="bi bi-1-circle-fill text-darkblue fs-6 px-2 py-1 rounded-1 lightblue opacity-50 me-1"></i> Incoming Events
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                <h1 class="card-title danger title"><?php echo $pendingCount; ?></h1>
                                </div>
                                <div>
                                    <i class="bi-hourglass-top pending fs-1 p-2 opacity-50"></i>
                                </div>
                            </div>
                            <p class="card-text d-flex justify-content-end text-muted small">This month</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4 col-lg-4 mb-3">
                    <div class="card oncard text-bg-light h-100">
                        <div class="card-header">
                            <i class="bi bi-2-circle-fill text-darkblue fs-6 px-2 py-1 rounded-1 lightblue opacity-50 me-1"></i> Ongoing Events
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                <h1 class="card-title primary title"><?php echo $ongoingCount; ?></h1>
                                </div>
                                <div>
                                    <i class="bi-hourglass-split ongoing fs-1 p-2 opacity-50"></i>
                                </div>
                            </div>
                            <p class="card-text d-flex justify-content-end text-muted small">This day</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4 col-lg-4 mb-3">
                    <div class="card comcard text-bg-light h-100">
                        <div class="card-header">
                            <i class="bi bi-3-circle-fill text-darkblue fs-6 px-2 py-1 rounded-1 lightblue opacity-50 me-1"></i> Completed Events
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                <h1 class="card-title success title"><?php echo $completedCount; ?></h1>
                                </div>
                                <div>
                                    <i class="bi-hourglass-bottom completed fs-1 p-2 opacity-50"></i>
                                </div>
                            </div>
                            <p class="card-text d-flex justify-content-end text-muted small">This month</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row s-graphs">
                <div class="col-12 col-md-6 col-lg-6 mb-3">
                    <div class="card text-bg-light graphs">
                        <div class="d-flex justify-content-between">
                            <div class="card-header">
                                <i class="bi bi-4-circle-fill text-darkblue fs-6 px-2 py-1 rounded-1 lightblue opacity-50 me-1"></i> Activity Progress Tracker
                            </div> 
                            <div class="p-custom">
                                <p class="card-text d-flex justify-content-end text-muted small">This month</p>
                            </div>                           
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div style="height: 200px;">
                                <canvas id="progressLineChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-6 mb-3">
                    <div class="card text-bg-light notifs">
                        <div class="d-flex justify-content-between">
                            <div class="card-header">
                                <i class="bi bi-5-circle-fill text-darkblue fs-6 px-2 py-1 rounded-1 lightblue opacity-50 me-1"></i> Upcoming Events
                            </div>
                            <div class="p-custom">
                                <p class="card-text d-flex justify-content-end text-muted small">This week</p>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <div id="upcoming-container">
                                <?php if (!empty($upcomingEvents)): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($upcomingEvents as $event): ?>
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong><?= htmlspecialchars($event['event_title']) ?></strong><br>
                                                        <small class="text-muted">
                                                            <?= date('M d, Y', strtotime($event['event_date'])) ?>
                                                            (<?= date('h:i A', strtotime($event['start_time'])) ?> - <?= date('h:i A', strtotime($event['end_time'])) ?>)
                                                        </small>
                                                    </div>
                                                    <span class="badge bg-warning text-dark">
                                                        <?= htmlspecialchars($event['status']) ?>
                                                    </span>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted text-center">No upcoming events this week</p>
                                <?php endif; ?>
                            </div>
                            </div>

                            <div class="d-flex justify-content-center mt-2">
                                <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
                            </div>
                        </div>    
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h5 class="px-0"><i class="bi bi-folder-fill text-primary"></i> Project's Overview</h5>
            <div class="d-flex justify-content-between align-items-center border-bottom">


                <ul class="nav nav-tabs tabletabs mb-3" id="taskTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button id="pendingTab"
                            class="nav-links tablee task active link d-flex align-items-center gap-1 btn btn-sm"
                            data-bs-toggle="tab" data-bs-target="#pending" type="button">
                            <ion-icon name="hourglass-outline"></ion-icon> Pending
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button id="ongoingTab"
                            class="nav-links tablee task link d-flex align-items-center gap-1 btn btn-sm"
                            data-bs-toggle="tab" data-bs-target="#ongoing" type="button">
                            <ion-icon name="checkmark-circle-outline"></ion-icon> Ongoing
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

            <table class="table table-striped align-middle">

            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">Date Started</th>
                <th scope="col">Project</th>
                <th class="mid" scope="col">Status</th>
                </tr>
            </thead>
            <tbody id="projectTableBody"></tbody>
            </table>
        </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

 let currentPage = 1;

function loadPage(page) {
    fetch('fetch_upcoming.php?page=' + page)
        .then(res => res.json())
        .then(data => {

            document.getElementById('upcoming-container').innerHTML = data.html;

            renderPagination(data.page, data.totalPages);
        });
}

function renderPagination(page, totalPages) {
    let pagination = '';

    pagination += `
        <li class="page-item ${page <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadPage(${page - 1})">&laquo;</a>
        </li>
    `;

    for (let i = 1; i <= totalPages; i++) {
        pagination += `
            <li class="page-item ${i === page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadPage(${i})">${i}</a>
            </li>
        `;
    }

    pagination += `
        <li class="page-item ${page >= totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadPage(${page + 1})">&raquo;</a>
        </li>
    `;

    document.getElementById('pagination').innerHTML = pagination;
}

document.addEventListener("DOMContentLoaded", function () {
    loadPage(1);
});

let currentStatus = 'pending';

document.addEventListener("DOMContentLoaded", function() {

const params = new URLSearchParams(window.location.search);

if (params.get('login') === 'success') {
    Swal.fire({
        icon: 'success',
        title: 'Login Successful',
        text: 'Welcome back!',
        timer: 1500,
        showConfirmButton: false
    });
}

});

document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('progressLineChart').getContext('2d');

    // ✅ DATA FROM PHP
    const labels = <?php
    $allDays = array_unique(array_merge(array_keys($pending), array_keys($completed)));
    sort($allDays);
    echo json_encode($allDays);
    ?>;

    const pendingData = <?php echo json_encode(array_values($pending)); ?>;
    const completedData = <?php echo json_encode(array_values($completed)); ?>;

    // ✅ GRADIENTS (same style as your sample)
    const gradientBlue = ctx.createLinearGradient(0, 0, 0, 250);
    gradientBlue.addColorStop(0, 'rgba(13, 110, 253, 0.4)');
    gradientBlue.addColorStop(1, 'rgba(13, 110, 253, 0)');

    const gradientRed = ctx.createLinearGradient(0, 0, 0, 250);
    gradientRed.addColorStop(0, 'rgba(220, 53, 69, 0.4)');
    gradientRed.addColorStop(1, 'rgba(220, 53, 69, 0)');

    const gradientGreen = ctx.createLinearGradient(0, 0, 0, 250);
    gradientGreen.addColorStop(0, 'rgba(25, 135, 84, 0.4)');
    gradientGreen.addColorStop(1, 'rgba(25, 135, 84, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pending',
                    data: pendingData,
                    borderColor: '#dc3545',
                    backgroundColor: gradientRed,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#dc3545',
                    pointRadius: 3,
                    pointHoverRadius: 6
                },
                {
                    label: 'Completed',
                    data: completedData,
                    borderColor: '#198754',
                    backgroundColor: gradientGreen,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#198754',
                    pointRadius: 3,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // ✅ matches your sample (clean look)
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    border: { display: false }
                },
                x: {
                    grid: {
                        display: false
                    },
                    border: { display: false },
                    ticks: {
                    font: {
                        size: 10 // 👈 change this value
                    }
                }
                }
            }
        }
    });
});

<?php if (isset($_GET['login']) && $_GET['login'] == 'success'): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</script>
<?php endif; ?>
</script>
<script>
function loadProjects(page = 1, status = currentStatus) {

    currentPage = page;
    currentStatus = status;

    fetch(`fetch_projects.php?page=${page}&status=${status}`)
        .then(res => res.json())
        .then(res => {

            let html = '';

            if (res.data.length === 0) {
                html = `
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No projects found
                        </td>
                    </tr>
                `;
            } else {

                res.data.forEach((p, i) => {
                    html += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${p.event_date}</td>
                        <td>${p.event_title}</td>
                        <td>
                            <select class="form-select form-select-sm project-status"
                                    data-id="${p.id}">
                                <option value="pending" ${p.project_status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="ongoing" ${p.project_status === 'ongoing' ? 'selected' : ''}>Ongoing</option>
                                <option value="completed" ${p.project_status === 'completed' ? 'selected' : ''}>Completed</option>
                            </select>
                        </td>
                    </tr>`;
                });

            }

            document.getElementById('projectTableBody').innerHTML = html;

            attachStatusEvents();
        })
        .catch(err => console.error(err));
}

function attachStatusEvents() {

    document.querySelectorAll('.project-status').forEach(select => {

        select.addEventListener('change', function () {

            fetch('update_project_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${this.dataset.id}&status=${this.value}`
            })
            .then(res => res.text())
            .then(result => {

                console.log(result);

                // reload current tab after update
                loadProjects(currentPage, currentStatus);

            });
        });

    });

}

document.addEventListener("DOMContentLoaded", () => {

    // default = pending tab
    loadProjects(1, 'pending');

    const pendingTab = document.getElementById('pendingTab');
    const ongoingTab = document.getElementById('ongoingTab');

    if (pendingTab) {
        pendingTab.addEventListener('click', () => {
            loadProjects(1, 'pending');
        });
    }

    if (ongoingTab) {
        ongoingTab.addEventListener('click', () => {
            loadProjects(1, 'ongoing');
        });
    }

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



<script>
    if ("Notification" in window) {

    if (Notification.permission === "default") {
        Notification.requestPermission();
    }

}

setInterval(() => {

    fetch("check_reminders.php")
        .then(res => res.json())
        .then(data => {

            data.forEach(event => {

                new Notification(event.title, {
                    body: event.body,
                    icon: "finallogo.png"
                });

            });

        });

}, 300000); // every 5 minutes
    </script>


</body>
</html>



<!-- event  -->
 <!-- bi bi-megaphone -->

 <!-- total -->
 <!-- bi bi-calendar-check -->

