<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('images/favicon_ico.png') }}">
    <title>LiteLearn</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Fade up animation */
        .fade-up-enter {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .fade-up-enter-active {
            opacity: 1;
            transform: translateY(0);
        }

        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }
    </style>
</head>

<body class="bg-[#055EB2] font-sans antialiased text-[#101114]">

    <div class="min-h-screen flex flex-col relative z-10">
        <main class="flex-1">
            <!-- Hero Section (Copy + Live Leaderboard Podium) -->
            <section class="relative z-10 min-h-screen lg:min-h-[115vh] flex items-center py-16 overflow-hidden bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/backgrounds.png') }}')">

                <!-- Content Container -->
                <div class="max-w-7xl mx-auto px-8 sm:px-12 lg:px-16 observer-target relative z-10 w-full lg:-translate-y-[7.5vh]">

                    <div class="py-8 max-w-3xl mx-auto text-center">

                        <img src="{{ asset('images/LiteLearn_Text.png') }}" alt="LiteLearn" class="fade-up-enter delay-100 animate-float-gentle mt-6 w-96 sm:w-[32rem] md:w-[38rem] mx-auto">

                        <p class="fade-up-enter delay-200 mt-6 max-w-xl mx-auto text-xl text-[#101114] leading-relaxed" style="font-family: 'Google Sans', sans-serif;">
                            ทุกงานที่ส่ง ทุกคอมเมนต์ที่ตอบ แปลงเป็น XP และเหรียญให้ทันที
                            พร้อมกระดานผู้นำที่อัปเดตสด ให้ห้องเรียนสนุกแบบเกมจริง ๆ
                        </p>

                        <div class="fade-up-enter delay-300 mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="{{ route('register') }}" class="btn-3d btn-3d--amber rounded-2xl px-8 py-4 text-base w-full sm:w-64 flex items-center justify-center">
                                เริ่มสร้างห้องเรียนฟรี <x-icon name="rocket-launch" class="h-4 w-4 ml-2" />
                            </a>
                            <a href="{{ route('login') }}" class="btn-3d btn-3d--white rounded-2xl px-8 py-4 text-base w-full sm:w-64 flex items-center justify-center">
                                เข้าสู่ระบบ <x-icon name="arrow-left-on-rectangle" class="h-4 w-4 ml-2" />
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Feature Highlights -->
            <section class="relative z-10 py-16 md:py-24 overflow-hidden bg-[#055EB2]">
                <div class="max-w-7xl mx-auto px-8 sm:px-12 lg:px-16 space-y-20 md:space-y-28">

                    <!-- Feature: Leaderboard -->
                    <div class="observer-target grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <div class="fade-up-enter flex justify-center">
                            <img src="{{ asset('images/features_trophy.png') }}" alt="ระบบจัดอันดับผู้เรียน" class="w-full max-w-sm lg:max-w-md">
                        </div>
                        <div class="fade-up-enter delay-100 text-center lg:text-left">
                            <span class="inline-flex items-center gap-2 rounded-full border border-[#dedee5] bg-white px-4 py-1.5 text-xs font-bold text-[var(--ll-blue-dark)] shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                                <x-icon name="trophy" class="h-3.5 w-3.5" /> ระบบจัดอันดับ
                            </span>
                            <h2 class="mt-4 text-3xl md:text-4xl font-extrabold text-white leading-tight" style="letter-spacing: -0.5px; font-family: 'Noto Sans Thai', sans-serif;">
                                แข่งกันขึ้นโพเดียม เห็นอันดับได้ทุกวินาที
                            </h2>
                            <p class="mt-4 text-lg text-white/80 leading-relaxed" style="font-family: 'Google Sans', sans-serif;">
                                ทุกงานที่ส่งและทุกคอมเมนต์ที่ตอบ แปลงเป็น XP และเหรียญทันที
                                กระดานผู้นำอัปเดตแบบเรียลไทม์ ให้นักเรียนอยากกลับมาเช็กอันดับของตัวเองอยู่เสมอ
                            </p>
                        </div>
                    </div>

                    <!-- Feature: Themes -->
                    <div class="observer-target grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <div class="fade-up-enter flex justify-center lg:order-2">
                            <img src="{{ asset('images/features_theme.png') }}" alt="ธีมดาวเคราะห์สำหรับแต่งห้องเรียน" class="w-full max-w-sm lg:max-w-md">
                        </div>
                        <div class="fade-up-enter delay-100 text-center lg:text-left lg:order-1">
                            <span class="inline-flex items-center gap-2 rounded-full border border-[#dedee5] bg-white px-4 py-1.5 text-xs font-bold text-[var(--ll-blue-dark)] shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                                <x-icon name="sparkles" class="h-3.5 w-3.5" /> ธีมห้องเรียน
                            </span>
                            <h2 class="mt-4 text-3xl md:text-4xl font-extrabold text-white leading-tight" style="letter-spacing: -0.5px; font-family: 'Noto Sans Thai', sans-serif;">
                                ให้ครูแต่งห้องเรียนด้วยธีมดาวเคราะห์หลากสไตล์
                            </h2>
                            <p class="mt-4 text-lg text-white/80 leading-relaxed" style="font-family: 'Google Sans', sans-serif;">
                                ครูเลือกธีมดาวเคราะห์และสีประจำห้องเรียนได้เอง จากหน้าตั้งค่าห้องเรียน
                                ให้แต่ละห้องมีสไตล์และบรรยากาศที่ไม่ซ้ำใคร เปลี่ยนได้ทุกเมื่อที่ต้องการ
                            </p>
                        </div>
                    </div>

                    <!-- Feature: Coins -->
                    <div class="observer-target grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <div class="fade-up-enter flex justify-center">
                            <img src="{{ asset('images/features_coinie.png') }}" alt="แหล่งที่มาของเหรียญ" class="w-full max-w-sm lg:max-w-md">
                        </div>
                        <div class="fade-up-enter delay-100 text-center lg:text-left">
                            <span class="inline-flex items-center gap-2 rounded-full border border-[#dedee5] bg-white px-4 py-1.5 text-xs font-bold text-[var(--ll-blue-dark)] shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                                <x-icon name="sparkles" class="h-3.5 w-3.5" /> แหล่งที่มาของเหรียญ
                            </span>
                            <h2 class="mt-4 text-3xl md:text-4xl font-extrabold text-white leading-tight" style="letter-spacing: -0.5px; font-family: 'Noto Sans Thai', sans-serif;">
                                ได้เหรียญจากทุกความขยัน ไม่ใช่แค่ส่งงาน
                            </h2>
                            <p class="mt-4 text-lg text-white/80 leading-relaxed" style="font-family: 'Google Sans', sans-serif;">
                                ส่งงานตรงเวลา เข้าเรียนครบ ปลดล็อกความสำเร็จ หรือร่วมกิจกรรมในห้องเรียน
                                ทุกอย่างแปลงเป็นเหรียญให้ทันที สะสมไว้แลกของแต่งโปรไฟล์ได้เลย
                            </p>
                        </div>
                    </div>

                </div>
            </section>

            </main>
        
        <footer class="bg-[#101114] pt-20 md:pt-24 pb-8 relative overflow-hidden">
            <!-- SVG Wave Curve Transition from blue features to dark footer -->
            <div class="absolute top-0 left-0 w-full overflow-hidden leading-0 z-0">
                <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-15 md:h-20" style="fill: #055EB2;">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"></path>
                </svg>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-8 mb-12">
                    <!-- Brand -->
                    <div class="col-span-1 md:col-span-1">
                        <div class="flex items-center mb-6 relative">
                            <img src="{{ asset('images/favicon_full.png') }}" alt="LiteLearn" class="h-9 w-auto object-contain">
                        </div>
                        <p class="text-[#9497a9] text-sm leading-relaxed max-w-sm">
                            ยกระดับการจัดการห้องเรียนให้ง่ายและสนุกยิ่งขึ้น
                            ด้วยเครื่องมือที่ตอบโจทย์ทั้งผู้สอนและผู้เรียน
                        </p>
                        <div class="flex gap-4 mt-6">
                            <a href="#" class="w-10 h-10 rounded-xl bg-[#1a1b23] border border-[#2a2b36] flex items-center justify-center text-[#9497a9] hover:border-[var(--ll-blue)] hover:text-[var(--ll-blue)] transition-all">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-xl bg-[#1a1b23] border border-[#2a2b36] flex items-center justify-center text-[#9497a9] hover:border-[var(--ll-blue)] hover:text-[var(--ll-blue)] transition-all">
                                <i class="fa-brands fa-twitter"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-xl bg-[#1a1b23] border border-[#2a2b36] flex items-center justify-center text-[#9497a9] hover:border-[var(--ll-blue)] hover:text-[var(--ll-blue)] transition-all">
                                <i class="fa-brands fa-discord"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Links section 1 -->
                    <div class="col-span-1">
                        <h4 class="text-white font-semibold mb-6 uppercase tracking-wider text-sm">ส่วนสำคัญ</h4>
                        <ul class="space-y-4">
                            <li><a href="{{ route('register') }}" class="text-[#9497a9] hover:text-white transition-colors text-sm">สมัครสมาชิกฟรี</a></li>
                        </ul>
                    </div>

                    <!-- Links section 2 -->
                    <div class="col-span-1">
                        <h4 class="text-white font-semibold mb-6 uppercase tracking-wider text-sm">ความช่วยเหลือ</h4>
                        <ul class="space-y-4">
                            <li><a href="#" class="text-[#9497a9] hover:text-white transition-colors text-sm">ศูนย์ช่วยเหลือ (Help Center)</a></li>
                            <li><a href="#" class="text-[#9497a9] hover:text-white transition-colors text-sm">ติดต่อทีมงาน</a></li>
                            <li><a href="#" class="text-[#9497a9] hover:text-white transition-colors text-sm">เงื่อนไขการให้บริการ (ToS)</a></li>
                            <li><a href="#" class="text-[#9497a9] hover:text-white transition-colors text-sm">นโยบายความเป็นส่วนตัว (Privacy)</a></li>
                        </ul>
                    </div>
                </div>

                <div class="pt-8 border-t border-[#2a2b36] flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-[#9497a9] font-medium">
                        &copy; {{ now()->year }} LiteLearn. All rights reserved.
                    </p>
                    <div class="flex items-center gap-2 text-sm text-[#9497a9]">
                        <span>Made with</span>
                        <x-icon name="heart" class="h-4 w-4 text-[#e11d48]" />
                        <span>in Thailand</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Intersection Observer for scroll animations
        document.addEventListener('DOMContentLoaded', () => {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const targets = entry.target.querySelectorAll('.fade-up-enter');
                        targets.forEach(el => {
                            el.classList.add('fade-up-enter-active');
                        });

                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.observer-target').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>