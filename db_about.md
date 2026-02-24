# LiteLearning — Database Description

> อธิบายตาราง, คอลัมน์, และความสัมพันธ์ใน SQLite database ของ LiteLearning

---

## ภาพรวมโครงสร้าง

```
users ──── classrooms ──── assignments ──── submissions
  │              │               │
  │         classroom_user  attendance_sessions
  │              │               │
  │           topics         quiz_questions ──── quiz_responses
  │
  ├── announcements ──── comments
  ├── attachments
  ├── user_gamifications
  ├── coin_transactions
  ├── achievements ──── user_achievements
  ├── badges ──── user_badges
  ├── store_items ──── user_store_items
  ├── bug_reports
  └── classroom_sidebar_preferences
```

---

## ตารางและคอลัมน์

### `users` — ผู้ใช้งานระบบ

ตารางหลักที่เก็บข้อมูลผู้ใช้ทุกประเภท (admin, teacher, student)

| คอลัมน์               | ประเภท       | คำอธิบาย                                                             |
| --------------------- | ------------ | -------------------------------------------------------------------- |
| `id`                  | PK           | รหัสผู้ใช้เอกลักษณ์                                                  |
| `name`                | varchar      | ชื่อแสดงผลของผู้ใช้                                                  |
| `email`               | varchar (UK) | อีเมลสำหรับ Login, ต้องไม่ซ้ำ                                        |
| `email_verified_at`   | datetime     | เวลาที่ยืนยันอีเมล (null = ยังไม่ยืนยัน)                             |
| `password`            | varchar      | รหัสผ่านที่ hashed ด้วย bcrypt                                       |
| `role`                | varchar      | บทบาท: `admin` / `teacher` / `student` ควบคุมสิทธิ์การเข้าถึงทุกส่วน |
| `avatar`              | varchar      | path ไฟล์รูป avatar ใน storage                                       |
| `cover_image`         | varchar      | path ไฟล์รูป banner โปรไฟล์                                          |
| `bio`                 | text         | คำแนะนำตัว                                                           |
| `locale`              | varchar      | ภาษา UI: `th`, `en` (default: `en`)                                  |
| `theme`               | varchar      | ธีม UI: `light`, `dark`, `system`                                    |
| `ui_scale`            | int          | ขนาด UI (%) เช่น 100 = ปกติ                                          |
| `active_name_color`   | varchar      | code ของ name color ที่ใช้งานอยู่จากร้านค้า                          |
| `active_avatar_frame` | varchar      | code ของ avatar frame ที่ใช้งานอยู่จากร้านค้า                        |
| `is_active`           | boolean      | ถ้า false = บัญชีถูกระงับ, Login ไม่ได้                              |
| `school_name`         | varchar      | ชื่อสถาบันการศึกษา                                                   |
| `study_year`          | varchar      | ชั้นปี/ระดับชั้น                                                     |
| `birth_date`          | date         | วันเกิด                                                              |
| `tos_accepted_at`     | datetime     | เวลายอมรับเงื่อนไขการใช้งาน                                          |
| `setup_completed_at`  | datetime     | เวลาที่ทำ Setup Flow เสร็จสมบูรณ์ (null = ยังไม่ผ่าน setup)          |
| `remember_token`      | varchar      | Token สำหรับ "Remember me"                                           |

---

### `classrooms` — ห้องเรียน

ห้องเรียนเสมือนที่ผู้สอนสร้างและผู้เรียนเข้าร่วม

| คอลัมน์       | ประเภท       | คำอธิบาย                                           |
| ------------- | ------------ | -------------------------------------------------- |
| `id`          | PK           | รหัสห้องเรียน                                      |
| `teacher_id`  | FK → users   | ผู้สอนที่เป็นเจ้าของห้อง                           |
| `name`        | varchar      | ชื่อห้องเรียน                                      |
| `slug`        | varchar (UK) | URL-friendly identifier สำหรับ route (`/c/{slug}`) |
| `section`     | varchar      | ส่วน/กลุ่ม เช่น "ม.4/1"                            |
| `subject`     | varchar      | วิชา เช่น "คณิตศาสตร์"                             |
| `description` | text         | คำอธิบายห้องเรียน                                  |
| `code`        | varchar (UK) | รหัส 6 ตัวอักษรสำหรับนักเรียนใช้เข้าร่วม           |
| `theme_color` | varchar      | สี HEX ของ Header ห้อง (default: `#4F46E5`)        |
| `is_archived` | boolean      | ถ้า true = ห้องถูก Archive แล้ว                    |

