# LiteLearning — Test Cases

> อ้างอิงจากขอบเขตระบบใน `Req.txt` (v 2026-02-25)

---

## ผู้ดูแลระบบ (Admin)

### 1) จัดการระบบ

| TC-ID  | Test Case                                | เงื่อนไขเริ่มต้น     | ขั้นตอน                                                          | ผลลัพธ์ที่คาดหวัง           |
| ------ | ---------------------------------------- | -------------------- | ---------------------------------------------------------------- | --------------------------- |
| TC-A01 | เข้าสู่ระบบด้วยบัญชี Admin               | มีบัญชี admin ในระบบ | 1. ไปที่หน้า Login 2. ใส่ email/password ของ admin 3. กด Sign in | เข้าสู่ Admin Dashboard ได้ |
| TC-A02 | ผู้ดูแลสามารถเข้าถึง Admin Panel ได้     | Login เป็น admin     | 1. ไปที่ `/admin/dashboard`                                      | เห็นหน้า Admin Panel        |
| TC-A03 | ผู้สอนไม่สามารถเข้าถึง Admin Panel ได้   | Login เป็น teacher   | 1. ไปที่ `/admin/dashboard`                                      | ได้รับ 403 Forbidden        |
| TC-A04 | ผู้เรียนไม่สามารถเข้าถึง Admin Panel ได้ | Login เป็น student   | 1. ไปที่ `/admin/dashboard`                                      | ได้รับ 403 Forbidden        |

### 2) จัดการผู้ใช้

| TC-ID  | Test Case                               | เงื่อนไขเริ่มต้น                          | ขั้นตอน                                                                      | ผลลัพธ์ที่คาดหวัง                    |
| ------ | --------------------------------------- | ----------------------------------------- | ---------------------------------------------------------------------------- | ------------------------------------ |
| TC-A05 | ดูรายชื่อผู้ใช้ทั้งหมด                  | Login เป็น admin                          | 1. ไปที่ `/admin/users`                                                      | เห็นรายชื่อผู้ใช้ทุกคน               |
| TC-A06 | ค้นหาผู้ใช้ด้วยชื่อ                     | Login เป็น admin, มีผู้ใช้ "สมชาย"        | 1. พิมพ์ "สมชาย" ใน search box                                               | เห็นเฉพาะผู้ใช้ที่ชื่อตรง            |
| TC-A07 | เปลี่ยน Role ผู้ใช้                     | Login เป็น admin, มี user ที่เป็น teacher | 1. คลิก dropdown role ของ user 2. เปลี่ยนเป็น student                        | Role เปลี่ยนสำเร็จ แสดง notification |
| TC-A08 | เปลี่ยน Role เป็นค่าที่ไม่ถูกต้องไม่ได้ | Login เป็น admin                          | Frontend validation ไม่อนุญาตให้เลือก role นอกเหนือจาก admin/teacher/student | ไม่มี option อื่นใน dropdown         |
| TC-A09 | Admin ไม่สามารถ Deactivate ตัวเองได้    | Login เป็น admin                          | 1. พยายาม deactivate ตัวเอง                                                  | ได้รับ error message                 |
| TC-A10 | ระงับการใช้งานผู้ใช้ (Deactivate)       | Login เป็น admin, มี user อื่น            | 1. คลิก deactivate user                                                      | User ถูกระงับ, is_active = false     |
| TC-A11 | ผู้ใช้ที่ถูกระงับไม่สามารถ Login ได้    | มี user ที่ is_active = false             | 1. พยายาม login ด้วย user ที่ถูกระงับ                                        | Login ล้มเหลว                        |

### 3) จัดการเนื้อหา (Store)

