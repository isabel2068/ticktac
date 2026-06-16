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

?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calendar</title>
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
                            <a class="nav-link" href="dashboard.php"><i class="bi bi-pie-chart me-2"></i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#"><i class="bi bi-calendar3 me-2"></i>Calendar</a>
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
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 bg-white sticky-top border-bottom">
                
                <div class="d-flex align-items-center">
                    <h3 class="mb-0">Calendar</h3>
                </div>
                
                <div class="d-flex align-items-center gap-4">
                    <div class="text-center d-flex align-items-center gap-2 modern-pagination">
                        <div class="d-flex gap-2">
                            <button id="prevMonth" class="btn btn-sm btn-light shadow-sm">
                                <i class="bi bi-chevron-left"></i>
                            </button>

                            <span id="monthDisplay" class="center-text px-3 py-1 rounded-pill"></span>

                            <button id="nextMonth" class="btn btn-sm btn-light shadow-sm">
                                <i class="bi bi-chevron-right"></i>
                            </button>

                            <button class="btn bg-trans py-0 px-2 fs-5 border-0 shadow-none rounded-circle" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling"><i class="bi bi-calendar-check"></i></button>
                        </div>
                    </div>

                    <div class="offcanvas offcanvas-end bg-white" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
                        <div class="offcanvas-header border-bottom">
                            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">
                                Events
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        
                        <div class="offcanvas-body">
                            <ul class="nav nav-pills nav-fill mb-4 bg-light p-1 rounded-2" id="eventTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link cal active py-1 px-0" id="weekly-tab" data-bs-toggle="tab" data-bs-target="#weekly" type="button" role="tab">Weekly</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link cal py-1 px-0" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab">Monthly</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link cal py-1 px-0" id="mytask-tab" data-bs-toggle="tab" data-bs-target="#mytask" type="button" role="tab">My Task</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="eventTabContent">
                                <div class="tab-pane fade show active" id="weekly" role="tabpanel">
                                    <div class="list-group list-group-flush" id="weeklyList">
                                        
                                        <div id="ongoingWrapper" class="mb-3">
                                            <div class="d-flex align-items-center px-2 mb-2">
                                                <div class="spinner-grow spinner-grow-sm text-primary me-2" role="status" style="width: 10px; height: 10px;"></div>
                                                <h6 class="text-primary fw-bold mb-0 small text-uppercase">Ongoing Events</h6>
                                            </div>
                                            <div id="ongoingContainer"></div>
                                        </div>

                                        <div id="pendingWrapper" class="mb-2">
                                            <button class="btn btn-sm btn-light border w-100 d-flex justify-content-between align-items-center py-2 shadow-none button-hover-custom" 
                                                    type="button" data-bs-toggle="collapse" data-bs-target="#pendingCollapse">
                                                <span class="fw-bold text-danger small"><i class="bi bi-clock-history me-2"></i>PENDING EVENTS</span>
                                                <i class="bi bi-chevron-down"></i>
                                            </button>
                                            <div class="collapse" id="pendingCollapse"> <div id="pendingContainer" class="pt-2"></div>
                                            </div>
                                        </div>

                                        <div id="completedWrapper" class="mb-2">
                                            <button class="btn btn-sm btn-light border w-100 d-flex justify-content-between align-items-center py-2 shadow-none button-hover-custom" 
                                                    type="button" data-bs-toggle="collapse" data-bs-target="#completedCollapse">
                                                <span class="fw-bold text-success small"><i class="bi bi-check-circle me-2"></i>COMPLETED EVENTS</span>
                                                <i class="bi bi-chevron-down"></i>
                                            </button>
                                            <div class="collapse" id="completedCollapse"> <div id="completedContainer" class="pt-2"></div>
                                            </div>
                                        </div>
                                        
                                        <div id="emptyState" class="text-center py-4 d-none">
                                            <p class="text-muted small">No weekly events found.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="monthly" role="tabpanel">
                                    <div class="list-group list-group-flush" id="monthlyList">
                                        
                                        <div id="monthlyOngoingWrapper" class="mb-3">
                                            <div class="d-flex align-items-center px-2 mb-2">
                                                <div class="spinner-grow spinner-grow-sm text-primary me-2" role="status" style="width: 10px; height: 10px;"></div>
                                                <h6 class="text-primary fw-bold mb-0 small text-uppercase">Ongoing Events</h6>
                                            </div>
                                            <div id="monthlyOngoingContainer"></div>
                                        </div>

                                        <div id="monthlyPendingWrapper" class="mb-2">
                                            <button class="btn btn-sm btn-light border w-100 d-flex justify-content-between align-items-center py-2 shadow-none button-hover-custom" 
                                                    type="button" data-bs-toggle="collapse" data-bs-target="#monthlyPendingCollapse">
                                                <span class="fw-bold text-danger small"><i class="bi bi-clock-history me-2"></i>PENDING EVENTS</span>
                                                <i class="bi bi-chevron-down"></i>
                                            </button>
                                            <div class="collapse" id="monthlyPendingCollapse"> 
                                                <div id="monthlyPendingContainer" class="pt-2"></div>
                                            </div>
                                        </div>

                                        <div id="monthlyCompletedWrapper" class="mb-2">
                                            <button class="btn btn-sm btn-light border w-100 d-flex justify-content-between align-items-center py-2 shadow-none button-hover-custom" 
                                                    type="button" data-bs-toggle="collapse" data-bs-target="#monthlyCompletedCollapse">
                                                <span class="fw-bold text-success small"><i class="bi bi-check-circle me-2"></i>COMPLETED EVENTS</span>
                                                <i class="bi bi-chevron-down"></i>
                                            </button>
                                            <div class="collapse" id="monthlyCompletedCollapse"> 
                                                <div id="monthlyCompletedContainer" class="pt-2"></div>
                                            </div>
                                        </div>

                                        <div id="monthlyEmptyState" class="text-center py-4 d-none">
                                            <p class="text-muted small">No monthly events found.</p>
                                        </div>
                                        
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="mytask" role="tabpanel">
                                    <div class="list-group list-group-flush" id="mytaskList">
                                        
                                        <!-- Ongoing Tasks Section -->
                                        <div id="mytaskOngoingWrapper" class="mb-3">
                                            <div class="d-flex align-items-center px-2 mb-2">
                                                <div class="spinner-grow spinner-grow-sm text-primary me-2" role="status" style="width: 10px; height: 10px;"></div>
                                                <h6 class="text-primary fw-bold mb-0 small text-uppercase">Ongoing Tasks</h6>
                                            </div>
                                            <div id="mytaskOngoingContainer"></div>
                                        </div>

                                        <!-- Pending Tasks Section -->
                                        <div id="mytaskPendingWrapper" class="mb-2">
                                            <button class="btn btn-sm btn-light border w-100 d-flex justify-content-between align-items-center py-2 shadow-none button-hover-custom" 
                                                    type="button" data-bs-toggle="collapse" data-bs-target="#mytaskPendingCollapse">
                                                <span class="fw-bold text-danger small"><i class="bi bi-clock-history me-2"></i>PENDING TASKS</span>
                                                <i class="bi bi-chevron-down"></i>
                                            </button>
                                            <div class="collapse" id="mytaskPendingCollapse"> 
                                                <div id="mytaskPendingContainer" class="pt-2"></div>
                                            </div>
                                        </div>

                                        <!-- Completed Tasks Section -->
                                        <div id="mytaskCompletedWrapper" class="mb-2">
                                            <button class="btn btn-sm btn-light border w-100 d-flex justify-content-between align-items-center py-2 shadow-none button-hover-custom" 
                                                    type="button" data-bs-toggle="collapse" data-bs-target="#mytaskCompletedCollapse">
                                                <span class="fw-bold text-success small"><i class="bi bi-check-circle me-2"></i>COMPLETED TASKS</span>
                                                <i class="bi bi-chevron-down"></i>
                                            </button>
                                            <div class="collapse" id="mytaskCompletedCollapse"> 
                                                <div id="mytaskCompletedContainer" class="pt-2"></div>
                                            </div>
                                        </div>

                                        <!-- Empty State -->
                                        <div id="mytaskEmptyState" class="text-center py-4 d-none">
                                            <p class="text-muted small">No tasks found.</p>
                                        </div>
                                        
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="calendar-card">
                <div class="calendar-grid" id="calendarGrid"></div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>window.CURRENT_USER_ID = <?= json_encode($_SESSION['user_id']); ?>;</script>

<script src="calendar.js"></script>

<script src="task_sync.js"></script>
<script src="weekly.js"></script>
<script src="monthly.js"></script>
<script src="mytask.js"></script>



<script>
document.addEventListener("DOMContentLoaded", () => {

    // Refresh weekly tab when opened
    document.getElementById("weekly-tab")
        .addEventListener("shown.bs.tab", function () {
            loadWeeklyTasks();
        });

    // Refresh monthly tab when opened
    document.getElementById("monthly-tab")
        .addEventListener("shown.bs.tab", function () {
            loadMonthlyTasks();
        });

    // Refresh my task tab when opened
    document.getElementById("mytask-tab")
        .addEventListener("shown.bs.tab", function () {
            loadMyTasks();
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