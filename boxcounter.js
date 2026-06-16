function updateStatusUI() {
    const statusItems = document.querySelectorAll('.list-group-item');

    statusItems.forEach(item => {
        // --- 1. ICON & COLOR LOGIC ---
        const badge = item.querySelector('.status-badge');
        const iconContainer = item.querySelector('.status-icon-container');
        const icon = iconContainer?.querySelector('i');
        
        if (badge && iconContainer && icon) {
            const status = badge.textContent.trim().toLowerCase();

            badge.className = 'badge status-badge mb-1';
            iconContainer.className = 'status-icon-box me-3 status-icon-container';
            icon.className = 'bi fs-5';

            if (status === 'pending') {
                badge.classList.add('bg-danger-subtle', 'text-danger');
                iconContainer.classList.add('text-danger');
                icon.classList.add('bi-hourglass-top');
            } 
            else if (status === 'preparing') {
                badge.classList.add('bg-primary-subtle', 'text-primary');
                iconContainer.classList.add('text-primary');
                icon.classList.add('bi-hourglass-split');
            } 
            else if (status === 'completed') {
                badge.classList.add('bg-success-subtle', 'text-success');
                iconContainer.classList.add('text-success');
                icon.classList.add('bi-hourglass-bottom');
            }
        }

        // --- 2. TASK COUNTER LOGIC ---
        const checkboxes = item.querySelectorAll('.form-check-input');
        const counterDisplay = item.querySelector('.task-counter');
        
        if (checkboxes.length > 0 && counterDisplay) {
            const totalTasks = checkboxes.length;
            const completedTasks = Array.from(checkboxes).filter(cb => cb.checked).length;
            
            // Check if all tasks are done
            if (completedTasks === totalTasks) {
                counterDisplay.textContent = "Done";
                counterDisplay.classList.replace('text-muted', 'text-success');
            } else {
                counterDisplay.textContent = `${completedTasks}/${totalTasks}`;
                counterDisplay.classList.replace('text-success', 'text-muted');
            }
        }
    });
}

// Run on page load
document.addEventListener('DOMContentLoaded', () => {
    updateStatusUI();

    // Listen for any checkbox changes
    document.getElementById('eventTabContent').addEventListener('change', (e) => {
        if (e.target.classList.contains('form-check-input')) {
            updateStatusUI();
        }
    });
});