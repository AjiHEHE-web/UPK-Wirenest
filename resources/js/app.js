

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

if (document.querySelector('[data-ar-qr-value]')) {
    import('./ar-qr.js');
}

if (document.querySelector('[data-ar-open-scanner]')) {
    import('./ar-scanner.js');
}

if (document.querySelector('[data-ar-viewer]')) {
    import('./ar-viewer.js');
}

if (document.querySelector('[data-ar-marker-experience]')) {
    import('./ar-marker.js');
}
