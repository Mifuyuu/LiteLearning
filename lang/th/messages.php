<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */

    'auth.login_throttle'           => 'คุณพยายามเข้าสู่ระบบหลายครั้งเกินไป กรุณาลองอีกครั้งใน :seconds วินาที',
    'auth.login_failed'             => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
    'auth.register_otp_fast'        => 'ส่งรหัสบ่อยเกินไป กรุณารอ :seconds วินาที',
    'auth.register_otp_too_many'    => 'ลองผิดบ่อยเกินไป กรุณารอ :seconds วินาที',
    'auth.register_otp_expired'     => 'รหัสหมดอายุแล้ว กรุณาขอรหัสใหม่',
    'auth.register_otp_wrong'       => 'รหัสไม่ถูกต้อง',
    'auth.register_role_invalid'    => 'บทบาทไม่ถูกต้อง',
    'auth.forgot_email_not_found'   => 'ไม่พบอีเมลนี้ในระบบ',
    'auth.forgot_otp_fast'          => 'ส่งรหัสบ่อยเกินไป กรุณารอ :seconds วินาที',
    'auth.forgot_otp_too_many'      => 'ลองผิดบ่อยเกินไป กรุณารอ :seconds วินาที',
    'auth.forgot_otp_expired'       => 'รหัสหมดอายุแล้ว กรุณาขอรหัสใหม่',
    'auth.forgot_otp_wrong'         => 'รหัสไม่ถูกต้อง',
    'auth.forgot_account_disabled'  => 'บัญชีนี้ถูกปิดใช้งานแล้ว',
    'auth.forgot_password_reset'    => 'รีเซ็ตรหัสผ่านเรียบร้อยแล้ว',
    'auth.forgot_reset_expired'     => 'เวลาสำหรับการตั้งรหัสผ่านใหม่หมดอายุแล้ว กรุณาขอรหัสยืนยันใหม่อีกครั้ง',
    'auth.otp_digits'               => 'รหัส OTP ต้องเป็น 6 หลัก',
    'auth.password_min'             => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
    'auth.password_mismatch'        => 'รหัสผ่านไม่ตรงกัน',

    /*
    |--------------------------------------------------------------------------
    | Classroom
    |--------------------------------------------------------------------------
    */

    'classroom.settings_saved'      => 'บันทึกการตั้งค่าเรียบร้อยแล้ว',
    'classroom.archived'            => 'เก็บถาวรห้องเรียนแล้ว',
    'classroom.restored'            => 'กู้คืนห้องเรียนแล้ว',
    'classroom.delete_confirm'      => 'กรุณาพิมพ์ชื่อห้องเรียนให้ตรงเพื่อยืนยันการลบ',
    'classroom.deleted'             => 'ลบห้องเรียน :name แล้ว',
    'classroom.co_teacher_added'    => 'เพิ่มผู้สอนร่วมเรียบร้อยแล้ว',
    'classroom.co_teacher_removed'  => 'ลบผู้สอนร่วมเรียบร้อยแล้ว',
    'classroom.member_removed'      => 'นำสมาชิกออกเรียบร้อยแล้ว',
    'classroom.all_students_removed'=> 'นำนักเรียนทั้งหมดออกเรียบร้อยแล้ว',
    'classroom.user_not_found'      => 'ไม่พบผู้ใช้งานนี้ในระบบ',
    'classroom.already_owner'       => 'ผู้ใช้นี้เป็นเจ้าของห้องอยู่แล้ว',
    'classroom.teacher_only'        => 'สามารถเพิ่มผู้สอนร่วมได้เฉพาะบัญชีอาจารย์เท่านั้น',
    'classroom.already_co_teacher'  => 'ผู้ใช้นี้เป็นผู้สอนร่วมอยู่แล้ว',
    'classroom.join_throttle'       => 'พยายามมากเกินไป กรุณารอสักครู่',
    'classroom.code_not_found'      => 'ไม่พบห้องเรียนด้วยรหัสนี้',
    'classroom.already_owner_join'  => 'คุณเป็นครูเจ้าของห้องเรียนนี้',
    'classroom.already_member'      => 'คุณเป็นสมาชิกของห้องเรียนนี้อยู่แล้ว',

    /*
    |--------------------------------------------------------------------------
    | Assignment & Grade
    |--------------------------------------------------------------------------
    */

    'assignment.closed'             => 'ปิดรับงานแล้ว',
    'assignment.file_uploaded'      => 'อัปโหลดไฟล์เรียบร้อยแล้ว',
    'assignment.draft_saved'        => 'บันทึกฉบับร่างแล้ว',
    'assignment.updated'            => 'บันทึกงานแล้ว',
    'assignment.graded'             => 'ให้คะแนนเรียบร้อยแล้ว',
    'assignment.returned'           => 'ส่งคืนนักเรียนแล้ว',

    'grade.score_required'          => 'กรุณากรอกคะแนน',
    'grade.score_number'            => 'คะแนนต้องเป็นตัวเลข',
    'grade.score_min'               => 'คะแนนต้องไม่น้อยกว่า :min',
    'grade.score_max'               => 'คะแนนต้องไม่เกิน :max',

    /*
    |--------------------------------------------------------------------------
    | Attendance
    |--------------------------------------------------------------------------
    */

    'attendance.not_open'           => 'เซสชันเช็คชื่อยังไม่เปิด',
    'attendance.already'            => 'เช็คชื่อแล้ว',
    'attendance.code_expired'       => 'รหัสเช็คชื่อหมดอายุ',
    'attendance.wrong_code'         => 'รหัสไม่ถูกต้อง',
    'attendance.success'            => 'เช็คชื่อเรียบร้อยแล้ว',
    'attendance.throttle'           => 'ลองใส่รหัสหลายครั้งเกินไป กรุณาลองอีกครั้งใน :seconds วินาที',

    /*
    |--------------------------------------------------------------------------
    | Material
    |--------------------------------------------------------------------------
    */

    'material.created'              => 'สร้างเอกสารเรียบร้อยแล้ว',
    'material.updated'              => 'บันทึกเอกสารแล้ว',

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    'admin.cannot_self_disable'     => 'ไม่สามารถปิดใช้งานบัญชีของตัวเองได้',
    'admin.user_status_updated'     => 'อัปเดตสถานะผู้ใช้แล้ว',
    'admin.role_invalid'            => 'บทบาทไม่ถูกต้อง',
    'admin.cannot_self_demote'      => 'ไม่สามารถยกเลิกบทบาทผู้ดูแลของตัวเองได้',
    'admin.user_role_updated'       => 'อัปเดตบทบาทผู้ใช้เป็น :role แล้ว',
    'admin.user_deleted'            => 'ลบผู้ใช้แล้ว',
    'admin.classroom_deleted'       => 'ลบห้องเรียนแล้ว',
    'admin.store_updated'           => 'บันทึกสินค้าแล้ว',
    'admin.store_created'           => 'เพิ่มสินค้าแล้ว',
    'admin.store_status_updated'    => 'อัปเดตสถานะสินค้าแล้ว',
    'admin.store_deleted'           => 'ลบสินค้าแล้ว',
    'admin.achievement_updated'     => 'บันทึกความสำเร็จแล้ว',
    'admin.achievement_created'     => 'เพิ่มความสำเร็จแล้ว',
    'admin.achievement_status_updated' => 'อัปเดตสถานะความสำเร็จแล้ว',
    'admin.achievement_deleted'     => 'ลบความสำเร็จแล้ว',
    'admin.theme_name_required'     => 'กรุณาระบุชื่อหมวดหมู่',
    'admin.theme_color_hex'         => 'กรุณาระบุสีในรูปแบบ hex (#RRGGBB)',
    'admin.theme_planet_required'   => 'กรุณาเลือกดาวเคราะห์',
    'admin.theme_planet_range'      => 'หมายเลขดาวเคราะห์ต้องอยู่ระหว่าง :min-:max',
    'admin.theme_updated'           => 'บันทึกหมวดหมู่แล้ว',
    'admin.theme_created'           => 'สร้างหมวดหมู่แล้ว',
    'admin.theme_deleted'           => 'ลบหมวดหมู่แล้ว',

    /*
    |--------------------------------------------------------------------------
    | Student / Store & Inventory
    |--------------------------------------------------------------------------
    */

    'store.purchased'               => 'ซื้อไอเทมเรียบร้อยแล้ว ไปที่คลังเก็บของเพื่อสวมใส่',
    'store.equipped'                => 'สวมใส่ไอเทมเรียบร้อยแล้ว',
    'store.not_owned'               => 'คุณไม่มีไอเทมนี้',
    'store.unequipped'              => 'ถอดไอเทมเรียบร้อยแล้ว',
    'store.equip_error'             => 'ไม่สามารถสวมใส่ไอเทมได้ กรุณาลองอีกครั้ง',

    /*
    |--------------------------------------------------------------------------
    | Profile Settings
    |--------------------------------------------------------------------------
    */

    'profile.saved'                 => 'บันทึกการเปลี่ยนแปลงเรียบร้อยแล้ว',
    'profile.avatar_reset'          => 'รีเซ็ตรูปโปรไฟล์เรียบร้อยแล้ว',
    'profile.cover_reset'           => 'รีเซ็ตรูปปกเรียบร้อยแล้ว',
    'profile.avatar_updated'        => 'อัปเดตรูปโปรไฟล์เรียบร้อยแล้ว',
    'profile.cover_updated'         => 'อัปเดตรูปปกเรียบร้อยแล้ว',
    'profile.upload_failed'         => 'อัปโหลดล้มเหลว กรุณาลองอีกครั้ง',

    /*
    |--------------------------------------------------------------------------
    | Report Bug
    |--------------------------------------------------------------------------
    */

    'report.throttle'               => 'คุณส่งรายงานบ่อยเกินไป กรุณารอสักครู่แล้วลองอีกครั้ง',
    'report.success'                => 'ส่งรายงานแล้ว ขอบคุณสำหรับข้อมูล',

    /*
    |--------------------------------------------------------------------------
    | Validation — domain-specific required messages
    |--------------------------------------------------------------------------
    */

    'validation.name'               => 'กรุณากรอกชื่อ',
    'validation.username'           => 'กรุณากรอกชื่อผู้ใช้',
    'validation.username_format'    => 'ชื่อผู้ใช้ต้องขึ้นต้นด้วยตัวอักษรภาษาอังกฤษ และมีได้เฉพาะตัวอักษร ตัวเลข จุด หรือ _',
    'validation.username_taken'     => 'ชื่อผู้ใช้นี้มีคนใช้แล้ว',
    'validation.name_classroom'     => 'กรุณากรอกชื่อห้องเรียน',
    'validation.name_store_item'    => 'กรุณากรอกชื่อสินค้า',
    'validation.name_achievement'   => 'กรุณากรอกชื่อความสำเร็จ',
    'validation.title_assignment'   => 'กรุณากรอกชื่องาน',
    'validation.title_material'     => 'กรุณากรอกชื่อเอกสาร',
    'validation.title_report'       => 'กรุณากรอกหัวข้อ',
    'validation.section'            => 'กรุณากรอกตอนเรียน',
    'validation.description'        => 'กรุณากรอกรายละเอียด',
    'validation.email'              => 'กรุณากรอกอีเมล',
    'validation.password'           => 'กรุณากรอกรหัสผ่าน',
    'validation.password_confirm'   => 'กรุณากรอกยืนยันรหัสผ่าน',
    'validation.role'               => 'กรุณาเลือกบทบาท',
    'validation.code_classroom'     => 'กรุณากรอกรหัสห้องเรียน',
    'validation.code_store'         => 'กรุณากรอกรหัสสินค้า',
    'validation.code_achievement'   => 'กรุณากรอกรหัสความสำเร็จ',
    'validation.comment'            => 'กรุณากรอกข้อความ',
    'validation.type_assignment'    => 'กรุณาเลือกประเภทงาน',
    'validation.type_report'        => 'กรุณาเลือกประเภท',
    'validation.type_store'         => 'กรุณาเลือกประเภท',
    'validation.status'             => 'กรุณาเลือกสถานะ',
    'validation.max_score'          => 'กรุณากรอกคะแนนเต็ม',
    'validation.topic'              => 'กรุณากรอกชื่อหัวข้อ',
    'validation.value_store'        => 'กรุณากรอกค่าสินค้า',
    'validation.price'              => 'กรุณากรอกราคา',
    'validation.coin_reward'        => 'กรุณากรอกรางวัลเหรียญ',
    'validation.xp_reward'          => 'กรุณากรอกรางวัล XP',
];
