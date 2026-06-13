@section('page-title', __('Work') . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-[#7132f5]">
            {{ auth()->user()->isTeacher() ? __('My classes') : __('Classrooms') }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-[#686b82] transition-colors hover:text-[#7132f5]">
            {{ $classroom->name }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ __('Classwork') }}</span>
    </nav>
@endsection

@php
    $scopeLinks = [
        'all' => __('All'),
        'pending' => auth()->user()->isStudent() ? __('Not Done') : __('In Progress'),
        'completed' => auth()->user()->isStudent() ? __('Done') : __('Completed'),
    ];

    $renderSection = function (\Illuminate\Support\Collection $items, string $title, string $emptyText) use ($classroom) {
        if ($items->isEmpty()) {
            return view('components.empty-state-inline', [
                'title' => $title,
                'body' => $emptyText,
            ])->render();
        }

        $grouped = $items->groupBy(fn ($assignment) => $assignment->topic ?? __('General'));

        ob_start();
        echo '<section class="space-y-5">';
        echo '<p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#9497a9]">'.e($title).'</p>';
        foreach ($grouped as $topic => $assignments) {
            echo '<section class="space-y-3">';
            echo '<div class="flex items-center gap-3">';
            echo '<h3 class="text-sm font-semibold uppercase tracking-[0.18em] text-[#9497a9]">'.e($topic).'</h3>';
            echo '<div class="h-px flex-1 bg-[#dedee5]"></div>';
            echo '</div>';
            echo '<div class="space-y-3">';
            foreach ($assignments as $assignment) {
                $submission = auth()->user()->isStudent()
                    ? $assignment->submissions->firstWhere('user_id', auth()->id())
                    : null;
                $isCompleted = $submission?->isTurnedIn() ?? false;
                echo view('livewire.classroom.work-item-card', [
                    'assignment' => $assignment,
                    'classroom' => $classroom,
                    'submission' => $submission,
                    'isCompleted' => $isCompleted,
                ])->render();
            }
            echo '</div>';
            echo '</section>';
        }
        echo '</section>';

        return ob_get_clean();
    };
@endphp

<div class="space-y-6 ">
    @include('livewire.classroom.partials.subnav', ['classroom' => $classroom])

    <section class="flex flex-wrap items-center gap-2 rounded-2xl border border-[#dedee5] bg-white p-2 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        @foreach($scopeLinks as $key => $label)
            <a href="{{ route('classroom.work', ['classroom' => $classroom, 'scope' => $key]) }}" wire:navigate
                class="inline-flex items-center gap-2 rounded-[10px] px-4 py-2 text-sm font-medium transition-colors {{ $scope === $key ? 'bg-[rgba(133,91,251,0.16)] text-[#7132f5]' : 'text-[#686b82] hover:bg-[rgba(133,91,251,0.04)] hover:text-[#7132f5]' }}">
                {{ $label }}
            </a>
        @endforeach
        @if($classroom->canManageClassroom(auth()->user()))
            <div class="ml-auto flex flex-wrap gap-2">
                <a href="{{ route('assignment.create', $classroom) }}?type=question" wire:navigate
                    class="inline-flex items-center gap-2 rounded-[12px] bg-[#7132f5] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#5741d8]">
                    <x-icon name="plus" class="h-4 w-4" />
                    {{ __('Create Assignment') }}
                </a>
            </div>
        @endif
    </section>

    <div class="space-y-6">
        @if($scope !== 'completed')
            {!! $renderSection($pendingAssignments, $scopeLinks['pending'], __('No work in this state.')) !!}
        @endif
        @if($scope !== 'pending')
            {!! $renderSection($completedAssignments, $scopeLinks['completed'], __('No work in this state.')) !!}
        @endif
    </div>
</div>
