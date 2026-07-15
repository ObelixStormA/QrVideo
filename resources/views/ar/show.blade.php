<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{ $video->title }} — AR video</title>
    <script src="https://aframe.io/releases/1.5.0/aframe.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mind-ar@1.2.5/dist/mindar-image-aframe.prod.js"></script>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #000;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        a-scene {
            width: 100%;
            height: 100%;
        }

        .ar-overlay {
            position: fixed;
            inset: 0;
            z-index: 10;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 32px 24px 96px;
            pointer-events: none;
            transition: opacity .25s ease;
        }

        .ar-overlay.is-hidden {
            opacity: 0;
        }

        .ar-hint {
            background: rgba(0, 0, 0, .6);
            color: #fff;
            text-align: center;
            font-size: 15px;
            line-height: 1.5;
            max-width: 320px;
            padding: 14px 20px;
            border-radius: 14px;
        }

        .ar-sound-btn {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 20;
            display: none;
            pointer-events: auto;
            background: #fff;
            color: #111;
            border: none;
            border-radius: 999px;
            padding: 12px 22px;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .35);
        }
    </style>
</head>
<body>
    <div id="ar-overlay" class="ar-overlay">
        <div id="ar-hint" class="ar-hint">Yuklanmoqda...</div>
    </div>

    <button id="ar-sound-btn" class="ar-sound-btn" type="button">🔇 Ovozni yoqish</button>

    <a-scene
        mindar-image="imageTargetSrc: {{ $mindUrl }}; autoStart: true; uiScanning: false; uiLoading: false; uiError: false;"
        color-space="sRGB"
        renderer="colorManagement: true"
        vr-mode-ui="enabled: false"
        device-orientation-permission-ui="enabled: false"
        embedded
    >
        <a-assets>
            <video
                id="ar-video"
                src="{{ $videoUrl }}"
                preload="auto"
                loop
                muted
                playsinline
                webkit-playsinline
                crossorigin="anonymous"
            ></video>
        </a-assets>

        <a-camera position="0 0 0" look-controls="enabled: false"></a-camera>

        <a-entity id="ar-target" mindar-image-target="targetIndex: 0">
            <a-video src="#ar-video" width="1" height="{{ $ratio }}" position="0 0 0"></a-video>
        </a-entity>
    </a-scene>

    <script>
        const sceneEl = document.querySelector('a-scene');
        const targetEl = document.querySelector('#ar-target');
        const videoEl = document.querySelector('#ar-video');
        const overlayEl = document.querySelector('#ar-overlay');
        const hintEl = document.querySelector('#ar-hint');
        const soundBtn = document.querySelector('#ar-sound-btn');

        sceneEl.addEventListener('arReady', () => {
            hintEl.textContent = '📷 Kamerani jurnaldagi rasmga qarating';
            soundBtn.style.display = 'block';
        });

        sceneEl.addEventListener('arError', () => {
            hintEl.textContent = "Kameraga ruxsat berilmadi yoki qurilma qo'llab-quvvatlamaydi.";
        });

        targetEl.addEventListener('targetFound', () => {
            overlayEl.classList.add('is-hidden');
            videoEl.play().catch(() => {});
        });

        targetEl.addEventListener('targetLost', () => {
            overlayEl.classList.remove('is-hidden');
            videoEl.pause();
        });

        soundBtn.addEventListener('click', () => {
            videoEl.muted = false;
            videoEl.play().catch(() => {});
            soundBtn.style.display = 'none';
        });
    </script>
</body>
</html>
