<?php

namespace Database\Seeders;

use App\Models\ThemeCategory;
use Illuminate\Database\Seeder;

class ThemeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            ['name' => 'ดาวอัลมอนด์', 'color' => '#B85623', 'planet_key' => 'almond'],
            ['name' => 'ดาวชีส', 'color' => '#FFD663', 'planet_key' => 'cheese'],
            ['name' => 'ดาวช็อกโกแลต', 'color' => '#C8774B', 'planet_key' => 'chocolate'],
            ['name' => 'ดาวกาแฟ', 'color' => '#C2793B', 'planet_key' => 'coffee'],
            ['name' => 'หลุมดำ', 'color' => '#9D4CFF', 'planet_key' => 'darkhole'],
            ['name' => 'โลก', 'color' => '#00A4D2', 'planet_key' => 'earth'],
            ['name' => 'ดาวมรกต', 'color' => '#60D79E', 'planet_key' => 'emerald'],
            ['name' => 'ดาวเขียวสด', 'color' => '#89DA2B', 'planet_key' => 'evergreen'],
            ['name' => 'ดาวคริปตัน', 'color' => '#F3414E', 'planet_key' => 'krypton'],
            ['name' => 'ดาวรัก', 'color' => '#DE3F76', 'planet_key' => 'lovely'],
            ['name' => 'ดาวเนปจูน', 'color' => '#3CBEBE', 'planet_key' => 'neptune'],
            ['name' => 'ดาวพลูโต', 'color' => '#E4E4E4', 'planet_key' => 'pluto'],
            ['name' => 'ดาวทับทิม', 'color' => '#F15D75', 'planet_key' => 'ruby'],
            ['name' => 'ดาวเสาร์', 'color' => '#E08A3C', 'planet_key' => 'saturn'],
            ['name' => 'ดวงอาทิตย์', 'color' => '#FC8827', 'planet_key' => 'sun'],
            ['name' => 'ดาวยูเรนัส', 'color' => '#7E91A8', 'planet_key' => 'uranus'],
            ['name' => 'ดาวไวรัส', 'color' => '#1D848C', 'planet_key' => 'virus'],
            ['name' => 'ความว่างเปล่า', 'color' => '#E0A6FE', 'planet_key' => 'void'],
            ['name' => 'ดาวน้ำตก', 'color' => '#6F74C6', 'planet_key' => 'waterfall'],
            ['name' => 'หลุมขาว', 'color' => '#484848', 'planet_key' => 'whitehole'],
        ];

        foreach ($themes as $theme) {
            ThemeCategory::updateOrCreate(
                ['planet_key' => $theme['planet_key']],
                array_merge($theme, ['is_active' => true])
            );
        }
    }
}
