document.addEventListener("DOMContentLoaded", () => {
    const avatarBtn = document.getElementById("avatar-btn");
    const userDropdown = document.getElementById("user-dropdown");
    const profileBtn = document.getElementById("profile-btn");
    const logoutBtn = document.getElementById("logout-btn");
    const searchInput = document.getElementById("history-search");
    const gradeFilter = document.getElementById("grade-filter");
    const dateFilter = document.getElementById("date-filter");
    const resetBtn = document.getElementById("reset-filters");
    const emptyReset = document.getElementById("empty-reset");
    const emptyState = document.getElementById("empty-state");
    const tableWrap = document.querySelector(".table-wrap");
    const tableFooter = document.querySelector(".table-footer");
    const resultCount = document.getElementById("result-count");
    const rows = [...document.querySelectorAll(".transaction-row")];
    const detailsOverlay = document.getElementById("details-overlay");
    const detailsCloseFooter = document.getElementById("details-close-footer");
    const detailsPrint = document.getElementById("details-print");
    const exportBtn = document.getElementById("export-btn");
    let activeTransaction = null;

    avatarBtn?.addEventListener("click", (event) => {
        event.stopPropagation();
        const opening = userDropdown.hidden;
        userDropdown.hidden = !opening;
        avatarBtn.setAttribute("aria-expanded", String(opening));
    });

    document.addEventListener("click", (event) => {
        if (!event.target.closest(".user-menu") && userDropdown && !userDropdown.hidden) {
            userDropdown.hidden = true;
            avatarBtn?.setAttribute("aria-expanded", "false");
        }
    });

    profileBtn?.addEventListener("click", () => {
        userDropdown.hidden = true;
        avatarBtn?.setAttribute("aria-expanded", "false");
    });

    logoutBtn?.addEventListener("click", () => {
        window.location.href = "logout.php";
    });

    function getTodayString() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, "0");
        const day = String(now.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    }

    function daysBetween(dateString) {
        const today = new Date(`${getTodayString()}T00:00:00`);
        const date = new Date(`${dateString}T00:00:00`);
        return Math.floor((today - date) / 86400000);
    }

    function applyFilters() {
        const query = searchInput.value.trim().toLowerCase();
        const selectedGrade = gradeFilter.value;
        const selectedDate = dateFilter.value;
        let visible = 0;

        rows.forEach((row) => {
            const idMatches = row.dataset.id.includes(query);
            const gradeMatches = selectedGrade === "all" || row.dataset.grade === selectedGrade;
            let dateMatches = true;

            if (selectedDate === "today") {
                dateMatches = row.dataset.date === getTodayString();
            } else if (selectedDate !== "all") {
                const difference = daysBetween(row.dataset.date);
                dateMatches = difference >= 0 && difference < Number(selectedDate);
            }

            const show = idMatches && gradeMatches && dateMatches;
            row.hidden = !show;
            if (show) visible++;
        });

        const noResults = visible === 0;
        emptyState.hidden = !noResults;
        tableWrap.hidden = noResults;
        tableFooter.hidden = noResults;
        resultCount.textContent = `Showing ${visible} ${visible === 1 ? "transaction" : "transactions"}`;
    }

    function resetFilters() {
        searchInput.value = "";
        gradeFilter.value = "all";
        dateFilter.value = "all";
        applyFilters();
        searchInput.focus();
    }

    searchInput.addEventListener("input", applyFilters);
    gradeFilter.addEventListener("change", applyFilters);
    dateFilter.addEventListener("change", applyFilters);
    resetBtn.addEventListener("click", resetFilters);
    emptyReset.addEventListener("click", resetFilters);

    function formatDate(dateString) {
        return new Intl.DateTimeFormat("en-PH", {
            month: "long",
            day: "numeric",
            year: "numeric"
        }).format(new Date(`${dateString}T00:00:00`));
    }

    function formatPeso(value) {
        return `₱${Number(value).toLocaleString("en-PH", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })}`;
    }

    function gradeClass(grade) {
        if (grade === "Extra Class") return "grade-extra";
        if (grade === "Class I") return "grade-one";
        if (grade === "Class II") return "grade-two";
        return "";
    }

    function openDetails(transaction) {
        activeTransaction = transaction;

        document.getElementById("details-title").textContent = transaction.id;
        document.getElementById("details-datetime").textContent = `${formatDate(transaction.date)} · ${transaction.time}`;

        const grade = document.getElementById("details-grade");
        grade.textContent = transaction.grade;
        grade.className = `grade-badge ${gradeClass(transaction.grade)}`;

        document.getElementById("details-confidence").textContent = `${transaction.confidence.toFixed(1)}%`;
        document.getElementById("details-coffee").textContent = transaction.coffee;
        document.getElementById("details-weight").textContent = `${transaction.weight.toFixed(1)} g`;
        document.getElementById("details-beans").textContent = transaction.beans.toLocaleString();
        document.getElementById("details-defects").textContent = transaction.defects.toLocaleString();
        document.getElementById("details-points").textContent = `${transaction.defectPoints} defect ${transaction.defectPoints === 1 ? "point" : "points"}`;
        document.getElementById("details-reference").textContent = `${formatPeso(transaction.referencePrice)}/kg`;
        document.getElementById("details-recorded-weight").textContent = `${transaction.weight.toFixed(1)} g`;
        document.getElementById("details-price").textContent = formatPeso(transaction.suggestedPrice);

        const defectList = document.getElementById("details-defect-list");
        defectList.innerHTML = "";

        if (!transaction.defectList.length) {
            defectList.innerHTML = `<div class="defect-item"><span>No recorded defects</span><strong>0</strong></div>`;
        } else {
            transaction.defectList.forEach((defect) => {
                const item = document.createElement("div");
                item.className = "defect-item";
                item.innerHTML = `<span>${escapeHTML(defect.name)}</span><strong>${defect.count}</strong>`;
                defectList.appendChild(item);
            });
        }

        detailsOverlay.hidden = false;
        document.body.classList.add("details-open");
        detailsCloseFooter?.focus();
    }

    function closeDetails() {
        detailsOverlay.hidden = true;
        document.body.classList.remove("details-open");
        activeTransaction = null;
    }

    function escapeHTML(value) {
        return String(value)
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    rows.forEach((row) => {
        const transaction = JSON.parse(row.dataset.transaction);

        row.addEventListener("click", (event) => {
            if (event.target.closest(".print-btn") || event.target.closest(".view-btn")) return;
            openDetails(transaction);
        });

        row.addEventListener("keydown", (event) => {
            if (event.key === "Enter") openDetails(transaction);
        });

        row.querySelector(".view-btn").addEventListener("click", () => {
            openDetails(transaction);
        });

        row.querySelector(".print-btn").addEventListener("click", () => {
            printReceipt(transaction);
        });
    });

    detailsCloseFooter?.addEventListener("click", closeDetails);

    detailsOverlay.addEventListener("click", (event) => {
        if (event.target === detailsOverlay) closeDetails();
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && !detailsOverlay.hidden) closeDetails();
    });

    detailsPrint.addEventListener("click", () => {
        if (activeTransaction) printReceipt(activeTransaction);
    });

    function buildReceipt(transaction) {
        return `
        <div class="receipt-paper">
            <div class="receipt-brand">
                <h3>COFFEE GRADE<br>IDENTIFICATION</h3>
                <p>Quality Assessment Receipt</p>
            </div>

            <div class="receipt-rule"></div>

            <div class="receipt-row">
                <span>Transaction ID</span>
                <strong>${escapeHTML(transaction.id)}</strong>
            </div>
            <div class="receipt-row">
                <span>Date</span>
                <strong>${escapeHTML(formatDate(transaction.date))}</strong>
            </div>
            <div class="receipt-row">
                <span>Time</span>
                <strong>${escapeHTML(transaction.time)}</strong>
            </div>

            <div class="receipt-rule"></div>

            <div class="receipt-row">
                <span>Coffee type</span>
                <strong>${escapeHTML(transaction.coffee)}</strong>
            </div>
            <div class="receipt-row">
                <span>Quality grade</span>
                <strong>${escapeHTML(transaction.grade)}</strong>
            </div>
            <div class="receipt-row">
                <span>Weight</span>
                <strong>${transaction.weight.toFixed(1)} g</strong>
            </div>
            <div class="receipt-row">
                <span>Reference price</span>
                <strong>${formatPeso(transaction.referencePrice)} / kg</strong>
            </div>

            <div class="receipt-rule"></div>

            <div class="receipt-total">
                <span>SUGGESTED PRICE</span>
                <strong>${formatPeso(transaction.suggestedPrice)}</strong>
            </div>

            <div class="receipt-rule"></div>

            <p class="receipt-note">
                This receipt contains a suggested transaction price and does not represent a finalized sale.
            </p>

            <p class="receipt-thank-you">
                Thank you for using CGI.
            </p>
        </div>
    `;
    }

    function printReceipt(transaction) {
        const printArea = document.getElementById("receipt-print-area");

        if (!printArea) return;

        printArea.innerHTML = buildReceipt(transaction);
        document.body.classList.add("printing-history-receipt");

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                window.print();
            });
        });
    }

    window.addEventListener("afterprint", () => {
        document.body.classList.remove("printing-history-receipt");
    });

    exportBtn.addEventListener("click", () => {
        const visibleTransactions = rows
            .filter((row) => !row.hidden)
            .map((row) => JSON.parse(row.dataset.transaction));

        if (!visibleTransactions.length) return;

        const headers = [
            "Transaction ID",
            "Date",
            "Time",
            "Coffee Type",
            "Weight (g)",
            "Detected Beans",
            "Detected Defects",
            "Defect Points",
            "Grade",
            "Confidence (%)",
            "Reference Price (PHP/kg)",
            "Suggested Price (PHP)"
        ];

        const csvRows = visibleTransactions.map((transaction) => [
            transaction.id,
            transaction.date,
            transaction.time,
            transaction.coffee,
            transaction.weight,
            transaction.beans,
            transaction.defects,
            transaction.defectPoints,
            transaction.grade,
            transaction.confidence,
            transaction.referencePrice,
            transaction.suggestedPrice
        ]);

        const csv = [headers, ...csvRows]
            .map((row) => row.map(csvValue).join(","))
            .join("\n");

        const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download = `CGI-Transaction-History-${getTodayString()}.csv`;
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(url);
    });

    function csvValue(value) {
        const string = String(value ?? "");
        return `"${string.replaceAll('"', '""')}"`;
    }

    applyFilters();
});