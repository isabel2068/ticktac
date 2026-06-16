let currentPage = 1;
let currentStatus = "pending";

document.addEventListener("DOMContentLoaded", () => {
    loadProjects();

    document.querySelectorAll(".task").forEach(btn => {
        btn.addEventListener("click", function () {
            currentStatus = this.dataset.status;
            currentPage = 1;
            loadProjects();
        });
    });
});

function loadProjects() {
    fetch(`fetch_projects.php?page=${currentPage}&status=${currentStatus}`)
        .then(res => res.json())
        .then(res => {
            renderTable(res.data);
            updatePagination(res.total, res.limit);
        });
}

function renderTable(data) {
    const tbody = document.getElementById("projectTableBody");
    tbody.innerHTML = "";

    data.forEach((item, index) => {
        tbody.innerHTML += `
            <tr>
                <th>${index + 1}</th>
                <td>${item.event_date}</td>
                <td>${item.event_title}</td>
                <td>
                    <option value="pending" <?= $p['project_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="ongoing" <?= $p['project_status'] === 'ongoing' ? 'selected' : '' ?>>Ongoing</option>
                    <option value="completed" <?= $p['project_status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                </td>
            </tr>
        `;
    });

    attachStatusListeners();
}

function attachStatusListeners() {
    document.querySelectorAll(".statusSelect").forEach(select => {
        select.addEventListener("change", function () {
            fetch("update_project_status.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id=${this.dataset.id}&status=${this.value}`
            })
            .then(() => loadProjects());
        });
    });
}

function changePage(step) {
    currentPage += step;
    if (currentPage < 1) currentPage = 1;
    loadProjects();
}

function updatePagination(total, limit) {
    const pages = Math.ceil(total / limit);

    document.getElementById("pageInfo").innerText =
        `Page ${currentPage} of ${pages}`;

    document.getElementById("prevBtn").disabled = currentPage <= 1;
    document.getElementById("paginationNextBtn").disabled = currentPage >= pages;
}