| TC-ID  | Test Case                        | เงื่อนไขเริ่มต้น                                         | ขั้นตอน                                                                         | ผลลัพธ์ที่คาดหวัง           |
| ------ | -------------------------------- | -------------------------------------------------------- | ------------------------------------------------------------------------------- | --------------------------- |
| TC-A12 | สร้าง Store Item ใหม่            | Login เป็น admin                                         | 1. ไปที่ `/admin/store` 2. กด Create 3. กรอก code, name, type, price 4. กด Save | Item ถูกสร้าง ปรากฏในรายการ |
| TC-A13 | สร้าง Store Item ซ้ำ code ไม่ได้ | Login เป็น admin, มี item ที่ code "frame-gold" อยู่แล้ว | 1. สร้าง item ใหม่ด้วย code "frame-gold"                                        | Validation error: code ซ้ำ  |
| TC-A14 | แก้ไข Store Item                 | Login เป็น admin                                         | 1. คลิก Edit บน item 2. เปลี่ยน price 3. กด Save                                | Item ถูกอัปเดต              |
| TC-A15 | Toggle ซ่อน/แสดง Store Item      | Login เป็น admin                                         | 1. คลิก toggle active บน item                                                   | สถานะ is_active เปลี่ยน     |
| TC-A16 | ลบ Store Item                    | Login เป็น admin                                         | 1. คลิก Delete บน item                                                          | Item ถูกลบออกจากรายการ      |

### 4) จัดการห้องเรียน

| TC-ID  | Test Case                 | เงื่อนไขเริ่มต้น | ขั้นตอน                      | ผลลัพธ์ที่คาดหวัง           |
| ------ | ------------------------- | ---------------- | ---------------------------- | --------------------------- |
| TC-A17 | ดูรายชื่อห้องเรียนทั้งหมด | Login เป็น admin | 1. ไปที่ `/admin/classrooms` | เห็นรายชื่อห้องเรียนทุกห้อง |

### 5) ดูรายงานและสถิติ

| TC-ID  | Test Case                | เงื่อนไขเริ่มต้น | ขั้นตอน                     | ผลลัพธ์ที่คาดหวัง                      |
| ------ | ------------------------ | ---------------- | --------------------------- | -------------------------------------- |
| TC-A18 | ดู Admin Dashboard สถิติ | Login เป็น admin | 1. ไปที่ `/admin/dashboard` | เห็นจำนวนผู้ใช้, ห้องเรียน, submission |

### 6) ดูแลความปลอดภัย

| TC-ID  | Test Case                              | เงื่อนไขเริ่มต้น | ขั้นตอน                           | ผลลัพธ์ที่คาดหวัง                      |
| ------ | -------------------------------------- | ---------------- | --------------------------------- | -------------------------------------- |
| TC-A19 | Login หลายครั้งเกินกำหนดถูก Rate Limit | ไม่ได้ Login     | 1. ใส่ password ผิด 5 ครั้งติดกัน | ได้รับ error "Too many login attempts" |
| TC-A20 | ผู้ไม่ได้ Login ถูก Redirect ไป Login  | ไม่ได้ Login     | 1. เข้าถึง `/dashboard` โดยตรง    | Redirect ไปหน้า Login                  |

### 7) สนับสนุนผู้ใช้ (Bug Reports)

| TC-ID  | Test Case            | เงื่อนไขเริ่มต้น | ขั้นตอน                   | ผลลัพธ์ที่คาดหวัง              |
| ------ | -------------------- | ---------------- | ------------------------- | ------------------------------ |
| TC-A21 | ดูรายการ Bug Reports | Login เป็น admin | 1. ไปที่ `/admin/reports` | เห็นรายการ bug reports ทั้งหมด |

### 8) จัดการ Achievements และ Badges

| TC-ID  | Test Case              | เงื่อนไขเริ่มต้น | ขั้นตอน                                                              | ผลลัพธ์ที่คาดหวัง    |
| ------ | ---------------------- | ---------------- | -------------------------------------------------------------------- | -------------------- |
| TC-A22 | สร้าง Achievement ใหม่ | Login เป็น admin | 1. ไปที่ `/admin/achievements` 2. กด Create 3. กรอกข้อมูล 4. กด Save | Achievement ถูกสร้าง |
| TC-A23 | สร้าง Badge ใหม่       | Login เป็น admin | 1. ไปที่ `/admin/badges` 2. กด Create 3. กรอกข้อมูล 4. กด Save       | Badge ถูกสร้าง       |

