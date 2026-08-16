<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
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
        <header class="fixed top-0 md:top-4 z-50 w-full transition-all duration-300" id="navbar">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 md:py-0">
                <div id="navbar-card" class="bg-white/80 backdrop-blur-md border border-[#dedee5] rounded-xl shadow-[rgba(0,0,0,0.03)_0px_4px_24px] px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between transition-all duration-300">
                    <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--ll-blue)] text-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] transition-transform duration-300 group-hover:scale-105">
                            <x-icon name="academic-cap" class="h-5 w-5" />
                        </span>
                        <span class="text-xl font-bold text-[#101114] tracking-tight" style="letter-spacing: -0.5px;">LiteLearning</span>
                    </a>

                    <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-[#686b82]">
                        <a href="#features" class="hover:text-[var(--ll-blue)] transition-colors">ฟีเจอร์</a>
                        <a href="#benefits" class="hover:text-[var(--ll-blue)] transition-colors">ประโยชน์</a>
                    </nav>

                    <div class="hidden md:flex items-center gap-3">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-[#101114] hover:text-[var(--ll-blue)] transition-colors px-4 py-2 rounded-xl border border-transparent hover:bg-[rgba(37,99,235,0.08)]">
                            <x-icon name="users" class="h-4 w-4 mr-1.5" />ครู
                        </a>
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-[#101114] hover:text-[var(--ll-blue)] transition-colors px-4 py-2 rounded-xl border border-transparent hover:bg-[rgba(37,99,235,0.08)]">
                            <x-icon name="academic-cap" class="h-4 w-4 mr-1.5" />นักเรียน
                        </a>
                        <a href="{{ route('register') }}" class="btn-3d btn-3d--blue rounded-xl px-5 py-2.5 text-sm">
                            เข้าใช้งานฟรี <x-icon name="arrow-right" class="h-4 w-4 ml-1" />
                        </a>
                    </div>

                    <button id="mobile-menu-toggle" type="button" aria-label="Toggle menu" class="inline-flex md:hidden h-10 w-10 items-center justify-center rounded-[10px] border border-[#dedee5] text-[#101114] hover:bg-[#f9f9fb] relative focus:outline-none focus:ring-2 focus:ring-[var(--ll-blue)]">
                        <span class="hamburger-icon">
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                        </span>
                    </button>
                </div>
            </div>

            <div id="mobile-menu-panel" class="md:hidden overflow-hidden max-h-0 opacity-0 -translate-y-1 transition-all duration-300 ease-in-out border-t border-[#dedee5] bg-white shadow-lg absolute w-full left-0">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <nav class="flex flex-col gap-2 text-base font-medium text-[#101114]">
                        <a href="#features" data-menu-close class="rounded-xl px-4 py-3 hover:bg-[rgba(37,99,235,0.08)] hover:text-[var(--ll-blue)] transition-colors"><x-icon name="cube" class="h-4 w-4 mr-2 text-[#9497a9]" /> ฟีเจอร์</a>
                        <a href="#benefits" data-menu-close class="rounded-xl px-4 py-3 hover:bg-[rgba(37,99,235,0.08)] hover:text-[var(--ll-blue)] transition-colors"><x-icon name="lightbulb" class="h-4 w-4 mr-2 text-[#9497a9]" /> ประโยชน์</a>
                    </nav>
                    <div class="mt-5 flex flex-col gap-3 pt-5 border-t border-[#dedee5]">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#dedee5] px-4 py-3 text-base font-semibold text-[#101114] hover:bg-[rgba(37,99,235,0.04)] hover:border-[var(--ll-blue)] transition-all"><x-icon name="users" class="h-4 w-4" /> เข้าสู่ระบบ (ครู)</a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#dedee5] px-4 py-3 text-base font-semibold text-[#101114] hover:bg-[rgba(37,99,235,0.04)] hover:border-[var(--ll-blue)] transition-all"><x-icon name="academic-cap" class="h-4 w-4" /> เข้าสู่ระบบ (นักเรียน)</a>
                        <a href="{{ route('register') }}" class="btn-3d btn-3d--blue rounded-xl px-4 py-3 text-base text-center">เริ่มต้นใช้งานฟรี</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <!-- Hero Section (Centered with Background Image & Stats) -->
            <section class="relative min-h-[calc(100vh-5rem)] flex flex-col justify-between z-10 pt-28 md:pt-24 pb-6 overflow-hidden">
                <!-- Overflowing background image positioned behind the hero -->
                <div class="absolute left-0 w-full h-full z-0 pointer-events-none" style="background-image: linear-gradient(to bottom, rgba(23, 37, 84, 0) 60%, #172554 100%), url('/images/landing.png'); background-repeat: no-repeat; background-position: top center; background-size: 100% auto;"></div>

                <!-- Content Container -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center observer-target relative z-10 w-full flex-1 flex flex-col justify-between">
                    <!-- Spacer/Flex push -->
                    <div></div>
                    
                    <!-- Main Text & CTAs (Centered vertically) -->
                    <div class="py-8">
                        <h1 class="fade-up-enter delay-100 max-w-4xl mx-auto text-5xl md:text-6xl lg:text-7xl font-extrabold text-[#101114] leading-tight" style="font-family: 'Google Sans', sans-serif; letter-spacing: -1px;">
                            เริ่มการเรียนรู้ด้วย
                            <br class="hidden md:block" />
                            <span class="bg-linear-to-r from-[#3b82f6] to-[#1d4ed8] bg-clip-text text-transparent">"ความสนุก" ที่มากกว่า</span>
                        </h1>

                        <p class="fade-up-enter delay-200 mt-6 max-w-2xl mx-auto text-lg md:text-xl text-[#101114] leading-relaxed">
                            เปลี่ยนห้องเรียนธรรมดาให้เป็นความท้าทาย จัดการงาน มอบหมายคะแนน และใช้ระบบเกมมิฟิเคชัน
                            ทั้งหมดนี้ในแพลตฟอร์มเดียวที่ออกแบบมาอย่างเป็นระบบ
                        </p>

                        <div class="fade-up-enter delay-300 mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="{{ route('register') }}" class="btn-3d btn-3d--blue rounded-xl px-8 py-4 text-base w-full sm:w-auto">
                                เริ่มสร้างห้องเรียนฟรี <x-icon name="arrow-right" class="h-4 w-4 ml-2" />
                            </a>
                            <a href="#features" class="btn-3d btn-3d--white rounded-xl px-8 py-4 text-base font-bold w-full sm:w-auto">
                                ดูฟีเจอร์ <x-icon name="arrow-down" class="h-4 w-4 ml-2" />
                            </a>
                        </div>
                    </div>

                    <!-- Stats Grid (Aligned at the bottom) -->
                    <div class="mt-auto grid grid-cols-2 md:grid-cols-4 gap-6 text-sm py-8 border-t border-white/10">
                        <div class="px-4">
                            <p id="stat-submissions" class="text-3xl font-extrabold text-[#93c5fd]" style="font-family: 'Google Sans', sans-serif;">10k+</p>
                            <p class="text-[#93c5fd]/80 font-medium mt-1">งานที่ถูกส่ง</p>
                        </div>
                        <div class="px-4 border-l border-white/10">
                            <p id="stat-classrooms" class="text-3xl font-extrabold text-[#93c5fd]" style="font-family: 'Google Sans', sans-serif;">500+</p>
                            <p class="text-[#93c5fd]/80 font-medium mt-1">ห้องเรียนที่แอคทีฟ</p>
                        </div>
                        <div class="px-4 hidden md:block border-l border-white/10">
                            <p id="stat-satisfaction" class="text-3xl font-extrabold text-[#93c5fd]" style="font-family: 'Google Sans', sans-serif;">4.9/5</p>
                            <p class="text-[#93c5fd]/80 font-medium mt-1">คะแนนความพอใจ</p>
                        </div>
                        <div class="px-4 hidden md:block border-l border-white/10">
                            <p id="stat-uptime" class="text-3xl font-extrabold text-[#93c5fd]" style="font-family: 'Google Sans', sans-serif;">99.9%</p>
                            <p class="text-[#93c5fd]/80 font-medium mt-1">Uptime สูงสุด</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Container (Solid Purple BG) -->
            <div class="landing-space-bg relative">

                <!-- Features Section -->
                <section id="features" class="py-20 lg:py-28 relative">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 observer-target">
                        <div class="text-center max-w-3xl mx-auto mb-16 fade-up-enter">
                            <span class="text-sm font-bold tracking-wider text-[#93c5fd] uppercase bg-white/10 px-3 py-1 rounded-lg">ฟีเจอร์เด่น</span>
                            <h2 class="mt-4 text-3xl md:text-4xl font-extrabold text-white" style="letter-spacing: -0.5px;">
                                ตัวช่วยที่ทำให้ห้องเรียนของคุณ<br />โดดเด่นไม่เหมือนใคร
                            </h2>
                            <p class="mt-4 text-lg text-[#93c5fd]/80">รวบรวมทุกฟังก์ชันที่จำเป็นพร้อมระบบออกแบบที่สวยงาม ใช้งานง่าย</p>
                        </div>

                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Feature 1 -->
                            <div class="fade-up-enter bg-white/5 backdrop-blur-sm rounded-2xl p-8 shadow-[rgba(0,0,0,0.1)_0px_8px_32px] border border-white/10 hover:border-white/30 hover:bg-white/10 hover:-translate-y-1 transition-all duration-300 group">
                                <div class="w-14 h-14 rounded-xl bg-white/10 text-[#93c5fd] flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition-transform">
                                    <x-icon name="users" class="h-6 w-6" />
                                </div>
                                <h3 class="text-xl font-bold text-white">จัดการห้องเรียนครบวงจร</h3>
                                <p class="mt-3 text-[#93c5fd]/80 leading-relaxed">สร้างห้อง เชิญนักเรียน คัดกรองสมาชิกด้วยรหัสผ่าน และจัดทุกอย่างให้อยู่ในที่เดียวอย่างเป็นระบบ</p>
                            </div>

                            <!-- Feature 2 -->
                            <div class="fade-up-enter delay-100 bg-white/5 backdrop-blur-sm rounded-2xl p-8 shadow-[rgba(0,0,0,0.1)_0px_8px_32px] border border-white/10 hover:border-white/30 hover:bg-white/10 hover:-translate-y-1 transition-all duration-300 group">
                                <div class="w-14 h-14 rounded-xl bg-white/10 text-[#93c5fd] flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition-transform">
                                    <x-icon name="clipboard-document-list" class="h-6 w-6" />
                                </div>
                                <h3 class="text-xl font-bold text-white">ระบบสั่งงานอัจฉริยะ</h3>
                                <p class="mt-3 text-[#93c5fd]/80 leading-relaxed">สั่งงาน แนบไฟล์ กำหนดส่ง และตรวจให้คะแนนได้ทันที พร้อมระบบเช็คคนส่งช้าอัตโนมัติ</p>
                            </div>

                            <!-- Feature 3 -->
                            <div class="fade-up-enter delay-200 bg-white/5 backdrop-blur-sm rounded-2xl p-8 shadow-[rgba(0,0,0,0.1)_0px_8px_32px] border border-white/10 hover:border-white/30 hover:bg-white/10 hover:-translate-y-1 transition-all duration-300 group">
                                <div class="w-14 h-14 rounded-xl bg-white/10 text-[#93c5fd] flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition-transform">
                                    <x-icon name="trophy" class="h-6 w-6" />
                                </div>
                                <h3 class="text-xl font-bold text-white">เกมมิฟิเคชัน (Gamification)</h3>
                                <p class="mt-3 text-[#93c5fd]/80 leading-relaxed">กระตุ้นผู้เรียนด้วยระบบ Level, เควสต์สะสม XP และระบบเหรียญรางวัล ทำให้การเรียนไม่น่าเบื่อ</p>
                            </div>

                            <!-- Feature 4 -->
                            <div class="fade-up-enter bg-white/5 backdrop-blur-sm rounded-2xl p-8 shadow-[rgba(0,0,0,0.1)_0px_8px_32px] border border-white/10 hover:border-white/30 hover:bg-white/10 hover:-translate-y-1 transition-all duration-300 group">
                                <div class="w-14 h-14 rounded-xl bg-white/10 text-[#93c5fd] flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition-transform">
                                    <x-icon name="chat-bubble-left" class="h-6 w-6" />
                                </div>
                                <h3 class="text-xl font-bold text-white">กระดานสนทนาแบบเรียลไทม์</h3>
                                <p class="mt-3 text-[#93c5fd]/80 leading-relaxed">มี Stream สำหรับประกาศข่าว และส่วนคอมเมนต์ใต้ชิ้นงาน ให้ทุกคนแลกเปลี่ยนความคิดเห็นได้ทันที</p>
                            </div>

                            <!-- Feature 5 -->
                            <div class="fade-up-enter delay-100 bg-white/5 backdrop-blur-sm rounded-2xl p-8 shadow-[rgba(0,0,0,0.1)_0px_8px_32px] border border-white/10 hover:border-white/30 hover:bg-white/10 hover:-translate-y-1 transition-all duration-300 group">
                                <div class="w-14 h-14 rounded-xl bg-white/10 text-[#93c5fd] flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition-transform">
                                    <x-icon name="shield-check" class="h-6 w-6" />
                                </div>
                                <h3 class="text-xl font-bold text-white">แยกบทบาทชัดเจน</h3>
                                <p class="mt-3 text-[#93c5fd]/80 leading-relaxed">ประสบการณ์ที่ออกแบบมาโดยเฉพาะสำหรับ ครูผู้สอน และ นักเรียน หน้าต่างการใช้งานแยกกันชัดเจน</p>
                            </div>

                            <!-- Feature 6 -->
                            <div class="fade-up-enter delay-200 bg-white/5 backdrop-blur-sm rounded-2xl p-8 shadow-[rgba(0,0,0,0.1)_0px_8px_32px] border border-white/10 hover:border-white/30 hover:bg-white/10 hover:-translate-y-1 transition-all duration-300 group">
                                <div class="w-14 h-14 rounded-xl bg-white/10 text-[#93c5fd] flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition-transform">
                                    <x-icon name="globe" class="h-6 w-6" />
                                </div>
                                <h3 class="text-xl font-bold text-white">รองรับ 2 ภาษา</h3>
                                <p class="mt-3 text-[#93c5fd]/80 leading-relaxed">เลือกใช้งานได้ทั้งภาษาไทยและภาษาอังกฤษ ปรับเปลี่ยนได้ตลอดเวลาเพื่อให้เข้ากับผู้ใช้งานมากที่สุด</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Benefits -->
            <section id="benefits" class="bg-[#101114] pt-28 pb-20 lg:pt-36 lg:pb-28 text-white relative overflow-hidden">
                <!-- SVG Wave Curve Transition from Purple to Dark -->
                <div class="absolute top-0 left-0 w-full overflow-hidden leading-0 z-0">
                    <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-15 md:h-20" style="fill: #172554;">
                        <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"></path>
                    </svg>
                </div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 observer-target">
                    <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                        <div class="fade-up-enter">
                            <span class="text-sm font-bold tracking-wider text-[#3b82f6] uppercase">สิทธิประโยชน์</span>
                            <h2 class="mt-4 text-3xl md:text-5xl font-extrabold leading-tight" style="font-family: 'Google Sans', sans-serif; letter-spacing: -1px;">
                                เปลี่ยนงานน่าเบื่อ<br /><span class="text-[#3b82f6]">ให้ไวและง่ายกว่าเดิม</span>
                            </h2>
                            <p class="mt-6 text-lg text-[#9497a9] leading-relaxed">
                                เราตั้งใจออกแบบระบบที่ลดขั้นตอนซับซ้อนทิ้งไป เพื่อให้คุณโฟกัสกับเรื่องสำคัญที่สุด
                                นั่นคือความสำเร็จของผู้เรียน
                            </p>

                            <div class="mt-10 space-y-8">
                                <div class="flex gap-4">
                                    <div class="mt-1 w-10 h-10 rounded-xl bg-[var(--ll-blue)] flex items-center justify-center shrink-0">
                                        <x-icon name="check" class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-white">สำหรับครูผู้สอน</h4>
                                        <p class="mt-1 text-[#9497a9] leading-relaxed">ตรวจงานจากคิวได้อัตโนมัติ
                                            ไม่ต้องคลิกเปลี่ยนหน้า จัดการเอกสารและคะแนนอย่างเป็นระบบ ลดเวลาทำสรุปคะแนน
                                        </p>
                                         <a href="{{ route('register') }}" class="inline-block mt-2 text-sm font-semibold text-[#93c5fd] hover:text-white transition-colors">
                                             สมัครใช้งานสำหรับผู้สอน <x-icon name="chevron-right" class="h-4 w-4 ml-1" />
                                         </a>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="mt-1 w-10 h-10 rounded-xl bg-[var(--ll-blue)] flex items-center justify-center shrink-0">
                                        <x-icon name="check" class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-white">สำหรับนักเรียน</h4>
                                        <p class="mt-1 text-[#9497a9] leading-relaxed">เช็กงานค้าง ส่งงาน
                                            และพูดคุยกับเพื่อนได้ทันที รู้สึกเหมือนกำลังเล่นเกมสะสมเลเวล
                                            ให้รางวัลความขยันด้วยตนเอง</p>
                                         <a href="{{ route('register') }}" class="inline-block mt-2 text-sm font-semibold text-[#93c5fd] hover:text-white transition-colors">
                                             สมัครใช้งานสำหรับนักเรียน <x-icon name="chevron-right" class="h-4 w-4 ml-1" />
                                         </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Abstract Mockup representaton -->
                        <div class="fade-up-enter delay-200 relative lg:h-100 rounded-2xl bg-[#1a1b23] border border-[#2a2b36] shadow-2xl overflow-hidden p-8 flex flex-col justify-center items-center">
                            <x-icon name="cube" class="h-24 w-24 text-[#2a2b36] mb-8" />
                            <div class="w-full space-y-4 max-w-sm">
                                <div class="h-10 w-full bg-[#2a2b36] rounded-lg"></div>
                                <div class="flex gap-4">
                                    <div class="h-10 w-2/3 bg-[#2a2b36] rounded-lg"></div>
                                    <div class="h-10 w-1/3 bg-[#1d4ed8] rounded-lg"></div>
                                </div>
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
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--ll-blue)] text-white">
                                <x-icon name="academic-cap" class="h-5 w-5" />
                            </span>
                            <span class="text-2xl font-bold text-white tracking-tight" style="font-family: 'Google Sans', sans-serif;">LiteLearning</span>
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
                            <li><a href="#features" class="text-[#9497a9] hover:text-white transition-colors text-sm">ฟีเจอร์เด่น</a></li>
                            <li><a href="#benefits" class="text-[#9497a9] hover:text-white transition-colors text-sm">ประโยชน์การใช้งาน</a></li>
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
                        <x-icon name="heart" class="h-4 w-4 text-[#e11d48]" />
                        <span>in Thailand</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/countup.js@2.8.0/dist/countUp.umd.js"></script>
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

                        // Trigger CountUp animation for stats
                        if (entry.target.querySelector('#stat-submissions')) {
                            if (typeof countUp !== 'undefined') {
                                const optSub = { suffix: 'k+' };
                                const optCls = { suffix: '+' };
                                const optSat = { decimalPlaces: 1, suffix: '/5' };
                                const optUpt = { decimalPlaces: 1, suffix: '%' };

                                const countSub = new countUp.CountUp('stat-submissions', 10, optSub);
                                const countCls = new countUp.CountUp('stat-classrooms', 500, optCls);
                                const countSat = new countUp.CountUp('stat-satisfaction', 4.9, optSat);
                                const countUpt = new countUp.CountUp('stat-uptime', 99.9, optUpt);

                                if (!countSub.error) countSub.start();
                                if (!countCls.error) countCls.start();
                                if (!countSat.error) countSat.start();
                                if (!countUpt.error) countUpt.start();
                            }
                        }

                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.observer-target').forEach(el => {
                observer.observe(el);
            });

            // Navbar background on scroll
            const navbarCard = document.getElementById('navbar-card');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) {
                    navbarCard.classList.add('shadow-[rgba(0,0,0,0.08)_0px_12px_32px]');
                    navbarCard.classList.remove('border-[#dedee5]');
                    navbarCard.classList.add('border-transparent');
                } else {
                    navbarCard.classList.remove('shadow-[rgba(0,0,0,0.08)_0px_12px_32px]');
                    navbarCard.classList.add('border-[#dedee5]');
                    navbarCard.classList.remove('border-transparent');
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