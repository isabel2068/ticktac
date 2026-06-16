document.addEventListener("DOMContentLoaded", function () {
    loadMonthlyTasks();
});

window.loadMonthlyTasks = async function () {

    try {

        const res = await fetch("get_monthly_tasks.php");

        // SAFE JSON PARSE
        const text = await res.text();

        let tasks;

        try {
            tasks = JSON.parse(text);
        } catch (e) {
            console.error("Invalid JSON from server:", text);
            return;
        }

        const ongoingCont = document.getElementById("monthlyOngoingContainer");
        const pendingCont = document.getElementById("monthlyPendingContainer");
        const completedCont = document.getElementById("monthlyCompletedContainer");
        const emptyState = document.getElementById("monthlyEmptyState");

        // CLEAR CONTAINERS
        if (ongoingCont) ongoingCont.innerHTML = "";
        if (pendingCont) pendingCont.innerHTML = "";
        if (completedCont) completedCont.innerHTML = "";

        // NO TASKS
        if (!tasks || !tasks.length) {

            toggleWrapper("monthlyOngoingWrapper", ongoingCont);
            toggleWrapper("monthlyPendingWrapper", pendingCont);
            toggleWrapper("monthlyCompletedWrapper", completedCont);

            if (emptyState) {
                emptyState.classList.remove("d-none");
            }

            return;
        }

        if (emptyState) {
            emptyState.classList.add("d-none");
        }

        tasks.forEach((task) => {

            // PERSON IN CHARGE CHECK
            let assignedUsers = [];

            try {

                assignedUsers = JSON.parse(task.person_in_charge || "[]")
                    .map(id => String(id).trim());

            } catch (e) {
                assignedUsers = [];
            }

            const canEditChecklist =
                assignedUsers.includes(String(CURRENT_USER_ID).trim());

            // CHECKLIST PARSE
            let checklist = [];

            try {

                const parsed = JSON.parse(task.checklist || "[]");

                checklist = parsed.map(item => {

                    if (typeof item === "string") {
                        return { title: item, done: 0 };
                    }

                    if (typeof item === "object" && item !== null) {

                        return {
                            title: item.title || "Untitled",
                            done: item.done == 1 ? 1 : 0
                        };
                    }

                    return null;

                }).filter(Boolean);

            } catch (e) {
                checklist = [];
            }

            let total = checklist.length;
            let doneCount = checklist.filter(c => c.done == 1).length;

            // STATUS LOGIC
            let status = (task.status || "pending").toLowerCase();

            const now = new Date();

            const [y, m, d] = task.event_date.split('-');

            const [sh = 0, sm = 0] =
                (task.start_time || "00:00").split(':');

            const [eh = 23, em = 59] =
                (task.end_time || "23:59").split(':');

            const start = new Date(y, m - 1, d, sh, sm);

            const end = new Date(y, m - 1, d, eh, em);

            if (status !== "completed") {

                if (now < start) {
                    status = "pending";
                }

                else if (now >= start && now <= end) {
                    status = "ongoing";
                }

                else if (now > end) {
                    status = "completed";
                }
            }

            // SYNC DB
            if (task.status !== status) {

                fetch("update_task_status.php", {
                    method: "POST",
                    headers: {
                        "Content-Type":
                            "application/x-www-form-urlencoded"
                    },
                    body: `id=${task.id}&status=${status}`
                });
            }

            // UI STYLING
            let statusClass = "bg-danger-subtle text-danger";
            let iconClass = "bi-hourglass-top";
            let colorClass = "text-danger";

            if (status === "ongoing") {

                statusClass = "bg-primary-subtle text-primary";
                iconClass = "bi-hourglass-split";
                colorClass = "text-primary";

            } else if (status === "completed") {

                statusClass = "bg-success-subtle text-success";
                iconClass = "bi-hourglass-bottom";
                colorClass = "text-success";
            }

            const counterText =
                total === 0
                    ? "N/A"
                    : (
                        doneCount === total
                            ? "Done"
                            : `<span class="fw-bold" style="font-size:14px;">${doneCount}</span><span style="font-size:10px;"> / ${total}</span>`
                    );

            const collapseId = `monthlyTask${task.id}`;

            // CHECKLIST HTML
            const checklistHTML = checklist.map((item, i) => `

                <div class="form-check mb-1">

                    <input class="form-check-input shadow-none checklist-item"
                           type="checkbox"
                           data-task-id="${task.id}"
                           data-index="${i}"
                           ${item.done == 1 ? "checked" : ""}
                           ${!canEditChecklist ? "disabled" : ""}
                           style="margin-top:7px;">

                    <label class="form-check-label small">
                        ${item.title}
                    </label>

                </div>

            `).join("");

            // TEMPLATE
            const html = `

                <div class="list-group-item border-cus bg-transparent borderbox px-0 mb-2">

                    <div class="d-flex align-items-start w-100">

                        <div class="status-icon-box me-3 mt-3 ${colorClass}">
                            <i class="bi ${iconClass} fs-5 bg-light border p-1 px-2 rounded-circle"></i>
                        </div>

                        <div class="flex-grow-1">

                            <h6 class="mb-0 text-capitalize" style="font-size:14px;">
                                ${task.event_title}
                            </h6>

                            <small class="text-muted d-block mb-1" style="font-size:11.5px;">
                                ${formatDate(task.event_date)} • ${formatTime(task.start_time)}
                            </small>

                            <span class="badge status-badge ${statusClass}">
                                ${status}
                            </span>

                        </div>

                        <div class="d-flex flex-column align-items-end ms-3">

                            <button class="btn btn-sm btn-light border-0 shadow-none rounded-circle mb-3"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#${collapseId}">

                                <i class="bi bi-chevron-down toggle-icon"></i>

                            </button>

                            <div class="d-flex align-items-center gap-1 mt-1">

                                <small class="text-muted small" style="font-size:10px;">
                                    Checklist:
                                </small>

                                <small class="text-muted fw-bold task-counter" style="font-size:10px;">
                                    ${counterText}
                                </small>

                            </div>

                        </div>

                    </div>

                    <div class="collapse" id="${collapseId}">

                        <div class="card card-body bg-light border-0 py-2 mt-2">

                            ${checklistHTML || `<small class="text-muted">No checklist items</small>`}

                        </div>

                    </div>

                </div>

            `;

            // APPEND TO CORRECT CONTAINER
            if (status === "ongoing") {
                ongoingCont.innerHTML += html;
            }

            else if (status === "pending") {
                pendingCont.innerHTML += html;
            }

            else if (status === "completed") {
                completedCont.innerHTML += html;
            }

        });

        // HIDE EMPTY WRAPPERS
        toggleWrapper("monthlyOngoingWrapper", ongoingCont);
        toggleWrapper("monthlyPendingWrapper", pendingCont);
        toggleWrapper("monthlyCompletedWrapper", completedCont);

        // EMPTY STATE
        if (
            ongoingCont.innerHTML.trim() === "" &&
            pendingCont.innerHTML.trim() === "" &&
            completedCont.innerHTML.trim() === ""
        ) {

            if (emptyState) {
                emptyState.classList.remove("d-none");
            }

        } else {

            if (emptyState) {
                emptyState.classList.add("d-none");
            }
        }

    } catch (err) {
        console.error("Monthly load error:", err);
    }
};

