@section('page-title', __('กระดานสนทนา') . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-[#7132f5]">
            {{ auth()->user()->isTeacher() ? __('My classes') : __('Classrooms') }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-[#686b82] transition-colors hover:text-[#7132f5]">
            {{ $classroom->name }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ __('กระดานสนทนา') }}</span>
    </nav>
@endsection

@php
    $manager = $classroom->canManageClassroom(auth()->user());
@endphp

<div class="space-y-5 animate__animated animate__fadeIn">
    @include('livewire.classroom.partials.subnav', ['classroom' => $classroom])

    <section class="rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#9497a9]">{{ __('กระดานสนทนา') }}</p>
                <h1 class="mt-1 text-2xl font-black text-[#101114]">{{ __('Latest updates') }}</h1>
            </div>
            @if($manager)
                <a href="{{ route('assignment.create', $classroom) }}?type=announcement" wire:navigate
                    class="inline-flex items-center gap-2 rounded-[10px] bg-[#7132f5] px-4 py-2.5 text-sm font-extrabold text-white transition hover:bg-[#5741d8]">
                    <x-icon name="plus" class="h-4 w-4" />
                    {{ __('Announcement') }}
                </a>
            @endif
        </div>

        <div class="mt-5 space-y-3">
            @forelse($classroom->announcements as $announcement)
                <article class="rounded-[12px] border border-[#dedee5] bg-[rgba(133,91,251,0.02)] p-4">
                    <div class="flex items-start gap-3">
                        <img src="{{ $announcement->user->avatar_url }}" alt="{{ $announcement->user->name }}"
                            class="h-10 w-10 shrink-0 rounded-[10px] object-cover">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-extrabold text-[#101114]">{{ $announcement->user->name }}</p>
                                    <p class="text-xs font-semibold text-[#9497a9]">{{ $announcement->created_at->diffForHumans() }}</p>
                                </div>
                                @if($announcement->user_id === auth()->id() || $manager)
                                    <button type="button" wire:click="confirmDeleteAnnouncement({{ $announcement->id }})"
                                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-[8px] text-rose-400 transition hover:bg-rose-50 hover:text-rose-600"
                                        title="{{ __('Delete') }}">
                                        <x-icon name="trash" class="h-3.5 w-3.5" />
                                    </button>
                                @endif
                            </div>
                            <div class="prose prose-sm mt-3 max-w-none text-[#686b82] [&_p:first-child]:mt-0 [&_p:last-child]:mb-0">
                                {!! $announcement->content !!}
                            </div>
                        </div>
                    </div>
                    @livewire('classroom.stream-comment', ['announcementId' => $announcement->id], "stream-comment-{$announcement->id}")
                </article>
            @empty
                <x-empty-state-inline :title="__('กระดานสนทนา')" :body="__('No announcements yet.')" />
            @endforelse
        </div>
    </section>

    <div x-data x-show="$wire.showDeleteAnnouncementModal" x-cloak
        class="fixed inset-0 z-70 flex items-center justify-center bg-black/50 p-4"
        @click.self="$wire.set('showDeleteAnnouncementModal', false)">
        <div class="w-full max-w-md rounded-[12px] border border-[#dedee5] bg-white p-6 shadow-[rgba(0,0,0,0.08)_0px_8px_32px]">
            <h4 class="text-lg font-black text-[#101114]">{{ __('Delete Announcement') }}</h4>
            <p class="mt-2 text-sm text-[#686b82]">{{ __('Are you sure you want to delete this announcement? This action cannot be undone.') }}</p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="$wire.set('showDeleteAnnouncementModal', false)"
                    class="rounded-[10px] border border-[#dedee5] px-4 py-2.5 text-sm font-bold text-[#686b82] transition hover:bg-[rgba(133,91,251,0.04)]">
                    {{ __('Cancel') }}
                </button>
                <button type="button" wire:click="deleteAnnouncement"
                    class="rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                    {{ __('Delete') }}
                </button>
            </div>
        </div>
    </div>
</div>
