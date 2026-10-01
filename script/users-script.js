document.addEventListener("DOMContentLoaded", () => {
    const STORAGE_KEY = "cgiUsers";

    const defaultUsers = [
        {
            id: 1,
            name: "CGI Administrator",
            username: "admin",
            role: "Administrator",
            status: "Active",
            lastLogin: "October 2, 2026 · 12:49 AM",
            password: "administrator"
        },
        {
            id: 2,
            name: "CGI Operator",
            username: "operator01",
            role: "Operator",
            status: "Active",
            lastLogin: "October 1, 2026 · 04:18 PM",
            password: "operator01"
        },
        {
            id: 3,
            name: "Assessment Operator",
            username: "operator02",
            role: "Operator",
            status: "Active",
            lastLogin: "September 30, 2026 · 10:06 AM",
            password: "operator02"
        }
    ];

    const tableBody = document.getElementById("users-table-body");
    const emptyState = document.getElementById("users-empty");
    const totalUsers = document.getElementById("total-users");
    const totalAdmins = document.getElementById("total-admins");
    const activeUsers = document.getElementById("active-users");
    const userCount = document.getElementById("user-count");
    const showingUsers = document.getElementById("showing-users");

    const searchInput = document.getElementById("user-search");
    const roleFilter = document.getElementById("role-filter");
    const statusFilter = document.getElementById("status-filter");
    const resetFiltersBtn = document.getElementById("reset-filters-btn");

    const addUserBtn = document.getElementById("add-user-btn");
    const userModal = document.getElementById("user-modal");
    const userForm = document.getElementById("user-form");
    const cancelUserBtn = document.getElementById("cancel-user-btn");
    const editUserId = document.getElementById("edit-user-id");
    const fullName = document.getElementById("full-name");
    const username = document.getElementById("username");
    const role = document.getElementById("user-role");
    const status = document.getElementById("user-status");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm-password");
    const modalTitle = document.getElementById("user-modal-title");
    const modalEyebrow = document.getElementById("user-modal-eyebrow");
    const modalDescription = document.getElementById("user-modal-description");
    const saveUserBtn = document.getElementById("save-user-btn");
    const passwordLabel = document.getElementById("password-label");
    const passwordHint = document.getElementById("password-hint");

    const confirmModal = document.getElementById("confirm-modal");
    const confirmTitle = document.getElementById("confirm-title");
    const confirmMessage = document.getElementById("confirm-message");
    const confirmIcon = document.getElementById("confirm-icon");
    const confirmCancelBtn = document.getElementById("confirm-cancel-btn");
    const confirmActionBtn = document.getElementById("confirm-action-btn");

    const resetPasswordModal = document.getElementById("reset-password-modal");
    const resetPasswordForm = document.getElementById("reset-password-form");
    const resetPasswordDescription = document.getElementById("reset-password-description");
    const resetPassword = document.getElementById("reset-password");
    const resetConfirmPassword = document.getElementById("reset-confirm-password");
    const resetPasswordCancel = document.getElementById("reset-password-cancel");

    const toast = document.getElementById("users-toast");
    const toastTitle = document.getElementById("toast-title");
    const toastMessage = document.getElementById("toast-message");

    let users = loadUsers();
    let confirmationAction = null;
    let resetPasswordUserId = null;
    let toastTimer = null;

    render();

    addUserBtn.addEventListener("click", openAddUser);

    searchInput.addEventListener("input", render);
    roleFilter.addEventListener("change", render);
    statusFilter.addEventListener("change", render);

    resetFiltersBtn.addEventListener("click", () => {
        searchInput.value = "";
        roleFilter.value = "all";
        statusFilter.value = "all";
        render();
    });

    cancelUserBtn.addEventListener("click", closeUserModal);

    userForm.addEventListener("submit", event => {
        event.preventDefault();
        saveUser();
    });

    confirmCancelBtn.addEventListener("click", closeConfirmation);

    confirmActionBtn.addEventListener("click", () => {
        if (typeof confirmationAction === "function") confirmationAction();
        closeConfirmation();
    });

    resetPasswordCancel.addEventListener("click", closeResetPassword);

    resetPasswordForm.addEventListener("submit", event => {
        event.preventDefault();
        updatePassword();
    });

    document.querySelectorAll(".password-toggle").forEach(button => {
        button.addEventListener("click", () => {
            const input = document.getElementById(button.dataset.target);
            if (!input) return;

            const showing = input.type === "text";
            input.type = showing ? "password" : "text";
            button.setAttribute("aria-label", showing ? "Show password" : "Hide password");
        });
    });

    [userModal, confirmModal, resetPasswordModal].forEach(modal => {
        modal.addEventListener("click", event => {
            if (event.target !== modal) return;

            if (modal === userModal) closeUserModal();
            if (modal === confirmModal) closeConfirmation();
            if (modal === resetPasswordModal) closeResetPassword();
        });
    });

    document.addEventListener("click", event => {
        if (!event.target.closest(".table-actions")) {
            closeActionMenus();
        }
    });

    document.addEventListener("keydown", event => {
        if (event.key !== "Escape") return;

        closeActionMenus();

        if (!confirmModal.hidden) {
            closeConfirmation();
            return;
        }

        if (!resetPasswordModal.hidden) {
            closeResetPassword();
            return;
        }

        if (!userModal.hidden) closeUserModal();
    });

    function loadUsers() {
        try {
            const stored = JSON.parse(localStorage.getItem(STORAGE_KEY));

            if (Array.isArray(stored)) return stored;

            localStorage.setItem(STORAGE_KEY, JSON.stringify(defaultUsers));
            return structuredClone(defaultUsers);
        } catch {
            return structuredClone(defaultUsers);
        }
    }

    function saveUsers() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(users));
    }

    function render() {
        updateStats();

        const filteredUsers = getFilteredUsers();

        tableBody.innerHTML = filteredUsers.map(user => createUserRow(user)).join("");

        const empty = filteredUsers.length === 0;
        emptyState.hidden = !empty;
        document.querySelector(".users-table").style.display = empty ? "none" : "table";

        userCount.textContent = `${users.length} ${users.length === 1 ? "user" : "users"}`;
        showingUsers.textContent = filteredUsers.length === users.length
            ? `Showing ${users.length} ${users.length === 1 ? "user" : "users"}`
            : `Showing ${filteredUsers.length} of ${users.length} users`;

        bindTableActions();
    }

    function updateStats() {
        totalUsers.textContent = users.length;
        totalAdmins.textContent = users.filter(user => user.role === "Administrator").length;
        activeUsers.textContent = users.filter(user => user.status === "Active").length;
    }

    function getFilteredUsers() {
        const query = searchInput.value.trim().toLowerCase();
        const selectedRole = roleFilter.value;
        const selectedStatus = statusFilter.value;

        return users.filter(user => {
            const matchesSearch =
                !query ||
                user.name.toLowerCase().includes(query) ||
                user.username.toLowerCase().includes(query);

            const matchesRole =
                selectedRole === "all" ||
                user.role === selectedRole;

            const matchesStatus =
                selectedStatus === "all" ||
                user.status === selectedStatus;

            return matchesSearch && matchesRole && matchesStatus;
        });
    }

    function createUserRow(user) {
        const initials = getInitials(user.name);
        const roleClass = user.role === "Administrator" ? "admin" : "";
        const statusClass = user.status === "Inactive" ? "inactive" : "";
        const statusAction = user.status === "Active" ? "Deactivate account" : "Activate account";

        return `
            <tr>
                <td>
                    <div class="table-user">
                        <div class="table-avatar">${escapeHTML(initials)}</div>
                        <div class="table-user-info">
                            <strong>${escapeHTML(user.name)}</strong>
                            <span>${user.role === "Administrator" ? "System administrator" : "System operator"}</span>
                        </div>
                    </div>
                </td>

                <td>
                    <span class="username-cell">@${escapeHTML(user.username)}</span>
                </td>

                <td>
                    <span class="role-badge ${roleClass}">
                        ${escapeHTML(user.role)}
                    </span>
                </td>

                <td>
                    <span class="status-badge ${statusClass}">
                        ${escapeHTML(user.status)}
                    </span>
                </td>

                <td>
                    <span class="last-login">
                        ${escapeHTML(user.lastLogin || "No login recorded")}
                    </span>
                </td>

                <td>
                    <div class="table-actions">
                        <button type="button"
                                class="edit-user-btn"
                                data-edit-user="${user.id}">
                            Edit
                        </button>

                        <button type="button"
                                class="more-user-btn"
                                data-more-user="${user.id}"
                                aria-label="More actions"
                                aria-expanded="false">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2Zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2Zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2Z"/>
                            </svg>
                        </button>

                        <div class="action-menu"
                             data-action-menu="${user.id}"
                             hidden>
                            <button type="button" data-reset-password="${user.id}">
                                Reset password
                            </button>

                            <button type="button" data-toggle-status="${user.id}">
                                ${statusAction}
                            </button>

                            <button type="button"
                                    class="danger-action"
                                    data-delete-user="${user.id}">
                                Delete user
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    }

    function bindTableActions() {
        document.querySelectorAll("[data-edit-user]").forEach(button => {
            button.addEventListener("click", () => {
                openEditUser(Number(button.dataset.editUser));
            });
        });

        document.querySelectorAll("[data-more-user]").forEach(button => {
            button.addEventListener("click", event => {
                event.stopPropagation();

                const id = Number(button.dataset.moreUser);
                const menu = document.querySelector(`[data-action-menu="${id}"]`);
                const opening = menu.hidden;

                closeActionMenus();

                menu.hidden = !opening;
                button.setAttribute("aria-expanded", String(opening));
            });
        });

        document.querySelectorAll("[data-reset-password]").forEach(button => {
            button.addEventListener("click", () => {
                openResetPassword(Number(button.dataset.resetPassword));
            });
        });

        document.querySelectorAll("[data-toggle-status]").forEach(button => {
            button.addEventListener("click", () => {
                confirmStatusChange(Number(button.dataset.toggleStatus));
            });
        });

        document.querySelectorAll("[data-delete-user]").forEach(button => {
            button.addEventListener("click", () => {
                confirmDeleteUser(Number(button.dataset.deleteUser));
            });
        });
    }

    function closeActionMenus() {
        document.querySelectorAll(".action-menu").forEach(menu => {
            menu.hidden = true;
        });

        document.querySelectorAll(".more-user-btn").forEach(button => {
            button.setAttribute("aria-expanded", "false");
        });
    }

    function openAddUser() {
        clearUserForm();

        modalEyebrow.textContent = "NEW SYSTEM ACCOUNT";
        modalTitle.textContent = "Add User";
        modalDescription.textContent = "Create an account authorized to access CGI.";
        saveUserBtn.textContent = "Create user";
        passwordLabel.textContent = "Password";
        passwordHint.textContent = "Minimum of 8 characters.";
        password.placeholder = "Enter password";
        confirmPassword.placeholder = "Confirm password";

        editUserId.value = "";
        status.value = "Active";

        userModal.hidden = false;
        document.body.style.overflow = "hidden";

        requestAnimationFrame(() => fullName.focus());
    }

    function openEditUser(id) {
        const user = users.find(item => item.id === id);
        if (!user) return;

        clearUserForm();

        modalEyebrow.textContent = "ACCOUNT DETAILS";
        modalTitle.textContent = "Edit User";
        modalDescription.textContent = "Update the account information and access level.";
        saveUserBtn.textContent = "Save changes";
        passwordLabel.textContent = "New password";
        passwordHint.textContent = "Leave blank to keep the current password.";
        password.placeholder = "Leave blank to keep current password";
        confirmPassword.placeholder = "Confirm new password";

        editUserId.value = user.id;
        fullName.value = user.name;
        username.value = user.username;
        role.value = user.role;
        status.value = user.status;

        userModal.hidden = false;
        document.body.style.overflow = "hidden";

        requestAnimationFrame(() => fullName.focus());
    }

    function closeUserModal() {
        userModal.hidden = true;
        document.body.style.overflow = "";
        clearUserForm();
    }

    function clearUserForm() {
        userForm.reset();
        editUserId.value = "";

        document.querySelectorAll("#user-form .field-error").forEach(error => {
            error.textContent = "";
        });

        document.querySelectorAll("#user-form .has-error").forEach(field => {
            field.classList.remove("has-error");
        });

        password.type = "password";
        confirmPassword.type = "password";
    }

    function saveUser() {
        clearFormErrors();

        const id = Number(editUserId.value) || null;
        const nameValue = fullName.value.trim();
        const usernameValue = username.value.trim();
        const roleValue = role.value;
        const statusValue = status.value;
        const passwordValue = password.value;
        const confirmValue = confirmPassword.value;

        let valid = true;

        if (nameValue.length < 2) {
            setFieldError(fullName, "full-name-error", "Enter the user's full name.");
            valid = false;
        }

        if (!/^[a-zA-Z0-9._-]{3,30}$/.test(usernameValue)) {
            setFieldError(
                username,
                "username-error",
                "Use 3–30 letters, numbers, dots, underscores, or hyphens."
            );
            valid = false;
        }

        const usernameExists = users.some(user =>
            user.username.toLowerCase() === usernameValue.toLowerCase() &&
            user.id !== id
        );

        if (usernameExists) {
            setFieldError(username, "username-error", "This username is already in use.");
            valid = false;
        }

        const requiresPassword = !id;

        if (requiresPassword && passwordValue.length < 8) {
            setFieldError(password, "password-error", "Password must contain at least 8 characters.");
            valid = false;
        }

        if (id && passwordValue && passwordValue.length < 8) {
            setFieldError(password, "password-error", "Password must contain at least 8 characters.");
            valid = false;
        }

        if ((requiresPassword || passwordValue) && passwordValue !== confirmValue) {
            setFieldError(
                confirmPassword,
                "confirm-password-error",
                "Passwords do not match."
            );
            valid = false;
        }

        if (!valid) return;

        if (id) {
            const index = users.findIndex(user => user.id === id);
            if (index === -1) return;

            users[index] = {
                ...users[index],
                name: nameValue,
                username: usernameValue,
                role: roleValue,
                status: statusValue,
                password: passwordValue || users[index].password
            };

            saveUsers();
            closeUserModal();
            render();

            showToast(
                "User updated",
                `${nameValue}'s account information has been updated.`
            );

            return;
        }

        users.unshift({
            id: generateUserId(),
            name: nameValue,
            username: usernameValue,
            role: roleValue,
            status: statusValue,
            lastLogin: "No login recorded",
            password: passwordValue
        });

        saveUsers();
        closeUserModal();
        render();

        showToast(
            "User created",
            `${nameValue} has been added to the system.`
        );
    }

    function confirmStatusChange(id) {
        closeActionMenus();

        const user = users.find(item => item.id === id);
        if (!user) return;

        const deactivating = user.status === "Active";

        confirmIcon.classList.add("warning");
        confirmTitle.textContent = deactivating
            ? "Deactivate account?"
            : "Activate account?";

        confirmMessage.textContent = deactivating
            ? `${user.name} will no longer be able to access the CGI system.`
            : `${user.name} will be authorized to access the CGI system again.`;

        confirmActionBtn.textContent = deactivating
            ? "Deactivate"
            : "Activate";

        confirmActionBtn.className = deactivating
            ? "btn-danger"
            : "btn-primary";

        confirmationAction = () => {
            user.status = deactivating ? "Inactive" : "Active";

            saveUsers();
            render();

            showToast(
                deactivating ? "Account deactivated" : "Account activated",
                `${user.name}'s account is now ${user.status.toLowerCase()}.`
            );
        };

        confirmModal.hidden = false;
        document.body.style.overflow = "hidden";
    }

    function confirmDeleteUser(id) {
        closeActionMenus();

        const user = users.find(item => item.id === id);
        if (!user) return;

        confirmIcon.classList.remove("warning");
        confirmTitle.textContent = "Delete user?";
        confirmMessage.textContent =
            `${user.name}'s account will be permanently removed from the system. This action cannot be undone.`;

        confirmActionBtn.textContent = "Delete user";
        confirmActionBtn.className = "btn-danger";

        confirmationAction = () => {
            users = users.filter(item => item.id !== id);

            saveUsers();
            render();

            showToast(
                "User deleted",
                `${user.name}'s account has been removed.`
            );
        };

        confirmModal.hidden = false;
        document.body.style.overflow = "hidden";
    }

    function closeConfirmation() {
        confirmModal.hidden = true;
        confirmationAction = null;
        confirmIcon.classList.remove("warning");
        document.body.style.overflow = "";
    }

    function openResetPassword(id) {
        closeActionMenus();

        const user = users.find(item => item.id === id);
        if (!user) return;

        resetPasswordUserId = id;
        resetPassword.value = "";
        resetConfirmPassword.value = "";
        resetPassword.type = "password";
        resetConfirmPassword.type = "password";

        document.getElementById("reset-password-error").textContent = "";
        document.getElementById("reset-confirm-error").textContent = "";

        resetPasswordDescription.textContent =
            `Set a new password for ${user.name}.`;

        resetPasswordModal.hidden = false;
        document.body.style.overflow = "hidden";

        requestAnimationFrame(() => resetPassword.focus());
    }

    function updatePassword() {
        const user = users.find(item => item.id === resetPasswordUserId);
        if (!user) return;

        const newPassword = resetPassword.value;
        const confirmation = resetConfirmPassword.value;

        document.getElementById("reset-password-error").textContent = "";
        document.getElementById("reset-confirm-error").textContent = "";

        let valid = true;

        if (newPassword.length < 8) {
            document.getElementById("reset-password-error").textContent =
                "Password must contain at least 8 characters.";
            valid = false;
        }

        if (newPassword !== confirmation) {
            document.getElementById("reset-confirm-error").textContent =
                "Passwords do not match.";
            valid = false;
        }

        if (!valid) return;

        user.password = newPassword;

        saveUsers();
        closeResetPassword();

        showToast(
            "Password updated",
            `${user.name}'s password has been reset successfully.`
        );
    }

    function closeResetPassword() {
        resetPasswordModal.hidden = true;
        resetPasswordUserId = null;
        resetPasswordForm.reset();
        document.body.style.overflow = "";
    }

    function clearFormErrors() {
        document.querySelectorAll("#user-form .field-error").forEach(error => {
            error.textContent = "";
        });

        document.querySelectorAll("#user-form .has-error").forEach(field => {
            field.classList.remove("has-error");
        });
    }

    function setFieldError(input, errorId, message) {
        const field = input.closest(".form-field");
        const error = document.getElementById(errorId);

        field?.classList.add("has-error");
        if (error) error.textContent = message;
    }

    function generateUserId() {
        return users.length
            ? Math.max(...users.map(user => Number(user.id))) + 1
            : 1;
    }

    function getInitials(name) {
        return name
            .trim()
            .split(/\s+/)
            .slice(0, 2)
            .map(part => part.charAt(0).toUpperCase())
            .join("");
    }

    function showToast(title, message) {
        clearTimeout(toastTimer);

        toastTitle.textContent = title;
        toastMessage.textContent = message;
        toast.hidden = false;

        toastTimer = setTimeout(() => {
            toast.hidden = true;
        }, 3500);
    }

    function escapeHTML(value) {
        return String(value)
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }
});