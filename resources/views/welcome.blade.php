<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LiteLearning</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500;700&family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-text {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-image: linear-gradient(90deg, #4f46e5, #9333ea, #4f46e5);
            background-size: 200% auto;
            animation: textGradient 4s linear infinite;
        }

        @keyframes textGradient {
            0% {
                background-position: 0% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            z-index: 0;
            pointer-events: none;
        }

        .hero-bg {
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 32px 32px;
        }

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

        .delay-100 {
            transition-delay: 100ms;
        }

        .delay-200 {
            transition-delay: 200ms;
        }

        .delay-300 {
            transition-delay: 300ms;
        }

        /* Hamburger Menu Icon */
        .hamburger-icon {
            position: relative;
            width: 18px;
            height: 14px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .hamburger-line {
            display: block;
            width: 100%;
            height: 2px;
            background-color: currentColor;
            border-radius: 1px;
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                opacity 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: center;
        }

        .hamburger-icon.is-active .hamburger-line:nth-child(1) {
            transform: translateY(6px) rotate(45deg);
        }

        .hamburger-icon.is-active .hamburger-line:nth-child(2) {
            opacity: 0;
            transform: scaleX(0);
        }

        .hamburger-icon.is-active .hamburger-line:nth-child(3) {
            transform: translateY(-6px) rotate(-45deg);
        }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased relative text-gray-800">

    <!-- Background decorative blobs wrapper to prevent overflow -->
    <div class="absolute top-0 left-0 w-full overflow-hidden min-h-screen pointer-events-none z-0">
        <div class="blob bg-indigo-200 w-96 h-96 -top-20 -left-20 mix-blend-multiply"></div>
        <div class="blob bg-purple-200 w-96 h-96 top-40 -right-20 mix-blend-multiply animation-delay-2000"></div>
    </div>

    <div class="min-h-screen flex flex-col relative z-10">
        <header
            class="sticky top-0 z-50 border-b border-white/20 bg-white/70 backdrop-blur-md shadow-sm transition-all duration-300"
            id="navbar">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                    <span
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                        <i class="fas fa-graduation-cap"></i>
                    </span>
                    <span class="text-xl font-bold text-gray-900 tracking-tight">LiteLearning</span>
                </a>

                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                    <a href="#features" class="hover:text-indigo-600 transition-colors">ฟีเจอร์</a>
                    <a href="#benefits" class="hover:text-indigo-600 transition-colors">ประโยชน์</a>
                    <a href="#pricing" class="hover:text-indigo-600 transition-colors">แผนใช้งาน</a>
                </nav>

                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('login') }}"
                        class="text-sm font-semibold text-indigo-700 hover:text-indigo-500 transition-colors px-4 py-2 rounded-xl border border-indigo-200 hover:bg-indigo-50"><i class="fa-solid fa-chalkboard-user mr-1.5"></i>ครู</a>
                    <a href="{{ route('login') }}"
                        class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors px-4 py-2 rounded-xl border border-gray-200 hover:bg-gray-50"><i class="fa-solid fa-user-graduate mr-1.5"></i>นักเรียน</a>
                    <a href="{{ route('register') }}"
                        class="btn-3d btn-3d--indigo rounded-xl px-5 py-2.5 text-sm">เข้าใช้งานฟรี <i
                            class="fa-solid fa-arrow-right ml-1"></i></a>
                </div>

                <button id="mobile-menu-toggle" type="button" aria-label="Toggle menu"
                    class="inline-flex md:hidden h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 relative focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="hamburger-icon">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </span>
                </button>
            </div>

            <div id="mobile-menu-panel"
                class="md:hidden overflow-hidden max-h-0 opacity-0 -translate-y-1 transition-all duration-300 ease-in-out border-t border-gray-200 bg-white shadow-lg absolute w-full left-0">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <nav class="flex flex-col gap-2 text-base font-medium text-gray-700">
                        <a href="#features" data-menu-close
                            class="rounded-xl px-4 py-3 hover:bg-indigo-50 hover:text-indigo-700 transition-colors"><i
                                class="fa-solid fa-cubes w-6 text-center mr-2 text-indigo-400"></i> ฟีเจอร์</a>
                        <a href="#benefits" data-menu-close
                            class="rounded-xl px-4 py-3 hover:bg-indigo-50 hover:text-indigo-700 transition-colors"><i
                                class="fa-solid fa-lightbulb w-6 text-center mr-2 text-indigo-400"></i> ประโยชน์</a>
                        <a href="#pricing" data-menu-close
                            class="rounded-xl px-4 py-3 hover:bg-indigo-50 hover:text-indigo-700 transition-colors"><i
                                class="fa-solid fa-layer-group w-6 text-center mr-2 text-indigo-400"></i> แผนใช้งาน</a>
                    </nav>

                    <div class="mt-5 flex flex-col gap-3 pt-5 border-t border-gray-100">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-indigo-200 px-4 py-3 text-base font-semibold text-indigo-700 hover:bg-indigo-50 hover:border-indigo-300 transition-all"><i class="fa-solid fa-chalkboard-user"></i> เข้าสู่ระบบ (ครู)</a>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-gray-200 px-4 py-3 text-base font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-all"><i class="fa-solid fa-user-graduate"></i> เข้าสู่ระบบ (นักเรียน)</a>
                        <a href="{{ route('register') }}"
                            class="btn-3d btn-3d--indigo rounded-xl px-4 py-3 text-base text-center">เริ่มต้นใช้งานฟรี</a>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <!-- Hero Section -->
            <section class="hero-bg relative pt-20 pb-24 lg:pt-32 lg:pb-32 overflow-hidden">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center observer-target">
                    <span
                        class="fade-up-enter inline-flex items-center rounded-full border border-indigo-200 bg-white/60 backdrop-blur-sm px-4 py-1.5 text-sm font-bold text-indigo-700 shadow-sm mb-6">
                        <span class="flex h-2 w-2 rounded-full bg-indigo-600 mr-2 animate-pulse"></span>
                        สร้างมาเพื่อห้องเรียนยุคใหม่
                    </span>

                    <h1
                        class="fade-up-enter delay-100 max-w-4xl mx-auto text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-gray-900 leading-tight">
                        จัดการห้องเรียนอย่างโปร
                        <br class="hidden md:block" />
                        ด้วย <span class="gradient-text">ความสนุกที่มากกว่า</span>
                    </h1>

                    <p
                        class="fade-up-enter delay-200 mt-6 max-w-2xl mx-auto text-lg md:text-xl text-gray-600 leading-relaxed">
                        เปลี่ยนห้องเรียนธรรมดาให้เป็นความท้าทาย จัดการงาน มอบหมายคะแนน และใช้ระบบเกมมิฟิเคชัน
                        ทั้งหมดนี้ในแพลตฟอร์มเดียวที่คุณจะหลงรัก
                    </p>

                    <!-- Rocket Image -->
                    <div class="fade-up-enter delay-200 flex justify-center mt-8 mb-2">
                        <img src="{{ asset('images/rocket.png') }}" alt="LiteLearning Rocket" class="w-28 h-28 md:w-36 md:h-36 drop-shadow-xl animate-bounce" style="animation-duration: 3s;">
                    </div>

                    <div
                        class="fade-up-enter delay-300 mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}"
                            class="btn-3d btn-3d--indigo rounded-2xl px-8 py-4 text-base w-full sm:w-auto shadow-xl shadow-indigo-200 transition-transform">
                            เริ่มสร้างห้องเรียนฟรี <i class="fa-solid fa-rocket ml-2"></i>
                        </a>
                        <a href="#features"
                            class="btn-3d btn-3d--white rounded-2xl px-8 py-4 text-base font-bold text-gray-700 w-full sm:w-auto">
                            ดูฟีเจอร์ <i class="fa-solid fa-arrow-down ml-2"></i>
                        </a>
                    </div>

                    <!-- Dashboard Mockup Image / Graphic -->
                    <div
                        class="fade-up-enter delay-300 mt-16 relative max-w-5xl mx-auto rounded-2xl border-4 border-white/50 bg-white/40 p-2 shadow-2xl backdrop-blur-sm transform rotate-1 transition-transform duration-500">
                        <div
                            class="rounded-xl overflow-hidden shadow-inner border border-gray-100 bg-gray-50 flex flex-col">
                            <!-- Mac Window header mock -->
                            <div class="bg-gray-100 px-4 py-3 flex gap-2 border-b border-gray-200">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="p-6 md:p-10 text-left grid md:grid-cols-3 gap-6">
                                <!-- Mock Sidebar -->
                                <div class="hidden md:flex flex-col gap-4 border-r border-gray-200 pr-6">
                                    <div class="h-8 w-3/4 bg-gray-200 rounded-lg animate-pulse"></div>
                                    <div class="h-4 w-1/2 bg-gray-200 rounded animate-pulse mt-4"></div>
                                    <div class="h-4 w-2/3 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-4 w-1/2 bg-gray-200 rounded animate-pulse"></div>
                                </div>
                                <!-- Mock Content -->
                                <div class="md:col-span-2 space-y-6">
                                    <div class="flex justify-between items-center">
                                        <div class="h-6 w-1/3 bg-indigo-100 rounded-lg"></div>
                                        <div class="h-8 w-8 bg-indigo-200 rounded-full"></div>
                                    </div>
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                        <div
                                            class="h-24 bg-white rounded-xl shadow-sm border border-gray-100 p-4 shrink-0 flex flex-col justify-between">
                                            <div class="h-4 w-1/2 bg-gray-100 rounded"></div>
                                            <div class="h-6 w-3/4 bg-indigo-50 rounded"></div>
                                        </div>
                                        <div
                                            class="h-24 bg-white rounded-xl shadow-sm border border-gray-100 p-4 shrink-0 flex flex-col justify-between">
                                            <div class="h-4 w-1/2 bg-gray-100 rounded"></div>
                                            <div class="h-6 w-2/3 bg-green-50 rounded"></div>
                                        </div>
                                        <div
                                            class="h-24 bg-white rounded-xl shadow-sm border border-gray-100 p-4 shrink-0 flex flex-col justify-between sm:flex">
                                            <div class="h-4 w-1/2 bg-gray-100 rounded"></div>
                                            <div class="h-6 w-3/4 bg-amber-50 rounded"></div>
                                        </div>
                                        <div
                                            class="h-24 bg-white rounded-xl shadow-sm border border-gray-100 p-4 shrink-0 flex flex-col justify-between lg:flex">
                                            <div class="h-4 w-1/2 bg-gray-100 rounded"></div>
                                            <div class="h-6 w-1/2 bg-purple-50 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="h-32 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                                        <div class="h-4 w-1/4 bg-gray-100 rounded mb-4"></div>
                                        <div class="space-y-2">
                                            <div class="h-3 w-full bg-gray-50 rounded"></div>
                                            <div class="h-3 w-5/6 bg-gray-50 rounded"></div>
                                            <div class="h-3 w-4/6 bg-gray-50 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6 text-sm divide-x divide-gray-200 border-y border-gray-200 py-8">
                        <div class="px-4">
                            <p class="text-3xl font-extrabold text-indigo-600">10k+</p>
                            <p class="text-gray-500 font-medium mt-1">งานที่ถูกส่ง</p>
                        </div>
                        <div class="px-4">
                            <p class="text-3xl font-extrabold text-indigo-600">500+</p>
                            <p class="text-gray-500 font-medium mt-1">ห้องเรียนที่แอคทีฟ</p>
                        </div>
                        <div class="px-4 hidden md:block">
                            <p class="text-3xl font-extrabold text-indigo-600">4.9/5</p>
                            <p class="text-gray-500 font-medium mt-1">คะแนนความพอใจ</p>
                        </div>
                        <div class="px-4 hidden md:block">
                            <p class="text-3xl font-extrabold text-indigo-600">99.9%</p>
                            <p class="text-gray-500 font-medium mt-1">Uptime สูงสุด</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section id="features" class="bg-white py-20 lg:py-28 relative">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 observer-target">
                    <div class="text-center max-w-3xl mx-auto mb-16 fade-up-enter">
                        <span
                            class="text-sm font-bold tracking-wider text-indigo-600 uppercase bg-indigo-50 px-3 py-1 rounded-lg">ฟีเจอร์เด่น</span>
                        <h2 class="mt-4 text-3xl md:text-4xl font-extrabold text-gray-900">
                            ตัวช่วยที่ทำให้ห้องเรียนของคุณ<br />โดดเด่นไม่เหมือนใคร</h2>
                        <p class="mt-4 text-lg text-gray-600">รวบรวมทุกฟังก์ชันที่จำเป็นพร้อมระบบออกแบบที่สวยงาม
                            ใช้งานง่าย</p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Feature 1 -->
                        <div
                            class="fade-up-enter bg-gray-50 rounded-2xl p-8 hover:bg-white hover:shadow-xl hover:shadow-indigo-100/50 hover:-translate-y-1 transition-all duration-300 border border-transparent hover:border-indigo-100 group">
                            <div
                                class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">จัดการห้องเรียนครบวงจร</h3>
                            <p class="mt-3 text-gray-600 leading-relaxed">สร้างห้อง เชิญนักเรียน
                                คัดกรองสมาชิกด้วยรหัสผ่าน และจัดทุกอย่างให้อยู่ในที่เดียวอย่างเป็นระบบ</p>
                        </div>

                        <!-- Feature 2 -->
                        <div
                            class="fade-up-enter delay-100 bg-gray-50 rounded-2xl p-8 hover:bg-white hover:shadow-xl hover:shadow-indigo-100/50 hover:-translate-y-1 transition-all duration-300 border border-transparent hover:border-indigo-100 group">
                            <div
                                class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform">
                                <i class="fa-solid fa-list-check"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">ระบบสั่งงานอัจฉริยะ</h3>
                            <p class="mt-3 text-gray-600 leading-relaxed">สั่งงาน แนบไฟล์ กำหนดส่ง
                                และตรวจให้คะแนนได้ทันที พร้อมระบบเช็คคนส่งช้าอัตโนมัติ</p>
                        </div>

                        <!-- Feature 3 -->
                        <div
                            class="fade-up-enter delay-200 bg-gray-50 rounded-2xl p-8 hover:bg-white hover:shadow-xl hover:shadow-indigo-100/50 hover:-translate-y-1 transition-all duration-300 border border-transparent hover:border-indigo-100 group">
                            <div
                                class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform">
                                <i class="fa-solid fa-trophy"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">เกมมิฟิเคชัน (Gamification)</h3>
                            <p class="mt-3 text-gray-600 leading-relaxed">กระตุ้นผู้เรียนด้วยระบบ Level, เควสต์สะสม XP
                                และระบบเหรียญรางวัล ทำให้การเรียนไม่น่าเบื่อ</p>
                        </div>

                        <!-- Feature 4 -->
                        <div
                            class="fade-up-enter bg-gray-50 rounded-2xl p-8 hover:bg-white hover:shadow-xl hover:shadow-indigo-100/50 hover:-translate-y-1 transition-all duration-300 border border-transparent hover:border-indigo-100 group">
                            <div
                                class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform">
                                <i class="fa-solid fa-comments"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">กระดานสนทนาแบบเรียลไทม์</h3>
                            <p class="mt-3 text-gray-600 leading-relaxed">มี Stream สำหรับประกาศข่าว
                                และส่วนคอมเมนต์ใต้ชิ้นงาน ให้ทุกคนแลกเปลี่ยนความคิดเห็นได้ทันที</p>
                        </div>

                        <!-- Feature 5 -->
                        <div
                            class="fade-up-enter delay-100 bg-gray-50 rounded-2xl p-8 hover:bg-white hover:shadow-xl hover:shadow-indigo-100/50 hover:-translate-y-1 transition-all duration-300 border border-transparent hover:border-indigo-100 group">
                            <div
                                class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">แยกบทบาทชัดเจน</h3>
                            <p class="mt-3 text-gray-600 leading-relaxed">ประสบการณ์ที่ออกแบบมาโดยเฉพาะสำหรับ ครูผู้สอน
                                และ นักเรียน หน้าต่างการใช้งานแยกกันชัดเจน</p>
                        </div>

                        <!-- Feature 6 -->
                        <div
                            class="fade-up-enter delay-200 bg-gray-50 rounded-2xl p-8 hover:bg-white hover:shadow-xl hover:shadow-indigo-100/50 hover:-translate-y-1 transition-all duration-300 border border-transparent hover:border-indigo-100 group">
                            <div
                                class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform">
                                <i class="fa-solid fa-language"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">รองรับ 2 ภาษา</h3>
                            <p class="mt-3 text-gray-600 leading-relaxed">เลือกใช้งานได้ทั้งภาษาไทยและภาษาอังกฤษ
                                ปรับเปลี่ยนได้ตลอดเวลาเพื่อให้เข้ากับผู้ใช้งานมากที่สุด</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Benefits -->
            <section id="benefits" class="bg-slate-900 py-20 lg:py-28 text-white relative overflow-hidden">
                <!-- Dark blobs -->
                <div
                    class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-[100px] pointer-events-none">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-sky-500/20 rounded-full blur-[100px] pointer-events-none">
                </div>

                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 observer-target">
                    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                        <div class="fade-up-enter">
                            <span
                                class="text-sm font-bold tracking-wider text-indigo-400 uppercase">สิทธิประโยชน์</span>
                            <h2 class="mt-4 text-3xl md:text-5xl font-extrabold leading-tight">
                                เปลี่ยนงานน่าเบื่อ<br /><span class="text-indigo-400">ให้ไวและง่ายกว่าเดิม</span></h2>
                            <p class="mt-6 text-lg text-slate-300 leading-relaxed">
                                เราตั้งใจออกแบบระบบที่ลดขั้นตอนซับซ้อนทิ้งไป เพื่อให้คุณโฟกัสกับเรื่องสำคัญที่สุด
                                นั่นคือความสำเร็จของผู้เรียน
                            </p>

                            <div class="mt-10 space-y-6">
                                <div class="flex gap-4">
                                    <div
                                        class="mt-1 w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-check text-indigo-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold">สำหรับครูผู้สอน</h4>
                                        <p class="mt-1 text-slate-400 leading-relaxed">ตรวจงานจากคิวได้อัตโนมัติ
                                            ไม่ต้องคลิกเปลี่ยนหน้า จัดการเอกสารและคะแนนอย่างเป็นระบบ ลดเวลาทำสรุปคะแนน
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div
                                        class="mt-1 w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-check text-indigo-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold">สำหรับนักเรียน</h4>
                                        <p class="mt-1 text-slate-400 leading-relaxed">เช็กงานค้าง ส่งงาน
                                            และพูดคุยกับเพื่อนได้ทันที รู้สึกเหมือนกำลังเล่นเกมสะสมเลเวล
                                            ให้รางวัลความขยันด้วยตนเอง</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image / Graphic representaton -->
                        <div
                            class="fade-up-enter delay-200 relative lg:h-[500px] rounded-3xl bg-linear-to-tr from-slate-800 to-slate-700 border border-slate-600 shadow-2xl overflow-hidden p-8 flex flex-col justify-center items-center">
                            <i class="fa-solid fa-chart-pie text-9xl text-slate-600 mb-6 drop-shadow-lg"></i>
                            <div class="w-full space-y-4 max-w-sm">
                                <div class="h-12 w-full bg-slate-600/50 rounded-xl animate-pulse"></div>
                                <div class="h-12 w-5/6 bg-slate-600/50 rounded-xl animate-pulse delay-75"></div>
                                <div class="h-12 w-3/4 bg-slate-600/50 rounded-xl animate-pulse delay-150"></div>
                            </div>
                            <div
                                class="absolute inset-0 bg-linear-to-t from-slate-900 via-transparent to-transparent opacity-80">
                            </div>
                            <p class="absolute bottom-8 left-0 right-0 text-center text-slate-300 font-medium">Dashboard
                                Preview</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pricing CTA -->
            <section id="pricing" class="py-20 lg:py-32 bg-gray-50 relative">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center observer-target">
                    <div
                        class="fade-up-enter rounded-3xl bg-indigo-600 p-10 md:p-16 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                        <!-- BG pattern block -->
                        <div class="absolute inset-0 opacity-10"
                            style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;">
                        </div>

                        <div class="relative z-10">
                            <span
                                class="inline-block py-1 px-3 rounded-full bg-white/20 text-white text-xs font-bold uppercase tracking-wider mb-6">เริ่มใช้งานฟรีทันที</span>
                            <h2 class="text-3xl md:text-5xl font-extrabold leading-tight">
                                เริ่มสร้างห้องเรียนของคุณ<br />ในเวลาเพียง 2 นาที</h2>
                            <p class="mt-6 text-indigo-100 text-lg max-w-2xl mx-auto leading-relaxed">
                                ไม่มีค่าใช้จ่ายแอบแฝง ไม่มีระยะเวลาทดลองใช้ ฟีเจอร์ทุกอย่างเปิดให้ใช้งานฟรีแบบ 100%</p>

                            <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
                                <a href="{{ route('register') }}"
                                    class="btn-3d btn-3d--white w-full sm:w-auto px-8 py-4 rounded-xl text-indigo-600 font-bold text-lg">
                                    สมัครสมาชิกฟรี <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                                </a>
                                <a href="{{ route('login') }}"
                                    class="btn-3d btn-3d--dark w-full sm:w-auto px-6 py-4 rounded-xl font-bold text-lg">
                                    <i class="fa-solid fa-chalkboard-user mr-2"></i>ครู
                                </a>
                                <a href="{{ route('login') }}"
                                    class="btn-3d btn-3d--dark w-full sm:w-auto px-6 py-4 rounded-xl font-bold text-lg">
                                    <i class="fa-solid fa-user-graduate mr-2"></i>นักเรียน
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <div class="relative mt-auto">
            <!-- Wave SVG -->
            <svg class="absolute bottom-full left-0 w-full h-12 md:h-24 lg:h-32 text-slate-900 drop-shadow-sm pointer-events-none"
                preserveAspectRatio="none" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg">
                <path fill="currentColor" fill-opacity="1"
                    d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,117.3C960,139,1056,181,1152,192C1248,203,1344,181,1392,170.7L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
                </path>
            </svg>

            <footer class="bg-slate-900 border-t border-slate-800 pt-16 pb-8">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-8 mb-12">
                        <!-- Brand -->
                        <div class="col-span-1 md:col-span-1">
                            <div class="flex items-center gap-3 mb-6 relative">
                                <span
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500 text-white shadow-lg shadow-indigo-500/30">
                                    <i class="fas fa-graduation-cap text-lg"></i>
                                </span>
                                <span class="text-2xl font-bold text-white tracking-tight">LiteLearning</span>
                            </div>
                            <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                                ยกระดับการจัดการห้องเรียนให้ง่ายและสนุกยิ่งขึ้น
                                ด้วยเครื่องมือที่ตอบโจทย์ทั้งผู้สอนและผู้เรียน
                            </p>
                            <div class="flex gap-4 mt-6">
                                <a href="#"
                                    class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-indigo-500 hover:text-white transition-all shadow-sm">
                                    <i class="fa-brands fa-facebook-f"></i>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-indigo-500 hover:text-white transition-all shadow-sm">
                                    <i class="fa-brands fa-twitter"></i>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-indigo-500 hover:text-white transition-all shadow-sm">
                                    <i class="fa-brands fa-discord"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Links section 1 -->
                        <div class="col-span-1">
                            <h4 class="text-white font-semibold mb-6 uppercase tracking-wider text-sm">ส่วนสำคัญ</h4>
                            <ul class="space-y-4">
                                <li><a href="#features"
                                        class="text-slate-400 hover:text-indigo-400 transition-colors text-sm">ฟีเจอร์เด่น</a>
                                </li>
                                <li><a href="#benefits"
                                        class="text-slate-400 hover:text-indigo-400 transition-colors text-sm">ประโยชน์การใช้งาน</a>
                                </li>
                                <li><a href="#pricing"
                                        class="text-slate-400 hover:text-indigo-400 transition-colors text-sm">แผนการใช้งาน</a>
                                </li>
                                <li><a href="{{ route('register') }}"
                                        class="text-slate-400 hover:text-indigo-400 transition-colors text-sm">สมัครสมาชิกฟรี</a>
                                </li>
                            </ul>
                        </div>

                        <!-- Links section 2 -->
                        <div class="col-span-1">
                            <h4 class="text-white font-semibold mb-6 uppercase tracking-wider text-sm">ความช่วยเหลือ
                            </h4>
                            <ul class="space-y-4">
                                <li><a href="#"
                                        class="text-slate-400 hover:text-indigo-400 transition-colors text-sm">ศูนย์ช่วยเหลือ
                                        (Help Center)</a></li>
                                <li><a href="#"
                                        class="text-slate-400 hover:text-indigo-400 transition-colors text-sm">ติดต่อทีมงาน</a>
                                </li>
                                <li><a href="#"
                                        class="text-slate-400 hover:text-indigo-400 transition-colors text-sm">เงื่อนไขการให้บริการ
                                        (ToS)</a></li>
                                <li><a href="#"
                                        class="text-slate-400 hover:text-indigo-400 transition-colors text-sm">นโยบายความเป็นส่วนตัว
                                        (Privacy)</a></li>
                            </ul>
                        </div>
                    </div>

                    <div
                        class="pt-8 border-t border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-slate-500 font-medium">
                            &copy; {{ now()->year }} LiteLearning. All rights reserved.
                        </p>
                        <div class="flex items-center gap-2 text-sm text-slate-500">
                            <span>Made with</span>
                            <i class="fa-solid fa-heart text-red-500 animate-pulse"></i>
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

                // Navbar background on scroll
                const navbar = document.getElementById('navbar');
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 20) {
                        navbar.classList.add('shadow-md');
                    } else {
                        navbar.classList.remove('shadow-md');
                    }
                });
            });

            // Mobile Menu Script
            (() => {
                const toggle = document.getElementById('mobile-menu-toggle');
                const panel = document.getElementById('mobile-menu-panel');
                const icon = toggle.querySelector('.hamburger-icon');

                if (!toggle || !panel || !icon) {
                    return;
                }

                const toggleIcon = (isOpen) => {
                    icon.classList.toggle('is-active', isOpen);
                };

                const closeMenu = () => {
                    panel.classList.remove('max-h-[500px]', 'opacity-100', 'translate-y-0');
                    panel.classList.add('max-h-0', 'opacity-0', '-translate-y-1');
                    toggleIcon(false);
                };

                const openMenu = () => {
                    panel.classList.remove('max-h-0', 'opacity-0', '-translate-y-1');
                    panel.classList.add('max-h-[500px]', 'opacity-100', 'translate-y-0');
                    toggleIcon(true);
                };

                toggle.addEventListener('click', () => {
                    if (panel.classList.contains('max-h-0')) {
                        openMenu();
                    } else {
                        closeMenu();
                    }
                });

                panel.querySelectorAll('[data-menu-close]').forEach((link) => {
                    link.addEventListener('click', closeMenu);
                });
            })();
        </script>
</body>

</html>