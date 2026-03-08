<?php

namespace Database\Seeders;

use App\Models\ThemeCategory;
use Illuminate\Database\Seeder;

class ThemeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            [
                'name' => 'ดาวพุธ',
                'color' => '#B5B5B5',
                'planet_number' => 1,
                'sort_order' => 1,
            ],
            [
                'name' => 'ดาวศุกร์',
                'color' => '#E8C87A',
                'planet_number' => 2,
                'sort_order' => 2,
            ],
            [
                'name' => 'โลก',
                'color' => '#4A90D9',
                'planet_number' => 3,
                'sort_order' => 3,
            ],
            [
                'name' => 'ดาวอังคาร',
                'color' => '#C1440E',
                'planet_number' => 4,
                'sort_order' => 4,
            ],
            [
                'name' => 'ดาวพฤหัสบดี',
                'color' => '#C88B3A',
                'planet_number' => 5,
                'sort_order' => 5,
            ],
            [
                'name' => 'ดาวเสาร์',
                'color' => '#D4AA70',
                'planet_number' => 6,
                'sort_order' => 6,
            ],
            [
                'name' => 'ดาวยูเรนัส',
                'color' => '#7DE8E8',
                'planet_number' => 7,
                'sort_order' => 7,
            ],
            [
                'name' => 'ดาวเนปจูน',
                'color' => '#3F54BA',
                'planet_number' => 8,
                'sort_order' => 8,
            ],
            [
                'name' => 'ดาวพลูโต',
                'color' => '#A0826D',
                'planet_number' => 9,
                'sort_order' => 9,
            ],
            [
                'name' => 'ดาวเคราะห์น้อยแดง',
                'color' => '#E05C2A',
                'planet_number' => 10,
                'sort_order' => 10,
            ],
            [
                'name' => 'ดาวเคราะห์ม่วง',
                'color' => '#7B2FBE',
                'planet_number' => 11,
                'sort_order' => 11,
            ],
            [
                'name' => 'ดาวเคราะห์เขียว',
                'color' => '#2ECC71',
                'planet_number' => 12,
                'sort_order' => 12,
            ],
            [
                'name' => 'ดาวเคราะห์ฟ้า',
                'color' => '#5DADE2',
                'planet_number' => 13,
                'sort_order' => 13,
            ],
            [
                'name' => 'ดาวเคราะห์ทอง',
                'color' => '#F4D03F',
                'planet_number' => 14,
                'sort_order' => 14,
            ],
            [
                'name' => 'ดาวเคราะห์ส้ม',
                'color' => '#E67E22',
                'planet_number' => 15,
                'sort_order' => 15,
            ],
            [
                'name' => 'ดาวเคราะห์ชมพู',
                'color' => '#E91E8C',
                'planet_number' => 16,
                'sort_order' => 16,
            ],
            [
                'name' => 'ดาวเคราะห์ลาย',
                'color' => '#8E44AD',
                'planet_number' => 17,
                'sort_order' => 17,
            ],
            [
                'name' => 'ดาวเคราะห์คู่',
                'color' => '#1ABC9C',
                'planet_number' => 18,
                'sort_order' => 18,
            ],
            [
                'name' => 'ดาวเคราะห์วงแหวน',
                'color' => '#F39C12',
                'planet_number' => 19,
                'sort_order' => 19,
            ],
            [
                'name' => 'ดาวเคราะห์น้ำเงินเข้ม',
                'color' => '#1F3A93',
                'planet_number' => 20,
                'sort_order' => 20,
            ],
            [
                'name' => 'ดาวเคราะห์ลึกลับ',
                'color' => '#4A235A',
                'planet_number' => 21,
                'sort_order' => 21,
            ],
        ];

        foreach ($themes as $theme) {
            ThemeCategory::updateOrCreate(
                ['planet_number' => $theme['planet_number']],
                array_merge($theme, ['is_active' => true])
            );
        }
    }
}