---

## ผู้สอน (Teacher)

### 1.1 การจัดการห้องเรียน

| TC-ID  | Test Case                          | เงื่อนไขเริ่มต้น                | ขั้นตอน                                                            | ผลลัพธ์ที่คาดหวัง                      |
| ------ | ---------------------------------- | ------------------------------- | ------------------------------------------------------------------ | -------------------------------------- |
| TC-T01 | สร้างห้องเรียน                     | Login เป็น teacher              | 1. กด "Create Class" 2. กรอก ชื่อ, section, subject 3. กด Create   | ห้องเรียนถูกสร้าง, redirect ไปหน้าห้อง |
| TC-T02 | ผู้เรียนไม่สามารถสร้างห้องเรียนได้ | Login เป็น student              | 1. ไปที่ classrooms page                                           | ไม่เห็นปุ่ม "Create Class"             |
| TC-T03 | แก้ไขการตั้งค่าห้องเรียน           | Login เป็น teacher, เจ้าของห้อง | 1. เข้าห้องเรียน 2. ไปที่ tab Settings 3. แก้ไขชื่อ 4. กด Save     | ข้อมูลห้องถูกอัปเดต                    |
| TC-T04 | เปลี่ยนสีธีมห้องเรียน              | Login เป็น teacher, เจ้าของห้อง | 1. Settings tab 2. เลือกสีใหม่ 3. Save                             | สี Header ของห้องเปลี่ยน               |
| TC-T05 | ลบห้องเรียน                        | Login เป็น teacher, เจ้าของห้อง | 1. Settings → Danger Zone 2. พิมพ์ชื่อห้องเพื่อยืนยัน 3. กด Delete | ห้องเรียนถูกลบ, redirect ไป classrooms |
| TC-T06 | Archive ห้องเรียน                  | Login เป็น teacher, เจ้าของห้อง | _(ผ่าน Settings ถ้า implement)_                                    | ห้องย้ายไป filter "Archived"           |

### 1.2 จัดการผู้เรียนภายในห้อง

| TC-ID  | Test Case                             | เงื่อนไขเริ่มต้น                     | ขั้นตอน                                       | ผลลัพธ์ที่คาดหวัง                      |
| ------ | ------------------------------------- | ------------------------------------ | --------------------------------------------- | -------------------------------------- |
| TC-T07 | ดูรายชื่อนักเรียนในห้อง               | Login เป็น teacher, มีนักเรียน       | 1. เข้าห้อง → tab People                      | เห็นรายชื่อนักเรียนทุกคน               |
| TC-T08 | ห้องเปล่า People tab แสดง Empty State | Login เป็น teacher, ยังไม่มีนักเรียน | 1. เข้าห้อง → tab People                      | เห็นข้อความ "No students enrolled yet" |
| TC-T09 | นักเรียนใช้ Code เข้าห้องเรียน        | Login เป็น student, มี code          | 1. กด Join Class 2. ใส่ class code 3. กด Join | เข้าห้องเรียนสำเร็จ                    |
| TC-T10 | Join ห้องด้วย Code ผิด                | Login เป็น student                   | 1. ใส่ code ที่ไม่มีในระบบ                    | Error "Classroom not found"            |

### 1.3 การจัดการภารกิจ (Assignment)