// CHECKLIST CHANGE
document.addEventListener("change", async function (e) {

    if (!e.target.classList.contains("checklist-item")) {
        return;
    }

    const checkbox = e.target;

    const taskId = checkbox.dataset.taskId;
    const index = checkbox.dataset.index;
    const isChecked = checkbox.checked ? 1 : 0;

    // UPDATE DB
    fetch("update_checklist.php", {
        method: "POST",
        headers: {
            "Content-Type":
                "application/x-www-form-urlencoded"
        },
        body: `task_id=${taskId}&index=${index}&done=${isChecked}`
    });

    // UPDATE COUNTER UI
    const card = checkbox.closest(".list-group-item");

    if (!card) return;

    const checkboxes = card.querySelectorAll(".checklist-item");

    const total = checkboxes.length;

    const done = Array.from(checkboxes)
        .filter(cb => cb.checked).length;

    const counter = card.querySelector(".task-counter");

    if (counter) {

        if (total === 0) {

            counter.textContent = "N/A";

        } else if (done === total) {

            counter.textContent = "Done";

        } else {

            counter.innerHTML =
                `<span class="fw-bold" style="font-size:14px;">${done}</span>
                 <span style="font-size:10px;"> / ${total}</span>`;
        }
    }
});

// TOGGLE EMPTY WRAPPERS
function toggleWrapper(id, container) {

    const el = document.getElementById(id);

    if (el) {

        el.style.display =
            container.innerHTML.trim() === ""
                ? "none"
                : "block";
    }
}

// FORMAT TIME
function formatTime(t) {

    if (!t) return "";

    let [h, min] = t.split(':');

    let suffix = h >= 12 ? 'PM' : 'AM';

    return `${h % 12 || 12}:${min} ${suffix}`;
}

// FORMAT DATE
function formatDate(date) {

    const d = new Date(date);

    return d.toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    });
}