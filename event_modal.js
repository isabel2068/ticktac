/* =========================
   EVENT MODAL LOGIC
========================= */

function formatCreatedDate(datetime) {
    if (!datetime) return '';

    let date = new Date(datetime.replace(' ', 'T'));

    return date.toLocaleString('en-US', {
        month: '2-digit',
        day: '2-digit',
        year: '2-digit',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    }).replace(',', ' ,');
}

/* CHECKLIST RENDER */
function renderChecklist(json) {
    if (!json) return "<span class='text-muted'>No checklist available</span>";

    try {
        // ✅ Ensure proper parsing
            let data = [];

        try {
            data = typeof json === "string" ? JSON.parse(json) : json;
        } catch (e) {
            console.error("Invalid checklist JSON:", json);
            return "<span class='text-danger'>Checklist corrupted</span>";
        }

        let html = `<div class="list-group">`;

        data.forEach((item, index) => {

            // ✅ FIX: protect against undefined fields
            let title = item?.title ?? "Untitled Task";
            let done = item?.done ?? 0;

            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${title}</span>
                    <span class="badge ${done == 1 ? 'bg-success' : 'bg-danger'}">
                        ${done == 1 ? 'Done' : 'Pending'}
                    </span>
                </div>
            `;
        });

        html += `</div>`;
        return html;

    } catch (e) {
        console.error("Checklist parse error:", e, json);
        return "<span class='text-danger'>Checklist corrupted</span>";
    }
}

/* MODAL CONNECTOR (IMPORTANT: works with AJAX tables) */
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".viewBtn");
    if (!btn) return;

    document.getElementById("mTitle").innerText = btn.dataset.title;
    document.getElementById("mType").innerText = btn.dataset.type;
    document.getElementById("mDate").innerText = formatDateLong(btn.dataset.date);

    // TIME (fix fallback)
    let start = formatTime(btn.dataset.start);
    let end = formatTime(btn.dataset.end);

    document.getElementById("mTime").innerText =
        (btn.dataset.start && btn.dataset.end)
            ? `${start} - ${end}`
            : "N/A";

    document.getElementById("mVenue").innerText = safe(btn.dataset.venue);
    document.getElementById("mStatus").innerText = btn.dataset.status;

    document.getElementById("mCreated").innerText =
        formatCreatedDate(btn.dataset.created);

    /* PIC */
    let picData = JSON.parse(btn.dataset.pic || "[]");
    let picBox = document.getElementById("mPIC");

    if (picData.length === 0) {
        picBox.innerHTML = `<span class="text-muted">No person assigned</span>`;
    } else {
        picBox.innerHTML = picData.map(u => `
            <div title="${u.first_name} ${u.last_name}">
                ${u.profile_pic
                    ? `<img src="uploads/${u.profile_pic}" class="rounded-circle" width="34" height="34">`
                    : `<div class="pic-circle">${u.first_name[0]}</div>`
                }
            </div>
        `).join('');
    }

    /* CHECKLIST */
    document.getElementById("mChecklist").innerHTML =
        renderChecklist(btn.dataset.checklist);
});

function formatTime(time) {
    if (!time) return "N/A";

    let [h, m] = time.split(":");
    h = parseInt(h);

    let ampm = h >= 12 ? "PM" : "AM";
    h = h % 12 || 12;

    return `${h}:${m} ${ampm}`;
}

function formatDateLong(dateStr) {
    if (!dateStr) return "N/A";

    let date = new Date(dateStr + "T00:00:00");

    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric"
    });
}