// 1. GLOBAL STATE - Keeps track of which month the user is viewing
let currentView = new Date();
currentView.setDate(1); // Ensures calendar starts on the 1st day of current month

async function renderCalendar() {
    const calendarGrid = document.getElementById('calendarGrid');
    const monthDisplay = document.getElementById('monthDisplay');
    if (!calendarGrid) return;

    // Update the Month Display text in your header
    if (monthDisplay) {
        monthDisplay.innerText = currentView.toLocaleDateString('en-US', { 
            month: 'long', 
            year: 'numeric' 
        });
    }

    const currentYear = currentView.getFullYear();
    const currentMonth = currentView.getMonth();

    // 2. DYNAMIC HOLIDAY FETCHING
    let holidays = [];
    try {
        const response = await fetch(`https://date.nager.at/api/v3/publicholidays/${currentYear}/PH`);
        const data = await response.json();
        holidays = data.map(h => ({
            day: parseInt(h.date.split('-')[2]),
            month: parseInt(h.date.split('-')[1]) - 1, 
            name: h.name
        }));
    } catch (error) {
        console.error("Holiday API failed to load:", error);
    }

    const realToday = new Date();
    const realDay = realToday.getDate();
    const realMonth = realToday.getMonth();
    const realYear = realToday.getFullYear();

    // ✅ REPLACED: FETCH REAL DATA FROM DATABASE
    let allEvents = [];

    try {
        const monthParam = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}`;
        const response = await fetch(`fetch_calendar_tasks.php?month=${monthParam}`);
        const data = await response.json();

        // Convert DB → calendar format
        allEvents = data.map(task => {
            const [year, month, day] = task.event_date.split('-');
            const d = new Date(year, month - 1, day);

            // Optional: format time
            const formatTime = (time) => {
                const [h, m] = time.split(':');
                let hour = parseInt(h);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                hour = hour % 12 || 12;
                return `${hour}:${m} ${ampm}`;
            };

            return {
                day: d.getDate(),
                month: d.getMonth(),
                year: d.getFullYear(),
            
                startTime: formatTime(task.start_time),
                endTime: formatTime(task.end_time),
                title: task.event_title,
            
                location: task.location,
                venue: task.venue,
            
                checklist: (() => {
                    try {
                        return task.checklist ? JSON.parse(task.checklist) : [];
                    } catch (e) {
                        return [];
                    }
                })(),
            
                status: (() => {
                    try {
                        const raw = task.checklist;
                
                        if (!raw || raw === "[]" || raw === "") {
                            return "no checklist";
                        }
                
                        const checklist = JSON.parse(raw);
                
                        if (!Array.isArray(checklist) || checklist.length === 0) {
                            return "no checklist";
                        }
                
                        const done = checklist.filter(i => Number(i.done) === 1).length;
                        const total = checklist.length;
                
                        if (done === 0) return "pending";
                        if (done === total) return "completed";
                
                        return "pending";
                
                    } catch (e) {
                        return "pending";
                    }
                })(),
            
                personnel: task.personnel.map(u => ({
                    name: `${u.first_name} ${u.last_name}`,
                    img: u.profile_pic ? `uploads/${u.profile_pic}` : "default_profile/user.png"
                }))
            };
        });

    } catch (error) {
        console.error("Failed to fetch tasks:", error);
    }

    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const firstDayIndex = new Date(currentYear, currentMonth, 1).getDay();

    calendarGrid.innerHTML = '';

    // Day Labels
    ["SUNDAY", "MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY"].forEach(label => {
        const d = document.createElement('div');
        d.className = 'day-label';
        d.innerText = label;
        calendarGrid.appendChild(d);
    });

    for (let i = 0; i < firstDayIndex; i++) {
        const empty = document.createElement('div');
        empty.className = 'calendar-day empty';
        calendarGrid.appendChild(empty);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dayDiv = document.createElement('div');
        
        const isToday = (day === realDay && currentMonth === realMonth && currentYear === realYear);
        const holidayData = holidays.find(h => h.day === day && h.month === currentMonth);

        let classNames = isToday ? 'calendar-day today' : 'calendar-day';
        if (holidayData) classNames += ' holiday';
        dayDiv.className = classNames;

        dayDiv.addEventListener('click', function(e) {
            e.stopPropagation();
            const isActive = this.classList.contains('active');
            document.querySelectorAll('.calendar-day.active').forEach(d => d.classList.remove('active'));
            if (!isActive) this.classList.add('active');
        });

        // Filter events for current day AND current month/year
        const dailyEvents = allEvents.filter(e => e.day === day && e.month === currentMonth && e.year === currentYear);
        const MAX_VISIBLE = 2;

        const header = document.createElement('div');
        header.className = 'day-header';
        header.innerHTML = `<span class="day-number">${day}</span>`;
        if (dailyEvents.length > MAX_VISIBLE) {
            header.innerHTML += `<span class="more-indicator">${dailyEvents.length}</span>`;
        }
        dayDiv.appendChild(header);

        if (holidayData) {
            const hLabel = document.createElement('div');
            hLabel.className = 'holiday-label';
            hLabel.innerText = holidayData.name;
            dayDiv.appendChild(hLabel);
        }

        if (dailyEvents.length > 0) {
            const listContainer = document.createElement('div');
            dailyEvents.slice(0, MAX_VISIBLE).forEach(event => {
                const bullet = document.createElement('div');
                bullet.className = 'event-bullet-item';
                bullet.innerHTML = `<span class="event-time">${event.startTime}</span> ${event.title}`;
                listContainer.appendChild(bullet);
            });
            dayDiv.appendChild(listContainer);

            const tooltip = document.createElement('div');
            tooltip.className = 'event-tooltip';
            
            const dayOfWeek = new Date(currentYear, currentMonth, day).getDay();
            if (dayOfWeek >= 5) {
                tooltip.style.left = 'auto';
                tooltip.style.right = '95%';
            }

            tooltip.innerHTML = `
                <div class="tooltip-header"><i class="bi bi-list"></i> <p>Events</p></div>
                <ul>${dailyEvents.map(e => `
                    <li>
                        <div class="event-info-main">
                            <div class="tooltip-time">${e.startTime} - ${e.endTime}</div>
                            <div class="tooltip-title">${e.title}</div>
                        </div>
                        <div class="location-details">
                        <div class="location-details">
                        ${e.venue ? `
                        <div class="location-item" style="margin-top:5px;">
                            <span class="loc-label" style="padding-right:12px;">Venue:</span> ${e.venue}
                        </div>
                        ` : ''}
                        </div>
                        </div>
                        <div class="checklist-details" style="margin-top:5px;">
                        <div class="checklist-item">
                            <span class="checklist-label">Checklist:</span>
                            ${(() => {
                                if (!e.status || e.status === "no checklist") {
                                    return "No checklist";
                                }
                            
                                if (e.status === "pending") return "Pending";
                                if (e.status === "completed") return "Completed";
                            
                                return "Pending";
                            })()}
                        </div>
                        </div>
                        <div class="personnel-container">
                            <div class="avatar-row">
                            ${e.personnel.map(p => `
                                <img 
                                    src="${p.img}" 
                                    class="personnel-avatar" 
                                    alt="${p.name}" 
                                    title="${p.name}"
                                >
                            `).join('')}
                            </div>
                        </div>
                    </li>`).join('')}
                </ul>`;
            dayDiv.appendChild(tooltip);
        }

        calendarGrid.appendChild(dayDiv);
    }
}

// Global Event Listeners
document.addEventListener('click', () => {
    document.querySelectorAll('.calendar-day.active').forEach(d => d.classList.remove('active'));
});

document.addEventListener('DOMContentLoaded', () => {
    renderCalendar();

    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentView.setMonth(currentView.getMonth() - 1);
            renderCalendar();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentView.setMonth(currentView.getMonth() + 1);
            renderCalendar();
        });
    }
});