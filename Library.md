# ภาคผนวก: Library ที่ใช้ในโครงงาน (LiteLearning)

โครงงานนี้พัฒนาด้วย **Laravel 12** (PHP) เป็น Backend framework หลัก ร่วมกับ **Livewire 4** สำหรับสร้าง UI แบบ Interactive โดยไม่ต้องเขียน JavaScript framework แยกต่างหาก และใช้ **Vite** เป็นตัวจัดการ Asset ฝั่ง Frontend (CSS/JS)

การติดตั้ง Library ทั้งหมดสามารถทำได้ในคำสั่งเดียวโดยใช้ไฟล์ `composer.json` (ฝั่ง PHP) และ `package.json` (ฝั่ง JavaScript) ที่ project กำหนดเวอร์ชันไว้อยู่แล้ว:

```bash
# ติดตั้ง PHP library ทั้งหมดตาม composer.json / composer.lock
composer install

# ติดตั้ง JavaScript library ทั้งหมดตาม package.json / package-lock.json
npm install
```

รายละเอียดด้านล่างคือ Library แต่ละตัวที่ใช้งานจริงในโค้ด พร้อมหน้าที่และคำสั่งติดตั้งเฉพาะตัว (กรณีต้องการติดตั้งเพิ่มทีละตัว)

---

## 1. PHP Library (จัดการผ่าน Composer)

### 1.1 laravel/framework (^12.0)
Framework หลักของโครงงาน ทำหน้าที่เป็นแกนกลางของระบบทั้งหมด ได้แก่ Routing, ORM (Eloquent) สำหรับติดต่อฐานข้อมูล, Authentication, Validation, Queue, Migration และโครงสร้าง MVC
```bash
composer require laravel/laravel
```

### 1.2 livewire/livewire (^4.1)
ใช้สร้างหน้าเว็บแบบ Interactive (Reactive Components) โดยเขียนด้วย PHP ล้วน ไม่ต้องเขียน API แยกและ JavaScript SPA framework เช่น Vue/React ในโครงงานนี้ใช้ Livewire ควบคุมเกือบทุกหน้า เช่น ระบบห้องเรียน (Classroom), งานที่มอบหมาย (Assignment), เอกสารประกอบการเรียน (Material), หน้าตั้งค่าผู้ใช้ (Settings) และระบบ Auth
```bash
composer require livewire/livewire
```

### 1.3 mews/purifier (^3.4)
ใช้กรอง (Sanitize) เนื้อหา HTML ที่ผู้ใช้กรอกผ่าน Rich Text Editor (เช่น เนื้อหางาน/เอกสารที่ครูสร้าง) ก่อนบันทึกและแสดงผล เพื่อป้องกันการโจมตีแบบ XSS (Cross-Site Scripting) จากสคริปต์อันตรายที่แฝงมากับ HTML
```bash
composer require mews/purifier
```

### 1.4 league/flysystem-aws-s3-v3 (^3.31)
Driver สำหรับให้ Laravel Filesystem เชื่อมต่อกับ Storage แบบ S3-compatible (เช่น Amazon S3 หรือ MinIO ที่รันเองในเครื่อง) ใช้สำหรับจัดเก็บไฟล์แนบ (Attachment) ที่ครูและนักเรียนอัปโหลด เช่น ไฟล์งาน เอกสารประกอบการสอน และรูปโปรไฟล์
```bash
composer require league/flysystem-aws-s3-v3
```

### 1.5 laravel/tinker (^2.10)
เครื่องมือ REPL (Interactive Shell) สำหรับรันโค้ด PHP/Eloquent ทดสอบข้อมูลผ่าน command line โดยไม่ต้องเขียนหน้าเว็บทดสอบ
```bash
composer require laravel/tinker
```

### เครื่องมือสำหรับพัฒนา (require-dev)
ใช้เฉพาะตอนพัฒนา/ทดสอบ ไม่ถูกนำไปใช้งานจริงบน Production
| Library | หน้าที่ |
|---|---|
| `laravel/pint` | จัดรูปแบบโค้ด PHP (Code Formatter) ให้เป็นมาตรฐานเดียวกัน |
| `laravel/pail` | ดู Log แบบ real-time ผ่าน command line ตอนพัฒนา |
| `laravel/sail` | สภาพแวดล้อมพัฒนาแบบ Docker |
| `phpunit/phpunit` | เฟรมเวิร์กสำหรับเขียนและรัน Unit/Feature Test |
| `mockery/mockery` | สร้าง Mock Object ประกอบการเขียนเทสต์ |
| `fakerphp/faker` | สร้างข้อมูลตัวอย่าง (Dummy Data) สำหรับ Seeder/Test |
| `nunomaduro/collision` | แสดงผล Error ใน command line ให้อ่านง่ายขึ้น |

