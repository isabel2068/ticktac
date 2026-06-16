async function syncTaskUpdate(taskId, index, done) {
    try {
        await fetch("update_checklist.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `task_id=${taskId}&index=${index}&done=${done}`
        });

        // Reload both tabs
        if (typeof loadWeeklyTasks === "function") {
            loadWeeklyTasks();
        }

        if (typeof loadMonthlyTasks === "function") {
            loadMonthlyTasks();
        }

    } catch (err) {
        console.error("Checklist sync failed:", err);
    }
}