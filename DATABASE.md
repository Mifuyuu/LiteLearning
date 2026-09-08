# DATABASE.md

เปรียบเทียบโครงสร้างฐานข้อมูลระหว่างบรานช์ `1.1` (ปัจจุบัน) กับ `main` โดยดูจากสถานะสุดท้ายของแต่ละตาราง (หลัง migration ทั้งหมดรันจบ) ไม่ใช่ diff ของไฟล์ migration ทีละไฟล์ เพราะ `1.1` มีการ consolidate ฐานข้อมูลใหม่ (`2026_03_08_000000_create_all_tables.php`) ไปแล้ว

---

## ภาพรวมการเปลี่ยนแปลงใหญ่

1. **Class Table Inheritance (CTI) สำหรับงานในห้องเรียน** — เพิ่มตาราง `classwork_items` เป็นตารางแม่ ให้ `assignments`, `materials`, `announcements`, `attendance_sessions` เป็นตารางลูกที่ผูกกับ `classwork_item_id` แทนที่จะมี `classroom_id`/`user_id`/`title`/`slug`/`description` แยกกันคนละชุดในแต่ละตาราง เหตุผล: งานเรียนทุกประเภท (ประกาศ/งาน/เอกสาร/เช็คชื่อ) แชร์ฟิลด์เดียวกัน (ห้องเรียนไหน ใครสร้าง หัวข้อไหน ชื่ออะไร ลิงก์อะไร) การรวมไว้ที่เดียวลด duplicate schema และทำให้ list งานทั้งหมดในห้อง query ตารางเดียวได้
2. **ลบระบบ Quiz ทั้งหมด** (`quiz_questions`, `quiz_responses`) และ **ลบ Badge ทั้งหมด** (`badges`, `user_badges`) — รวม Badge เข้ากับ Achievement เป็นระบบเดียว
3. **ลบ Sidebar preference ของห้องเรียน** (`classroom_sidebar_preferences`) — ฟีเจอร์ pin/จัดเรียงห้องเรียนใน sidebar ถูกถอดออก
4. **เพิ่มระบบใหม่**: ธีมห้องเรียนแบบ "ดาวเคราะห์" (`theme_categories`), OTP ยืนยันอีเมล (`email_otp_verifications`), การตั้งค่าระบบแบบ key-value (`settings`), audit log (`audit_logs`)

---

## ตารางที่ถูกลบทั้งตาราง (มีใน main แต่ไม่มีใน 1.1)

| ตาราง | เหตุผลที่ลบ |
|---|---|
| `quiz_questions`, `quiz_responses` | ฟีเจอร์ควิซถูกตัดออกจากสโคปของแอป งานประเภท `type` ใน `assignments` เหลือแค่รูปแบบที่ต้องส่งไฟล์/ตอบคำถามแบบข้อความ ไม่มีข้อสอบแบบเลือกตอบในระบบอีกต่อไป |
| `badges`, `user_badges` | รวมเข้ากับ `achievements`/`user_achievements` (คอมมิต "Merge badges into achievements and cleanup") เพราะทั้งสองระบบทำหน้าที่ซ้ำกัน (ปลดล็อกเหรียญตรา + ให้รางวัล) การมีสองตารางคู่ขนานทำให้โค้ด gamification ต้องเช็กทั้งสองที่ทุกครั้ง |
| `classroom_sidebar_preferences` | ฟีเจอร์ปักหมุด/จัดลำดับห้องเรียนใน sidebar ถูกถอดออกจาก UI (คอมมิต "Remove sidebar/content models") |

## ตารางที่เพิ่มใหม่ทั้งตาราง (มีใน 1.1 แต่ไม่มีใน main)

| ตาราง | เหตุผลที่เพิ่ม |
|---|---|
| `classwork_items` | ตารางแม่ของ CTI ดูรายละเอียดด้านบน — เก็บ `type`, `classroom_id`, `user_id`, `topic_id`, `title`, `slug`, `description`, `published_at` ที่ `assignments`/`materials`/`announcements`/`attendance_sessions` เคยเก็บแยกกัน |
| `materials` | แยกงาน "เอกสาร/สื่อการสอน" ออกจาก `assignments` ให้เป็นประเภทงานของตัวเอง (ก่อนหน้านี้ `type = material` เป็นแค่ค่า enum ใน `assignments`) |
| `theme_categories` | ธีมห้องเรียนแบบเลือก "ดาวเคราะห์" (`planet_key`) พร้อมสีประจำธีม แทนที่ `classrooms.theme_color` เดิมที่เป็น hex code ตายตัว |
| `email_otp_verifications` | รองรับ flow สมัครสมาชิกแบบยืนยันอีเมลด้วย OTP ก่อนสร้าง user จริง (เก็บ `user_data` เป็น JSON ระหว่างรอยืนยัน) |
| `settings` | เก็บค่า config ของระบบแบบ key-value ที่แอดมินแก้ได้ผ่านหน้าเว็บ โดยไม่ต้องแก้ `.env`/deploy ใหม่ |
| `audit_logs` | บันทึกการกระทำสำคัญของผู้ใช้/แอดมินเพื่อตรวจสอบย้อนหลัง (security & accountability) |

