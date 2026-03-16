# พจนานุกรมข้อมูล (Data Dictionary)

ระบบ LiteLearning — Learning Management System  
ฐานข้อมูล: MySQL (production) / SQLite (testing)

---

## ตาราง users

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสผู้ใช้งาน (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | name | ชื่อผู้ใช้งาน | 255 | VARCHAR | - |
| 3 | email | อีเมลผู้ใช้งาน | 255 | VARCHAR | UQ |
| 4 | email_verified_at | วันเวลาที่ยืนยันอีเมล | - | TIMESTAMP | - |
| 5 | password | รหัสผ่าน (hashed) | 255 | VARCHAR | - |
| 6 | role | บทบาทของผู้ใช้งาน (admin, teacher, student) | - | ENUM | IDX |
| 7 | is_active | สถานะเปิดใช้งานบัญชี | - | BOOLEAN | - |
| 8 | setup_completed_at | วันเวลาที่ตั้งค่าโปรไฟล์เสร็จสมบูรณ์ | - | TIMESTAMP | - |
| 9 | avatar | path ไฟล์รูปโปรไฟล์บน S3 | 255 | VARCHAR | - |
| 10 | cover_image | path ไฟล์รูปหน้าปกบน S3 | 255 | VARCHAR | - |
| 11 | bio | ข้อมูลแนะนำตัวผู้ใช้งาน | - | TEXT | - |
| 12 | active_name_color | code สีชื่อที่เปิดใช้งานอยู่ | 255 | VARCHAR | - |
| 13 | active_avatar_frame | code กรอบ avatar ที่เปิดใช้งานอยู่ | 255 | VARCHAR | - |
| 14 | remember_token | token สำหรับ Remember Me | 100 | VARCHAR | - |
| 15 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 16 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง password_reset_tokens

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | email | อีเมลที่ขอรีเซ็ตรหัสผ่าน | 255 | VARCHAR | PK |
| 2 | token | token สำหรับรีเซ็ตรหัสผ่าน | 255 | VARCHAR | - |
| 3 | created_at | วันเวลาที่สร้าง token | - | TIMESTAMP | - |

---

## ตาราง sessions

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัส session | 255 | VARCHAR | PK |
| 2 | user_id | รหัสผู้ใช้งาน (อาจเป็น null สำหรับ guest) | 8 bytes | BIGINT UNSIGNED | IDX, FK → users |
| 3 | ip_address | IP address ของผู้ใช้งาน | 45 | VARCHAR | - |
| 4 | user_agent | User-Agent ของเบราว์เซอร์ | - | TEXT | - |
| 5 | payload | ข้อมูล session (serialized) | - | LONGTEXT | - |
| 6 | last_activity | Unix timestamp ที่ใช้งานล่าสุด | 4 bytes | INT | IDX |

---

## ตาราง cache

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | key | คีย์ของ cache | 255 | VARCHAR | PK |
| 2 | value | ค่าที่เก็บใน cache | - | MEDIUMTEXT | - |
| 3 | expiration | Unix timestamp ที่หมดอายุ | 4 bytes | INT | - |

---

## ตาราง cache_locks

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | key | คีย์ของ lock | 255 | VARCHAR | PK |
| 2 | owner | เจ้าของ lock | 255 | VARCHAR | - |
| 3 | expiration | Unix timestamp ที่หมดอายุ | 4 bytes | INT | - |

---

## ตาราง jobs

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัส job (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | queue | ชื่อ queue | 255 | VARCHAR | IDX |
| 3 | payload | ข้อมูล job (serialized) | - | LONGTEXT | - |
| 4 | attempts | จำนวนครั้งที่พยายามรัน | 1 byte | TINYINT UNSIGNED | - |
| 5 | reserved_at | Unix timestamp ที่ถูกจองรัน | 4 bytes | INT UNSIGNED | - |
| 6 | available_at | Unix timestamp ที่พร้อมรัน | 4 bytes | INT UNSIGNED | - |
| 7 | created_at | Unix timestamp ที่สร้าง job | 4 bytes | INT UNSIGNED | - |

---

## ตาราง job_batches

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัส batch | 255 | VARCHAR | PK |
| 2 | name | ชื่อ batch | 255 | VARCHAR | - |
| 3 | total_jobs | จำนวน job ทั้งหมด | 4 bytes | INT | - |
| 4 | pending_jobs | จำนวน job ที่รอดำเนินการ | 4 bytes | INT | - |
| 5 | failed_jobs | จำนวน job ที่ล้มเหลว | 4 bytes | INT | - |
| 6 | failed_job_ids | รายการรหัส job ที่ล้มเหลว (JSON) | - | LONGTEXT | - |
| 7 | options | ตัวเลือกเพิ่มเติมของ batch | - | MEDIUMTEXT | - |
| 8 | cancelled_at | Unix timestamp ที่ยกเลิก | 4 bytes | INT | - |
| 9 | created_at | Unix timestamp ที่สร้าง | 4 bytes | INT | - |
| 10 | finished_at | Unix timestamp ที่เสร็จสิ้น | 4 bytes | INT | - |

---

## ตาราง failed_jobs

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัส failed job (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | uuid | UUID ของ job | 255 | VARCHAR | UQ |
| 3 | connection | ชื่อ connection ที่ใช้รัน | - | TEXT | - |
| 4 | queue | ชื่อ queue | - | TEXT | - |
| 5 | payload | ข้อมูล job (serialized) | - | LONGTEXT | - |
| 6 | exception | ข้อมูล exception ที่เกิดขึ้น | - | LONGTEXT | - |
| 7 | failed_at | วันเวลาที่ล้มเหลว (default now) | - | TIMESTAMP | - |

---

## ตาราง theme_categories

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสหมวดหมู่ธีม (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | name | ชื่อหมวดหมู่ธีม | 255 | VARCHAR | - |
| 3 | color | รหัสสี HEX ของธีม | 7 | VARCHAR | - |
| 4 | is_active | สถานะเปิดใช้งานธีม | - | BOOLEAN | - |
| 5 | planet_number | หมายเลขดาวเคราะห์ของธีม | 1 byte | TINYINT UNSIGNED | - |
| 6 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 7 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง classrooms

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสห้องเรียน (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | teacher_id | รหัสครูเจ้าของห้องเรียน | 8 bytes | BIGINT UNSIGNED | FK → users |
| 3 | name | ชื่อห้องเรียน | 255 | VARCHAR | - |
| 4 | section | ส่วนหรือกลุ่มของห้องเรียน | 255 | VARCHAR | - |
| 5 | description | คำอธิบายห้องเรียน | - | TEXT | - |
| 6 | code | รหัสเข้าร่วมห้องเรียน | 255 | VARCHAR | UQ |
| 7 | slug | Slug สำหรับ URL | 255 | VARCHAR | - |
| 8 | is_archived | สถานะเก็บถาวรห้องเรียน | - | BOOLEAN | - |
| 9 | theme_category_id | รหัสหมวดหมู่ธีมของห้องเรียน | 8 bytes | BIGINT UNSIGNED | FK → theme_categories |
| 10 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 11 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง classroom_user

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | classroom_id | รหัสห้องเรียน | 8 bytes | BIGINT UNSIGNED | PK, FK → classrooms |
| 2 | user_id | รหัสผู้ใช้งาน | 8 bytes | BIGINT UNSIGNED | PK, FK → users |
| 3 | role | บทบาทในห้องเรียน (student, co-teacher) | - | ENUM | - |
| 4 | joined_at | วันเวลาที่เข้าร่วมห้องเรียน | - | TIMESTAMP | - |
| 5 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 6 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง topics

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสหัวข้อ (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | classroom_id | รหัสห้องเรียน | 8 bytes | BIGINT UNSIGNED | FK → classrooms |
| 3 | name | ชื่อหัวข้อ | 255 | VARCHAR | - |
| 4 | order | ลำดับการแสดงผลหัวข้อ | 4 bytes | INT | - |
| 5 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 6 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง classwork_items

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสงาน (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | type | ประเภทงาน (assignment, material, announcement, attendance) | - | ENUM | - |
| 3 | classroom_id | รหัสห้องเรียน | 8 bytes | BIGINT UNSIGNED | FK → classrooms |
| 4 | user_id | รหัสผู้สร้างงาน | 8 bytes | BIGINT UNSIGNED | FK → users |
| 5 | topic_id | รหัสหัวข้อที่งานอยู่ | 8 bytes | BIGINT UNSIGNED | FK → topics |
| 6 | title | ชื่องาน | 255 | VARCHAR | - |
| 7 | slug | Slug สำหรับ URL | 32 | VARCHAR | UQ |
| 8 | description | คำอธิบายงาน | - | LONGTEXT | - |
| 9 | published_at | วันเวลาที่กำหนดให้เผยแพร่อัตโนมัติ (null = เผยแพร่ทันที) | - | TIMESTAMP | - |
| 10 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 11 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง announcements

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสประกาศ (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | classwork_item_id | รหัส classwork_items ที่เชื่อมโยง | 8 bytes | BIGINT UNSIGNED | UQ, FK → classwork_items |
| 3 | content | เนื้อหาประกาศ | - | TEXT | - |
| 4 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 5 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง assignments

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสงานมอบหมาย (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | classwork_item_id | รหัส classwork_items ที่เชื่อมโยง | 8 bytes | BIGINT UNSIGNED | UQ, FK → classwork_items |
| 3 | max_score | คะแนนเต็มของงาน | 4 bytes | INT | - |
| 4 | exp_reward | XP ที่ได้รับเมื่อส่งงาน | 4 bytes | INT UNSIGNED | - |
| 5 | coin_reward | Coin ที่ได้รับเมื่อส่งงาน | 4 bytes | INT UNSIGNED | - |
| 6 | due_date | วันเวลาหมดอายุการส่งงาน | - | TIMESTAMP | IDX |
| 7 | status | สถานะงาน (draft, published, scheduled, closed) | 255 | VARCHAR | - |
| 8 | type | ประเภทงาน เช่น question, attendance | 255 | VARCHAR | - |
| 9 | allow_late_submission | อนุญาตให้ส่งงานหลังกำหนดได้ | - | BOOLEAN | - |
| 10 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 11 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง materials

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสสื่อการเรียน (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | classwork_item_id | รหัส classwork_items ที่เชื่อมโยง | 8 bytes | BIGINT UNSIGNED | UQ, FK → classwork_items |
| 3 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 4 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง attachments

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสไฟล์แนบ (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | attachable_type | ประเภทของ model ที่แนบอยู่ (polymorphic) | 255 | VARCHAR | IDX |
| 3 | attachable_id | รหัสของ record ที่แนบอยู่ (polymorphic) | 8 bytes | BIGINT UNSIGNED | IDX |
| 4 | file_name | ชื่อไฟล์ | 255 | VARCHAR | - |
| 5 | file_path | path ไฟล์บน S3 | 255 | VARCHAR | - |
| 6 | file_type | MIME type ของไฟล์ | 255 | VARCHAR | - |
| 7 | file_size | ขนาดไฟล์ (bytes) | 4 bytes | INT | - |
| 8 | uploaded_by | รหัสผู้อัปโหลดไฟล์ | 8 bytes | BIGINT UNSIGNED | FK → users |
| 9 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 10 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง submissions

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสการส่งงาน (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | slug | Slug สำหรับ URL | 16 | VARCHAR | UQ |
| 3 | assignment_id | รหัสงานมอบหมาย | 8 bytes | BIGINT UNSIGNED | FK → assignments |
| 4 | user_id | รหัสนักเรียนที่ส่งงาน | 8 bytes | BIGINT UNSIGNED | IDX, FK → users |
| 5 | content | เนื้อหาที่นักเรียนส่ง | - | TEXT | - |
| 6 | status | สถานะการส่งงาน (assigned, turned_in, graded, returned) | - | ENUM | IDX |
| 7 | score | คะแนนที่ได้รับ | 4 bytes | INT | - |
| 8 | feedback | ความคิดเห็นจากครู | - | TEXT | - |
| 9 | turned_in_at | วันเวลาที่ส่งงาน | - | TIMESTAMP | - |
| 10 | graded_at | วันเวลาที่ตรวจงาน | - | TIMESTAMP | - |
| 11 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 12 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง attendance_sessions

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัส session เช็คชื่อ (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | classwork_item_id | รหัส classwork_items ที่เชื่อมโยง | 8 bytes | BIGINT UNSIGNED | UQ, FK → classwork_items |
| 3 | current_code | รหัสเช็คชื่อปัจจุบัน (6 หลัก) | 6 | VARCHAR | - |
| 4 | is_active | สถานะเปิดรับเช็คชื่อ | - | BOOLEAN | - |
| 5 | started_at | วันเวลาที่เริ่ม session เช็คชื่อ | - | TIMESTAMP | - |
| 6 | code_rotated_at | วันเวลาที่หมุนรหัสเช็คชื่อล่าสุด | - | TIMESTAMP | - |
| 7 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 8 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง comments

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสความคิดเห็น (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | commentable_type | ประเภทของ model ที่แสดงความคิดเห็น (polymorphic) | 255 | VARCHAR | IDX |
| 3 | commentable_id | รหัสของ record ที่แสดงความคิดเห็น (polymorphic) | 8 bytes | BIGINT UNSIGNED | IDX |
| 4 | user_id | รหัสผู้แสดงความคิดเห็น | 8 bytes | BIGINT UNSIGNED | FK → users |
| 5 | content | เนื้อหาความคิดเห็น | - | TEXT | - |
| 6 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 7 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง achievements

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสความสำเร็จ (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | code | รหัสระบุความสำเร็จ (unique) | 255 | VARCHAR | UQ |
| 3 | name | ชื่อความสำเร็จ | 255 | VARCHAR | - |
| 4 | description | คำอธิบายเงื่อนไขความสำเร็จ | 255 | VARCHAR | - |
| 5 | badge_image | path ไฟล์รูป badge บน S3 | 255 | VARCHAR | - |
| 6 | coin_reward | Coin ที่ได้รับเมื่อปลดล็อก | 4 bytes | INT | - |
| 7 | xp_reward | XP ที่ได้รับเมื่อปลดล็อก | 4 bytes | INT | - |
| 8 | is_active | สถานะเปิดใช้งานความสำเร็จ | - | BOOLEAN | - |
| 9 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 10 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง user_achievements

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | user_id | รหัสผู้ใช้งาน | 8 bytes | BIGINT UNSIGNED | PK, FK → users |
| 2 | achievement_id | รหัสความสำเร็จ | 8 bytes | BIGINT UNSIGNED | PK, FK → achievements |
| 3 | unlocked_at | วันเวลาที่ปลดล็อกความสำเร็จ | - | TIMESTAMP | - |
| 4 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 5 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง user_gamifications

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสข้อมูล gamification (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | user_id | รหัสผู้ใช้งาน (1 ต่อ 1 กับ users) | 8 bytes | BIGINT UNSIGNED | UQ, FK → users |
| 3 | coins | จำนวน coin สะสม | 4 bytes | INT | - |
| 4 | xp | จำนวน XP สะสม | 4 bytes | INT | - |
| 5 | level | ระดับของผู้ใช้งาน | 4 bytes | INT | - |
| 6 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 7 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง coin_transactions

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสธุรกรรม (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | user_id | รหัสผู้ใช้งาน | 8 bytes | BIGINT UNSIGNED | IDX, FK → users |
| 3 | amount | จำนวน coin ที่เปลี่ยนแปลง (บวก/ลบ) | 4 bytes | INT | - |
| 4 | type | ประเภทธุรกรรม (earn, spend) | 255 | VARCHAR | IDX |
| 5 | source | แหล่งที่มาของธุรกรรม | 255 | VARCHAR | - |
| 6 | reference_type | ประเภท model ที่เกี่ยวข้อง (polymorphic) | 255 | VARCHAR | IDX |
| 7 | reference_id | รหัส record ที่เกี่ยวข้อง (polymorphic) | 8 bytes | BIGINT UNSIGNED | IDX |
| 8 | metadata | ข้อมูลเพิ่มเติม (JSON) | - | TEXT | - |
| 9 | happened_at | วันเวลาที่ธุรกรรมเกิดขึ้น | - | TIMESTAMP | IDX |
| 10 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 11 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง store_items

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสสินค้า (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | code | รหัสระบุสินค้า (unique) | 255 | VARCHAR | UQ |
| 3 | name | ชื่อสินค้า | 255 | VARCHAR | - |
| 4 | description | คำอธิบายสินค้า | - | TEXT | - |
| 5 | type | ประเภทสินค้า (name_color, avatar_frame) | - | ENUM | - |
| 6 | value | ค่าที่ใช้งาน เช่น รหัสสี HEX หรือชื่อ frame | 255 | VARCHAR | - |
| 7 | price | ราคาสินค้า (coin) | 4 bytes | INT | - |
| 8 | is_active | สถานะเปิดจำหน่าย | - | BOOLEAN | - |
| 9 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 10 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง user_store_items

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | user_id | รหัสผู้ใช้งาน | 8 bytes | BIGINT UNSIGNED | PK, FK → users |
| 2 | store_item_id | รหัสสินค้า | 8 bytes | BIGINT UNSIGNED | PK, FK → store_items |
| 3 | is_active | สถานะเปิดใช้งานสินค้า (เลือกใช้แล้ว) | - | BOOLEAN | - |
| 4 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 5 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง email_otp_verifications

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสการยืนยัน OTP (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | email | อีเมลที่ขอยืนยัน | 255 | VARCHAR | IDX |
| 3 | otp | รหัส OTP | 255 | VARCHAR | - |
| 4 | user_data | ข้อมูลผู้ใช้งานที่รอสร้าง (JSON) | - | JSON | - |
| 5 | expires_at | วันเวลาหมดอายุ OTP | - | TIMESTAMP | - |
| 6 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 7 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## ตาราง bug_reports

| ลำดับ | คุณสมบัติ | อธิบาย | ขนาด | ประเภท | ประเภทคีย์ |
|-------|-----------|--------|------|--------|------------|
| 1 | id | รหัสรายงาน (Auto Increment) | 8 bytes | BIGINT UNSIGNED | PK |
| 2 | user_id | รหัสผู้ส่งรายงาน | 8 bytes | BIGINT UNSIGNED | FK → users |
| 3 | type | ประเภทรายงาน (bug, suggestion, other) | - | ENUM | - |
| 4 | title | หัวข้อรายงาน | 255 | VARCHAR | - |
| 5 | message | รายละเอียดรายงาน | - | TEXT | - |
| 6 | status | สถานะรายงาน (pending, resolved) | - | ENUM | - |
| 7 | created_at | วันเวลาที่สร้างรายการ | - | TIMESTAMP | - |
| 8 | updated_at | วันเวลาที่แก้ไขรายการล่าสุด | - | TIMESTAMP | - |

---

## หมายเหตุ

| สัญลักษณ์ | ความหมาย |
|-----------|----------|
| PK | Primary Key |
| FK | Foreign Key |
| UQ | Unique |
| IDX | Index |
| - | ไม่มีข้อจำกัดพิเศษ |

- ขนาด `-` หมายถึงขนาดตามประเภทข้อมูลมาตรฐาน หรือไม่มีการกำหนดขนาดตายตัว
- BIGINT UNSIGNED ใช้ขนาด 8 bytes รองรับค่า 0 ถึง 18,446,744,073,709,551,615
- BOOLEAN จัดเก็บเป็น TINYINT(1) ในฐานข้อมูล MySQL (0 = false, 1 = true)
- TIMESTAMP รองรับช่วง 1970-01-01 ถึง 2038-01-19 สำหรับ MySQL
- ทุกตารางที่มี `created_at` และ `updated_at` ถูกจัดการอัตโนมัติโดย Eloquent
