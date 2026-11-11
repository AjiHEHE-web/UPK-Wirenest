if (window.AFRAME && !window.AFRAME.components['ar-model-fit']) {
    window.AFRAME.registerComponent('ar-model-fit', {
        init() {
            this.el.addEventListener('model-loaded', () => {
                const object = this.el.getObject3D('mesh');

                if (!object) {
                    return;
                }

                const box = new window.AFRAME.THREE.Box3().setFromObject(object);
                const size = box.getSize(new window.AFRAME.THREE.Vector3());
                const center = box.getCenter(new window.AFRAME.THREE.Vector3());
                const longestSide = Math.max(size.x, size.y, size.z);

                if (longestSide <= 0) {
                    this.el.emit('ar-model-error');

                    return;
                }

                const scale = 0.45 / longestSide;
                object.scale.setScalar(scale);
                object.position.set(-center.x * scale, -box.min.y * scale, -center.z * scale);
                this.el.emit('ar-model-ready');
            });

            this.el.addEventListener('model-error', () => this.el.emit('ar-model-error'));
        },
    });
}

document.querySelectorAll('[data-ar-marker-experience]').forEach((experience) => {
    const startButton = document.querySelector('[data-ar-marker-start]');
    const stopButton = experience.querySelector('[data-ar-marker-stop]');
    const stage = experience.querySelector('[data-ar-marker-stage]');
    const status = experience.querySelector('[data-ar-marker-status]');
    const modelUrl = experience.dataset.modelUrl;

    if (
        !(startButton instanceof HTMLButtonElement)
        || !(stopButton instanceof HTMLButtonElement)
        || !stage
        || !status
        || !modelUrl
        || !window.AFRAME
    ) {
        if (status) {
            status.textContent = 'Fitur AR marker gagal dimuat. Muat ulang halaman dan coba lagi.';
        }

        return;
    }

    let scene;

    const stopCamera = () => {
        const video = scene?.querySelector('video');

        video?.srcObject?.getTracks().forEach((track) => track.stop());
        scene?.pause();
        scene?.remove();
        scene = null;
        stage.replaceChildren();
        stage.hidden = true;
        startButton.hidden = false;
        stopButton.hidden = true;
        status.textContent = 'Kamera dimatikan. Tekan “Lihat pada marker” untuk memulai lagi.';
    };

    startButton.addEventListener('click', () => {
        if (!window.isSecureContext) {
            status.textContent = 'Kamera hanya dapat digunakan melalui HTTPS atau localhost.';

            return;
        }

        if (scene) {
            return;
        }

        stage.hidden = false;
        startButton.hidden = true;
        stopButton.hidden = false;
        status.textContent = 'Meminta izin kamera dan memuat model 3D...';

        scene = document.createElement('a-scene');
        scene.setAttribute('embedded', '');
        scene.setAttribute('vr-mode-ui', 'enabled: false');
        scene.setAttribute('renderer', 'logarithmicDepthBuffer: true; colorManagement: true');
        scene.setAttribute('arjs', 'trackingMethod: best; sourceType: webcam; debugUIEnabled: false;');
        scene.style.width = '100%';
        scene.style.height = '100%';

        const marker = document.createElement('a-marker');
        marker.setAttribute('preset', 'hiro');
        marker.setAttribute('size', '0.15');
        marker.setAttribute('emitevents', 'true');
        marker.setAttribute('smooth', 'true');
        marker.setAttribute('smooth-count', '5');

        const model = document.createElement('a-entity');
        model.setAttribute('gltf-model', `url(${modelUrl})`);
        model.setAttribute('ar-model-fit', '');
        marker.append(model);

        const camera = document.createElement('a-entity');
        camera.setAttribute('camera', '');

        marker.addEventListener('markerFound', () => {
            status.textContent = 'Marker terdeteksi. Model mengikuti marker.';
        });
        marker.addEventListener('markerLost', () => {
            status.textContent = 'Marker tidak terlihat. Arahkan kamera kembali ke marker.';
        });
        model.addEventListener('ar-model-ready', () => {
            status.textContent = 'Model siap. Arahkan kamera ke marker Hiro yang sudah dicetak.';
        });
        model.addEventListener('ar-model-error', () => {
            status.textContent = 'Model gagal dimuat. Pastikan berkas GLB dapat diakses lalu coba lagi.';
        });
        scene.addEventListener('camera-error', () => {
            status.textContent = 'Kamera tidak dapat dibuka. Periksa izin kamera dan gunakan HTTPS.';
        });

        scene.append(marker, camera);
        stage.append(scene);
    });

    stopButton.addEventListener('click', stopCamera);
    window.addEventListener('pagehide', () => {
        const video = scene?.querySelector('video');
        video?.srcObject?.getTracks().forEach((track) => track.stop());
    }, { once: true });
});
