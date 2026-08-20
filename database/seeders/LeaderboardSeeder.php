<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserGamification;
use App\Services\GamificationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LeaderboardSeeder extends Seeder
{
    public function run(): void
    {
        // 'level' is derived from 'xp' below via GamificationService::resolveLevelFromXp() —
        // do not hardcode it here, it will drift from the XP formula (see Dashboard XP bar bug).
        $students = [
            ['name' => 'ปาล์ม วงค์ชนะ',    'email' => 'palm@student.com',    'xp' => 11200, 'coins' => 980],
            ['name' => 'มินท์ สายใจ',       'email' => 'mint@student.com',    'xp' => 8500,  'coins' => 750],
            ['name' => 'ฟ้า อรุณรัตน์',     'email' => 'fah@student.com',     'xp' => 7600,  'coins' => 640],
            ['name' => 'บีม ศิริพงษ์',      'email' => 'beam@student.com',    'xp' => 6500,  'coins' => 580],
            ['name' => 'แนน ธิดารัตน์',     'email' => 'nan@student.com',     'xp' => 5000,  'coins' => 500],
            ['name' => 'ต้น กิตติพงษ์',     'email' => 'ton@student.com',     'xp' => 4800,  'coins' => 470],
            ['name' => 'ปิ่น วรรณสิทธิ์',   'email' => 'pin@student.com',     'xp' => 4100,  'coins' => 390],
            ['name' => 'จิ๊บ ณัฐพล',        'email' => 'jib@student.com',     'xp' => 3600,  'coins' => 320],
            ['name' => 'โอ๊ต นภาพร',        'email' => 'oat@student.com',     'xp' => 3200,  'coins' => 300],
            ['name' => 'ไอซ์ อนันต์',       'email' => 'ice@student.com',     'xp' => 2700,  'coins' => 260],
            ['name' => 'แพท ชลธิชา',        'email' => 'pat@student.com',     'xp' => 2500,  'coins' => 240],
            ['name' => 'มาร์ค ธีรภัทร',     'email' => 'mark@student.com',    'xp' => 2100,  'coins' => 200],
            ['name' => 'นุ่น พิมพิกา',      'email' => 'nun@student.com',     'xp' => 1900,  'coins' => 185],
            ['name' => 'ไนซ์ ณัฐวุฒิ',      'email' => 'nice@student.com',    'xp' => 1600,  'coins' => 150],
            ['name' => 'เปียโน วันดี',      'email' => 'piano@student.com',   'xp' => 1400,  'coins' => 130],
            ['name' => 'ฝน อรนุช',          'email' => 'fon@student.com',     'xp' => 1050,  'coins' => 110],
            ['name' => 'กอล์ฟ วิชิต',       'email' => 'golf@student.com',    'xp' => 900,   'coins' => 90],
            ['name' => 'แบม สุนิสา',        'email' => 'bam@student.com',     'xp' => 600,   'coins' => 70],
            ['name' => 'เปี๊ยก ประเสริฐ',   'email' => 'peak@student.com',    'xp' => 250,   'coins' => 40],
            ['name' => 'ใหม่ สิริยา',       'email' => 'mai@student.com',     'xp' => 50,    'coins' => 15],
        ];

        $gamificationService = new GamificationService;

        foreach ($students as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]
            );

            UserGamification::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'coins' => $data['coins'],
                    'xp' => $data['xp'],
                    'level' => $gamificationService->resolveLevelFromXp($data['xp']),
                ]
            );
        }

        $this->command->info('✅ Leaderboard dummy data seeded: '.count($students).' students');
    }
}
