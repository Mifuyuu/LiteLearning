<?php

return [
    'accepted' => ':attribute ต้องได้รับการยอมรับ',
    'before' => ':attribute ต้องเป็นวันที่ก่อน :date',
    'boolean' => ':attribute ต้องเป็นจริงหรือเท็จ',
    'confirmed' => ':attribute และการยืนยันไม่ตรงกัน',
    'date' => ':attribute ต้องเป็นวันที่ที่ถูกต้อง',
    'email' => ':attribute ต้องเป็นอีเมลที่ถูกต้อง',
    'in' => ':attribute ที่เลือกไม่ถูกต้อง',
    'max' => [
        'numeric' => ':attribute ต้องไม่เกิน :max',
        'string' => ':attribute ต้องมีความยาวไม่เกิน :max ตัวอักษร',
    ],
    'min' => [
        'numeric' => ':attribute ต้องมีค่าอย่างน้อย :min',
        'string' => ':attribute ต้องมีความยาวอย่างน้อย :min ตัวอักษร',
    ],
    'size' => [
        'string' => ':attribute ต้องมีความยาว :size ตัวอักษร',
    ],
    'nullable' => ':attribute สามารถเว้นว่างได้',
    'required' => 'กรุณากรอก:attribute',
    'required_if' => 'กรุณากรอก:attribute',
    'string' => ':attribute ต้องเป็นข้อความ',
    'unique' => ':attribute นี้ถูกใช้งานแล้ว',

    'attributes' => [
        'name' => 'ชื่อ',
        'email' => 'อีเมล',
        'password' => 'รหัสผ่าน',
        'password_confirmation' => 'ยืนยันรหัสผ่าน',
        'role' => 'บทบาท',
        'school' => 'สถานศึกษา',
        'school_other' => 'สถานศึกษา',
        'study_year' => 'ชั้นปี',
        'study_year_other' => 'ชั้นปี',
        'birth_date' => 'วัน/เดือน/ปีเกิด',
        'accept_tos' => 'การยอมรับข้อตกลงการใช้งาน',

        // Assignment
        'title' => 'ชื่องาน',
        'description' => 'คำอธิบาย',
        'instructions' => 'คำสั่ง',
        'max_score' => 'คะแนนเต็ม',
        'due_date' => 'กำหนดส่ง',
        'status' => 'สถานะ',
        'type' => 'ประเภท',
        'topic' => 'หัวข้อ',
        'allow_late_submission' => 'อนุญาตส่งงานล่าช้า',

        // Assignment edit
        'editTitle' => 'ชื่องาน',
        'editDescription' => 'คำอธิบาย',
        'editInstructions' => 'คำสั่ง',
        'editMaxScore' => 'คะแนนเต็ม',
        'editDueDate' => 'กำหนดส่ง',
        'editStatus' => 'สถานะ',
        'editType' => 'ประเภท',
        'editTopic' => 'หัวข้อ',

        // Grade
        'score' => 'คะแนน',
        'feedback' => 'ข้อเสนอแนะ',

        // Classroom
        'section' => 'ตอนเรียน',
        'subject' => 'วิชา',
        'theme_color' => 'สีธีม',
        'code' => 'รหัสห้อง',
        'deleteConfirm' => 'ยืนยันการลบ',

        // File upload
        'uploadedFiles' => 'ไฟล์ที่อัปโหลด',
        'uploadedFiles.*' => 'ไฟล์ที่อัปโหลด',
    ],
];