| TC-ID  | Test Case                                                        | เงื่อนไขเริ่มต้น                               | ขั้นตอน                                                           | ผลลัพธ์ที่คาดหวัง                                |
| ------ | ---------------------------------------------------------------- | ---------------------------------------------- | ----------------------------------------------------------------- | ------------------------------------------------ |
| TC-T11 | สร้าง Assignment                                                 | Login เป็น teacher, เจ้าของห้อง                | 1. Classwork tab → Create 2. กรอก title, description 3. กด Assign | Assignment ปรากฏใน Classwork tab                 |
| TC-T12 | บันทึก Assignment เป็น Draft                                     | Login เป็น teacher                             | 1. กรอกข้อมูล assignment 2. กด "Save as Draft"                    | Assignment มี status "Draft", มองเห็นแค่ teacher |
| TC-T13 | ผู้เรียนเห็นเฉพาะ Published assignment                           | Login เป็น student, มีทั้ง draft และ published | 1. เข้าห้องเรียน → Classwork                                      | เห็นเฉพาะ assignment ที่ published               |
| TC-T14 | แก้ไข Assignment                                                 | Login เป็น teacher                             | 1. เข้า assignment → Edit 2. แก้ไข title 3. Save                  | Assignment ถูกอัปเดต                             |
| TC-T15 | ลบ Assignment                                                    | Login เป็น teacher                             | 1. Classwork → Delete assignment 2. Confirm                       | Assignment ถูกลบ                                 |
| TC-T16 | ผู้สอนห้องอื่นสร้าง Assignment ในห้องนี้ไม่ได้                   | Login เป็น teacher ที่ไม่ใช่เจ้าของ            | 1. พยายามเข้า `/c/{classroom}/a/create`                           | ได้รับ 403                                       |
| TC-T17 | Route `/c/{classroom}/a/create` ไม่ถูก match เป็น `{assignment}` | -                                              | 1. เข้า URL `/c/{slug}/a/create`                                  | โหลดหน้า Create Assignment ได้ (ไม่ใช่ 404)      |

### 1.4 จัดการคะแนน

| TC-ID  | Test Case                                   | เงื่อนไขเริ่มต้น                             | ขั้นตอน                                                                                    | ผลลัพธ์ที่คาดหวัง                            |
| ------ | ------------------------------------------- | -------------------------------------------- | ------------------------------------------------------------------------------------------ | -------------------------------------------- |
| TC-T18 | ดูตาราง Grades                              | Login เป็น teacher, มีนักเรียนและ submission | 1. เข้าห้อง → tab Grades                                                                   | เห็นตารางคะแนนนักเรียนทุกคน                  |
| TC-T19 | Grade tab แสดง Empty State ถ้าไม่มีนักเรียน | Login เป็น teacher, ไม่มีนักเรียน            | 1. เข้าห้อง → tab Grades                                                                   | เห็นข้อความ "No students enrolled yet"       |
| TC-T20 | ให้คะแนน Submission                         | Login เป็น teacher, มี submission            | 1. เข้า assignment → คลิก submission ของนักเรียน 2. ใส่คะแนน + feedback 3. กด Submit Grade | Submission status เป็น "graded", คะแนนบันทึก |
| TC-T21 | คะแนนเฉลี่ยในตาราง Grades คำนวณถูกต้อง      | มีนักเรียน 1 คน score 80/100, 60/100         | 1. เข้า Grades tab                                                                         | เห็น Average = 70%                           |

### 1.5 การสื่อสาร (Announcements)

| TC-ID  | Test Case                                 | เงื่อนไขเริ่มต้น                       | ขั้นตอน                                                    | ผลลัพธ์ที่คาดหวัง                  |
| ------ | ----------------------------------------- | -------------------------------------- | ---------------------------------------------------------- | ---------------------------------- |
| TC-T22 | โพสต์ Announcement                        | Login เป็น teacher, เจ้าของห้อง        | 1. Stream tab 2. คลิก text area 3. พิมพ์ข้อความ 4. กด Post | Announcement ปรากฏใน Stream        |
| TC-T23 | ลบ Announcement ของตัวเอง                 | Login เป็น teacher                     | 1. คลิก trash icon บน announcement                         | Announcement ถูกลบ                 |
| TC-T24 | Teacher ลบ Announcement จากห้องอื่นไม่ได้ | Login เป็น teacher                     | 1. พยายามลบ announcement จากห้องที่ไม่ใช่ตัวเอง            | ได้รับ 403                         |
| TC-T25 | Stream ว่างแสดง Empty State               | Login เป็น teacher, ไม่มี announcement | 1. เข้าห้อง → Stream                                       | เห็นข้อความ "No announcements yet" |
| TC-T26 | Comment บน Announcement                   | Login ใดๆ ที่มีสิทธิ์เข้าห้อง          | 1. พิมพ์ comment ใต้ announcement 2. กด Post               | Comment ปรากฏ                      |