---

## รายละเอียดการเปลี่ยนแปลงฟิลด์ในตารางที่มีอยู่แล้วทั้งสองบรานช์

### `users`

| ฟิลด์ | main | 1.1 | หมายเหตุ |
|---|---|---|---|
| `username` | ❌ | ✅ `string(15) unique nullable` | เพิ่มชื่อผู้ใช้แบบสั้นสำหรับแสดงแทนอีเมล/ชื่อจริงในหน้า public (โปรไฟล์, leaderboard) มี backfill สร้างให้ user เก่าอัตโนมัติตอน migrate |
| `bio` | ✅ `text nullable` | ❌ ถูกลบ | ไม่มีใครใช้ฟีเจอร์แนะนำตัวในโปรไฟล์ (คอมมิต "Add classroom soft deletes and remove user bio") |
| `locale` | ✅ `string default 'en'` | ❌ ถูกลบ | ระบบ hard-code เป็นภาษาไทยเสมอ (`SetLocale::handle()`) ฟิลด์นี้ไม่เคยถูกอ่านจริง |
| `theme` | ✅ `string default 'system'` | ❌ ถูกลบ | ไม่เคยมีโค้ดอ้างอิงฟิลด์นี้ (dark/light mode ไม่ได้ทำ) |
| `ui_scale` | ✅ `integer default 100` | ❌ ถูกลบ | ฟีเจอร์ปรับขนาด UI ไม่เคยถูกสร้างจริง |
| `coins`, `xp`, `level` | ✅ (ย้ายออกไป `user_gamifications` ระหว่างทางใน main เอง) | ❌ ไม่เคยอยู่ใน `users` เลยตั้งแต่ base migration | ทั้งสองบรานช์ลงเอยที่จุดเดียวกัน คือเก็บค่าพวกนี้ที่ `user_gamifications` — main ย้ายทีหลังผ่าน migration, ส่วน 1.1 ออกแบบให้แยกไว้ตั้งแต่ base เพื่อไม่ปนข้อมูล "บัญชีผู้ใช้" กับ "สถานะเกม" |
| `school_name`, `study_year`, `birth_date`, `tos_accepted_at`, `setup_completed_at` | ✅ เพิ่มแล้วลบเอง (onboarding flow เดิม) | ❌ ไม่เคยมีในสคีมาที่ consolidate | main ยังเก็บร่องรอย migration เพิ่ม/ลบของ onboarding เดิมไว้ ส่วน 1.1 ตัดฟีเจอร์ onboarding แบบกรอกฟอร์มยาวออกไปแล้วตั้งแต่ base จึงไม่ต้องมีเลย |
| `active_name_color`, `active_avatar_frame` | ✅ ยังอยู่ | ❌ ถูกลบ | ย้ายไปเป็น `user_store_items.is_active` แทน — เดิมเก็บ "ค่าไอเทมที่ active" ซ้ำไว้ที่ `users` (denormalized) พอมี `is_active` บน pivot ตารางแล้วก็ไม่ต้อง sync สองที่ |
| `is_active` | ✅ `boolean default true` | ✅ เหมือนกัน | ใช้ระงับ/แบนบัญชีโดยไม่ต้องลบ |

### `classrooms`

