@section('page-title', 'จัดการห้องเรียน')

<div class="space-y-6 ">
    <!-- Filters and Search -->
    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
            <div class="relative w-full sm:w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-icon name="magnifying-glass" class="h-4 w-4" />
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                    placeholder="ค้นหาด้วยชื่อหรือรหัส...">
            </div>

            <div class="w-full sm:w-auto">
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                        class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 hover:bg-white transition-colors focus:outline-none focus:ring-1 focus:ring-blue-500 w-full sm:w-48">
                        <x-icon name="funnel" class="h-4 w-4 text-gray-400" />
                        <span>
                            @if($statusFilter === '') ห้องเรียนทั้งหมด
                            @elseif($statusFilter === 'active') เฉพาะที่ใช้งาน
                            @else เฉพาะที่ถูกเก็บถาวร
                            @endif
                        </span>
                        <x-icon name="chevron-down" class="h-3.5 w-3.5 text-gray-400 ml-auto" />
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
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $statusFilter === '' ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }}">
ห้องเรียนทั้งหมด
                                 @if($statusFilter === '') <x-icon name="check" class="h-4 w-4" /> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('statusFilter', 'active')"
                                @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $statusFilter === 'active' ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }}">
เฉพาะที่ใช้งาน
                                 @if($statusFilter === 'active') <x-icon name="check" class="h-4 w-4" /> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('statusFilter', 'archived')"
                                @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $statusFilter === 'archived' ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }}">
เฉพาะที่ถูกเก็บถาวร
                                 @if($statusFilter === 'archived') <x-icon name="check" class="h-4 w-4" /> @endif
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
                        <th class="px-6 py-3 text-left">ห้องเรียน</th>
                        <th class="px-6 py-3 text-left">ครูผู้สอน</th>
                        <th class="px-6 py-3 text-left">นักเรียน</th>
                        <th class="px-6 py-3 text-left">สถานะ</th>
                        <th class="px-6 py-3 text-right">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($classrooms as $classroom)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg shrink-0 flex items-center justify-center text-white font-bold text-lg"
                                        style="background-color: {{ $classroom->themeCategory?->color ?? \App\Models\ThemeCategory::fallbackFor($classroom->id)['color'] }}">
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
                                    <x-icon name="users" class="h-4 w-4 mr-1.5 opacity-50" />
                                    {{ $classroom->members->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $classroom->is_archived ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $classroom->is_archived ? 'เก็บถาวรแล้ว' : 'เปิดใช้งาน' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                <div class="flex justify-end gap-3 text-gray-400">
                                    <a href="{{ route('classroom.show', $classroom) }}"
                                        class="hover:text-blue-600 transition-colors p-1"
                                        title="ดู">
                                        <x-icon name="arrow-top-right-on-square" class="h-4 w-4" />
                                    </a>
                                    <button type="button"
                                        @click="$dispatch('open-delete-classroom', { id: {{ $classroom->id }}, name: '{{ addslashes($classroom->name) }}' })"
                                        class="hover:text-red-600 transition-colors p-1"
                                        title="ลบ">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <x-icon name="academic-cap" class="h-9 w-9 mb-3 opacity-20" />
                                    <p>ไม่พบห้องเรียน</p>
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
                <x-confirm-modal show="showDeleteModal" cancel="showDeleteModal = false" heading="ยืนยันการลบ">
                    <x-slot:message>
                        คุณแน่ใจหรือไม่ว่าต้องการลบ <span class="font-semibold text-[#101114]" x-text="deleteName"></span>? การกระทำนี้ไม่สามารถย้อนกลับได้
                    </x-slot:message>
                    <button type="button" @click="$wire.deleteClassroom(deleteId); showDeleteModal = false"
                        class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                        ลบ
                    </button>
                </x-confirm-modal>
            </template>
        </div>
    </div>