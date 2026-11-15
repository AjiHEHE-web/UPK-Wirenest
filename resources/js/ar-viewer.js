import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

const loadWith = (loader, url, mapResult = (result) => result) => new Promise((resolve, reject) => {
    const onError = (error) => reject(error instanceof Error ? error : new Error('Berkas model tidak dapat dibaca.'));
    const onProgress = (event) => {
        if (event.total > 0) {
            const percentage = Math.round((event.loaded / event.total) * 100);
            const loading = document.querySelector('[data-ar-loading]');

            if (loading) {
                loading.textContent = `Memuat model 3D... ${percentage}%`;
            }
        }
    };

    loader.load(url, (result) => resolve(mapResult(result)), onProgress, onError);
});

const loaderFor = async (format, url) => {
    switch (format) {
        case 'glb':
        case 'gltf': {
            const [gltfModule, dracoModule] = await Promise.all([
                import('three/addons/loaders/GLTFLoader.js'),
                import('three/addons/loaders/DRACOLoader.js'),
            ]);
            const dracoLoader = new dracoModule.DRACOLoader();
            dracoLoader.setDecoderPath(dracoModule.DRACO_GLTF_CONFIG);

            const gltfLoader = new gltfModule.GLTFLoader();
            gltfLoader.setDRACOLoader(dracoLoader);

            return loadWith(gltfLoader, url, (result) => result.scene);
        }
        case 'obj': {
            const { OBJLoader } = await import('three/addons/loaders/OBJLoader.js');

            return loadWith(new OBJLoader(), url);
        }
        case 'fbx': {
            const { FBXLoader } = await import('three/addons/loaders/FBXLoader.js');

            return loadWith(new FBXLoader(), url);
        }
        case 'stl':
        {
            const { STLLoader } = await import('three/addons/loaders/STLLoader.js');
            const geometry = await loadWith(new STLLoader(), url);

            return new THREE.Mesh(geometry, new THREE.MeshStandardMaterial({ color: 0xb9a37a, metalness: 0.08, roughness: 0.75 }));
        }
        case 'dae': {
            const { ColladaLoader } = await import('three/addons/loaders/ColladaLoader.js');

            return loadWith(new ColladaLoader(), url, (result) => result.scene);
        }
        case 'usdz': {
            const { USDLoader } = await import('three/addons/loaders/USDLoader.js');

            return loadWith(new USDLoader(), url);
        }
        default:
            throw new Error('Format model 3D tidak didukung.');
    }
};

