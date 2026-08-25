@section('page-title', 'สมาชิก' . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)">
            {{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-[#686b82] transition-colors hover:text-(--ll-blue)" title="{{ $classroom->name }}">
            {{ \Illuminate\Support\Str::limit($classroom->name, 25, '..') }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ 'สมาชิก' }}</span>
    </nav>
@endsection

@php
    $fallback = \App\Models\ThemeCategory::fallbackFor($classroom->id);
    $themeColor = $classroom->themeCategory?->color ?? $fallback['color'];
@endphp

<div class="max-w-4xl mx-auto" style="--cw-color: {{ $themeColor }}; --cw-subtle: {{ $themeColor }}26; --cw-faint: {{ $themeColor }}12;"
    x-data="{ openPopover: null, showKickModal: false, kickName: '', kickId: null, kickType: '' }"
    @click.away="openPopover = null">
    <section class="rounded-2xl border-3 border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] min-h-[calc(100vh-3rem)]">

        {{-- Sort --}}
        <div class="flex flex-wrap items-center gap-2 p-4">
            <a href="{{ route('classroom.roster', ['classroom' => $classroom, 'sort' => 'sort-first-name']) }}" wire:navigate
                class="rounded-[10px] px-4 py-2 text-sm font-medium transition-colors {{ $sort === 'sort-first-name' ? 'bg-(--cw-color) text-white' : 'text-[#686b82] hover:bg-(--cw-faint) hover:text-(--cw-color)' }}">
                {{ 'ชื่อต้น' }}
            </a>
            <a href="{{ route('classroom.roster', ['classroom' => $classroom, 'sort' => 'sort-newest']) }}" wire:navigate
                class="rounded-[10px] px-4 py-2 text-sm font-medium transition-colors {{ $sort === 'sort-newest' ? 'bg-(--cw-color) text-white' : 'text-[#686b82] hover:bg-(--cw-faint) hover:text-(--cw-color)' }}">
                {{ 'ใหม่ล่าสุด' }}
            </a>
        </div>
        
        {{-- Teachers --}}
        <div class="p-6 pb-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="mt-2 text-xl font-bold text-[#101114]">{{ 'ผู้สอน' }}</h2>
                </div>
                <span class="rounded-md bg-[rgba(104,107,130,0.12)] px-3 py-1 text-xs font-semibold text-[#484b5e]">
                    {{ 1 + $coTeachers->count() }}
                </span>
            </div>

            <div class="mt-6 space-y-3">
                <a href="{{ route('profile', $classroom->teacher) }}" wire:navigate class="rounded-lg border border-[#dedee5] bg-(--cw-faint) p-4 block transition hover:border-(--cw-color)/30 hover:bg-(--cw-color)/8">
                    <div class="flex items-center gap-3">
                        <img src="{{ $classroom->teacher->avatar_url }}" alt="{{ $classroom->teacher->name }}" class="h-11 w-11 rounded-2xl object-cover">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-[#101114]">{{ $classroom->teacher->name }}</p>
                            <p class="truncate text-xs text-[#686b82]">{{ $classroom->teacher->email }}</p>
                        </div>
                        <span class="ml-auto rounded-md bg-[rgba(20,158,97,0.16)] px-2 py-0.5 text-xs font-semibold text-[#026b3f]">{{ 'เจ้าของ' }}</span>
                    </div>
                </a>

                @foreach($coTeachers as $coTeacher)
                    <div class="rounded-xl border border-[#dedee5] bg-white p-4" wire:key="coteacher-{{ $coTeacher->id }}">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('profile', $coTeacher) }}" wire:navigate class="flex items-center gap-3 min-w-0 flex-1">
                                <img src="{{ $coTeacher->avatar_url }}" alt="{{ $coTeacher->name }}" class="h-11 w-11 rounded-2xl object-cover">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-[#101114] transition-colors">{{ $coTeacher->name }}</p>
                                    <p class="truncate text-xs text-[#686b82]">{{ $coTeacher->email }}</p>
                                </div>
                            </a>
                            <span class="rounded-lg bg-[rgba(104,107,130,0.12)] px-2 py-0.5 text-xs font-medium text-[#484b5e]">{{ 'ผู้สอนร่วม' }}</span>
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
                <form wire:submit.prevent="addCoTeacher" class="mt-6 rounded-lg border border-dashed border-[#dedee5] bg-(--cw-faint) p-4">
                    <label class="block">
                        <span class="mb-2 block text-sm font-medium text-[#686b82]">{{ 'เพิ่มผู้สอนร่วม' }}</span>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <input wire:model="inviteCoTeacherEmail" type="email"
                                class="w-full rounded-lg border border-[#dedee5] bg-white px-4 py-3 text-sm outline-none transition focus:border-(--cw-color) focus:ring-2 focus:ring-(--cw-subtle)"
                                placeholder="teacher@example.com">
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-(--cw-color) px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90 shadow-sm">
                                <x-icon name="user-plus" class="h-4 w-4" />
                                {{ 'เพิ่ม' }}
                            </button>
                        </div>
                        @error('inviteCoTeacherEmail') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </label>
                </form>
            @endif
        </div>

        {{-- Students --}}
        <div class="p-6 pt-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="mt-2 text-xl font-bold text-[#101114]">{{ 'สมาชิกในชั้นเรียน' }}</h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="rounded-lg bg-[rgba(104,107,130,0.12)] px-3 py-1 text-xs font-semibold text-[#484b5e]">
                        {{ $students->count() }}
                    </span>
                    @if($students->count() > 0 && ($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin()))
                        <button type="button"
                            @click="kickId = -1;                             kickName = @js('นักเรียนทั้งหมด'); kickType = 'all'; showKickModal = true"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] text-[#9497a9] transition hover:bg-rose-50 hover:text-rose-600">
                            <x-icon name="trash" class="h-4 w-4" />
                        </button>
                    @endif
                </div>
            </div>

            <div class="mt-6 space-y-3">
                @forelse($students as $member)
                    <div class="rounded-lg border border-[#dedee5] bg-(--cw-faint) p-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('profile', $member) }}" wire:navigate class="flex items-center gap-3 min-w-0 flex-1">
                                <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="h-11 w-11 rounded-2xl object-cover">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-[#101114] transition-colors">{{ $member->name }}</p>
                                    <p class="truncate text-xs text-[#686b82]">{{ $member->email }}</p>
                                </div>
                            </a>
                            @if($classroom->canManageClassroom(auth()->user()))
                                <button type="button"
                                    @click="kickId = {{ $member->id }}; kickName = @js($member->name); kickType = 'student'; showKickModal = true"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition hover:bg-rose-50 hover:text-rose-600">
                                    <x-icon name="user-minus" class="h-4 w-4" />
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <x-empty-state-inline :title="'นักเรียน'" :body="'ยังไม่มีนักเรียนลงทะเบียน'" />
                @endforelse
            </div>
        </div>
    </section>

    <template x-teleport="body">
        <x-confirm-modal show="showKickModal" cancel="showKickModal = false" heading="ยืนยันการลบ">
            <x-slot:message>
                {{ 'ลบ' }} <span class="font-semibold text-[#101114]" x-text="kickName"></span>?
            </x-slot:message>
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
                class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                {{ 'ลบ' }}
            </button>
        </x-confirm-modal>
    </template>
</div>
