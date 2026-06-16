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

<nav class="navbar navbar-expand-lg bg-light border-bottom border-body" data-bs-theme="light">
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
                        <i class="bi bi-door-closed-fill icon-closed fs-5"></i>
                        <i class="bi bi-door-open-fill icon-open fs-5" style="display: none;"></i>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-body-tertiary sidebar collapse px-0">
            <div class="position-sticky pt-3">
                <p class="d-flex justify-content-between align-items-center px-3 mb-1 overview">
                    Overview
                </p>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <span class="align-text-bottom"></span>
                            <i class="bi bi-pie-chart me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="calendar.php">
                            <i class="bi bi-calendar3 me-2"></i>Calendar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="bi bi-list-check me-2"></i>Events
                        </a>
                    </li>
                </ul>

                <p class="d-flex justify-content-between align-items-center mt-3 px-3 mb-1 overview">
                    Reports
                </p>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Report 1</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Report 2</a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h3 class="mb-0">Manage Events</h3>

                <!-- ADD TASK BUTTON -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                        <i class="bi bi-plus-circle"></i> Add Task
                    </button>
            </div>

                <!-- TABS -->
                <ul class="nav nav-tabs tabletabs" id="taskTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active link d-flex align-items-center justify-content-center gap-1" data-bs-toggle="tab" data-bs-target="#pending" type="button">   
                        <ion-icon name="hourglass-outline"></ion-icon>Pending
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link link d-flex align-items-center justify-content-center gap-1" data-bs-toggle="tab" data-bs-target="#ongoing" type="button">
                        <ion-icon name="sync-circle-outline"></ion-icon>Ongoing
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link link d-flex align-items-center justify-content-center gap-1" data-bs-toggle="tab" data-bs-target="#completed" type="button">
                        <ion-icon name="checkmark-circle-outline"></ion-icon>Completed
                        </button>
                    </li>
                </ul>

                <!-- TAB CONTENT -->
                <div class="tab-content bg-white border-top-0">

                    <!-- PENDING -->
                    <div class="tab-pane fade show active" id="pending">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="mid">No.</th>
                                        <th>Event</th>
                                        <th class="mid">Date</th>
                                        <th class="mid">Time</th>
                                        <th class="mid">Location</th>
                                        <th class="mid">Lead</th>
                                        <th class="mid">Checklist</th>
                                        <th class="mid">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php $no = 1; ?>
                                        <td class="mid"><?= $no++ ?></td>
                                        <td>Business Seminar</td>
                                        <td class="mid">2026-04-10</td>
                                        <td class="mid">8:00 AM - 12:00 PM</td>
                                        <td class="mid">DTI Office </td>
                                        <td class="mid">Juan Dela Cruz</td>
                                        <td class="mid">
                                            <button class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#checklistModal"
                                                onclick="openChecklist('Business Seminar')">

                                                <span id="progressText">3/5 Done</span>
                                            </button>
                                        </td>
                                        <td class="mid">
                                            <button class="btn btn-sm"><i class="bi bi-eye"></i></button>
                                            <button class="btn btn-sm"><i class="bi bi-pencil"></i></button>
                                            <button class="btn btn-sm"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ONGOING -->
                    <div class="tab-pane fade" id="ongoing">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="mid">No.</th>
                                        <th>Title</th>
                                        <th class="mid">Type</th>
                                        <th class="mid">Date</th>
                                        <th class="mid">Location</th>
                                        <th class="mid">Lead</th>
                                        <th class="mid">Checklist</th>
                                        <th class="mid">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php $no = 1; ?>
                                        <td class="mid"><?= $no++ ?></td>
                                        <td>Sample Ongoing Event</td>
                                        <td class="mid">Meeting</td>
                                        <td class="mid">2026-04-12</td>
                                        <td class="mid">DTI Office</td>
                                        <td class="mid">Maria Santos</td>
                                        <td class="mid">
                                            <button class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#checklistModal"
                                                onclick="openChecklist('Business Seminar')">

                                                <span id="progressText">3/5 Done</span>

                                            </button>
                                        </td>
                                        <td class="mid">
                                            <button class="btn btn-sm"><i class="bi bi-eye"></i></button>
                                            <button class="btn btn-sm"><i class="bi bi-pencil"></i></button>
                                            <button class="btn btn-sm"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
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
                                        <th class="mid">No.</th>
                                        <th>Event</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Lead</th>
                                        <th>Checklist</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <?php $no = 1; ?>
                                    <td class="mid"><?= $no++ ?></td>
                                    <td><?= $row['event_title'] ?></td>
                                    <td><?= $row['event_type'] ?></td>
                                    <td><?= $row['event_date'] ?></td>
                                    <td><?= $row['location'] ?></td>
                                    <td><?= $row['person_in_charge'] ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#checklistModal"
                                                onclick="openChecklist('Business Seminar')">

                                                <span id="progressText">Completed</span>

                                            </button>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info"><i class="bi bi-eye"></i></button>
                                        </td>
                                    </tr>
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
                                            <small class="text-muted">Step <span id="stepLabel">1</span> of 2</small>
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
                                                <input type="text" name="event_title" class="form-control" placeholder="Enter event title">
                                            </div>

                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label small text-muted">Event Type</label>
                                                    <select class="form-select" name="event_type">
                                                        <option value="" disabled selected>Select type</option>
                                                        <option>Training</option>
                                                        <option>Seminar</option>
                                                        <option>Meeting</option>
                                                        <option>Workshop</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label small text-muted">Person In Charge</label>
                                                    <input type="text" name="person_in_charge" class="form-control">
                                                </div>
                                            </div>

                                            <!-- DATE & TIME -->
                                            <div class="row g-2 mt-2">
                                                <div class="col-md-4">
                                                    <label class="form-label small text-muted">Date</label>
                                                    <input type="date" name="event_date" class="form-control">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label small text-muted">Starts at</label>
                                                    <input type="time" name="start_time" class="form-control">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label small text-muted">Ends at</label>
                                                    <input type="time" name="end_time" class="form-control">
                                                </div>
                                            </div>

                                            <!-- LOCATION -->
                                            <div class="mt-3">
                                                <label class="form-label small text-muted">Location</label>
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <select id="province" class="form-select"></select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select id="city" class="form-select"></select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select id="barangay" class="form-select"></select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- VENUE -->
                                            <div class="mt-3">
                                                <label class="form-label small text-muted">Venue (Optional)</label>
                                                <input type="text" name="venue" class="form-control" placeholder="Enter venue">
                                            </div>

                                            <input type="hidden" name="location" id="location">

                                        </div>

                                        <!-- STEP 2 -->
                                        <div id="step2" style="display:none;">

                                            <p class="small text-muted mb-2">Checklist</p>

                                            <div class="border rounded-3 p-3">

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" checked disabled>
                                                    <label class="form-check-label">Send Invitation Letters</label>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" checked disabled>
                                                    <label class="form-check-label">Print Attendance Sheets</label>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" checked disabled>
                                                    <label class="form-check-label">Prepare Venue</label>
                                                </div>

                                            </div>

                                            <!-- ADD ITEM -->
                                            <div class="input-group mt-3">
                                                <input type="text" id="customChecklistInput" class="form-control" placeholder="Add item...">
                                                <button class="btn btn-outline-primary" type="button" onclick="addChecklistItem()">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>

                                            <ul id="customChecklistList" class="list-group mt-2"></ul>

                                            <input type="hidden" name="checklist" id="checklist">
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
                     


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

