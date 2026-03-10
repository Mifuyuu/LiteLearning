@section('page-title', __('People') . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ auth()->user()->isTeacher() ? __('ชั้นเรียนของฉัน') : __('ห้องเรียน') }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('classroom.show', $classroom) }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ $classroom->name }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-semibold">{{ __('People') }}</span>
    </nav>
@endsection

<div class="max-w-3xl mx-auto animate__animated animate__fadeIn"
    x-data="{ openPopover: null, showKickModal: false, kickName: '', kickId: null, kickType: '' }"
    @click.away="openPopover = null">
    <!-- Back -->
    <a href="{{ route('classroom.show', $classroom) }}"
        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i> {{ __('Back to :name', ['name' => $classroom->name]) }}
    </a>

    <!-- Teacher -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4"><i
                class="fas fa-chalkboard-teacher mr-2 text-indigo-500"></i>{{ __('Teacher') }}</h3>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center">
                <img src="{{ $classroom->teacher->avatar_url }}" class="w-12 h-12 rounded-full mr-4">
                <div>
                    <p class="font-semibold text-gray-900">{{ $classroom->teacher->name }}</p>
                    <p class="text-sm text-gray-500">{{ $classroom->teacher->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Co-Teachers -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-user-tie mr-2 text-purple-500"></i>{{ __('Co-Teachers') }}
                <span class="text-gray-400 font-normal">({{ $classroom->coTeachers->count() }})</span>
            </h3>
        </div>

        @if($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
            <form wire:submit="addCoTeacher" class="mb-4 flex gap-2">
                <input wire:model="inviteCoTeacherEmail" type="email"
                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    placeholder="{{ __('อีเมลอาจารย์ที่ต้องการเพิ่ม...') }}">
                <button type="submit"
                    class="btn-3d btn-3d--indigo px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-user-plus mr-1"></i> {{ __('เพิ่ม') }}
                </button>
            </form>
            @error('inviteCoTeacherEmail')
                <p class="text-sm text-red-500 mb-3">{{ $message }}</p>
            @enderror
        @endif

        <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
            @forelse($classroom->coTeachers as $coTeacher)
                <div class="flex items-center justify-between p-4" wire:key="coteacher-{{ $coTeacher->id }}">
                    <div class="flex items-center">
                        <img src="{{ $coTeacher->avatar_url }}" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $coTeacher->name }}</p>
                            <p class="text-xs text-gray-500">{{ $coTeacher->email }}</p>
                        </div>
                    </div>
                    @if($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
                        <div class="relative">
                            <button @click="openPopover = openPopover === 'ct-{{ $coTeacher->id }}' ? null : 'ct-{{ $coTeacher->id }}'"
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-colors cursor-pointer">
                                <i class="fas fa-ellipsis-v px-1"></i>
                            </button>
                            <div x-show="openPopover === 'ct-{{ $coTeacher->id }}'" x-transition.opacity.duration.200ms x-cloak
                                class="absolute right-0 top-10 w-44 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-10">
                                <button @click="kickId = {{ $coTeacher->id }}; kickName = '{{ addslashes($coTeacher->name) }}'; kickType = 'co-teacher'; showKickModal = true; openPopover = null"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2 cursor-pointer">
                                    <i class="fas fa-user-times w-4"></i> {{ __('ลบออก') }}
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-gray-400 text-sm">{{ __('ยังไม่มี Co-Teacher') }}</div>
            @endforelse
        </div>
    </div>

    <!-- Students -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-users mr-2 text-indigo-500"></i>{{ __('Students') }}
                <span class="text-gray-400 font-normal">({{ $classroom->students->count() }})</span>
            </h3>

            @if($classroom->students->count() > 0 && ($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin()))
                <div class="relative">
                    <button @click="openPopover = openPopover === 'remove-all' ? null : 'remove-all'"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors cursor-pointer">
                        <i class="fas fa-ellipsis-v px-1"></i>
                    </button>
                    <div x-show="openPopover === 'remove-all'" x-transition.opacity.duration.200ms x-cloak
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-10">
                        <button @click="kickId = -1; kickName = '{{ __('นักเรียนทั้งหมด') }}'; kickType = 'all'; showKickModal = true; openPopover = null"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-users-slash w-4"></i> {{ __('classrooms.remove_all') }}
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
            @forelse($classroom->students as $member)
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <img src="{{ $member->avatar_url }}" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                            <p class="text-xs text-gray-500">{{ $member->email }}</p>
                        </div>
                    </div>
                    @if($classroom->canManageClassroom(auth()->user()))
                        <div class="relative">
                            <button @click="openPopover = openPopover === 'st-{{ $member->id }}' ? null : 'st-{{ $member->id }}'"
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-colors cursor-pointer">
                                <i class="fas fa-ellipsis-v px-1"></i>
                            </button>
                            <div x-show="openPopover === 'st-{{ $member->id }}'" x-transition.opacity.duration.200ms x-cloak
                                class="absolute right-0 top-10 w-44 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-10">
                                <button @click="kickId = {{ $member->id }}; kickName = '{{ addslashes($member->name) }}'; kickType = 'student'; showKickModal = true; openPopover = null"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2 cursor-pointer">
                                    <i class="fas fa-user-times w-4"></i> {{ __('classrooms.remove_student') }}
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">{{ __('No students enrolled yet.') }}</div>
            @endforelse
        </div>
    </div>

    <!-- Kick Confirmation Modal -->
    <div x-show="showKickModal" x-cloak class="fixed inset-0 z-70 flex items-center justify-center p-4"
        @keydown.escape.window="showKickModal = false">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60" @click="showKickModal = false"
            x-show="showKickModal"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-100"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <!-- Modal -->
        <div class="relative w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-100"
            x-show="showKickModal"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <!-- Header -->
            <div class="p-6 pb-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user-times mr-2 text-red-500"></i>{{ __('ยืนยันการลบสมาชิก') }}
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            {{ __('คุณต้องการลบ') }} <span class="font-semibold text-gray-700" x-text="kickName"></span> {{ __('ออกจากห้องเรียนนี้ใช่หรือไม่?') }}
                        </p>
                    </div>
                    <button @click="showKickModal = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end gap-3">
                <button @click="showKickModal = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                    {{ __('ยกเลิก') }}
                </button>
                <button
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
                    class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors cursor-pointer">
                    <i class="fas fa-user-times mr-1"></i> {{ __('ลบออก') }}
                </button>
            </div>
        </div>
    </div>
</div>
