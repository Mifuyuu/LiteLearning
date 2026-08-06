@section('page-title', 'จัดการผู้ใช้')

<div class="space-y-6 ">
    <!-- Filters and Search -->
    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-wrap items-center gap-3 justify-between">
            <div class="relative w-full sm:w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-icon name="magnifying-glass" class="h-4 w-4" />
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                    placeholder="ค้นหาด้วยชื่อหรืออีเมล...">
            </div>
            <div class="flex items-center gap-3">
            <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                        class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 hover:bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <x-icon name="tag" class="h-4 w-4 text-gray-400" />
                        <span>
                            @if($roleFilter === '') ทุกบทบาท
                            @elseif($roleFilter === 'admin') แอดมิน
                            @elseif($roleFilter === 'teacher') ครู
                            @else นักเรียน
                            @endif
                        </span>
                        <x-icon name="chevron-down" class="h-3.5 w-3.5 text-gray-400" />
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-0 mt-2 min-w-full bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                        <div role="menu">
                            <button type="button" role="menuitem" wire:click="$set('roleFilter', '')" @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $roleFilter === '' ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }}">
                                ทุกบทบาท
                                @if($roleFilter === '') <x-icon name="check" class="h-4 w-4" /> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('roleFilter', 'admin')" @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $roleFilter === 'admin' ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }}">
                                แอดมิน
                                @if($roleFilter === 'admin') <x-icon name="check" class="h-4 w-4" /> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('roleFilter', 'teacher')" @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $roleFilter === 'teacher' ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }}">
                                ครู
                                @if($roleFilter === 'teacher') <x-icon name="check" class="h-4 w-4" /> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('roleFilter', 'student')" @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $roleFilter === 'student' ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }}">
                                นักเรียน
                                @if($roleFilter === 'student') <x-icon name="check" class="h-4 w-4" /> @endif
                            </button>
                        </div>
                    </div>
                </div>

            <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                        class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 hover:bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <x-icon name="circle" class="h-1.5 w-1.5 {{ $statusFilter === 'active' ? 'text-green-500' : ($statusFilter === 'inactive' ? 'text-red-400' : 'text-gray-400') }}" />
                        <span>
                            @if($statusFilter === '') ทุกสถานะ
                            @elseif($statusFilter === 'active') ใช้งาน
                            @else ปิดใช้งาน
                            @endif
                        </span>
                        <x-icon name="chevron-down" class="h-3.5 w-3.5 text-gray-400" />
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-0 mt-2 min-w-full bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                        <div role="menu">
                            <button type="button" role="menuitem" wire:click="$set('statusFilter', '')" @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $statusFilter === '' ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }}">
                                ทุกสถานะ
                                @if($statusFilter === '') <x-icon name="check" class="h-4 w-4" /> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('statusFilter', 'active')" @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $statusFilter === 'active' ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }}">
                                ใช้งาน
                                @if($statusFilter === 'active') <x-icon name="check" class="h-4 w-4" /> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('statusFilter', 'inactive')" @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $statusFilter === 'inactive' ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }}">
                                ปิดใช้งาน
                                @if($statusFilter === 'inactive') <x-icon name="check" class="h-4 w-4" /> @endif
                            </button>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto [scrollbar-width:thin]">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 uppercase text-sm font-bold text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">ผู้ใช้</th>
                        <th class="px-6 py-3 text-left">บทบาท</th>
                        <th class="px-6 py-3 text-left">สถานะ</th>
                        <th class="px-6 py-3 text-left">วันที่สมัคร</th>
                        <th class="px-6 py-3 text-right">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img class="h-9 w-9 rounded-full object-cover ring-2 ring-gray-100 shrink-0"
                                        src="{{ $user->avatar_url }}" alt="">
                                    <div class="ml-3">
                                        <div class="text-sm font-bold text-gray-900 truncate max-w-48 flex items-center gap-1.5">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span
                                                    class="text-[9px] px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded-full font-bold">YOU</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="relative" x-data="{ open: false }">
                                    <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                                        class="flex items-center gap-1.5 text-xs font-bold rounded py-1 px-2 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-400 {{ $user->id === auth()->id() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}"
                                        @if($user->id === auth()->id()) disabled @endif>
                                        <span>
                                            @if($user->role === 'admin') แอดมิน
                                            @elseif($user->role === 'teacher') ครู
                                            @else นักเรียน
                                            @endif
                                        </span>
                                        <x-icon name="chevron-down" class="h-2.5 w-2.5 text-gray-400" />
                                    </button>
                                    <div x-show="open" x-cloak @click.outside="open = false"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute left-0 mt-1 w-36 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                                        <div role="menu">
                                            <button type="button" role="menuitem" wire:click="updateRole({{ $user->id }}, 'admin')" @click="open = false"
                                                class="flex w-full items-center justify-between px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors cursor-pointer {{ $user->role === 'admin' ? 'text-blue-700 bg-blue-50 font-bold' : 'text-gray-700' }}">
                                                แอดมิน
                                                @if($user->role === 'admin') <x-icon name="check" class="h-3 w-3" /> @endif
                                            </button>
                                            <button type="button" role="menuitem" wire:click="updateRole({{ $user->id }}, 'teacher')" @click="open = false"
                                                class="flex w-full items-center justify-between px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors cursor-pointer {{ $user->role === 'teacher' ? 'text-blue-700 bg-blue-50 font-bold' : 'text-gray-700' }}">
                                                ครู
                                                @if($user->role === 'teacher') <x-icon name="check" class="h-3 w-3" /> @endif
                                            </button>
                                            <button type="button" role="menuitem" wire:click="updateRole({{ $user->id }}, 'student')" @click="open = false"
                                                class="flex w-full items-center justify-between px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors cursor-pointer {{ $user->role === 'student' ? 'text-blue-700 bg-blue-50 font-bold' : 'text-gray-700' }}">
                                                นักเรียน
                                                @if($user->role === 'student') <x-icon name="check" class="h-3 w-3" /> @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleStatus({{ $user->id }})"
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-colors
                                                {{ $user->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}"
                                    @if($user->id === auth()->id()) disabled @endif>
                                    <x-icon name="circle" class="h-1.5 w-1.5 mr-1.5" />
                                    {{ $user->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-3 text-gray-400">
                                    <a href="{{ route('profile', $user) }}" wire:navigate class="hover:text-blue-600 transition-colors p-1"
                                        title="ดูโปรไฟล์">
                                        <x-icon name="arrow-top-right-on-square" class="h-4 w-4" />
                                    </a>
                                    @if($user->id !== auth()->id())
                        <button type="button"
                            @click="$dispatch('open-delete-user', { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}' })"
                            class="hover:text-red-600 transition-colors p-1"
                            title="ลบผู้ใช้">
                            <x-icon name="trash" class="h-4 w-4" />
                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <x-icon name="users" class="h-9 w-9 mb-3 opacity-20" />
                                    <p>ไม่พบผู้ใช้ที่ตรงกับเงื่อนไข</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    <div x-data="{ showDeleteModal: false, deleteId: null, deleteName: '' }"
        @open-delete-user.window="deleteId = $event.detail.id; deleteName = $event.detail.name; showDeleteModal = true"
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
                            <h4 class="text-base font-semibold text-gray-900">ยืนยันการลบ</h4>
                            <p class="text-sm font-medium text-gray-700 mt-1" x-text="deleteName"></p>
                        </div>
                        <button type="button" @click="showDeleteModal = false"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <x-icon name="x-mark" class="h-5 w-5" />
                        </button>
                    </div>
                    <div class="px-6 py-5">
                        <p class="text-sm text-gray-500 mb-4">
                            คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้? การกระทำนี้ไม่สามารถย้อนกลับได้
                        </p>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showDeleteModal = false"
                                class="inline-flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <x-icon name="x-mark" class="h-4 w-4 mr-1.5" />ยกเลิก
                            </button>
                            <button type="button"
                                @click="$wire.deleteUser(deleteId); showDeleteModal = false"
                                class="px-4 py-2 text-sm text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors inline-flex items-center">
                                <x-icon name="trash" class="h-4 w-4 mr-1.5" />ลบ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>