### 1.6 การติดตามผู้เรียน (Attendance)

| TC-ID  | Test Case                  | เงื่อนไขเริ่มต้น                                    | ขั้นตอน                                        | ผลลัพธ์ที่คาดหวัง         |
| ------ | -------------------------- | --------------------------------------------------- | ---------------------------------------------- | ------------------------- |
| TC-T27 | เริ่ม Session เช็คชื่อ     | Login เป็น teacher, มี assignment type "attendance" | 1. เข้า assignment → Start Session             | Session เริ่ม, code ปรากฏ |
| TC-T28 | นักเรียนกรอก Code เช็คชื่อ | Login เป็น student, มี active session               | 1. เข้า assignment 2. กรอก code 3. กด Check In | บันทึกการเข้าเรียนสำเร็จ  |

### 1.7 รายงานข้อผิดพลาด

| TC-ID  | Test Case               | เงื่อนไขเริ่มต้น   | ขั้นตอน                                                                 | ผลลัพธ์ที่คาดหวัง                      |
| ------ | ----------------------- | ------------------ | ----------------------------------------------------------------------- | -------------------------------------- |
| TC-T29 | ส่ง Bug Report          | Login เป็น teacher | 1. เปิด Report modal 2. เลือก type 3. กรอก title + message 4. กด Submit | Report บันทึกสำเร็จ, notification แสดง |
| TC-T30 | Bug Report Rate Limited | Login เป็น teacher | 1. ส่ง report 3 ครั้งภายใน 10 นาที 2. ส่งครั้งที่ 4                     | ได้รับ error "Too many reports"        |

---

## ผู้เรียน (Student)

### 2.1 ปรับแต่งโปรไฟล์

| TC-ID  | Test Case                        | เงื่อนไขเริ่มต้น   | ขั้นตอน                                     | ผลลัพธ์ที่คาดหวัง       |
| ------ | -------------------------------- | ------------------ | ------------------------------------------- | ----------------------- |
| TC-S01 | อัปโหลด Avatar                   | Login เป็น student | 1. ไปที่ Profile 2. อัปโหลดรูป crop 3. Save | Avatar เปลี่ยน          |
| TC-S02 | อัปโหลดไฟล์ที่ไม่ใช่รูปภาพไม่ได้ | Login เป็น student | 1. พยายามอัปโหลด .pdf เป็น avatar           | Error MIME type         |
| TC-S03 | อัปโหลดรูปขนาดเกิน 5MB ไม่ได้    | Login เป็น student | 1. อัปโหลดรูปที่ขนาดใหญ่เกิน 5MB            | Error "Image too large" |
| TC-S04 | เปลี่ยนภาษา (Locale)             | Login เป็น student | 1. Settings → เลือกภาษา                     | UI เปลี่ยนภาษา          |

### 2.2 เข้าร่วมห้องเรียน

