// ============================================
// CGI DASHBOARD JAVASCRIPT
// ============================================


// --------------------------------------------
// USER DROPDOWN
// --------------------------------------------

const avatarBtn = document.getElementById('avatar-btn');
const userDropdown = document.getElementById('user-dropdown');
const profileBtn = document.getElementById('profile-btn');
const logoutBtn = document.getElementById('logout-btn');

function closeDropdown() {
    if (!userDropdown || !avatarBtn) {
        return;
    }

    userDropdown.hidden = true;
    avatarBtn.setAttribute('aria-expanded', 'false');
}

function openDropdown() {
    if (!userDropdown || !avatarBtn) {
        return;
    }

    userDropdown.hidden = false;
    avatarBtn.setAttribute('aria-expanded', 'true');
}

if (avatarBtn && userDropdown) {
    avatarBtn.addEventListener('click', (event) => {
        event.stopPropagation();

        if (userDropdown.hidden) {
            openDropdown();
        } else {
            closeDropdown();
        }
    });

    document.addEventListener('click', (event) => {
        if (
            !userDropdown.hidden &&
            !event.target.closest('.user-menu')
        ) {
            closeDropdown();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeDropdown();
        }
    });
}

if (profileBtn) {
    profileBtn.addEventListener('click', () => {
        closeDropdown();
        window.location.href = 'profile.php';
    });
}

if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
        closeDropdown();
        window.location.href = 'logout.php';
    });
}


// --------------------------------------------
// LIVE CLOCK
// --------------------------------------------

function updateDateTime() {
    const clock = document.getElementById('live-clock');
    const date = document.getElementById('live-date');

    const now = new Date();

    if (clock) {
        clock.textContent = now.toLocaleTimeString('en-PH', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });
    }

    if (date) {
        date.textContent = now.toLocaleDateString('en-PH', {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
    }
}

updateDateTime();

setInterval(updateDateTime, 1000);