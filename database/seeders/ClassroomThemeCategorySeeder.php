<?php

namespace Database\Seeders;

use App\Models\ClassroomThemeCategory;
use Illuminate\Database\Seeder;

class ClassroomThemeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            [
                'name' => 'ดาวพุธ',
                'description' => 'ดาวเคราะห์ที่เล็กที่สุดและอยู่ใกล้ดวงอาทิตย์มากที่สุด',
                'preview_color' => '#B5B5B5',
                'planet_number' => 1,
                'sort_order' => 1,
            ],
            [
                'name' => 'ดาวศุกร์',
                'description' => 'ดาวเคราะห์ที่ร้อนที่สุดในระบบสุริยะ ส่องสว่างที่สุดบนท้องฟ้า',
                'preview_color' => '#E8C87A',
                'planet_number' => 2,
                'sort_order' => 2,
            ],
            [
                'name' => 'โลก',
                'description' => 'บ้านของเรา ดาวเคราะห์สีน้ำเงินในอวกาศ',
                'preview_color' => '#4A90D9',
                'planet_number' => 3,
                'sort_order' => 3,
            ],
            [
                'name' => 'ดาวอังคาร',
                'description' => 'ดาวแดง ดาวเคราะห์ที่มีภูเขาไฟสูงที่สุดในระบบสุริยะ',
                'preview_color' => '#C1440E',
                'planet_number' => 4,
                'sort_order' => 4,
            ],
            [
                'name' => 'ดาวพฤหัสบดี',
                'description' => 'ดาวเคราะห์ที่ใหญ่ที่สุดในระบบสุริยะ มีพายุจุดแดงใหญ่',
                'preview_color' => '#C88B3A',
                'planet_number' => 5,
                'sort_order' => 5,
            ],
            [
                'name' => 'ดาวเสาร์',
                'description' => 'ดาวเคราะห์ที่มีวงแหวนสวยงาม เป็นที่รู้จักมากที่สุด',
                'preview_color' => '#D4AA70',
                'planet_number' => 6,
                'sort_order' => 6,
            ],
            [
                'name' => 'ดาวยูเรนัส',
                'description' => 'ดาวเคราะห์สีน้ำเงินอมเขียว หมุนตะแคงข้าง',
                'preview_color' => '#7DE8E8',
                'planet_number' => 7,
                'sort_order' => 7,
            ],
            [
                'name' => 'ดาวเนปจูน',
                'description' => 'ดาวเคราะห์ที่อยู่ไกลที่สุดในระบบสุริยะ มีพายุรุนแรงที่สุด',
                'preview_color' => '#3F54BA',
                'planet_number' => 8,
                'sort_order' => 8,
            ],
            [
                'name' => 'ดาวพลูโต',
                'description' => 'ดาวเคราะห์แคระ ขอบเขตของระบบสุริยะ',
                'preview_color' => '#A0826D',
                'planet_number' => 9,
                'sort_order' => 9,
            ],
            [
                'name' => 'ดาวเคราะห์น้อยแดง',
                'description' => 'โลกของนักสำรวจ ร้อนแรงและเต็มไปด้วยความท้าทาย',
                'preview_color' => '#E05C2A',
                'planet_number' => 10,
                'sort_order' => 10,
            ],
            [
                'name' => 'ดาวเคราะห์ม่วง',
                'description' => 'ดาวแห่งความลึกลับ เต็มไปด้วยพลังงานมืด',
                'preview_color' => '#7B2FBE',
                'planet_number' => 11,
                'sort_order' => 11,
            ],
            [
                'name' => 'ดาวเคราะห์เขียว',
                'description' => 'โลกที่เต็มไปด้วยธรรมชาติและชีวิต',
                'preview_color' => '#2ECC71',
                'planet_number' => 12,
                'sort_order' => 12,
            ],
            [
                'name' => 'ดาวเคราะห์ฟ้า',
                'description' => 'ดาวแห่งน้ำแข็งและความเงียบสงบ',
                'preview_color' => '#5DADE2',
                'planet_number' => 13,
                'sort_order' => 13,
            ],
            [
                'name' => 'ดาวเคราะห์ทอง',
                'description' => 'ดาวแห่งความมั่งคั่งและปัญญา',
                'preview_color' => '#F4D03F',
                'planet_number' => 14,
                'sort_order' => 14,
            ],
            [
                'name' => 'ดาวเคราะห์ส้ม',
                'description' => 'ดาวแห่งพลังงานและความกระตือรือร้น',
                'preview_color' => '#E67E22',
                'planet_number' => 15,
                'sort_order' => 15,
            ],
            [
                'name' => 'ดาวเคราะห์ชมพู',
                'description' => 'ดาวแห่งความอ่อนโยนและความคิดสร้างสรรค์',
                'preview_color' => '#E91E8C',
                'planet_number' => 16,
                'sort_order' => 16,
            ],
            [
                'name' => 'ดาวเคราะห์ลาย',
                'description' => 'ดาวหลายสี เต็มไปด้วยความหลากหลาย',
                'preview_color' => '#8E44AD',
                'planet_number' => 17,
                'sort_order' => 17,
            ],
            [
                'name' => 'ดาวเคราะห์คู่',
                'description' => 'ระบบดาวคู่ที่โคจรรอบกันและกัน',
                'preview_color' => '#1ABC9C',
                'planet_number' => 18,
                'sort_order' => 18,
            ],
            [
                'name' => 'ดาวเคราะห์วงแหวน',
                'description' => 'ดาวที่มีวงแหวนพิเศษ สวยงามยิ่งกว่าดาวเสาร์',
                'preview_color' => '#F39C12',
                'planet_number' => 19,
                'sort_order' => 19,
            ],
            [
                'name' => 'ดาวเคราะห์น้ำเงินเข้ม',
                'description' => 'ดาวแห่งความลึกของมหาสมุทรอวกาศ',
                'preview_color' => '#1F3A93',
                'planet_number' => 20,
                'sort_order' => 20,
            ],
            [
                'name' => 'ดาวเคราะห์ลึกลับ',
                'description' => 'ดาวที่ยังไม่ถูกค้นพบ เต็มไปด้วยความมหัศจรรย์',
                'preview_color' => '#4A235A',
                'planet_number' => 21,
                'sort_order' => 21,
            ],
        ];

        foreach ($themes as $theme) {
            ClassroomThemeCategory::updateOrCreate(
                ['planet_number' => $theme['planet_number']],
                array_merge($theme, ['is_active' => true])
            );
        }
    }
}