| TC-ID  | Test Case                         | เงื่อนไขเริ่มต้น                           | ขั้นตอน                                      | ผลลัพธ์ที่คาดหวัง                    |
| ------ | --------------------------------- | ------------------------------------------ | -------------------------------------------- | ------------------------------------ |
| TC-S05 | เข้าร่วมห้องเรียนด้วย Code        | Login เป็น student                         | 1. กด Join Class 2. ใส่ Code ถูกต้อง 3. Join | เข้าร่วมสำเร็จ ห้องปรากฏในรายการ     |
| TC-S06 | Join ห้องเดิมซ้ำไม่ได้            | Login เป็น student ที่เป็น member อยู่แล้ว | 1. พยายาม join ห้องเดิม                      | Error หรือ redirect โดยไม่ duplicate |
| TC-S07 | Rate Limit Join ห้อง 5 ครั้ง/นาที | Login เป็น student                         | 1. ลอง join ผิดๆ 5 ครั้งติดกัน               | Error "Too many attempts"            |

### 2.3 เข้าถึงเนื้อหาภารกิจ

| TC-ID  | Test Case                                  | เงื่อนไขเริ่มต้น                                     | ขั้นตอน                                                | ผลลัพธ์ที่คาดหวัง                               |
| ------ | ------------------------------------------ | ---------------------------------------------------- | ------------------------------------------------------ | ----------------------------------------------- |
| TC-S08 | ส่งงาน (Turn In)                           | Login เป็น student, สมาชิกห้อง, assignment published | 1. เข้า assignment 2. เขียน/แนบงาน 3. กด Turn In       | Submission status เป็น "turned_in"              |
| TC-S09 | Save Draft                                 | Login เป็น student                                   | 1. เขียนงานบางส่วน 2. กด Save Draft                    | Submission status เป็น "assigned", งานถูกบันทึก |
| TC-S10 | Unsubmit งาน                               | Login เป็น student, status "turned_in"               | 1. กด Unsubmit                                         | Submission กลับเป็น "assigned"                  |
| TC-S11 | ส่งงานหลัง due date ถ้า allow_late = false | Login เป็น student                                   | 1. พยายามส่งงานหลัง due date                           | Error "Assignment is closed"                    |
| TC-S12 | ส่งงานหลัง due date ถ้า allow_late = true  | Login เป็น student                                   | 1. ส่งงานหลัง due date                                 | ส่งสำเร็จ (late submission)                     |
| TC-S13 | นักเรียนนอกห้องเข้า assignment ไม่ได้      | Login เป็น student ที่ไม่ใช่สมาชิก                   | 1. เข้า URL assignment ของห้องที่ไม่ได้เป็นสมาชิก      | ได้รับ 403                                      |
| TC-S14 | Assignment จากห้องอื่น IDOR                | Login เป็น student                                   | 1. เข้า `/c/{classroomA}/a/{assignmentFromClassroomB}` | ได้รับ 403/404                                  |

### 2.4 ตารางจัดอันดับ (Leaderboard)

| TC-ID  | Test Case                       | เงื่อนไขเริ่มต้น   | ขั้นตอน                 | ผลลัพธ์ที่คาดหวัง              |
| ------ | ------------------------------- | ------------------ | ----------------------- | ------------------------------ |
| TC-S15 | ดู Leaderboard                  | Login เป็น student | 1. ไปที่ `/leaderboard` | เห็นอันดับผู้เรียนตาม XP/Level |
| TC-S16 | Teacher เข้า Leaderboard ไม่ได้ | Login เป็น teacher | 1. ไปที่ `/leaderboard` | ได้รับ 403                     |

### 2.5 เข้าถึงความสำเร็จ (Achievements)

| TC-ID  | Test Case                        | เงื่อนไขเริ่มต้น   | ขั้นตอน                  | ผลลัพธ์ที่คาดหวัง                         |
| ------ | -------------------------------- | ------------------ | ------------------------ | ----------------------------------------- |
| TC-S17 | ดูหน้า Achievements              | Login เป็น student | 1. ไปที่ `/achievements` | เห็น achievement ที่ได้รับและยังไม่ได้รับ |
| TC-S18 | Teacher เข้า Achievements ไม่ได้ | Login เป็น teacher | 1. ไปที่ `/achievements` | ได้รับ 403                                |

### 2.6 เก็บ Level/XP

