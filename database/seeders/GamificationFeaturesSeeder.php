<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\StoreItem;
use Illuminate\Database\Seeder;

class GamificationFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove stale records not in current data
        Achievement::whereNotIn('code', [
            'first_classroom_joined',
            'first_assignment_turned_in', 'consistent_submitter',
            'perfect_score', 'early_bird', 'social_butterfly', 'on_a_roll',
            'grade_seeker', 'multi_class', 'chatterbox', 'level_up',
        ])->delete();

        // 1. Initial Store Items
        $storeItems = [
            // Name Colors
            ['code' => 'color_red', 'name' => 'Ruby Red', 'description' => 'A shining red name color.', 'type' => 'name_color', 'value' => 'text-red-500', 'price' => 0],
            ['code' => 'color_blue', 'name' => 'Ocean Blue', 'description' => 'A deep blue name color.', 'type' => 'name_color', 'value' => 'text-blue-500', 'price' => 0],
            ['code' => 'color_gold', 'name' => 'Golden Legend', 'description' => 'A prestige gold name color.', 'type' => 'name_color', 'value' => 'text-amber-500 font-bold', 'price' => 0],
            ['code' => 'color_purple', 'name' => 'Royal Purple', 'description' => 'A rich purple name color.', 'type' => 'name_color', 'value' => 'text-purple-600', 'price' => 0],

            // Avatar Frames (Free for testing)
            ['code' => 'frame_01', 'name' => 'Cyber Gear', 'description' => 'A futuristic gear frame.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_01.svg', 'price' => 0],
            ['code' => 'frame_02', 'name' => 'Mystic Aura', 'description' => 'A glowing mystical border.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_02.svg', 'price' => 0],
            ['code' => 'frame_03', 'name' => 'Royal Guard', 'description' => 'A simple royal border.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_03.svg', 'price' => 0],
            ['code' => 'frame_04', 'name' => 'Golden Lion', 'description' => 'A majestic golden frame.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_04.svg', 'price' => 0],
            ['code' => 'frame_05', 'name' => 'Silver Edge', 'description' => 'A sharp silver border.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_05.svg', 'price' => 0],
            ['code' => 'frame_06', 'name' => 'Neon Hex', 'description' => 'Hexagonal neon outline.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_06.svg', 'price' => 0],
            ['code' => 'frame_07', 'name' => 'Flame Ring', 'description' => 'A burning ring of fire.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_07.svg', 'price' => 0],
            ['code' => 'frame_08', 'name' => 'Ice Crystal', 'description' => 'Frozen crystalline frame.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_08.svg', 'price' => 0],
            ['code' => 'frame_09', 'name' => 'Dark Void', 'description' => 'A sinister dark energy frame.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_09.svg', 'price' => 0],
            ['code' => 'frame_10', 'name' => 'Prismatic', 'description' => 'A colorful rainbow border.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_10.svg', 'price' => 0],
        ];

        foreach ($storeItems as $item) {
            StoreItem::updateOrCreate(['code' => $item['code']], $item);
        }

        $achievements = [
            // Synced with GamificationService
            ['code' => 'first_classroom_joined',    'name' => 'ก้าวแรกในห้องเรียน',   'description' => 'เข้าร่วมห้องเรียนครั้งแรก',                    'badge_image' => 'images/achievements/achievements-img-01.svg', 'coin_reward' => 15,  'xp_reward' => 50,  'is_active' => true],
            ['code' => 'first_assignment_turned_in', 'name' => 'ส่งงานครั้งแรก',        'description' => 'ส่งงานครั้งแรกเป็นที่เรียบร้อย',               'badge_image' => 'images/achievements/achievements-img-01.svg', 'coin_reward' => 10,  'xp_reward' => 50,  'is_active' => true],
            ['code' => 'consistent_submitter',      'name' => 'ขยันส่งงาน',             'description' => 'ส่งงานครบทุกชิ้นในห้องเรียน',                  'badge_image' => 'images/achievements/achievements-img-01.svg', 'coin_reward' => 10,  'xp_reward' => 100, 'is_active' => true],
            // Future achievements
            ['code' => 'perfect_score',             'name' => 'Perfectionist',          'description' => 'ได้คะแนนเต็มในงานมอบหมาย',                    'badge_image' => 'images/achievements/achievements-img-01.svg', 'coin_reward' => 200, 'xp_reward' => 500, 'is_active' => true],
            ['code' => 'early_bird',                'name' => 'Early Bird',             'description' => 'ส่งงานก่อนกำหนดอย่างน้อย 1 วัน',               'badge_image' => 'images/achievements/achievements-img-01.svg', 'coin_reward' => 150, 'xp_reward' => 300, 'is_active' => true],
            ['code' => 'social_butterfly',          'name' => 'Social Butterfly',       'description' => 'แสดงความคิดเห็นในโพสต์หรืองาน',                'badge_image' => 'images/achievements/achievements-img-01.svg', 'coin_reward' => 50,  'xp_reward' => 100, 'is_active' => true],
            ['code' => 'on_a_roll',                 'name' => 'On a Roll',              'description' => 'ส่งงานสำเร็จ 5 ชิ้นติดต่อกัน',                 'badge_image' => 'images/achievements/achievements-img-01.svg', 'coin_reward' => 150, 'xp_reward' => 350, 'is_active' => true],
            ['code' => 'grade_seeker',              'name' => 'Grade Seeker',           'description' => 'ได้รับการตรวจงาน 5 ชิ้น',                      'badge_image' => 'images/achievements/achievements-img-01.svg', 'coin_reward' => 100, 'xp_reward' => 250, 'is_active' => true],
            ['code' => 'multi_class',               'name' => 'Multi-Class',            'description' => 'เข้าร่วมห้องเรียน 3 ห้องขึ้นไป',               'badge_image' => 'images/achievements/achievements-img-01.svg', 'coin_reward' => 100, 'xp_reward' => 200, 'is_active' => true],
            ['code' => 'chatterbox',                'name' => 'Chatterbox',             'description' => 'แสดงความคิดเห็น 5 ครั้ง',                      'badge_image' => 'images/achievements/achievements-img-01.svg', 'coin_reward' => 75,  'xp_reward' => 150, 'is_active' => true],
            ['code' => 'level_up',                  'name' => 'Level Up!',              'description' => 'ไปถึง Level 5',                                 'badge_image' => 'images/achievements/achievements-img-01.svg', 'coin_reward' => 200, 'xp_reward' => 400, 'is_active' => true],
        ];

        foreach ($achievements as $ach) {
            Achievement::updateOrCreate(['code' => $ach['code']], $ach);
        }
    }
}
