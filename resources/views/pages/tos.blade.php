<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>(Terms of Service) - LiteLearning</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.cdnfonts.com/css/google-sans" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 antialiased tos-google-sans">
    <main class="max-w-5xl mx-auto px-4 py-4 md:py-10">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="bg-white border-b border-gray-200 px-6 md:px-8 py-4 sticky top-0 z-10">
                <div class="flex items-center justify-between gap-3">
                    <h1 class="text-xl md:text-2xl font-bold text-indigo-700">ข้อตกลงและเงื่อนไขการใช้งาน (Terms of Service)</h1>
                    <button
                        type="button"
                        onclick="closeTos()"
                        class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                    >
                        <i class="fa-solid fa-arrow-left mr-2"></i>ย้อนกลับ
                    </button>
                </div>
            </div>

            <div class="p-6 md:p-10 text-gray-800 leading-8">
                <div class="mb-6 rounded-lg bg-blue-50 border border-blue-100 px-4 py-3 text-blue-900 text-sm">
                    <i class="fa-solid fa-circle-info mr-2"></i>
                    <strong>ประกาศสำคัญ:</strong> เอกสารฉบับนี้จัดทำขึ้นเพื่อให้สอดคล้องกับพระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 (PDPA) และกฎหมายไทยที่เกี่ยวข้อง
                </div>

                <h5 class="font-bold text-indigo-700 mt-6">1. บทนำและขอบเขตการให้บริการ</h5>
                <p class="mt-2 text-sm md:text-base">
                    ยินดีต้อนรับสู่ <strong>LiteLearning</strong> ("ผู้ให้บริการ", "เรา") แพลตฟอร์มบริหารจัดการห้องเรียนรูปแบบ Gamification
                    การเข้าใช้งานเว็บไซต์หรือแอปพลิเคชันนี้ ถือว่าท่าน ("ผู้ใช้บริการ", "ท่าน") ได้อ่าน เข้าใจ และตกลงที่จะผูกพันตามข้อตกลงและเงื่อนไขฉบับนี้อย่างสมบูรณ์
                    หากท่านไม่ตกลงตามเงื่อนไขข้อใดข้อหนึ่ง กรุณาระงับการใช้งานทันที
                </p>

                <h5 class="font-bold text-indigo-700 mt-6">2. การเก็บรวบรวมและใช้ข้อมูลส่วนบุคคล (PDPA Compliance)</h5>
                <p class="mt-2 text-sm md:text-base">
                    เพื่อให้เป็นไปตาม <strong>พระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 (PDPA)</strong> เราขอแจ้งให้ท่านทราบถึงรายละเอียดการประมวลผลข้อมูลดังนี้:
                </p>
                <ul class="mt-2 pl-5 list-disc text-sm md:text-base space-y-2">
                    <li>
                        <strong>2.1 ฐานประมวลผลข้อมูล:</strong> เราประมวลผลข้อมูลของท่านภายใต้ฐานสัญญา (Contractual Basis) เพื่อการให้บริการตามจุดประสงค์ของแพลตฟอร์ม และฐานความยินยอม (Consent) สำหรับกรณีที่กฎหมายกำหนด
                    </li>
                    <li>
                        <strong>2.2 ข้อมูลที่จัดเก็บ:</strong>
                        <ul class="mt-1 pl-5 list-disc space-y-1">
                            <li>ข้อมูลระบุตัวตน: ชื่อ, นามสกุล, อีเมล (Email Address)</li>
                            <li>ข้อมูลการใช้งาน: Username, Password (เข้ารหัส), สถานะผู้ใช้งาน (ครู/นักเรียน)</li>
                            <li>ข้อมูลกิจกรรม: ประวัติการเข้าเรียน, คะแนน (XP), ระดับ (Level), การทำภารกิจ, และบันทึกกิจกรรม (Activity Logs)</li>
                            <li>ข้อมูลทางเทคนิค: IP Address, Browser Type, Cookies</li>
                        </ul>
                    </li>
                    <li>
                        <strong>2.3 วัตถุประสงค์การใช้ข้อมูล:</strong>
                        <ul class="mt-1 pl-5 list-disc space-y-1">
                            <li>เพื่อยืนยันตัวตนในการเข้าสู่ระบบ</li>
                            <li>เพื่อคำนวณและแสดงผลความก้าวหน้าในรูปแบบ Gamification (Leaderboard, Achievements)</li>
                            <li>เพื่อวิเคราะห์และปรับปรุงประสิทธิภาพของระบบ</li>
                            <li>เพื่อการสื่อสารแจ้งเตือนเกี่ยวกับกิจกรรมในห้องเรียน</li>
                        </ul>
                    </li>
                </ul>

                <h5 class="font-bold text-indigo-700 mt-6">3. สิทธิของเจ้าของข้อมูลส่วนบุคคล</h5>
                <p class="mt-2 text-sm md:text-base">ตามกฎหมาย PDPA ท่านมีสิทธิดังต่อไปนี้:</p>
                <ul class="mt-2 pl-5 list-disc text-sm md:text-base space-y-1">
                    <li><strong>สิทธิในการเข้าถึง:</strong> ขอรับสำเนาข้อมูลส่วนบุคคลของท่านที่เราเก็บรักษาไว้</li>
                    <li><strong>สิทธิในการแก้ไข:</strong> ขอให้แก้ไขข้อมูลที่ไม่ถูกต้องหรือไม่เป็นปัจจุบัน</li>
                    <li><strong>สิทธิในการลบ:</strong> ขอให้ลบหรือทำลายข้อมูลส่วนบุคคลของท่าน (Right to be forgotten) เมื่อหมดความจำเป็น</li>
                    <li><strong>สิทธิในการคัดค้าน:</strong> คัดค้านการเก็บรวบรวม ใช้ หรือเปิดเผยข้อมูล</li>
                    <li><strong>สิทธิในการถอนความยินยอม:</strong> ท่านสามารถถอนความยินยอมได้ตลอดเวลา (อาจส่งผลต่อการใช้งานบางฟีเจอร์)</li>
                </ul>
                <p class="mt-2 text-xs text-gray-500">
                    * ท่านสามารถใช้สิทธิผ่านเมนูการตั้งค่าในระบบ หรือติดต่อผู้ดูแลระบบผ่านช่องทางที่ระบุไว้
                </p>

                <h5 class="font-bold text-indigo-700 mt-6">4. การเปิดเผยข้อมูลแก่บุคคลภายนอก</h5>
                <p class="mt-2 text-sm md:text-base">เราจะไม่เปิดเผยข้อมูลส่วนบุคคลของท่านแก่บุคคลภายนอก เว้นแต่:</p>
                <ul class="mt-2 pl-5 list-disc text-sm md:text-base space-y-1">
                    <li>ได้รับความยินยอมจากท่านอย่างชัดแจ้ง</li>
                    <li>เป็นการปฏิบัติตามคำสั่งศาล หรือคำสั่งของเจ้าหน้าที่รัฐที่มีอำนาจตามกฎหมาย</li>
                    <li>เป็นการเปิดเผยในลักษณะข้อมูลภาพรวม (Aggregated Data) ที่ไม่สามารถระบุตัวตนได้เพื่อการวิจัยทางการศึกษา</li>
                </ul>

                <h5 class="font-bold text-indigo-700 mt-6">5. เนื้อหาและทรัพย์สินทางปัญญา</h5>
                <ul class="mt-2 pl-5 list-disc text-sm md:text-base space-y-1">
                    <li>เนื้อหาที่ท่านสร้างขึ้นในระบบ (เช่น การบ้าน, ข้อความในฟอรั่ม) ยังคงเป็นลิขสิทธิ์ของท่าน แต่ท่านอนุญาตให้เราใช้เพื่อการแสดงผลในห้องเรียนได้</li>
                    <li>องค์ประกอบของระบบ LiteLearning (โลโก้, กราฟิก, โค้ด) เป็นทรัพย์สินทางปัญญาของผู้ให้บริการ ห้ามทำซ้ำ ดัดแปลง หรือนำไปใช้เชิงพาณิชย์โดยไม่ได้รับอนุญาต</li>
                </ul>

                <h5 class="font-bold text-indigo-700 mt-6">6. ข้อพึงปฏิบัติและข้อห้าม</h5>
                <p class="mt-2 text-sm md:text-base">ท่านตกลงที่จะไม่กระทำการดังต่อไปนี้:</p>
                <ul class="mt-2 pl-5 list-disc text-sm md:text-base space-y-1">
                    <li>โพสต์เนื้อหาที่ผิดกฎหมาย ลามกอนาจาร หมิ่นประมาท หรือสร้างความเกลียดชัง</li>
                    <li>พยายามเจาะระบบ (Hacking) หรือรบกวนการทำงานของ Server</li>
                    <li>ใช้ระบบเพื่อการโฆษณาหรือสแปม (Spamming)</li>
                </ul>
                <p class="mt-2 text-xs text-red-600">
                    * หากตรวจพบการฝ่าฝืน เราขอสงวนสิทธิ์ในการระงับบัญชีของท่านทันทีโดยไม่ต้องแจ้งล่วงหน้า
                </p>

                <h5 class="font-bold text-indigo-700 mt-6">7. การจำกัดความรับผิด</h5>
                <p class="mt-2 text-sm md:text-base">
                    LiteLearning ให้บริการในลักษณะ "ตามสภาพ" (As is) เราไม่รับรองว่าบริการจะปราศจากข้อผิดพลาดหรือความล่าช้า
                    และไม่รับผิดชอบต่อความเสียหายใดๆ ที่เกิดจากการใช้งาน หรือการสูญหายของข้อมูล (แม้เราจะมีมาตรการสำรองข้อมูลตามมาตรฐานก็ตาม)
                </p>

                <h5 class="font-bold text-indigo-700 mt-6">8. กฎหมายที่ใช้บังคับ</h5>
                <p class="mt-2 text-sm md:text-base">
                    ข้อตกลงนี้อยู่ภายใต้บังคับของ <strong>กฎหมายแห่งราชอาณาจักรไทย</strong> และให้ศาลไทยเป็นศาลที่มีเขตอำนาจในการพิจารณาข้อพิพาทที่เกิดขึ้น
                </p>

                <hr class="my-8 border-gray-200">

                <div class="text-center text-sm md:text-base">
                    <p class="mb-4">
                        หากท่านมีข้อสงสัยเกี่ยวกับข้อตกลงนี้ หรือต้องการใช้สิทธิเกี่ยวกับข้อมูลส่วนบุคคล<br>
                        กรุณาติดต่อ: <a href="mailto:admin@gamiclass.com" class="text-indigo-600 hover:text-indigo-500">admin@gamiclass.com</a>
                    </p>
                    <button
                        type="button"
                        onclick="closeTos()"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-2.5 text-white hover:bg-indigo-700"
                    >
                        <i class="fa-solid fa-circle-check mr-2"></i>รับทราบและกลับสู่หน้าก่อนหน้า
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        function closeTos() {
            if (window.opener) {
                window.close();
                return;
            }

            if (window.history.length > 1) {
                window.history.back();
                return;
            }

            window.location.href = '{{ route('setup') }}';
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap');
        .tos-google-sans {
            font-family: 'Google Sans', 'Noto Sans Thai', 'Noto Sans', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
        }
    </style>
</body>
</html>