ติดตั้งแบบ dev-dependency: `composer require --dev <package-name>`

---

## 2. JavaScript Library (จัดการผ่าน npm)

### 2.1 vite (^7.0) และ laravel-vite-plugin (^2.0)
Vite คือเครื่องมือ Build/Bundle ไฟล์ CSS และ JavaScript ให้ทำงานเร็วในระหว่างพัฒนา (Hot Module Reload) และบีบอัดไฟล์ให้พร้อมใช้งานจริงตอน build ส่วน `laravel-vite-plugin` คือตัวเชื่อม Vite เข้ากับ Laravel ให้เรียกใช้ asset ผ่าน Blade directive ได้
```bash
npm install --save-dev vite laravel-vite-plugin
```

### 2.2 tailwindcss (^4.0) และ @tailwindcss/vite (^4.0)
Framework CSS แบบ Utility-first ใช้จัดหน้าตา (Layout, สี, ระยะห่าง, Responsive) ของทั้งระบบแทนการเขียน CSS เอง ส่วน `@tailwindcss/vite` คือ plugin ที่ให้ Vite ประมวลผล Tailwind โดยตรง
```bash
npm install --save-dev tailwindcss @tailwindcss/vite
```

### 2.3 daisyui (^5.5)
Plugin เสริมของ Tailwind ที่มี Component สำเร็จรูป (เช่น card, badge, avatar, tooltip, skeleton, toggle, checkbox) ช่วยลดเวลาการออกแบบ UI element ที่ใช้ซ้ำในหลายหน้า
```bash
npm install --save-dev daisyui
```

### 2.4 @tailwindcss/typography (^0.5)
Plugin เสริมของ Tailwind สำหรับจัดรูปแบบข้อความเนื้อหายาว (เช่น เนื้อหาบทเรียน/งานที่ผ่านการแก้ไขจาก Rich Text Editor) ให้อ่านง่าย มีระยะบรรทัดและหัวข้อที่เหมาะสมโดยอัตโนมัติ
```bash
npm install @tailwindcss/typography
```

### 2.5 @tiptap/* (core, starter-kit, extension-link, extension-placeholder, extension-text-align, extension-underline) (^3.20)
ชุด Library สำหรับสร้าง Rich Text Editor (พิมพ์ตัวหนา ตัวเอียง จัดย่อหน้า ใส่ลิงก์ ฯลฯ) ใช้ในหน้าสร้าง/แก้ไขเนื้อหางาน (Assignment) และเอกสารประกอบการเรียน (Material) ให้ครูพิมพ์เนื้อหาแบบมีสไตล์ได้เหมือนโปรแกรมพิมพ์เอกสารทั่วไป
```bash
npm install @tiptap/core @tiptap/starter-kit @tiptap/extension-link @tiptap/extension-placeholder @tiptap/extension-text-align @tiptap/extension-underline
```

### 2.6 cropperjs (^1.6)
Library สำหรับครอบตัดรูปภาพ (Image Cropping) บนหน้าเว็บ ใช้ในหน้าตั้งค่าโปรไฟล์ผู้ใช้ (Settings) ให้ผู้ใช้ปรับขนาด/ครอบรูปโปรไฟล์ก่อนอัปโหลด
```bash
npm install cropperjs
```

### 2.7 axios (^1.11)
Library สำหรับเรียก HTTP Request (AJAX) จากฝั่ง JavaScript ไปยัง Backend มาพร้อมกับไฟล์ตั้งต้นของ Laravel (`resources/js/bootstrap.js`)
```bash
npm install axios
```

### 2.8 concurrently (^9.0)
เครื่องมือช่วยรันหลายคำสั่ง (เช่น `php artisan serve`, `queue:listen`, `npm run dev`) พร้อมกันในหน้าต่าง terminal เดียวตอนพัฒนา ผ่านคำสั่ง `composer dev`
```bash
npm install --save-dev concurrently
```

---

## สรุปคำสั่งติดตั้งทั้งโครงงาน

```bash
git clone <repository-url>
cd LiteLearning

composer install        # ติดตั้ง PHP library ทั้งหมด
npm install              # ติดตั้ง JavaScript library ทั้งหมด

cp .env.example .env
php artisan key:generate

php artisan migrate

npm run build             # หรือ npm run dev สำหรับโหมดพัฒนา
```
