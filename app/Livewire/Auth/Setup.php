<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Setup extends Component
{
    public string $role = 'student';
    public string $school = '';
    public string $study_year = '';
    public string $study_year_other = '';
    public string $birth_date = '';
    public bool $accept_tos = false;

    public array $schoolCatalog = [];

    public array $studyYearOptions = [
        'Year 1',
        'Year 2',
        'Year 3',
        'Year 4',
        'Other',
    ];

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->needsSetup()) {
            $this->redirectRoute('dashboard', navigate: true);
            return;
        }

        $this->role = $user->role;
        $this->schoolCatalog = $this->loadSchoolCatalog();
    }

    public function completeSetup(): void
    {
        $this->validate([
            'role' => 'required|in:student,teacher',
            'school' => 'required|string|max:255',
            'study_year' => 'required|string|max:255',
            'study_year_other' => 'required_if:study_year,Other|nullable|string|max:255',
            'birth_date' => 'required|date|before:today',
            'accept_tos' => 'accepted',
        ]);

        $resolvedSchool = trim($this->school);
        $resolvedStudyYear = $this->study_year === 'Other' ? trim($this->study_year_other) : $this->study_year;

        /** @var User $user */
        $user = Auth::user();
        $user->update([
            'role' => $this->role,
            'school_name' => $resolvedSchool,
            'study_year' => $resolvedStudyYear,
            'birth_date' => $this->birth_date,
            'locale' => app()->getLocale(),
            'tos_accepted_at' => now(),
            'setup_completed_at' => now(),
        ]);

        session()->flash('message', __('Setup completed successfully.'));
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function getSchoolSuggestionsProperty(): array
    {
        $existingSchoolNames = User::query()
            ->whereNotNull('school_name')
            ->pluck('school_name')
            ->filter();

        $source = collect($this->schoolCatalog)
            ->merge($existingSchoolNames)
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->unique()
            ->values();

        $keyword = mb_strtolower(trim($this->school));

        if ($keyword === '') {
            return [];
        }

        return $source
            ->filter(fn (string $name) => str_contains(mb_strtolower($name), $keyword))
            ->take(5)
            ->values()
            ->all();
    }

    private function loadSchoolCatalog(): array
    {
        $catalogPath = base_path('Collage.md');

        if (!is_readable($catalogPath)) {
            return [];
        }

        $content = file_get_contents($catalogPath);
        if ($content === false) {
            return [];
        }

        $lines = preg_split('/\R/u', $content) ?: [];

        return collect($lines)
            ->map(function (string $line): string {
                $line = str_replace("\u{00A0}", ' ', $line);
                $line = preg_replace('/\s+/u', ' ', trim($line)) ?? '';

                return $line;
            })
            ->filter(fn (string $line) => $line !== '' && !str_starts_with($line, '#'))
            ->unique()
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.auth.setup');
    }
}