let step = 1;
let checklist = [];

/* =========================
   SHOW STEPS
========================= */
function showStep() {
    document.getElementById("step1").style.display = step === 1 ? "block" : "none";
    document.getElementById("step2").style.display = step === 2 ? "block" : "none";

    document.getElementById("backBtn").style.display = step === 2 ? "inline-block" : "none";
    document.getElementById("nextBtn").style.display = step === 1 ? "inline-block" : "none";
    document.getElementById("saveBtn").style.display = step === 2 ? "inline-block" : "none";

    document.getElementById("stepProgress").style.width = (step * 50) + "%";

    document.getElementById("stepLabel").innerText = step;
    

    // 🔥 ADD THIS LINE (IMPORTANT)
    if (step === 2) {
        renderChecklist();
    }
}

/* =========================
   NEXT STEP (WITH VALIDATION)
========================= */
function nextStep() {

    let step1 = document.getElementById("step1");
    let inputs = step1.querySelectorAll("input[required], select[required]");

    let isValid = true;

    // Validate required fields
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add("is-invalid");
        } else {
            input.classList.remove("is-invalid");
        }
    });

    // Time validation
    let start = document.querySelector('[name="start_time"]');
    let end = document.querySelector('[name="end_time"]');

    if (start && end && start.value && end.value) {
        if (end.value <= start.value) {
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

    step = 2;
    showStep();
}

/* =========================
   PREVIOUS STEP
========================= */
function prevStep() {
    step = 1;
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
function renderChecklist() {
    let list = document.getElementById("customChecklistList");
    if (!list) return;

    list.innerHTML = "";

    checklist.forEach((item, index) => {
        let li = document.createElement("li");
        li.className = "list-group-item d-flex justify-content-between align-items-center";

        li.innerHTML = `
            <span>${item}</span>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeChecklist(${index})">X</button>
        `;

        list.appendChild(li);
    });
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

/* =========================
   FORM SUBMIT
========================= */
document.getElementById("taskForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let form = this;
    let checklistItems = [];

    // Default checklist
    document.querySelectorAll("#step2 .form-check-label").forEach(label => {
        checklistItems.push(label.innerText);
    });

    // Custom checklist
    checklist.forEach(item => {
        checklistItems.push(typeof item === "object" ? item.item : item);
    });

    document.getElementById("checklist").value = JSON.stringify(checklistItems);

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

/* =========================
   INIT STEP
========================= */
showStep();

/* =========================
   SUCCESS ALERT
========================= */
const urlParams = new URLSearchParams(window.location.search);

if (urlParams.get('success') === '1') {
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Task added successfully.',
        confirmButtonColor: '#0d6efd'
    });

    window.history.replaceState({}, document.title, window.location.pathname);
}

/* =========================
   PROVINCE DATA
========================= */
fetch('ph-province-list.json')
.then(res => res.json())
.then(data => {

    data.sort((a, b) => a.province.localeCompare(b.province));

    let provinceSelect = document.getElementById("province");
    provinceSelect.innerHTML = '<option value="">Select Province</option>';

    data.forEach(p => {
        provinceSelect.innerHTML += `<option value="${p.province}">${p.province}</option>`;
    });
});

/* =========================
   PROVINCE → CITY
========================= */
document.getElementById("province").addEventListener("change", function () {

    let province = this.value;

    document.getElementById("city").innerHTML = '<option value="">Select City / Municipality</option>';
    document.getElementById("barangay").innerHTML = '<option value="">Select Barangay</option>';

    fetch('ph-municipality-list.json')
    .then(res => res.json())
    .then(data => {

        let filtered = data
            .filter(m => m.province.trim().toLowerCase() === province.trim().toLowerCase())
            .sort((a, b) => a.municipality.localeCompare(b.municipality));

        let citySelect = document.getElementById("city");

        filtered.forEach(m => {
            citySelect.innerHTML += `<option value="${m.municipality}">${m.municipality}</option>`;
        });
    });
});

/* =========================
   CITY → BARANGAY
========================= */
document.getElementById("city").addEventListener("change", function () {

    let city = this.value;

    fetch('ph-brgy-list.json')
    .then(res => res.json())
    .then(data => {

        let filtered = data
            .filter(b => b.municipality.trim().toLowerCase() === city.trim().toLowerCase())
            .sort((a, b) => a.barangay.localeCompare(b.barangay));

        let brgySelect = document.getElementById("barangay");
        brgySelect.innerHTML = '<option value="">Select Barangay</option>';

        filtered.forEach(b => {
            brgySelect.innerHTML += `<option value="${b.barangay}">${b.barangay}</option>`;
        });
    });
});

/* =========================
   FINAL LOCATION BUILD
========================= */
document.getElementById("barangay").addEventListener("change", function () {

    let province = document.getElementById("province").value;
    let city = document.getElementById("city").value;
    let brgy = this.value;

    document.getElementById("location").value =
        `${brgy}, ${city}, ${province}`;
});

</script>
</body>
</html>