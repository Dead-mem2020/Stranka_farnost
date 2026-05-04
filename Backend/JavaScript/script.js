// Calendar Events Data
const events = {
    '2024-05-24': 'Aktivní bohoslužby',
    '2024-05-18': 'Brigáda na farní zahradě',
    '2024-05-10': 'Výsledek Tříkrálové sbírky'
};

const activeServiceDates = {
    '2024-05-24': 'Aktivní bohoslužby'
};

// Calendar DOM Elements
const calendarGrid = document.getElementById('calendarGrid');
const calendarMonthLabel = document.getElementById('calendarMonth');
const prevMonthBtn = document.getElementById('prevMonth');
const nextMonthBtn = document.getElementById('nextMonth');
const activeEventsButton = document.getElementById('showActiveEvents');
const calendarInfo = document.getElementById('calendarInfo');
let currentDate = new Date();

/**
 * Format date to YYYY-MM-DD format for key matching
 */
function formatKey(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/**
 * Render calendar for given date
 */
function renderCalendar(date) {
    const month = date.getMonth();
    const year = date.getFullYear();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startWeekDay = (firstDay.getDay() + 6) % 7;
    const totalDays = lastDay.getDate();

    calendarMonthLabel.textContent = firstDay.toLocaleString('cs-CZ', { month: 'long', year: 'numeric' });
    calendarGrid.innerHTML = '';

    const previousLastDay = new Date(year, month, 0).getDate();
    const totalCells = startWeekDay + totalDays;
    const weeks = Math.ceil(totalCells / 7) * 7;

    for (let i = 0; i < weeks; i++) {
        const cell = document.createElement('div');
        const dayNumber = i - startWeekDay + 1;
        let cellDate;
        let isCurrentMonth = true;

        if (i < startWeekDay) {
            cellDate = new Date(year, month - 1, previousLastDay - startWeekDay + i + 1);
            isCurrentMonth = false;
        } else if (dayNumber > totalDays) {
            cellDate = new Date(year, month + 1, dayNumber - totalDays);
            isCurrentMonth = false;
        } else {
            cellDate = new Date(year, month, dayNumber);
        }

        cell.className = 'calendar-day';
        if (!isCurrentMonth) cell.classList.add('other-month');
        if (formatKey(cellDate) === formatKey(new Date())) cell.classList.add('today');

        const key = formatKey(cellDate);
        if (events[key]) {
            cell.classList.add('has-event');
            cell.title = events[key];
        }

        cell.textContent = cellDate.getDate();
        calendarGrid.appendChild(cell);
    }
}

/**
 * Change month by offset
 */
function changeMonth(offset) {
    currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + offset, 1);
    renderCalendar(currentDate);
}

/**
 * Show active services in calendar
 */
function showActiveServices() {
    Object.assign(events, activeServiceDates);
    const firstActiveDate = Object.keys(activeServiceDates)[0];
    const [year, month] = firstActiveDate.split('-');
    currentDate = new Date(Number(year), Number(month) - 1, 1);
    renderCalendar(currentDate);
    calendarInfo.textContent = 'Zobrazeny aktivní bohoslužby v kalendáři.';
}

/**
 * Calendar Event Listeners
 */
if (prevMonthBtn) {
    prevMonthBtn.addEventListener('click', () => changeMonth(-1));
}
if (nextMonthBtn) {
    nextMonthBtn.addEventListener('click', () => changeMonth(1));
}
if (activeEventsButton) {
    activeEventsButton.addEventListener('click', showActiveServices);
}

/**
 * Reservation Modal Functionality
 */
function initReservationModal() {
    const modal = document.getElementById("rezervaceModal");
    const btn = document.getElementById("showActiveEvents");
    const span = document.getElementsByClassName("close-btn")[0];

    if (!modal) return;

    // Open modal
    if (btn) {
        btn.onclick = function() {
            modal.style.display = "block";
        }
    }

    // Close modal with X button
    if (span) {
        span.onclick = function() {
            modal.style.display = "none";
        }
    }

    // Close modal by clicking outside
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}

/**
 * Initialize all functionality when DOM is ready
 */
document.addEventListener('DOMContentLoaded', () => {
    renderCalendar(currentDate);
    initReservationModal();
});