<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <title>ไม่มีสิทธิ์เข้าถึง | LiteLearning</title>
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
            margin: 0 0 28px;
            line-height: 1.6;
        }

        a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #3293F5;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            padding: 10px 28px;
            border-radius: 9999px;
            transition: background .15s;
        }

        a:hover {
            background: #1d7fe0;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <img src="{{ asset('images/error.svg') }}" alt="403">
        <h1>คุณไม่มีสิทธิ์เข้าถึงหน้านี้</h1>
        <p>บัญชีของคุณไม่ได้รับอนุญาตให้เข้าถึงหน้านี้ หากคุณคิดว่านี่เป็นความผิดพลาด โปรดติดต่อผู้ดูแลระบบ</p>
        <a href="{{ url('/') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
            กลับหน้าหลัก
        </a>
    </div>
</body>

</html>
