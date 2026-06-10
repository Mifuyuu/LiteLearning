@php
    $pillClass = fn (bool $active): string => $active
        ? 'bg-[rgba(133,91,251,0.16)] text-[#7132f5]'
        : 'text-[#686b82] hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5]';
    $iconName = fn (string $name, bool $active): string => $active ? $name . '-solid' : $name;
    $sort = $sort ?? 'sort-last-name';
    $display = $display ?? 'default';
@endphp

<nav class="tabs-scroll sticky top-0 z-30 flex items-center gap-2 overflow-x-auto rounded-[12px] border border-[#dedee5] bg-white/95 p-2 shadow-[rgba(0,0,0,0.03)_0px_4px_18px] backdrop-blur">
    @php $active = request()->routeIs('classroom.show'); @endphp
    <a href="{{ route('classroom.show', $classroom) }}" wire:navigate
        class="inline-flex items-center gap-2 rounded-[10px] px-4 py-2 text-sm font-bold transition-colors {{ $pillClass($active) }}">
        <x-icon :name="$iconName('home', $active)" class="h-4 w-4" />
        <span class="hidden sm:inline">{{ __('หน้าหลัก') }}</span>
    </a>

    @php $active = request()->routeIs('classroom.stream'); @endphp
    <a href="{{ route('classroom.stream', $classroom) }}" wire:navigate
        class="inline-flex items-center gap-2 rounded-[10px] px-4 py-2 text-sm font-bold transition-colors {{ $pillClass($active) }}">
        <x-icon :name="$iconName('chat-bubble-left-ellipsis', $active)" class="h-4 w-4" />
        <span class="hidden sm:inline">{{ __('กระดานสนทนา') }}</span>
    </a>

    @php $active = request()->routeIs('classroom.work'); @endphp
    <a href="{{ route('classroom.work', ['classroom' => $classroom, 'scope' => 'all']) }}" wire:navigate
        class="inline-flex items-center gap-2 rounded-[10px] px-4 py-2 text-sm font-bold transition-colors {{ $pillClass($active) }}">
        <x-icon :name="$iconName('clipboard-document-list', $active)" class="h-4 w-4" />
        <span class="hidden sm:inline">{{ __('Classwork') }}</span>
    </a>

    @php $active = request()->routeIs('classroom.roster'); @endphp
    <a href="{{ route('classroom.roster', ['classroom' => $classroom, 'sort' => $sort]) }}" wire:navigate
        class="inline-flex items-center gap-2 rounded-[10px] px-4 py-2 text-sm font-bold transition-colors {{ $pillClass($active) }}">
        <x-icon :name="$iconName('users', $active)" class="h-4 w-4" />
        <span class="hidden sm:inline">{{ __('สมาชิก') }}</span>
    </a>

    @if($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
        @php $active = request()->routeIs('classroom.settings'); @endphp
        <a href="{{ route('classroom.settings', $classroom) }}" wire:navigate
            class="inline-flex items-center gap-2 rounded-[10px] px-4 py-2 text-sm font-bold transition-colors {{ $pillClass($active) }}">
            <x-icon name="cog-6-tooth" class="h-4 w-4" />
            <span class="hidden sm:inline">{{ __('Settings') }}</span>
        </a>
    @endif

    @if($classroom->canManageClassroom(auth()->user()))
        @php $active = request()->routeIs('classroom.gradebook'); @endphp
        <a href="{{ route('classroom.gradebook', ['classroom' => $classroom, 'sort' => $sort, 'display' => $display]) }}" wire:navigate
            class="ml-auto inline-flex items-center gap-2 rounded-[10px] px-4 py-2 text-sm font-bold transition-colors {{ $pillClass($active) }}">
            <x-icon :name="$iconName('chart-bar', $active)" class="h-4 w-4" />
            <span class="hidden sm:inline">{{ __('Gradebook') }}</span>
        </a>
    @endif
</nav>
