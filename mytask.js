document.addEventListener("DOMContentLoaded", function () {
    loadMyTasks();
});

window.loadMyTasks = async function () {

    try {

        const res = await fetch("get_weekly_tasks.php");
        const tasks = await res.json();

        const ongoingCont = document.getElementById("mytaskOngoingContainer");
        const pendingCont = document.getElementById("mytaskPendingContainer");
        const completedCont = document.getElementById("mytaskCompletedContainer");
        const emptyState = document.getElementById("mytaskEmptyState");

        ongoingCont.innerHTML = "";
        pendingCont.innerHTML = "";
        completedCont.innerHTML = "";

        let hasTasks = false;

        tasks.forEach((task) => {

            // CHECK ASSIGNED USERS
            let assignedUsers = [];

            try {
                assignedUsers = JSON.parse(task.person_in_charge || "[]")
                    .map(id => String(id).trim());
            } catch (e) {
                assignedUsers = [];
            }

            // ONLY SHOW TASKS ASSIGNED TO CURRENT USER
            if (!assignedUsers.includes(CURRENT_USER_ID)) {
                return;
            }

            hasTasks = true;

            // PROCESS CHECKLIST
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

            // STATUS
            let status = (task.status || "pending").toLowerCase();

            const now = new Date();

            const [y, m, d] = task.event_date.split('-');

            const [sh = 0, sm = 0] = (task.start_time || "00:00").split(':');

            const [eh = 23, em = 59] = (task.end_time || "23:59").split(':');

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

            const counterText = total === 0
                ? "N/A"
                : (doneCount === total
                    ? "Done"
                    : `<span class="fw-bold" style="font-size:14px;">${doneCount}</span><span style="font-size:10px;"> / ${total}</span>`);

            const collapseId = `mytask${task.id}`;

            const checklistHTML = checklist.map((item, i) => `

                <div class="form-check mb-1">

                    <input class="form-check-input shadow-none checklist-item"
                        type="checkbox"
                        data-task-id="${task.id}"
                        data-index="${i}"
                        ${item.done == 1 ? "checked" : ""}
                        style="margin-top:7px;">

                    <label class="form-check-label small">
                        ${item.title}
                    </label>

                </div>

            `).join("");

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
                                ${formatDayTime(task.event_date, task.start_time, task.end_time)}
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
        toggleWrapper("mytaskOngoingWrapper", ongoingCont);
        toggleWrapper("mytaskPendingWrapper", pendingCont);
        toggleWrapper("mytaskCompletedWrapper", completedCont);

        // EMPTY STATE
        if (
            ongoingCont.innerHTML.trim() === "" &&
            pendingCont.innerHTML.trim() === "" &&
            completedCont.innerHTML.trim() === ""
        ) {
            emptyState.classList.remove("d-none");
        } else {
            emptyState.classList.add("d-none");
        }

    } catch (err) {
        console.error("MyTask Load Error:", err);
    }
};

function toggleWrapper(id, container) {

    const el = document.getElementById(id);

    if (el) {
        el.style.display =
            container.innerHTML.trim() === ""
                ? "none"
                : "block";
    }
}

function formatDayTime(date, startTime, endTime) {

    const [y, m, d] = date.split('-');

    const dayName = new Date(y, m - 1, d)
        .toLocaleDateString('en-US', { weekday: 'long' });

    const fTime = (t) => {

        if (!t) return "";

        let [h, min] = t.split(':');

        let suffix = h >= 12 ? 'PM' : 'AM';

        return `${h % 12 || 12}:${min} ${suffix}`;
    };

    return endTime
        ? `${dayName} • ${fTime(startTime)} - ${fTime(endTime)}`
        : `${dayName} • ${fTime(startTime)}`;
}