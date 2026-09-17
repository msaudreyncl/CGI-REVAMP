<?php
/**
 * COFFEE GRADE IDENTIFICATION — Main Dashboard
 * Automated Quality & Transactional Pricing System
 *
 * This page represents the machine's idle/ready screen on a 7" touchscreen
 * (16:9 landscape). All styling lives in style.css — no inline CSS.
 */

date_default_timezone_set('Asia/Manila');

// --- System / hardware status -------------------------------------------
// In the real machine these would come from sensor/service checks.
// Each status is one of: ready, warning, error
$hardware = [
    [
        'name'    => 'CAMERA',
        'icon'    => 'camera',
        'status'  => 'ready',
        'label'   => 'READY',
        'subtext' => 'Connected',
    ],
    [
        'name'    => 'WEIGHING SCALE',
        'icon'    => 'scale',
        'status'  => 'ready',
        'label'   => 'READY',
        'subtext' => 'Connected',
    ],
    [
        'name'    => 'PRINTER',
        'icon'    => 'printer',
        'status'  => 'ready',
        'label'   => 'READY',
        'subtext' => 'Connected',
    ],
];

$system_ready = true;
foreach ($hardware as $h) {
    if ($h['status'] !== 'ready') {
        $system_ready = false;
        break;
    }
}

// --- Last transaction (would come from the database) --------------------
$last_transaction = [
    'id'     => 'CGI-2026-001',
    'grade'  => 'EXTRA CLASS',
    'weight' => '287.4 g',
    'price'  => '₱482.50',
];

// --- Header date / time ---------------------------------------------------
$display_date = strtoupper(date('d M Y'));
$display_time = date('g:i A');