| ฟิลด์ | main | 1.1 | หมายเหตุ |
|---|---|---|---|
| `subject` | ✅ `string nullable` | ❌ ถูกลบ | ไม่ใช้แยกวิชาแล้ว ชื่อห้อง/section พอสื่อความหมายอยู่แล้ว |
| `cover_image` | ✅ เพิ่มแล้วลบเองใน main | ❌ ไม่มีตั้งแต่ base | ทั้งสองบรานช์ตัดสินใจเหมือนกันว่าไม่ต้องมีรูปปกห้องเรียน |
| `theme_color` | ✅ `string default '#4F46E5'` | ❌ ถูกลบ | แทนที่ด้วย `theme_category_id` → สีมาจาก `theme_categories.color` ของธีมที่เลือก ไม่ต้องเก็บ hex ลอยๆ ในห้องเรียนอีก |
| `theme_category_id` | ❌ | ✅ `FK nullable → theme_categories, nullOnDelete` | รองรับระบบเลือกธีม "ดาวเคราะห์" ต่อห้องเรียน |
| `join_enabled` | ❌ | ✅ `boolean default true` | ให้ครูปิดการรับสมัครนักเรียนเข้าห้อง (เช่นห้องเต็ม/ปิดเทอม) โดยไม่ต้องลบรหัสห้อง |
| `deleted_at` (soft delete) | ❌ | ✅ | เพิ่ม soft delete ให้ห้องเรียน กันครูลบห้องพลาดแล้วข้อมูลหายถาวร (คอมมิต "Add classroom soft deletes") |

### `announcements`

| ฟิลด์ | main | 1.1 | หมายเหตุ |
|---|---|---|---|
| `classroom_id`, `user_id`, `title`, `slug` | ✅ (`title`/`slug` ไม่มีใน main ด้วยซ้ำ — main ไม่มี CTI) | ❌ ย้ายไปอยู่ที่ `classwork_items` | ส่วนหนึ่งของการรีแฟกเตอร์ CTI |
| `content` | `text` (not null) | `text nullable` | เปลี่ยนให้ null ได้ เพราะรองรับประกาศที่มีแค่ไฟล์แนบ (`attachments`) โดยไม่มีข้อความ |

### `assignments`

| ฟิลด์ | main | 1.1 | หมายเหตุ |
|---|---|---|---|
| `classroom_id`, `user_id`, `title`, `description`, `slug`, `topic` | ✅ อยู่ตรงนี้ | ❌ ย้ายไป `classwork_items` (`topic` → `topic_id` FK) | CTI refactor |
| `instructions` | ✅ เพิ่มแล้วลบเองใน main (รวมเข้า `description`) | ❌ ไม่เคยมี | รวมเป็นฟิลด์เดียวตั้งแต่ต้นใน 1.1 |
| `attachments` (json) | ✅ | ❌ ถูกลบ | ใช้ตาราง `attachments` แบบ polymorphic (`morphs`) แทน ไม่ต้องเก็บ path ไฟล์เป็น JSON ซ้อนในคอลัมน์เดียว |
| `status` | `enum('draft','published','closed')` | `string` (ไม่ใช่ enum) | เปลี่ยนเป็น string เพื่อรองรับสถานะใหม่ `scheduled` (งานตั้งเวลาโพสต์ล่วงหน้า) โดยไม่ต้องแก้ enum ทุกครั้งที่เพิ่มสถานะ |
| `type` | `enum('attendance','file','question','quiz','material')` default `question` | `string` default `file` | เปลี่ยนเป็น string ตามเหตุผลเดียวกับ `status`; ตัด `quiz` ออก (ไม่มีฟีเจอร์ควิซแล้ว), ตัด `attendance`/`material` ออกเพราะกลายเป็น `classwork_items.type` ระดับบนแทน (attendance → `attendance_sessions`, material → ตาราง `materials`); default เปลี่ยนเป็น `file` เพราะเป็นรูปแบบงานที่ครูใช้บ่อยที่สุด |
| `exp_reward`, `coin_reward` | ❌ | ✅ `unsignedInteger default 0` | ให้ครูกำหนด "รางวัล XP/เหรียญ" ต่องานแยกจากค่า default ของระบบ กระตุ้น gamification ต่องานได้ละเอียดขึ้น |
| `published_at` | ❌ (อยู่คนละที่) | ✅ อยู่ที่ `classwork_items.published_at` | รองรับฟีเจอร์ตั้งเวลาโพสต์งาน (`classwork:publish-scheduled`) |

### `submissions`

| ฟิลด์ | main | 1.1 | หมายเหตุ |
|---|---|---|---|
| `slug` | ❌ | ✅ `string(16) unique` | ให้แต่ละการส่งงานมี URL/identifier สาธารณะของตัวเอง แทนการอ้างด้วย auto-increment id ตรงๆ (สอดคล้องกับ pattern slug ที่ใช้ทั้งระบบ) |

### `attendance_sessions`

