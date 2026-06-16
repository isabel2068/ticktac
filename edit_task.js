document.addEventListener("click", function (e) {

    if (e.target.closest(".editBtn")) {

        let btn = e.target.closest(".editBtn");
        let id = btn.dataset.id;

        fetch("get_single_task.php?id=" + id)
            .then(res => res.json())
            .then(data => {

                document.getElementById("edit_id").value = data.id;
                document.getElementById("edit_title").value = data.event_title;

                // dropdown
                document.getElementById("edit_type").value = data.event_type;

                document.getElementById("edit_venue").value = data.venue;

                // flatpickr values (IMPORTANT: use setDate)
                editDatePicker.setDate(data.event_date, true, "Y-m-d");

                editStartPicker.setDate(data.start_time, true);
                editEndPicker.setDate(data.end_time, true);

                new bootstrap.Modal(document.getElementById("editTaskModal")).show();
            });
    }
});

document.getElementById("editTaskForm").addEventListener("submit", function(e){
    e.preventDefault();

    let form = this;

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to update this task?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#0d6efd",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, update it"
    }).then((result) => {

        if (result.isConfirmed) {

            fetch("update_task.php", {
                method: "POST",
                body: new FormData(form)
            })
            .then(res => res.text())
            .then(res => {

                if (res === "success") {

                    Swal.fire({
                        icon: "success",
                        title: "Updated!",
                        text: "Task updated successfully",
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // optional: close modal first
                    bootstrap.Modal.getInstance(
                        document.getElementById("editTaskModal")
                    ).hide();

                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            });

        }
    });
});


document.addEventListener("DOMContentLoaded", function () {
    editDatePicker = flatpickr("#edit_date", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "m-d-Y",
        allowInput: true
    });

    editStartPicker = flatpickr("#edit_start", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
        time_24hr: false
    });

    editEndPicker = flatpickr("#edit_end", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
        time_24hr: false
    });
});