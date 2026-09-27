(() => {
    'use strict';

    // ========================================
    // CGI TRANSACTION WIZARD
    // ========================================

    const $ = (id) => document.getElementById(id);

    const modal = $('transaction-modal');

    // Stop the script cleanly if the modal does not exist.
    // This prevents one missing HTML element from breaking
    // the entire dashboard JavaScript.
    if (!modal) {
        console.error(
            '[CGI] transaction-modal was not found in the page.'
        );
        return;
    }

    const modalDialog = modal.querySelector(
        '.transaction-modal'
    );

    const nextBtn = $('wizard-next-btn');
    const backBtn = $('wizard-back-btn');
    const cancelBtn = $('wizard-cancel-btn');
    const closeBtn = $('modal-close');

    const titles = [
        'Prepare Sample',
        'Capture & Weigh',
        'Analyze Quality',
        'Assessment Results',
        'Transaction Receipt'
    ];

    const subtitles = [
        'Prepare your coffee bean sample.',
        'Capture the sample and record its weight.',
        'Processing the captured coffee sample.',
        'Review the grading and pricing results.',
        'Print and complete the transaction.'
    ];

    // ========================================
    // DEMO PRICES
    // ========================================
    // These are ONLY for interface testing.
    // They are not verified market prices.

    const DEFAULT_PRICES = {
        'EXTRA CLASS': 220,
        'CLASS I': 190,
        'CLASS II': 160
    };

    // ========================================
    // STATE
    // ========================================

    let currentStep = 1;
    let previousFocus = null;

    let cameraStream = null;
    let cameraReady = false;

    let capturedImage = null;
    let sampleWeight = null;

    let analysisComplete = false;
    let analysisRunning = false;
    let analysisRunId = 0;

    let transaction = null;

    let demoMode = false;

    let hardware = {
        camera: 'WARNING',
        weighingScale: 'WARNING',
        printer: 'WARNING'
    };

    // ========================================
    // CAMERA ELEMENTS
    // ========================================

    const video = $('camera-video');
    const canvas = $('camera-canvas');
    const capturedPreview = $('captured-image');
    const placeholder = $('camera-placeholder');

    // ========================================
    // UTILITY FUNCTIONS
    // ========================================

    const money = (value) =>
        new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).format(Number(value) || 0);

    const delay = (ms) =>
        new Promise((resolve) => setTimeout(resolve, ms));

    function setText(id, value) {
        const element = $(id);

        if (!element) {
            console.warn(
                `[CGI] Element #${id} was not found.`
            );
            return;
        }

        element.textContent = value;
    }

    function showNotice(id, message, isError = false) {
        const element = $(id);

        if (!element) {
            return;
        }

        element.textContent = message;
        element.classList.toggle(
            'error',
            Boolean(isError)
        );
    }

    function isReady(device) {
        return hardware[device] === 'READY';
    }


    // ========================================
    // HARDWARE STATUS
    // ========================================

    function updateHardwareStatus(data = {}) {
        hardware = {
            camera: data.camera || 'ERROR',
            weighingScale:
                data.weighingScale || 'ERROR',
            printer: data.printer || 'ERROR'
        };

        demoMode = data.demo === true;

        document.querySelectorAll(
            '[data-check-device]'
        ).forEach((card) => {

            const key = card.dataset.checkDevice;
            const status = hardware[key] || 'ERROR';

            const label = card.querySelector(
                '.hardware-check-status'
            );

            card.classList.remove(
                'ready',
                'warning',
                'error'
            );

            card.classList.add(
                status.toLowerCase()
            );

            if (label) {
                if (demoMode) {
                    label.textContent =
                        status === 'READY'
                            ? 'Demo ready'
                            : `Demo ${status.toLowerCase()}`;
                } else {
                    label.textContent =
                        status === 'READY'
                            ? 'Connected'
                            : status === 'WARNING'
                                ? 'Needs attention'
                                : 'Disconnected';
                }
            }
        });

        updatePrinterStatus();

        if (!modal.hidden) {
            updateNavigation();
        }
    }

    document.addEventListener(
        'cgi:hardware-status',
        (event) => {
            updateHardwareStatus(
                event.detail || {}
            );
        }
    );

    async function refreshDevices() {
        if (!window.CGIHardware) {
            console.warn(
                '[CGI] CGIHardware is not available.'
            );

            return;
        }

        try {
            const status =
                await window.CGIHardware.refresh();

            updateHardwareStatus(status);

        } catch (error) {
            console.error(
                '[CGI] Hardware refresh failed:',
                error
            );
        }
    }

    const refreshDevicesBtn =
        $('refresh-devices-btn');

    if (refreshDevicesBtn) {
        refreshDevicesBtn.addEventListener(
            'click',
            refreshDevices
        );
    }


    // ========================================
    // RESET WIZARD
    // ========================================

    function resetWizard() {
        analysisRunId++;

        stopCamera();

        currentStep = 1;

        cameraReady = false;
        capturedImage = null;
        sampleWeight = null;

        analysisComplete = false;
        analysisRunning = false;

        transaction = null;

        // Reset preparation checklist
        document.querySelectorAll(
            '.prep-checkbox'
        ).forEach((checkbox) => {
            checkbox.checked = false;
        });

        // Reset camera
        if (video) {
            video.hidden = true;
            video.removeAttribute('src');
        }

        if (capturedPreview) {
            capturedPreview.hidden = true;
            capturedPreview.removeAttribute('src');
        }

        if (placeholder) {
            placeholder.hidden = false;
        }

        setText(
            'camera-indicator',
            'OFFLINE'
        );

        const cameraIndicator =
            $('camera-indicator');

        if (cameraIndicator) {
            cameraIndicator.classList.remove(
                'live'
            );
        }

        const captureBtn =
            $('capture-btn');

        if (captureBtn) {
            captureBtn.disabled = true;
            captureBtn.hidden = false;
        }

        const retakeBtn =
            $('retake-btn');

        if (retakeBtn) {
            retakeBtn.hidden = true;
        }

        const openCameraBtn =
            $('open-camera-btn');

        if (openCameraBtn) {
            openCameraBtn.hidden = false;
        }

        setText(
            'weight-value',
            '---.-'
        );

        setText(
            'weight-status',
            'Waiting for reading'
        );

        setText(
            'capture-status',
            'Not captured'
        );

        setText(
            'weight-confirmation',
            'Not recorded'
        );

        showNotice(
            'camera-message',
            'Waiting for camera.'
        );

        showNotice(
            'prepare-notice',
            'Complete the checklist to continue.'
        );

        showNotice(
            'capture-notice',
            'Capture an image and record the sample weight.'
        );

        showNotice(
            'analysis-notice',
            'Waiting to begin analysis.'
        );

        showNotice(
            'receipt-notice',
            ''
        );

        const analysisProgress =
            $('analysis-progress');

        if (analysisProgress) {
            analysisProgress.style.width = '0%';
        }

        const analysisProgressbar =
            $('analysis-progressbar');

        if (analysisProgressbar) {
            analysisProgressbar.setAttribute(
                'aria-valuenow',
                '0'
            );
        }

        setText(
            'analysis-percentage',
            '0'
        );

        document.querySelectorAll(
            '.analysis-stage'
        ).forEach((stage) => {
            stage.classList.remove(
                'active',
                'completed'
            );
        });

        const demoDisclaimer =
            $('results-demo-disclaimer');

        if (demoDisclaimer) {
            demoDisclaimer.hidden = true;
        }

        const receiptDemo =
            $('receipt-demo');

        if (receiptDemo) {
            receiptDemo.hidden = true;
        }

        updateNavigation();
    }


    // ========================================
    // OPEN / CLOSE
    // ========================================

    async function open() {
        previousFocus =
            document.activeElement;

        resetWizard();

        modal.hidden = false;

        document.body.classList.add(
            'modal-open'
        );

        showStep(1);

        if (closeBtn) {
            closeBtn.focus();
        }

        await refreshDevices();
    }

    function stopCamera() {
        if (cameraStream) {
            cameraStream
                .getTracks()
                .forEach((track) => {
                    track.stop();
                });

            cameraStream = null;
        }

        if (video) {
            video.srcObject = null;
        }

        cameraReady = false;
    }

    function close(force = false) {

        if (!force && currentStep > 1) {

            const confirmed =
                window.confirm(
                    'Cancel this transaction? ' +
                    'Any unsaved progress will be lost.'
                );

            if (!confirmed) {
                return;
            }
        }

        analysisRunId++;

        stopCamera();

        modal.hidden = true;

        document.body.classList.remove(
            'modal-open'
        );

        if (
            previousFocus &&
            typeof previousFocus.focus === 'function'
        ) {
            previousFocus.focus();
        }
    }

    if (closeBtn) {
        closeBtn.addEventListener(
            'click',
            () => close()
        );
    }

    if (cancelBtn) {
        cancelBtn.addEventListener(
            'click',
            () => close()
        );
    }

    modal.addEventListener(
        'click',
        (event) => {

            if (event.target === modal) {
                close();
            }
        }
    );

    document.addEventListener(
        'keydown',
        (event) => {

            if (modal.hidden) {
                return;
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                close();
            }

            if (event.key === 'Tab') {

                const focusable = [
                    ...modal.querySelectorAll(
                        'button:not([disabled]):not([hidden]), ' +
                        'input:not([disabled]), ' +
                        'select:not([disabled]), ' +
                        'textarea:not([disabled])'
                    )
                ].filter(
                    (element) =>
                        element.getClientRects().length > 0
                );

                if (!focusable.length) {
                    return;
                }

                const first =
                    focusable[0];

                const last =
                    focusable[
                    focusable.length - 1
                    ];

                if (
                    event.shiftKey &&
                    document.activeElement === first
                ) {
                    event.preventDefault();
                    last.focus();

                } else if (
                    !event.shiftKey &&
                    document.activeElement === last
                ) {
                    event.preventDefault();
                    first.focus();
                }
            }
        }
    );


    // ========================================
    // STEP NAVIGATION
    // ========================================

    function showStep(step) {

        currentStep = step;

        document.querySelectorAll(
            '.wizard-page'
        ).forEach((page) => {

            const active =
                page.id ===
                `wizard-step-${step}`;

            page.hidden = !active;

            page.classList.toggle(
                'active',
                active
            );
        });

        document.querySelectorAll(
            '[data-step-indicator]'
        ).forEach((indicator) => {

            const number =
                Number(
                    indicator.dataset.stepIndicator
                );

            indicator.classList.toggle(
                'active',
                number === step
            );

            indicator.classList.toggle(
                'completed',
                number < step
            );

            const numberElement =
                indicator.querySelector(
                    '.step-number'
                );

            if (numberElement) {
                numberElement.textContent =
                    number < step
                        ? '✓'
                        : number;
            }
        });

        setText(
            'modal-title',
            titles[step - 1]
        );

        setText(
            'modal-subtitle',
            subtitles[step - 1]
        );

        if (backBtn) {
            backBtn.hidden =
                step === 1 ||
                step === 3 ||
                step === 5;
        }

        if (cancelBtn) {
            cancelBtn.hidden =
                step === 5;
        }

        if (nextBtn) {
            nextBtn.textContent =
                step === 1
                    ? 'Continue'
                    : step === 2
                        ? 'Analyze sample'
                        : step === 3
                            ? 'View results'
                            : step === 4
                                ? 'Generate receipt'
                                : 'Finish transaction';
        }

        // STEP 3
        if (
            step === 3 &&
            !analysisComplete &&
            !analysisRunning
        ) {
            startAnalysis();
        }

        // STEP 4
        if (
            step === 4 &&
            transaction
        ) {
            renderResults();
        }

        // STEP 5
        if (
            step === 5 &&
            transaction
        ) {
            renderReceipt();
        }

        updateNavigation();

        const modalBody =
            modal.querySelector(
                '.modal-body'
            );

        if (modalBody) {
            modalBody.scrollTop = 0;
        }
    }


    // ========================================
    // STEP 1 VALIDATION
    // ========================================

    function preparationComplete() {

        const checkboxes = [
            ...document.querySelectorAll(
                '.prep-checkbox'
            )
        ];

        const allChecked =
            checkboxes.length > 0 &&
            checkboxes.every(
                (checkbox) =>
                    checkbox.checked
            );

        /*
         * IMPORTANT:
         *
         * Demo mode allows the wizard to continue
         * even though the hardware status is simulated.
         *
         * HOWEVER:
         * the laptop webcam is still REAL.
         */

        const essentialDevicesReady =
            demoMode ||
            (
                isReady('camera') &&
                isReady('weighingScale')
            );

        return (
            allChecked &&
            essentialDevicesReady
        );
    }


    // ========================================
    // STEP 2 VALIDATION
    // ========================================

    function captureComplete() {
        return (
            capturedImage !== null &&
            Number.isFinite(sampleWeight) &&
            sampleWeight > 0
        );
    }


    // ========================================
    // NAVIGATION STATE
    // ========================================

    function updateNavigation() {

        if (!nextBtn) {
            return;
        }

        // STEP 1
        if (currentStep === 1) {

            nextBtn.disabled =
                !preparationComplete();

            const checkboxes = [
                ...document.querySelectorAll(
                    '.prep-checkbox'
                )
            ];

            const allChecked =
                checkboxes.length > 0 &&
                checkboxes.every(
                    (checkbox) =>
                        checkbox.checked
                );

            if (!allChecked) {

                showNotice(
                    'prepare-notice',
                    'Complete the preparation checklist.'
                );

            } else if (
                !demoMode &&
                (
                    !isReady('camera') ||
                    !isReady('weighingScale')
                )
            ) {

                showNotice(
                    'prepare-notice',
                    'Camera and weighing scale must be ready.',
                    true
                );

            } else {

                showNotice(
                    'prepare-notice',
                    demoMode
                        ? 'Preparation complete. Demo mode is active.'
                        : 'Preparation complete. Ready to continue.'
                );
            }
        }

        // STEP 2
        if (currentStep === 2) {
            nextBtn.disabled =
                !captureComplete();
        }

        // STEP 3
        if (currentStep === 3) {
            nextBtn.disabled =
                !analysisComplete;
        }

        // STEP 4
        if (currentStep === 4) {
            nextBtn.disabled =
                !transaction;
        }

        // STEP 5
        if (currentStep === 5) {
            nextBtn.disabled =
                !transaction;
        }
    }


    // ========================================
    // PREPARATION CHECKBOXES
    // ========================================

    document.querySelectorAll(
        '.prep-checkbox'
    ).forEach((checkbox) => {

        checkbox.addEventListener(
            'change',
            updateNavigation
        );
    });


    // ========================================
    // BACK BUTTON
    // ========================================

    if (backBtn) {

        backBtn.addEventListener(
            'click',
            () => {

                if (currentStep === 2) {

                    stopCamera();

                    showStep(1);

                } else if (
                    currentStep === 4
                ) {

                    // Results become invalid
                    // if the user returns to capture.

                    analysisComplete = false;
                    transaction = null;

                    showStep(2);
                }
            }
        );
    }


    // ========================================
    // NEXT BUTTON
    // ========================================

    if (nextBtn) {

        nextBtn.addEventListener(
            'click',
            async () => {

                if (nextBtn.disabled) {
                    return;
                }

                if (currentStep === 1) {

                    showStep(2);

                } else if (currentStep === 2) {

                    stopCamera();

                    showStep(3);

                } else if (currentStep === 3) {

                    showStep(4);

                } else if (currentStep === 4) {

                    showStep(5);

                } else if (currentStep === 5) {

                    finishTransaction();
                }
            }
        );
    }


    // ========================================
    // STEP 2: CAMERA
    // ========================================

    /*
     * IMPORTANT:
     *
     * The camera is ALWAYS the REAL laptop/webcam.
     *
     * Demo mode does NOT create a fake coffee-bean image.
     *
     * Demo mode only affects:
     * - AI analysis
     * - grade result
     * - pricing
     * - weighing reading
     */

    async function startCamera() {

        if (!video) {
            showNotice(
                'camera-message',
                'Camera video element is missing.',
                true
            );

            return;
        }

        if (
            !navigator.mediaDevices ||
            !navigator.mediaDevices.getUserMedia
        ) {

            showNotice(
                'camera-message',
                'Camera access requires localhost or HTTPS.',
                true
            );

            return;
        }

        // Stop any previous camera first.
        stopCamera();

        try {

            showNotice(
                'camera-message',
                'Requesting camera access...'
            );

            cameraStream =
                await navigator.mediaDevices.getUserMedia({
                    audio: false,

                    video: {
                        facingMode: 'user',

                        width: {
                            ideal: 1280
                        },

                        height: {
                            ideal: 720
                        }
                    }
                });

            video.srcObject =
                cameraStream;

            video.hidden = false;

            if (placeholder) {
                placeholder.hidden = true;
            }

            if (capturedPreview) {
                capturedPreview.hidden = true;
            }

            /*
             * Some browsers require playsInline
             * before video playback works correctly.
             */
            video.setAttribute(
                'playsinline',
                ''
            );

            video.muted = true;

            await video.play();

            cameraReady = true;

            setText(
                'camera-indicator',
                'LIVE'
            );

            const indicator =
                $('camera-indicator');

            if (indicator) {
                indicator.classList.add(
                    'live'
                );
            }

            const captureBtn =
                $('capture-btn');

            if (captureBtn) {
                captureBtn.disabled = false;
                captureBtn.hidden = false;
            }

            const openCameraBtn =
                $('open-camera-btn');

            if (openCameraBtn) {
                openCameraBtn.hidden = true;
            }

            showNotice(
                'camera-message',
                'Camera is live. Position the coffee beans inside the guide.'
            );

        } catch (error) {

            console.error(
                '[CGI] Camera access error:',
                error
            );

            stopCamera();

            let message =
                'Unable to access the camera.';

            if (
                error &&
                error.name ===
                'NotAllowedError'
            ) {

                message =
                    'Camera permission was denied. Please allow camera access in your browser and try again.';

            } else if (
                error &&
                error.name ===
                'NotFoundError'
            ) {

                message =
                    'No camera was detected on this device.';

            } else if (
                error &&
                error.name ===
                'NotReadableError'
            ) {

                message =
                    'The camera is already being used by another application.';

            } else if (
                error &&
                error.name ===
                'SecurityError'
            ) {

                message =
                    'Camera access was blocked by the browser. Use localhost or HTTPS.';
            }

            showNotice(
                'camera-message',
                message,
                true
            );
        }
    }


    // ========================================
    // OPEN CAMERA BUTTON
    // ========================================

    const openCameraBtn =
        $('open-camera-btn');

    if (openCameraBtn) {

        openCameraBtn.addEventListener(
            'click',
            startCamera
        );
    }


    // ========================================
    // IMAGE CAPTURE
    // ========================================

    async function captureImage() {

        if (
            !cameraReady ||
            !video ||
            !video.videoWidth
        ) {

            showNotice(
                'camera-message',
                'Camera is not ready yet.',
                true
            );

            return;
        }

        const captureBtn =
            $('capture-btn');

        const countdown =
            $('capture-countdown');

        if (captureBtn) {
            captureBtn.disabled = true;
        }

        // ====================================
        // 3-2-1 COUNTDOWN
        // ====================================

        for (
            let number = 3;
            number >= 1;
            number--
        ) {

            if (
                modal.hidden ||
                currentStep !== 2 ||
                !cameraReady
            ) {

                if (countdown) {
                    countdown.hidden = true;
                }

                if (captureBtn) {
                    captureBtn.disabled = false;
                }

                return;
            }

            if (countdown) {
                countdown.hidden = false;
                countdown.textContent =
                    number;
            }

            await delay(700);
        }

        if (countdown) {
            countdown.hidden = true;
        }

        // ====================================
        // CAPTURE REAL WEBCAM FRAME
        // ====================================

        if (!canvas) {

            showNotice(
                'camera-message',
                'Camera canvas element is missing.',
                true
            );

            if (captureBtn) {
                captureBtn.disabled = false;
            }

            return;
        }

        canvas.width =
            video.videoWidth;

        canvas.height =
            video.videoHeight;

        const context =
            canvas.getContext('2d');

        if (!context) {

            showNotice(
                'camera-message',
                'Unable to prepare image capture.',
                true
            );

            if (captureBtn) {
                captureBtn.disabled = false;
            }

            return;
        }

        context.drawImage(
            video,
            0,
            0,
            canvas.width,
            canvas.height
        );

        capturedImage =
            canvas.toDataURL(
                'image/jpeg',
                0.90
            );

        // ====================================
        // DISPLAY CAPTURED IMAGE
        // ====================================

        if (capturedPreview) {

            capturedPreview.src =
                capturedImage;

            capturedPreview.hidden = false;
        }

        video.hidden = true;

        if (placeholder) {
            placeholder.hidden = true;
        }

        setText(
            'capture-status',
            'Captured'
        );

        if (captureBtn) {
            captureBtn.hidden = true;
        }

        const retakeBtn =
            $('retake-btn');

        if (retakeBtn) {
            retakeBtn.hidden = false;
        }

        showNotice(
            'camera-message',
            'Image captured successfully. You can retake the photo if needed.'
        );

        // Stop webcam after capture.
        stopCamera();

        setText(
            'camera-indicator',
            'CAPTURED'
        );

        const indicator =
            $('camera-indicator');

        if (indicator) {
            indicator.classList.remove(
                'live'
            );
        }

        updateNavigation();
    }


    // ========================================
    // CAPTURE BUTTON
    // ========================================

    const captureBtn =
        $('capture-btn');

    if (captureBtn) {

        captureBtn.addEventListener(
            'click',
            captureImage
        );
    }


    // ========================================
    // RETAKE BUTTON
    // ========================================

    const retakeBtn =
        $('retake-btn');

    if (retakeBtn) {

        retakeBtn.addEventListener(
            'click',
            async () => {

                capturedImage = null;

                setText(
                    'capture-status',
                    'Not captured'
                );

                retakeBtn.hidden = true;

                if (capturedPreview) {
                    capturedPreview.hidden = true;
                    capturedPreview.removeAttribute(
                        'src'
                    );
                }

                const captureButton =
                    $('capture-btn');

                if (captureButton) {
                    captureButton.hidden = false;
                    captureButton.disabled = true;
                }

                setText(
                    'camera-indicator',
                    'OFFLINE'
                );

                const indicator =
                    $('camera-indicator');

                if (indicator) {
                    indicator.classList.remove(
                        'live'
                    );
                }

                showNotice(
                    'camera-message',
                    'Starting camera again...'
                );

                await startCamera();

                updateNavigation();
            }
        );
    }


    // ========================================
    // STEP 2: WEIGHING SCALE
    // ========================================

    async function readWeight() {

        const button =
            $('read-weight-btn');

        if (!button) {
            return;
        }

        button.disabled = true;

        setText(
            'weight-status',
            'Reading scale...'
        );

        try {

            let weight;

            // ====================================
            // DEMO MODE
            // ====================================

            if (demoMode) {

                await delay(600);

                // Simulated weight around 350 g.
                weight =
                    Math.round(
                        (
                            345 +
                            Math.random() * 10
                        ) * 10
                    ) / 10;

            } else {

                // ====================================
                // REAL HARDWARE MODE
                // ====================================

                if (
                    !isReady(
                        'weighingScale'
                    )
                ) {

                    throw new Error(
                        'Weighing scale is not connected.'
                    );
                }

                const response =
                    await fetch(
                        'api/read-weight.php',
                        {
                            cache: 'no-store'
                        }
                    );

                if (!response.ok) {
                    throw new Error(
                        'Unable to read weighing scale.'
                    );
                }

                const data =
                    await response.json();

                if (data.demo === true) {

                    throw new Error(
                        'Unexpected demo reading in hardware mode.'
                    );
                }

                weight =
                    Number(data.weight);
            }

            if (
                !Number.isFinite(weight) ||
                weight <= 0
            ) {

                throw new Error(
                    'Invalid weight reading.'
                );
            }

            sampleWeight = weight;

            setText(
                'weight-value',
                weight.toFixed(1)
            );

            setText(
                'weight-status',
                demoMode
                    ? 'Simulated reading'
                    : 'Weight recorded'
            );

            setText(
                'weight-confirmation',
                `${weight.toFixed(1)} g`
            );

            updateNavigation();

        } catch (error) {

            console.error(
                '[CGI] Weight reading error:',
                error
            );

            sampleWeight = null;

            setText(
                'weight-value',
                '---.-'
            );

            setText(
                'weight-confirmation',
                'Not recorded'
            );

            setText(
                'weight-status',
                error.message ||
                'Unable to read weight.'
            );

            updateNavigation();

        } finally {

            button.disabled = false;
        }
    }

    const readWeightBtn =
        $('read-weight-btn');

    if (readWeightBtn) {

        readWeightBtn.addEventListener(
            'click',
            readWeight
        );
    }


    // ========================================
    // STEP 3: ANALYSIS
    // ========================================

    const STAGE_COUNT = 6;

    function updateAnalysisProgress(
        percentage,
        activeStage
    ) {

        setText(
            'analysis-percentage',
            percentage
        );

        const progress =
            $('analysis-progress');

        if (progress) {

            progress.style.width =
                `${percentage}%`;
        }

        const progressbar =
            $('analysis-progressbar');

        if (progressbar) {

            progressbar.setAttribute(
                'aria-valuenow',
                String(percentage)
            );
        }

        document.querySelectorAll(
            '.analysis-stage'
        ).forEach(
            (stage, index) => {

                stage.classList.toggle(
                    'completed',
                    index < activeStage
                );

                stage.classList.toggle(
                    'active',
                    index === activeStage &&
                    percentage < 100
                );

                if (
                    percentage === 100
                ) {

                    stage.classList.add(
                        'completed'
                    );

                    stage.classList.remove(
                        'active'
                    );
                }
            }
        );
    }


    // ========================================
    // DEMO ANALYSIS RESULT
    // ========================================

    function createDemoResult() {

        // Illustrative interface-testing values.
        // NOT an actual AI prediction.

        const grade =
            'CLASS I';

        const unitPrice =
            DEFAULT_PRICES[grade];

        const weightKg =
            sampleWeight / 1000;

        return {

            grade,

            confidence: 94.2,

            beanCount: 105,

            defectCount: 7,

            defects: [
                {
                    name: 'Discolored beans',
                    count: 3
                },
                {
                    name: 'Broken beans',
                    count: 2
                },
                {
                    name: 'Insect-damaged beans',
                    count: 2
                }
            ],

            unitPrice,

            totalPrice:
                Math.round(
                    unitPrice *
                    weightKg *
                    100
                ) / 100,

            demo: true
        };
    }


    // ========================================
    // VALIDATE ANALYSIS RESULT
    // ========================================

    function validateAnalysisResult(
        result
    ) {

        const validGrades = [
            'EXTRA CLASS',
            'CLASS I',
            'CLASS II'
        ];

        if (
            !result ||
            !validGrades.includes(
                result.grade
            )
        ) {

            throw new Error(
                'Invalid classification result.'
            );
        }

        const numericFields = [
            'confidence',
            'beanCount',
            'defectCount',
            'unitPrice',
            'totalPrice'
        ];

        for (
            const field of numericFields
        ) {

            if (
                !Number.isFinite(
                    Number(
                        result[field]
                    )
                ) ||
                Number(
                    result[field]
                ) < 0
            ) {

                throw new Error(
                    `Invalid result field: ${field}`
                );
            }
        }

        if (
            Number(result.confidence) > 100 ||
            !Array.isArray(
                result.defects
            )
        ) {

            throw new Error(
                'Invalid analysis response.'
            );
        }

        return result;
    }


    // ========================================
    // TRANSACTION ID
    // ========================================

    function createTransactionId() {

        const now =
            new Date();

        const date = [
            now.getFullYear(),

            String(
                now.getMonth() + 1
            ).padStart(2, '0'),

            String(
                now.getDate()
            ).padStart(2, '0')

        ].join('');

        const random =
            Math.random()
                .toString(36)
                .slice(2, 8)
                .toUpperCase();

        return (
            `CGI-${date}-${random}`
        );
    }


    // ========================================
    // REAL ANALYSIS API
    // ========================================

    async function requestRealAnalysis() {

        const response =
            await fetch(
                'api/analyze.php',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json'
                    },

                    body: JSON.stringify({
                        image:
                            capturedImage,

                        weight:
                            sampleWeight,

                        coffeeType:
                            'Robusta'
                    })
                }
            );

        let result;

        try {
            result =
                await response.json();
        } catch (error) {

            throw new Error(
                'The analysis server returned an invalid response.'
            );
        }

        if (!response.ok) {

            throw new Error(
                result.error ||
                'AI analysis failed.'
            );
        }

        if (
            result.demo === true
        ) {

            throw new Error(
                'Hardware mode received simulated results.'
            );
        }

        return validateAnalysisResult(
            result
        );
    }


    // ========================================
    // START ANALYSIS
    // ========================================

    async function startAnalysis() {

        if (
            analysisRunning ||
            analysisComplete
        ) {
            return;
        }

        if (
            !capturedImage ||
            !Number.isFinite(sampleWeight)
        ) {

            showNotice(
                'analysis-notice',
                'Please capture an image and record the sample weight first.',
                true
            );

            return;
        }

        analysisRunning = true;

        if (nextBtn) {
            nextBtn.disabled = true;
        }

        const runId =
            ++analysisRunId;

        const analysisImage =
            $('analysis-image');

        if (analysisImage) {
            analysisImage.src =
                capturedImage;
        }

        updateAnalysisProgress(
            0,
            0
        );

        showNotice(
            'analysis-notice',
            demoMode
                ? 'Running simulated analysis.'
                : 'Sending image to the analysis service.'
        );

        try {

            let result;

            // ====================================
            // DEMO MODE
            // ====================================

            if (demoMode) {

                for (
                    let stage = 0;
                    stage < STAGE_COUNT;
                    stage++
                ) {

                    if (
                        runId !==
                        analysisRunId
                    ) {
                        return;
                    }

                    updateAnalysisProgress(
                        Math.round(
                            stage /
                            STAGE_COUNT *
                            100
                        ),
                        stage
                    );

                    await delay(650);
                }

                result =
                    createDemoResult();

            } else {

                // ====================================
                // REAL BACKEND ANALYSIS
                // ====================================

                result =
                    await requestRealAnalysis();
            }

            if (
                runId !==
                analysisRunId
            ) {
                return;
            }

            const now =
                new Date();

            transaction = {

                id:
                    createTransactionId(),

                timestamp:
                    now.toISOString(),

                coffeeType:
                    'Robusta',

                weight:
                    sampleWeight,

                image:
                    capturedImage,

                ...result
            };

            analysisComplete = true;

            updateAnalysisProgress(
                100,
                6
            );

            showNotice(
                'analysis-notice',
                demoMode
                    ? 'Simulated analysis completed.'
                    : 'Analysis completed successfully.'
            );

        } catch (error) {

            console.error(
                '[CGI] Analysis error:',
                error
            );

            analysisComplete = false;

            showNotice(
                'analysis-notice',
                error.message ||
                'Analysis failed.',
                true
            );

        } finally {

            if (
                runId ===
                analysisRunId
            ) {

                analysisRunning =
                    false;

                updateNavigation();
            }
        }
    }


    // ========================================
    // STEP 4: RESULTS
    // ========================================

    function renderResults() {

        if (!transaction) {
            return;
        }

        setText(
            'result-grade',
            transaction.grade
        );

        setText(
            'result-confidence',
            `${Number(
                transaction.confidence
            ).toFixed(1)}%`
        );

        setText(
            'result-weight',
            `${Number(
                transaction.weight
            ).toFixed(1)} g`
        );

        setText(
            'result-bean-count',
            transaction.beanCount
        );

        setText(
            'result-defect-count',
            transaction.defectCount
        );

        setText(
            'result-total-price',
            Number(
                transaction.totalPrice
            ).toFixed(2)
        );

        setText(
            'pricing-grade',
            transaction.grade
        );

        setText(
            'result-unit-price',
            `${money(
                transaction.unitPrice
            )} / kg`
        );

        setText(
            'pricing-weight',
            `${Number(
                transaction.weight
            ).toFixed(1)} g`
        );

        const demoDisclaimer =
            $('results-demo-disclaimer');

        if (demoDisclaimer) {

            demoDisclaimer.hidden =
                !transaction.demo;
        }

        const defectList =
            $('defect-list');

        if (!defectList) {
            return;
        }

        defectList.replaceChildren();

        if (
            !transaction.defects ||
            transaction.defects.length === 0
        ) {

            defectList.textContent =
                'No defects reported.';

            return;
        }

        transaction.defects.forEach(
            (defect) => {

                const row =
                    document.createElement(
                        'div'
                    );

                row.className =
                    'defect-item';

                const name =
                    document.createElement(
                        'span'
                    );

                name.textContent =
                    defect.name;

                const count =
                    document.createElement(
                        'strong'
                    );

                count.textContent =
                    `${defect.count} detected`;

                row.append(
                    name,
                    count
                );

                defectList.appendChild(
                    row
                );
            }
        );
    }


    // ========================================
    // STEP 5: RECEIPT
    // ========================================

    function updatePrinterStatus() {

        const status =
            $('receipt-printer-status');

        if (!status) {
            return;
        }

        if (demoMode) {

            status.textContent =
                'Demo mode — browser printing';

            return;
        }

        if (
            isReady('printer')
        ) {

            status.textContent =
                'Printer connected';

        } else {

            status.textContent =
                'Printer unavailable';
        }
    }


    function renderReceipt() {

        if (!transaction) {
            return;
        }

        const date =
            new Date(
                transaction.timestamp
            );

        setText(
            'receipt-id',
            transaction.id
        );

        setText(
            'receipt-date',
            date.toLocaleDateString(
                'en-PH',
                {
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit'
                }
            )
        );

        setText(
            'receipt-time',
            date.toLocaleTimeString(
                'en-PH',
                {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }
            )
        );

        setText(
            'receipt-grade',
            transaction.grade
        );

        setText(
            'receipt-weight',
            `${Number(
                transaction.weight
            ).toFixed(1)} g`
        );

        setText(
            'receipt-unit-price',
            `${money(
                transaction.unitPrice
            )} / kg`
        );

        setText(
            'receipt-total',
            money(
                transaction.totalPrice
            )
        );

        const receiptDemo =
            $('receipt-demo');

        if (receiptDemo) {

            receiptDemo.hidden =
                !transaction.demo;
        }

        updatePrinterStatus();
    }


    // ========================================
    // PRINT RECEIPT
    // ========================================

    const printReceiptBtn =
        $('print-receipt-btn');

    if (printReceiptBtn) {

        printReceiptBtn.addEventListener(
            'click',
            () => {

                if (!transaction) {
                    return;
                }

                showNotice(
                    'receipt-notice',
                    'Opening browser print dialog. Select your receipt printer.'
                );

                window.print();
            }
        );
    }


    // ========================================
    // FINISH TRANSACTION
    // ========================================

    function finishTransaction() {

        if (!transaction) {
            return;
        }

        // Do not store the captured image
        // in localStorage.

        const savedTransaction = {
            ...transaction
        };

        delete savedTransaction.image;

        try {

            const history =
                JSON.parse(
                    localStorage.getItem(
                        'cgi-transaction-history'
                    ) || '[]'
                );

            history.unshift(
                savedTransaction
            );

            localStorage.setItem(
                'cgi-transaction-history',
                JSON.stringify(
                    history.slice(0, 100)
                )
            );

            document.dispatchEvent(
                new CustomEvent(
                    'cgi:transaction-completed',
                    {
                        detail:
                            savedTransaction
                    }
                )
            );

            close(true);

        } catch (error) {

            console.error(
                '[CGI] Could not save transaction:',
                error
            );

            showNotice(
                'receipt-notice',
                'Unable to save this transaction. Check browser storage and try again.',
                true
            );
        }
    }


    // ========================================
    // PUBLIC API
    // ========================================

    window.CGITransactionWizard = {
        open,
        close
    };

})();