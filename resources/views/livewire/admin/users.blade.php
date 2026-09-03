@section('page-title', 'จัดการผู้ใช้')

<div class="space-y-6 ">
    <div class="bg-white rounded-2xl border-3 border-gray-200 overflow-hidden">
    <!-- Filters and Search -->
    <div class="p-4 sm:p-6">
        <div class="flex flex-wrap items-center gap-3 justify-between">
            <div class="relative w-full sm:w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-icon name="magnifying-glass" class="h-4 w-4" />
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                    placeholder="ค้นหาด้วยชื่อหรืออีเมล...">
            </div>
            <div class="flex items-center gap-3">
            <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                        class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 hover:bg-white transition-colors focus:outline-none focus:ring-1 focus:ring-blue-500">
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
                        class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 hover:bg-white transition-colors focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <span>
                            @if($statusFilter === '') ทุกสถานะ
                            @elseif($statusFilter === 'active') เปิดใช้งาน
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
                                เปิดใช้งาน
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
    <div class="border-t border-gray-200">
        <div class="overflow-x-auto [scrollbar-width:thin]">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 uppercase text-sm font-bold text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">ผู้ใช้</th>
                        <th class="px-6 py-3 text-left">บทบาท</th>
                        <th class="px-6 py-3 text-left">วันที่สมัคร</th>
                        <th class="px-6 py-3 text-left">พื้นที่จัดเก็บ</th>
                        <th class="px-6 py-3 text-left">สถานะ</th>
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
                                        class="flex items-center gap-1.5 text-xs font-bold rounded py-1 px-2 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-1 focus:ring-blue-400 {{ $user->id === auth()->id() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}"
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
                                            <button type="button" role="menuitem" wire:click="updateRole('{{ $user->username }}', 'admin')" @click="open = false"
                                                class="flex w-full items-center justify-between px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors cursor-pointer {{ $user->role === 'admin' ? 'text-blue-700 bg-blue-50 font-bold' : 'text-gray-700' }}">
                                                แอดมิน
                                                @if($user->role === 'admin') <x-icon name="check" class="h-3 w-3" /> @endif
                                            </button>
                                            <button type="button" role="menuitem" wire:click="updateRole('{{ $user->username }}', 'teacher')" @click="open = false"
                                                class="flex w-full items-center justify-between px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors cursor-pointer {{ $user->role === 'teacher' ? 'text-blue-700 bg-blue-50 font-bold' : 'text-gray-700' }}">
                                                ครู
                                                @if($user->role === 'teacher') <x-icon name="check" class="h-3 w-3" /> @endif
                                            </button>
                                            <button type="button" role="menuitem" wire:click="updateRole('{{ $user->username }}', 'student')" @click="open = false"
                                                class="flex w-full items-center justify-between px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors cursor-pointer {{ $user->role === 'student' ? 'text-blue-700 bg-blue-50 font-bold' : 'text-gray-700' }}">
                                                นักเรียน
                                                @if($user->role === 'student') <x-icon name="check" class="h-3 w-3" /> @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $user->created_at->translatedFormat('j M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $usedBytes = $user->attachments_sum_file_size ?? 0;
                                    $usedPercent = min(100, ($usedBytes / \App\Services\AdminAnalyticsService::STORAGE_LIMIT_BYTES) * 100);
                                @endphp
                                <div class="w-32">
                                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $usedPercent >= 90 ? 'bg-red-500' : ($usedPercent >= 70 ? 'bg-amber-500' : 'bg-blue-500') }}"
                                            style="width: {{ max(2, $usedPercent) }}%"></div>
                                    </div>
                                    <p class="mt-1 text-[10px] text-gray-500">{{ number_format($usedBytes / 1048576, 1) }} / 1024 MB</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleStatus('{{ $user->username }}')"
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-colors
                                                {{ $user->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}"
                                    @if($user->id === auth()->id()) disabled @endif>
                                    {{ $user->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-3 text-gray-400">
                                    <a href="{{ route('profile', $user) }}" wire:navigate class="hover:text-blue-600 transition-colors p-1"
                                        title="ดูโปรไฟล์">
                                        <x-icon name="arrow-top-right-on-square" class="h-4 w-4" />
                                    </a>
                                    <button type="button"
                                        @click="$dispatch('open-manage-user', { id: @js($user->username), name: @js($user->name), role: @js($user->role), coins: {{ $user->coins }}, xp: {{ $user->xp }} })"
                                        class="hover:text-amber-600 transition-colors p-1"
                                        title="จัดการผู้ใช้">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </button>
                                    @if($user->id !== auth()->id())
                        <button type="button"
                            @click="$dispatch('open-delete-user', { id: '{{ $user->username }}', name: '{{ addslashes($user->name) }}' })"
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
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
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
    </div>
    </div>
    <div x-data="{ showDeleteModal: false, deleteId: null, deleteName: '' }"
        @open-delete-user.window="deleteId = $event.detail.id; deleteName = $event.detail.name; showDeleteModal = true"
        @keydown.escape.window="showDeleteModal = false">
        <template x-teleport="body">
            <x-confirm-modal show="showDeleteModal" cancel="showDeleteModal = false" heading="ยืนยันการลบ">
                <x-slot:message>
                    คุณแน่ใจหรือไม่ว่าต้องการลบ <span class="font-semibold text-[#101114]" x-text="deleteName"></span>? การกระทำนี้ไม่สามารถย้อนกลับได้
                </x-slot:message>
                <button type="button" @click="$wire.deleteUser(deleteId); showDeleteModal = false"
                    class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                    ลบ
                </button>
            </x-confirm-modal>
        </template>
    </div>
    <div x-data="{ show: false, id: null, name: '', role: 'student', coins: 0, xp: 0 }"
        @open-manage-user.window="id = $event.detail.id; name = $event.detail.name; role = $event.detail.role; coins = $event.detail.coins; xp = $event.detail.xp; show = true"
        @keydown.escape.window="show = false">
        <template x-teleport="body">
            <div x-show="show" x-cloak class="fixed inset-0 z-70 flex items-center justify-center bg-black/50 p-4"
                @click.self="show = false">
                <div x-show="show"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="w-full max-w-md rounded-xl border border-[#dedee5] bg-white p-6 shadow-[rgba(0,0,0,0.08)_0px_8px_32px]">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-50">
                        <x-icon name="pencil" class="h-7 w-7 text-amber-500" />
                    </div>
                    <h4 class="mt-4 text-center text-lg font-black text-[#101114]">จัดการผู้ใช้</h4>
                    <div class="mt-4 space-y-3">
                        <label class="block">
                            <span class="text-xs font-bold text-[#686b82]">ชื่อ</span>
                            <input type="text" x-model="name" maxlength="{{ \App\Models\User::NAME_MAX_LENGTH }}"
                                class="mt-1 block w-full rounded-lg border border-[#dedee5] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </label>
                        <template x-if="role === 'student'">
                            <div class="grid grid-cols-2 gap-3">
                                <label class="block">
                                    <span class="text-xs font-bold text-[#686b82]">เหรียญ</span>
                                    <input type="number" min="0" x-model.number="coins"
                                        class="mt-1 block w-full rounded-lg border border-[#dedee5] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                </label>
                                <label class="block">
                                    <span class="text-xs font-bold text-[#686b82]">XP</span>
                                    <input type="number" min="0" x-model.number="xp"
                                        class="mt-1 block w-full rounded-lg border border-[#dedee5] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                </label>
                            </div>
                        </template>
                    </div>
                    <div class="mt-5 flex justify-center gap-2">
                        <button type="button" @click="show = false"
                            class="flex-1 rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-bold text-[#686b82] transition hover:bg-gray-200">
                            ยกเลิก
                        </button>
                        <button type="button" @click="$wire.updateUser(id, name, coins, xp); show = false"
                            class="flex-1 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-amber-600">
                            บันทึก
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>