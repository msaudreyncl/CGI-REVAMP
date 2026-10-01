<header class="site-header">
    <a href="dashboard.php" class="nav-brand" aria-label="CGI Dashboard">
        <img src="assets/cgi-logo.png" alt="CGI logo" class="brand-mark">

        <div class="nav-brand-text">
            <span class="nav-brand-name">CGI</span>
            <span class="nav-brand-subtitle">Coffee Grade Identification</span>
        </div>
    </a>

    <nav class="main-nav" aria-label="Main navigation">
        <a href="history.php"
           class="<?= $currentPage === 'history' ? 'active' : '' ?>"
           <?= $currentPage === 'history' ? 'aria-current="page"' : '' ?>>
            History
        </a>

        <a href="dashboard.php"
           class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>"
           <?= $currentPage === 'dashboard' ? 'aria-current="page"' : '' ?>>
            Dashboard
        </a>

        <a href="configuration.php"
           class="<?= $currentPage === 'configuration' ? 'active' : '' ?>"
           <?= $currentPage === 'configuration' ? 'aria-current="page"' : '' ?>>
            Configuration
        </a>
    </nav>

    <div class="user-menu">
        <button type="button"
                class="avatar"
                id="avatar-btn"
                aria-haspopup="true"
                aria-expanded="false"
                aria-controls="user-dropdown"
                aria-label="Open user menu">
            <img src="assets/user-icon.png" alt="" class="avatar-icon">
        </button>

        <div class="user-dropdown" id="user-dropdown" hidden>
            <div class="dropdown-profile">
                <div class="dropdown-avatar">
                    <img src="assets/user-icon.png" alt="">
                </div>

                <div class="dropdown-profile-text">
                    <strong>CGI Administrator</strong>
                    <span>System account</span>
                </div>
            </div>

            <div class="dropdown-divider"></div>

            <a href="users.php" class="dropdown-item dropdown-link">
                <span class="dropdown-item-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3ZM8 11c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3Zm8 2c-2 0-6 1-6 3v2h12v-2c0-2-4-3-6-3ZM8 13c-2.33 0-7 1.17-7 3.5V18h7v-2c0-.85.33-1.57.89-2.18A7.86 7.86 0 0 0 8 13Z"/>
                    </svg>
                </span>
                <span>User Management</span>
            </a>

            <div class="dropdown-divider"></div>

            <button type="button" class="dropdown-item dropdown-logout" id="logout-btn">
                <span class="dropdown-item-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M10 17v2H5V5h5v2h2V3H3v18h9v-4h-2Zm9-5-4-4v3H9v2h6v3l4-4Z"/>
                    </svg>
                </span>
                <span>Log out</span>
            </button>
        </div>
    </div>
</header>