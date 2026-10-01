<?php
$transactions = [
    [
        "id" => "CGI-20261002-5AY0ST",
        "date" => "2026-10-02",
        "time" => "12:18 AM",
        "coffee" => "Robusta",
        "weight" => 352.1,
        "beans" => 247,
        "defects" => 6,
        "defectPoints" => 3,
        "grade" => "Class I",
        "confidence" => 94.2,
        "referencePrice" => 190.00,
        "suggestedPrice" => 66.90,
        "defectList" => [
            ["name" => "Full Black", "count" => 1],
            ["name" => "Partial Black", "count" => 3],
            ["name" => "Immature", "count" => 2]
        ]
    ],
    [
        "id" => "CGI-20261001-6IXFRV",
        "date" => "2026-10-01",
        "time" => "11:54 PM",
        "coffee" => "Robusta",
        "weight" => 353.9,
        "beans" => 251,
        "defects" => 5,
        "defectPoints" => 2,
        "grade" => "Class I",
        "confidence" => 95.6,
        "referencePrice" => 190.00,
        "suggestedPrice" => 67.24,
        "defectList" => [
            ["name" => "Partial Black", "count" => 3],
            ["name" => "Slight Insect Damage", "count" => 2]
        ]
    ],
    [
        "id" => "CGI-20260930-K8F2PA",
        "date" => "2026-09-30",
        "time" => "04:42 PM",
        "coffee" => "Robusta",
        "weight" => 350.7,
        "beans" => 244,
        "defects" => 2,
        "defectPoints" => 1,
        "grade" => "Extra Class",
        "confidence" => 97.8,
        "referencePrice" => 220.00,
        "suggestedPrice" => 77.15,
        "defectList" => [
            ["name" => "Partial Black", "count" => 2]
        ]
    ],
    [
        "id" => "CGI-20260930-M4Q7BC",
        "date" => "2026-09-30",
        "time" => "02:16 PM",
        "coffee" => "Robusta",
        "weight" => 355.2,
        "beans" => 253,
        "defects" => 11,
        "defectPoints" => 7,
        "grade" => "Class II",
        "confidence" => 91.7,
        "referencePrice" => 160.00,
        "suggestedPrice" => 56.83,
        "defectList" => [
            ["name" => "Full Black", "count" => 2],
            ["name" => "Partial Sour", "count" => 3],
            ["name" => "Immature", "count" => 5],
            ["name" => "Fungus Damage", "count" => 1]
        ]
    ],
    [
        "id" => "CGI-20260929-N2L5XD",
        "date" => "2026-09-29",
        "time" => "10:35 AM",
        "coffee" => "Robusta",
        "weight" => 351.4,
        "beans" => 246,
        "defects" => 4,
        "defectPoints" => 2,
        "grade" => "Extra Class",
        "confidence" => 96.3,
        "referencePrice" => 220.00,
        "suggestedPrice" => 77.31,
        "defectList" => [
            ["name" => "Partial Black", "count" => 3],
            ["name" => "Slight Insect Damage", "count" => 1]
        ]
    ],
    [
        "id" => "CGI-20260928-R9C3HT",
        "date" => "2026-09-28",
        "time" => "03:08 PM",
        "coffee" => "Robusta",
        "weight" => 354.6,
        "beans" => 250,
        "defects" => 8,
        "defectPoints" => 5,
        "grade" => "Class II",
        "confidence" => 92.9,
        "referencePrice" => 160.00,
        "suggestedPrice" => 56.74,
        "defectList" => [
            ["name" => "Full Sour", "count" => 1],
            ["name" => "Partial Black", "count" => 3],
            ["name" => "Immature", "count" => 4]
        ]
    ]
];

$totalTransactions = count($transactions);
$extraClass = count(array_filter($transactions, fn($t) => $t["grade"] === "Extra Class"));
$classI = count(array_filter($transactions, fn($t) => $t["grade"] === "Class I"));
$classII = count(array_filter($transactions, fn($t) => $t["grade"] === "Class II"));

