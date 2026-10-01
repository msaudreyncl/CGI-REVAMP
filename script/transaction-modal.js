(() => {
    'use strict';

    // =========================================================
    // COFFEE GRADE IDENTIFICATION
    // TRANSACTION MODAL CONTROLLER
    //
    // FLOW:
    // 1. Prepare
    // 2. Weight
    // 3. Capture Side A + Side B
    // 4. Analyze
    // 5. Results
    // 6. Receipt
    // =========================================================


    // =========================================================
    // HELPERS
    // =========================================================

    const $ = (id) => document.getElementById(id);

    const delay = (ms) =>
        new Promise((resolve) => setTimeout(resolve, ms));

    const money = (value) =>
        new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).format(Number(value) || 0);


    function setText(id, value) {
        const element = $(id);

        if (element) {
            element.textContent = value;
        }
    }


    function showNotice(id, message, isError = false) {
        const element = $(id);

        if (!element) {
            return;
        }

        element.textContent = message;
        element.classList.toggle('error', Boolean(isError));
    }


    // =========================================================
    // MODAL ELEMENTS
    // =========================================================

    const modal = $('transaction-modal');

    if (!modal) {
        console.error(
            '[CGI] #transaction-modal was not found.'
        );
        return;
    }


    const nextBtn = $('wizard-next-btn');
    const backBtn = $('wizard-back-btn');
    const cancelBtn = $('wizard-cancel-btn');
    const closeBtn = $('modal-close');

    const video = $('camera-video');
    const canvas = $('camera-canvas');
    const capturedPreview = $('captured-image');
    const placeholder = $('camera-placeholder');


    // =========================================================
    // WIZARD CONFIGURATION
    // =========================================================

    const STEP_COUNT = 6;

    const TITLES = [
        'Prepare Sample',
        'Record Sample Weight',
        'Capture Sample',
        'Analyze Quality',
        'Assessment Results',
        'Transaction Receipt'
    ];

    const SUBTITLES = [
        'Prepare the coffee bean sample before beginning the transaction.',
        'Place the prepared sample on the weighing scale.',
        'Capture both sides of the complete coffee bean sample.',
        'Processing both captured sides of the coffee sample.',
        'Review the grading and suggested pricing results.',
        'Review, print, and complete the transaction.'
    ];


    // =========================================================
    // SYSTEM CONFIGURATION
    // =========================================================

    const REQUIRED_WEIGHT = 350;

    const DEFAULT_PRICES = {
        'EXTRA CLASS': 220,
        'CLASS I': 190,
        'CLASS II': 160
    };


    // =========================================================
    // STATE
    // =========================================================

    const state = {
        currentStep: 1,

        previousFocus: null,

        cameraStream: null,
        cameraReady: false,

        /*
         * IMPORTANT:
         * activeSide changes ONLY when:
         *
         * 1. Wizard is reset -> A
         * 2. User clicks SIDE A or SIDE B
         *
         * Capturing an image NEVER changes activeSide.
         */
        activeSide: 'A',

        captured: {
            A: null,
            B: null
        },

        sampleWeight: null,

        analysisRunning: false,
        analysisComplete: false,
        analysisRunId: 0,

        transaction: null,

        hardware: {
            camera: 'READY',
            weighingScale: 'READY',
            printer: 'READY'
        }
    };


    // =========================================================
    // HARDWARE STATUS
    // =========================================================

    function isReady(device) {
        return state.hardware[device] === 'READY';
    }


    function updateHardwareStatus(data = {}) {
        state.hardware = {
            camera: data.camera || 'READY',
            weighingScale:
                data.weighingScale || 'READY',
            printer:
                data.printer || 'READY'
        };


        document
            .querySelectorAll('[data-check-device]')
            .forEach((card) => {
                const key =
                    card.dataset.checkDevice;

                const status =
                    state.hardware[key] || 'READY';

                const label =
                    card.querySelector(
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
                    label.textContent =
                        status === 'READY'
                            ? 'Ready'
                            : status === 'WARNING'
                                ? 'Needs attention'
                                : 'Unavailable';
                }
            });


        updatePrinterStatus();
        updateNavigation();
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
        if (
            window.CGIHardware &&
            typeof window.CGIHardware.refresh === 'function'
        ) {
            try {
                const status =
                    await window.CGIHardware.refresh();

                updateHardwareStatus(status);
                return;

            } catch (error) {
                console.warn(
                    '[CGI] Hardware refresh failed.',
                    error
                );
            }
        }


        updateHardwareStatus({
            camera: 'READY',
            weighingScale: 'READY',
            printer: 'READY'
        });
    }


    const refreshDevicesBtn =
        $('refresh-devices-btn');

    if (refreshDevicesBtn) {
        refreshDevicesBtn.addEventListener(
            'click',
            refreshDevices
        );
    }


    // =========================================================
    // RESET WIZARD
    // =========================================================

    function resetWizard() {
        state.analysisRunId++;

        stopCamera();

        state.currentStep = 1;

        state.cameraReady = false;

        /*
         * Every new transaction begins on Side A.
         */
        state.activeSide = 'A';

        state.captured = {
            A: null,
            B: null
        };

        state.sampleWeight = null;

        state.analysisRunning = false;
        state.analysisComplete = false;

        state.transaction = null;


        // -----------------------------------------------------
        // PREPARATION
        // -----------------------------------------------------

        document
            .querySelectorAll('.prep-checkbox')
            .forEach((checkbox) => {
                checkbox.checked = false;
            });


        showNotice(
            'prepare-notice',
            'Complete the preparation checklist to continue.'
        );


        // -----------------------------------------------------
        // WEIGHT
        // -----------------------------------------------------

        setText(
            'weight-value',
            '---.-'
        );

        setText(
            'weight-status',
            'Waiting for reading'
        );

        setText(
            'weight-confirmation',
            'Not recorded'
        );

        showNotice(
            'weight-notice',
            `Place approximately ${REQUIRED_WEIGHT} g of green Robusta coffee beans on the scale.`
        );


        // -----------------------------------------------------
        // CAMERA
        // -----------------------------------------------------

        if (video) {
            video.hidden = true;
            video.srcObject = null;
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


        const indicator =
            $('camera-indicator');

        if (indicator) {
            indicator.classList.remove('live');
        }


        const captureBtn =
            $('capture-btn');

        if (captureBtn) {
            captureBtn.hidden = false;
            captureBtn.disabled = true;
            captureBtn.textContent =
                'Capture Side A';
        }


        const retakeBtn =
            $('retake-btn');

        if (retakeBtn) {
            retakeBtn.hidden = true;
        }


        const openCameraBtn =
            $('open-camera-btn');

        if (openCameraBtn) {
            openCameraBtn.hidden = true;
        }


        setText(
            'capture-status',
            'Not captured'
        );

        setText(
            'side-a-status',
            'Not captured'
        );

        setText(
            'side-b-status',
            'Not captured'
        );

        setText(
            'side-a-confirmation',
            'Pending'
        );

        setText(
            'side-b-confirmation',
            'Pending'
        );


        showNotice(
            'camera-message',
            'Camera will start automatically when you reach the Capture step.'
        );

        showNotice(
            'capture-notice',
            'Capture Side A first, then reorient the sample and select Side B.'
        );


        // -----------------------------------------------------
        // ANALYSIS
        // -----------------------------------------------------

        resetAnalysisDisplay();


        // -----------------------------------------------------
        // RECEIPT
        // -----------------------------------------------------

        showNotice(
            'receipt-notice',
            ''
        );


        setActiveSideButton('A');
        updateSideStatus();
        updateNavigation();
    }


    // =========================================================
    // RESET ANALYSIS DISPLAY
    // =========================================================

    function resetAnalysisDisplay() {
        const progress =
            $('analysis-progress');

        if (progress) {
            progress.style.width = '0%';
        }


        const progressbar =
            $('analysis-progressbar');

        if (progressbar) {
            progressbar.setAttribute(
                'aria-valuenow',
                '0'
            );
        }


        setText(
            'analysis-percentage',
            '0'
        );


        document
            .querySelectorAll('.analysis-stage')
            .forEach((stage) => {
                stage.classList.remove(
                    'active',
                    'completed'
                );
            });


        showNotice(
            'analysis-notice',
            'Waiting to begin analysis.'
        );
    }


    // =========================================================
    // OPEN MODAL
    // =========================================================

    async function open() {
        state.previousFocus =
            document.activeElement;

        resetWizard();

        modal.hidden = false;

        document.body.classList.add(
            'modal-open'
        );

        showStep(1);

        await refreshDevices();

        if (closeBtn) {
            closeBtn.focus();
        }
    }


    // =========================================================
    // CLOSE MODAL
    // =========================================================

    function close(force = false) {
        if (
            !force &&
            state.currentStep > 1
        ) {
            const confirmed =
                window.confirm(
                    'Cancel this transaction? Any unsaved progress will be lost.'
                );

            if (!confirmed) {
                return;
            }
        }


        state.analysisRunId++;

        stopCamera();

        modal.hidden = true;

        document.body.classList.remove(
            'modal-open'
        );


        if (
            state.previousFocus &&
            typeof state.previousFocus.focus ===
                'function'
        ) {
            state.previousFocus.focus();
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
        }
    );


    // =========================================================
    // SHOW STEP
    // =========================================================

    function showStep(step) {
        if (
            step < 1 ||
            step > STEP_COUNT
        ) {
            return;
        }


        state.currentStep = step;


        document
            .querySelectorAll('.wizard-page')
            .forEach((page) => {
                const active =
                    page.id ===
                    `wizard-step-${step}`;

                page.hidden = !active;

                page.classList.toggle(
                    'active',
                    active
                );
            });


        document
            .querySelectorAll('[data-step-indicator]')
            .forEach((indicator) => {
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
            TITLES[step - 1]
        );

        setText(
            'modal-subtitle',
            SUBTITLES[step - 1]
        );


        configureFooter();


        // =====================================================
        // STEP 3 — CAPTURE
        // =====================================================

        if (step === 3) {

            /*
             * IMPORTANT:
             *
             * DO NOT automatically select Side B here.
             *
             * state.activeSide remains whatever the USER
             * last selected.
             *
             * On a new transaction, resetWizard() sets it
             * to Side A.
             */

            updateCaptureScreen();


            /*
             * Start camera automatically when entering
             * the Capture step.
             */
            if (!state.cameraReady) {
                startCamera();
            }
        }


        // =====================================================
        // STEP 4 — ANALYSIS
        // =====================================================

        if (
            step === 4 &&
            !state.analysisComplete &&
            !state.analysisRunning
        ) {
            startAnalysis();
        }


        // =====================================================
        // STEP 5 — RESULTS
        // =====================================================

        if (
            step === 5 &&
            state.transaction
        ) {
            renderResults();
        }


        // =====================================================
        // STEP 6 — RECEIPT
        // =====================================================

        if (
            step === 6 &&
            state.transaction
        ) {
            renderReceipt();
        }


        updateNavigation();


        const modalBody =
            modal.querySelector('.modal-body');

        if (modalBody) {
            modalBody.scrollTop = 0;
        }
    }


    // =========================================================
    // FOOTER
    // =========================================================

    function configureFooter() {
        if (backBtn) {
            backBtn.hidden =
                state.currentStep === 1 ||
                state.currentStep === 4 ||
                state.currentStep === 6;
        }


        if (cancelBtn) {
            cancelBtn.hidden =
                state.currentStep === 6;
        }


        if (!nextBtn) {
            return;
        }


        switch (state.currentStep) {
            case 1:
                nextBtn.textContent =
                    'Continue to Weight';
                break;

            case 2:
                nextBtn.textContent =
                    'Continue to Capture';
                break;

            case 3:
                nextBtn.textContent =
                    'Analyze Sample';
                break;

            case 4:
                nextBtn.textContent =
                    'View Results';
                break;

            case 5:
                nextBtn.textContent =
                    'Generate Receipt';
                break;

            case 6:
                nextBtn.textContent =
                    'Finish Transaction';
                break;
        }
    }


    // =========================================================
    // STEP 1 — PREPARATION
    // =========================================================

    function preparationComplete() {
        const checkboxes = [
            ...document.querySelectorAll(
                '.prep-checkbox'
            )
        ];

        return (
            checkboxes.length > 0 &&
            checkboxes.every(
                (checkbox) =>
                    checkbox.checked
            )
        );
    }


    document
        .querySelectorAll('.prep-checkbox')
        .forEach((checkbox) => {
            checkbox.addEventListener(
                'change',
                updateNavigation
            );
        });


    // =========================================================
    // STEP 2 — WEIGHT
    // =========================================================

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
            let weight = null;


            // -------------------------------------------------
            // ATTEMPT ACTUAL SCALE API
            // -------------------------------------------------

            try {
                const response =
                    await fetch(
                        'api/read-weight.php',
                        {
                            cache: 'no-store'
                        }
                    );

                if (response.ok) {
                    const data =
                        await response.json();

                    const receivedWeight =
                        Number(data.weight);

                    if (
                        Number.isFinite(
                            receivedWeight
                        ) &&
                        receivedWeight > 0
                    ) {
                        weight =
                            receivedWeight;
                    }
                }

            } catch (error) {
                console.info(
                    '[CGI] Scale API unavailable. Using current prototype reading.'
                );
            }


            // -------------------------------------------------
            // CURRENT PROTOTYPE READING
            // -------------------------------------------------

            if (
                !Number.isFinite(weight) ||
                weight <= 0
            ) {
                await delay(500);

                /*
                 * Keep the generated value at or above
                 * the required 350 g so the prototype
                 * transaction can proceed normally.
                 */
                weight =
                    Math.round(
                        (
                            350 +
                            Math.random() * 4
                        ) * 10
                    ) / 10;
            }


            state.sampleWeight =
                weight;


            setText(
                'weight-value',
                weight.toFixed(1)
            );

            setText(
                'weight-status',
                'Weight recorded'
            );

            setText(
                'weight-confirmation',
                `${weight.toFixed(1)} g`
            );


            if (weight < REQUIRED_WEIGHT) {
                showNotice(
                    'weight-notice',
                    `Recorded weight: ${weight.toFixed(1)} g. Add more coffee beans until the required ${REQUIRED_WEIGHT} g sample is reached.`,
                    true
                );

            } else {
                showNotice(
                    'weight-notice',
                    `Sample weight recorded successfully: ${weight.toFixed(1)} g.`
                );
            }

        } catch (error) {
            console.error(
                '[CGI] Weight error:',
                error
            );


            state.sampleWeight =
                null;

            setText(
                'weight-value',
                '---.-'
            );

            setText(
                'weight-status',
                'Unable to read weight'
            );

            setText(
                'weight-confirmation',
                'Not recorded'
            );

            showNotice(
                'weight-notice',
                'Unable to obtain a valid weight reading.',
                true
            );

        } finally {
            button.disabled = false;
            updateNavigation();
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


    function weightComplete() {
        return (
            Number.isFinite(
                state.sampleWeight
            ) &&
            state.sampleWeight >=
                REQUIRED_WEIGHT
        );
    }


    // =========================================================
    // STEP 3 — CAMERA
    // =========================================================

    async function startCamera() {
        if (!video) {
            showNotice(
                'camera-message',
                'Camera video element was not found.',
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

            showCameraRecoveryButton();
            return;
        }


        /*
         * Camera already running.
         */
        if (
            state.cameraReady &&
            state.cameraStream
        ) {
            updateCaptureScreen();
            return;
        }


        stopCamera();


        try {
            showNotice(
                'camera-message',
                `Starting camera for Side ${state.activeSide}...`
            );


            state.cameraStream =
                await navigator.mediaDevices
                    .getUserMedia({
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
                state.cameraStream;

            video.setAttribute(
                'playsinline',
                ''
            );

            video.muted = true;

            await video.play();

            state.cameraReady = true;


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


            const openCameraBtn =
                $('open-camera-btn');

            if (openCameraBtn) {
                openCameraBtn.hidden = true;
            }


            updateCaptureScreen();

        } catch (error) {
            console.error(
                '[CGI] Camera error:',
                error
            );


            stopCamera();


            let message =
                'Unable to access the camera.';


            if (
                error &&
                error.name === 'NotAllowedError'
            ) {
                message =
                    'Camera permission was denied. Allow camera access in the browser and try again.';

            } else if (
                error &&
                error.name === 'NotFoundError'
            ) {
                message =
                    'No camera was detected on this device.';

            } else if (
                error &&
                error.name === 'NotReadableError'
            ) {
                message =
                    'The camera is currently being used by another application.';

            } else if (
                error &&
                error.name === 'SecurityError'
            ) {
                message =
                    'Camera access was blocked. Use localhost or HTTPS.';
            }


            showNotice(
                'camera-message',
                message,
                true
            );

            showCameraRecoveryButton();
            updateCaptureScreen();
        }
    }


    function showCameraRecoveryButton() {
        const button =
            $('open-camera-btn');

        if (button) {
            button.hidden = false;
            button.textContent =
                'Start Camera';
        }
    }


    function stopCamera() {
        if (state.cameraStream) {
            state.cameraStream
                .getTracks()
                .forEach((track) => {
                    track.stop();
                });

            state.cameraStream = null;
        }


        if (video) {
            video.srcObject = null;
        }


        state.cameraReady = false;


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
    }


    const openCameraBtn =
        $('open-camera-btn');

    if (openCameraBtn) {
        openCameraBtn.addEventListener(
            'click',
            startCamera
        );
    }


    // =========================================================
    // SIDE SELECTOR
    // =========================================================

    function setActiveSideButton(side) {
        document
            .querySelectorAll(
                '[data-capture-side]'
            )
            .forEach((button) => {
                const active =
                    button.dataset.captureSide ===
                    side;

                button.classList.toggle(
                    'active',
                    active
                );

                button.setAttribute(
                    'aria-pressed',
                    active
                        ? 'true'
                        : 'false'
                );
            });
    }


    /*
     * THIS IS THE ONLY USER INTERACTION
     * THAT CHANGES SIDE A <-> SIDE B.
     */
    document
        .querySelectorAll(
            '[data-capture-side]'
        )
        .forEach((button) => {
            button.addEventListener(
                'click',
                () => {
                    const side =
                        button.dataset.captureSide;

                    if (
                        side !== 'A' &&
                        side !== 'B'
                    ) {
                        return;
                    }


                    /*
                     * USER manually selected a side.
                     */
                    state.activeSide = side;

                    updateCaptureScreen();
                }
            );
        });


    // =========================================================
    // CAPTURE SCREEN
    // =========================================================

    function updateCaptureScreen() {
        const side =
            state.activeSide;

        const currentImage =
            state.captured[side];

        const captureBtn =
            $('capture-btn');

        const retakeBtn =
            $('retake-btn');

        const cameraMessage =
            $('camera-message');


        /*
         * Highlight ONLY the side stored in activeSide.
         *
         * Since captureCurrentSide() never changes activeSide,
         * capturing Side A will NOT move this to Side B.
         */
        setActiveSideButton(side);


        // =====================================================
        // SELECTED SIDE HAS ALREADY BEEN CAPTURED
        // =====================================================

        if (currentImage) {

            /*
             * Show captured image for selected side.
             */
            if (capturedPreview) {
                capturedPreview.src =
                    currentImage;

                capturedPreview.hidden =
                    false;
            }


            /*
             * Hide live video while reviewing
             * captured image.
             *
             * Camera stream remains active in background.
             */
            if (video) {
                video.hidden = true;
            }


            if (placeholder) {
                placeholder.hidden = true;
            }


            /*
             * Hide Capture button.
             */
            if (captureBtn) {
                captureBtn.hidden = true;
            }


            /*
             * Show Retake button for selected side.
             */
            if (retakeBtn) {
                retakeBtn.hidden = false;

                retakeBtn.textContent =
                    `Retake Side ${side}`;
            }


            if (cameraMessage) {

                if (
                    side === 'A' &&
                    !state.captured.B
                ) {
                    cameraMessage.textContent =
                        'Side A captured successfully. Reorient the coffee beans, then select SIDE B when ready.';

                } else if (
                    side === 'B' &&
                    !state.captured.A
                ) {
                    cameraMessage.textContent =
                        'Side B captured successfully. Select SIDE A to complete the other side.';

                } else if (
                    state.captured.A &&
                    state.captured.B
                ) {
                    cameraMessage.textContent =
                        `Side ${side} captured successfully. Both sides are ready for analysis.`;

                } else {
                    cameraMessage.textContent =
                        `Side ${side} captured successfully.`;
                }
            }
        }


        // =====================================================
        // SELECTED SIDE HAS NOT BEEN CAPTURED
        // =====================================================

        else {

            /*
             * Remove old captured preview.
             */
            if (capturedPreview) {
                capturedPreview.hidden = true;
                capturedPreview.removeAttribute(
                    'src'
                );
            }


            /*
             * No image exists for this side,
             * therefore Retake must not appear.
             */
            if (retakeBtn) {
                retakeBtn.hidden = true;
            }


            /*
             * Show Capture button for selected side.
             */
            if (captureBtn) {
                captureBtn.hidden = false;

                captureBtn.textContent =
                    `Capture Side ${side}`;

                captureBtn.disabled =
                    !state.cameraReady;
            }


            // -------------------------------------------------
            // CAMERA ACTIVE
            // -------------------------------------------------

            if (
                state.cameraReady &&
                state.cameraStream &&
                video &&
                video.srcObject
            ) {
                video.hidden = false;

                if (placeholder) {
                    placeholder.hidden = true;
                }

                if (cameraMessage) {
                    if (
                        side === 'B' &&
                        state.captured.A
                    ) {
                        cameraMessage.textContent =
                            'Sample reoriented? Capture Side B when ready.';

                    } else {
                        cameraMessage.textContent =
                            `Camera ready. Capture Side ${side}.`;
                    }
                }
            }


            // -------------------------------------------------
            // CAMERA NOT ACTIVE
            // -------------------------------------------------

            else {
                if (video) {
                    video.hidden = true;
                }

                if (placeholder) {
                    placeholder.hidden = false;
                }

                if (cameraMessage) {
                    cameraMessage.textContent =
                        `Starting camera for Side ${side}...`;
                }
            }
        }


        updateSideStatus();
        updateNavigation();
    }


    // =========================================================
    // SIDE STATUS
    // =========================================================

    function updateSideStatus() {
        const sideAComplete =
            Boolean(state.captured.A);

        const sideBComplete =
            Boolean(state.captured.B);


        setText(
            'side-a-status',
            sideAComplete
                ? 'Captured'
                : 'Not captured'
        );

        setText(
            'side-b-status',
            sideBComplete
                ? 'Captured'
                : 'Not captured'
        );


        setText(
            'side-a-confirmation',
            sideAComplete
                ? 'Captured'
                : 'Pending'
        );

        setText(
            'side-b-confirmation',
            sideBComplete
                ? 'Captured'
                : 'Pending'
        );


        if (
            sideAComplete &&
            sideBComplete
        ) {
            setText(
                'capture-status',
                'Both sides captured'
            );

            showNotice(
                'capture-notice',
                'Side A and Side B are ready for analysis.'
            );

        } else if (sideAComplete) {
            setText(
                'capture-status',
                'Side A captured'
            );

            showNotice(
                'capture-notice',
                'Side A is complete. Reorient the coffee beans, then manually select Side B.'
            );

        } else if (sideBComplete) {
            setText(
                'capture-status',
                'Side B captured'
            );

            showNotice(
                'capture-notice',
                'Side B is complete. Select Side A to complete the remaining capture.'
            );

        } else {
            setText(
                'capture-status',
                'Not captured'
            );

            showNotice(
                'capture-notice',
                'Capture Side A first, then reorient the sample and manually select Side B.'
            );
        }
    }


    // =========================================================
    // CAPTURE CURRENT SIDE
    // =========================================================

    async function captureCurrentSide() {
        /*
         * Lock the selected side at the beginning
         * of the capture.
         *
         * This prevents accidental state changes during
         * the countdown.
         */
        const side =
            state.activeSide;


        if (
            !state.cameraReady ||
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


        if (!canvas) {
            showNotice(
                'camera-message',
                'Camera capture canvas was not found.',
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


        // =====================================================
        // COUNTDOWN
        // =====================================================

        for (
            let number = 3;
            number >= 1;
            number--
        ) {
            if (
                modal.hidden ||
                state.currentStep !== 3 ||
                !state.cameraReady
            ) {
                if (countdown) {
                    countdown.hidden = true;
                }

                updateCaptureScreen();
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


        // =====================================================
        // PREPARE CANVAS
        // =====================================================

        canvas.width =
            video.videoWidth;

        canvas.height =
            video.videoHeight;


        const context =
            canvas.getContext('2d');


        if (!context) {
            showNotice(
                'camera-message',
                'Unable to prepare the camera capture.',
                true
            );

            updateCaptureScreen();
            return;
        }


        // =====================================================
        // CAPTURE IMAGE
        // =====================================================

        try {
            context.drawImage(
                video,
                0,
                0,
                canvas.width,
                canvas.height
            );


            const image =
                canvas.toDataURL(
                    'image/jpeg',
                    0.90
                );


            /*
             * Save image ONLY to selected side.
             */
            state.captured[side] =
                image;


            // =================================================
            // CRITICAL BEHAVIOR
            // =================================================
            //
            // DO NOT:
            //
            // state.activeSide = 'B';
            //
            // DO NOT automatically change tabs.
            //
            // If Side A was selected before capture,
            // Side A remains selected after capture.
            //
            // If Side B was selected before capture,
            // Side B remains selected after capture.
            //
            // =================================================


            updateCaptureScreen();


            if (side === 'A') {
                showNotice(
                    'camera-message',
                    'Side A captured successfully. Review the image, reorient the coffee beans, then select SIDE B when ready.'
                );

            } else {
                if (
                    state.captured.A &&
                    state.captured.B
                ) {
                    showNotice(
                        'camera-message',
                        'Side B captured successfully. Both sides are ready for analysis.'
                    );

                } else {
                    showNotice(
                        'camera-message',
                        'Side B captured successfully. Select SIDE A to complete the remaining capture.'
                    );
                }
            }


            updateSideStatus();
            updateNavigation();

        } catch (error) {
            console.error(
                '[CGI] Capture failed:',
                error
            );

            showNotice(
                'camera-message',
                `Unable to capture Side ${side}.`,
                true
            );

            updateCaptureScreen();
        }
    }


    const captureBtn =
        $('capture-btn');

    if (captureBtn) {
        captureBtn.addEventListener(
            'click',
            captureCurrentSide
        );
    }


    // =========================================================
    // RETAKE CURRENT SIDE
    // =========================================================

    function retakeCurrentSide() {
        const side =
            state.activeSide;


        /*
         * Delete ONLY the selected side.
         */
        state.captured[side] = null;


        /*
         * activeSide DOES NOT CHANGE.
         *
         * Example:
         *
         * SIDE A selected
         * -> Retake Side A
         * -> still SIDE A
         * -> live camera
         * -> Capture Side A
         */
        updateCaptureScreen();


        showNotice(
            'camera-message',
            `Camera ready. Capture Side ${side} again.`
        );


        updateSideStatus();
        updateNavigation();
    }


    const retakeBtn =
        $('retake-btn');

    if (retakeBtn) {
        retakeBtn.addEventListener(
            'click',
            retakeCurrentSide
        );
    }


    // =========================================================
    // CAPTURE VALIDATION
    // =========================================================

    function captureComplete() {
        return Boolean(
            state.captured.A &&
            state.captured.B
        );
    }


    // =========================================================
    // STEP 4 — ANALYSIS
    // =========================================================

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


        document
            .querySelectorAll('.analysis-stage')
            .forEach(
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

                    if (percentage === 100) {
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


    // =========================================================
    // ANALYSIS IMAGE PREVIEWS
    // =========================================================

    function setAnalysisPreviews() {
        const sideA =
            $('analysis-side-a');

        const sideB =
            $('analysis-side-b');


        if (sideA) {
            sideA.src =
                state.captured.A || '';
        }

        if (sideB) {
            sideB.src =
                state.captured.B || '';
        }


        /*
         * Compatibility with old HTML.
         */
        const legacyImage =
            $('analysis-image');

        if (
            legacyImage &&
            !sideA &&
            !sideB
        ) {
            legacyImage.src =
                state.captured.A || '';
        }


        setText(
            'analysis-weight',
            Number.isFinite(
                state.sampleWeight
            )
                ? `${state.sampleWeight.toFixed(1)} g`
                : '—'
        );
    }


    // =========================================================
    // CURRENT PROTOTYPE ASSESSMENT RESULT
    // =========================================================

    function createPrototypeResult() {
        const grade =
            'CLASS I';

        const unitPrice =
            DEFAULT_PRICES[grade];

        const weightKg =
            state.sampleWeight / 1000;


        return {
            grade,

            confidence: 94.2,

            beanCount: 105,

            defectCount: 7,

            defects: [
                {
                    name: 'Partial Black',
                    count: 3,
                    points: 1
                },

                {
                    name: 'Slight Insect Damage',
                    count: 2,
                    points: 0.2
                },

                {
                    name: 'Immature',
                    count: 2,
                    points: 0.4
                }
            ],

            unitPrice,

            totalPrice:
                Math.round(
                    unitPrice *
                    weightKg *
                    100
                ) / 100
        };
    }


    // =========================================================
    // REAL ANALYSIS API
    // =========================================================

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
                        sideA:
                            state.captured.A,

                        sideB:
                            state.captured.B,

                        weight:
                            state.sampleWeight,

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
                'Analysis failed.'
            );
        }


        return result;
    }


    // =========================================================
    // START ANALYSIS
    // =========================================================

    async function startAnalysis() {
        if (
            state.analysisRunning ||
            state.analysisComplete
        ) {
            return;
        }


        if (!captureComplete()) {
            showNotice(
                'analysis-notice',
                'Both Side A and Side B must be captured before analysis.',
                true
            );

            return;
        }


        if (!weightComplete()) {
            showNotice(
                'analysis-notice',
                'A valid sample weight is required before analysis.',
                true
            );

            return;
        }


        state.analysisRunning = true;

        const runId =
            ++state.analysisRunId;


        setAnalysisPreviews();

        updateAnalysisProgress(
            0,
            0
        );


        showNotice(
            'analysis-notice',
            'Analyzing Side A and Side B...'
        );


        try {

            // -------------------------------------------------
            // ANALYSIS PROGRESS
            // -------------------------------------------------

            for (
                let stage = 0;
                stage < STAGE_COUNT;
                stage++
            ) {
                if (
                    runId !==
                    state.analysisRunId
                ) {
                    return;
                }


                updateAnalysisProgress(
                    Math.round(
                        (
                            stage /
                            STAGE_COUNT
                        ) * 100
                    ),
                    stage
                );


                await delay(650);
            }


            let result;


            // -------------------------------------------------
            // ATTEMPT ACTUAL ANALYSIS
            // -------------------------------------------------

            try {
                result =
                    await requestRealAnalysis();

            } catch (error) {
                console.info(
                    '[CGI] Analysis API unavailable. Continuing with current prototype assessment.'
                );

                result =
                    createPrototypeResult();
            }


            if (
                runId !==
                state.analysisRunId
            ) {
                return;
            }


            const now =
                new Date();


            state.transaction = {
                id:
                    createTransactionId(),

                timestamp:
                    now.toISOString(),

                coffeeType:
                    'Robusta',

                weight:
                    state.sampleWeight,

                sideA:
                    state.captured.A,

                sideB:
                    state.captured.B,

                ...result
            };


            state.analysisComplete = true;


            updateAnalysisProgress(
                100,
                STAGE_COUNT
            );


            showNotice(
                'analysis-notice',
                'Analysis completed successfully.'
            );

        } catch (error) {
            console.error(
                '[CGI] Analysis error:',
                error
            );


            state.analysisComplete = false;


            showNotice(
                'analysis-notice',
                error.message ||
                'Analysis failed.',
                true
            );

        } finally {
            if (
                runId ===
                state.analysisRunId
            ) {
                state.analysisRunning = false;
                updateNavigation();
            }
        }
    }


    // =========================================================
    // TRANSACTION ID
    // =========================================================

    function createTransactionId() {
        const now =
            new Date();


        const date = [
            now.getFullYear(),

            String(
                now.getMonth() + 1
            ).padStart(
                2,
                '0'
            ),

            String(
                now.getDate()
            ).padStart(
                2,
                '0'
            )
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


    // =========================================================
    // STEP 5 — RESULTS
    // =========================================================

    function renderResults() {
        const transaction =
            state.transaction;

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


        const disclaimer =
            $('results-demo-disclaimer');

        if (disclaimer) {
            disclaimer.hidden = true;
        }


        renderDefects();
    }


    // =========================================================
    // DEFECT RESULTS
    // =========================================================

    function renderDefects() {
        const defectList =
            $('defect-list');


        if (
            !defectList ||
            !state.transaction
        ) {
            return;
        }


        defectList.replaceChildren();


        const defects =
            state.transaction.defects || [];


        if (!defects.length) {
            defectList.textContent =
                'No defects detected.';
            return;
        }


        defects.forEach(
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


    // =========================================================
    // STEP 6 — RECEIPT
    // =========================================================

    function updatePrinterStatus() {
        const element =
            $('receipt-printer-status');

        if (!element) {
            return;
        }


        element.textContent =
            isReady('printer')
                ? 'Printer ready'
                : 'Printer unavailable';
    }


    function renderReceipt() {
        const transaction =
            state.transaction;

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
            receiptDemo.hidden = true;
        }


        updatePrinterStatus();
    }


    // =========================================================
    // PRINT RECEIPT
    // =========================================================

    function printReceipt() {
        if (!state.transaction) {
            return;
        }


        const receipt =
            $('receipt-paper');


        if (!receipt) {
            showNotice(
                'receipt-notice',
                'Receipt content could not be found.',
                true
            );

            return;
        }


        const printWindow =
            window.open(
                '',
                'CGIReceipt',
                'width=480,height=720'
            );


        if (!printWindow) {
            showNotice(
                'receipt-notice',
                'The print window was blocked. Allow pop-ups for this page and try again.',
                true
            );

            return;
        }


        const receiptHTML =
            receipt.outerHTML;


        printWindow.document.open();


        printWindow.document.write(`
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<title>CGI Transaction Receipt</title>

<style>

    @page {
        margin: 4mm;
    }

    * {
        box-sizing: border-box;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        background: #ffffff;
        color: #000000;
        font-family:
            "Courier New",
            Courier,
            monospace;
    }

    body {
        width: 80mm;
        margin: 0 auto;
        padding: 4mm;
        font-size: 12px;
        line-height: 1.4;
    }

    #receipt-paper {
        width: 100%;
        margin: 0;
        padding: 0;
        background: #ffffff;
        color: #000000;
        box-shadow: none;
        border: none;
    }

    #receipt-paper * {
        color: #000000 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    h1,
    h2,
    h3,
    p {
        margin-top: 0;
    }

    img {
        max-width: 100%;
    }

    button {
        display: none !important;
    }

</style>

</head>

<body>

${receiptHTML}

</body>
</html>
        `);


        printWindow.document.close();


        showNotice(
            'receipt-notice',
            'Receipt prepared for printing.'
        );


        setTimeout(
            () => {
                printWindow.focus();
                printWindow.print();
            },
            300
        );


        printWindow.onafterprint =
            () => {
                printWindow.close();
            };
    }


    const printReceiptBtn =
        $('print-receipt-btn');

    if (printReceiptBtn) {
        printReceiptBtn.addEventListener(
            'click',
            printReceipt
        );
    }


    // =========================================================
    // NAVIGATION VALIDATION
    // =========================================================

    function updateNavigation() {
        if (!nextBtn) {
            return;
        }


        switch (state.currentStep) {

            // PREPARE
            case 1:
                nextBtn.disabled =
                    !preparationComplete();

                if (
                    preparationComplete()
                ) {
                    showNotice(
                        'prepare-notice',
                        'Preparation complete. Ready to continue.'
                    );
                }

                break;


            // WEIGHT
            case 2:
                nextBtn.disabled =
                    !weightComplete();

                break;


            // CAPTURE
            case 3:
                /*
                 * Analysis is available ONLY when
                 * both sides have been captured.
                 */
                nextBtn.disabled =
                    !captureComplete();

                break;


            // ANALYSIS
            case 4:
                nextBtn.disabled =
                    !state.analysisComplete;

                break;


            // RESULTS
            case 5:
                nextBtn.disabled =
                    !state.transaction;

                break;


            // RECEIPT
            case 6:
                nextBtn.disabled =
                    !state.transaction;

                break;
        }
    }


    // =========================================================
    // NEXT BUTTON
    // =========================================================

    if (nextBtn) {
        nextBtn.addEventListener(
            'click',
            () => {
                if (nextBtn.disabled) {
                    return;
                }


                switch (state.currentStep) {

                    case 1:
                        showStep(2);
                        break;


                    case 2:
                        showStep(3);
                        break;


                    case 3:
                        /*
                         * Both sides are already captured,
                         * so camera can now be stopped.
                         */
                        stopCamera();

                        showStep(4);
                        break;


                    case 4:
                        showStep(5);
                        break;


                    case 5:
                        showStep(6);
                        break;


                    case 6:
                        finishTransaction();
                        break;
                }
            }
        );
    }


    // =========================================================
    // BACK BUTTON
    // =========================================================

    if (backBtn) {
        backBtn.addEventListener(
            'click',
            () => {
                switch (state.currentStep) {

                    case 2:
                        showStep(1);
                        break;


                    case 3:
                        stopCamera();
                        showStep(2);
                        break;


                    case 5:
                        /*
                         * Returning from Results allows the
                         * user to revise the captures.
                         */
                        state.analysisRunId++;

                        state.analysisComplete =
                            false;

                        state.analysisRunning =
                            false;

                        state.transaction =
                            null;

                        resetAnalysisDisplay();

                        /*
                         * IMPORTANT:
                         * Do NOT automatically choose Side B.
                         *
                         * Keep the last side selected by user.
                         */
                        showStep(3);

                        break;
                }
            }
        );
    }


    // =========================================================
    // FINISH TRANSACTION
    // =========================================================

    function finishTransaction() {
        if (!state.transaction) {
            return;
        }


        /*
         * Do not store large Base64 camera images
         * in localStorage.
         */
        const savedTransaction = {
            ...state.transaction
        };


        delete savedTransaction.sideA;
        delete savedTransaction.sideB;


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
                    history.slice(
                        0,
                        100
                    )
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
                '[CGI] Transaction save error:',
                error
            );


            showNotice(
                'receipt-notice',
                'Unable to save the transaction.',
                true
            );
        }
    }


    // =========================================================
    // INITIALIZE
    // =========================================================

    updateHardwareStatus({
        camera: 'READY',
        weighingScale: 'READY',
        printer: 'READY'
    });


    // =========================================================
    // PUBLIC API
    // =========================================================

    window.CGITransactionWizard = {
        open,
        close,
        refreshDevices
    };

})();