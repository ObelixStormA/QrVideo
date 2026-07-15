<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AR video mavjud emas</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            background: #0f0f12;
            color: #fff;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wrap {
            max-width: 360px;
            text-align: center;
            padding: 32px 24px;
        }

        .icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 12px;
        }

        p {
            font-size: 15px;
            line-height: 1.6;
            color: #b8b8bf;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="icon">⚠️</div>
        <h1>AR video ochilmadi</h1>
        <p>{{ $message }}</p>
    </div>
</body>
</html>
