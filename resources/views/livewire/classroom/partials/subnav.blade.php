@php
    $pillClass = fn (bool $active): string => $active
        ? 'bg-[rgba(133,91,251,0.16)] text-[#7132f5]'
        : 'text-[#686b82] hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5]';
    $iconName = fn (string $name, bool $active): string => $active ? $name . '-solid' : $name;
    $sort = $sort ?? 'sort-last-name';
    $display = $display ?? 'default';
@endphp

<div class="flex flex-wrap items-center gap-2 rounded-[12px] border border-[#dedee5] bg-white p-2">
    @php $active = request()->routeIs('classroom.show'); @endphp
    <a href="{{ route('classroom.show', $classroom) }}" wire:navigate
        class="inline-flex items-center gap-2 rounded-[10px] px-4 py-2 text-sm font-bold transition-colors {{ $pillClass($active) }}">
        <x-icon :name="$iconName('home', $active)" class="h-4 w-4" />
        {{ __('Overview') }}
    </a>
    @php $active = request()->routeIs('classroom.work'); @endphp
    <a href="{{ route('classroom.work', ['classroom' => $classroom, 'scope' => 'all']) }}" wire:navigate
        class="inline-flex items-center gap-2 rounded-[10px] px-4 py-2 text-sm font-bold transition-colors {{ $pillClass($active) }}">
        <x-icon :name="$iconName('clipboard-document-list', $active)" class="h-4 w-4" />
        {{ __('Work') }}
    </a>
    @php $active = request()->routeIs('classroom.roster'); @endphp
    <a href="{{ route('classroom.roster', ['classroom' => $classroom, 'sort' => $sort]) }}" wire:navigate
        class="inline-flex items-center gap-2 rounded-[10px] px-4 py-2 text-sm font-bold transition-colors {{ $pillClass($active) }}">
        <x-icon :name="$iconName('users', $active)" class="h-4 w-4" />
        {{ __('People') }}
    </a>
    @if($classroom->canManageClassroom(auth()->user()))
        @php $active = request()->routeIs('classroom.gradebook'); @endphp
        <a href="{{ route('classroom.gradebook', ['classroom' => $classroom, 'sort' => $sort, 'display' => $display]) }}" wire:navigate
            class="inline-flex items-center gap-2 rounded-[10px] px-4 py-2 text-sm font-bold transition-colors {{ $pillClass($active) }}">
            <x-icon :name="$iconName('chart-bar', $active)" class="h-4 w-4" />
            {{ __('Gradebook') }}
        </a>
    @endif
</div>
