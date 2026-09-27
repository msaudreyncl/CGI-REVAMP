<?php
// Coffee Grade Identification — Dashboard

$systemStatus = "Checking hardware";

$devices = [
    [
        "key" => "camera",
        "name" => "Camera",
        "status" => "Checking",
        "sub" => "Checking connection",
        "icon" => "assets/camera-icon.png"
    ],
    [
        "key" => "weighingScale",
        "name" => "Weighing scale",
        "status" => "Checking",
        "sub" => "Checking connection",
        "icon" => "assets/scale-icon.png"
    ],
    [
        "key" => "printer",
        "name" => "Printer",
        "status" => "Checking",
        "sub" => "Checking connection",
        "icon" => "assets/printer-icon.png"
    ]
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title>Coffee Grade Identification</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Work+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="style/dashboard-style.css">
</head>

<body>

    <div id="bean-field" aria-hidden="true"></div>

    <!-- HEADER -->

    <header class="site-header">

        <div class="brand">
            <img
                src="assets/cgi-logo.png"
                alt="CGI logo"
                class="brand-mark">
        </div>

        <nav aria-label="Main navigation">
            <a href="history.php">History</a>
            <a href="dashboard.php" class="active" aria-current="page">
                Dashboard
            </a>
            <a href="settings.php">Settings</a>
        </nav>

        <div class="user-menu">

            <button
                type="button"
                class="avatar"
                id="avatar-btn"
                aria-haspopup="true"
                aria-expanded="false"
                aria-controls="user-dropdown"
                aria-label="Open user menu">
                <img
                    src="assets/user-icon.png"
                    alt=""
                    class="avatar-icon">
            </button>

            <div
                class="user-dropdown"
                id="user-dropdown"
                hidden>
                <button
                    type="button"
                    class="dropdown-item"
                    id="profile-btn">
                    User profile
                </button>

                <button
                    type="button"
                    class="dropdown-item"
                    id="logout-btn">
                    Log out
                </button>
            </div>

        </div>

    </header>


    <!-- DASHBOARD -->

    <main>

        <section class="panel">

            <div class="panel-head">

                <div class="panel-title">

                    <img
                        src="assets/bean-icon.png"
                        alt=""
                        class="panel-icon">

                    <div>
                        <h1>Coffee Grade Identification</h1>

                        <p>
                            Automated quality &amp;
                            transactional pricing
                        </p>
                    </div>

                </div>

                <div class="header-status">

                    <span
                        class="status-pill status-pill-warning"
                        id="system-status-pill">
                        <span class="dot"></span>
                        <span id="system-status-text">
                            Checking hardware
                        </span>
                    </span>

                    <span
                        class="demo-tag"
                        id="dashboard-demo-tag"
                        hidden>
                        DEMO MODE
                    </span>

                </div>

            </div>


            <div class="panel-body">

                <div class="status-row">

                    <p class="section-label">
                        System status
                    </p>

                    <div class="timestamp">
                        <span id="live-clock">--:--:--</span>
                        <span id="live-date">Loading date...</span>
                    </div>

                </div>


                <!-- HARDWARE STATUS -->

                <div class="device-grid">

                    <?php foreach ($devices as $device): ?>

                        <div
                            class="device-card"
                            data-device="<?php
                                            echo htmlspecialchars($device["key"]);
                                            ?>">

                            <img
                                src="<?php
                                        echo htmlspecialchars($device["icon"]);
                                        ?>"
                                alt=""
                                class="device-icon">

                            <div class="device-info">

                                <p class="device-name">
                                    <?php
                                    echo htmlspecialchars($device["name"]);
                                    ?>
                                </p>

                                <div class="device-status">

                                    <span class="dot dot-warning"></span>

                                    <span class="device-status-text">
                                        <?php
                                        echo htmlspecialchars($device["status"]);
                                        ?>
                                    </span>

                                </div>

                                <p class="device-sub">
                                    <?php
                                    echo htmlspecialchars($device["sub"]);
                                    ?>
                                </p>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>


                <!-- START TRANSACTION -->

                <div class="cta-zone">

                    <div class="cta-content">

                        <span class="cta-eyebrow">
                            COFFEE QUALITY ASSESSMENT
                        </span>

                        <h2>Ready for a new transaction?</h2>

                        <p>
                            Prepare your green coffee bean sample
                            to begin the grading process.
                        </p>

                        <button
                            type="button"
                            class="start-btn"
                            id="start-btn">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M8 5v14l11-7z" />
                            </svg>

                            Start transaction
                        </button>

                    </div>

                </div>


                <!-- LAST TRANSACTION -->

                <div class="last-txn" id="last-txn">
                    No transactions recorded yet
                </div>

            </div>

        </section>

    </main>



    <!-- =====================================================
     FIVE-STEP TRANSACTION MODAL
===================================================== -->

    <div
        class="modal-overlay"
        id="transaction-modal"
        hidden>

        <section
            class="transaction-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-title"
            tabindex="-1">

            <!-- MODAL HEADER -->

            <div class="modal-header">

                <div class="modal-heading">

                    <span class="modal-eyebrow">
                        COFFEE GRADE IDENTIFICATION
                    </span>

                    <h2 id="modal-title">
                        New Transaction
                    </h2>

                    <p id="modal-subtitle">
                        Prepare your coffee bean sample.
                    </p>

                </div>

                <button
                    type="button"
                    class="modal-close"
                    id="modal-close"
                    aria-label="Close transaction">
                    &times;
                </button>

            </div>


            <!-- PROGRESS STEPPER -->

            <div
                class="wizard-progress"
                aria-label="Transaction progress">

                <div class="wizard-step active" data-step-indicator="1">
                    <span class="step-number">1</span>
                    <span class="step-label">Prepare</span>
                </div>

                <div class="step-connector"></div>

                <div class="wizard-step" data-step-indicator="2">
                    <span class="step-number">2</span>
                    <span class="step-label">Capture</span>
                </div>

                <div class="step-connector"></div>

                <div class="wizard-step" data-step-indicator="3">
                    <span class="step-number">3</span>
                    <span class="step-label">Analyze</span>
                </div>

                <div class="step-connector"></div>

                <div class="wizard-step" data-step-indicator="4">
                    <span class="step-number">4</span>
                    <span class="step-label">Results</span>
                </div>

                <div class="step-connector"></div>

                <div class="wizard-step" data-step-indicator="5">
                    <span class="step-number">5</span>
                    <span class="step-label">Receipt</span>
                </div>

            </div>



            <!-- MODAL CONTENT -->

            <div class="modal-body">


                <!-- ==========================================
                 STEP 1: SAMPLE PREPARATION
            =========================================== -->

                <div
                    class="wizard-page active"
                    id="wizard-step-1">

                    <div class="step-heading">

                        <span class="step-eyebrow">
                            STEP 01 / 05
                        </span>

                        <h3>Prepare your sample</h3>

                        <p>
                            Make sure your coffee beans and
                            system devices are ready before
                            starting the assessment.
                        </p>

                    </div>


                    <div class="preparation-card">

                        <div class="preparation-icon">
                            <img
                                src="assets/bean-icon.png"
                                alt="">
                        </div>

                        <div>

                            <span class="small-label">
                                SAMPLE REQUIREMENTS
                            </span>

                            <h4>Green Robusta Coffee Beans</h4>

                            <p>
                                Prepare approximately 350 g
                                of green coffee beans.
                                Spread them evenly in a
                                single layer on the sample tray.
                            </p>

                        </div>

                    </div>


                    <h4 class="group-heading">
                        Hardware readiness
                    </h4>

                    <div class="hardware-check-list">

                        <div
                            class="hardware-check"
                            data-check-device="camera">
                            <span class="hardware-check-name">
                                Camera
                            </span>

                            <span class="hardware-check-status">
                                Checking...
                            </span>
                        </div>

                        <div
                            class="hardware-check"
                            data-check-device="weighingScale">
                            <span class="hardware-check-name">
                                Weighing scale
                            </span>

                            <span class="hardware-check-status">
                                Checking...
                            </span>
                        </div>

                        <div
                            class="hardware-check"
                            data-check-device="printer">
                            <span class="hardware-check-name">
                                Printer
                            </span>

                            <span class="hardware-check-status">
                                Checking...
                            </span>
                        </div>

                    </div>


                    <button
                        type="button"
                        class="text-action"
                        id="refresh-devices-btn">
                        Refresh device status
                    </button>


                    <h4 class="group-heading">
                        Preparation checklist
                    </h4>

                    <div class="checklist">

                        <label class="checklist-item">

                            <input
                                type="checkbox"
                                class="prep-checkbox">

                            <span>
                                The sample tray is clean and dry.
                            </span>

                        </label>

                        <label class="checklist-item">

                            <input
                                type="checkbox"
                                class="prep-checkbox">

                            <span>
                                The green coffee beans are
                                evenly distributed on the tray.
                            </span>

                        </label>

                        <label class="checklist-item">

                            <input
                                type="checkbox"
                                class="prep-checkbox">

                            <span>
                                The sample is ready for weighing
                                and image capture.
                            </span>

                        </label>

                    </div>

                    <p class="wizard-notice" id="prepare-notice">
                        Complete the checklist to continue.
                    </p>

                </div>



                <!-- ==========================================
                 STEP 2: IMAGE CAPTURE AND WEIGHING
            =========================================== -->

                <div
                    class="wizard-page"
                    id="wizard-step-2"
                    hidden>

                    <div class="step-heading">

                        <span class="step-eyebrow">
                            STEP 02 / 05
                        </span>

                        <h3>Capture &amp; weigh</h3>

                        <p>
                            Position your sample inside the
                            camera frame and record its weight.
                        </p>

                    </div>


                    <div class="capture-layout">


                        <!-- LIVE CAMERA -->

                        <div class="camera-panel">

                            <div class="camera-panel-header">

                                <span>
                                    LIVE CAMERA
                                </span>

                                <span
                                    class="camera-indicator"
                                    id="camera-indicator">
                                    OFFLINE
                                </span>

                            </div>


                            <div class="camera-viewport">

                                <video
                                    id="camera-video"
                                    autoplay
                                    muted
                                    playsinline></video>

                                <canvas
                                    id="camera-canvas"
                                    hidden></canvas>

                                <img
                                    id="captured-image"
                                    alt="Captured coffee bean sample"
                                    hidden>

                                <div
                                    class="camera-placeholder"
                                    id="camera-placeholder">
                                    <span class="camera-placeholder-icon">
                                        ◉
                                    </span>

                                    <p>
                                        Camera preview
                                    </p>

                                    <small>
                                        Start the camera to view
                                        your sample.
                                    </small>

                                </div>

                                <div class="camera-guides">
                                    <div class="camera-guide-frame"></div>
                                </div>

                                <div
                                    class="capture-countdown"
                                    id="capture-countdown"
                                    hidden>
                                    3
                                </div>

                            </div>


                            <div class="camera-toolbar">

                                <button
                                    type="button"
                                    class="btn-secondary"
                                    id="open-camera-btn">
                                    Start camera
                                </button>

                                <button
                                    type="button"
                                    class="btn-primary"
                                    id="capture-btn"
                                    disabled>
                                    Capture image
                                </button>

                                <button
                                    type="button"
                                    class="btn-secondary"
                                    id="retake-btn"
                                    hidden>
                                    Retake
                                </button>

                            </div>

                            <p
                                class="camera-message"
                                id="camera-message"
                                role="status">
                                Waiting for camera.
                            </p>

                        </div>



                        <!-- WEIGHING PANEL -->

                        <div class="weighing-panel">

                            <span class="small-label">
                                DIGITAL WEIGHING SCALE
                            </span>

                            <div class="weight-display">

                                <span id="weight-value">
                                    ---.-
                                </span>

                                <span class="weight-unit">
                                    g
                                </span>

                            </div>

                            <span
                                class="weight-status"
                                id="weight-status">
                                Waiting for reading
                            </span>


                            <div class="weight-actions">

                                <button
                                    type="button"
                                    class="btn-secondary"
                                    id="read-weight-btn">
                                    Read weight
                                </button>

                            </div>


                            <div class="weight-divider"></div>


                            <span class="small-label">
                                SAMPLE INFORMATION
                            </span>

                            <div class="sample-info-row">
                                <span>Coffee type</span>
                                <strong>Robusta</strong>
                            </div>

                            <div class="sample-info-row">
                                <span>Sample target</span>
                                <strong>350 g</strong>
                            </div>

                            <div class="sample-info-row">
                                <span>Image</span>
                                <strong id="capture-status">
                                    Not captured
                                </strong>
                            </div>

                            <div class="sample-info-row">
                                <span>Weight</span>
                                <strong id="weight-confirmation">
                                    Not recorded
                                </strong>
                            </div>

                        </div>

                    </div>

                    <p
                        class="wizard-notice"
                        id="capture-notice"
                        role="status">
                        Capture an image and record the sample
                        weight to continue.
                    </p>

                </div>



                <!-- ==========================================
                 STEP 3: COMPUTER VISION PROCESSING
            =========================================== -->

                <div
                    class="wizard-page"
                    id="wizard-step-3"
                    hidden>

                    <div class="step-heading">

                        <span class="step-eyebrow">
                            STEP 03 / 05
                        </span>

                        <h3>Analyzing coffee quality</h3>

                        <p>
                            Processing the captured image
                            and evaluating the coffee sample.
                        </p>

                    </div>


                    <div class="analysis-layout">

                        <div class="analysis-image-container">

                            <img
                                id="analysis-image"
                                alt="Coffee sample being analyzed">

                            <span class="analysis-image-label">
                                CAPTURED SAMPLE
                            </span>

                        </div>


                        <div class="analysis-details">

                            <span class="small-label">
                                ANALYSIS PROGRESS
                            </span>

                            <div class="analysis-percentage">
                                <span id="analysis-percentage">
                                    0
                                </span>%
                            </div>

                            <div
                                class="progress-track"
                                role="progressbar"
                                aria-label="Analysis progress"
                                aria-valuemin="0"
                                aria-valuemax="100"
                                aria-valuenow="0"
                                id="analysis-progressbar">
                                <div
                                    class="progress-fill"
                                    id="analysis-progress"></div>
                            </div>

                            <div class="analysis-stages">

                                <div class="analysis-stage" data-stage="0">
                                    <span class="stage-dot"></span>
                                    Image acquisition
                                </div>

                                <div class="analysis-stage" data-stage="1">
                                    <span class="stage-dot"></span>
                                    Image preprocessing
                                </div>

                                <div class="analysis-stage" data-stage="2">
                                    <span class="stage-dot"></span>
                                    Coffee bean detection
                                </div>

                                <div class="analysis-stage" data-stage="3">
                                    <span class="stage-dot"></span>
                                    Defect identification
                                </div>

                                <div class="analysis-stage" data-stage="4">
                                    <span class="stage-dot"></span>
                                    Quality classification
                                </div>

                                <div class="analysis-stage" data-stage="5">
                                    <span class="stage-dot"></span>
                                    Pricing computation
                                </div>

                            </div>

                        </div>

                    </div>

                    <p
                        class="wizard-notice"
                        id="analysis-notice"
                        role="status"
                        aria-live="polite">
                        Waiting to begin analysis.
                    </p>

                </div>



                <!-- ==========================================
                 STEP 4: RESULTS AND PRICING
            =========================================== -->

                <div
                    class="wizard-page"
                    id="wizard-step-4"
                    hidden>

                    <div class="step-heading">

                        <span class="step-eyebrow">
                            STEP 04 / 05
                        </span>

                        <h3>Quality assessment results</h3>

                        <p>
                            Review the identified coffee grade,
                            sample information, and suggested
                            transaction price.
                        </p>

                    </div>


                    <div class="results-layout">


                        <div class="result-main-card">

                            <span class="small-label">
                                IDENTIFIED COFFEE GRADE
                            </span>

                            <div
                                class="grade-badge"
                                id="result-grade">
                                —
                            </div>

                            <div class="result-confidence">
                                Classification confidence:
                                <strong id="result-confidence">
                                    —
                                </strong>
                            </div>

                            <div class="result-divider"></div>

                            <div class="result-data-row">
                                <span>Coffee type</span>
                                <strong>Robusta</strong>
                            </div>

                            <div class="result-data-row">
                                <span>Sample weight</span>
                                <strong id="result-weight">
                                    —
                                </strong>
                            </div>

                            <div class="result-data-row">
                                <span>Detected beans</span>
                                <strong id="result-bean-count">
                                    —
                                </strong>
                            </div>

                            <div class="result-data-row">
                                <span>Detected defects</span>
                                <strong id="result-defect-count">
                                    —
                                </strong>
                            </div>

                        </div>



                        <div class="pricing-card">

                            <span class="small-label">
                                SUGGESTED TRANSACTIONAL PRICE
                            </span>

                            <div class="suggested-price">
                                ₱<span id="result-total-price">
                                    —
                                </span>
                            </div>

                            <p class="pricing-description">
                                Based on the identified grade,
                                recorded weight, and configured
                                pricing parameters.
                            </p>

                            <div class="pricing-divider"></div>

                            <div class="result-data-row">
                                <span>Grade</span>
                                <strong id="pricing-grade">
                                    —
                                </strong>
                            </div>

                            <div class="result-data-row">
                                <span>Reference price</span>
                                <strong id="result-unit-price">
                                    —
                                </strong>
                            </div>

                            <div class="result-data-row">
                                <span>Recorded weight</span>
                                <strong id="pricing-weight">
                                    —
                                </strong>
                            </div>

                            <p class="pricing-disclaimer">
                                This is a suggested price only.
                                The final transaction price is
                                subject to agreement between
                                the farmer and buyer.
                            </p>

                        </div>

                    </div>


                    <div class="defect-panel">

                        <h4>Defect analysis</h4>

                        <div id="defect-list">
                            No analysis available.
                        </div>

                    </div>

                    <div
                        class="demo-disclaimer"
                        id="results-demo-disclaimer"
                        hidden>
                        DEMO RESULT — These classification
                        values are simulated for interface
                        testing and are not actual AI predictions.
                    </div>

                </div>



                <!-- ==========================================
                 STEP 5: RECEIPT AND COMPLETION
            =========================================== -->

                <div
                    class="wizard-page"
                    id="wizard-step-5"
                    hidden>

                    <div class="step-heading">

                        <span class="step-eyebrow">
                            STEP 05 / 05
                        </span>

                        <h3>Transaction receipt</h3>

                        <p>
                            Review your transaction summary
                            before printing or completing
                            the transaction.
                        </p>

                    </div>


                    <div class="receipt-layout">

                        <div
                            class="receipt-paper"
                            id="receipt-paper">

                            <div class="receipt-brand">

                                <h3>
                                    COFFEE GRADE
                                    IDENTIFICATION
                                </h3>

                                <p>
                                    Quality Assessment Receipt
                                </p>

                            </div>

                            <div class="receipt-rule"></div>

                            <div class="receipt-row">
                                <span>Transaction ID</span>
                                <strong id="receipt-id">—</strong>
                            </div>

                            <div class="receipt-row">
                                <span>Date</span>
                                <strong id="receipt-date">—</strong>
                            </div>

                            <div class="receipt-row">
                                <span>Time</span>
                                <strong id="receipt-time">—</strong>
                            </div>

                            <div class="receipt-rule"></div>

                            <div class="receipt-row">
                                <span>Coffee type</span>
                                <strong>Robusta</strong>
                            </div>

                            <div class="receipt-row">
                                <span>Quality grade</span>
                                <strong id="receipt-grade">—</strong>
                            </div>

                            <div class="receipt-row">
                                <span>Weight</span>
                                <strong id="receipt-weight">—</strong>
                            </div>

                            <div class="receipt-row">
                                <span>Reference price</span>
                                <strong id="receipt-unit-price">—</strong>
                            </div>

                            <div class="receipt-rule"></div>

                            <div class="receipt-total">

                                <span>SUGGESTED PRICE</span>

                                <strong id="receipt-total">
                                    ₱0.00
                                </strong>

                            </div>

                            <div class="receipt-rule"></div>

                            <p class="receipt-note">
                                This receipt contains a suggested
                                transaction price and does not
                                represent a finalized sale.
                            </p>

                            <p
                                class="receipt-demo"
                                id="receipt-demo"
                                hidden>
                                DEMO — SIMULATED ASSESSMENT
                            </p>

                            <p class="receipt-thank-you">
                                Thank you for using CGI.
                            </p>

                        </div>



                        <div class="receipt-actions">

                            <div class="receipt-status-card">

                                <span class="small-label">
                                    PRINTER STATUS
                                </span>

                                <strong id="receipt-printer-status">
                                    Checking...
                                </strong>

                                <p>
                                    Make sure your receipt printer
                                    is connected before printing.
                                </p>

                            </div>

                            <button
                                type="button"
                                class="btn-primary"
                                id="print-receipt-btn">
                                Print receipt
                            </button>

                            <p
                                class="wizard-notice"
                                id="receipt-notice"
                                role="status"></p>

                        </div>

                    </div>

                </div>

            </div>



            <!-- MODAL FOOTER -->

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn-secondary"
                    id="wizard-back-btn"
                    hidden>
                    Back
                </button>

                <div class="footer-spacer"></div>

                <button
                    type="button"
                    class="btn-secondary"
                    id="wizard-cancel-btn">
                    Cancel
                </button>

                <button
                    type="button"
                    class="btn-primary"
                    id="wizard-next-btn"
                    disabled>
                    Continue
                </button>

            </div>

        </section>

    </div>


    <script src="script/transaction-modal.js"></script>
    <script src="script/dashboard-script.js"></script>

</body>

</html>