---

### `classroom_user` — ความเป็นสมาชิกในห้องเรียน

Pivot table เชื่อม users กับ classrooms สำหรับนักเรียนและ co-teacher

| คอลัมน์        | ประเภท          | คำอธิบาย                              |
| -------------- | --------------- | ------------------------------------- |
| `classroom_id` | FK → classrooms | ห้องเรียนที่เป็นสมาชิก                |
| `user_id`      | FK → users      | ผู้ใช้ที่เป็นสมาชิก                   |
| `role`         | varchar         | บทบาทในห้อง: `student` / `co-teacher` |
| `joined_at`    | datetime        | เวลาที่เข้าร่วมห้อง                   |

---

### `assignments` — ภารกิจ/งาน

งานที่ผู้สอนสร้างภายในห้องเรียน ครอบคลุมหลายประเภท

| คอลัมน์                 | ประเภท          | คำอธิบาย                                                            |
| ----------------------- | --------------- | ------------------------------------------------------------------- |
| `classroom_id`          | FK → classrooms | ห้องเรียนที่ assignment อยู่                                        |
| `user_id`               | FK → users      | ผู้สอนที่สร้าง assignment                                           |
| `title`                 | varchar         | ชื่องาน                                                             |
| `slug`                  | varchar (UK)    | URL identifier                                                      |
| `description`           | text            | คำอธิบาย/โจทย์ (HTML)                                               |
| `attachments`           | text (JSON)     | รายการไฟล์แนบ                                                       |
| `max_score`             | int             | คะแนนเต็ม (default: 100)                                            |
| `due_date`              | datetime        | วันส่งงาน (null = ไม่มีกำหนด)                                       |
| `status`                | varchar         | สถานะ: `draft` / `published` / `closed`                             |
| `type`                  | varchar         | ประเภทงาน: `attendance` / `file` / `question` / `quiz` / `material` |
| `topic`                 | varchar         | หมวดหมู่เนื้อหาภายในห้อง                                            |
| `allow_late_submission` | boolean         | อนุญาตส่งงานล่าช้าหรือไม่                                           |

---

### `submissions` — การส่งงาน

งานที่ผู้เรียนส่งเพื่อตรวจให้คะแนน

| คอลัมน์         | ประเภท           | คำอธิบาย                                                                                               |
| --------------- | ---------------- | ------------------------------------------------------------------------------------------------------ |
| `assignment_id` | FK → assignments | งานที่ submission นี้เป็นของ                                                                           |
| `user_id`       | FK → users       | ผู้เรียนที่ส่ง                                                                                         |
| `content`       | text             | เนื้อหาที่ส่ง (ข้อความ/ลิงก์/ข้อมูล)                                                                   |
| `status`        | varchar          | สถานะ: `assigned` (ยังไม่ส่ง) / `turned_in` (ส่งแล้ว) / `graded` (ให้คะแนนแล้ว) / `returned` (ส่งกลับ) |
| `score`         | int              | คะแนนที่ได้รับ (null = ยังไม่ให้คะแนน)                                                                 |
| `feedback`      | text             | ความเห็น/ข้อแนะนำจากผู้สอน                                                                             |
| `turned_in_at`  | datetime         | เวลาที่ส่งงาน                                                                                          |
| `graded_at`     | datetime         | เวลาที่ให้คะแนน                                                                                        |

---

### `announcements` — ประกาศในห้องเรียน

ข้อความที่ผู้สอนโพสต์ใน Stream ของห้อง

| คอลัมน์        | ประเภท          | คำอธิบาย          |
| -------------- | --------------- | ----------------- |
| `classroom_id` | FK → classrooms | ห้องที่ประกาศอยู่ |
| `user_id`      | FK → users      | ผู้โพสต์          |
| `content`      | text            | เนื้อหาประกาศ     |

