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

        .ar-debug {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 999;
            background: rgba(0, 255, 0, .15);
            color: #0f0;
            font-family: monospace;
            font-size: 11px;
            line-height: 1.4;
            padding: 6px 8px;
            white-space: pre-wrap;
            pointer-events: none;
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
    <div id="ar-debug" class="ar-debug">debug...</div>

    <div id="ar-overlay" class="ar-overlay">
        <div id="ar-hint" class="ar-hint">Yuklanmoqda...</div>
    </div>

    <button id="ar-sound-btn" class="ar-sound-btn" type="button">🔇 Ovozni yoqish</button>

    <a-scene
        mindar-image="imageTargetSrc: {{ $mindUrl }}; autoStart: true; uiScanning: false; uiLoading: false; uiError: false; warmupTolerance: 2; missTolerance: 15;"
        color-space="sRGB"
        renderer="colorManagement: true; alpha: true"
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

        sceneEl.addEventListener('renderstart', () => {
            sceneEl.renderer.setClearColor(0x000000, 0);
            sceneEl.object3D.background = null;
        });

        const debugEl = document.querySelector('#ar-debug');
        setInterval(() => {
            const canvas = document.querySelector('a-scene canvas.a-canvas');
            const lines = [];
            const px = Math.floor(window.innerWidth / 2);
            const py = 130;

            if (canvas) {
                const gl = canvas.getContext('webgl2') || canvas.getContext('webgl');
                if (gl) {
                    const rect = canvas.getBoundingClientRect();
                    const dpx = Math.round((px - rect.left) * canvas.width / rect.width);
                    const dpy = Math.round(canvas.height - (py - rect.top) * canvas.height / rect.height);
                    const pixel = new Uint8Array(4);
                    try {
                        gl.readPixels(dpx, dpy, 1, 1, gl.RGBA, gl.UNSIGNED_BYTE, pixel);
                        lines.push('canvas WxH=' + canvas.width + 'x' + canvas.height + ' rect=' + Math.round(rect.width) + 'x' + Math.round(rect.height));
                        lines.push('gl pixel@(' + dpx + ',' + dpy + '): rgba(' + pixel[0] + ',' + pixel[1] + ',' + pixel[2] + ',' + pixel[3] + ')');
                    } catch (e) {
                        lines.push('readPixels error: ' + e.message);
                    }
                } else {
                    lines.push('no gl context');
                }
            } else {
                lines.push('no canvas');
            }

            const bodyBg = getComputedStyle(document.body).backgroundColor;
            lines.push('body bg: ' + bodyBg);
            debugEl.textContent = lines.join('\n');
        }, 1000);

        let arReadyFired = false;

        const readyTimeout = setTimeout(() => {
            if (!arReadyFired) {
                hintEl.textContent = "Kamera ochilmayapti. Sahifani https:// orqali oching, kameraga ruxsat bering va qayta urinib ko'ring.";
            }
        }, 12000);

        sceneEl.addEventListener('arReady', () => {
            arReadyFired = true;
            clearTimeout(readyTimeout);
            hintEl.textContent = '📷 Kamerani jurnaldagi rasmga qarating';
            soundBtn.style.display = 'block';
        });

        sceneEl.addEventListener('arError', () => {
            clearTimeout(readyTimeout);
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
