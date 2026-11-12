

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

if (document.querySelector('[data-ar-qr-value]')) {
    import('./ar-qr.js');
}

const scannerOpenButton = document.querySelector('[data-ar-open-scanner]');

if (scannerOpenButton instanceof HTMLButtonElement) {
    let scannerModuleLoaded = false;
    let scannerModulePromise;

    scannerOpenButton.addEventListener('click', async () => {
        if (scannerModuleLoaded) {
            return;
        }

        try {
            scannerModulePromise ??= import('./ar-scanner.js')
                .then(() => {
                    scannerModuleLoaded = true;
                })
                .catch((error) => {
                    scannerModulePromise = undefined;
                    throw error;
                });

            await scannerModulePromise;
            scannerOpenButton.click();
        } catch (error) {
            console.error('Modul pemindai QR gagal dimuat.', error);
        }
    });
}

if (document.querySelector('[data-ar-viewer]')) {
    import('./ar-viewer.js');
}

if (document.querySelector('[data-ar-marker-experience]')) {
    import('./ar-marker.js');
}