| ฟิลด์ | main | 1.1 | หมายเหตุ |
|---|---|---|---|
| `assignment_id` | ✅ `FK → assignments` | ❌ | เปลี่ยนไปผูกกับ `classwork_item_id` แทน เพราะ attendance กลายเป็น `classwork_items.type = 'attendance'` เอง ไม่ใช่ลูกของ `assignments` อีกต่อไป |
| `classwork_item_id` | ❌ | ✅ `unique FK → classwork_items` | ตามข้อบน |

### `achievements`

| ฟิลด์ | main | 1.1 | หมายเหตุ |
|---|---|---|---|
| `icon` | ✅ `string nullable` | ❌ ถูกลบ | แทนที่ด้วย `badge_image` |
| `badge_image` | ❌ | ✅ `string nullable` | เก็บ path รูปเหรียญตราจริง (มาจากการรวม `badges` เข้ากับ `achievements` — `badges` เดิมมีทั้ง `icon` และ `color`) |
| `target_role` | ✅ `string default 'student'` | ❌ ถูกลบ | Gamification ใน 1.1 ใช้เฉพาะนักเรียนเท่านั้นอยู่แล้ว (`GamificationService::isEligible()` เช็กที่โค้ด) ไม่ต้องมีคอลัมน์แยก role ต่อ achievement |

### `user_achievements`

| ฟิลด์ | main | 1.1 | หมายเหตุ |
|---|---|---|---|
| `is_displayed` | ❌ | ✅ `boolean default false` | ให้ผู้ใช้เลือกว่าจะโชว์เหรียญตรานี้บนโปรไฟล์สาธารณะหรือไม่ (ปลดล็อกได้เยอะ แต่โชว์แค่บางอันที่อยากอวด) |

### `user_gamifications`

| ฟิลด์ | main | 1.1 | หมายเหตุ |
|---|---|---|---|
| `pending_celebrations` | ❌ | ✅ `json nullable` | เก็บ event ฉลอง (เลเวลอัป/ปลดล็อกความสำเร็จ) ที่เกิดขึ้นตอนคนอื่นเป็นคน trigger ให้ (เช่นครูให้คะแนนแล้วนักเรียนเลเวลอัป) ไว้โชว์ตอนเจ้าของบัญชีเข้าเว็บครั้งถัดไป แล้วค่อยเคลียร์ทิ้ง |
| index `(level, xp)` | ❌ | ✅ | ใช้เรียง leaderboard เร็วขึ้น |

### `coin_transactions`

| ฟิลด์ | main | 1.1 | หมายเหตุ |
|---|---|---|---|
| `reference` (nullableMorphs → `reference_type`, `reference_id`) | ✅ | ✅ (คอลัมน์เหมือนกัน เขียนแบบ explicit แทน `nullableMorphs`) | ไม่เปลี่ยนพฤติกรรม แค่ประกาศชัดเจนขึ้น |
| `idempotency_key` | ❌ | ✅ `string nullable unique` | กัน transaction เหรียญซ้ำซ้อนเวลามี retry/double-submit (เช่นกด "ส่งงาน" ซ้ำ, queue job รันซ้ำ) — unique constraint บังคับที่ระดับ DB ว่า event เดียวกันให้เหรียญได้ครั้งเดียว |
| index `(user_id, happened_at)` | ❌ | ✅ | ใช้ query ประวัติธุรกรรมของผู้ใช้เรียงตามเวลาบนหน้าโปรไฟล์ |

### `user_store_items`

| ฟิลด์ | main | 1.1 | หมายเหตุ |
|---|---|---|---|
| `is_active` | ❌ | ✅ `boolean` | บอกว่าไอเทม (สีชื่อ/กรอบรูป) ชิ้นไหนที่ผู้ใช้ "สวมใส่อยู่" — แทนที่ `users.active_name_color`/`active_avatar_frame` เดิม ย้าย state มาไว้ที่ pivot table แทนที่จะ denormalize ไว้ที่ `users` |

### `bug_reports`

| ฟิลด์ | main | 1.1 | หมายเหตุ |
|---|---|---|---|
| `admin_reply`, `replied_at`, `read_at` | ❌ | ✅ | รองรับให้แอดมินตอบกลับ bug report ในระบบได้เลย (ไม่ต้องอีเมลนอกระบบ) และ track ว่าผู้ใช้อ่านคำตอบหรือยัง |

---

## ตารางที่ไม่เปลี่ยนแปลง (schema เหมือนกันทั้งสองบรานช์)

`password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `classroom_user`, `topics`, `attachments`, `comments`, `store_items` — เป็น Laravel boilerplate หรือฟีเจอร์ที่ไม่ถูกแตะต้องระหว่างสองบรานช์
