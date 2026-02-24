# LiteLearning — Data Flow Diagram (DFD) Details

> เอกสารอธิบายการไหลของข้อมูล (Data Flow) ตั้งแต่ภาพรวมจนถึงระดับปฏิบัติการ อ้างอิงตามขอบเขตใน `Req.txt`

---

## 1. External Entities

หน่วยงานหรือระบบภายนอกที่มีการโต้ตอบกับระบบ LiteLearning:

1. **Admin (ผู้ดูแลระบบ)**: ดูแลภาพรวม จัดการผู้ใช้ และเนื้อหา
2. **Teacher (ผู้สอน)**: จัดการห้องเรียน และกิจกรรมการเรียนการสอน
3. **Student (ผู้เรียน)**: เข้าร่วมกิจกรรม เรียนรู้ และรับรางวัล
4. **AWS S3 (Cloud Storage)**: ระบบเก็บไฟล์แนบและรูปภาพภายนอก
5. **System Clock**: (Logical Entity) สำหรับ Trigger วันกำหนดส่งหรือ Session เช็คชื่อ

---

## 2. DFD Level 0: Context Diagram

แสดงภาพรวมสูงสุดของระบบ

- **Admin** → [System Config, User Management, Rewards Config] → **LiteLearning**
- **Teacher** → [Classroom Details, Assignment, Announcement, Grades] → **LiteLearning**
- **Student** → [Profile, Join Code, Submission, Purchase] → **LiteLearning**
- **LiteLearning** → [Reports, System Info] → **Admin**
- **LiteLearning** → [Student Submissions, Classroom Stats] → **Teacher**
- **LiteLearning** → [Content, Feedback, XP/Coins, Item] → **Student**
- **LiteLearning** → [File Data] → **AWS S3**
- **AWS S3** → [File URL / Status] → **LiteLearning**

---

## 3. DFD Level 1: Major Processes

แบ่งระบบออกเป็น 6 กระบวนการหลัก:

- **P1: Account & Profile Management**
    - จัดการการเข้าถึง, Setup Flow, และการแก้ไขโปรไฟล์ (Avatar, Bio)
- **P2: Classroom & Member Management**
    - การสร้างห้องเรียน, การเข้าร่วมด้วย Code, และการจัดการสมาชิก (Pin ห้อง)
- **P3: Assignment & Learning Activity**
    - การสร้างภารกิจ, การเช็คชื่อ (Attendance), การส่งงาน, และการทำ Quiz
- **P4: Grading & Tracking**
    - การตรวจงาน, การให้คะแนน, และผลรวมคะแนนเฉลี่ย
- **P5: Gamification & Rewards**
    - การคำนวณ XP, การ Level Up, การซื้อของใน Store และ Achievement
- **P6: Admin Content & Support**
    - การจัดการไอเทมในร้านค้า, การสร้างความสำเร็จ, และ Bug Reporting

---

## 4. DFD Level 2: Detailed Processes

### P2: Classroom & Member Management (Detailed)

1. **2.1 สร้างห้องเรียน (Create Classroom)**
    - `Teacher` → [Class Data] → `P2.1` → [New Classroom Record] → `D2: Classrooms`
2. **2.2 เข้าร่วมห้องเรียน (Join Classroom)**
    - `Student` → [Join Code] → `P2.2` → [Check Code] → `D2: Classrooms`
    - `P2.2` → [New Member Record] → `D2: Members`
    - `D2: Classrooms` → [Code Validation Result] → `Student`
3. **2.3 จัดการ Sidebar (Pin/Order)**
    - `User` → [Preference] → `P2.3` → [Update Position] → `D7: Preferences`

### P3: Assignment & Learning Activity (Detailed)

1. **3.1 สร้างภารกิจ (Manage Assignment)**
    - `Teacher` → [Content/Deadline/Type] → `P3.1` → [Save] → `D3: Assignments`
    - `P3.1` → [File Store Request] → `AWS S3`
2. **3.2 เช็คชื่อ (Attendance Tracking)**
    - `Teacher` → [Start Session] → `P3.2` → [Active Code] → `D4: Attendance`
    - `Student` → [Current Code] → `P3.2` → [Verify] → `D5: Submissions`
3. **3.3 ส่งงาน (Submission Flow)**
    - `Student` → [Content/Attachments] → `P3.3` → [Record] → `D5: Submissions`
    - `P3.3` → [Upload] → `AWS S3`

### P4: Grading & Tracking (Detailed)

1. **4.1 ตรวจและให้คะแนน (Grading)**
    - `Teacher` → [Score/Feedback] → `P4.1` → [Update Status] → `D5: Submissions`
    - `P4.1` → [Trigger Reward] → `P5 (Gamification)`
2. **4.2 สรุปผลการเรียน (Grades Calculation)**
    - `D5: Submissions` → [Raw Scores] → `P4.2`
    - `P4.2` → [Calculated Average] → `Teacher (Grade Tab)`

### P5: Gamification & Rewards (Detailed)

1. **5.1 คำนวณ XP & Coins (System Reward)**
    - `System Activity` → [Trigger] → `P5.1` → [Update Stats] → `D6: Gamification`
    - `P5.1` → [Check Level Up] → `User (Notification)`
2. **5.2 ร้านค้าและไอเทม (Store Transaction)**
    - `Student` → [Purchase Request] → `P5.2` → [Check Balance] → `D6: Gamification`
    - `P5.2` → [Record Transaction] → `D8: Transactions`
    - `P5.2` → [Add Item] → `D9: User Items`
3. **5.3 ปลดล็อกความสำเร็จ (Achievement System)**
    - `Activity Data` → [Compare Criteria] → `P5.3` → [Unlock] → `D10: User Achievements`

---

## 5. Data Stores (d)

ที่เก็บข้อมูลสำคัญในระบบ:

- **D1: Users** (ข้อมูลบัญชี, Role)
- **D2: Classrooms** (รายละเอียดห้อง, Code)
- **D3: Assignments** (โจทย์, ประเภท, Due date)
- **D4: Attendance** (Active Sessions)
- **D5: Submissions** (งานที่ส่ง, คะแนน, Feedback)
- **D6: Gamification** (XP, Coins, Level)
- **D7: Preferences** (Sidebar Pinned)
- **D8: Transactions** (ประวัติการใช้เงิน)
- **D9: Store Items** (รายการสินค้า)
- **D10: Achievements/Badges** (รายการความสำเร็จ)
- **D11: Bug Reports** (ข้อมูลปัญหาที่แจ้ง)
