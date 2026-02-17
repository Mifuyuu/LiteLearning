<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LiteLearning</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500;700&family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <header class="sticky top-0 z-20 border-b border-gray-200 bg-white/95 backdrop-blur">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <a href="{{ route('landing') }}" class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm">
                        <i class="fas fa-graduation-cap"></i>
                    </span>
                    <span class="text-lg font-bold text-gray-900">LiteLearning</span>
                </a>

                <nav class="hidden md:flex items-center gap-6 text-sm text-gray-600">
                    <a href="#features" class="hover:text-gray-900">ฟีเจอร์</a>
                    <a href="#benefits" class="hover:text-gray-900">ประโยชน์</a>
                    <a href="#pricing" class="hover:text-gray-900">แผนใช้งาน</a>
                </nav>

                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-right-to-bracket mr-2"></i>เข้าสู่ระบบ</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"><i class="fa-solid fa-rocket mr-2"></i>เริ่มใช้งานฟรี</a>
                </div>

                <button id="mobile-menu-toggle" type="button" aria-label="Toggle menu" class="inline-flex md:hidden h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 relative">
                    <span class="hamburger-icon">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </span>
                </button>
            </div>

            <div id="mobile-menu-panel" class="md:hidden overflow-hidden max-h-0 opacity-0 -translate-y-1 transition-all duration-200 ease-out border-t border-gray-200 bg-white">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                    <nav class="flex flex-col gap-1 text-sm text-gray-700">
                        <a href="#features" data-menu-close class="rounded-lg px-3 py-2 hover:bg-gray-50"><i class="fa-solid fa-cubes mr-2"></i>ฟีเจอร์</a>
                        <a href="#benefits" data-menu-close class="rounded-lg px-3 py-2 hover:bg-gray-50"><i class="fa-solid fa-book mr-2"></i>วิธีใช้งาน</a>
                        <a href="#pricing" data-menu-close class="rounded-lg px-3 py-2 hover:bg-gray-50"><i class="fa-solid fa-layer-group mr-2"></i>แผนใช้งาน</a>
                    </nav>

                    <div class="mt-3 flex flex-col gap-2">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-right-to-bracket mr-2"></i>เข้าสู่ระบบ</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"><i class="fa-solid fa-user-plus mr-2"></i>สมัครสมาชิก</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-12">
                <div class="grid lg:grid-cols-2 gap-10 items-center">
                    <div>
                        <span class="inline-flex items-center rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">สร้างมาเพื่อห้องเรียนยุคใหม่</span>
                        <h1 class="mt-5 text-4xl md:text-5xl font-bold tracking-tight text-gray-900 leading-tight">
                            แพลตฟอร์มเดียวสำหรับ <span class="text-indigo-600">การสอน งานมอบหมาย และความก้าวหน้าของผู้เรียน</span>
                        </h1>
                        <p class="mt-5 text-base md:text-lg text-gray-600 leading-8">
                            จัดการห้องเรียน สั่งงาน ตรวจงาน และกระตุ้นการเรียนรู้ด้วยระบบเกมมิฟิเคชัน โดยไม่ต้องสลับหลายเครื่องมือ
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"><i class="fa-solid fa-circle-plus mr-2"></i>สร้างพื้นที่เรียนของคุณ</a>
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-right-to-bracket mr-2"></i>เข้าสู่ระบบ</a>
                        </div>
                        <div class="mt-8 grid grid-cols-3 gap-4 text-sm">
                            <div class="rounded-lg border border-gray-200 bg-white px-4 py-3">
                                <p class="text-2xl font-bold text-gray-900">10k+</p>
                                <p class="text-gray-500">งานที่ส่ง</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white px-4 py-3">
                                <p class="text-2xl font-bold text-gray-900">500+</p>
                                <p class="text-gray-500">ห้องเรียน</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white px-4 py-3">
                                <p class="text-2xl font-bold text-gray-900">99.9%</p>
                                <p class="text-gray-500">ความพร้อมใช้งาน</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 md:p-8 shadow-sm">
                        <p class="text-sm font-semibold text-indigo-600">ทำไมหลายสถาบันเลือก LiteLearning</p>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-7 w-7 items-center justify-center rounded-md bg-indigo-100 text-indigo-700"><i class="fas fa-layer-group text-xs"></i></span>
                                <div>
                                    <p class="font-semibold text-gray-900">ครบจบในแพลตฟอร์มเดียว</p>
                                    <p class="text-sm text-gray-600">ตั้งแต่ประกาศไปจนถึงให้คะแนน ทุกขั้นตอนอยู่ในแดชบอร์ดเดียว</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-7 w-7 items-center justify-center rounded-md bg-indigo-100 text-indigo-700"><i class="fas fa-bolt text-xs"></i></span>
                                <div>
                                    <p class="font-semibold text-gray-900">เริ่มใช้งานได้รวดเร็ว</p>
                                    <p class="text-sm text-gray-600">ครูและนักเรียนตั้งค่าโปรไฟล์แล้วเริ่มได้ภายในไม่กี่นาที</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-7 w-7 items-center justify-center rounded-md bg-indigo-100 text-indigo-700"><i class="fas fa-shield-alt text-xs"></i></span>
                                <div>
                                    <p class="font-semibold text-gray-900">คำนึงถึงความเป็นส่วนตัว</p>
                                    <p class="text-sm text-gray-600">ข้อกำหนดชัดเจน ควบคุมสิทธิ์เข้าถึง และเรียนรู้อย่างปลอดภัย</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="features" class="border-y border-gray-200 bg-white">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold text-indigo-600">ฟีเจอร์เด่น</p>
                        <h2 class="mt-2 text-3xl font-bold text-gray-900">ทุกอย่างที่ต้องใช้เพื่อขับเคลื่อนห้องเรียนอย่างมีส่วนร่วม</h2>
                        <p class="mt-3 text-gray-600">ออกแบบมาเพื่อการใช้งานจริงทุกวันสำหรับครูและนักเรียน</p>
                    </div>

                    <div class="mt-8 grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                        <article class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                            <h3 class="font-semibold text-gray-900">จัดการห้องเรียน</h3>
                            <p class="mt-2 text-sm text-gray-600">สร้างห้อง เชิญนักเรียน และจัดทุกอย่างให้อยู่ในที่เดียว</p>
                        </article>
                        <article class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                            <h3 class="font-semibold text-gray-900">งานและการให้คะแนน</h3>
                            <p class="mt-2 text-sm text-gray-600">มอบหมายงาน ตรวจงาน และส่งคะแนนพร้อมข้อเสนอแนะได้รวดเร็ว</p>
                        </article>
                        <article class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                            <h3 class="font-semibold text-gray-900">ระบบเกมมิฟิเคชัน</h3>
                            <p class="mt-2 text-sm text-gray-600">เพิ่มแรงจูงใจด้วย XP เลเวล แบดจ์ และความสำเร็จ</p>
                        </article>
                        <article class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                            <h3 class="font-semibold text-gray-900">ปฏิทินและกำหนดส่ง</h3>
                            <p class="mt-2 text-sm text-gray-600">ติดตามกำหนดส่งและกิจกรรมที่จะมาถึงด้วยมุมมองที่ชัดเจน</p>
                        </article>
                        <article class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                            <h3 class="font-semibold text-gray-900">ประสบการณ์ตามบทบาท</h3>
                            <p class="mt-2 text-sm text-gray-600">แยก workflow สำหรับครูและนักเรียนตั้งแต่วันแรก</p>
                        </article>
                        <article class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                            <h3 class="font-semibold text-gray-900">รองรับหลายภาษา</h3>
                            <p class="mt-2 text-sm text-gray-600">รองรับไทยและอังกฤษให้เหมาะกับบริบทของสถาบัน</p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="benefits" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <div class="grid lg:grid-cols-2 gap-8">
                    <div class="rounded-2xl border border-gray-200 bg-white p-7">
                        <p class="text-sm font-semibold text-indigo-600">สำหรับครูผู้สอน</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">ลดเวลางานซ้ำ เพิ่มเวลาสอน</h3>
                        <ul class="mt-4 space-y-2 text-sm text-gray-600 list-disc pl-5">
                            <li>ใช้เทมเพลตงานซ้ำและจัดหมวดหมู่ตามหัวข้อ</li>
                            <li>ตรวจงานที่ค้างได้รวดเร็วจากคิวเดียว</li>
                            <li>ประกาศอัปเดตถึงผู้เรียนได้ทันทีในสตรีมห้องเรียน</li>
                        </ul>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-7">
                        <p class="text-sm font-semibold text-indigo-600">สำหรับนักเรียน</p>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900">ติดตามงานง่าย และมีแรงจูงใจต่อเนื่อง</h3>
                        <ul class="mt-4 space-y-2 text-sm text-gray-600 list-disc pl-5">
                            <li>เห็นงานและกำหนดส่งทั้งหมดได้ในหน้าเดียว</li>
                            <li>เข้าใจความก้าวหน้าผ่านคะแนนและความสำเร็จ</li>
                            <li>ส่งงานได้มั่นใจพร้อมสถานะที่ชัดเจน</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="pricing" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
                <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-8 text-center">
                    <p class="text-sm font-semibold text-indigo-700">แผนที่เรียบง่าย</p>
                    <h2 class="mt-2 text-3xl font-bold text-gray-900">เริ่มฟรี แล้วเติบโตไปพร้อมห้องเรียนของคุณ</h2>
                    <p class="mt-3 text-gray-600">ไม่ต้องตั้งค่าซับซ้อน สมัครแล้วเริ่มห้องเรียนแรกได้ทันที</p>
                    <div class="mt-6 flex flex-wrap justify-center gap-3">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"><i class="fa-solid fa-user-plus mr-2"></i>เริ่มใช้งานตอนนี้</a>
                        <a href="{{ route('tos') }}" class="inline-flex items-center justify-center rounded-lg border border-indigo-300 px-6 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-white"><i class="fa-solid fa-file-contract mr-2"></i>อ่านข้อกำหนด</a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-gray-200 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-gray-900">LiteLearning</p>
                    <p class="text-sm text-gray-500">แพลตฟอร์มการเรียนการสอนสมัยใหม่สำหรับครูและนักเรียน</p>
                </div>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <a href="{{ route('tos') }}" class="hover:text-gray-900">ข้อกำหนด</a>
                    <a href="{{ route('login') }}" class="hover:text-gray-900">เข้าสู่ระบบ</a>
                    <a href="{{ route('register') }}" class="hover:text-gray-900">สมัครสมาชิก</a>
                </div>
                <p class="text-sm text-gray-400">© {{ now()->year }} LiteLearning</p>
            </div>
        </footer>
    </div>

    <style>
        html {
            scroll-behavior: smooth;
        }

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

    <script>
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
                panel.classList.remove('max-h-96', 'opacity-100', 'translate-y-0');
                panel.classList.add('max-h-0', 'opacity-0', '-translate-y-1');
                toggleIcon(false);
            };

            const openMenu = () => {
                panel.classList.remove('max-h-0', 'opacity-0', '-translate-y-1');
                panel.classList.add('max-h-96', 'opacity-100', 'translate-y-0');
                toggleIcon(true);
            };

            toggle.addEventListener('click', () => {
                if (panel.classList.contains('max-h-0')) {
                    openMenu();
                    return;
                }

                closeMenu();
            });

            panel.querySelectorAll('[data-menu-close]').forEach((link) => {
                link.addEventListener('click', closeMenu);
            });
        })();
    </script>
</body>
</html>
