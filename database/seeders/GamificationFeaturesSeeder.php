<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Achievement;
use App\Models\Badge;
use App\Models\StoreItem;

class GamificationFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove stale records not in current data
        Badge::whereNotIn('code', ['top_student', 'helpful_peer', 'consistent', 'quick_learner', 'star_pupil', 'explorer', 'night_owl', 'streak_master'])->delete();
        Achievement::whereNotIn('code', ['first_login', 'first_assignment', 'perfect_score', 'early_bird', 'social_butterfly', 'on_a_roll', 'grade_seeker', 'multi_class', 'chatterbox', 'level_up'])->delete();

        // 1. Initial Store Items
        $storeItems = [
            // Name Colors
            ['code' => 'color_red', 'name' => 'Ruby Red', 'description' => 'A shining red name color.', 'type' => 'name_color', 'value' => 'text-red-500', 'price' => 100],
            ['code' => 'color_blue', 'name' => 'Ocean Blue', 'description' => 'A deep blue name color.', 'type' => 'name_color', 'value' => 'text-blue-500', 'price' => 100],
            ['code' => 'color_gold', 'name' => 'Golden Legend', 'description' => 'A prestige gold name color.', 'type' => 'name_color', 'value' => 'text-amber-500 font-bold', 'price' => 500],
            ['code' => 'color_purple', 'name' => 'Royal Purple', 'description' => 'A rich purple name color.', 'type' => 'name_color', 'value' => 'text-purple-600', 'price' => 250],
            
            // Avatar Frames
            ['code' => 'frame_01', 'name' => 'Cyber Gear', 'description' => 'A futuristic gear frame.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_01.svg', 'price' => 500],
            ['code' => 'frame_02', 'name' => 'Mystic Aura', 'description' => 'A glowing mystical border.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_02.svg', 'price' => 500],
            ['code' => 'frame_03', 'name' => 'Royal Guard', 'description' => 'A simple royal border.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_03.svg', 'price' => 300],
            ['code' => 'frame_04', 'name' => 'Golden Lion', 'description' => 'A majestic golden frame.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_04.svg', 'price' => 1000],
            ['code' => 'frame_05', 'name' => 'Silver Edge', 'description' => 'A sharp silver border.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_05.svg', 'price' => 400],
            ['code' => 'frame_06', 'name' => 'Neon Hex', 'description' => 'Hexagonal neon outline.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_06.svg', 'price' => 750],
            ['code' => 'frame_07', 'name' => 'Flame Ring', 'description' => 'A burning ring of fire.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_07.svg', 'price' => 800],
            ['code' => 'frame_08', 'name' => 'Ice Crystal', 'description' => 'Frozen crystalline frame.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_08.svg', 'price' => 800],
            ['code' => 'frame_09', 'name' => 'Dark Void', 'description' => 'A sinister dark energy frame.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_09.svg', 'price' => 1200],
            ['code' => 'frame_10', 'name' => 'Prismatic', 'description' => 'A colorful rainbow border.', 'type' => 'avatar_frame', 'value' => '/images/frames/Avatar_Frame_10.svg', 'price' => 1500],
        ];

        foreach ($storeItems as $item) {
            StoreItem::updateOrCreate(['code' => $item['code']], $item);
        }

        // 2. Initial Achievements
        $achievements = [
            ['code' => 'first_login',      'name' => 'Welcome Aboard',   'description' => 'Log in to the platform for the first time.',              'icon' => 'fas fa-door-open',     'coin_reward' => 50,  'xp_reward' => 100,  'is_active' => true, 'target_role' => 'student'],
            ['code' => 'first_assignment', 'name' => 'First Blood',      'description' => 'Complete your first assignment.',                          'icon' => 'fas fa-check-circle',  'coin_reward' => 100, 'xp_reward' => 200,  'is_active' => true, 'target_role' => 'student'],
            ['code' => 'perfect_score',    'name' => 'Perfectionist',    'description' => 'Get a perfect score on an assignment.',                    'icon' => 'fas fa-star',          'coin_reward' => 200, 'xp_reward' => 500,  'is_active' => true, 'target_role' => 'student'],
            ['code' => 'early_bird',       'name' => 'Early Bird',       'description' => 'Submit an assignment well before the deadline.',           'icon' => 'fas fa-clock',         'coin_reward' => 150, 'xp_reward' => 300,  'is_active' => true, 'target_role' => 'student'],
            ['code' => 'social_butterfly', 'name' => 'Social Butterfly', 'description' => 'Leave a comment on a post or assignment.',                 'icon' => 'fas fa-comments',      'coin_reward' => 50,  'xp_reward' => 100,  'is_active' => true, 'target_role' => 'student'],
            ['code' => 'on_a_roll',        'name' => 'On a Roll',        'description' => 'Submit 5 assignments successfully.',                       'icon' => 'fas fa-fire-alt',      'coin_reward' => 150, 'xp_reward' => 350,  'is_active' => true, 'target_role' => 'student'],
            ['code' => 'grade_seeker',     'name' => 'Grade Seeker',     'description' => 'Get graded on 5 assignments.',                             'icon' => 'fas fa-graduation-cap','coin_reward' => 100, 'xp_reward' => 250,  'is_active' => true, 'target_role' => 'student'],
            ['code' => 'multi_class',      'name' => 'Multi-Class',      'description' => 'Join 3 or more classrooms.',                               'icon' => 'fas fa-layer-group',   'coin_reward' => 100, 'xp_reward' => 200,  'is_active' => true, 'target_role' => 'student'],
            ['code' => 'chatterbox',       'name' => 'Chatterbox',       'description' => 'Leave 5 comments on posts or assignments.',                'icon' => 'fas fa-comment-dots',  'coin_reward' => 75,  'xp_reward' => 150,  'is_active' => true, 'target_role' => 'student'],
            ['code' => 'level_up',         'name' => 'Level Up!',        'description' => 'Reach level 5 on the platform.',                          'icon' => 'fas fa-arrow-up',      'coin_reward' => 200, 'xp_reward' => 400,  'is_active' => true, 'target_role' => 'student'],
        ];

        foreach ($achievements as $ach) {
            Achievement::updateOrCreate(['code' => $ach['code']], $ach);
        }

        // 3. Initial Badges
        $badges = [
            ['code' => 'top_student',   'name' => 'Top Student',    'description' => 'Awarded for exceptional academic performance.', 'icon' => 'fas fa-crown',          'color' => 'text-amber-500',   'target_role' => 'student'],
            ['code' => 'helpful_peer',  'name' => 'Helpful Peer',   'description' => 'Awarded for helping other students.',           'icon' => 'fas fa-hands-helping', 'color' => 'text-emerald-500', 'target_role' => 'student'],
            ['code' => 'consistent',    'name' => 'Consistent',     'description' => 'Never missed a deadline for an entire term.',   'icon' => 'fas fa-calendar-check', 'color' => 'text-blue-500',    'target_role' => 'student'],
            ['code' => 'quick_learner', 'name' => 'Quick Learner',  'description' => 'Reached level 5 on the platform.',             'icon' => 'fas fa-bolt',          'color' => 'text-yellow-500',  'target_role' => 'student'],
            ['code' => 'star_pupil',    'name' => 'Star Pupil',     'description' => 'Achieved a perfect score 3 times.',            'icon' => 'fas fa-star',          'color' => 'text-indigo-500',  'target_role' => 'student'],
            ['code' => 'explorer',      'name' => 'Explorer',       'description' => 'Joined 3 or more classrooms.',                 'icon' => 'fas fa-compass',       'color' => 'text-cyan-500',    'target_role' => 'student'],
            ['code' => 'night_owl',     'name' => 'Night Owl',      'description' => 'Submitted an assignment after midnight.',       'icon' => 'fas fa-moon',          'color' => 'text-purple-500',  'target_role' => 'student'],
            ['code' => 'streak_master', 'name' => 'Streak Master',  'description' => 'Submitted 5 assignments on time in a row.',    'icon' => 'fas fa-fire',          'color' => 'text-orange-500',  'target_role' => 'student'],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['code' => $badge['code']], $badge);
        }
    }
}
