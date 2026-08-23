<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('images/favicon_ico.png') }}">
    <title>ปิดปรับปรุงระบบ | LiteLearn</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            font-family: 'Noto Sans Thai', sans-serif;
            color: #101114;
        }

        .wrap {
            text-align: center;
            padding: 24px;
        }

        .wrap img {
            height: 200px;
            max-width: 90vw;
            margin: 0 auto 16px;
            display: block;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px;
            letter-spacing: -0.5px;
        }

        p {
            font-size: 15px;
            color: #686b82;
            margin: 0;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <img src="{{ asset('images/error.svg') }}" alt="503">
        <h1>ระบบกำลังปิดปรับปรุงชั่วคราว</h1>
        <p>ขออภัยในความไม่สะดวก เรากำลังปรับปรุงระบบอยู่ โปรดกลับมาใหม่อีกครั้งในภายหลัง</p>
    </div>
</body>

</html>
