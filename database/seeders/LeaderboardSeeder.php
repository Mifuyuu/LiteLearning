<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserGamification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LeaderboardSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['name' => 'ปาล์ม วงค์ชนะ',    'email' => 'palm@student.com',    'level' => 15, 'xp' => 11200, 'coins' => 980],
            ['name' => 'มินท์ สายใจ',       'email' => 'mint@student.com',    'level' => 13, 'xp' => 8500,  'coins' => 750],
            ['name' => 'ฟ้า อรุณรัตน์',     'email' => 'fah@student.com',     'level' => 12, 'xp' => 7600,  'coins' => 640],
            ['name' => 'บีม ศิริพงษ์',      'email' => 'beam@student.com',    'level' => 11, 'xp' => 6500,  'coins' => 580],
            ['name' => 'แนน ธิดารัตน์',     'email' => 'nan@student.com',     'level' => 10, 'xp' => 5000,  'coins' => 500],
            ['name' => 'ต้น กิตติพงษ์',     'email' => 'ton@student.com',     'level' => 10, 'xp' => 4800,  'coins' => 470],
            ['name' => 'ปิ่น วรรณสิทธิ์',   'email' => 'pin@student.com',     'level' => 9,  'xp' => 4100,  'coins' => 390],
            ['name' => 'จิ๊บ ณัฐพล',        'email' => 'jib@student.com',     'level' => 8,  'xp' => 3600,  'coins' => 320],
            ['name' => 'โอ๊ต นภาพร',        'email' => 'oat@student.com',     'level' => 8,  'xp' => 3200,  'coins' => 300],
            ['name' => 'ไอซ์ อนันต์',       'email' => 'ice@student.com',     'level' => 7,  'xp' => 2700,  'coins' => 260],
            ['name' => 'แพท ชลธิชา',        'email' => 'pat@student.com',     'level' => 7,  'xp' => 2500,  'coins' => 240],
            ['name' => 'มาร์ค ธีรภัทร',     'email' => 'mark@student.com',    'level' => 6,  'xp' => 2100,  'coins' => 200],
            ['name' => 'นุ่น พิมพิกา',      'email' => 'nun@student.com',     'level' => 6,  'xp' => 1900,  'coins' => 185],
            ['name' => 'ไนซ์ ณัฐวุฒิ',      'email' => 'nice@student.com',    'level' => 5,  'xp' => 1600,  'coins' => 150],
            ['name' => 'เปียโน วันดี',      'email' => 'piano@student.com',   'level' => 5,  'xp' => 1400,  'coins' => 130],
            ['name' => 'ฝน อรนุช',          'email' => 'fon@student.com',     'level' => 4,  'xp' => 1050,  'coins' => 110],
            ['name' => 'กอล์ฟ วิชิต',       'email' => 'golf@student.com',    'level' => 4,  'xp' => 900,   'coins' => 90],
            ['name' => 'แบม สุนิสา',        'email' => 'bam@student.com',     'level' => 3,  'xp' => 600,   'coins' => 70],
            ['name' => 'เปี๊ยก ประเสริฐ',   'email' => 'peak@student.com',    'level' => 2,  'xp' => 250,   'coins' => 40],
            ['name' => 'ใหม่ สิริยา',       'email' => 'mai@student.com',     'level' => 1,  'xp' => 50,    'coins' => 15],
        ];

        foreach ($students as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'setup_completed_at' => now(),
                    'email_verified_at' => now(),
                ]
            );

            UserGamification::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'coins' => $data['coins'],
                    'xp' => $data['xp'],
                    'level' => $data['level'],
                ]
            );
        }

        $this->command->info('✅ Leaderboard dummy data seeded: '.count($students).' students');
    }
}
