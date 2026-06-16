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
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 bg-white sticky-top border-bottom">
                
                <div class="d-flex align-items-center">
                    <h3 class="mb-0 me-3">Calendar</h3>
                    
                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle d-flex align-items-center" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                        <ul class="dropdown-menu shadow" aria-labelledby="filterDropdown">
                            <li><h6 class="dropdown-header">Event Type</h6></li>
                            <li><a class="dropdown-item" href="#">Training</a></li>
                            <li><a class="dropdown-item" href="#">Seminar</a></li>
                            <li><a class="dropdown-item" href="#">Meeting</a></li>
                            <li><a class="dropdown-item" href="#">Workshop</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item active" href="#">All</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-4">
                    <div class="text-center d-flex align-items-center gap-3">
                        <div class="d-flex gap-2">
                            <button id="prevMonth" class="btn btn-sm bg-trans border-0 shadow-none p-0 fs-5">
                                <i class="bi bi-chevron-left"></i>
                            </button>

                        <h4 id="monthDisplay" class="mb-0"></h4>

                            <button id="nextMonth" class="btn btn-sm bg-trans border-0 shadow-none p-0 fs-5">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <button class="btn bg-trans py-0 px-3 fs-5 border-0 shadow-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling"><i class="bi bi-calendar-check"></i></button>

                    <div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
                        <div class="offcanvas-header border-bottom">
                            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">
                                <i class="bi bi-calendar-check me-2"></i>Events
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        
                        <div class="offcanvas-body">
                            <ul class="nav nav-pills nav-fill mb-4 bg-light p-1 rounded-2" id="eventTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-1" id="weekly-tab" data-bs-toggle="tab" data-bs-target="#weekly" type="button" role="tab">Weekly</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab">Monthly</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="eventTabContent">
                                <div class="tab-pane fade show active" id="weekly" role="tabpanel">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item bg-transparent borderbox px-0">
                                            <div class="d-flex align-items-start w-100">
                                                
                                                <div class="status-icon-box me-3 status-icon-container">
                                                    <i class="fs-5"></i>
                                                </div>

                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0 fw-bold mt-0">Business Seminar</h6>
                                                    <small class="text-muted d-block">Friday • 8:00 AM</small>
                                                    <span class="badge status-badge mb-1">Pending</span>
                                                </div>

                                                <div class="d-flex flex-column align-items-end ms-3">
                                                    <button class="btn btn-sm btn-light border-0 shadow-none rounded-circle mb-3 mt-0" 
                                                            type="button" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#checklistEvent1">
                                                        <i class="bi bi-chevron-down toggle-icon"></i>
                                                    </button>
                                                    
                                                    <div class="d-flex align-items-center gap-1">
                                                        <small class="text-muted small">Checklist:</small>
                                                        <small class="text-muted fw-bold task-counter"></small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="collapse" id="checklistEvent1">
                                                <div class="card card-body bg-light border-0 py-2 mt-2">
                                                    <div class="form-check mb-1">
                                                        <input class="form-check-input shadow-none" type="checkbox" id="step1">
                                                        <label class="form-check-label small" for="step1">Prepare presentation slides</label>
                                                    </div>
                                                    <div class="form-check mb-1">
                                                        <input class="form-check-input shadow-none" type="checkbox" id="step2">
                                                        <label class="form-check-label small" for="step2">Confirm guest speakers</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input shadow-none" type="checkbox" id="step3">
                                                        <label class="form-check-label small" for="step3">Print attendance sheet</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="monthly" role="tabpanel">
                                    <div class="list-group list-group-flush">
                                        
                                        <div class="list-group-item bg-transparent borderbox px-0">
                                            <div class="d-flex align-items-start w-100">
                                                <div class="status-icon-box me-3 status-icon-container">
                                                    <i class="fs-5"></i>
                                                </div>

                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0 fw-bold mt-0">Mayor's Office Audit</h6>
                                                    <small class="text-muted d-block">April 24, 2026</small>
                                                    <span class="badge status-badge mb-1">Pending</span>
                                                </div>

                                                <div class="d-flex flex-column align-items-end ms-3">
                                                    <button class="btn btn-sm btn-light border-0 shadow-none rounded-circle mb-3 mt-0" 
                                                            type="button" data-bs-toggle="collapse" data-bs-target="#checklistMonthly1">
                                                        <i class="bi bi-chevron-down toggle-icon"></i>
                                                    </button>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <small class="text-muted small">Checklist:</small>
                                                        <small class="text-muted fw-bold task-counter"></small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="collapse" id="checklistMonthly1">
                                                <div class="card card-body bg-light border-0 py-2 mt-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input shadow-none" type="checkbox" id="m1_step1">
                                                        <label class="form-check-label small" for="m1_step1">Review document logs</label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="list-group-item bg-transparent borderbox px-0">
                                            <div class="d-flex align-items-start w-100">
                                                <div class="status-icon-box me-3 status-icon-container">
                                                    <i class="fs-5"></i>
                                                </div>

                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0 fw-bold mt-0">Charity Gala</h6>
                                                    <small class="text-muted d-block">April 05, 2026</small>
                                                    <span class="badge status-badge mb-1">Completed</span>
                                                </div>

                                                <div class="d-flex flex-column align-items-end ms-3">
                                                    <button class="btn btn-sm btn-light border-0 shadow-none rounded-circle mb-3 mt-0" 
                                                            type="button" data-bs-toggle="collapse" data-bs-target="#checklistMonthly2">
                                                        <i class="bi bi-chevron-down toggle-icon"></i>
                                                    </button>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <small class="text-muted small">Checklist:</small>
                                                        <small class="text-muted fw-bold task-counter">1/1</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="collapse" id="checklistMonthly2">
                                                <div class="card card-body bg-light border-0 py-2 mt-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input shadow-none" type="checkbox" id="m2_step1" checked>
                                                        <label class="form-check-label small" for="m2_step1">Confirm venue booking</label>
                                                    </div>
                                                </div>
                                            </div>
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
<script src="calendar.js"></script>
<script src="boxcounter.js"></script>
</body>
</html>