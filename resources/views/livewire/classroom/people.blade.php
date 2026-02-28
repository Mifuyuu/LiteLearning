@section('page-title', __('People') . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ __('Classrooms') }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('classroom.show', $classroom) }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ $classroom->name }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-semibold">{{ __('People') }}</span>
    </nav>
@endsection

<div class="max-w-3xl mx-auto animate__animated animate__fadeIn">
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
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.outside="open = false"
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-colors cursor-pointer">
                                <i class="fas fa-ellipsis-v px-1"></i>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms x-cloak
                                class="absolute right-0 top-10 w-44 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-10">
                                <button wire:click="removeCoTeacher({{ $coTeacher->id }})"
                                    wire:confirm="{{ __('ต้องการลบ Co-Teacher คนนี้ออกจากห้องใช่ไหม?') }}"
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
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors cursor-pointer">
                        <i class="fas fa-ellipsis-v px-1"></i>
                    </button>
                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-10">
                        <button wire:click="removeAllMembers" wire:confirm="{{ __('classrooms.remove_all_confirm') }}"
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
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.outside="open = false"
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-colors cursor-pointer">
                                <i class="fas fa-ellipsis-v px-1"></i>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms x-cloak
                                class="absolute right-0 top-10 w-44 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-10">
                                <button wire:click="removeMember({{ $member->id }})"
                                    wire:confirm="{{ __('classrooms.remove_confirm', ['name' => $member->name]) }}"
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
</div>