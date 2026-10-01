document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("configuration-form");
    const avatarBtn = document.getElementById("avatar-btn");
    const userDropdown = document.getElementById("user-dropdown");
    const logoutBtn = document.getElementById("logout-btn");
    const criteriaToggle = document.getElementById("criteria-toggle");
    const defectCriteria = document.getElementById("defect-criteria");
    const resetBtn = document.getElementById("reset-config-btn");
    const saveBtn = document.getElementById("save-config-btn");
    const saveStatus = document.getElementById("save-status");
    const toast = document.getElementById("config-toast");

    const fields = {
        extraClassPrice: document.getElementById("extra-class-price"),
        classIPrice: document.getElementById("class-i-price"),
        classIIPrice: document.getElementById("class-ii-price"),
        minimumWeight: document.getElementById("minimum-weight")
    };

    const defaultConfig = {
        extraClassPrice: 220,
        classIPrice: 190,
        classIIPrice: 160,
        minimumWeight: 350
    };

    let savedConfig = loadConfiguration();
    let toastTimer = null;

    applyConfiguration(savedConfig);
    setCleanState();

    avatarBtn?.addEventListener("click", event => {
        event.stopPropagation();
        const willOpen = userDropdown.hidden;
        userDropdown.hidden = !willOpen;
        avatarBtn.setAttribute("aria-expanded", String(willOpen));
    });

    document.addEventListener("click", event => {
        if (!userDropdown || userDropdown.hidden) return;
        if (!event.target.closest(".user-menu")) closeUserDropdown();
    });

    document.addEventListener("keydown", event => {
        if (event.key === "Escape") closeUserDropdown();
    });

    logoutBtn?.addEventListener("click", () => {
        window.location.href = "logout.php";
    });

    criteriaToggle?.addEventListener("click", () => {
        const expanded = criteriaToggle.getAttribute("aria-expanded") === "true";
        criteriaToggle.setAttribute("aria-expanded", String(!expanded));
        defectCriteria.hidden = expanded;
    });

    Object.values(fields).forEach(field => {
        field?.addEventListener("input", updateDirtyState);
    });

    resetBtn?.addEventListener("click", () => {
        applyConfiguration(savedConfig);
        setCleanState();
    });

    form?.addEventListener("submit", event => {
        event.preventDefault();

        const config = readConfiguration();

        if (!validateConfiguration(config)) return;

        savedConfig = config;
        localStorage.setItem("cgiConfiguration", JSON.stringify(savedConfig));
        setCleanState();
        showToast();
    });

    function closeUserDropdown() {
        if (!userDropdown || !avatarBtn) return;
        userDropdown.hidden = true;
        avatarBtn.setAttribute("aria-expanded", "false");
    }

    function loadConfiguration() {
        try {
            const stored = JSON.parse(localStorage.getItem("cgiConfiguration"));
            if (!stored) return {...defaultConfig};

            return {
                extraClassPrice: Number(stored.extraClassPrice ?? defaultConfig.extraClassPrice),
                classIPrice: Number(stored.classIPrice ?? defaultConfig.classIPrice),
                classIIPrice: Number(stored.classIIPrice ?? defaultConfig.classIIPrice),
                minimumWeight: Number(stored.minimumWeight ?? defaultConfig.minimumWeight)
            };
        } catch {
            return {...defaultConfig};
        }
    }

    function readConfiguration() {
        return {
            extraClassPrice: Number(fields.extraClassPrice.value),
            classIPrice: Number(fields.classIPrice.value),
            classIIPrice: Number(fields.classIIPrice.value),
            minimumWeight: Number(fields.minimumWeight.value)
        };
    }

    function applyConfiguration(config) {
        fields.extraClassPrice.value = Number(config.extraClassPrice).toFixed(2);
        fields.classIPrice.value = Number(config.classIPrice).toFixed(2);
        fields.classIIPrice.value = Number(config.classIIPrice).toFixed(2);
        fields.minimumWeight.value = Number(config.minimumWeight);
    }

    function validateConfiguration(config) {
        const invalid = Object.values(config).some(value => !Number.isFinite(value) || value <= 0);

        if (invalid) {
            saveStatus.textContent = "Enter valid values greater than zero.";
            saveStatus.style.color = "var(--danger)";
            return false;
        }

        return true;
    }

    function hasChanges() {
        const current = readConfiguration();

        return Object.keys(savedConfig).some(key =>
            Number(current[key]) !== Number(savedConfig[key])
        );
    }

    function updateDirtyState() {
        const dirty = hasChanges();

        resetBtn.disabled = !dirty;
        saveBtn.disabled = !dirty;
        saveStatus.textContent = dirty
            ? "You have unsaved configuration changes."
            : "No unsaved changes.";
        saveStatus.style.color = "";
    }

    function setCleanState() {
        resetBtn.disabled = true;
        saveBtn.disabled = true;
        saveStatus.textContent = "Configuration is up to date.";
        saveStatus.style.color = "";
    }

    function showToast() {
        clearTimeout(toastTimer);
        toast.hidden = false;

        toastTimer = setTimeout(() => {
            toast.hidden = true;
        }, 3500);
    }
});