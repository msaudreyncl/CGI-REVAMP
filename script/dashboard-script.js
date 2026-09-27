
// ============================================
// CGI DASHBOARD JAVASCRIPT
// ============================================


// --------------------------------------------
// USER DROPDOWN
// --------------------------------------------

const avatarBtn = document.getElementById('avatar-btn');
const userDropdown = document.getElementById('user-dropdown');

function closeDropdown() {
    userDropdown.hidden = true;
    avatarBtn.setAttribute('aria-expanded', 'false');
}

function openDropdown() {
    userDropdown.hidden = false;
    avatarBtn.setAttribute('aria-expanded', 'true');
}

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

document
    .getElementById('profile-btn')
    .addEventListener('click', () => {
        closeDropdown();
        window.location.href = 'profile.php';
    });

document
    .getElementById('logout-btn')
    .addEventListener('click', () => {
        closeDropdown();
        window.location.href = 'logout.php';
    });


// --------------------------------------------
// LIVE CLOCK
// --------------------------------------------

function updateDateTime() {
    const clock = document.getElementById('live-clock');
    const date = document.getElementById('live-date');

    const now = new Date();

    // Real-time clock
    if (clock) {
        clock.textContent = now.toLocaleTimeString('en-PH', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });
    }

    // Real-time date
    if (date) {
        date.textContent = now.toLocaleDateString('en-PH', {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
    }
}

// Update immediately when the page loads
updateDateTime();

// Update every second
setInterval(updateDateTime, 1000);

// --------------------------------------------
// HARDWARE STATUS
// --------------------------------------------

const DEVICE_KEY_MAP = {
    camera: 'camera',
    weighingScale: 'weighingScale',
    printer: 'printer'
};

let currentHardwareStatus = {
    camera: 'WARNING',
    weighingScale: 'WARNING',
    printer: 'WARNING',
    demo: false
};

function normalizeDeviceStatus(value) {
    const status = String(value || '').toUpperCase();

    return ['READY', 'WARNING', 'ERROR'].includes(status)
        ? status
        : 'ERROR';
}

function updateDeviceCard(card, status) {
    const dot = card.querySelector('.device-status .dot');
    const text = card.querySelector('.device-status-text');
    const sub = card.querySelector('.device-sub');

    const labels = {
        READY: 'Ready',
        WARNING: 'Warning',
        ERROR: 'Error'
    };

    const descriptions = {
        READY: 'Connected',
        WARNING: 'Connection needs attention',
        ERROR: 'Not connected'
    };

    text.textContent = labels[status];
    sub.textContent = descriptions[status];

    dot.classList.remove(
        'dot-warning',
        'dot-error'
    );

    if (status === 'WARNING') {
        dot.classList.add('dot-warning');
    }

    if (status === 'ERROR') {
        dot.classList.add('dot-error');
    }
}

function renderHardwareStatus(data) {
    const normalized = {
        camera: normalizeDeviceStatus(data.camera),
        weighingScale: normalizeDeviceStatus(
            data.weighingScale
        ),
        printer: normalizeDeviceStatus(data.printer),
        demo: data.demo === true
    };

    currentHardwareStatus = normalized;

    let allReady = true;

    document.querySelectorAll('.device-card')
        .forEach((card) => {
            const key = card.dataset.device;
            const status = normalized[DEVICE_KEY_MAP[key]];

            updateDeviceCard(card, status);

            if (status !== 'READY') {
                allReady = false;
            }
        });

    const pill = document.getElementById(
        'system-status-pill'
    );

    const pillText = document.getElementById(
        'system-status-text'
    );

    pillText.textContent = allReady
        ? 'System ready'
        : 'Attention needed';

    pill.classList.toggle(
        'status-pill-warning',
        !allReady
    );

    document.getElementById(
        'dashboard-demo-tag'
    ).hidden = !normalized.demo;

    // Notify the modal when device states change.
    document.dispatchEvent(
        new CustomEvent('cgi:hardware-status', {
            detail: normalized
        })
    );

    return normalized;
}

async function refreshHardwareStatus() {
    try {
        const response = await fetch(
            'api/hardware-status.php',
            { cache: 'no-store' }
        );

        if (!response.ok) {
            throw new Error(
                `Hardware API returned ${response.status}`
            );
        }

        const data = await response.json();

        return renderHardwareStatus(data);

    } catch (error) {
        console.error(
            '[CGI] Hardware status check failed:',
            error
        );

        // Do not display devices as ready if the
        // hardware endpoint cannot be reached.
        return renderHardwareStatus({
            camera: 'ERROR',
            weighingScale: 'ERROR',
            printer: 'ERROR',
            demo: false
        });
    }
}

window.CGIHardware = {
    refresh: refreshHardwareStatus,

    getStatus: () => ({
        ...currentHardwareStatus
    })
};

refreshHardwareStatus();

setInterval(
    refreshHardwareStatus,
    15000
);


// --------------------------------------------
// START TRANSACTION
// --------------------------------------------

document
    .getElementById('start-btn')
    .addEventListener('click', () => {

        if (window.CGITransactionWizard) {
            window.CGITransactionWizard.open();
        } else {
            console.error(
                'Transaction modal script is missing.'
            );
        }

    });


// --------------------------------------------
// LAST TRANSACTION
// --------------------------------------------

function formatPeso(value) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(value);
}