---

### `comments` — ความคิดเห็น (Polymorphic)

Comment ที่แนบกับ Model ใดก็ได้ (ปัจจุบันใช้กับ announcements)

| คอลัมน์            | ประเภท     | คำอธิบาย                                                      |
| ------------------ | ---------- | ------------------------------------------------------------- |
| `commentable_type` | varchar    | ชื่อ Model ที่ comment แนบอยู่ เช่น `App\Models\Announcement` |
| `commentable_id`   | int        | ID ของ record ที่ comment แนบอยู่                             |
| `user_id`          | FK → users | ผู้ comment                                                   |
| `content`          | text       | เนื้อหา comment                                               |

---

### `attachments` — ไฟล์แนบ (Polymorphic)

ไฟล์ที่แนบกับ Model ใดก็ได้

| คอลัมน์           | ประเภท     | คำอธิบาย                  |
| ----------------- | ---------- | ------------------------- |
| `attachable_type` | varchar    | ชื่อ Model เจ้าของไฟล์    |
| `attachable_id`   | int        | ID ของ record เจ้าของไฟล์ |
| `file_name`       | varchar    | ชื่อไฟล์ต้นฉบับ           |
| `file_path`       | varchar    | path ในระบบ storage       |
| `file_type`       | varchar    | MIME type                 |
| `file_size`       | int        | ขนาดไฟล์ (bytes)          |
| `uploaded_by`     | FK → users | ผู้อัปโหลด                |

---

### `topics` — หัวข้อเนื้อหา

จัดกลุ่ม assignment ภายในห้องเรียนตามหัวข้อ

| คอลัมน์        | ประเภท          | คำอธิบาย                |
| -------------- | --------------- | ----------------------- |
| `classroom_id` | FK → classrooms | ห้องเรียนที่ topic อยู่ |
| `name`         | varchar         | ชื่อหัวข้อ              |
| `order`        | int             | ลำดับการแสดงผล          |

---

### `quiz_questions` — คำถามใน Quiz

คำถามที่เป็นส่วนหนึ่งของ assignment ประเภท quiz

| คอลัมน์          | ประเภท           | คำอธิบาย                                                            |
| ---------------- | ---------------- | ------------------------------------------------------------------- |
| `assignment_id`  | FK → assignments | Quiz ที่คำถามนี้อยู่ใน                                              |
| `question`       | text             | ข้อความคำถาม                                                        |
| `type`           | varchar          | ประเภท: `multiple_choice` / `true_false` / `short_answer` / `essay` |
| `options`        | text (JSON)      | ตัวเลือก (สำหรับ multiple choice)                                   |
| `correct_answer` | text             | คำตอบที่ถูกต้อง                                                     |
| `points`         | int              | คะแนนของข้อนี้                                                      |
| `order`          | int              | ลำดับในชุดข้อสอบ                                                    |

---

### `quiz_responses` — คำตอบ Quiz ของผู้เรียน

คำตอบที่ผู้เรียนตอบในแต่ละคำถาม

| คอลัมน์            | ประเภท              | คำอธิบาย                            |
| ------------------ | ------------------- | ----------------------------------- |
| `quiz_question_id` | FK → quiz_questions | คำถามที่ตอบ                         |
| `submission_id`    | FK → submissions    | การส่งงานที่ response นี้อยู่ภายใต้ |
| `user_id`          | FK → users          | ผู้ตอบ                              |
| `answer`           | text                | คำตอบที่เลือก/พิมพ์                 |
| `is_correct`       | boolean             | ถูกหรือผิด                          |
| `points_earned`    | int                 | คะแนนที่ได้จากข้อนี้                |

---

### `attendance_sessions` — Session การเช็คชื่อ

Session ที่ผู้สอนเปิดสำหรับให้ผู้เรียนกรอก code เช็คชื่อ

