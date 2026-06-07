@section('page-title', $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-[#7132f5]">
            {{ auth()->user()->isTeacher() ? __('My classes') : __('Classrooms') }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ \Illuminate\Support\Str::limit($classroom->name, 30) }}</span>
    </nav>
@endsection

@php
    $manager = $classroom->canManageClassroom(auth()->user());
    $themeColor = $classroom->themeCategory?->color ?? '#7132f5';
    $latestItems = collect($topicGroups)->flatMap(fn ($group) => $group['items'])->take(5);
@endphp

<div class="space-y-5 animate__animated animate__fadeIn">

    {{-- ═══════════════════════════ HERO HEADER ═══════════════════════════ --}}
    <section class="overflow-hidden rounded-[12px] border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">

        {{-- Theme color accent strip --}}
        <div class="h-2 w-full" style="background-color: {{ $themeColor }};"></div>

        <div class="p-6 sm:p-8">
            {{-- Top row: Info + Actions --}}
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">

                {{-- Left: Classroom Info --}}
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#7132f5]">{{ __('Classroom') }}</p>
                    <h1 class="mt-2 text-3xl font-black leading-tight tracking-tight text-[#101114] sm:text-4xl">
                        {{ $classroom->name }}
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#686b82]">
                        {{ $classroom->description ?: __('Class updates, work, materials, and people in one place.') }}
                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-[8px] bg-[rgba(133,91,251,0.12)] px-3 py-1.5 text-xs font-bold text-[#7132f5]">
                            <x-icon name="user" class="h-3.5 w-3.5" />
                            {{ $classroom->teacher->name }}
                        </span>
                        @if($classroom->section)
                            <span class="inline-flex items-center gap-1.5 rounded-[8px] bg-[rgba(133,91,251,0.12)] px-3 py-1.5 text-xs font-bold text-[#7132f5]">
                                <x-icon name="bookmark" class="h-3.5 w-3.5" />
                                {{ $classroom->section }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 rounded-[8px] bg-[rgba(133,91,251,0.12)] px-3 py-1.5 text-xs font-bold text-[#7132f5]">
                            <x-icon name="users" class="h-3.5 w-3.5" />
                            {{ $classroom->students->count() }} {{ __('students') }}
                        </span>
                    </div>
                </div>

                {{-- Right: Quick Actions --}}
                <div class="flex shrink-0 flex-wrap gap-2">
                    <button type="button" wire:click="routeToWork"
                        class="inline-flex items-center gap-2 rounded-[12px] bg-[#7132f5] px-4 py-2.5 text-sm font-extrabold text-white transition hover:bg-[#5741d8]">
                        <x-icon name="clipboard-document-list" class="h-4 w-4" />
                        {{ __('Open Missions') }}
                    </button>
                    <button type="button" wire:click="routeToRoster"
                        class="inline-flex items-center gap-2 rounded-[12px] border border-[#dedee5] bg-white px-4 py-2.5 text-sm font-bold text-[#686b82] transition hover:border-[rgba(113,50,245,0.3)] hover:text-[#7132f5]">
                        <x-icon name="users" class="h-4 w-4" />
                        {{ __('Roster') }}
                    </button>
                    @if($manager)
                        <button type="button" wire:click="routeToGradebook"
                            class="inline-flex items-center gap-2 rounded-[12px] border border-[#dedee5] bg-white px-4 py-2.5 text-sm font-bold text-[#686b82] transition hover:border-[rgba(113,50,245,0.3)] hover:text-[#7132f5]">
                            <x-icon name="chart-bar" class="h-4 w-4" />
                            {{ __('Gradebook') }}
                        </button>
                    @endif
                </div>
            </div>

            {{-- Stats Bar --}}
            <div class="mt-6 flex flex-wrap gap-3 border-t border-[#dedee5] pt-5">
                <div class="flex items-center gap-3 rounded-[10px] border border-[#dedee5] bg-[rgba(133,91,251,0.03)] px-4 py-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-[8px] bg-[rgba(133,91,251,0.12)] text-[#7132f5]">
                        <x-icon name="bell" class="h-4 w-4" />
                    </span>
                    <div>
                        <p class="text-xl font-black leading-none text-[#101114]">{{ $classroom->announcements->count() }}</p>
                        <p class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-[#9497a9]">{{ __('Briefings') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-[10px] border border-[#dedee5] bg-[rgba(133,91,251,0.03)] px-4 py-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-[8px] bg-[rgba(133,91,251,0.12)] text-[#7132f5]">
                        <x-icon name="document-text" class="h-4 w-4" />
                    </span>
                    <div>
                        <p class="text-xl font-black leading-none text-[#101114]">{{ $classroom->assignments->count() }}</p>
                        <p class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-[#9497a9]">{{ __('Missions') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-[10px] border border-[#dedee5] bg-[rgba(133,91,251,0.03)] px-4 py-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-[8px] bg-[rgba(133,91,251,0.12)] text-[#7132f5]">
                        <x-icon name="book-open" class="h-4 w-4" />
                    </span>
                    <div>
                        <p class="text-xl font-black leading-none text-[#101114]">{{ $classroom->materials->count() }}</p>
                        <p class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-[#9497a9]">{{ __('Library') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-[10px] border border-[#dedee5] bg-[rgba(133,91,251,0.03)] px-4 py-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-[8px] bg-[rgba(133,91,251,0.12)] text-[#7132f5]">
                        <x-icon name="tag" class="h-4 w-4" />
                    </span>
                    <div>
                        <p class="text-xl font-black leading-none text-[#101114]">{{ $classroom->topics->count() }}</p>
                        <p class="mt-0.5 text-[10px] font-bold uppercase tracking-wider text-[#9497a9]">{{ __('Topics') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Subnav --}}
    @include('livewire.classroom.partials.subnav', ['classroom' => $classroom])

    {{-- ═══════════════════════════ CONTENT GRID ═══════════════════════════ --}}
    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_320px]">

        {{-- ── MAIN: Learning Map ── --}}
        <main class="space-y-5">

            <section class="rounded-[12px] border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#9497a9]">{{ __('Classwork') }}</p>
                        <h2 class="mt-1 text-2xl font-black text-[#101114]">{{ __('Learning Map') }}</h2>
                    </div>
                    @if($manager)
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('assignment.create', $classroom) }}?type=question" wire:navigate
                                class="inline-flex items-center gap-2 rounded-[12px] bg-[#7132f5] px-4 py-2.5 text-sm font-extrabold text-white transition hover:bg-[#5741d8]">
                                <x-icon name="plus" class="h-4 w-4" />
                                {{ __('Mission') }}
                            </a>
                            <a href="{{ route('material.create', $classroom) }}" wire:navigate
                                class="inline-flex items-center gap-2 rounded-[12px] border border-[#dedee5] bg-white px-4 py-2.5 text-sm font-bold text-[#686b82] transition hover:border-[rgba(113,50,245,0.3)] hover:text-[#7132f5]">
                                <x-icon name="book-open" class="h-4 w-4" />
                                {{ __('Resource') }}
                            </a>
                        </div>
                    @endif
                </div>

                <div class="mt-5 space-y-4">
                    @forelse($topicGroups as $group)
                        <section class="rounded-[12px] border border-[#dedee5] bg-[rgba(133,91,251,0.02)] p-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-[rgba(133,91,251,0.16)] text-[#7132f5]">
                                    <x-icon name="tag" class="h-4 w-4" />
                                </span>
                                <div>
                                    <h3 class="font-black text-[#101114]">{{ $group['name'] }}</h3>
                                    <p class="text-xs font-semibold text-[#9497a9]">{{ count($group['items']) }} {{ __('items') }}</p>
                                </div>
                            </div>

                            <div class="mt-4 grid gap-3 md:grid-cols-2">
                                @foreach($group['items'] as $item)
                                    @php $model = $item['model']; @endphp
                                    <a href="{{ $item['kind'] === 'assignment'
                                            ? route('assignment.show', ['classroom' => $classroom, 'assignment' => $model])
                                            : route('material.show', ['classroom' => $classroom, 'material' => $model]) }}"
                                        wire:navigate
                                        class="group flex items-start gap-3 rounded-[12px] border border-[#dedee5] bg-white p-4 transition hover:border-[rgba(113,50,245,0.3)] hover:bg-[rgba(133,91,251,0.04)]">
                                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] {{ $item['kind'] === 'assignment' ? 'bg-[rgba(133,91,251,0.16)] text-[#7132f5]' : 'bg-amber-50 text-amber-700' }}">
                                            <x-icon :name="$item['kind'] === 'assignment' ? 'document-text' : 'book-open'" class="h-4 w-4" />
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-extrabold text-[#101114] group-hover:text-[#7132f5]">{{ $model->title }}</p>
                                            <p class="mt-1 text-xs font-semibold text-[#9497a9]">
                                                {{ $item['kind'] === 'assignment' ? $model->typeLabel() : __('Resource') }}
                                                @if($item['kind'] === 'assignment' && $model->due_date)
                                                    · {{ $model->due_date->format('d M Y H:i') }}
                                                @endif
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @empty
                        <x-empty-state-inline :title="__('Learning Map')" :body="__('No missions or resources yet.')" />
                    @endforelse
                </div>
            </section>

            {{-- Classroom Settings (Owner only — collapsible) --}}
            @if($classroom->isOwnedBy(auth()->user()))
                <section x-data="{ open: false }"
                    class="overflow-hidden rounded-[12px] border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">

                    <button type="button" @click="open = !open"
                        class="flex w-full items-center justify-between px-5 py-4 text-left transition hover:bg-[rgba(133,91,251,0.03)]">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#9497a9]">{{ __('Studio Controls') }}</p>
                            <h2 class="mt-0.5 text-lg font-black text-[#101114]">{{ __('Classroom settings') }}</h2>
                        </div>
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-[10px] bg-[rgba(133,91,251,0.08)] text-[#7132f5] transition-transform duration-200"
                            :class="{ 'rotate-180': open }">
                            <x-icon name="chevron-down" class="h-4 w-4" />
                        </span>
                    </button>

                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="border-t border-[#dedee5]">

                        <form wire:submit="saveSettings" class="space-y-5 p-5">
                            <div class="grid gap-4 md:grid-cols-2">
                                <label class="block">
                                    <span class="mb-2 block text-sm font-bold text-[#101114]">{{ __('Name') }}</span>
                                    <input wire:model="name" type="text"
                                        class="w-full rounded-[12px] border border-[#dedee5] bg-white px-4 py-3 text-sm text-[#101114] outline-none transition focus:border-[#7132f5] focus:ring-2 focus:ring-[rgba(133,91,251,0.16)]">
                                    @error('name') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                                </label>
                                <label class="block">
                                    <span class="mb-2 block text-sm font-bold text-[#101114]">{{ __('Section') }}</span>
                                    <input wire:model="section" type="text"
                                        class="w-full rounded-[12px] border border-[#dedee5] bg-white px-4 py-3 text-sm text-[#101114] outline-none transition focus:border-[#7132f5] focus:ring-2 focus:ring-[rgba(133,91,251,0.16)]">
                                    @error('section') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                                </label>
                            </div>

                            <label class="block">
                                <span class="mb-2 block text-sm font-bold text-[#101114]">{{ __('Description') }}</span>
                                <textarea wire:model="description" rows="4"
                                    class="w-full rounded-[12px] border border-[#dedee5] bg-white px-4 py-3 text-sm text-[#101114] outline-none transition focus:border-[#7132f5] focus:ring-2 focus:ring-[rgba(133,91,251,0.16)]"></textarea>
                                @error('description') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                            </label>

                            <div>
                                <p class="mb-3 text-sm font-bold text-[#101114]">{{ __('Theme') }}</p>
                                <div class="grid grid-cols-4 gap-3 sm:grid-cols-7">
                                    @foreach($themes as $theme)
                                        @php $planet = str_pad($theme->planet_number, 2, '0', STR_PAD_LEFT); @endphp
                                        <button type="button" wire:click="$set('theme_category_id', {{ $theme->id }})"
                                            class="rounded-[10px] border-2 p-2 transition {{ $theme_category_id == $theme->id ? 'border-[#7132f5] bg-[rgba(133,91,251,0.16)]' : 'border-[#dedee5] hover:border-[rgba(113,50,245,0.3)] hover:bg-[rgba(133,91,251,0.04)]' }}">
                                            <img src="/images/planets/planet_{{ $planet }}.svg" alt="{{ $theme->name }}" class="mx-auto h-12 w-12 object-contain">
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex flex-wrap justify-end gap-2">
                                <button type="button" wire:click="toggleArchive"
                                    class="inline-flex items-center gap-2 rounded-[12px] border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-bold text-amber-700 transition hover:bg-amber-100">
                                    <x-icon name="archive-box" class="h-4 w-4" />
                                    {{ $classroom->is_archived ? __('Restore') : __('Archive') }}
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-[12px] bg-[#7132f5] px-5 py-2.5 text-sm font-extrabold text-white transition hover:bg-[#5741d8]">
                                    <x-icon name="check" class="h-4 w-4" />
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            @endif
        </main>

        {{-- ── SIDEBAR ── --}}
        <aside class="space-y-5 xl:sticky xl:top-6 xl:self-start">

            {{-- Announcements / Briefing Feed --}}
            <section class="rounded-[12px] border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#9497a9]">{{ __('Briefing Feed') }}</p>
                        <h2 class="mt-0.5 text-lg font-black text-[#101114]">{{ __('Latest updates') }}</h2>
                    </div>
                    @if($manager)
                        <a href="{{ route('assignment.create', $classroom) }}?type=announcement" wire:navigate
                            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-[#7132f5] text-white transition hover:bg-[#5741d8]">
                            <x-icon name="plus" class="h-4 w-4" />
                        </a>
                    @endif
                </div>

                <div class="mt-4 space-y-3">
                    @forelse($classroom->announcements as $announcement)
                        <article class="rounded-[12px] border border-[#dedee5] bg-white p-4">
                            <div class="flex items-start gap-3">
                                <img src="{{ $announcement->user->avatar_url }}" alt="{{ $announcement->user->name }}"
                                    class="h-9 w-9 shrink-0 rounded-[10px] object-cover">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-extrabold text-[#101114]">{{ $announcement->user->name }}</p>
                                            <p class="text-xs font-semibold text-[#9497a9]">{{ $announcement->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if($announcement->user_id === auth()->id() || $manager)
                                            <button type="button" wire:click="confirmDeleteAnnouncement({{ $announcement->id }})"
                                                class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-[8px] text-rose-400 transition hover:bg-rose-50 hover:text-rose-600">
                                                <x-icon name="trash" class="h-3.5 w-3.5" />
                                            </button>
                                        @endif
                                    </div>
                                    <div class="prose prose-sm mt-3 max-w-none text-[#686b82] [&_p:first-child]:mt-0 [&_p:last-child]:mb-0">
                                        {!! $announcement->content !!}
                                    </div>
                                </div>
                            </div>
                            @livewire('classroom.stream-comment', ['announcementId' => $announcement->id], "overview-comment-{$announcement->id}")
                        </article>
                    @empty
                        <x-empty-state-inline :title="__('Briefings')" :body="__('No updates yet.')" />
                    @endforelse
                </div>
            </section>

            {{-- Join Code (Manager only) --}}
            @if($manager)
                <section class="rounded-[12px] border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-5">
                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#9497a9]">{{ __('Class Access') }}</p>
                    <h2 class="mt-0.5 text-lg font-black text-[#101114]">{{ __('Join code') }}</h2>
                    <div class="mt-4 flex items-center justify-between gap-3 rounded-[12px] border border-[#dedee5] bg-[rgba(133,91,251,0.04)] px-4 py-3">
                        <p class="text-2xl font-black tracking-[0.22em] text-[#101114]">{{ $classroom->code }}</p>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $classroom->code }}')"
                            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-[rgba(133,91,251,0.16)] text-[#7132f5] transition hover:bg-[rgba(133,91,251,0.24)]">
                            <x-icon name="clipboard-document-list" class="h-4 w-4" />
                        </button>
                    </div>
                </section>
            @endif
        </aside>
    </div>

    {{-- Delete Announcement Modal --}}
    <div x-data x-show="$wire.showDeleteAnnouncementModal" x-cloak
        class="fixed inset-0 z-70 flex items-center justify-center bg-black/50 p-4"
        @click.self="$wire.set('showDeleteAnnouncementModal', false)">
        <div class="w-full max-w-md rounded-[12px] border border-[#dedee5] bg-white p-6 shadow-[rgba(0,0,0,0.08)_0px_8px_32px]">
            <h4 class="text-lg font-black text-[#101114]">{{ __('Delete Announcement') }}</h4>
            <p class="mt-2 text-sm text-[#686b82]">{{ __('Are you sure you want to delete this announcement? This action cannot be undone.') }}</p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="$wire.set('showDeleteAnnouncementModal', false)"
                    class="rounded-[12px] border border-[#dedee5] px-4 py-2.5 text-sm font-bold text-[#686b82] transition hover:bg-[rgba(133,91,251,0.04)]">
                    {{ __('Cancel') }}
                </button>
                <button type="button" wire:click="deleteAnnouncement"
                    class="rounded-[12px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                    {{ __('Delete') }}
                </button>
            </div>
        </div>
    </div>
</div>
