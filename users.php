<?php
$currentPage = 'users';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>User Management | Coffee Grade Identification</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style/users-style.css?v=20261002-1">
    <link rel="stylesheet" href="style/header-style.css">
</head>

<body>

    <?php include 'components/header.php'; ?>

    <main class="users-main">

        <section class="users-hero">
            <div class="hero-content">
                <div class="hero-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3ZM8 11c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3Zm8 2c-2 0-6 1-6 3v2h12v-2c0-2-4-3-6-3ZM8 13c-2.33 0-7 1.17-7 3.5V18h7v-2c0-.85.33-1.57.89-2.18A7.86 7.86 0 0 0 8 13Z" />
                    </svg>
                </div>

                <div>
                    <span class="eyebrow">SYSTEM ADMINISTRATION</span>
                    <h1>User Management</h1>
                    <p>Manage accounts authorized to access the Coffee Grade Identification system.</p>
                </div>
            </div>

            <button type="button" class="add-user-btn" id="add-user-btn">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z" />
                </svg>
                <span>Add user</span>
            </button>
        </section>

        <section class="user-stats" aria-label="User account summary">
            <article class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3ZM8 11c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3Zm8 2c-2 0-6 1-6 3v2h12v-2c0-2-4-3-6-3ZM8 13c-2.33 0-7 1.17-7 3.5V18h7v-2c0-.85.33-1.57.89-2.18A7.86 7.86 0 0 0 8 13Z" />
                    </svg>
                </div>
                <div>
                    <span>Total users</span>
                    <strong id="total-users">0</strong>
                    <small>Registered accounts</small>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2 4 5v6c0 5.05 3.41 9.74 8 11 4.59-1.26 8-5.95 8-11V5l-8-3Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 10.2c-1.83 0-3.45-.87-4.48-2.2.03-1.49 2.99-2.3 4.48-2.3 1.48 0 4.45.81 4.48 2.3A5.66 5.66 0 0 1 12 17.2Z" />
                    </svg>
                </div>
                <div>
                    <span>Administrators</span>
                    <strong id="total-admins">0</strong>
                    <small>Administrative access</small>
                </div>
            </article>

            <article class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm-2 15-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9Z" />
                    </svg>
                </div>
                <div>
                    <span>Active accounts</span>
                    <strong id="active-users">0</strong>
                    <small>Currently authorized</small>
                </div>
            </article>
        </section>

        <section class="users-panel">
            <div class="users-panel-head">
                <div>
                    <span class="eyebrow">AUTHORIZED ACCOUNTS</span>
                    <h2>System Users</h2>
                </div>

                <span class="user-count" id="user-count">0 users</span>
            </div>

            <div class="user-toolbar">
                <label class="search-box">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m21 19-5.2-5.2A7 7 0 1 0 14 15.8L19 21l2-2ZM5 10a5 5 0 1 1 10 0 5 5 0 0 1-10 0Z" />
                    </svg>
                    <input type="search" id="user-search" placeholder="Search name or username..." autocomplete="off">
                </label>

                <div class="toolbar-filters">
                    <label class="filter-control">
                        <span>Role</span>
                        <select id="role-filter">
                            <option value="all">All roles</option>
                            <option value="Administrator">Administrator</option>
                            <option value="Operator">Operator</option>
                        </select>
                    </label>

                    <label class="filter-control">
                        <span>Status</span>
                        <select id="status-filter">
                            <option value="all">All statuses</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </label>

                    <button type="button" class="reset-filters-btn" id="reset-filters-btn">
                        Reset
                    </button>
                </div>
            </div>

            <div class="users-table-wrap">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last login</th>
                            <th class="actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body"></tbody>
                </table>

                <div class="users-empty" id="users-empty" hidden>
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3ZM8 11c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3Zm8 2c-2 0-6 1-6 3v2h12v-2c0-2-4-3-6-3ZM8 13c-2.33 0-7 1.17-7 3.5V18h7v-2c0-.85.33-1.57.89-2.18A7.86 7.86 0 0 0 8 13Z" />
                        </svg>
                    </div>
                    <strong>No users found</strong>
                    <span>Try changing your search or filters.</span>
                </div>
            </div>

            <div class="users-panel-footer">
                <span id="showing-users">Showing 0 users</span>
            </div>
        </section>

    </main>

    <div class="modal-overlay" id="user-modal" hidden>
        <section class="user-modal-card" role="dialog" aria-modal="true" aria-labelledby="user-modal-title">
            <div class="modal-header">
                <div>
                    <span class="eyebrow" id="user-modal-eyebrow">NEW SYSTEM ACCOUNT</span>
                    <h2 id="user-modal-title">Add User</h2>
                    <p id="user-modal-description">Create an account authorized to access CGI.</p>
                </div>
            </div>

            <form id="user-form" novalidate>
                <input type="hidden" id="edit-user-id">

                <div class="form-grid">
                    <label class="form-field full-field">
                        <span>Full name</span>
                        <input type="text" id="full-name" maxlength="80" placeholder="Enter full name" autocomplete="off">
                        <small class="field-error" id="full-name-error"></small>
                    </label>

                    <label class="form-field">
                        <span>Username</span>
                        <input type="text" id="username" maxlength="30" placeholder="Enter username" autocomplete="off">
                        <small class="field-error" id="username-error"></small>
                    </label>

                    <label class="form-field">
                        <span>Role</span>
                        <select id="user-role">
                            <option value="Operator">Operator</option>
                            <option value="Administrator">Administrator</option>
                        </select>
                        <small class="field-error"></small>
                    </label>

                    <label class="form-field password-field">
                        <span id="password-label">Password</span>
                        <div class="password-input">
                            <input type="password" id="password" placeholder="Enter password" autocomplete="new-password">
                            <button type="button" class="password-toggle" data-target="password" aria-label="Show password">
                                <svg class="eye-open" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" />
                                </svg>
                            </button>
                        </div>
                        <small class="field-hint" id="password-hint">Minimum of 8 characters.</small>
                        <small class="field-error" id="password-error"></small>
                    </label>

                    <label class="form-field password-field">
                        <span>Confirm password</span>
                        <div class="password-input">
                            <input type="password" id="confirm-password" placeholder="Confirm password" autocomplete="new-password">
                            <button type="button" class="password-toggle" data-target="confirm-password" aria-label="Show password">
                                <svg class="eye-open" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" />
                                </svg>
                            </button>
                        </div>
                        <small class="field-error" id="confirm-password-error"></small>
                    </label>

                    <label class="form-field full-field">
                        <span>Account status</span>
                        <select id="user-status">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        <small class="field-hint">Inactive users cannot access the system.</small>
                    </label>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancel-user-btn">Cancel</button>
                    <button type="submit" class="btn-primary" id="save-user-btn">Create user</button>
                </div>
            </form>
        </section>
    </div>

    <div class="modal-overlay confirmation-overlay" id="confirm-modal" hidden>
        <section class="confirm-card" role="alertdialog" aria-modal="true" aria-labelledby="confirm-title">
            <div class="confirm-icon" id="confirm-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2Zm1 15h-2v-2h2v2Zm0-4h-2V7h2v6Z" />
                </svg>
            </div>

            <h2 id="confirm-title">Delete user?</h2>
            <p id="confirm-message">This action cannot be undone.</p>

            <div class="confirm-actions">
                <button type="button" class="btn-secondary" id="confirm-cancel-btn">Cancel</button>
                <button type="button" class="btn-danger" id="confirm-action-btn">Delete user</button>
            </div>
        </section>
    </div>

    <div class="modal-overlay" id="reset-password-modal" hidden>
        <section class="reset-password-card" role="dialog" aria-modal="true" aria-labelledby="reset-password-title">
            <span class="eyebrow">ACCOUNT SECURITY</span>
            <h2 id="reset-password-title">Reset Password</h2>
            <p id="reset-password-description">Set a new password for this account.</p>

            <form id="reset-password-form" novalidate>
                <label class="form-field">
                    <span>New password</span>
                    <div class="password-input">
                        <input type="password" id="reset-password" placeholder="Enter new password" autocomplete="new-password">
                        <button type="button" class="password-toggle" data-target="reset-password" aria-label="Show password">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" />
                            </svg>
                        </button>
                    </div>
                    <small class="field-error" id="reset-password-error"></small>
                </label>

                <label class="form-field">
                    <span>Confirm new password</span>
                    <div class="password-input">
                        <input type="password" id="reset-confirm-password" placeholder="Confirm new password" autocomplete="new-password">
                        <button type="button" class="password-toggle" data-target="reset-confirm-password" aria-label="Show password">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" />
                            </svg>
                        </button>
                    </div>
                    <small class="field-error" id="reset-confirm-error"></small>
                </label>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="reset-password-cancel">Cancel</button>
                    <button type="submit" class="btn-primary">Update password</button>
                </div>
            </form>
        </section>
    </div>

    <div class="toast" id="users-toast" hidden role="status" aria-live="polite">
        <span class="toast-icon">✓</span>
        <div>
            <strong id="toast-title">User created</strong>
            <span id="toast-message">The account has been added successfully.</span>
        </div>
    </div>

    <script src="users-script.js"></script>
</body>

</html>