<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'เข้าสู่ระบบ' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="space-bg font-sans antialiased auth-page">
    <canvas id="starfield-guest" style="position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0;opacity:0.4;"></canvas>
    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8 auth-page-shell relative z-10">
        <!-- Content -->
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts

    <script>
    (function() {
        const canvas = document.getElementById('starfield-guest');
        const ctx = canvas.getContext('2d');
        let stars = [];
        function resize() { canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
        function init() {
            stars = [];
            for (let i = 0; i < 180; i++) {
                stars.push({ x: Math.random() * canvas.width, y: Math.random() * canvas.height, r: Math.random() * 1.5 + 0.3, o: Math.random(), d: (Math.random() * 0.005 + 0.001) * (Math.random() > 0.5 ? 1 : -1) });
            }
        }
        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            stars.forEach(s => {
                s.o += s.d;
                if (s.o > 1 || s.o < 0.1) s.d *= -1;
                ctx.beginPath();
                ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(180,160,255,${s.o})`;
                ctx.fill();
            });
            requestAnimationFrame(draw);
        }
        window.addEventListener('resize', () => { resize(); init(); });
        resize(); init(); draw();
    })();
    </script>
</body>

</html>
