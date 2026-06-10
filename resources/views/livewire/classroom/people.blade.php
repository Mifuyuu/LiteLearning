@section('page-title', __('People') . ' - ' . $classroom->name)
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
        <span class="font-semibold text-[#101114]">{{ __('สมาชิก') }}</span>
    </nav>
@endsection

<div class="space-y-6 animate__animated animate__fadeIn"
    x-data="{ openPopover: null, showKickModal: false, kickName: '', kickId: null, kickType: '' }"
    @click.away="openPopover = null">
    @include('livewire.classroom.partials.subnav', ['classroom' => $classroom, 'sort' => $sort])

    <section class="flex flex-wrap items-center gap-2 rounded-2xl border border-[#dedee5] bg-white p-2 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        <a href="{{ route('classroom.roster', ['classroom' => $classroom, 'sort' => 'sort-first-name']) }}" wire:navigate
            class="rounded-[10px] px-4 py-2 text-sm font-medium transition-colors {{ $sort === 'sort-first-name' ? 'bg-[#7132f5] text-white' : 'text-[#686b82] hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5]' }}">
            {{ __('First name') }}
        </a>
        <a href="{{ route('classroom.roster', ['classroom' => $classroom, 'sort' => 'sort-last-name']) }}" wire:navigate
            class="rounded-[10px] px-4 py-2 text-sm font-medium transition-colors {{ $sort === 'sort-last-name' ? 'bg-[#7132f5] text-white' : 'text-[#686b82] hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5]' }}">
            {{ __('Last name') }}
        </a>
        <a href="{{ route('classroom.roster', ['classroom' => $classroom, 'sort' => 'sort-newest']) }}" wire:navigate
            class="rounded-[10px] px-4 py-2 text-sm font-medium transition-colors {{ $sort === 'sort-newest' ? 'bg-[#7132f5] text-white' : 'text-[#686b82] hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5]' }}">
            {{ __('Newest') }}
        </a>
    </section>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
        <section class="rounded-2xl border border-[#dedee5] bg-white p-6 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#9497a9]">{{ __('Teacher Team') }}</p>
                    <h2 class="mt-2 text-xl font-bold text-[#101114]">{{ __('Teacher & Co-Teachers') }}</h2>
                </div>
                <span class="rounded-[8px] bg-[rgba(104,107,130,0.12)] px-3 py-1 text-xs font-semibold text-[#484b5e]">
                    {{ 1 + $coTeachers->count() }}
                </span>
            </div>

            <div class="mt-6 space-y-3">
                <div class="rounded-[12px] border border-[#dedee5] bg-[rgba(133,91,251,0.04)] p-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ $classroom->teacher->avatar_url }}" alt="{{ $classroom->teacher->name }}" class="h-11 w-11 rounded-2xl object-cover">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-[#101114]">{{ $classroom->teacher->name }}</p>
                            <p class="truncate text-xs text-[#686b82]">{{ $classroom->teacher->email }}</p>
                        </div>
                        <span class="ml-auto rounded-[6px] bg-[rgba(20,158,97,0.16)] px-2 py-0.5 text-xs font-semibold text-[#026b3f]">{{ __('Owner') }}</span>
                    </div>
                </div>

                @foreach($coTeachers as $coTeacher)
                    <div class="rounded-[12px] border border-[#dedee5] bg-white p-4" wire:key="coteacher-{{ $coTeacher->id }}">
                        <div class="flex items-center gap-3">
                            <img src="{{ $coTeacher->avatar_url }}" alt="{{ $coTeacher->name }}" class="h-11 w-11 rounded-2xl object-cover">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-[#101114]">{{ $coTeacher->name }}</p>
                                <p class="truncate text-xs text-[#686b82]">{{ $coTeacher->email }}</p>
                            </div>
                            <span class="rounded-[8px] bg-[rgba(104,107,130,0.12)] px-2 py-0.5 text-xs font-medium text-[#484b5e]">{{ __('Co-Teacher') }}</span>
                            @if($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
                                <button type="button"
                                    @click="kickId = {{ $coTeacher->id }}; kickName = @js($coTeacher->name); kickType = 'co-teacher'; showKickModal = true"
                                    class="ml-auto inline-flex h-9 w-9 items-center justify-center rounded-[10px] text-rose-400 transition hover:bg-rose-50 hover:text-rose-600">
                                    <x-icon name="user-minus" class="h-4 w-4" />
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
                <form wire:submit="addCoTeacher" class="mt-6 rounded-[12px] border border-dashed border-[#dedee5] bg-[rgba(133,91,251,0.04)] p-4">
                    <label class="block">
                        <span class="mb-2 block text-sm font-medium text-[#686b82]">{{ __('Add co-teacher') }}</span>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <input wire:model="inviteCoTeacherEmail" type="email"
                                class="w-full rounded-[12px] border border-[#dedee5] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#7132f5] focus:ring-2 focus:ring-[rgba(133,91,251,0.16)]"
                                placeholder="{{ __('teacher@example.com') }}">
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-[12px] bg-[#7132f5] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#5741d8]">
                                <x-icon name="user-plus" class="h-4 w-4" />
                                {{ __('Add') }}
                            </button>
                        </div>
                        @error('inviteCoTeacherEmail') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </label>
                </form>
            @endif
        </section>

        <section class="rounded-2xl border border-[#dedee5] bg-white p-6 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#9497a9]">{{ __('Students') }}</p>
                    <h2 class="mt-2 text-xl font-bold text-[#101114]">{{ __('Class members') }}</h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="rounded-[8px] bg-[rgba(104,107,130,0.12)] px-3 py-1 text-xs font-semibold text-[#484b5e]">
                        {{ $students->count() }}
                    </span>
                    @if($students->count() > 0 && ($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin()))
                        <button type="button"
                            @click="kickId = -1; kickName = @js(__('all students')); kickType = 'all'; showKickModal = true"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] text-[#9497a9] transition hover:bg-rose-50 hover:text-rose-600">
                            <x-icon name="trash" class="h-4 w-4" />
                        </button>
                    @endif
                </div>
            </div>

            <div class="mt-6 space-y-3">
                @forelse($students as $member)
                    <div class="rounded-[12px] border border-[#dedee5] bg-[rgba(133,91,251,0.04)] p-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="h-11 w-11 rounded-2xl object-cover">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-[#101114]">{{ $member->name }}</p>
                                <p class="truncate text-xs text-[#686b82]">{{ $member->email }}</p>
                            </div>
                            @if($classroom->canManageClassroom(auth()->user()))
                                <button type="button"
                                    @click="kickId = {{ $member->id }}; kickName = @js($member->name); kickType = 'student'; showKickModal = true"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-[10px] text-rose-400 transition hover:bg-rose-50 hover:text-rose-600">
                                    <x-icon name="user-minus" class="h-4 w-4" />
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <x-empty-state-inline :title="__('Students')" :body="__('No students enrolled yet.')" />
                @endforelse
            </div>
        </section>
    </div>

    <div x-show="showKickModal" x-cloak class="fixed inset-0 z-70 flex items-center justify-center bg-black/50 p-4" @click.self="showKickModal = false">
        <div class="w-full max-w-md rounded-2xl border border-[#dedee5] bg-white p-6 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
            <h3 class="text-lg font-bold text-[#101114]">{{ __('Confirm removal') }}</h3>
            <p class="mt-2 text-sm leading-6 text-[#686b82]">
                {{ __('Remove') }} <span class="font-semibold text-[#101114]" x-text="kickName"></span>?
            </p>

            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="showKickModal = false"
                    class="rounded-[12px] border border-[#dedee5] px-4 py-3 text-sm font-semibold text-[#686b82] transition hover:bg-[rgba(133,91,251,0.04)]">
                    {{ __('Cancel') }}
                </button>
                <button type="button"
                    @click="
                        if (kickType === 'co-teacher') {
                            $wire.removeCoTeacher(kickId);
                        } else if (kickType === 'all') {
                            $wire.removeAllMembers();
                        } else {
                            $wire.removeMember(kickId);
                        }
                        showKickModal = false;
                    "
                    class="rounded-[12px] bg-rose-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-rose-700">
                    {{ __('Remove') }}
                </button>
            </div>
        </div>
    </div>
</div>
