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
            'multi_class', 'chatterbox', 'level_up', 'explorer', 'collector',
        ])->delete();

        // 1. Initial Store Items
        $storeItems = [
            // Name Colors
            ['code' => 'color_red', 'name' => 'แดงทับทิม', 'description' => 'สีชื่อโทนแดงสดใส มั้ง?', 'type' => 'name_color', 'value' => 'text-red-500', 'price' => 0],
            ['code' => 'color_blue', 'name' => 'น้ำเงินมหาสมุทร', 'description' => 'สีชื่อโทนน้ำเงินเข้มแบบธรรมดา', 'type' => 'name_color', 'value' => 'text-blue-500', 'price' => 0],
            ['code' => 'color_gold', 'name' => 'ทองแวววาว', 'description' => 'สีชื่อทองสุดหรูหราใส่ทีก็แสบตากันไปข้าง', 'type' => 'name_color', 'value' => 'text-amber-500 font-bold', 'price' => 0],
            ['code' => 'color_purple', 'name' => 'ม่วงลึกลับ', 'description' => 'สีชื่อโทนม่วงเข้มที่แสดงถึงความลึกลับ', 'type' => 'name_color', 'value' => 'text-purple-600', 'price' => 0],

            // Avatar Frames
            ['code' => 'shark_frame', 'name' => 'หมวกหัวฉลาม', 'description' => 'หมวกหัวฉลามที่แสนจะธรรมดา', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_01.svg', 'price' => 0],
            ['code' => 'orangehat_frame', 'name' => 'หมวกไหมพรมส้ม', 'description' => 'กรอบหมวกไหมพรมส้มซึ่งเข้ากับคุณพอดี', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_02.svg', 'price' => 0],
            ['code' => 'gum_frame', 'name' => 'หมากฝรั่ง', 'description' => 'ใครให้เคี้ยวหมากฝรั่งในห้องเรียน!!!', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_03.svg', 'price' => 0],
            ['code' => 'flowerpot_frame', 'name' => 'กระถางต้นไม้', 'description' => 'กระถางต้นไม้บนหัวนั่นมันอะไรกัน?', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_04.svg', 'price' => 0],
            ['code' => 'book_frame', 'name' => 'หนังสือ', 'description' => 'อ่านบ้างนะหนังสือ ไม่ใช่วางไว้สวยๆ', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_05.svg', 'price' => 0],
            ['code' => 'chicken_frame', 'name' => 'ลูกเจี๊ยบบนหัว', 'description' => 'สัตว์สุดแสนจะน่ารักที่สักวันฝันจะเป็นวิ้งแซบ', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_06.svg', 'price' => 0],
            ['code' => 'artisthat_frame', 'name' => 'หมวกจิตรกร', 'description' => 'หมวกที่ใสแล้วเหมือนจิตรกร แค่เหมือนนะ', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_07.svg', 'price' => 0],
            ['code' => 'black_frame', 'name' => 'หมวกดำ', 'description' => 'ก็แค่หมวกสีดำที่ไม่จำเป็นต้องซัก', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_08.svg', 'price' => 0],
            ['code' => 'jellyfish_frame', 'name' => 'หมวกแมงกะพรุน', 'description' => 'หมวกแมงกระพรุนที่ถักมาจากพลาสติก', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_09.svg', 'price' => 0],
            ['code' => 'squid_frame', 'name' => 'หมวกปลาหมึก', 'description' => 'ปลาหมึก มีปลาแต่ไม่ใช่ปลา สรุปปลามั้ย?', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_10.svg', 'price' => 0],
            ['code' => 'blackcat_frame', 'name' => 'น้องแมวดำอวกาศ', 'description' => 'กรอบรูปโปรไฟล์สไตล์น้องแมวดำอวกาศสุดน่ารัก..', 'type' => 'avatar_frame', 'value' => 'images/frames/npj6OXlEfykfwLh6GyMgDpw96KMt2fUtpliZv2wg.png', 'price' => 0],
            ['code' => 'pinkhat_frame', 'name' => 'หมวกชมพู', 'description' => 'กรอบหมวกสีชมพูสุดหวาน', 'type' => 'avatar_frame', 'value' => 'images/frames/swtYh9W7WswhheUqyVSXPIHaA0QJpD2tFAr1F2Hn.png', 'price' => 100],
        ];

        foreach ($storeItems as $item) {
            StoreItem::updateOrCreate(['code' => $item['code']], $item);
        }

        $achievements = [
            // Synced with GamificationService
            // Reward tiers by difficulty (floor: 75 coin / 100 xp):
            //   Trivial (one-off, zero effort)      → 75  / 100
            //   Easy (small repeated habit)          → 100 / 150
            //   Moderate (sustained behavior)         → 150 / 250
            //   Hard (skill or cumulative)            → 200 / 400
            //   Ultimate (meta: collect everything)   → 350 / 700
            ['code' => 'first_classroom_joined',    'name' => 'ก้าวแรกในห้องเรียน',     'description' => 'เข้าร่วมห้องเรียนครั้งแรก',                    'badge_image' => 'images/achievements/Achievements_Novice.png', 'coin_reward' => 75,  'xp_reward' => 100, 'is_active' => true],
            ['code' => 'first_assignment_turned_in', 'name' => 'ส่งงานครั้งแรก',         'description' => 'ส่งงานครั้งแรกเป็นที่เรียบร้อย',               'badge_image' => 'images/achievements/Achievements_Correctly.png', 'coin_reward' => 75,  'xp_reward' => 100, 'is_active' => true],
            ['code' => 'consistent_submitter',      'name' => 'ขยันส่งงาน',              'description' => 'ส่งงานครบทุกชิ้นในห้องเรียน',                  'badge_image' => 'images/achievements/Achievements_Hardworker.png', 'coin_reward' => 150, 'xp_reward' => 250, 'is_active' => true],
            // Future achievements
            ['code' => 'perfect_score',             'name' => 'คะแนนเต็ม',              'description' => 'ได้คะแนนเต็มในงานมอบหมาย',                    'badge_image' => 'images/achievements/Achievements_Perfectionist.png', 'coin_reward' => 200, 'xp_reward' => 400, 'is_active' => true],
            ['code' => 'early_bird',                'name' => 'ส่งก่อนใคร',             'description' => 'ส่งงานก่อนกำหนดอย่างน้อย 1 วัน',               'badge_image' => 'images/achievements/Achievements_QuickSubmiter.png', 'coin_reward' => 150, 'xp_reward' => 250, 'is_active' => true],
            ['code' => 'social_butterfly',          'name' => 'กล้าแสดงออก',            'description' => 'แสดงความคิดเห็นในโพสต์หรืองาน',                'badge_image' => 'images/achievements/Achievements_Extrovert.png', 'coin_reward' => 75,  'xp_reward' => 100, 'is_active' => true],
            ['code' => 'on_a_roll',                 'name' => 'สายส่งไม่หยุด',          'description' => 'ส่งงานสำเร็จรวม 5 ชิ้น',                       'badge_image' => 'images/achievements/Achievements_CreativeMan.png', 'coin_reward' => 150, 'xp_reward' => 250, 'is_active' => true],
            ['code' => 'multi_class',               'name' => 'นักเรียนรอบด้าน',        'description' => 'เข้าร่วมห้องเรียน 3 ห้องขึ้นไป',               'badge_image' => 'images/achievements/Achievements_Learner.png', 'coin_reward' => 100, 'xp_reward' => 150, 'is_active' => true],
            ['code' => 'chatterbox',                'name' => 'คอมเมนต์ตัวยง',          'description' => 'แสดงความคิดเห็น 5 ครั้ง',                      'badge_image' => 'images/achievements/Achievements_InTheParty.png', 'coin_reward' => 100, 'xp_reward' => 150, 'is_active' => true],
            ['code' => 'level_up',                  'name' => 'ก้าวสู่เลเวล 5',         'description' => 'ไปถึง Level 5',                                 'badge_image' => 'images/achievements/Achievements_LevelUp.png', 'coin_reward' => 200, 'xp_reward' => 400, 'is_active' => true],
            ['code' => 'explorer',                  'name' => 'ก้าวแรกสู่ระบบ',         'description' => 'เข้าสู่ระบบครั้งแรกหลังสมัครสมาชิก',           'badge_image' => 'images/achievements/Achievements_Explorer.png', 'coin_reward' => 75,  'xp_reward' => 100, 'is_active' => true],
            ['code' => 'collector',                 'name' => 'นักสะสมความสำเร็จ',      'description' => 'ปลดล็อกความสำเร็จอื่นครบทุกอย่าง',              'badge_image' => 'images/achievements/Achievements_Collector.png', 'coin_reward' => 350, 'xp_reward' => 700, 'is_active' => true],
        ];

        foreach ($achievements as $ach) {
            Achievement::updateOrCreate(['code' => $ach['code']], $ach);
        }
    }
}
