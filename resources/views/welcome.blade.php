<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LiteLearning</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
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
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: center;
        }

        .hamburger-icon.is-active .hamburger-line:nth-child(1) { transform: translateY(6px) rotate(45deg); }
        .hamburger-icon.is-active .hamburger-line:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .hamburger-icon.is-active .hamburger-line:nth-child(3) { transform: translateY(-6px) rotate(-45deg); }
    </style>
</head>

<body class="bg-[#ffffff] font-sans antialiased text-[#101114]">

    <div class="min-h-screen flex flex-col relative z-10">
        <!-- Navigation -->
        <header class="sticky top-0 z-50 border-b border-[#dedee5] bg-white/80 backdrop-blur-md transition-all duration-300" id="navbar">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                    <span class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#7132f5] text-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] transition-transform duration-300 group-hover:scale-105">
                        <i class="fas fa-academic-cap text-lg"></i>
                    </span>
                    <span class="text-xl font-bold text-[#101114] tracking-tight" style="font-family: 'IBM Plex Sans', sans-serif; letter-spacing: -0.5px;">LiteLearning</span>
                </a>

                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-[#686b82]">
                    <a href="#features" class="hover:text-[#7132f5] transition-colors">ฟีเจอร์</a>
                    <a href="#benefits" class="hover:text-[#7132f5] transition-colors">ประโยชน์</a>
                    <a href="#pricing" class="hover:text-[#7132f5] transition-colors">แผนใช้งาน</a>
                </nav>

                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-[#101114] hover:text-[#7132f5] transition-colors px-4 py-2 rounded-[12px] border border-transparent hover:bg-[rgba(133,91,251,0.08)]">
                        <i class="fa-solid fa-chalkboard-user mr-1.5"></i>ครู
                    </a>
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-[#101114] hover:text-[#7132f5] transition-colors px-4 py-2 rounded-[12px] border border-transparent hover:bg-[rgba(133,91,251,0.08)]">
                        <i class="fa-solid fa-user-graduate mr-1.5"></i>นักเรียน
                    </a>
                    <a href="{{ route('register') }}" class="btn-3d btn-3d--indigo rounded-[12px] px-5 py-2.5 text-sm">
                        เข้าใช้งานฟรี <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <button id="mobile-menu-toggle" type="button" aria-label="Toggle menu" class="inline-flex md:hidden h-10 w-10 items-center justify-center rounded-[10px] border border-[#dedee5] text-[#101114] hover:bg-[#f9f9fb] relative focus:outline-none focus:ring-2 focus:ring-[#7132f5]">
                    <span class="hamburger-icon">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </span>
                </button>
            </div>

            <div id="mobile-menu-panel" class="md:hidden overflow-hidden max-h-0 opacity-0 -translate-y-1 transition-all duration-300 ease-in-out border-t border-[#dedee5] bg-white shadow-lg absolute w-full left-0">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <nav class="flex flex-col gap-2 text-base font-medium text-[#101114]">
                        <a href="#features" data-menu-close class="rounded-[12px] px-4 py-3 hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5] transition-colors"><i class="fa-solid fa-cubes w-6 text-center mr-2 text-[#9497a9]"></i> ฟีเจอร์</a>
                        <a href="#benefits" data-menu-close class="rounded-[12px] px-4 py-3 hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5] transition-colors"><i class="fa-solid fa-lightbulb w-6 text-center mr-2 text-[#9497a9]"></i> ประโยชน์</a>
                        <a href="#pricing" data-menu-close class="rounded-[12px] px-4 py-3 hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5] transition-colors"><i class="fa-solid fa-layer-group w-6 text-center mr-2 text-[#9497a9]"></i> แผนใช้งาน</a>
                    </nav>
                    <div class="mt-5 flex flex-col gap-3 pt-5 border-t border-[#dedee5]">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-[12px] border border-[#dedee5] px-4 py-3 text-base font-semibold text-[#101114] hover:bg-[rgba(133,91,251,0.04)] hover:border-[#7132f5] transition-all"><i class="fa-solid fa-chalkboard-user"></i> เข้าสู่ระบบ (ครู)</a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-[12px] border border-[#dedee5] px-4 py-3 text-base font-semibold text-[#101114] hover:bg-[rgba(133,91,251,0.04)] hover:border-[#7132f5] transition-all"><i class="fa-solid fa-user-graduate"></i> เข้าสู่ระบบ (นักเรียน)</a>
                        <a href="{{ route('register') }}" class="btn-3d btn-3d--indigo rounded-[12px] px-4 py-3 text-base text-center">เริ่มต้นใช้งานฟรี</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <!-- Hero Section -->
            <section class="space-bg relative pt-20 pb-24 lg:pt-32 lg:pb-32 overflow-hidden border-b border-[#dedee5]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center observer-target relative z-10">
                    <span class="fade-up-enter inline-flex items-center rounded-full border border-[#dedee5] bg-[#ffffff] px-4 py-1.5 text-sm font-bold text-[#7132f5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] mb-6">
                        <span class="flex h-2 w-2 rounded-full bg-[#149e61] mr-2"></span>
                        สร้างมาเพื่อห้องเรียนยุคใหม่
                    </span>

                    <h1 class="fade-up-enter delay-100 max-w-4xl mx-auto text-5xl md:text-6xl lg:text-7xl font-extrabold text-[#101114] leading-tight" style="font-family: 'IBM Plex Sans', sans-serif; letter-spacing: -1px;">
                        จัดการห้องเรียนอย่างโปร
                        <br class="hidden md:block" />
                        ด้วย <span class="bg-gradient-to-r from-[#855bfb] to-[#5741d8] bg-clip-text text-transparent">ความสนุกที่มากกว่า</span>
                    </h1>

                    <p class="fade-up-enter delay-200 mt-6 max-w-2xl mx-auto text-lg md:text-xl text-[#686b82] leading-relaxed">
                        เปลี่ยนห้องเรียนธรรมดาให้เป็นความท้าทาย จัดการงาน มอบหมายคะแนน และใช้ระบบเกมมิฟิเคชัน
                        ทั้งหมดนี้ในแพลตฟอร์มเดียวที่ออกแบบมาอย่างเป็นระบบ
                    </p>

                    <div class="fade-up-enter delay-300 mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="btn-3d btn-3d--indigo rounded-[12px] px-8 py-4 text-base w-full sm:w-auto">
                            เริ่มสร้างห้องเรียนฟรี <i class="fa-solid fa-arrow-right ml-2"></i>
                        </a>
                        <a href="#features" class="btn-3d btn-3d--white rounded-[12px] px-8 py-4 text-base font-bold w-full sm:w-auto">
                            ดูฟีเจอร์ <i class="fa-solid fa-arrow-down ml-2"></i>
                        </a>
                    </div>

                    <!-- Dashboard Mockup Image / Graphic -->
                    <div class="fade-up-enter delay-300 mt-20 relative max-w-5xl mx-auto rounded-[16px] border border-[#dedee5] bg-[#ffffff] p-2 shadow-[rgba(0,0,0,0.06)_0px_12px_48px] transform hover:-translate-y-1 transition-transform duration-500">
                        <div class="rounded-[12px] overflow-hidden border border-[#dedee5] bg-[#f9f9fb] flex flex-col">
                            <!-- Mac Window header mock -->
                            <div class="bg-[#ffffff] px-4 py-3 flex gap-2 border-b border-[#dedee5]">
                                <div class="w-3 h-3 rounded-full bg-[#e11d48]"></div>
                                <div class="w-3 h-3 rounded-full bg-[#f59e0b]"></div>
                                <div class="w-3 h-3 rounded-full bg-[#149e61]"></div>
                            </div>
                            <div class="p-6 md:p-10 text-left grid md:grid-cols-4 gap-6">
                                <!-- Mock Sidebar -->
                                <div class="hidden md:flex flex-col gap-4 border-r border-[#dedee5] pr-6 col-span-1">
                                    <div class="h-8 w-10/12 bg-[#dedee5] rounded-[8px] animate-pulse"></div>
                                    <div class="h-4 w-1/2 bg-[#dedee5] rounded-[6px] animate-pulse mt-4"></div>
                                    <div class="h-4 w-2/3 bg-[#dedee5] rounded-[6px] animate-pulse"></div>
                                    <div class="h-4 w-1/2 bg-[#dedee5] rounded-[6px] animate-pulse"></div>
                                </div>
                                <!-- Mock Content -->
                                <div class="md:col-span-3 space-y-6">
                                    <div class="flex justify-between items-center">
                                        <div class="h-8 w-1/3 bg-[rgba(133,91,251,0.16)] rounded-[8px]"></div>
                                        <div class="h-10 w-10 bg-[#dedee5] rounded-full"></div>
                                    </div>
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                        <div class="h-24 bg-[#ffffff] rounded-[12px] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] border border-[#dedee5] p-4 shrink-0 flex flex-col justify-between">
                                            <div class="h-4 w-1/2 bg-[#dedee5] rounded-[6px]"></div>
                                            <div class="h-8 w-3/4 bg-[#855bfb] rounded-[6px]"></div>
                                        </div>
                                        <div class="h-24 bg-[#ffffff] rounded-[12px] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] border border-[#dedee5] p-4 shrink-0 flex flex-col justify-between">
                                            <div class="h-4 w-1/2 bg-[#dedee5] rounded-[6px]"></div>
                                            <div class="h-8 w-2/3 bg-[#149e61] rounded-[6px]"></div>
                                        </div>
                                        <div class="h-24 bg-[#ffffff] rounded-[12px] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] border border-[#dedee5] p-4 shrink-0 flex flex-col justify-between sm:flex">
                                            <div class="h-4 w-1/2 bg-[#dedee5] rounded-[6px]"></div>
                                            <div class="h-8 w-3/4 bg-[#f59e0b] rounded-[6px]"></div>
                                        </div>
                                        <div class="h-24 bg-[#ffffff] rounded-[12px] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] border border-[#dedee5] p-4 shrink-0 flex flex-col justify-between lg:flex">
                                            <div class="h-4 w-1/2 bg-[#dedee5] rounded-[6px]"></div>
                                            <div class="h-8 w-1/2 bg-[#5741d8] rounded-[6px]"></div>
                                        </div>
                                    </div>
                                    <div class="h-32 bg-[#ffffff] rounded-[12px] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] border border-[#dedee5] p-4">
                                        <div class="h-4 w-1/4 bg-[#dedee5] rounded-[6px] mb-4"></div>
                                        <div class="space-y-3">
                                            <div class="h-3 w-full bg-[#f9f9fb] rounded-[4px]"></div>
                                            <div class="h-3 w-5/6 bg-[#f9f9fb] rounded-[4px]"></div>
                                            <div class="h-3 w-4/6 bg-[#f9f9fb] rounded-[4px]"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6 text-sm py-8 border-t border-[#dedee5]">
                        <div class="px-4">
                            <p class="text-3xl font-extrabold text-[#7132f5]" style="font-family: 'IBM Plex Sans', sans-serif;">10k+</p>
                            <p class="text-[#686b82] font-medium mt-1">งานที่ถูกส่ง</p>
                        </div>
                        <div class="px-4 border-l border-[#dedee5]">
                            <p class="text-3xl font-extrabold text-[#7132f5]" style="font-family: 'IBM Plex Sans', sans-serif;">500+</p>
                            <p class="text-[#686b82] font-medium mt-1">ห้องเรียนที่แอคทีฟ</p>
                        </div>
                        <div class="px-4 hidden md:block border-l border-[#dedee5]">
                            <p class="text-3xl font-extrabold text-[#7132f5]" style="font-family: 'IBM Plex Sans', sans-serif;">4.9/5</p>
                            <p class="text-[#686b82] font-medium mt-1">คะแนนความพอใจ</p>
                        </div>
                        <div class="px-4 hidden md:block border-l border-[#dedee5]">
                            <p class="text-3xl font-extrabold text-[#7132f5]" style="font-family: 'IBM Plex Sans', sans-serif;">99.9%</p>
                            <p class="text-[#686b82] font-medium mt-1">Uptime สูงสุด</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section id="features" class="bg-[#ffffff] py-20 lg:py-28 relative border-b border-[#dedee5]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 observer-target">
                    <div class="text-center max-w-3xl mx-auto mb-16 fade-up-enter">
                        <span class="text-sm font-bold tracking-wider text-[#7132f5] uppercase bg-[rgba(133,91,251,0.16)] px-3 py-1 rounded-[8px]">ฟีเจอร์เด่น</span>
                        <h2 class="mt-4 text-3xl md:text-4xl font-extrabold text-[#101114]" style="font-family: 'IBM Plex Sans', sans-serif; letter-spacing: -0.5px;">
                            ตัวช่วยที่ทำให้ห้องเรียนของคุณ<br />โดดเด่นไม่เหมือนใคร
                        </h2>
                        <p class="mt-4 text-lg text-[#686b82]">รวบรวมทุกฟังก์ชันที่จำเป็นพร้อมระบบออกแบบที่สวยงาม ใช้งานง่าย</p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Feature 1 -->
                        <div class="fade-up-enter bg-[#ffffff] rounded-[16px] p-8 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] border border-[#dedee5] hover:border-[#7132f5] hover:-translate-y-1 transition-all duration-300 group">
                            <div class="w-14 h-14 rounded-[12px] bg-[rgba(133,91,251,0.16)] text-[#7132f5] flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                            <h3 class="text-xl font-bold text-[#101114]">จัดการห้องเรียนครบวงจร</h3>
                            <p class="mt-3 text-[#686b82] leading-relaxed">สร้างห้อง เชิญนักเรียน คัดกรองสมาชิกด้วยรหัสผ่าน และจัดทุกอย่างให้อยู่ในที่เดียวอย่างเป็นระบบ</p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="fade-up-enter delay-100 bg-[#ffffff] rounded-[16px] p-8 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] border border-[#dedee5] hover:border-[#7132f5] hover:-translate-y-1 transition-all duration-300 group">
                            <div class="w-14 h-14 rounded-[12px] bg-[rgba(133,91,251,0.16)] text-[#7132f5] flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-list-check"></i>
                            </div>
                            <h3 class="text-xl font-bold text-[#101114]">ระบบสั่งงานอัจฉริยะ</h3>
                            <p class="mt-3 text-[#686b82] leading-relaxed">สั่งงาน แนบไฟล์ กำหนดส่ง และตรวจให้คะแนนได้ทันที พร้อมระบบเช็คคนส่งช้าอัตโนมัติ</p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="fade-up-enter delay-200 bg-[#ffffff] rounded-[16px] p-8 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] border border-[#dedee5] hover:border-[#7132f5] hover:-translate-y-1 transition-all duration-300 group">
                            <div class="w-14 h-14 rounded-[12px] bg-[rgba(133,91,251,0.16)] text-[#7132f5] flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-trophy"></i>
                            </div>
                            <h3 class="text-xl font-bold text-[#101114]">เกมมิฟิเคชัน (Gamification)</h3>
                            <p class="mt-3 text-[#686b82] leading-relaxed">กระตุ้นผู้เรียนด้วยระบบ Level, เควสต์สะสม XP และระบบเหรียญรางวัล ทำให้การเรียนไม่น่าเบื่อ</p>
                        </div>

                        <!-- Feature 4 -->
                        <div class="fade-up-enter bg-[#ffffff] rounded-[16px] p-8 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] border border-[#dedee5] hover:border-[#7132f5] hover:-translate-y-1 transition-all duration-300 group">
                            <div class="w-14 h-14 rounded-[12px] bg-[rgba(133,91,251,0.16)] text-[#7132f5] flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-comments"></i>
                            </div>
                            <h3 class="text-xl font-bold text-[#101114]">กระดานสนทนาแบบเรียลไทม์</h3>
                            <p class="mt-3 text-[#686b82] leading-relaxed">มี Stream สำหรับประกาศข่าว และส่วนคอมเมนต์ใต้ชิ้นงาน ให้ทุกคนแลกเปลี่ยนความคิดเห็นได้ทันที</p>
                        </div>

                        <!-- Feature 5 -->
                        <div class="fade-up-enter delay-100 bg-[#ffffff] rounded-[16px] p-8 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] border border-[#dedee5] hover:border-[#7132f5] hover:-translate-y-1 transition-all duration-300 group">
                            <div class="w-14 h-14 rounded-[12px] bg-[rgba(133,91,251,0.16)] text-[#7132f5] flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <h3 class="text-xl font-bold text-[#101114]">แยกบทบาทชัดเจน</h3>
                            <p class="mt-3 text-[#686b82] leading-relaxed">ประสบการณ์ที่ออกแบบมาโดยเฉพาะสำหรับ ครูผู้สอน และ นักเรียน หน้าต่างการใช้งานแยกกันชัดเจน</p>
                        </div>

                        <!-- Feature 6 -->
                        <div class="fade-up-enter delay-200 bg-[#ffffff] rounded-[16px] p-8 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] border border-[#dedee5] hover:border-[#7132f5] hover:-translate-y-1 transition-all duration-300 group">
                            <div class="w-14 h-14 rounded-[12px] bg-[rgba(133,91,251,0.16)] text-[#7132f5] flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-language"></i>
                            </div>
                            <h3 class="text-xl font-bold text-[#101114]">รองรับ 2 ภาษา</h3>
                            <p class="mt-3 text-[#686b82] leading-relaxed">เลือกใช้งานได้ทั้งภาษาไทยและภาษาอังกฤษ ปรับเปลี่ยนได้ตลอดเวลาเพื่อให้เข้ากับผู้ใช้งานมากที่สุด</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Benefits -->
            <section id="benefits" class="bg-[#101114] py-20 lg:py-28 text-white relative overflow-hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 observer-target">
                    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                        <div class="fade-up-enter">
                            <span class="text-sm font-bold tracking-wider text-[#855bfb] uppercase">สิทธิประโยชน์</span>
                            <h2 class="mt-4 text-3xl md:text-5xl font-extrabold leading-tight" style="font-family: 'IBM Plex Sans', sans-serif; letter-spacing: -1px;">
                                เปลี่ยนงานน่าเบื่อ<br /><span class="text-[#855bfb]">ให้ไวและง่ายกว่าเดิม</span>
                            </h2>
                            <p class="mt-6 text-lg text-[#9497a9] leading-relaxed">
                                เราตั้งใจออกแบบระบบที่ลดขั้นตอนซับซ้อนทิ้งไป เพื่อให้คุณโฟกัสกับเรื่องสำคัญที่สุด
                                นั่นคือความสำเร็จของผู้เรียน
                            </p>

                            <div class="mt-10 space-y-8">
                                <div class="flex gap-4">
                                    <div class="mt-1 w-10 h-10 rounded-[12px] bg-[#7132f5] flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-check text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-white">สำหรับครูผู้สอน</h4>
                                        <p class="mt-1 text-[#9497a9] leading-relaxed">ตรวจงานจากคิวได้อัตโนมัติ
                                            ไม่ต้องคลิกเปลี่ยนหน้า จัดการเอกสารและคะแนนอย่างเป็นระบบ ลดเวลาทำสรุปคะแนน
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="mt-1 w-10 h-10 rounded-[12px] bg-[#7132f5] flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-check text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-white">สำหรับนักเรียน</h4>
                                        <p class="mt-1 text-[#9497a9] leading-relaxed">เช็กงานค้าง ส่งงาน
                                            และพูดคุยกับเพื่อนได้ทันที รู้สึกเหมือนกำลังเล่นเกมสะสมเลเวล
                                            ให้รางวัลความขยันด้วยตนเอง</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Abstract Mockup representaton -->
                        <div class="fade-up-enter delay-200 relative lg:h-[400px] rounded-[16px] bg-[#1a1b23] border border-[#2a2b36] shadow-2xl overflow-hidden p-8 flex flex-col justify-center items-center">
                            <i class="fa-solid fa-layer-group text-8xl text-[#2a2b36] mb-8"></i>
                            <div class="w-full space-y-4 max-w-sm">
                                <div class="h-10 w-full bg-[#2a2b36] rounded-[8px]"></div>
                                <div class="flex gap-4">
                                    <div class="h-10 w-2/3 bg-[#2a2b36] rounded-[8px]"></div>
                                    <div class="h-10 w-1/3 bg-[#5741d8] rounded-[8px]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pricing CTA -->
            <section id="pricing" class="py-20 lg:py-32 bg-[#f9f9fb] border-y border-[#dedee5] relative">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center observer-target">
                    <div class="fade-up-enter rounded-[24px] bg-[#7132f5] p-10 md:p-16 text-white shadow-[rgba(113,50,245,0.24)_0px_20px_40px] relative overflow-hidden">
                        
                        <div class="relative z-10">
                            <span class="inline-block py-1.5 px-4 rounded-full bg-[rgba(255,255,255,0.16)] text-white text-xs font-bold uppercase tracking-wider mb-6">เริ่มใช้งานฟรีทันที</span>
                            <h2 class="text-3xl md:text-5xl font-extrabold leading-tight" style="font-family: 'IBM Plex Sans', sans-serif; letter-spacing: -1px;">
                                เริ่มสร้างห้องเรียนของคุณ<br />ในเวลาเพียง 2 นาที
                            </h2>
                            <p class="mt-6 text-[#c4b5fd] text-lg max-w-2xl mx-auto leading-relaxed">
                                ไม่มีค่าใช้จ่ายแอบแฝง ไม่มีระยะเวลาทดลองใช้ ฟีเจอร์ทุกอย่างเปิดให้ใช้งานฟรีแบบ 100%
                            </p>

                            <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
                                <a href="{{ route('register') }}" class="btn-3d btn-3d--white w-full sm:w-auto px-8 py-4 rounded-[12px] text-[#101114] font-bold text-lg">
                                    สมัครสมาชิกฟรี <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                                </a>
                                <a href="{{ route('login') }}" class="btn-3d btn-3d--dark w-full sm:w-auto px-6 py-4 rounded-[12px] font-bold text-lg text-white">
                                    <i class="fa-solid fa-chalkboard-user mr-2"></i>ครู
                                </a>
                                <a href="{{ route('login') }}" class="btn-3d btn-3d--dark w-full sm:w-auto px-6 py-4 rounded-[12px] font-bold text-lg text-white">
                                    <i class="fa-solid fa-user-graduate mr-2"></i>นักเรียน
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        
        <footer class="bg-[#101114] border-t border-[#2a2b36] pt-16 pb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-8 mb-12">
                    <!-- Brand -->
                    <div class="col-span-1 md:col-span-1">
                        <div class="flex items-center gap-3 mb-6 relative">
                            <span class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#7132f5] text-white">
                                <i class="fas fa-academic-cap text-lg"></i>
                            </span>
                            <span class="text-2xl font-bold text-white tracking-tight" style="font-family: 'IBM Plex Sans', sans-serif;">LiteLearning</span>
                        </div>
                        <p class="text-[#9497a9] text-sm leading-relaxed max-w-sm">
                            ยกระดับการจัดการห้องเรียนให้ง่ายและสนุกยิ่งขึ้น
                            ด้วยเครื่องมือที่ตอบโจทย์ทั้งผู้สอนและผู้เรียน
                        </p>
                        <div class="flex gap-4 mt-6">
                            <a href="#" class="w-10 h-10 rounded-[12px] bg-[#1a1b23] border border-[#2a2b36] flex items-center justify-center text-[#9497a9] hover:border-[#7132f5] hover:text-[#7132f5] transition-all">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-[12px] bg-[#1a1b23] border border-[#2a2b36] flex items-center justify-center text-[#9497a9] hover:border-[#7132f5] hover:text-[#7132f5] transition-all">
                                <i class="fa-brands fa-twitter"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-[12px] bg-[#1a1b23] border border-[#2a2b36] flex items-center justify-center text-[#9497a9] hover:border-[#7132f5] hover:text-[#7132f5] transition-all">
                                <i class="fa-brands fa-discord"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Links section 1 -->
                    <div class="col-span-1">
                        <h4 class="text-white font-semibold mb-6 uppercase tracking-wider text-sm">ส่วนสำคัญ</h4>
                        <ul class="space-y-4">
                            <li><a href="#features" class="text-[#9497a9] hover:text-white transition-colors text-sm">ฟีเจอร์เด่น</a></li>
                            <li><a href="#benefits" class="text-[#9497a9] hover:text-white transition-colors text-sm">ประโยชน์การใช้งาน</a></li>
                            <li><a href="#pricing" class="text-[#9497a9] hover:text-white transition-colors text-sm">แผนการใช้งาน</a></li>
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
                        &copy; {{ now()->year }} LiteLearning. All rights reserved.
                    </p>
                    <div class="flex items-center gap-2 text-sm text-[#9497a9]">
                        <span>Made with</span>
                        <i class="fa-solid fa-heart text-[#e11d48]"></i>
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
                    navbar.classList.add('shadow-[rgba(0,0,0,0.03)_0px_4px_24px]');
                    navbar.classList.remove('border-[#dedee5]');
                    navbar.classList.add('border-transparent');
                } else {
                    navbar.classList.remove('shadow-[rgba(0,0,0,0.03)_0px_4px_24px]');
                    navbar.classList.add('border-[#dedee5]');
                    navbar.classList.remove('border-transparent');
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