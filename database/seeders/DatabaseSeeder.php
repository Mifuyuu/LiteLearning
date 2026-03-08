<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\ClassroomContent;
use App\Models\Comment;
use App\Models\Submission;
use App\Models\ThemeCategory;
use App\Models\User;
use App\Models\UserGamification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    private function seedClassroomDemoData(Collection $teachers, Collection $students): void
    {
        $themes = ThemeCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $blueprints = [
            [
                'name' => 'คณิตศาสตร์พื้นฐาน ม.1',
                'section' => 'ห้อง 1/1',
                'description' => 'ทบทวนพื้นฐานเลขคณิต สมการ และการแก้โจทย์ปัญหา พร้อมงานฝึกที่ส่งได้ทุกสัปดาห์',
                'teacher_index' => 0,
                'theme_index' => 0,
                'students' => [0, 1, 2, 3, 4, 5, 6, 7],
                'announcements' => [
                    ['title' => 'ยินดีต้อนรับสู่ห้องคณิตศาสตร์', 'content' => 'ภาคเรียนนี้เราจะเน้นการแก้โจทย์อย่างเป็นขั้นตอน และส่งงานทุกสัปดาห์ผ่านระบบ'],
                    ['title' => 'ตารางเรียนและกติกาการส่งงาน', 'content' => 'ส่งงานก่อน 23:59 ของวันศุกร์ หากติดปัญหาให้แจ้งในคอมเมนต์ของงานได้เลย'],
                ],
                'topics' => ['จำนวนเต็ม', 'เศษส่วนและทศนิยม', 'สมการเชิงเส้น'],
                'assignments' => [
                    ['title' => 'แบบฝึกหัดจำนวนเต็มชุดที่ 1', 'type' => 'question', 'topic' => 'จำนวนเต็ม', 'days' => -5, 'max_score' => 10, 'submission_states' => ['graded', 'graded', 'turned_in', 'returned', 'assigned', 'graded', 'turned_in', 'assigned']],
                    ['title' => 'ใบงานเศษส่วนและทศนิยม', 'type' => 'file', 'topic' => 'เศษส่วนและทศนิยม', 'days' => 2, 'max_score' => 20, 'submission_states' => ['turned_in', 'turned_in', 'graded', 'assigned', 'assigned', 'turned_in', 'assigned', 'assigned']],
                    ['title' => 'กิจกรรมเช็คชื่อคาบเช้า', 'type' => 'attendance', 'topic' => null, 'days' => 1, 'max_score' => 1, 'submission_states' => ['graded', 'graded', 'graded', 'graded', 'turned_in', 'graded', 'assigned', 'graded']],
                    ['title' => 'สรุปบทเรียนเรื่องสมการเชิงเส้น', 'type' => 'material', 'topic' => 'สมการเชิงเส้น', 'days' => null, 'max_score' => 0, 'submission_states' => []],
                ],
            ],
            [
                'name' => 'วิทยาการคำนวณ ม.2',
                'section' => 'ห้อง 2/2',
                'description' => 'เรียนรู้การคิดเชิงคำนวณ อัลกอริทึม และการออกแบบโครงงานดิจิทัลแบบร่วมมือกัน',
                'teacher_index' => 1,
                'theme_index' => 6,
                'students' => [1, 2, 3, 8, 9, 10, 11, 12, 13],
                'announcements' => [
                    ['title' => 'เตรียมพร้อมก่อนเริ่มโครงงาน', 'content' => 'สัปดาห์นี้ทุกคนจะจับกลุ่มและเลือกหัวข้อโครงงาน ให้เตรียมไอเดียมาอย่างน้อย 3 หัวข้อ'],
                    ['title' => 'ลิงก์แหล่งเรียนรู้อัลกอริทึม', 'content' => 'เพิ่มเอกสารสรุปและตัวอย่าง flowchart ไว้ใน material แล้ว สามารถเข้าไปอ่านได้ทันที'],
                ],
                'topics' => ['อัลกอริทึม', 'ผังงาน', 'โครงงานดิจิทัล'],
                'assignments' => [
                    ['title' => 'ออกแบบผังงานการแก้ปัญหา', 'type' => 'project', 'topic' => 'ผังงาน', 'days' => 4, 'max_score' => 50, 'submission_states' => ['turned_in', 'graded', 'turned_in', 'assigned', 'returned', 'turned_in', 'assigned', 'assigned', 'graded']],
                    ['title' => 'ตอบคำถามเรื่องอัลกอริทึม', 'type' => 'question', 'topic' => 'อัลกอริทึม', 'days' => -2, 'max_score' => 10, 'submission_states' => ['graded', 'graded', 'graded', 'turned_in', 'graded', 'returned', 'graded', 'assigned', 'turned_in']],
                    ['title' => 'ชุดสื่อการเรียนรู้เรื่องโครงงาน', 'type' => 'material', 'topic' => 'โครงงานดิจิทัล', 'days' => null, 'max_score' => 0, 'submission_states' => []],
                ],
            ],
            [
                'name' => 'ภาษาอังกฤษเพื่อการสื่อสาร',
                'section' => 'คาบ 3',
                'description' => 'ฝึกการอ่าน เขียน และสื่อสารภาษาอังกฤษในสถานการณ์จริง พร้อมภารกิจส่งงานสั้นทุกสัปดาห์',
                'teacher_index' => 2,
                'theme_index' => 12,
                'students' => [0, 4, 5, 6, 7, 8, 9, 14],
                'announcements' => [
                    ['title' => 'สัปดาห์นี้เน้นบทสนทนาในชีวิตประจำวัน', 'content' => 'อย่าลืมฝึกอ่านออกเสียงและอัปโหลดไฟล์เสียงในงาน speaking practice'],
                ],
                'topics' => ['Daily Conversation', 'Reading Practice'],
                'assignments' => [
                    ['title' => 'Speaking Practice: Introduce Yourself', 'type' => 'file', 'topic' => 'Daily Conversation', 'days' => 3, 'max_score' => 20, 'submission_states' => ['turned_in', 'assigned', 'turned_in', 'graded', 'assigned', 'turned_in', 'graded', 'assigned']],
                    ['title' => 'Reading Quiz Unit 2', 'type' => 'question', 'topic' => 'Reading Practice', 'days' => -1, 'max_score' => 15, 'submission_states' => ['graded', 'graded', 'turned_in', 'graded', 'returned', 'graded', 'assigned', 'turned_in']],
                    ['title' => 'เฉลยและคำศัพท์สำคัญประจำบท', 'type' => 'material', 'topic' => 'Reading Practice', 'days' => null, 'max_score' => 0, 'submission_states' => []],
                ],
            ],
        ];

        foreach ($blueprints as $classroomIndex => $blueprint) {
            $teacher = $teachers[$blueprint['teacher_index']];
            $theme = $themes[$blueprint['theme_index'] % max(1, $themes->count())] ?? null;

            $classroom = Classroom::create([
                'teacher_id' => $teacher->id,
                'name' => $blueprint['name'],
                'section' => $blueprint['section'],
                'description' => $blueprint['description'],
                'theme_category_id' => $theme?->id,
            ]);

            $enrolledStudents = collect($blueprint['students'])
                ->map(fn (int $index) => $students[$index % $students->count()])
                ->values();

            foreach ($enrolledStudents as $studentIndex => $student) {
                $classroom->members()->syncWithoutDetaching([
                    $student->id => [
                        'role' => 'student',
                        'joined_at' => now()->copy()->subDays(18 - ($studentIndex * 2)),
                    ],
                ]);

                UserGamification::firstOrCreate(
                    ['user_id' => $student->id],
                    [
                        'coins' => 80 + ($studentIndex * 15),
                        'xp' => 220 + ($studentIndex * 90),
                        'level' => min(8, 2 + intdiv($studentIndex, 2)),
                    ]
                );
            }

            $feedOrder = 1;

            foreach ($blueprint['announcements'] as $announcementData) {
                $announcement = Announcement::create([
                    'user_id' => $teacher->id,
                    'classroom_id' => $classroom->id,
                    'title' => $announcementData['title'],
                    'content' => $announcementData['content'],
                ]);

                ClassroomContent::create([
                    'classroom_id' => $classroom->id,
                    'contentable_type' => Announcement::class,
                    'contentable_id' => $announcement->id,
                    'order' => $feedOrder++,
                ]);

                foreach ($enrolledStudents->take(3) as $commentIndex => $student) {
                    Comment::create([
                        'commentable_type' => Announcement::class,
                        'commentable_id' => $announcement->id,
                        'user_id' => $student->id,
                        'content' => match ($commentIndex) {
                            0 => 'รับทราบแล้วค่ะ เดี๋ยวจะเตรียมตัวให้พร้อม',
                            1 => 'ขอบคุณครับ มีเอกสารสรุปให้อ่านต่อไหมครับ',
                            default => 'พร้อมเรียนแล้วครับ',
                        },
                    ]);
                }
            }

            foreach ($blueprint['topics'] as $topicIndex => $topicName) {
                \App\Models\Topic::create([
                    'classroom_id' => $classroom->id,
                    'name' => $topicName,
                    'order' => $topicIndex + 1,
                ]);
            }

            foreach ($blueprint['assignments'] as $assignmentIndex => $assignmentData) {
                $dueDate = $assignmentData['days'] !== null
                    ? now()->copy()->addDays($assignmentData['days'])->setTime(23, 59)
                    : null;

                $assignment = Assignment::create([
                    'user_id' => $teacher->id,
                    'classroom_id' => $classroom->id,
                    'title' => $assignmentData['title'],
                    'description' => 'รายละเอียดงาน: '.$assignmentData['title'].' โปรดอ่านคำชี้แจงให้ครบและส่งภายในเวลาที่กำหนด',
                    'max_score' => $assignmentData['max_score'],
                    'exp_reward' => $assignmentData['max_score'] > 0 ? 30 : 0,
                    'coin_reward' => $assignmentData['max_score'] > 0 ? 10 : 0,
                    'due_date' => $dueDate,
                    'status' => 'published',
                    'type' => $assignmentData['type'],
                    'topic' => $assignmentData['topic'],
                    'allow_late_submission' => in_array($assignmentData['type'], ['project', 'file'], true),
                ]);

                ClassroomContent::create([
                    'classroom_id' => $classroom->id,
                    'contentable_type' => Assignment::class,
                    'contentable_id' => $assignment->id,
                    'order' => $feedOrder++,
                ]);

                foreach ($assignmentData['submission_states'] as $studentIndex => $status) {
                    $student = $enrolledStudents[$studentIndex] ?? null;

                    if (! $student || in_array($assignmentData['type'], ['material', 'announcement', 'topic'], true)) {
                        continue;
                    }

                    $turnedInAt = match ($status) {
                        'turned_in', 'graded', 'returned' => $dueDate instanceof Carbon
                            ? $dueDate->copy()->subDays(max(0, ($studentIndex % 3) + 1))->setTime(18, 0)
                            : now()->copy()->subDays(2),
                        default => null,
                    };

                    $gradedAt = in_array($status, ['graded', 'returned'], true) && $turnedInAt instanceof Carbon
                        ? $turnedInAt->copy()->addDay()
                        : null;

                    Submission::create([
                        'assignment_id' => $assignment->id,
                        'user_id' => $student->id,
                        'content' => $status === 'assigned' ? null : 'นี่คือคำตอบและสรุปแนวคิดของนักเรียนสำหรับงาน '.$assignmentData['title'],
                        'status' => $status,
                        'score' => $status === 'graded' ? max(1, $assignmentData['max_score'] - (($studentIndex % 4) * 2)) : null,
                        'feedback' => in_array($status, ['graded', 'returned'], true)
                            ? ($status === 'graded' ? 'ทำได้ดีมาก อธิบายครบถ้วน' : 'เนื้อหาดีแล้ว แต่ขอให้แก้รูปแบบการอธิบายเพิ่มเติม')
                            : null,
                        'turned_in_at' => $turnedInAt,
                        'graded_at' => $gradedAt,
                    ]);
                }

                if ($assignmentIndex === 0) {
                    foreach ($enrolledStudents->take(2) as $student) {
                        Comment::create([
                            'commentable_type' => Assignment::class,
                            'commentable_id' => $assignment->id,
                            'user_id' => $student->id,
                            'content' => 'ถ้าส่งเป็นไฟล์ PDF ได้ไหมครับ/คะ',
                        ]);
                    }
                }
            }
        }
    }

    public function run(): void
    {
        $this->call(ThemeCategorySeeder::class);
        $this->call(GamificationFeaturesSeeder::class);
        $this->call(LeaderboardSeeder::class);

        // Create admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@litelearning.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'setup_completed_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // Create teachers
        $teachers = collect();
        $teacherData = [
            ['name' => 'John Smith', 'email' => 'teacher@litelearning.com'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah@litelearning.com'],
            ['name' => 'Michael Chen', 'email' => 'michael@litelearning.com'],
        ];

        foreach ($teacherData as $data) {
            $teachers->push(User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'teacher',
                    'setup_completed_at' => now(),
                    'email_verified_at' => now(),
                ]
            ));
        }

        // Create students
        $students = collect();
        $studentData = [
            ['name' => 'Alice Williams', 'email' => 'student@litelearning.com'],
            ['name' => 'Bob Martinez', 'email' => 'bob@litelearning.com'],
            ['name' => 'Carol Davis', 'email' => 'carol@litelearning.com'],
            ['name' => 'David Lee', 'email' => 'david@litelearning.com'],
            ['name' => 'Emma Wilson', 'email' => 'emma@litelearning.com'],
        ];

        foreach ($studentData as $data) {
            $students->push(User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'setup_completed_at' => now(),
                    'email_verified_at' => now(),
                ]
            ));
        }

        $moreStudents = User::factory(10)->create(['role' => 'student']);
        $allStudents = $students->merge($moreStudents);

        $allStudents->each(function (User $student, int $index): void {
            UserGamification::firstOrCreate(
                ['user_id' => $student->id],
                [
                    'coins' => 60 + ($index * 8),
                    'xp' => 150 + ($index * 55),
                    'level' => min(8, 1 + intdiv($index, 2)),
                ]
            );
        });

        $this->seedClassroomDemoData($teachers, $allStudents);
    }
}
