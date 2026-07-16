<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} — AR taklifnoma</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "DejaVu Sans", sans-serif;
            color: #1f2937;
        }

        .frame {
            position: absolute;
            top: 10mm;
            left: 10mm;
            right: 10mm;
            bottom: 10mm;
            border: 1.5pt solid #c9a24b;
            padding: 4mm;
        }

        .frame-inner {
            position: relative;
            width: 100%;
            height: 100%;
            border: 0.75pt solid #c9a24b;
        }

        .photo-wrap {
            position: absolute;
            top: 4mm;
            left: 4mm;
            right: 4mm;
            height: 130mm;
            overflow: hidden;
        }

        .photo-wrap img {
            width: 100%;
            height: 130mm;
            object-fit: cover;
        }

        .title {
            position: absolute;
            top: 138mm;
            left: 4mm;
            right: 4mm;
            text-align: center;
            font-size: 15pt;
            font-weight: bold;
            color: #3b2f14;
        }

        .subtitle {
            position: absolute;
            top: 148mm;
            left: 8mm;
            right: 8mm;
            text-align: center;
            font-size: 9pt;
            line-height: 1.5;
            color: #4b5563;
        }

        .qr-box {
            position: absolute;
            top: 166mm;
            left: 50%;
            margin-left: -18mm;
            width: 36mm;
            text-align: center;
        }

        .qr-box img {
            width: 30mm;
            height: 30mm;
            border: 1pt solid #c9a24b;
            padding: 1.5mm;
            background: #fff;
        }

        .qr-caption {
            margin-top: 2mm;
            font-size: 8pt;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="frame">
        <div class="frame-inner">
            <div class="photo-wrap">
                <img src="{{ $imageSrc }}" alt="{{ $title }}">
            </div>

            <div class="title">{{ $title }}</div>

            <div class="subtitle">
                Video-xotirani ko'rish uchun telefon kamerasini pastdagi QR kodga qarating,<br>
                so'ng kamerani ushbu rasmga yo'naltiring — video shu yerda jonlanadi.
            </div>

            <div class="qr-box">
                <img src="{{ $qrSrc }}" alt="QR kod">
                <div class="qr-caption">📷 Skanerlang</div>
            </div>
        </div>
    </div>
</body>
</html>