| TC-ID  | Test Case                                | เงื่อนไขเริ่มต้น             | ขั้นตอน                             | ผลลัพธ์ที่คาดหวัง                    |
| ------ | ---------------------------------------- | ---------------------------- | ----------------------------------- | ------------------------------------ |
| TC-S19 | ได้รับ XP เมื่อส่งงาน                    | Login เป็น student           | 1. ส่งงาน                           | XP ใน `user_gamifications` เพิ่มขึ้น |
| TC-S20 | Level Up เมื่อ XP ถึงเกณฑ์               | Level < 100, XP ใกล้ถึงเกณฑ์ | 1. ได้รับ XP จนเกิน threshold       | Level เพิ่ม 1, ได้ Bonus Coins       |
| TC-S21 | Level ไม่เกิน 100                        | Level = 100, XP เพิ่ม        | 1. ได้รับ XP เมื่อ level 100        | Level คงอยู่ที่ 100                  |
| TC-S22 | Teacher ไม่ได้รับ Coins จาก gamification | Login เป็น teacher           | 1. ทำ action ที่ trigger coinsAward | Coins ไม่เพิ่ม (teacher ถูก skip)    |

### 2.7 ร้านค้า (Store)

| TC-ID  | Test Case                            | เงื่อนไขเริ่มต้น                     | ขั้นตอน                          | ผลลัพธ์ที่คาดหวัง                                    |
| ------ | ------------------------------------ | ------------------------------------ | -------------------------------- | ---------------------------------------------------- |
| TC-S23 | เข้าร้านค้า                          | Login เป็น student                   | 1. ไปที่ `/store`                | เห็นรายการ item ที่ is_active = true                 |
| TC-S24 | Teacher เข้าร้านค้าไม่ได้            | Login เป็น teacher                   | 1. ไปที่ `/store`                | ได้รับ 403                                           |
| TC-S25 | ซื้อ Item ด้วย Coins เพียงพอ         | Login เป็น student, coins ≥ ราคา     | 1. กด Buy บน item 2. Confirm     | Coins ลด, item ปรากฏใน inventory, transaction บันทึก |
| TC-S26 | ซื้อ Item ไม่สำเร็จเมื่อ Coins ไม่พอ | Login เป็น student, coins < ราคา     | 1. กด Buy                        | Error "Insufficient coins"                           |
| TC-S27 | ซื้อ Item ซ้ำไม่ได้                  | Login เป็น student, มี item อยู่แล้ว | 1. พยายามซื้อ item ที่มีอยู่แล้ว | Error "Already owned"                                |

### 2.8 รายงานข้อผิดพลาด

| TC-ID  | Test Case               | เงื่อนไขเริ่มต้น   | ขั้นตอน                                      | ผลลัพธ์ที่คาดหวัง |
| ------ | ----------------------- | ------------------ | -------------------------------------------- | ----------------- |
| TC-S28 | ส่ง Bug Report          | Login เป็น student | 1. เปิด Report modal 2. กรอกข้อมูล 3. Submit | Report บันทึก     |
| TC-S29 | Bug Report Rate Limited | Login เป็น student | 1. ส่ง 3 ครั้ง 2. ส่งครั้งที่ 4              | Error: rate limit |

---

## กรณีทั่วไป (Cross-cutting)

| TC-ID  | Test Case                                         | ผลลัพธ์ที่คาดหวัง            |
| ------ | ------------------------------------------------- | ---------------------------- |
| TC-G01 | Setup Flow: ผู้ใช้ใหม่ถูก redirect ไป Setup ก่อน  | Redirect ไป `/setup`         |
| TC-G02 | Setup เสร็จแล้วผ่าน Setup redirect ได้            | เข้า `/dashboard` ได้ตามปกติ |
| TC-G03 | IDOR: Grade submission ของ assignment จากห้องอื่น | 403/404                      |
| TC-G04 | IDOR: ลบ announcement จากห้องที่ไม่ใช่ตัวเอง      | 403                          |
