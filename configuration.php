<?php
// ============================================================
// Coffee Grade Identification — Configuration
// ============================================================

$configuration = [
    "extraClassPrice" => 220.00,
    "classIPrice" => 190.00,
    "classIIPrice" => 160.00,
    "minimumWeight" => 350
];

$defectRules = [
    ["name" => "Full Black", "category" => "Category 1", "ratio" => "1 = 1 point"],
    ["name" => "Full Sour", "category" => "Category 1", "ratio" => "1 = 1 point"],
    ["name" => "Partial Black", "category" => "Category 2", "ratio" => "3 = 1 point"],
    ["name" => "Partial Sour", "category" => "Category 2", "ratio" => "3 = 1 point"],
    ["name" => "Dried Cherry / Pod", "category" => "Category 2", "ratio" => "1 = 1 point"],
    ["name" => "Fungus Damage", "category" => "Category 2", "ratio" => "1 = 1 point"],
    ["name" => "Severe Insect Damage", "category" => "Category 2", "ratio" => "5 = 1 point"],
    ["name" => "Foreign Matter", "category" => "Category 2", "ratio" => "1 = 1 point"],
    ["name" => "Hull / Husk", "category" => "Category 2", "ratio" => "5 = 1 point"],
    ["name" => "Parchment / Pergamino", "category" => "Category 2", "ratio" => "5 = 1 point"],
    ["name" => "Slight Insect Damage", "category" => "Category 2", "ratio" => "10 = 1 point"],
    ["name" => "Floater", "category" => "Category 2", "ratio" => "5 = 1 point"],
    ["name" => "Immature", "category" => "Category 2", "ratio" => "5 = 1 point"]
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Configuration | Coffee Grade Identification</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/configuration-style.css?v=1">
    <link rel="stylesheet" href="style/header-style.css">
</head>

<body>

    <?php
    $currentPage = 'configuration';
    include 'components/header.php';
    ?>

    <main class="configuration-main">
        <section class="configuration-hero">
            <div class="hero-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M19.14 12.94c.04-.31.06-.63.06-.94s-.02-.63-.07-.94l2.03-1.58-1.92-3.32-2.39.96a7.04 7.04 0 0 0-1.62-.94L14.87 3h-3.84l-.36 2.18c-.58.24-1.12.56-1.62.94l-2.39-.96-1.92 3.32 2.03 1.58c-.04.31-.07.64-.07.94s.03.63.07.94l-2.03 1.58 1.92 3.32 2.39-.96c.5.38 1.04.7 1.62.94l.36 2.18h3.84l.36-2.18c.58-.24 1.12-.56 1.62-.94l2.39.96 1.92-3.32-2.02-1.58ZM12.95 15.5a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7Z" />
                </svg>
            </div>
            <div>
                <span class="eyebrow">SYSTEM CONFIGURATION</span>
                <h1>Assessment Configuration</h1>
                <p>Manage the operational parameters used during coffee quality assessment and transactional pricing.</p>
            </div>
        </section>

        <form class="configuration-form" id="configuration-form">
            <section class="config-card pricing-config">
                <div class="config-card-header">
                    <div>
                        <span class="eyebrow">TRANSACTIONAL PRICING</span>
                        <h2>Reference Prices</h2>
                        <p>Set the reference price per kilogram used to calculate the suggested transaction price.</p>
                    </div>
                    <span class="editable-badge">Editable</span>
                </div>

                <div class="price-grid">
                    <label class="price-field">
                        <span>Extra Class</span>
                        <div class="input-unit">
                            <span class="input-prefix">₱</span>
                            <input type="number" id="extra-class-price" name="extraClassPrice" min="0" step="0.01" value="<?= number_format($configuration["extraClassPrice"], 2, ".", "") ?>">
                            <span class="input-suffix">/ kg</span>
                        </div>
                    </label>

                    <label class="price-field">
                        <span>Class I</span>
                        <div class="input-unit">
                            <span class="input-prefix">₱</span>
                            <input type="number" id="class-i-price" name="classIPrice" min="0" step="0.01" value="<?= number_format($configuration["classIPrice"], 2, ".", "") ?>">
                            <span class="input-suffix">/ kg</span>
                        </div>
                    </label>

                    <label class="price-field">
                        <span>Class II</span>
                        <div class="input-unit">
                            <span class="input-prefix">₱</span>
                            <input type="number" id="class-ii-price" name="classIIPrice" min="0" step="0.01" value="<?= number_format($configuration["classIIPrice"], 2, ".", "") ?>">
                            <span class="input-suffix">/ kg</span>
                        </div>
                    </label>
                </div>

                <div class="config-note">
                    <strong>Pricing reference</strong>
                    <span>Changes affect future assessments only. Previously recorded transactions retain the reference price used when they were created.</span>
                </div>
            </section>

            <section class="config-card">
                <div class="config-card-header">
                    <div>
                        <span class="eyebrow">SAMPLE REQUIREMENTS</span>
                        <h2>Assessment Sample</h2>
                        <p>Review the sample requirements used before computer vision processing begins.</p>
                    </div>
                </div>

                <div class="settings-list">
                    <div class="setting-row">
                        <div>
                            <strong>Coffee type</strong>
                            <span>Coffee variety currently supported by CGI</span>
                        </div>
                        <div class="readonly-value">Robusta</div>
                    </div>

                    <div class="setting-row">
                        <div>
                            <strong>Minimum sample weight</strong>
                            <span>Required recorded weight before image capture</span>
                        </div>
                        <div class="compact-input">
                            <input type="number" id="minimum-weight" name="minimumWeight" min="1" step="1" value="<?= $configuration["minimumWeight"] ?>">
                            <span>g</span>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div>
                            <strong>Capture requirement</strong>
                            <span>Views required before the sample can be analyzed</span>
                        </div>
                        <div class="readonly-value">Side A + Side B</div>
                    </div>

                    <div class="setting-row">
                        <div>
                            <strong>Capture method</strong>
                            <span>The complete weighed sample is assessed as one transaction</span>
                        </div>
                        <div class="readonly-value">Complete sample</div>
                    </div>
                </div>
            </section>

            <section class="config-card">
                <div class="config-card-header">
                    <div>
                        <span class="eyebrow">QUALITY CLASSIFICATION</span>
                        <h2>Grading Standard</h2>
                        <p>Review the grading framework and defect-point rules used by the assessment process.</p>
                    </div>
                    <span class="controlled-badge">Controlled</span>
                </div>

                <div class="standard-summary">
                    <div class="standard-primary">
                        <span>STANDARD BASIS</span>
                        <strong>Philippine National Standard</strong>
                        <p>Green coffee bean quality classification and defect assessment.</p>
                    </div>

                    <div class="standard-stat">
                        <span>Supported grades</span>
                        <strong>3</strong>
                        <small>Extra Class · Class I · Class II</small>
                    </div>

                    <div class="standard-stat">
                        <span>Defect categories</span>
                        <strong>2</strong>
                        <small>Category 1 · Category 2</small>
                    </div>
                </div>

                <button type="button" class="criteria-toggle" id="criteria-toggle" aria-expanded="false" aria-controls="defect-criteria">
                    <span>View defect-point criteria</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m7 10 5 5 5-5z" />
                    </svg>
                </button>

                <div class="defect-criteria" id="defect-criteria" hidden>
                    <div class="criteria-head">
                        <span>Defect</span>
                        <span>Category</span>
                        <span>Point equivalent</span>
                    </div>

                    <?php foreach ($defectRules as $rule): ?>
                        <div class="criteria-row">
                            <strong><?= htmlspecialchars($rule["name"]) ?></strong>
                            <span><?= htmlspecialchars($rule["category"]) ?></span>
                            <span><?= htmlspecialchars($rule["ratio"]) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="configuration-actions">
                <div class="save-status" id="save-status" role="status" aria-live="polite">
                    No unsaved changes.
                </div>

                <button type="button" class="btn-secondary" id="reset-config-btn" disabled>
                    Reset changes
                </button>

                <button type="submit" class="btn-primary" id="save-config-btn" disabled>
                    Save configuration
                </button>
            </div>
        </form>
    </main>

    <div class="toast" id="config-toast" role="status" aria-live="polite" hidden>
        <span class="toast-icon">✓</span>
        <div>
            <strong>Configuration saved</strong>
            <span>Updated parameters will be used for future assessments.</span>
        </div>
    </div>

    <script src="script/configuration-script.js?v=1"></script>
</body>

</html>