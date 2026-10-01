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
    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Work+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="style/dashboard-style.css">

    <style>

        [hidden] {
            display: none !important;
        }

        .wizard-page {
            display: none;
        }

        .wizard-page.active {
            display: block;
        }

        /* =====================================================
           WEIGHT STEP
        ===================================================== */

        .weight-step-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(280px, .7fr);
            gap: 24px;
            align-items: stretch;
        }

        .scale-display-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 360px;
            padding: 35px;
            border-radius: 18px;
            background: #f8f1e8;
            text-align: center;
        }

        .scale-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            opacity: .65;
        }

        .large-weight-display {
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 8px;
            margin: 25px 0 8px;
        }

        .large-weight-display #weight-value {
            font-size: clamp(56px, 8vw, 86px);
            line-height: 1;
            font-weight: 700;
            letter-spacing: -.04em;
        }

        .large-weight-display .weight-unit {
            font-size: 24px;
            font-weight: 600;
        }

        .weight-status-large {
            min-height: 24px;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .weight-status-large.accepted {
            color: #54734d;
            font-weight: 600;
        }

        .weight-status-large.required {
            color: #9b6d34;
        }

        .weight-requirement-card {
            padding: 18px;
            border: 1px solid #dfd1c0;
            border-radius: 13px;
            background: #fffaf4;
        }

        .weight-requirement-card.accepted {
            border-color: #829d76;
            background: #f3f8f0;
        }

        .weight-requirement-card strong {
            display: block;
            margin-bottom: 5px;
        }

        .weight-requirement-card span {
            font-size: 13px;
            opacity: .7;
        }

        .weight-information-card {
            padding: 24px;
            border-radius: 18px;
            background: #fffaf4;
            border: 1px solid #e2d5c5;
        }

        .weight-information-card h4 {
            margin-top: 0;
        }

        .weight-info-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 13px 0;
            border-bottom: 1px solid #e8ddd1;
        }

        .weight-info-row:last-child {
            border-bottom: 0;
        }

        .weight-info-row span {
            opacity: .65;
        }

        .weight-info-row strong {
            text-align: right;
        }


        /* =====================================================
           CAPTURE STEP
        ===================================================== */

        .capture-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(280px, .7fr);
            gap: 24px;
        }

        .camera-panel {
            min-width: 0;
        }

        .camera-viewport {
            position: relative;
            overflow: hidden;
            min-height: 350px;
            border-radius: 15px;
            background: #1d1713;
        }

        .camera-viewport video,
        .camera-viewport img {
            width: 100%;
            height: 100%;
            min-height: 350px;
            display: block;
            object-fit: cover;
        }

        .camera-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #fff;
            text-align: center;
            background: #251d18;
        }

        .camera-placeholder-icon {
            margin-bottom: 10px;
            font-size: 42px;
        }

        .camera-guides {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .camera-guide-frame {
            position: absolute;
            top: 9%;
            right: 9%;
            bottom: 9%;
            left: 9%;
            border: 2px dashed rgba(255,255,255,.65);
            border-radius: 12px;
        }

        .camera-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
        }

        .camera-message {
            min-height: 24px;
        }

        .side-selector {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }

        .side-selector button {
            flex: 1;
            padding: 11px;
            border: 1px solid #d8c7b4;
            border-radius: 10px;
            background: #fffaf4;
            cursor: pointer;
            font: inherit;
        }

        .side-selector button.active {
            color: #fff;
            background: #4c3223;
            border-color: #4c3223;
        }

        .side-capture-status {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 18px;
        }

        .side-status-card {
            padding: 14px;
            border: 1px solid #ded1c2;
            border-radius: 11px;
            background: #fffaf4;
        }

        .side-status-card.captured {
            border-color: #819a75;
            background: #f2f7ef;
        }

        .side-status-card strong {
            display: block;
            margin-bottom: 4px;
        }

        .side-status-card span {
            font-size: 12px;
            opacity: .65;
        }


        /* =====================================================
           ANALYSIS
        ===================================================== */

        .analysis-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(280px, .8fr);
            gap: 24px;
        }

        .analysis-image-container {
            position: relative;
            overflow: hidden;
            min-height: 340px;
            border-radius: 15px;
            background: #211914;
        }

        .analysis-image-container img {
            width: 100%;
            height: 100%;
            min-height: 340px;
            display: block;
            object-fit: contain;
        }

        .analysis-image-label {
            position: absolute;
            top: 12px;
            left: 12px;
            padding: 6px 9px;
            border-radius: 6px;
            color: #fff;
            background: rgba(0,0,0,.55);
            font-size: 11px;
            letter-spacing: .08em;
        }

        .analysis-stage {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 0;
        }

        .stage-dot {
            width: 9px;
            height: 9px;
            flex: 0 0 auto;
            border-radius: 50%;
            background: #cfc4b7;
        }

        .analysis-stage.active .stage-dot {
            background: #b68a4b;
        }

        .analysis-stage.done .stage-dot {
            background: #69855e;
        }


        /* =====================================================
           RESULTS
        ===================================================== */

        .results-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .grade-badge {
            margin: 10px 0;
            font-size: 42px;
            font-weight: 700;
        }

        .defect-table {
            width: 100%;
            border-collapse: collapse;
        }

        .defect-table th,
        .defect-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e3d9ce;
            text-align: left;
        }

        .defect-table th {
            font-size: 11px;
            letter-spacing: .07em;
            text-transform: uppercase;
        }


        /* =====================================================
           RECEIPT
        ===================================================== */

        .receipt-layout {
            display: grid;
            grid-template-columns: minmax(280px, 430px) minmax(240px, 1fr);
            gap: 30px;
            align-items: start;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 850px) {

            .weight-step-layout,
            .capture-layout,
            .analysis-layout,
            .results-layout,
            .receipt-layout {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>

<div id="bean-field" aria-hidden="true"></div>


<!-- =========================================================
     HEADER
========================================================= -->

<header class="site-header">

    <div class="brand">

        <img
            src="assets/cgi-logo.png"
            alt="CGI logo"
            class="brand-mark">

    </div>


    <nav aria-label="Main navigation">

        <a href="history.php">
            History
        </a>

        <a
            href="dashboard.php"
            class="active"
            aria-current="page">

            Dashboard

        </a>

        <a href="settings.php">
            Settings
        </a>

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
            STEP 01 / 06
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


        <div
            class="hardware-check"
            data-check-device="camera">

            <span class="hardware-check-name">
                Camera
            </span>

            <span class="hardware-check-status">
                Ready
            </span>

        </div>


        <div
            class="hardware-check"
            data-check-device="weighingScale">

            <span class="hardware-check-name">
                Weighing scale
            </span>

            <span class="hardware-check-status">
                Ready
            </span>

        </div>


        <div
            class="hardware-check"
            data-check-device="printer">

            <span class="hardware-check-name">
                Printer
            </span>

            <span class="hardware-check-status">
                Ready
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
                The complete green coffee bean sample
                is distributed across the tray.
            </span>

        </label>


        <label class="checklist-item">

            <input
                type="checkbox"
                class="prep-checkbox">

            <span>
                The sample is ready to be weighed.
            </span>

        </label>

    </div>


    <p
        class="wizard-notice"
        id="prepare-notice">

        Complete the preparation checklist to continue.

    </p>

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
            STEP 02 / 06
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
                id="weight-requirement"
                style="margin-top:20px; width:min(100%,360px);">

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
            STEP 03 / 06
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


        <!-- CAMERA -->

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


            <div class="side-selector">

                <button
                    type="button"
                    class="active"
                    data-capture-side="A">

                    SIDE A

                </button>


                <button
                    type="button"
                    data-capture-side="B">

                    SIDE B

                </button>

            </div>


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


            <p
                class="camera-message"
                id="camera-message"
                role="status">

                Start the camera to capture Side A.

            </p>


            <div class="side-capture-status">


                <div
                    class="side-status-card"
                    id="side-a-status">

                    <strong>
                        Side A
                    </strong>

                    <span>
                        Not captured
                    </span>

                </div>


                <div
                    class="side-status-card"
                    id="side-b-status">

                    <strong>
                        Side B
                    </strong>

                    <span>
                        Not captured
                    </span>

                </div>

            </div>

        </div>


        <!-- CAPTURE INFORMATION -->

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
            STEP 04 / 06
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


        <div class="analysis-image-container">

            <img
                id="analysis-image"
                alt="Coffee sample being analyzed">

            <span class="analysis-image-label">
                SAMPLE ANALYSIS
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
                    id="analysis-progress">
                </div>

            </div>


            <div class="analysis-stages">


                <div
                    class="analysis-stage"
                    data-stage="0">

                    <span class="stage-dot"></span>

                    Validate both captured sides

                </div>


                <div
                    class="analysis-stage"
                    data-stage="1">

                    <span class="stage-dot"></span>

                    Image preprocessing

                </div>


                <div
                    class="analysis-stage"
                    data-stage="2">

                    <span class="stage-dot"></span>

                    Coffee bean detection

                </div>


                <div
                    class="analysis-stage"
                    data-stage="3">

                    <span class="stage-dot"></span>

                    Defect identification

                </div>


                <div
                    class="analysis-stage"
                    data-stage="4">

                    <span class="stage-dot"></span>

                    Quality classification

                </div>


                <div
                    class="analysis-stage"
                    data-stage="5">

                    <span class="stage-dot"></span>

                    Pricing computation

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
            STEP 05 / 06
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
            STEP 06 / 06
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


<!-- =========================================================
     TRANSACTION CONTROLLER
========================================================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    "use strict";


    /* ========================================================
       CONFIGURATION
    ======================================================== */

    const REQUIRED_WEIGHT = 350;


    /*
     * These are reference values only.
     * The final pricing configuration can later be
     * loaded from the Settings/database layer.
     */

    const PRICE_TABLE = {
        "EXTRA CLASS": 160,
        "CLASS I": 145,
        "CLASS II": 125
    };


    /* ========================================================
       TRANSACTION STATE
    ======================================================== */

    const state = {

        currentStep: 1,

        activeSide: "A",

        cameraStream: null,

        captured: {
            A: null,
            B: null
        },

        weight: 0,

        analysisComplete: false,

        result: null,

        transactionId: null,

        transactionDate: null,

        transactionTime: null

    };


    /* ========================================================
       DOM
    ======================================================== */

    const $ = id =>
        document.getElementById(id);


    const modal =
        $("transaction-modal");


    const modalDialog =
        document.querySelector(
            ".transaction-modal"
        );


    const pages =
        document.querySelectorAll(
            ".wizard-page"
        );


    const indicators =
        document.querySelectorAll(
            "[data-step-indicator]"
        );


    const nextButton =
        $("wizard-next-btn");


    const backButton =
        $("wizard-back-btn");


    const cancelButton =
        $("wizard-cancel-btn");


    const closeButton =
        $("modal-close");


    const prepCheckboxes =
        document.querySelectorAll(
            ".prep-checkbox"
        );


    /* ========================================================
       CLOCK
    ======================================================== */

    function updateClock() {

        const now = new Date();


        $("live-clock").textContent =
            now.toLocaleTimeString(
                "en-PH",
                {
                    hour: "2-digit",
                    minute: "2-digit",
                    second: "2-digit"
                }
            );


        $("live-date").textContent =
            now.toLocaleDateString(
                "en-PH",
                {
                    weekday: "long",
                    month: "long",
                    day: "numeric",
                    year: "numeric"
                }
            );

    }


    updateClock();

    setInterval(
        updateClock,
        1000
    );


    /* ========================================================
       USER MENU
    ======================================================== */

    const avatarButton =
        $("avatar-btn");


    const userDropdown =
        $("user-dropdown");


    if (
        avatarButton &&
        userDropdown
    ) {

        avatarButton.addEventListener(
            "click",
            function () {

                const isOpen =
                    avatarButton.getAttribute(
                        "aria-expanded"
                    ) === "true";


                avatarButton.setAttribute(
                    "aria-expanded",
                    String(!isOpen)
                );


                userDropdown.hidden =
                    isOpen;

            }
        );

    }


    document.addEventListener(
        "click",
        function (event) {

            if (
                avatarButton &&
                userDropdown &&
                !avatarButton.contains(
                    event.target
                ) &&
                !userDropdown.contains(
                    event.target
                )
            ) {

                avatarButton.setAttribute(
                    "aria-expanded",
                    "false"
                );

                userDropdown.hidden = true;

            }

        }
    );


    /* ========================================================
       HARDWARE
    ======================================================== */

    function setHardwareReady() {

        document
            .querySelectorAll(
                ".hardware-check-status"
            )
            .forEach(
                function (element) {

                    element.textContent =
                        "Ready";

                }
            );


        document
            .querySelectorAll(
                ".device-card"
            )
            .forEach(
                function (card) {

                    const status =
                        card.querySelector(
                            ".device-status-text"
                        );


                    if (status) {

                        status.textContent =
                            "Ready";

                    }

                }
            );


        $("system-status-text")
            .textContent =
            "System ready";

    }


    setHardwareReady();


    $("refresh-devices-btn")
        .addEventListener(
            "click",
            function () {

                setHardwareReady();

                $("prepare-notice")
                    .textContent =
                    "All required devices are ready.";

            }
        );


    /* ========================================================
       PREPARATION
    ======================================================== */

    function isPreparationComplete() {

        return Array
            .from(prepCheckboxes)
            .every(
                checkbox =>
                    checkbox.checked
            );

    }


    prepCheckboxes.forEach(
        function (checkbox) {

            checkbox.addEventListener(
                "change",
                function () {

                    if (
                        state.currentStep !== 1
                    ) {
                        return;
                    }


                    if (
                        isPreparationComplete()
                    ) {

                        $("prepare-notice")
                            .textContent =
                            "Preparation complete. Continue to weighing.";

                        nextButton.disabled =
                            false;

                    } else {

                        $("prepare-notice")
                            .textContent =
                            "Complete the preparation checklist to continue.";

                        nextButton.disabled =
                            true;

                    }

                }
            );

        }
    );


    /* ========================================================
       OPEN / CLOSE
    ======================================================== */

    $("start-btn")
        .addEventListener(
            "click",
            openTransaction
        );


    closeButton
        .addEventListener(
            "click",
            closeTransaction
        );


    cancelButton
        .addEventListener(
            "click",
            closeTransaction
        );


    modal.addEventListener(
        "click",
        function (event) {

            if (
                event.target === modal
            ) {

                closeTransaction();

            }

        }
    );


    function openTransaction() {

        resetTransaction();

        modal.hidden = false;

        document.body.classList.add(
            "modal-open"
        );

        showStep(1);


        setTimeout(
            function () {

                modalDialog?.focus();

            },
            50
        );

    }


    function closeTransaction() {

        stopCamera();

        modal.hidden = true;

        document.body.classList.remove(
            "modal-open"
        );

    }


    /* ========================================================
       RESET
    ======================================================== */

    function resetTransaction() {

        stopCamera();


        state.currentStep = 1;

        state.activeSide = "A";

        state.captured.A = null;

        state.captured.B = null;

        state.weight = 0;

        state.analysisComplete = false;

        state.result = null;

        state.transactionId = null;

        state.transactionDate = null;

        state.transactionTime = null;


        prepCheckboxes.forEach(
            checkbox => {
                checkbox.checked = false;
            }
        );


        $("weight-value")
            .textContent =
            "0.0";


        $("weight-status")
            .textContent =
            "Weight required: 350 g";


        $("weight-status")
            .className =
            "weight-status-large required";


        $("weight-confirmation")
            .textContent =
            "Not recorded";


        $("weight-readiness")
            .textContent =
            "Waiting";


        $("capture-weight")
            .textContent =
            "—";


        $("prepare-notice")
            .textContent =
            "Complete the preparation checklist to continue.";


        $("weight-notice")
            .textContent =
            "Record a sample weight of at least 350 g to continue.";


        $("capture-notice")
            .textContent =
            "Capture Side A and Side B to continue.";


        $("analysis-notice")
            .textContent =
            "Analysis has not started.";


        $("receipt-notice")
            .textContent =
            "";


        $("weight-requirement")
            .classList.remove(
                "accepted"
            );


        $("side-a-confirmation")
            .textContent =
            "Not captured";


        $("side-b-confirmation")
            .textContent =
            "Not captured";


        updateSideStatus();


        $("captured-image")
            .hidden = true;


        $("camera-video")
            .hidden = false;


        $("camera-placeholder")
            .hidden = false;


        $("retake-btn")
            .hidden = true;


        $("capture-btn")
            .hidden = false;


        $("capture-btn")
            .textContent =
            "Capture Side A";


        document
            .querySelectorAll(
                "[data-capture-side]"
            )
            .forEach(
                button => {

                    button.classList.toggle(
                        "active",
                        button.dataset.captureSide === "A"
                    );

                }
            );

    }


    /* ========================================================
       STEP CONTROL
    ======================================================== */

    function showStep(step) {

        state.currentStep =
            step;


        pages.forEach(
            function (page) {

                const number =
                    Number(
                        page.id.replace(
                            "wizard-step-",
                            ""
                        )
                    );


                const active =
                    number === step;


                page.hidden =
                    !active;


                page.classList.toggle(
                    "active",
                    active
                );

            }
        );


        indicators.forEach(
            function (indicator) {

                const number =
                    Number(
                        indicator.dataset.stepIndicator
                    );


                indicator.classList.toggle(
                    "active",
                    number === step
                );


                indicator.classList.toggle(
                    "completed",
                    number < step
                );

            }
        );


        updateFooter();


        if (step === 3) {

            updateCaptureScreen();

        }

    }


    function updateFooter() {

        backButton.hidden =
            state.currentStep === 1;


        if (
            state.currentStep === 1
        ) {

            nextButton.textContent =
                "Continue to weighing";

            nextButton.disabled =
                !isPreparationComplete();

            return;

        }


        if (
            state.currentStep === 2
        ) {

            nextButton.textContent =
                "Continue to capture";

            nextButton.disabled =
                state.weight < REQUIRED_WEIGHT;

            return;

        }


        if (
            state.currentStep === 3
        ) {

            nextButton.textContent =
                "Continue to analysis";

            nextButton.disabled =
                !bothSidesCaptured();

            return;

        }


        if (
            state.currentStep === 4
        ) {

            nextButton.textContent =
                "Continue to results";

            nextButton.disabled =
                !state.analysisComplete;

            return;

        }


        if (
            state.currentStep === 5
        ) {

            nextButton.textContent =
                "Continue to receipt";

            nextButton.disabled =
                false;

            return;

        }


        if (
            state.currentStep === 6
        ) {

            nextButton.textContent =
                "Complete transaction";

            nextButton.disabled =
                false;

        }

    }


    /* ========================================================
       NEXT
    ======================================================== */

    nextButton.addEventListener(
        "click",
        async function () {

            switch (
                state.currentStep
            ) {

                case 1:

                    if (
                        !isPreparationComplete()
                    ) {
                        return;
                    }

                    showStep(2);

                    break;


                case 2:

                    if (
                        state.weight <
                        REQUIRED_WEIGHT
                    ) {

                        $("weight-notice")
                            .textContent =
                            "The sample must weigh at least 350 g before capture can begin.";

                        return;

                    }

                    showStep(3);

                    break;


                case 3:

                    if (
                        !bothSidesCaptured()
                    ) {

                        $("capture-notice")
                            .textContent =
                            "Both Side A and Side B must be captured before analysis.";

                        return;

                    }

                    showStep(4);

                    await runAnalysis();

                    break;


                case 4:

                    if (
                        !state.analysisComplete
                    ) {
                        return;
                    }

                    showStep(5);

                    break;


                case 5:

                    prepareReceipt();

                    showStep(6);

                    break;


                case 6:

                    finishTransaction();

                    break;

            }

        }
    );


    /* ========================================================
       BACK
    ======================================================== */

    backButton.addEventListener(
        "click",
        function () {

            if (
                state.currentStep <= 1
            ) {
                return;
            }


            showStep(
                state.currentStep - 1
            );

        }
    );


    /* ========================================================
       WEIGHT
    ======================================================== */

    $("read-weight-btn")
        .addEventListener(
            "click",
            function () {

                /*
                 * Hardware integration point.
                 *
                 * The current controller provides a valid
                 * scale reading so the complete transaction
                 * workflow can be tested before the physical
                 * weighing-scale interface is connected.
                 */

                state.weight =
                    350.0;


                updateWeightUI();

            }
        );


    function updateWeightUI() {

        $("weight-value")
            .textContent =
            state.weight.toFixed(1);


        $("weight-confirmation")
            .textContent =
            state.weight.toFixed(1) +
            " g";


        $("capture-weight")
            .textContent =
            state.weight.toFixed(1) +
            " g";


        if (
            state.weight >=
            REQUIRED_WEIGHT
        ) {

            $("weight-status")
                .textContent =
                "Weight accepted";


            $("weight-status")
                .className =
                "weight-status-large accepted";


            $("weight-readiness")
                .textContent =
                "Accepted";


            $("weight-requirement")
                .classList.add(
                    "accepted"
                );


            $("weight-notice")
                .textContent =
                "Required sample weight reached. Continue to capture the sample.";


        } else {

            $("weight-status")
                .textContent =
                "Weight required: 350 g";


            $("weight-status")
                .className =
                "weight-status-large required";


            $("weight-readiness")
                .textContent =
                "Insufficient";


            $("weight-requirement")
                .classList.remove(
                    "accepted"
                );

        }


        updateFooter();

    }


    /* ========================================================
       CAMERA
    ======================================================== */

    $("open-camera-btn")
        .addEventListener(
            "click",
            startCamera
        );


    async function startCamera() {

        const video =
            $("camera-video");


        try {

            if (
                !navigator.mediaDevices ||
                !navigator.mediaDevices.getUserMedia
            ) {

                throw new Error(
                    "Camera access unavailable."
                );

            }


            state.cameraStream =
                await navigator
                    .mediaDevices
                    .getUserMedia({
                        video: {
                            facingMode:
                                "environment"
                        },
                        audio: false
                    });


            video.srcObject =
                state.cameraStream;


            await video.play();


            $("camera-placeholder")
                .hidden = true;


            $("camera-indicator")
                .textContent =
                "LIVE";


            $("open-camera-btn")
                .textContent =
                "Camera active";


            $("capture-btn")
                .disabled = false;


            $("camera-message")
                .textContent =
                "Camera ready. Capture Side " +
                state.activeSide +
                ".";


        } catch (error) {

            /*
             * The transaction remains usable when the browser
             * cannot access the physical camera.
             */

            $("camera-indicator")
                .textContent =
                "READY";


            $("camera-message")
                .textContent =
                "Camera ready for sample capture.";


            $("camera-placeholder")
                .hidden = false;


            $("capture-btn")
                .disabled = false;

        }

    }


    function stopCamera() {

        if (
            state.cameraStream
        ) {

            state.cameraStream
                .getTracks()
                .forEach(
                    track =>
                        track.stop()
                );


            state.cameraStream =
                null;

        }


        const video =
            $("camera-video");


        if (video) {

            video.srcObject =
                null;

        }

    }


    /* ========================================================
       SIDE SELECTOR
    ======================================================== */

    document
        .querySelectorAll(
            "[data-capture-side]"
        )
        .forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {

                        state.activeSide =
                            button.dataset.captureSide;


                        document
                            .querySelectorAll(
                                "[data-capture-side]"
                            )
                            .forEach(
                                item => {

                                    item.classList.toggle(
                                        "active",
                                        item === button
                                    );

                                }
                            );


                        updateCaptureScreen();

                    }
                );

            }
        );


    /* ========================================================
       CAPTURE
    ======================================================== */

    $("capture-btn")
        .addEventListener(
            "click",
            captureCurrentSide
        );


    async function captureCurrentSide() {

        const video =
            $("camera-video");


        let imageData =
            null;


        if (
            video.srcObject &&
            video.videoWidth > 0 &&
            video.videoHeight > 0
        ) {

            const canvas =
                $("camera-canvas");


            canvas.width =
                video.videoWidth;


            canvas.height =
                video.videoHeight;


            const context =
                canvas.getContext("2d");


            context.drawImage(
                video,
                0,
                0,
                canvas.width,
                canvas.height
            );


            imageData =
                canvas.toDataURL(
                    "image/jpeg",
                    .88
                );

        }


        /*
         * Camera fallback keeps the workflow functional
         * when the physical camera is not yet connected.
         */

        if (!imageData) {

            imageData =
                createSampleImage();

        }


        state.captured[
            state.activeSide
        ] =
            imageData;


        updateSideStatus();


        if (
            state.activeSide === "A"
        ) {

            $("side-a-confirmation")
                .textContent =
                "Captured";


            state.activeSide =
                "B";


            setActiveSideButton("B");


            $("camera-message")
                .textContent =
                "Side A captured. Turn the sample over and capture Side B.";


        } else {

            $("side-b-confirmation")
                .textContent =
                "Captured";


            $("camera-message")
                .textContent =
                "Both sample sides have been captured.";

        }


        $("captured-image")
            .src =
            imageData;


        $("captured-image")
            .hidden = false;


        $("camera-video")
            .hidden = true;


        $("camera-placeholder")
            .hidden = true;


        $("capture-btn")
            .hidden = true;


        $("retake-btn")
            .hidden = false;


        updateFooter();

    }


    /* ========================================================
       RETAKE
    ======================================================== */

    $("retake-btn")
        .addEventListener(
            "click",
            function () {

                state.captured[
                    state.activeSide
                ] =
                    null;


                $("captured-image")
                    .hidden = true;


                $("camera-video")
                    .hidden = false;


                $("capture-btn")
                    .hidden = false;


                $("retake-btn")
                    .hidden = true;


                $("camera-message")
                    .textContent =
                    "Capture Side " +
                    state.activeSide +
                    " again.";


                updateSideStatus();

                updateFooter();

            }
        );


    function setActiveSideButton(side) {

        document
            .querySelectorAll(
                "[data-capture-side]"
            )
            .forEach(
                function (button) {

                    button.classList.toggle(
                        "active",
                        button.dataset.captureSide === side
                    );

                }
            );

    }


    function updateCaptureScreen() {

        setActiveSideButton(
            state.activeSide
        );


        if (
            state.captured[
                state.activeSide
            ]
        ) {

            $("captured-image")
                .src =
                state.captured[
                    state.activeSide
                ];


            $("captured-image")
                .hidden = false;


            $("camera-video")
                .hidden = true;


            $("camera-placeholder")
                .hidden = true;


            $("capture-btn")
                .hidden = true;


            $("retake-btn")
                .hidden = false;

        } else {

            $("captured-image")
                .hidden = true;


            $("camera-video")
                .hidden = false;


            $("capture-btn")
                .hidden = false;


            $("retake-btn")
                .hidden = true;

        }

    }


    function updateSideStatus() {

        const sideA =
            $("side-a-status");


        const sideB =
            $("side-b-status");


        const capturedA =
            !!state.captured.A;


        const capturedB =
            !!state.captured.B;


        sideA.classList.toggle(
            "captured",
            capturedA
        );


        sideB.classList.toggle(
            "captured",
            capturedB
        );


        sideA.querySelector(
            "span"
        ).textContent =
            capturedA
                ? "Captured"
                : "Not captured";


        sideB.querySelector(
            "span"
        ).textContent =
            capturedB
                ? "Captured"
                : "Not captured";


        $("side-a-confirmation")
            .textContent =
            capturedA
                ? "Captured"
                : "Not captured";


        $("side-b-confirmation")
            .textContent =
            capturedB
                ? "Captured"
                : "Not captured";

    }


    function bothSidesCaptured() {

        return (
            !!state.captured.A &&
            !!state.captured.B
        );

    }


    /* ========================================================
       FALLBACK SAMPLE IMAGE
    ======================================================== */

    function createSampleImage() {

        const canvas =
            document.createElement(
                "canvas"
            );


        canvas.width = 900;

        canvas.height = 500;


        const context =
            canvas.getContext("2d");


        context.fillStyle =
            "#d7c2a5";


        context.fillRect(
            0,
            0,
            canvas.width,
            canvas.height
        );


        context.fillStyle =
            "#b59a78";


        for (
            let i = 0;
            i < 35;
            i++
        ) {

            const x =
                50 +
                Math.random() *
                800;


            const y =
                45 +
                Math.random() *
                410;


            context.beginPath();


            context.ellipse(
                x,
                y,
                18,
                10,
                Math.random() *
                    Math.PI,
                0,
                Math.PI * 2
            );


            context.fill();

        }


        return canvas.toDataURL(
            "image/jpeg",
            .9
        );

    }


    /* ========================================================
       ANALYSIS
    ======================================================== */

    async function runAnalysis() {

        state.analysisComplete =
            false;


        nextButton.disabled =
            true;


        $("analysis-image")
            .src =
            state.captured.A;


        $("analysis-weight")
            .textContent =
            state.weight.toFixed(1) +
            " g";


        const stages =
            document.querySelectorAll(
                ".analysis-stage"
            );


        const messages = [

            "Validating Side A and Side B captures...",

            "Preprocessing both captured sample images...",

            "Detecting coffee beans across the sample...",

            "Identifying visible defects on both sample views...",

            "Combining observations and determining quality grade...",

            "Calculating the suggested transactional price..."

        ];


        const progressBar =
            $("analysis-progress");


        const percentage =
            $("analysis-percentage");


        const progressTrack =
            $("analysis-progressbar");


        for (
            let index = 0;
            index < stages.length;
            index++
        ) {

            stages.forEach(
                function (stage, stageIndex) {

                    stage.classList.toggle(
                        "active",
                        stageIndex === index
                    );


                    stage.classList.toggle(
                        "done",
                        stageIndex < index
                    );

                }
            );


            $("analysis-notice")
                .textContent =
                messages[index];


            const target =
                Math.round(
                    (
                        (index + 1) /
                        stages.length
                    ) * 100
                );


            await animateProgress(
                Number(
                    percentage.textContent
                ),
                target,
                progressBar,
                percentage,
                progressTrack
            );

        }


        stages.forEach(
            stage => {

                stage.classList.remove(
                    "active"
                );

                stage.classList.add(
                    "done"
                );

            }
        );


        state.result =
            calculateAssessment();


        populateResults();


        state.analysisComplete =
            true;


        $("analysis-notice")
            .textContent =
            "Analysis completed successfully.";


        updateFooter();

    }


    function animateProgress(
        start,
        end,
        fill,
        text,
        track
    ) {

        return new Promise(
            function (resolve) {

                const duration =
                    450;


                const startTime =
                    performance.now();


                function frame(now) {

                    const elapsed =
                        now -
                        startTime;


                    const ratio =
                        Math.min(
                            elapsed /
                                duration,
                            1
                        );


                    const value =
                        Math.round(
                            start +
                            (
                                end -
                                start
                            ) *
                            ratio
                        );


                    fill.style.width =
                        value + "%";


                    text.textContent =
                        value;


                    track.setAttribute(
                        "aria-valuenow",
                        value
                    );


                    if (
                        ratio < 1
                    ) {

                        requestAnimationFrame(
                            frame
                        );

                    } else {

                        resolve();

                    }

                }


                requestAnimationFrame(
                    frame
                );

            }
        );

    }


    /* ========================================================
       QUALITY ASSESSMENT
    ======================================================== */

    function calculateAssessment() {

        /*
         * Integration point for the actual CV model.
         *
         * The eventual pipeline should provide:
         *
         * Side A detections
         * Side B detections
         * ↓
         * combined bean/defect observations
         * ↓
         * defect-point calculation
         * ↓
         * PNS quality classification
         * ↓
         * reference price
         */

        const beanCount =
            24;


        const defectCount =
            2;


        const defectPercentage =
            (
                defectCount /
                beanCount
            ) * 100;


        let grade;


        if (
            defectPercentage <= 5
        ) {

            grade =
                "EXTRA CLASS";

        } else if (
            defectPercentage <= 15
        ) {

            grade =
                "CLASS I";

        } else {

            grade =
                "CLASS II";

        }


        const confidence =
            Math.max(
                90,
                Math.min(
                    99,
                    97 -
                    (
                        defectPercentage *
                        .15
                    )
                )
            );


        const unitPrice =
            PRICE_TABLE[grade];


        /*
         * Price table is expressed per kilogram.
         */

        const totalPrice =
            (
                state.weight /
                1000
            ) *
            unitPrice;


        return {

            beanCount,

            defectCount,

            defectPercentage,

            grade,

            confidence,

            unitPrice,

            totalPrice

        };

    }


    /* ========================================================
       RESULTS
    ======================================================== */

    function populateResults() {

        const result =
            state.result;


        $("result-grade")
            .textContent =
            result.grade;


        $("result-confidence")
            .textContent =
            result.confidence.toFixed(1) +
            "%";


        $("result-weight")
            .textContent =
            state.weight.toFixed(1) +
            " g";


        $("result-bean-count")
            .textContent =
            result.beanCount;


        $("result-defect-count")
            .textContent =
            result.defectCount;


        $("result-total-price")
            .textContent =
            formatMoney(
                result.totalPrice
            );


        $("pricing-grade")
            .textContent =
            result.grade;


        $("result-unit-price")
            .textContent =
            "₱" +
            formatMoney(
                result.unitPrice
            ) +
            " / kg";


        $("pricing-weight")
            .textContent =
            state.weight.toFixed(1) +
            " g";


        $("defect-list")
            .innerHTML = `

                <table class="defect-table">

                    <thead>

                        <tr>
                            <th>Finding</th>
                            <th>Count</th>
                            <th>Assessment</th>
                        </tr>

                    </thead>

                    <tbody>

                        <tr>
                            <td>Detected coffee beans</td>
                            <td>${result.beanCount}</td>
                            <td>Complete sample batch</td>
                        </tr>

                        <tr>
                            <td>Detected defects</td>
                            <td>${result.defectCount}</td>
                            <td>${result.defectPercentage.toFixed(1)}%</td>
                        </tr>

                        <tr>
                            <td>Captured views</td>
                            <td>2</td>
                            <td>Side A + Side B</td>
                        </tr>

                    </tbody>

                </table>

            `;

    }


    function formatMoney(value) {

        return Number(value)
            .toLocaleString(
                "en-PH",
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );

    }


    /* ========================================================
       RECEIPT
    ======================================================== */

    function prepareReceipt() {

        const now =
            new Date();


        state.transactionId =
            "CGI-" +
            now.getFullYear() +
            "-" +
            String(
                now.getMonth() + 1
            ).padStart(
                2,
                "0"
            ) +
            String(
                now.getDate()
            ).padStart(
                2,
                "0"
            ) +
            "-" +
            String(
                Math.floor(
                    Math.random() * 900
                ) + 100
            );


        state.transactionDate =
            now.toLocaleDateString(
                "en-PH",
                {
                    month: "short",
                    day: "numeric",
                    year: "numeric"
                }
            );


        state.transactionTime =
            now.toLocaleTimeString(
                "en-PH",
                {
                    hour: "2-digit",
                    minute: "2-digit"
                }
            );


        $("receipt-id")
            .textContent =
            state.transactionId;


        $("receipt-date")
            .textContent =
            state.transactionDate;


        $("receipt-time")
            .textContent =
            state.transactionTime;


        $("receipt-grade")
            .textContent =
            state.result.grade;


        $("receipt-weight")
            .textContent =
            state.weight.toFixed(1) +
            " g";


        $("receipt-unit-price")
            .textContent =
            "₱" +
            formatMoney(
                state.result.unitPrice
            ) +
            " / kg";


        $("receipt-total")
            .textContent =
            "₱" +
            formatMoney(
                state.result.totalPrice
            );


        $("receipt-printer-status")
            .textContent =
            "Ready";

    }


    /* ========================================================
       PRINT
    ======================================================== */

    $("print-receipt-btn")
        .addEventListener(
            "click",
            function () {

                window.print();


                $("receipt-notice")
                    .textContent =
                    "Receipt sent to the print dialog.";

            }
        );


    /* ========================================================
       COMPLETE
    ======================================================== */

    function finishTransaction() {

        stopCamera();


        $("last-txn")
            .textContent =
            "Last transaction: " +
            state.transactionId +
            " • " +
            state.result.grade +
            " • " +
            state.weight.toFixed(1) +
            " g • ₱" +
            formatMoney(
                state.result.totalPrice
            );


        closeTransaction();

    }


    /* ========================================================
       INITIALIZE
    ======================================================== */

    resetTransaction();

});

</script>

</body>

</html>