function gradeClass($grade) {
    return match ($grade) {
        "Extra Class" => "grade-extra",
        "Class I" => "grade-one",
        "Class II" => "grade-two",
        default => ""
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Transaction History | CGI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/history-style.css?v=20261002-1">
</head>
<body>

<header class="site-header">
    <a href="dashboard.php" class="nav-brand" aria-label="CGI Dashboard">
        <img src="assets/cgi-logo.png" alt="CGI logo" class="brand-mark">
        <div class="nav-brand-text">
            <span class="nav-brand-name">CGI</span>
            <span class="nav-brand-subtitle">Coffee Grade Identification</span>
        </div>
    </a>

    <nav class="main-nav" aria-label="Main navigation">
        <a href="history.php" class="active" aria-current="page">History</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="settings.php">Settings</a>
    </nav>

    <div class="user-menu">
        <button type="button" class="avatar" id="avatar-btn" aria-haspopup="true" aria-expanded="false" aria-controls="user-dropdown" aria-label="Open user menu">
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

            <button type="button" class="dropdown-item" id="profile-btn">
                <span class="dropdown-item-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z"/>
                    </svg>
                </span>
                <span>User profile</span>
            </button>

            <a href="settings.php" class="dropdown-item">
                <span class="dropdown-item-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M19.14 12.94c.04-.31.06-.63.06-.94s-.02-.63-.06-.94l2.03-1.58-1.92-3.32-2.39.96a7.2 7.2 0 0 0-1.62-.94L14.88 3h-3.76l-.36 2.18c-.58.24-1.12.56-1.62.94l-2.39-.96-1.92 3.32 2.03 1.58A7.64 7.64 0 0 0 6.8 12c0 .31.02.63.06.94l-2.03 1.58 1.92 3.32 2.39-.96c.5.38 1.04.7 1.62.94l.36 2.18h3.76l.36-2.18c.58-.24 1.12-.56 1.62-.94l2.39.96 1.92-3.32-2.03-1.58ZM13 15.46A3.5 3.5 0 1 1 13 8.5a3.5 3.5 0 0 1 0 6.96Z"/>
                    </svg>
                </span>
                <span>Settings</span>
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

<main class="history-main">
    <section class="page-heading">
        <div>
            <span class="eyebrow">TRANSACTION RECORDS</span>
            <h1>Transaction History</h1>
            <p>Review completed coffee quality assessments, inspect results, and reprint previous receipts.</p>
        </div>
        <button type="button" class="export-btn" id="export-btn">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M11 3h2v10.17l3.59-3.58L18 11l-6 6-6-6 1.41-1.41L11 13.17V3ZM5 19h14v2H5v-2Z"/>
            </svg>
            Export records
        </button>
    </section>

    <section class="summary-grid" aria-label="Transaction summary">
        <article class="summary-card summary-total">
            <div class="summary-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 2h9l5 5v15H6V2Zm8 2v4h4.17L14 4ZM8 12h10v-2H8v2Zm0 4h10v-2H8v2Zm0 4h7v-2H8v2Z"/>
                </svg>
            </div>
            <div>
                <strong><?= $totalTransactions ?></strong>
                <span>Total transactions</span>
            </div>
        </article>

        <article class="summary-card summary-extra">
            <div class="summary-dot"></div>
            <div>
                <strong><?= $extraClass ?></strong>
                <span>Extra Class</span>
            </div>
        </article>

        <article class="summary-card summary-one">
            <div class="summary-dot"></div>
            <div>
                <strong><?= $classI ?></strong>
                <span>Class I</span>
            </div>
        </article>

        <article class="summary-card summary-two">
            <div class="summary-dot"></div>
            <div>
                <strong><?= $classII ?></strong>
                <span>Class II</span>
            </div>
        </article>
    </section>

    <section class="history-panel">
        <div class="history-toolbar">
            <div class="search-box">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="m21 19-4.35-4.35A7.5 7.5 0 1 0 15.24 16L19.59 20.35 21 19ZM5 10.5a5.5 5.5 0 1 1 11 0 5.5 5.5 0 0 1-11 0Z"/>
                </svg>
                <input type="search" id="history-search" placeholder="Search transaction ID..." autocomplete="off">
            </div>

            <div class="filter-group">
                <select id="grade-filter" aria-label="Filter by grade">
                    <option value="all">All grades</option>
                    <option value="Extra Class">Extra Class</option>
                    <option value="Class I">Class I</option>
                    <option value="Class II">Class II</option>
                </select>

                <select id="date-filter" aria-label="Filter by date">
                    <option value="all">All dates</option>
                    <option value="today">Today</option>
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                </select>

                <button type="button" class="reset-btn" id="reset-filters">Reset</button>
            </div>
        </div>

        <div class="table-wrap">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Transaction</th>
                        <th>Date & Time</th>
                        <th>Weight</th>
                        <th>Defects</th>
                        <th>Grade</th>
                        <th>Suggested Price</th>
                        <th class="print-heading">Receipt</th>
                        <th class="view-heading"></th>
                    </tr>
                </thead>
                <tbody id="history-body">
                    <?php foreach ($transactions as $transaction): ?>
                    <tr
                        class="transaction-row"
                        tabindex="0"
                        data-transaction='<?= htmlspecialchars(json_encode($transaction), ENT_QUOTES, "UTF-8") ?>'
                        data-id="<?= htmlspecialchars(strtolower($transaction["id"])) ?>"
                        data-grade="<?= htmlspecialchars($transaction["grade"]) ?>"
                        data-date="<?= htmlspecialchars($transaction["date"]) ?>">

                        <td>
                            <div class="transaction-id">
                                <span class="transaction-mark"></span>
                                <div>
                                    <strong><?= htmlspecialchars($transaction["id"]) ?></strong>
                                    <span><?= htmlspecialchars($transaction["coffee"]) ?></span>
                                </div>
                            </div>
                        </td>

                        <td>
                            <strong class="table-date"><?= date("M d, Y", strtotime($transaction["date"])) ?></strong>
                            <span class="table-sub"><?= htmlspecialchars($transaction["time"]) ?></span>
                        </td>

                        <td>
                            <strong><?= number_format($transaction["weight"], 1) ?> g</strong>
                            <span class="table-sub"><?= number_format($transaction["beans"]) ?> beans</span>
                        </td>

                        <td>
                            <strong><?= number_format($transaction["defects"]) ?></strong>
                            <span class="table-sub"><?= number_format($transaction["defectPoints"]) ?> defect points</span>
                        </td>

                        <td>
                            <span class="grade-badge <?= gradeClass($transaction["grade"]) ?>">
                                <?= htmlspecialchars($transaction["grade"]) ?>
                            </span>
                        </td>

                        <td>
                            <strong class="price-value">₱<?= number_format($transaction["suggestedPrice"], 2) ?></strong>
                            <span class="table-sub">₱<?= number_format($transaction["referencePrice"], 2) ?>/kg ref.</span>
                        </td>

                        <td class="print-cell">
                            <button type="button" class="print-btn" aria-label="Print receipt for <?= htmlspecialchars($transaction["id"]) ?>">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M6 9V3h12v6h1a3 3 0 0 1 3 3v5h-4v4H6v-4H2v-5a3 3 0 0 1 3-3h1Zm2-4v4h8V5H8Zm8 14v-5H8v5h8Zm3-4h1v-3a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v3h2v-3h12v3h1Z"/>
                                </svg>
                                Print
                            </button>
                        </td>

                        <td class="view-cell">
                            <button type="button" class="view-btn" aria-label="View <?= htmlspecialchars($transaction["id"]) ?>">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="m9.29 6.71 1.42-1.42L17.41 12l-6.7 6.71-1.42-1.42L14.59 12 9.29 6.71Z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="empty-state" id="empty-state" hidden>
            <div class="empty-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M9.5 3a6.5 6.5 0 1 0 3.98 11.64L19.84 21 21 19.84l-6.36-6.36A6.5 6.5 0 0 0 9.5 3Zm0 2a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9Z"/>
                </svg>
            </div>
            <h3>No matching transactions</h3>
            <p>Try changing your search or transaction filters.</p>
            <button type="button" id="empty-reset">Clear filters</button>
        </div>

        <div class="table-footer">
            <p id="result-count">Showing <?= $totalTransactions ?> transactions</p>
        </div>
    </section>
</main>

<div class="details-overlay" id="details-overlay" hidden>
    <aside class="details-panel" role="dialog" aria-modal="true" aria-labelledby="details-title">
        <div class="details-header">
            <div>
                <span class="eyebrow">TRANSACTION DETAILS</span>
                <h2 id="details-title">Transaction</h2>
                <p id="details-datetime">—</p>
            </div>
        </div>

        <div class="details-body">
            <section class="details-grade">
                <span class="grade-badge" id="details-grade">—</span>
                <div>
                    <span>Classification confidence</span>
                    <strong id="details-confidence">—</strong>
                </div>
            </section>

            <section class="details-section">
                <span class="details-label">SAMPLE INFORMATION</span>
                <div class="details-list">
                    <div><span>Coffee type</span><strong id="details-coffee">—</strong></div>
                    <div><span>Sample weight</span><strong id="details-weight">—</strong></div>
                    <div><span>Detected beans</span><strong id="details-beans">—</strong></div>
                    <div><span>Detected defects</span><strong id="details-defects">—</strong></div>
                </div>
            </section>

            <section class="details-section">
                <div class="section-title-row">
                    <span class="details-label">DEFECT ANALYSIS</span>
                    <span id="details-points">—</span>
                </div>
                <div class="defect-list" id="details-defect-list"></div>
            </section>

            <section class="details-section pricing-section">
                <span class="details-label">SUGGESTED TRANSACTIONAL PRICING</span>
                <div class="details-list">
                    <div><span>Reference price</span><strong id="details-reference">—</strong></div>
                    <div><span>Recorded weight</span><strong id="details-recorded-weight">—</strong></div>
                </div>
                <div class="details-total">
                    <span>Suggested price</span>
                    <strong id="details-price">—</strong>
                </div>
                <p>This suggested price is generated from the recorded assessment and does not represent a finalized sale.</p>
            </section>
        </div>

        <div class="details-footer">
            <button type="button" class="secondary-btn" id="details-close-footer">Close</button>
            <button type="button" class="primary-btn" id="details-print">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 9V3h12v6h1a3 3 0 0 1 3 3v5h-4v4H6v-4H2v-5a3 3 0 0 1 3-3h1Zm2-4v4h8V5H8Zm8 14v-5H8v5h8Zm3-4h1v-3a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v3h2v-3h12v3h1Z"/>
                </svg>
                Print receipt
            </button>
        </div>
    </aside>
</div>

<div id="receipt-print-area" aria-hidden="true"></div>

<script src="script/history-script.js?v=20261002-1"></script>
</body>
</html>