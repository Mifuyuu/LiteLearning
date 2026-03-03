@section('page-title', __('admin.classrooms.title'))

<div class="space-y-6 animate__animated animate__fadeIn">
    <!-- Filters and Search -->
    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
            <div class="relative w-full sm:w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all"
                    placeholder="{{ __('admin.classrooms.search_placeholder') }}">
            </div>

            <div class="w-full sm:w-auto">
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                        class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 hover:bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-48">
                        <i class="fas fa-filter text-gray-400 text-xs"></i>
                        <span>
                            @if($statusFilter === '') {{ __('admin.classrooms.filter_all') }}
                            @elseif($statusFilter === 'active') {{ __('admin.classrooms.filter_active') }}
                            @else {{ __('admin.classrooms.filter_archived') }}
                            @endif
                        </span>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 ml-auto"></i>
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-0 mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                        <div role="menu">
                            <button type="button" role="menuitem" wire:click="$set('statusFilter', '')"
                                @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $statusFilter === '' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                {{ __('admin.classrooms.filter_all') }}
                                @if($statusFilter === '') <i class="fas fa-check text-xs"></i> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('statusFilter', 'active')"
                                @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $statusFilter === 'active' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                {{ __('admin.classrooms.filter_active') }}
                                @if($statusFilter === 'active') <i class="fas fa-check text-xs"></i> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('statusFilter', 'archived')"
                                @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $statusFilter === 'archived' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                {{ __('admin.classrooms.filter_archived') }}
                                @if($statusFilter === 'archived') <i class="fas fa-check text-xs"></i> @endif
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Classrooms Table -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 uppercase text-sm font-bold text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">{{ __('admin.classrooms.col_classroom') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.classrooms.col_teacher') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.classrooms.col_students') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.classrooms.col_status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('admin.classrooms.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($classrooms as $classroom)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg shrink-0 flex items-center justify-center text-white font-bold text-lg"
                                        style="background-color: {{ $classroom->theme_color }}">
                                        {{ substr($classroom->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 truncate max-w-48">
                                            {{ $classroom->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">#{{ $classroom->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img class="h-6 w-6 rounded-full object-cover mr-2 shrink-0"
                                        src="{{ $classroom->teacher->avatar_url }}" alt="">
                                    <span
                                        class="text-sm text-gray-700 truncate max-w-48">{{ $classroom->teacher->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-users mr-1.5 opacity-50"></i>
                                    {{ $classroom->members->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                                                            {{ $classroom->is_archived ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $classroom->is_archived ? __('admin.classrooms.status_archived') : __('admin.classrooms.status_active') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                <div class="flex justify-end gap-3 text-gray-400">
                                    <a href="{{ route('classroom.show', $classroom) }}"
                                        class="hover:text-indigo-600 transition-colors p-1"
                                        title="{{ __('admin.classrooms.view') }}">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <button type="button"
                                        @click="$dispatch('open-delete-classroom', { id: {{ $classroom->id }}, name: '{{ addslashes($classroom->name) }}' })"
                                        class="hover:text-red-600 transition-colors p-1"
                                        title="{{ __('admin.classrooms.delete') }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-chalkboard text-4xl mb-3 opacity-20"></i>
                                    <p>{{ __('admin.classrooms.empty') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classrooms->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $classrooms->links() }}
            </div>
        @endif
    <div x-data="{ showDeleteModal: false, deleteId: null, deleteName: '' }"
        @open-delete-classroom.window="deleteId = $event.detail.id; deleteName = $event.detail.name; showDeleteModal = true"
        @keydown.escape.window="showDeleteModal = false">
        <template x-teleport="body">
            <div x-show="showDeleteModal" x-cloak
                class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60"
                @click.self="showDeleteModal = false">
                <div x-show="showDeleteModal"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-semibold text-gray-900">{{ __('ยืนยันการลบ') }}</h4>
                            <p class="text-sm font-medium text-gray-700 mt-1" x-text="deleteName"></p>
                        </div>
                        <button type="button" @click="showDeleteModal = false"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <div class="px-6 py-5">
                        <p class="text-sm text-gray-500 mb-4">
                            {{ __('คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้? การกระทำนี้ไม่สามารถย้อนกลับได้') }}
                        </p>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showDeleteModal = false"
                                class="inline-flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-xmark mr-1.5"></i>{{ __('ยกเลิก') }}
                            </button>
                            <button type="button"
                                @click="$wire.deleteClassroom(deleteId); showDeleteModal = false"
                                class="px-4 py-2 text-sm text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors inline-flex items-center">
                                <i class="fas fa-trash-alt mr-1.5"></i>{{ __('ลบ') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>