document.querySelectorAll('[data-ar-viewer]').forEach(async (viewer) => {
    const stage = viewer.querySelector('[data-ar-stage]');
    const loading = viewer.querySelector('[data-ar-loading]');
    const errorMessage = viewer.querySelector('[data-ar-error]');
    const capability = viewer.querySelector('[data-ar-capability]');
    const enterArButton = viewer.querySelector('[data-ar-enter]');

    if (!stage || !loading || !errorMessage || !capability || !(enterArButton instanceof HTMLButtonElement)) {
        return;
    }

    let renderer;
    let modelContainer;
    let controls;

    try {
        const model = await loaderFor(viewer.dataset.modelFormat, viewer.dataset.modelUrl);
        const bounds = new THREE.Box3().setFromObject(model);

        if (bounds.isEmpty()) {
            throw new Error('Berkas model tidak berisi objek 3D yang dapat ditampilkan.');
        }

        model.traverse((object) => {
            if (object instanceof THREE.Mesh) {
                object.castShadow = true;
                object.receiveShadow = true;
            }
        });

        const center = bounds.getCenter(new THREE.Vector3());
        const size = bounds.getSize(new THREE.Vector3());
        const longestSide = Math.max(size.x, size.y, size.z);

        if (longestSide === 0) {
            throw new Error('Berkas model tidak memiliki ukuran 3D yang dapat ditampilkan.');
        }

        model.position.sub(center);
        model.scale.setScalar(2.2 / longestSide);

        modelContainer = new THREE.Group();
        modelContainer.add(model);

        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0xf3f1e8);
        scene.add(modelContainer);
        scene.add(new THREE.HemisphereLight(0xffffff, 0x77705e, 2.2));

        const keyLight = new THREE.DirectionalLight(0xffffff, 2.4);
        keyLight.position.set(3, 5, 4);
        scene.add(keyLight);

        const camera = new THREE.PerspectiveCamera(42, 1, 0.01, 100);
        camera.position.set(0, 0.25, 3.8);

        renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
        renderer.setSize(stage.clientWidth, stage.clientHeight);
        renderer.xr.enabled = true;
        renderer.domElement.setAttribute('role', 'img');
        renderer.domElement.setAttribute('aria-label', `${viewer.dataset.modelFormat.toUpperCase()} model 3D interaktif. Geser untuk memutar dan cubit untuk memperbesar.`);
        renderer.domElement.className = 'absolute inset-0 h-full w-full touch-none';
        stage.append(renderer.domElement);

        controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.target.set(0, 0, 0);
        controls.update();

        const resize = () => {
            const width = stage.clientWidth;
            const height = stage.clientHeight;

            camera.aspect = width / height;
            camera.updateProjectionMatrix();
            renderer.setSize(width, height);
        };

        const observer = new ResizeObserver(resize);
        observer.observe(stage);
        window.addEventListener('beforeunload', () => {
            observer.disconnect();
            controls.dispose();
            renderer.dispose();
        }, { once: true });

        renderer.xr.addEventListener('sessionstart', () => {
            scene.background = null;
            renderer.setClearColor(0x000000, 0);
            modelContainer.position.set(0, -0.75, -1.5);
            controls.enabled = false;
        });

        renderer.xr.addEventListener('sessionend', () => {
            scene.background = new THREE.Color(0xf3f1e8);
            renderer.setClearColor(0xf3f1e8, 1);
            modelContainer.position.set(0, 0, 0);
            controls.enabled = true;
        });

        renderer.setAnimationLoop(() => {
            controls.update();
            renderer.render(scene, camera);
        });

        enterArButton.addEventListener('click', async () => {
            if (!navigator.xr) {
                return;
            }

            enterArButton.disabled = true;

            try {
                const session = await navigator.xr.requestSession('immersive-ar', {
                    optionalFeatures: ['local-floor', 'hit-test'],
                });
                await renderer.xr.setSession(session);
            } catch (error) {
                capability.textContent = 'Mode AR gagal dimulai. Periksa izin kamera dan dukungan perangkat.';
                console.error('Gagal memulai sesi AR.', error);
            } finally {
                enterArButton.disabled = renderer.xr.isPresenting;
            }
        });

        if (!window.isSecureContext || !navigator.xr) {
            capability.textContent = 'Model 3D dapat diputar di sini. Mode AR memerlukan koneksi HTTPS dan perangkat yang mendukung WebXR.';
        } else {
            try {
                const isSupported = await navigator.xr.isSessionSupported('immersive-ar');
                enterArButton.disabled = !isSupported;
                capability.textContent = isSupported
                    ? 'Putar model, atau buka kamera AR pada perangkat yang mendukung.'
                    : 'Model 3D dapat diputar di sini. Mode AR belum didukung pada perangkat ini.';
            } catch (error) {
                capability.textContent = 'Model 3D dapat diputar di sini. Dukungan AR tidak dapat diperiksa pada perangkat ini.';
                console.error('Gagal memeriksa dukungan AR.', error);
            }
        }

        loading.remove();
    } catch (error) {
        loading.remove();
        errorMessage.hidden = false;
        errorMessage.textContent = error instanceof Error
            ? `Model 3D tidak dapat ditampilkan: ${error.message}`
            : 'Model 3D tidak dapat ditampilkan. Periksa berkas lalu coba lagi.';
        console.error('Gagal menampilkan model AR.', error);
    }
});