| คอลัมน์           | ประเภท           | คำอธิบาย                                           |
| ----------------- | ---------------- | -------------------------------------------------- |
| `assignment_id`   | FK → assignments | assignment ประเภท attendance ที่ session นี้สังกัด |
| `current_code`    | varchar          | รหัส 6 ตัวที่ใช้เช็คชื่อ ณ ขณะนั้น                 |
| `is_active`       | boolean          | session กำลัง active อยู่หรือไม่                   |
| `started_at`      | datetime         | เวลาเริ่ม session                                  |
| `code_rotated_at` | datetime         | เวลาล่าสุดที่ rotate code                          |

---

### `user_gamifications` — สถิติ Gamification ของผู้ใช้

เก็บ XP, Level, Coins แยกออกจาก users table เพื่อ Separation of Concerns

| คอลัมน์   | ประเภท          | คำอธิบาย                        |
| --------- | --------------- | ------------------------------- |
| `user_id` | FK → users (UK) | ผู้ใช้ที่เป็นเจ้าของ (1:1)      |
| `coins`   | int             | เหรียญสำหรับใช้ซื้อของในร้านค้า |
| `xp`      | int             | ประสบการณ์สะสม ใช้คำนวณ Level   |
| `level`   | int             | ระดับปัจจุบัน (1–100)           |

---

### `achievements` — รายการความสำเร็จ

รายการ achievement ที่ผู้ดูแลระบบกำหนด

| คอลัมน์       | ประเภท       | คำอธิบาย                                                             |
| ------------- | ------------ | -------------------------------------------------------------------- |
| `code`        | varchar (UK) | รหัสเอกลักษณ์ ใช้อ้างอิงใน code เช่น `first_submission`              |
| `name`        | varchar      | ชื่อแสดงผล                                                           |
| `description` | varchar      | คำอธิบายเงื่อนไขปลดล็อก                                              |
| `icon`        | varchar      | ไอคอน (CSS class หรือ emoji)                                         |
| `coin_reward` | int          | Coins ที่ได้รับเมื่อปลดล็อก                                          |
| `xp_reward`   | int          | XP ที่ได้รับ                                                         |
| `is_active`   | boolean      | แสดงหรือซ่อน achievement นี้                                         |
| `target_role` | varchar      | role ที่ได้รับ achievement นี้ได้ (`student`/`teacher`/null = ทุกคน) |

---

### `user_achievements` — Achievement ที่ผู้ใช้ปลดล็อก

| คอลัมน์          | ประเภท            | คำอธิบาย               |
| ---------------- | ----------------- | ---------------------- |
| `user_id`        | FK → users        | ผู้ใช้ที่ปลดล็อก       |
| `achievement_id` | FK → achievements | achievement ที่ปลดล็อก |
| `unlocked_at`    | datetime          | เวลาที่ปลดล็อก         |

---

### `badges` — เหรียญตรา

Badge ที่มอบให้ผู้ใช้ตามเงื่อนไขพิเศษ

| คอลัมน์       | ประเภท       | คำอธิบาย       |
| ------------- | ------------ | -------------- |
| `code`        | varchar (UK) | รหัสเอกลักษณ์  |
| `name`        | varchar      | ชื่อ badge     |
| `description` | varchar      | คำอธิบาย       |
| `icon`        | varchar      | ไอคอน          |
| `color`       | varchar      | สี (HEX)       |
| `target_role` | varchar      | role ที่รับได้ |

---

### `user_badges` — Badge ที่ผู้ใช้ได้รับ

| คอลัมน์     | ประเภท      | คำอธิบาย        |
| ----------- | ----------- | --------------- |
| `user_id`   | FK → users  | ผู้ได้รับ       |
| `badge_id`  | FK → badges | badge ที่ได้รับ |
| `earned_at` | datetime    | เวลาที่ได้รับ   |

---

### `coin_transactions` — ประวัติการได้รับ/ใช้ Coins

Log ทุกการเปลี่ยนแปลง coins เพื่อความโปร่งใส

