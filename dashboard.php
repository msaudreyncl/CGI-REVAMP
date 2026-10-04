<?php
// ============================================================
// Coffee Grade Identification — Dashboard
// ============================================================

$systemStatus = "System ready";

$devices = [
    [
        "key" => "camera",
        "name" => "Camera",
        "status" => "Ready",
        "sub" => "Camera available",
        "icon" => "assets/camera-icon.png"
    ],
    [
        "key" => "weighingScale",
        "name" => "Weighing scale",
        "status" => "Ready",
        "sub" => "Scale available",
        "icon" => "assets/scale-icon.png"
    ],
    [
        "key" => "printer",
        "name" => "Printer",
        "status" => "Ready",
        "sub" => "Printer available",
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
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/dashboard-style.css?v=20261001-2">
    <link rel="stylesheet" href="style/header-style.css">
</head>


<body>

    <div id="bean-field" aria-hidden="true"></div>


    <!-- =========================================================
     HEADER
========================================================= -->

    <?php
    $currentPage = 'dashboard';
    include 'components/header.php';
    ?>  


    <!-- =========================================================
     DASHBOARD
========================================================= -->

    <main>

        <section class="panel">

            <div class="panel-head">

                <div class="panel-title">

                    <img
                        src="assets/bean-icon.png"
                        alt=""
                        class="panel-icon">

                    <div>

                        <h1>
                            Coffee Grade Identification
                        </h1>

                        <p>
                            Automated quality &amp;
                            transactional pricing
                        </p>

                    </div>

                </div>


                <div class="header-status">

                    <span
                        class="status-pill status-pill-success"
                        id="system-status-pill">

                        <span class="dot"></span>

                        <span id="system-status-text">
                            System ready
                        </span>

                    </span>

                </div>

            </div>


            <div class="panel-body">

                <div class="status-row">

                    <p class="section-label">
                        System status
                    </p>

                    <div class="timestamp">

                        <span id="live-clock">
                            --:--:--
                        </span>

                        <span id="live-date">
                            Loading date...
                        </span>

                    </div>

                </div>


                <!-- =================================================
             HARDWARE
        ================================================== -->

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

                                    <span class="dot dot-success"></span>

                                    <span class="device-status-text">
                                        Ready
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


                <!-- =================================================
             START TRANSACTION
        ================================================== -->

                <div class="cta-zone">

                    <div class="cta-content">

                        <span class="cta-eyebrow">
                            COFFEE QUALITY ASSESSMENT
                        </span>

                        <h2>
                            Ready for a new transaction?
                        </h2>

                        <p>
                            Prepare the required green Robusta
                            coffee bean sample to begin.
                        </p>


                        <button
                            type="button"
                            class="start-btn"
                            id="start-btn">

                            <svg
                                viewBox="0 0 24 24"
                                aria-hidden="true">

                                <path d="M8 5v14l11-7z" />

                            </svg>

                            Start transaction

                        </button>

                    </div>

                </div>


                <div
                    class="last-txn"
                    id="last-txn">

                    No transactions recorded yet

                </div>

            </div>

        </section>

    </main>


    <!-- =========================================================
     TRANSACTION MODAL
     6 STEPS:
     1 Prepare
     2 Weight
     3 Capture
     4 Analyze
     5 Results
     6 Receipt
========================================================= -->

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


            <!-- =========================================================
     MODAL HEADER
========================================================= -->

            <div class="modal-header">

                <div class="modal-heading">

                    <span class="modal-eyebrow">
                        COFFEE GRADE IDENTIFICATION
                    </span>

                    <h2 id="modal-title">
                        New Transaction
                    </h2>

                    <p id="modal-subtitle">
                        Prepare the coffee sample.
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


            <!-- =========================================================
     PROGRESS
========================================================= -->

            <div
                class="wizard-progress"
                aria-label="Transaction progress">


                <div
                    class="wizard-step active"
                    data-step-indicator="1">

                    <span class="step-number">
                        1
                    </span>

                    <span class="step-label">
                        Prepare
                    </span>

                </div>


                <div class="step-connector"></div>


                <div
                    class="wizard-step"
                    data-step-indicator="2">

                    <span class="step-number">
                        2
                    </span>

                    <span class="step-label">
                        Weight
                    </span>

                </div>


                <div class="step-connector"></div>


                <div
                    class="wizard-step"
                    data-step-indicator="3">

                    <span class="step-number">
                        3
                    </span>

                    <span class="step-label">
                        Capture
                    </span>

                </div>


                <div class="step-connector"></div>


                <div
                    class="wizard-step"
                    data-step-indicator="4">

                    <span class="step-number">
                        4
                    </span>

                    <span class="step-label">
                        Analyze
                    </span>

                </div>


                <div class="step-connector"></div>


                <div
                    class="wizard-step"
                    data-step-indicator="5">

                    <span class="step-number">
                        5
                    </span>

                    <span class="step-label">
                        Results
                    </span>

                </div>


                <div class="step-connector"></div>


                <div
                    class="wizard-step"
                    data-step-indicator="6">

                    <span class="step-number">
                        6
                    </span>

                    <span class="step-label">
                        Receipt
                    </span>

                </div>

            </div>


            <!-- =========================================================
     MODAL BODY
========================================================= -->

            <div class="modal-body">


                <!-- =========================================================
     STEP 1 — PREPARE
========================================================= -->

                <div
                    class="wizard-page active"
                    id="wizard-step-1">

                    <div class="step-heading">

                        <span class="step-eyebrow">
                            STEP 1 OF 6
                        </span>

                        <h3>
                            Prepare the transaction sample
                        </h3>

                        <p>
                            Prepare the complete green Robusta coffee
                            bean sample and verify that the system is
                            ready before weighing.
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
                                REQUIRED SAMPLE
                            </span>

                            <h4>
                                Green Robusta Coffee Beans
                            </h4>

                            <p>
                                Place the complete coffee bean sample
                                on the clean sample tray. The system
                                evaluates the entire weighed batch as
                                one transaction.
                            </p>

                        </div>

                    </div>


                    <h4 class="group-heading">
                        Hardware readiness
                    </h4>


                    <div class="hardware-check-list">
                        <div class="hardware-check ready" data-check-device="camera">
                            <div class="hardware-check-icon">
                                <img src="assets/camera-icon.png" alt="">
                            </div>

                            <div class="hardware-check-info">
                                <span class="hardware-check-name">Camera</span>
                                <span class="hardware-check-detail">Image acquisition device</span>
                            </div>

                            <span class="hardware-check-status">
                                <span class="hardware-status-dot"></span>
                                <span class="hardware-status-text">Ready</span>
                            </span>
                        </div>

                        <div class="hardware-check ready" data-check-device="weighingScale">
                            <div class="hardware-check-icon">
                                <img src="assets/scale-icon.png" alt="">
                            </div>

                            <div class="hardware-check-info">
                                <span class="hardware-check-name">Weighing scale</span>
                                <span class="hardware-check-detail">Sample weight acquisition</span>
                            </div>

                            <span class="hardware-check-status">
                                <span class="hardware-status-dot"></span>
                                <span class="hardware-status-text">Ready</span>
                            </span>
                        </div>

                        <div class="hardware-check ready" data-check-device="printer">
                            <div class="hardware-check-icon">
                                <img src="assets/printer-icon.png" alt="">
                            </div>

                            <div class="hardware-check-info">
                                <span class="hardware-check-name">Printer</span>
                                <span class="hardware-check-detail">Transaction receipt output</span>
                            </div>

                            <span class="hardware-check-status">
                                <span class="hardware-status-dot"></span>
                                <span class="hardware-status-text">Ready</span>
                            </span>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="refresh-devices-btn"
                        id="refresh-devices-btn">

                        <span class="refresh-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M17.65 6.35A7.95 7.95 0 0 0 12 4a8 8 0 1 0 7.75 10h-2.08A6 6 0 1 1 12 6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35Z"/>
                            </svg>
                        </span>

                        <span class="refresh-spinner" aria-hidden="true"></span>

                        <span class="refresh-label">
                            Refresh device status
                        </span>
                    </button>


                    <div class="checklist">
                    <label class="checklist-item">
                        <input
                            type="checkbox"
                            class="prep-checkbox">

                        <span class="checklist-control" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="m9 16.2-3.5-3.5L4.1 14.1 9 19 20.3 7.7l-1.4-1.4L9 16.2Z"/>
                            </svg>
                        </span>

                        <span class="checklist-content">
                            <strong>Sample tray prepared</strong>
                            <small>The sample tray is clean and dry.</small>
                        </span>
                    </label>

                    <label class="checklist-item">
                        <input
                            type="checkbox"
                            class="prep-checkbox">

                        <span class="checklist-control" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="m9 16.2-3.5-3.5L4.1 14.1 9 19 20.3 7.7l-1.4-1.4L9 16.2Z"/>
                            </svg>
                        </span>

                        <span class="checklist-content">
                            <strong>Sample distributed</strong>
                            <small>
                                The complete green coffee bean sample is
                                distributed across the tray.
                            </small>
                        </span>
                    </label>

                    <label class="checklist-item">
                        <input
                            type="checkbox"
                            class="prep-checkbox">

                        <span class="checklist-control" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="m9 16.2-3.5-3.5L4.1 14.1 9 19 20.3 7.7l-1.4-1.4L9 16.2Z"/>
                            </svg>
                        </span>

                        <span class="checklist-content">
                            <strong>Ready for weighing</strong>
                            <small>
                                The prepared sample is ready to be weighed.
                            </small>
                        </span>
                    </label>
                </div>

                </div>


                <!-- =========================================================
     STEP 2 — WEIGHT
========================================================= -->

                <div
                    class="wizard-page"
                    id="wizard-step-2"
                    hidden>

                    <div class="step-heading">

                        <span class="step-eyebrow">
                            STEP 2 OF 6
                        </span>

                        <h3>
                            Weigh the coffee sample
                        </h3>

                        <p>
                            Place the complete coffee bean sample on the
                            weighing scale. The transaction requires a
                            minimum sample weight of 350 g before image
                            capture can begin.
                        </p>

                    </div>


                    <div class="weight-step-layout">


                        <!-- SCALE -->

                        <div class="scale-display-card">

                            <span class="scale-label">
                                Digital weighing scale
                            </span>


                            <div class="large-weight-display">

                                <span id="weight-value">
                                    0.0
                                </span>

                                <span class="weight-unit">
                                    g
                                </span>

                            </div>


                            <div
                                class="weight-status-large required"
                                id="weight-status">

                                Weight required: 350 g

                            </div>


                            <button
                                type="button"
                                class="btn-primary"
                                id="read-weight-btn">

                                Read weight

                            </button>


                            <div
                                class="weight-requirement-card"
                                id="weight-requirement">

                                <strong>
                                    Required sample weight
                                </strong>

                                <span>
                                    Minimum: 350 g
                                </span>

                            </div>

                        </div>


                        <!-- SAMPLE INFORMATION -->

                        <div class="weight-information-card">

                            <span class="small-label">
                                TRANSACTION SAMPLE
                            </span>

                            <h4>
                                Sample information
                            </h4>


                            <div class="weight-info-row">

                                <span>
                                    Coffee type
                                </span>

                                <strong>
                                    Robusta
                                </strong>

                            </div>


                            <div class="weight-info-row">

                                <span>
                                    Required weight
                                </span>

                                <strong>
                                    350 g
                                </strong>

                            </div>


                            <div class="weight-info-row">

                                <span>
                                    Current weight
                                </span>

                                <strong id="weight-confirmation">
                                    Not recorded
                                </strong>

                            </div>


                            <div class="weight-info-row">

                                <span>
                                    Weight status
                                </span>

                                <strong id="weight-readiness">
                                    Waiting
                                </strong>

                            </div>


                            <div class="weight-requirement-card">

                                <strong>
                                    Why is the weight required?
                                </strong>

                                <span>
                                    The entire weighed sample is treated
                                    as one transaction batch. The recorded
                                    weight is also used when computing the
                                    suggested transactional price.
                                </span>

                            </div>

                        </div>

                    </div>


                    <p
                        class="wizard-notice"
                        id="weight-notice"
                        role="status">

                        Record a sample weight of at least 350 g to continue.

                    </p>

                </div>

                <!-- =========================================================
     STEP 3 — CAPTURE
========================================================= -->

                <div
                    class="wizard-page"
                    id="wizard-step-3"
                    hidden>

                    <div class="step-heading">

                        <span class="step-eyebrow">
                            STEP 3 OF 6
                        </span>

                        <h3>
                            Capture both sides of the sample
                        </h3>

                        <p>
                            Keep the complete sample together on the tray.
                            Capture Side A first, then turn the sample over
                            and capture Side B. Both sides are required
                            before analysis.
                        </p>

                    </div>


                    <div class="capture-layout">


                        <!-- =================================================
             CAMERA
        ================================================== -->

                        <div class="camera-panel">

                            <div class="camera-panel-header">

                                <span>
                                    SAMPLE CAMERA
                                </span>

                                <span
                                    class="camera-indicator"
                                    id="camera-indicator">

                                    READY

                                </span>

                            </div>


                            <!-- CAMERA VIEW -->

                            <div class="camera-viewport">

                                <video
                                    id="camera-video"
                                    autoplay
                                    muted
                                    playsinline>
                                </video>


                                <canvas
                                    id="camera-canvas"
                                    hidden>
                                </canvas>


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
                                        Start the camera to capture
                                        the sample.
                                    </small>

                                </div>


                                <div class="camera-guides">
                                    <div class="camera-guide-frame"></div>
                                </div>

                            </div>


                            <!-- =============================================
                 SIDE NAVIGATION
                 Placed directly below the camera
            ============================================== -->

                            <div class="side-selector">

                                <button
                                    type="button"
                                    class="active"
                                    data-capture-side="A">

                                    <span class="side-selector-name">
                                        SIDE A
                                    </span>

                                    <span class="side-selector-state">
                                        Active
                                    </span>

                                </button>


                                <button
                                    type="button"
                                    data-capture-side="B">

                                    <span class="side-selector-name">
                                        SIDE B
                                    </span>

                                    <span class="side-selector-state">
                                        Not captured
                                    </span>

                                </button>

                            </div>


                            <!-- CAMERA MESSAGE -->

                            <p
                                class="camera-message"
                                id="camera-message"
                                role="status">

                                Start the camera to capture Side A.

                            </p>


                            <!-- CAMERA ACTIONS -->

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

                                    Capture Side A

                                </button>


                                <button
                                    type="button"
                                    class="btn-secondary"
                                    id="retake-btn"
                                    hidden>

                                    Retake

                                </button>

                            </div>


                            <!-- SIDE CAPTURE STATUS -->

                        </div>


                        <!-- =================================================
             CAPTURE INFORMATION
        ================================================== -->

                        <div class="weighing-panel">

                            <span class="small-label">
                                SAMPLE STATUS
                            </span>


                            <div class="sample-info-row">

                                <span>
                                    Coffee type
                                </span>

                                <strong>
                                    Robusta
                                </strong>

                            </div>


                            <div class="sample-info-row">

                                <span>
                                    Recorded weight
                                </span>

                                <strong id="capture-weight">
                                    —
                                </strong>

                            </div>


                            <div class="sample-info-row">

                                <span>
                                    Side A
                                </span>

                                <strong id="side-a-confirmation">
                                    Not captured
                                </strong>

                            </div>


                            <div class="sample-info-row">

                                <span>
                                    Side B
                                </span>

                                <strong id="side-b-confirmation">
                                    Not captured
                                </strong>

                            </div>


                            <div class="weight-divider"></div>


                            <span class="small-label">
                                CAPTURE REQUIREMENT
                            </span>


                            <p>
                                The system requires two views of the
                                complete sample before computer vision
                                processing can begin.
                            </p>


                            <div class="weight-requirement-card">

                                <strong>
                                    Both sides required
                                </strong>

                                <span>
                                    Side A + Side B must be captured
                                    successfully.
                                </span>

                            </div>

                        </div>

                    </div>


                    <p
                        class="wizard-notice"
                        id="capture-notice"
                        role="status">

                        Capture Side A and Side B to continue.

                    </p>

                </div>


                <!-- =========================================================
     STEP 4 — ANALYZE
========================================================= -->

                <div
                    class="wizard-page"
                    id="wizard-step-4"
                    hidden>

                    <div class="step-heading">

                        <span class="step-eyebrow">
                            STEP 4 OF 6
                        </span>

                        <h3>
                            Analyze the coffee sample
                        </h3>

                        <p>
                            The two captured views are processed together.
                            The system detects the beans, identifies defects,
                            determines the quality grade, and calculates the
                            suggested transactional price.
                        </p>

                    </div>


                    <div class="analysis-layout">

                        <!-- =========================================
         CAPTURED SAMPLE PREVIEWS
    ========================================== -->

                        <div class="analysis-preview-section">

                            <div class="analysis-preview-header">

                                <div>
                                    <span class="small-label">
                                        CAPTURED SAMPLE VIEWS
                                    </span>

                                    <p>
                                        Both captured sides are processed together
                                        during quality assessment.
                                    </p>
                                </div>

                            </div>


                            <div class="analysis-preview-grid">

                                <!-- SIDE A -->

                                <div class="analysis-preview-card">

                                    <div class="analysis-image-container">

                                        <img
                                            id="analysis-side-a"
                                            alt="Captured Side A of coffee sample">

                                        <span class="analysis-image-label">
                                            SIDE A
                                        </span>

                                    </div>

                                    <div class="analysis-preview-caption">

                                        <strong>Side A</strong>

                                        <span>
                                            First captured view
                                        </span>

                                    </div>

                                </div>


                                <!-- SIDE B -->

                                <div class="analysis-preview-card">

                                    <div class="analysis-image-container">

                                        <img
                                            id="analysis-side-b"
                                            alt="Captured Side B of coffee sample">

                                        <span class="analysis-image-label">
                                            SIDE B
                                        </span>

                                    </div>

                                    <div class="analysis-preview-caption">

                                        <strong>Side B</strong>

                                        <span>
                                            Reoriented sample view
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- =========================================
            ANALYSIS PROGRESS
        ========================================== -->

                        <div class="analysis-details">

                            <div class="analysis-progress-header">

                                <div>

                                    <span class="small-label">
                                        ANALYSIS PROGRESS
                                    </span>

                                    <strong>
                                        Processing coffee sample
                                    </strong>

                                </div>


                                <div class="analysis-percentage">

                                    <span id="analysis-percentage">
                                        0
                                    </span>%

                                </div>

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
                                    id="analysis-progress">
                                </div>

                            </div>


                            <div class="analysis-stages">

                                <div
                                    class="analysis-stage"
                                    data-stage="0">

                                    <span class="stage-dot"></span>

                                    <span>
                                        Validate both captured sides
                                    </span>

                                </div>


                                <div
                                    class="analysis-stage"
                                    data-stage="1">

                                    <span class="stage-dot"></span>

                                    <span>
                                        Image preprocessing
                                    </span>

                                </div>


                                <div
                                    class="analysis-stage"
                                    data-stage="2">

                                    <span class="stage-dot"></span>

                                    <span>
                                        Coffee bean detection
                                    </span>

                                </div>


                                <div
                                    class="analysis-stage"
                                    data-stage="3">

                                    <span class="stage-dot"></span>

                                    <span>
                                        Defect identification
                                    </span>

                                </div>


                                <div
                                    class="analysis-stage"
                                    data-stage="4">

                                    <span class="stage-dot"></span>

                                    <span>
                                        Quality classification
                                    </span>

                                </div>


                                <div
                                    class="analysis-stage"
                                    data-stage="5">

                                    <span class="stage-dot"></span>

                                    <span>
                                        Pricing computation
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="preparation-card">

                        <div>

                            <span class="small-label">
                                SAMPLE BEING PROCESSED
                            </span>

                            <div class="sample-info-row">

                                <span>
                                    Weight
                                </span>

                                <strong id="analysis-weight">
                                    —
                                </strong>

                            </div>


                            <div class="sample-info-row">

                                <span>
                                    Side A
                                </span>

                                <strong>
                                    Captured
                                </strong>

                            </div>


                            <div class="sample-info-row">

                                <span>
                                    Side B
                                </span>

                                <strong>
                                    Captured
                                </strong>

                            </div>

                        </div>

                    </div>


                    <p
                        class="wizard-notice"
                        id="analysis-notice"
                        role="status"
                        aria-live="polite">

                        Analysis has not started.

                    </p>

                </div>


                <!-- =========================================================
     STEP 5 — RESULTS
========================================================= -->

                <div
                    class="wizard-page"
                    id="wizard-step-5"
                    hidden>

                    <div class="step-heading">

                        <span class="step-eyebrow">
                            STEP 5 OF 6
                        </span>

                        <h3>
                            Quality assessment results
                        </h3>

                        <p>
                            Review the quality assessment of the complete
                            coffee bean sample and the suggested transactional
                            price based on the configured parameters.
                        </p>

                    </div>


                    <div class="results-layout">


                        <!-- GRADE -->

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

                                <span>
                                    Coffee type
                                </span>

                                <strong>
                                    Robusta
                                </strong>

                            </div>


                            <div class="result-data-row">

                                <span>
                                    Sample weight
                                </span>

                                <strong id="result-weight">
                                    —
                                </strong>

                            </div>


                            <div class="result-data-row">

                                <span>
                                    Detected beans
                                </span>

                                <strong id="result-bean-count">
                                    —
                                </strong>

                            </div>


                            <div class="result-data-row">

                                <span>
                                    Detected defects
                                </span>

                                <strong id="result-defect-count">
                                    —
                                </strong>

                            </div>

                        </div>


                        <!-- PRICE -->

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
                                Calculated from the identified grade,
                                recorded sample weight, and configured
                                reference price.
                            </p>


                            <div class="pricing-divider"></div>


                            <div class="result-data-row">

                                <span>
                                    Grade
                                </span>

                                <strong id="pricing-grade">
                                    —
                                </strong>

                            </div>


                            <div class="result-data-row">

                                <span>
                                    Reference price
                                </span>

                                <strong id="result-unit-price">
                                    —
                                </strong>

                            </div>


                            <div class="result-data-row">

                                <span>
                                    Recorded weight
                                </span>

                                <strong id="pricing-weight">
                                    —
                                </strong>

                            </div>


                            <p class="pricing-disclaimer">
                                This is a suggested price only.
                                The final transaction price remains
                                subject to agreement between the farmer
                                and buyer.
                            </p>

                        </div>

                    </div>


                    <div class="defect-panel">

                        <h4>
                            Defect analysis
                        </h4>


                        <div id="defect-list">
                            No analysis available.
                        </div>

                    </div>

                </div>


                <!-- =========================================================
     STEP 6 — RECEIPT
========================================================= -->

                <div
                    class="wizard-page"
                    id="wizard-step-6"
                    hidden>

                    <div class="step-heading">

                        <span class="step-eyebrow">
                            STEP 6 OF 6
                        </span>

                        <h3>
                            Transaction receipt
                        </h3>

                        <p>
                            Review the completed transaction information
                            before printing the receipt.
                        </p>

                    </div>


                    <div class="receipt-layout">


                        <!-- RECEIPT -->

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

                                <span>
                                    Transaction ID
                                </span>

                                <strong id="receipt-id">
                                    —
                                </strong>

                            </div>


                            <div class="receipt-row">

                                <span>
                                    Date
                                </span>

                                <strong id="receipt-date">
                                    —
                                </strong>

                            </div>


                            <div class="receipt-row">

                                <span>
                                    Time
                                </span>

                                <strong id="receipt-time">
                                    —
                                </strong>

                            </div>


                            <div class="receipt-rule"></div>


                            <div class="receipt-row">

                                <span>
                                    Coffee type
                                </span>

                                <strong>
                                    Robusta
                                </strong>

                            </div>


                            <div class="receipt-row">

                                <span>
                                    Quality grade
                                </span>

                                <strong id="receipt-grade">
                                    —
                                </strong>

                            </div>


                            <div class="receipt-row">

                                <span>
                                    Weight
                                </span>

                                <strong id="receipt-weight">
                                    —
                                </strong>

                            </div>


                            <div class="receipt-row">

                                <span>
                                    Reference price
                                </span>

                                <strong id="receipt-unit-price">
                                    —
                                </strong>

                            </div>


                            <div class="receipt-rule"></div>


                            <div class="receipt-total">

                                <span>
                                    SUGGESTED PRICE
                                </span>

                                <strong id="receipt-total">
                                    ₱0.00
                                </strong>

                            </div>


                            <div class="receipt-rule"></div>


                            <p class="receipt-note">
                                This receipt contains a suggested
                                transaction price and does not represent
                                a finalized sale.
                            </p>


                            <p class="receipt-thank-you">
                                Thank you for using CGI.
                            </p>

                        </div>


                        <!-- RECEIPT ACTIONS -->

                        <div class="receipt-actions">

                            <div class="receipt-status-card">

                                <span class="small-label">
                                    PRINTER STATUS
                                </span>

                                <strong id="receipt-printer-status">
                                    Ready
                                </strong>

                                <p>
                                    The receipt is ready to be printed.
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
                                role="status">
                            </p>

                        </div>

                    </div>

                </div>


            </div>


            <!-- =========================================================
     MODAL FOOTER
========================================================= -->

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

    <script src="script/dashboard-script.js?v=20261001-2"></script>
    <script src="script/transaction-modal.js?v=20261001-2"></script>

</body>

</html>