function displayLastTransaction(transaction) {
    const container = document.getElementById(
        'last-txn'
    );

    if (!transaction) {
        container.textContent =
            'No transactions recorded yet';

        return;
    }

    const demoLabel = transaction.demo
        ? ' · DEMO'
        : '';

    container.textContent =
        `Last transaction: ${transaction.id}` +
        ` · ${transaction.grade}` +
        ` · ${Number(transaction.weight).toFixed(1)} g` +
        ` · ${formatPeso(transaction.totalPrice)}` +
        demoLabel;
}

try {
    const lastTransaction = JSON.parse(
        localStorage.getItem('cgi-last-transaction')
    );

    displayLastTransaction(lastTransaction);
} catch (error) {
    displayLastTransaction(null);
}

document.addEventListener(
    'cgi:transaction-completed',
    (event) => {
        const transaction = event.detail;

        localStorage.setItem(
            'cgi-last-transaction',
            JSON.stringify(transaction)
        );

        displayLastTransaction(transaction);
    }
);


// --------------------------------------------
// AMBIENT COFFEE BEANS
// --------------------------------------------

const beanPath =
    'M12 2c-2 2.5-2 4.5 0 7s2 4.5 0 7 ' +
    'M8 5c-1.4 1.7-1.4 3 0 4.7';

const beanField = document.getElementById(
    'bean-field'
);

const BEAN_COUNT = 14;

for (let i = 0; i < BEAN_COUNT; i++) {
    const size = 20 + Math.random() * 34;
    const left = Math.random() * 100;
    const duration = 34 + Math.random() * 30;
    const delay = -Math.random() * duration;

    const rotationStart = Math.random() * 360;

    const rotationEnd =
        rotationStart +
        (Math.random() > 0.5 ? 140 : -140);

    const bean = document.createElement('div');

    bean.className = 'bean';

    bean.style.left = `${left}%`;
    bean.style.bottom = '-20vh';

    bean.style.width = `${size}px`;
    bean.style.height = `${size}px`;

    bean.style.animationDuration = `${duration}s`;
    bean.style.animationDelay = `${delay}s`;

    bean.style.setProperty(
        '--r0',
        `${rotationStart}deg`
    );

    bean.style.setProperty(
        '--r1',
        `${rotationEnd}deg`
    );

    bean.innerHTML = `
        <svg
            viewBox="0 0 24 24"
            width="100%"
            height="100%"
            fill="none"
            stroke="currentColor"
            stroke-width="1.3"
            stroke-linecap="round"
            aria-hidden="true"
        >
            <path d="${beanPath}"/>
        </svg>
    `;

    beanField.appendChild(bean);
}