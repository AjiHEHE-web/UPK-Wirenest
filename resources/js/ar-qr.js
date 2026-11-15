import QRCode from 'qrcode';

const renderQrCodes = (root = document) => {
    root.querySelectorAll('[data-ar-qr-value]').forEach(async (qrContainer) => {
        if (qrContainer.dataset.qrInitialized === 'true') {
            return;
        }

        qrContainer.dataset.qrInitialized = 'true';
        const canvas = qrContainer.querySelector('[data-ar-qr-canvas]');
        const downloadButton = qrContainer.querySelector('[data-ar-qr-download]');
        const status = qrContainer.querySelector('[data-ar-qr-status]');
        const qrValue = qrContainer.dataset.arQrValue;

        if (!(canvas instanceof HTMLCanvasElement) || !(downloadButton instanceof HTMLButtonElement) || !status || !qrValue) {
            return;
        }

        try {
            await QRCode.toCanvas(canvas, qrValue, {
                width: 144,
                margin: 1,
                color: {
                    dark: '#173f34',
                    light: '#ffffff',
                },
            });

            downloadButton.disabled = false;
            downloadButton.addEventListener('click', () => {
                const link = document.createElement('a');
                link.download = `qr-${qrContainer.dataset.arModelId || 'model-ar'}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        } catch (error) {
            status.textContent = 'QR gagal dibuat. Muat ulang halaman untuk mencoba kembali.';
            console.error('Gagal membuat QR model AR.', error);
        }
    });
};

renderQrCodes();
document.addEventListener('ajax:content-updated', (event) => {
    renderQrCodes(event.detail?.root || document);
});
