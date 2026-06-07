@section('page-title', __('People') . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] hover:text-[#7132f5] transition-colors">
            {{ auth()->user()->isTeacher() ? __('ชั้นเรียนของฉัน') : __('ห้องเรียน') }}
        </a>
        <i class="fas fa-chevron-right text-[#9497a9] text-xs"></i>
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-[#686b82] hover:text-[#7132f5] transition-colors">
            {{ $classroom->name }}
        </a>
        <i class="fas fa-chevron-right text-[#9497a9] text-xs"></i>
        <span class="text-[#101114] font-semibold">{{ __('People') }}</span>
    </nav>
@endsection

<div class="space-y-6 animate__animated animate__fadeIn"
    x-data="{ openPopover: null, showKickModal: false, kickName: '', kickId: null, kickType: '' }"
    @click.away="openPopover = null">
    <section class="overflow-hidden rounded-2xl border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-6 sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="inline-flex items-center gap-2 rounded-[8px] bg-[rgba(133,91,251,0.16)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-[#7132f5]">
                    <i class="fas fa-users"></i>
                    {{ __('Roster') }}
                </span>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-[#101114] sm:text-4xl">{{ $classroom->name }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-[#686b82]">
                    {{ __('ดูสมาชิกทั้งหมดของห้องเรียน แยกบทบาทชัดเจน และจัดลำดับตามรูปแบบที่ต้องการ') }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('classroom.roster', ['classroom' => $classroom, 'sort' => 'sort-first-name']) }}" wire:navigate
                    class="rounded-[10px] px-4 py-2 text-sm font-medium transition-colors {{ $sort === 'sort-first-name' ? 'bg-[#7132f5] text-white' : 'bg-white text-[#686b82] border border-[#dedee5] hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5]' }}">
                    {{ __('First name') }}
                </a>
                <a href="{{ route('classroom.roster', ['classroom' => $classroom, 'sort' => 'sort-last-name']) }}" wire:navigate
                    class="rounded-[10px] px-4 py-2 text-sm font-medium transition-colors {{ $sort === 'sort-last-name' ? 'bg-[#7132f5] text-white' : 'bg-white text-[#686b82] border border-[#dedee5] hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5]' }}">
                    {{ __('Last name') }}
                </a>
                <a href="{{ route('classroom.roster', ['classroom' => $classroom, 'sort' => 'sort-newest']) }}" wire:navigate
                    class="rounded-[10px] px-4 py-2 text-sm font-medium transition-colors {{ $sort === 'sort-newest' ? 'bg-[#7132f5] text-white' : 'bg-white text-[#686b82] border border-[#dedee5] hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5]' }}">
                    {{ __('Newest') }}
                </a>
            </div>
        </div>
    </section>

    @include('livewire.classroom.partials.subnav', ['classroom' => $classroom, 'sort' => $sort])

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
        <section class="rounded-2xl border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-6">
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
                                <div class="relative ml-auto">
                                    <button @click="openPopover = openPopover === 'ct-{{ $coTeacher->id }}' ? null : 'ct-{{ $coTeacher->id }}'"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-[10px] text-[#9497a9] transition hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5]">
                                        <i class="fas fa-ellipsis"></i>
                                    </button>
                                    <div x-show="openPopover === 'ct-{{ $coTeacher->id }}'" x-cloak
                                        class="absolute right-0 top-11 w-44 rounded-2xl border border-[#dedee5] bg-white py-2 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                                        <button @click="kickId = {{ $coTeacher->id }}; kickName = '{{ addslashes($coTeacher->name) }}'; kickType = 'co-teacher'; showKickModal = true; openPopover = null"
                                            class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-rose-600 transition hover:bg-rose-50">
                                            <i class="fas fa-user-minus w-4"></i>
                                            {{ __('Remove') }}
                                        </button>
                                    </div>
                                </div>
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
                                <i class="fas fa-user-plus text-xs"></i>
                                {{ __('Add') }}
                            </button>
                        </div>
                        @error('inviteCoTeacherEmail') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </label>
                </form>
            @endif
        </section>

        <section class="rounded-2xl border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-6">
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
                        <button @click="kickId = -1; kickName = '{{ __('นักเรียนทั้งหมด') }}'; kickType = 'all'; showKickModal = true"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] text-[#9497a9] transition hover:bg-rose-50 hover:text-rose-600">
                            <i class="fas fa-users-slash"></i>
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
                                <div class="relative">
                                    <button @click="openPopover = openPopover === 'st-{{ $member->id }}' ? null : 'st-{{ $member->id }}'"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-[10px] text-[#9497a9] transition hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5]">
                                        <i class="fas fa-ellipsis"></i>
                                    </button>
                                    <div x-show="openPopover === 'st-{{ $member->id }}'" x-cloak
                                        class="absolute right-0 top-11 w-44 rounded-2xl border border-[#dedee5] bg-white py-2 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                                        <button @click="kickId = {{ $member->id }}; kickName = '{{ addslashes($member->name) }}'; kickType = 'student'; showKickModal = true; openPopover = null"
                                            class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-rose-600 transition hover:bg-rose-50">
                                            <i class="fas fa-user-minus w-4"></i>
                                            {{ __('Remove') }}
                                        </button>
                                    </div>
                                </div>
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
        <div class="w-full max-w-md rounded-2xl bg-white border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-6">
            <h3 class="text-lg font-bold text-[#101114]">{{ __('Confirm removal') }}</h3>
            <p class="mt-2 text-sm leading-6 text-[#686b82]">
                {{ __('คุณต้องการลบ') }}
                <span class="font-semibold text-[#101114]" x-text="kickName"></span>
                {{ __('ออกจากห้องเรียนนี้ใช่หรือไม่?') }}
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
