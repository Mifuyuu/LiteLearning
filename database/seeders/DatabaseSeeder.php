<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\ClassroomContent;
use App\Models\Comment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ClassroomThemeCategorySeeder::class);
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

        // Create additional random students
        $moreStudents = User::factory(10)->create(['role' => 'student']);
        $allStudents = $students->merge($moreStudents);

        // Create classrooms
        $classroomData = [
            ['name' => 'Mathematics 101', 'subject' => 'Mathematics', 'section' => 'Section A'],
            ['name' => 'Computer Science 201', 'subject' => 'Computer Science', 'section' => 'Section B'],
            ['name' => 'English Literature', 'subject' => 'English', 'section' => 'Period 1'],
            ['name' => 'Physics Advanced', 'subject' => 'Physics', 'section' => 'Section A'],
            ['name' => 'Biology 101', 'subject' => 'Biology', 'section' => 'Period 2'],
        ];

        $colors = ['#DC2626', '#F97316', '#F59E0B', '#059669', '#0891B2', '#2563EB', '#4F46E5', '#9333EA', '#DB2777', '#475569'];

        foreach ($classroomData as $i => $data) {
            $teacher = $teachers[$i % $teachers->count()];

            $classroom = Classroom::create([
                'teacher_id' => $teacher->id,
                'name' => $data['name'],
                'section' => $data['section'],
                'subject' => $data['subject'],
                'description' => "Welcome to {$data['name']}! This course covers fundamental concepts in {$data['subject']}.",
                'theme_color' => $colors[$i],
            ]);

            // Enroll random students
            $enrolledStudents = $allStudents->random(rand(8, 12));
            foreach ($enrolledStudents as $student) {
                $classroom->members()->attach($student->id, [
                    'role' => 'student',
                    'joined_at' => now()->subDays(rand(1, 30)),
                ]);
            }

            // Create announcements
            for ($j = 0; $j < rand(2, 4); $j++) {
                $announcement = Announcement::create([
                    'user_id' => $teacher->id,
                    'content' => fake()->paragraphs(rand(1, 3), true),
                ]);
                ClassroomContent::create([
                    'classroom_id'     => $classroom->id,
                    'contentable_type' => Announcement::class,
                    'contentable_id'   => $announcement->id,
                ]);

                // Add comments to some announcements
                foreach ($enrolledStudents->random(rand(0, 3)) as $student) {
                    Comment::create([
                        'commentable_type' => Announcement::class,
                        'commentable_id'   => $announcement->id,
                        'user_id'          => $student->id,
                        'content'          => fake()->sentence(),
                    ]);
                }
            }

            // Create assignments
            $assignmentTypes = ['announcement', 'question', 'attendance', 'material', 'topic', 'project'];
            for ($j = 0; $j < rand(3, 6); $j++) {
                $type = $assignmentTypes[array_rand($assignmentTypes)];
                $assignment = Assignment::create([
                    'user_id'      => $teacher->id,
                    'title'        => fake()->sentence(4),
                    'description'  => fake()->paragraph() . "\n\n" . fake()->paragraphs(2, true),
                    'max_score'    => in_array($type, ['announcement', 'material', 'topic']) ? 0 : fake()->randomElement([10, 20, 50, 100]),
                    'due_date'     => in_array($type, ['announcement', 'material', 'topic']) ? null : fake()->dateTimeBetween('-1 week', '+2 weeks'),
                    'status'       => 'published',
                    'type'         => $type,
                ]);
                ClassroomContent::create([
                    'classroom_id'     => $classroom->id,
                    'contentable_type' => Assignment::class,
                    'contentable_id'   => $assignment->id,
                ]);

                // Create submissions for assignments (not materials)
                if (!in_array($type, ['announcement', 'material', 'topic'])) {
                    foreach ($enrolledStudents->random(rand(3, 8)) as $student) {
                        $statuses = ['turned_in', 'graded', 'assigned'];
                        $status = $statuses[array_rand($statuses)];

                        Submission::create([
                            'assignment_id' => $assignment->id,
                            'user_id' => $student->id,
                            'content' => $status !== 'assigned' ? fake()->paragraph() : null,
                            'status' => $status,
                            'score' => $status === 'graded' ? rand(60, 100) : null,
                            'feedback' => $status === 'graded' ? fake()->sentence() : null,
                            'turned_in_at' => $status !== 'assigned' ? now()->subDays(rand(0, 5)) : null,
                            'graded_at' => $status === 'graded' ? now()->subDays(rand(0, 3)) : null,
                        ]);
                    }
                }
            }
        }
    }
}
