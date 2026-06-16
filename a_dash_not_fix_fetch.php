<?php
require 'db.php';

// COUNT PENDING
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_tasks WHERE status = 'pending'");
$stmt->execute();
$pendingCount = $stmt->fetchColumn();

// COUNT ONGOING
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_tasks WHERE status = 'preparing'");
$stmt->execute();
$ongoingCount = $stmt->fetchColumn();

// COUNT COMPLETED
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_tasks WHERE status = 'completed'");
$stmt->execute();
$completedCount = $stmt->fetchColumn();

// SET TIMEZONE
date_default_timezone_set('Asia/Manila');

// GET ALL WEEKLY EVENTS (NO PAGINATION)
$stmt = $pdo->prepare("
    SELECT event_title, event_date, start_time, end_time, venue 
    FROM tbl_tasks
    WHERE event_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY event_date ASC, start_time ASC
");

$stmt->execute();
$upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
</head>
<body>

<style>
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
</style>

<nav class="navbar navbar-expand-lg bg-light border-bottom sticky-top border-body" data-bs-theme="light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><b>TICKTAC</b></a>
        <form class="d-flex col-md-3" role="search">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="d-flex align-items-center btn-door-animation" href="#">
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
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-body-tertiary sidebar collapse px-0 sticky-top" style="height: calc(100vh - 56px); top: 56px;">
            
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
                            <a class="nav-link" href="#"><i class="bi bi-calendar2-minus me-2"></i> Months</a>
                        </li>
                    </ul>
                </div>

                <div class="profile-section border-top p-3 mt-auto">
                    <div class="d-flex align-items-center gap-2">
                        <img src="default_profile/user.png" alt="Profile" width="40" height="40">
                        <div class="overflow-hidden">
                            <p class="mb-0 fw-bold text-dark text-truncate" style="font-size: 0.85rem;">Juan Dela Cruz</p>
                            <small class="text-muted d-block text-truncate" style="font-size: 0.7rem;">DTI PERSONNEL</small>
                        </div>
                    </div>
                </div>

            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h3 class="mb-0">Dashboard</h3>
            </div>

            <div class="row">
                <div class="col-12 col-md-4 col-lg-4 mb-3">
                    <div class="card pencard text-bg-light h-100">
                        <div class="card-header">
                            <i class="bi bi-1-circle-fill text-darkblue fs-6 px-2 py-1 rounded-1 lightblue opacity-50 me-1"></i> Pending Events
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

            <div class="row">
                <div class="col-12 col-md-6 col-lg-6 mb-3">
                    <div class="card text-bg-light h-100">
                        <div class="d-flex justify-content-between">
                            <div class="card-header">
                                <i class="bi bi-4-circle-fill text-darkblue fs-6 px-2 py-1 rounded-1 lightblue opacity-50 me-1"></i> Activity Progress Tracker
                            </div> 
                            <div class="p-custom">
                                <p class="card-text d-flex justify-content-end text-muted small">This month</p>
                            </div>                           
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div style="height: 230px; width: 100%; flex-grow: 1;">
                                <canvas id="progressLineChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-6 mb-3">
                    <div class="card text-bg-light h-100">
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
                                <div id="upcoming-container"></div>
                            </div>

                            <div class="d-flex justify-content-center mt-2">
                                <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
                            </div>
                        </div>    
                    </div>
                </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('progressLineChart').getContext('2d');
    
    // Create a beautiful gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(13, 110, 253, 0.4)');
    gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], // Timeline
            datasets: [{
                label: 'Activity level',
                data: [30, 55, 40, 75], // Your progress data
                borderColor: '#0d6efd',
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4, // This makes the line smooth/curvy
                pointBackgroundColor: '#0d6efd',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false } // Hide legend for a cleaner look
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)' // Subtle grid lines
                    },
                    border: { display: false }
                },
                x: {
                    grid: { display: false }, // Hide vertical lines for modern UI
                    border: { display: false }
                }
            }
        }
    });
});

<?php if (isset($_GET['login']) && $_GET['login'] == 'success'): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'success',
        title: 'Login Successful',
        text: 'Welcome back!',
        timer: 50000,
        showConfirmButton: false
    });
});
</script>
<?php endif; ?>
</script>
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

</script>



</body>
</html>

<!-- event  -->
 <!-- bi bi-megaphone -->

 <!-- total -->
 <!-- bi bi-calendar-check -->