function status_dot_class($status) {
    switch ($status) {
        case 'ready':   return 'dot--ready';
        case 'warning': return 'dot--warning';
        case 'error':   return 'dot--error';
        default:        return 'dot--ready';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Coffee Grade Identification — Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Slab:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style/dashboard-style.css">
</head>
<body>

  <!-- ============ TOP NAV (site-wide, outside the kiosk screen) ============ -->
  <nav class="topnav" aria-label="Primary">
    <div class="topnav__logo">
      <img src="assets/logo.png" alt="CGI Logo" class="topnav__logo-img">
    </div>

    <div class="topnav__tabs">
      <a href="history.php" class="topnav__tab">History</a>
      <a href="dashboard.php" class="topnav__tab topnav__tab--main" aria-current="page">Dashboard</a>
      <a href="settings.php" class="topnav__tab">Settings</a>
    </div>

    <div class="topnav__user">
      <button type="button" class="user-menu-trigger" id="userMenuTrigger" aria-haspopup="true" aria-expanded="false" aria-controls="userMenu">
        <svg viewBox="0 0 40 40" width="22" height="22" aria-hidden="true">
          <circle cx="20" cy="14" r="7" fill="none" stroke="currentColor" stroke-width="2.5"/>
          <path d="M6 34 C 6 24, 34 24, 34 34" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
      </button>
      <div class="user-menu" id="userMenu" hidden>
        <a href="profile.php" class="user-menu__item">
          <svg viewBox="0 0 20 20" width="16" height="16" aria-hidden="true">
            <circle cx="10" cy="7" r="3.5" fill="none" stroke="currentColor" stroke-width="1.6"/>
            <path d="M3 17 C 3 12, 17 12, 17 17" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
          </svg>
          View Profile
        </a>
        <a href="logout.php" class="user-menu__item user-menu__item--danger">
          <svg viewBox="0 0 20 20" width="16" height="16" aria-hidden="true">
            <path d="M8 3 H5 a2 2 0 0 0 -2 2 v10 a2 2 0 0 0 2 2 h3" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            <path d="M12 6 L17 10 L12 14" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            <line x1="17" y1="10" x2="7.5" y2="10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
          </svg>
          Log Out
        </a>
      </div>
    </div>
  </nav>

  <!-- ============ KIOSK SCREEN ============ -->
  <div class="stage">
  <div class="machine-frame">

  <!-- ============ HEADER ============ -->
  <header class="header">
    <div class="header__brand">
      <span class="bean-icon" aria-hidden="true">
        <svg viewBox="0 0 40 40" width="34" height="34">
          <ellipse cx="20" cy="20" rx="17" ry="13" transform="rotate(45 20 20)" fill="none" stroke="currentColor" stroke-width="2.5"/>
          <path d="M20 8 C 16 14, 16 26, 20 32" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
      </span>
      <div class="header__titles">
        <h1 class="header__title">COFFEE GRADE IDENTIFICATION</h1>
        <p class="header__subtitle">Automated Quality &amp; Transactional Pricing System</p>
      </div>
    </div>
    <div class="header__clock">
      <span id="clock-date"><?= htmlspecialchars($display_date) ?></span>
      <span class="header__clock-sep">&nbsp;&nbsp;</span>
      <span id="clock-time"><?= htmlspecialchars($display_time) ?></span>
    </div>
  </header>

  <!-- ============ MAIN GRID ============ -->
  <main class="main">

    <!-- ---- SYSTEM STATUS ---- -->
    <section class="status-section" aria-labelledby="status-heading">
      <div class="status-section__head">
        <h2 id="status-heading" class="section-label">System Status</h2>
        <div class="system-ready-pill <?= $system_ready ? 'system-ready-pill--ok' : 'system-ready-pill--warn' ?>">
          <span class="dot <?= $system_ready ? 'dot--ready' : 'dot--warning' ?>"></span>
          <span><?= $system_ready ? 'SYSTEM READY' : 'CHECK HARDWARE' ?></span>
        </div>
      </div>

      <div class="hardware-row">
        <?php foreach ($hardware as $h): ?>
          <div class="hw-card">
            <div class="hw-card__icon" aria-hidden="true">
              <?php if ($h['icon'] === 'camera'): ?>
                <svg viewBox="0 0 48 48" width="30" height="30">
                  <rect x="5" y="14" width="38" height="26" rx="3" fill="none" stroke="currentColor" stroke-width="2.5"/>
                  <path d="M16 14 L19 8 H29 L32 14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
                  <circle cx="24" cy="27" r="8" fill="none" stroke="currentColor" stroke-width="2.5"/>
                </svg>
              <?php elseif ($h['icon'] === 'scale'): ?>
                <svg viewBox="0 0 48 48" width="30" height="30">
                  <rect x="6" y="30" width="36" height="10" rx="2" fill="none" stroke="currentColor" stroke-width="2.5"/>
                  <rect x="14" y="12" width="20" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="2.5"/>
                  <line x1="24" y1="26" x2="24" y2="30" stroke="currentColor" stroke-width="2.5"/>
                </svg>
              <?php else: ?>
                <svg viewBox="0 0 48 48" width="30" height="30">
                  <rect x="8" y="14" width="32" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="2.5"/>
                  <rect x="13" y="30" width="22" height="12" fill="none" stroke="currentColor" stroke-width="2.5"/>
                  <line x1="15" y1="20" x2="33" y2="20" stroke="currentColor" stroke-width="2"/>
                </svg>
              <?php endif; ?>
            </div>
            <div class="hw-card__body">
              <span class="hw-card__name"><?= htmlspecialchars($h['name']) ?></span>
              <span class="hw-card__status">
                <span class="dot <?= status_dot_class($h['status']) ?>"></span>
                <?= htmlspecialchars($h['label']) ?>
              </span>
              <span class="hw-card__subtext"><?= htmlspecialchars($h['subtext']) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- ---- MAIN ACTION ---- -->
    <section class="action-section">
      <h2 class="action-section__heading">Ready to Start</h2>
      <p class="action-section__subtext">Start a new coffee quality assessment</p>

      <form action="transaction.php" method="post">
        <button type="submit" class="start-button">
          <span class="start-button__icon" aria-hidden="true">
            <svg viewBox="0 0 40 40" width="30" height="30">
              <circle cx="20" cy="20" r="17" fill="none" stroke="currentColor" stroke-width="2.5"/>
              <path d="M16 13 L28 20 L16 27 Z" fill="currentColor"/>
            </svg>
          </span>
          START TRANSACTION
        </button>
      </form>
    </section>

    <!-- ---- LAST TRANSACTION ---- -->
    <section class="bottom-row" aria-label="Last transaction">
      <div class="last-transaction">
        <span class="last-transaction__label">Last Transaction</span>
        <div class="last-transaction__grid">
          <span class="last-transaction__id"><?= htmlspecialchars($last_transaction['id']) ?></span>
          <span class="last-transaction__grade"><?= htmlspecialchars($last_transaction['grade']) ?></span>
          <span class="last-transaction__weight"><?= htmlspecialchars($last_transaction['weight']) ?></span>
          <span class="last-transaction__price"><?= htmlspecialchars($last_transaction['price']) ?></span>
        </div>
      </div>
    </section>

  </main>
  </div>
  </div>

<script src="script/dashboard-script.js"></script>
</body>
</html>