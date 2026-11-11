import { Html5Qrcode, Html5QrcodeScannerState } from 'html5-qrcode';

const dialog = document.querySelector('#ar-scanner-dialog');
const openButton = document.querySelector('[data-ar-open-scanner]');
const closeButton = document.querySelector('[data-ar-close-scanner]');
const dialogStatus = document.querySelector('[data-ar-dialog-status]');

if (
    dialog instanceof HTMLDialogElement
    && openButton instanceof HTMLButtonElement
    && closeButton instanceof HTMLButtonElement
    && dialogStatus
) {
    let scanner = null;
    let cameraStarted = false;
    let cameraStarting = false;
    let scanAttempt = 0;
    let navigating = false;

    const setStatus = (message) => {
        dialogStatus.textContent = message;
    };

    const stopCamera = async (currentScanner = scanner) => {
        if (!currentScanner) {
            return;
        }

        const state = currentScanner.getState();

        if (
            state === Html5QrcodeScannerState.SCANNING
            || state === Html5QrcodeScannerState.PAUSED
        ) {
            await currentScanner.stop();
        }

        if (currentScanner === scanner) {
            cameraStarted = false;
        }

        await currentScanner.clear();

        if (currentScanner === scanner) {
            scanner = null;
        }
    };

    const resolveModelUrl = (decodedText) => {
        const modelRoot = new URL(dialog.dataset.arRoutePrefix, window.location.origin);
        let scannedUrl;

        try {
            scannedUrl = new URL(decodedText, modelRoot.origin);
        } catch {
            return null;
        }

        const routePrefix = modelRoot.pathname.replace(/\/$/, '');
        const modelId = scannedUrl.pathname.startsWith(`${routePrefix}/`)
            ? scannedUrl.pathname.slice(routePrefix.length + 1)
            : '';
        const isUuid = /^[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}$/i.test(modelId);

        if (
            scannedUrl.origin !== modelRoot.origin
            || !isUuid
            || scannedUrl.search !== ''
            || scannedUrl.hash !== ''
        ) {
            return null;
        }

        return scannedUrl;
    };

    const startCamera = async () => {
        const attempt = ++scanAttempt;
        const currentScanner = new Html5Qrcode('qr-reader');
        navigating = false;
        scanner = currentScanner;
        cameraStarting = true;
        openButton.disabled = true;
        setStatus('Meminta akses kamera...');

        try {
            await currentScanner.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: (width, height) => {
                        const size = Math.floor(Math.min(width, height) * 0.72);

                        return { width: size, height: size };
                    },
                },
                async (decodedText) => {
                    if (navigating) {
                        return;
                    }

                    const modelUrl = resolveModelUrl(decodedText);

                    if (!modelUrl) {
                        setStatus('QR tidak valid. Pindai QR AR resmi dari kemasan WireNest.');
                        return;
                    }

                    navigating = true;
                    setStatus('QR berhasil dipindai. Membuka model AR...');

                    try {
                        await stopCamera();
                    } catch (error) {
                        console.error('Kamera gagal dihentikan setelah pemindaian.', error);
                    }

                    window.location.assign(modelUrl.href);
                },
                () => {},
            );
            cameraStarting = false;
            cameraStarted = true;

            if (attempt !== scanAttempt || !dialog.open) {
                await stopCamera(currentScanner);
                openButton.disabled = false;

                return;
            }

            setStatus('Arahkan kamera ke QR pada kemasan produk.');
        } catch (error) {
            if (attempt === scanAttempt) {
                setStatus('Kamera tidak dapat dibuka. Periksa izin kamera dan gunakan koneksi HTTPS.');
                console.error('Gagal memulai pemindai QR.', error);
            }

            cameraStarting = false;
            openButton.disabled = false;

            try {
                await stopCamera(currentScanner);
            } catch (cleanupError) {
                console.error('Pemindai QR gagal dihentikan setelah kamera tidak dapat dibuka.', cleanupError);
            }
        }
    };

    openButton.addEventListener('click', () => {
        if (!dialog.open) {
            dialog.showModal();
        }

        void startCamera();
    });

    closeButton.addEventListener('click', () => {
        dialog.close();
    });

    dialog.addEventListener('close', () => {
        scanAttempt += 1;
        navigating = false;
        openButton.disabled = cameraStarting;

        if (cameraStarted) {
            void stopCamera().catch((error) => {
                setStatus('Kamera gagal dihentikan. Tutup tab ini untuk melepas akses kamera.');
                console.error('Gagal menghentikan pemindai QR.', error);
            }).finally(() => {
                openButton.disabled = false;
            });
        }
    });
}
