<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserGamification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Profile extends Component
{
    use WithFileUploads;

    public User $user;

    public $avatar;

    public $cover_image;

    public function mount(): void
    {
        $this->user = Auth::user();
    }

    public function updatedAvatar($value): void
    {
        $this->uploadAvatar($value);
    }

    public function updatedCoverImage($value): void
    {
        $this->uploadCoverImage($value);
    }

    public function uploadAvatar($value): void
    {
        if (is_string($value) && str_starts_with($value, 'data:image')) {
            $this->storeBase64Image($value, 'avatars', 'avatar');
        }
    }

    public function uploadCoverImage($value): void
    {
        if (is_string($value) && str_starts_with($value, 'data:image')) {
            $this->storeBase64Image($value, 'covers', 'cover_image');
        }
    }

    protected function storeBase64Image(string $base64Data, string $folder, string $field): void
    {
        try {
            // Parse the base64 string
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (! in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    throw new \Exception('Invalid image type.');
                }

                $imageData = base64_decode($base64Data);

                if ($imageData === false) {
                    throw new \Exception('Base64 decode failed.');
                }

                // C3: Limit decoded image size to 5MB
                if (strlen($imageData) > 5 * 1024 * 1024) {
                    throw new \Exception('Image is too large. Maximum size is 5MB.');
                }

                // S4: Verify image integrity
                $img = @imagecreatefromstring($imageData);
                if ($img === false) {
                    throw new \Exception('Invalid image data.');
                }
                imagedestroy($img);

                $fileName = $folder.'/'.uniqid().'.'.$type;
                $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

                // Delete old file if exists
                if ($this->user->$field && Storage::disk($disk)->exists($this->user->$field)) {
                    Storage::disk($disk)->delete($this->user->$field);
                }

                Storage::disk($disk)->put($fileName, $imageData);

                $user = Auth::user();
                $user->update([$field => $fileName]);
                $this->user = $user->fresh();

                $this->$field = null;

                $message = $field === 'avatar' ? __('อัปเดตรูปโปรไฟล์เรียบร้อยแล้ว') : __('อัปเดตรูปปกเรียบร้อยแล้ว');
                $this->dispatch('notify', message: $message);
            }
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', message: __('Upload failed. Please try again.'), type: 'error');
        }
    }

    public function render(): View
    {
        $this->user = Auth::user()->fresh();
        $user = $this->user;

        $rank = null;
        $chartPoints = [];

        if ($user->isStudent()) {
            $userGamification = $user->gamification;
            if ($userGamification) {
                $rank = UserGamification::query()
                    ->whereHas('user', fn ($q) => $q->where('role', 'student'))
                    ->where(function ($query) use ($userGamification) {
                        $query->where('level', '>', $userGamification->level)
                            ->orWhere(function ($q) use ($userGamification) {
                                $q->where('level', '=', $userGamification->level)
                                    ->where('xp', '>', $userGamification->xp);
                            });
                    })
                    ->count() + 1;
            } else {
                $rank = UserGamification::query()
                    ->whereHas('user', fn ($q) => $q->where('role', 'student'))
                    ->count() + 1;
            }

            // Get current distribution of student XPs for fast in-memory binary search
            $allXps = UserGamification::query()
                ->whereHas('user', fn ($q) => $q->where('role', 'student'))
                ->pluck('xp')
                ->sort()
                ->values()
                ->toArray();

            // Collect all XP-earning events for the student within the past 90 days
            $events = [];

            // 1. Classrooms joined (25 XP each)
            $enrolledClassrooms = $user->enrolledClassrooms()->withPivot('joined_at')->get();
            foreach ($enrolledClassrooms as $classroom) {
                $date = $classroom->pivot->joined_at ?? $classroom->pivot->created_at ?? $classroom->created_at;
                if ($date) {
                    $events[] = [
                        'date' => \Illuminate\Support\Carbon::parse($date),
                        'xp' => 25,
                    ];
                }
            }

            // 2. Submissions turned in (20 XP each)
            $submissions = $user->submissions()->whereNotNull('turned_in_at')->get();
            foreach ($submissions as $submission) {
                $events[] = [
                    'date' => \Illuminate\Support\Carbon::parse($submission->turned_in_at),
                    'xp' => 20,
                ];
            }

            // 3. Achievements unlocked (XP reward value, fallback 100 XP)
            $achievementsPivot = $user->achievements()->withPivot('unlocked_at')->get();
            foreach ($achievementsPivot as $achievement) {
                $date = $achievement->pivot->unlocked_at ?? $achievement->pivot->created_at ?? $achievement->created_at;
                if ($date) {
                    $events[] = [
                        'date' => \Illuminate\Support\Carbon::parse($date),
                        'xp' => (int) ($achievement->xp_reward ?? 100),
                    ];
                }
            }

            $currentXp = $user->xp;
            $points = [];

            for ($i = 0; $i <= 90; $i++) {
                $day = 90 - $i;
                $targetDate = \Illuminate\Support\Carbon::now()->subDays($day)->endOfDay();

                // Compute student's XP on this day by subtracting XP earned after the target date
                $xpOnDay = $currentXp;
                foreach ($events as $event) {
                    if ($event['date']->greaterThan($targetDate)) {
                        $xpOnDay -= $event['xp'];
                    }
                }
                if ($xpOnDay < 0) {
                    $xpOnDay = 0;
                }

                // Map XP to rank via binary search
                $count = count($allXps);
                if ($count > 0) {
                    $low = 0;
                    $high = $count - 1;
                    $pos = $count;
                    while ($low <= $high) {
                        $mid = (int) (($low + $high) / 2);
                        if ($allXps[$mid] > $xpOnDay) {
                            $pos = $mid;
                            $high = $mid - 1;
                        } else {
                            $low = $mid + 1;
                        }
                    }
                    $rankOnDay = $count - $pos + 1;
                } else {
                    $rankOnDay = 1;
                }

                $points[] = [
                    'day' => $day === 0 ? __('วันนี้') : __(':days วันที่แล้ว', ['days' => $day]),
                    'rank' => $rankOnDay,
                ];
            }

            $ranks = array_column($points, 'rank');
            $minR = min($ranks);
            $maxR = max($ranks);
            $range = $maxR - $minR;

            foreach ($points as $index => $pt) {
                $x = ($index / (count($points) - 1)) * 400;
                if ($range > 0) {
                    $y = 20 + (($pt['rank'] - $minR) / $range) * 45;
                } else {
                    $y = 42.5;
                }
                $chartPoints[] = [
                    'x' => $x,
                    'y' => $y,
                    'day' => $pt['day'],
                    'rank' => $pt['rank'],
                ];
            }
        }

        $unlockedAchievements = $user->achievements()
            ->orderByPivot('unlocked_at', 'desc')
            ->get();

        $achievements = Achievement::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $classrooms = $user->allClassrooms();
        $classrooms->load(['teacher', 'themeCategory']);
        $classrooms->loadCount([
            'students',
            'assignments as assignments_count' => fn ($query) => $query->published(),
        ]);

        $profileClassrooms = $classrooms
            ->map(function ($classroom) use ($user): array {
                $role = __('Student');
                if ($classroom->isOwnedBy($user)) {
                    $role = __('Owner');
                } elseif ($classroom->pivot && $classroom->pivot->role === 'co-teacher') {
                    $role = __('Co-teacher');
                }

                return [
                    'model' => $classroom,
                    'role' => $role,
                    'students_count' => $classroom->students_count,
                    'assignments_count' => $classroom->assignments_count,
                ];
            })
            ->values();

        $recentSubmissions = $user->submissions()
            ->with(['assignment.classworkItem.classroom.themeCategory'])
            ->whereNotNull('turned_in_at')
            ->latest('turned_in_at')
            ->take(6)
            ->get();

        $gradedSubmissions = $user->submissions()
            ->whereNotNull('score');

        $profileStats = [
            'level' => $user->level,
            'xp' => $user->xp,
            'coins' => $user->coins,
            'achievements' => $unlockedAchievements->count(),
            'achievement_total' => $achievements->count(),
            'classrooms' => $profileClassrooms->count(),
            'submissions' => $user->submissions()->whereIn('status', ['turned_in', 'graded', 'returned'])->count(),
            'average_score' => round((float) ($gradedSubmissions->avg('score') ?? 0), 1),
        ];

        return view('livewire.profile', [
            'achievements' => $achievements,
            'unlockedAchievements' => $unlockedAchievements,
            'unlockedAchievementIds' => $unlockedAchievements->pluck('id')->flip(),
            'profileClassrooms' => $profileClassrooms,
            'recentSubmissions' => $recentSubmissions,
            'profileStats' => $profileStats,
            'rank' => $rank,
            'chartPoints' => $chartPoints,
        ]);
    }
}