| คอลัมน์          | ประเภท      | คำอธิบาย                                              |
| ---------------- | ----------- | ----------------------------------------------------- |
| `user_id`        | FK → users  | ผู้ใช้ที่เกี่ยวข้อง                                   |
| `amount`         | int         | จำนวน coins (บวก = ได้รับ, ลบ = ใช้จ่าย)              |
| `type`           | varchar     | `earn` (ได้มา) / `spend` (ใช้ไป)                      |
| `source`         | varchar     | แหล่งที่มา เช่น `level_up`, `achievement`, `purchase` |
| `reference_type` | varchar     | Model ที่อ้างอิง เช่น `App\Models\Achievement`        |
| `reference_id`   | int         | ID ของ record ที่อ้างอิง                              |
| `metadata`       | text (JSON) | ข้อมูลเพิ่มเติมอื่นๆ                                  |
| `happened_at`    | datetime    | เวลาที่เกิด transaction                               |

---

### `store_items` — รายการสินค้าในร้านค้า

ของตกแต่งที่ผู้เรียนซื้อด้วย Coins

| คอลัมน์       | ประเภท       | คำอธิบาย                                                     |
| ------------- | ------------ | ------------------------------------------------------------ |
| `code`        | varchar (UK) | รหัสเอกลักษณ์ เช่น `frame-gold`                              |
| `name`        | varchar      | ชื่อสินค้า                                                   |
| `description` | text         | คำอธิบาย                                                     |
| `type`        | varchar      | ประเภท: `name_color` (สีชื่อ) / `avatar_frame` (กรอบ avatar) |
| `value`       | varchar      | ค่าที่นำไปใช้งาน เช่น CSS color หรือ CSS class               |
| `price`       | int          | ราคาใน coins                                                 |
| `is_active`   | boolean      | แสดงใน store หรือไม่                                         |

---

### `user_store_items` — สินค้าที่ผู้ใช้ซื้อแล้ว

| คอลัมน์         | ประเภท           | คำอธิบาย      |
| --------------- | ---------------- | ------------- |
| `user_id`       | FK → users       | ผู้ซื้อ       |
| `store_item_id` | FK → store_items | สินค้าที่ซื้อ |

---

### `bug_reports` — รายงานข้อผิดพลาด

Bug report และ feedback จากผู้ใช้ถึงผู้ดูแลระบบ

| คอลัมน์       | ประเภท     | คำอธิบาย                                                   |
| ------------- | ---------- | ---------------------------------------------------------- |
| `user_id`     | FK → users | ผู้รายงาน                                                  |
| `title`       | varchar    | หัวข้อรายงาน                                               |
| `description` | text       | รายละเอียด                                                 |
| `status`      | varchar    | สถานะ: `pending` / `in_progress` / `resolved` / `wont_fix` |

---

### `classroom_sidebar_preferences` — การตั้งค่า Sidebar ของผู้ใช้

เก็บ Pin/ลำดับห้องเรียนในแถบด้านข้างของแต่ละผู้ใช้

| คอลัมน์        | ประเภท          | คำอธิบาย                 |
| -------------- | --------------- | ------------------------ |
| `user_id`      | FK → users      | ผู้ใช้                   |
| `classroom_id` | FK → classrooms | ห้องที่ Pin              |
| `is_pinned`    | boolean         | Pin อยู่หรือไม่          |
| `position`     | int             | ลำดับการแสดงผลใน Sidebar |
| `pinned_at`    | datetime        | เวลาที่ Pin              |

---

## สรุปความสัมพันธ์สำคัญ

| ความสัมพันธ์                                     | คำอธิบาย                                            |
| ------------------------------------------------ | --------------------------------------------------- |
| `users` → `classrooms`                           | ผู้สอน 1 คน สร้างได้หลายห้อง                        |
| `classrooms` ↔ `users` (via `classroom_user`)    | นักเรียนหลายคนเข้าร่วมหลายห้องได้                   |
| `classrooms` → `assignments`                     | ห้องเรียนมีงานหลายชิ้น                              |
| `assignments` → `submissions`                    | งาน 1 ชิ้น มีการส่งจากนักเรียนหลายคน                |
| `submissions` ↔ `quiz_responses`                 | ถ้า assignment เป็น quiz, submission จะมี responses |
| `users` → `user_gamifications`                   | 1:1 แยก stats ออกจาก core user data                 |
| `users` → `coin_transactions`                    | log การได้-ใช้ coins ทุกครั้ง                       |
| `users` ↔ `store_items` (via `user_store_items`) | ผู้ใช้ purchase สินค้าได้หลายชิ้น                   |
