@section('page-title', __('admin.users.title'))

<div class="space-y-6 animate__animated animate__fadeIn">
    <!-- Filters and Search -->
    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-wrap items-center gap-3 justify-between">
            <div class="relative w-full sm:w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all"
                    placeholder="{{ __('admin.users.search_placeholder') }}">
            </div>
            <div class="flex items-center gap-3">
            <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                        class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 hover:bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i class="fas fa-user-tag text-gray-400 text-xs"></i>
                        <span>
                            @if($roleFilter === '') {{ __('admin.users.filter_role') }}
                            @elseif($roleFilter === 'admin') {{ __('Admin') }}
                            @elseif($roleFilter === 'teacher') {{ __('Teacher') }}
                            @else {{ __('Student') }}
                            @endif
                        </span>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
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
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $roleFilter === '' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                {{ __('admin.users.filter_role') }}
                                @if($roleFilter === '') <i class="fas fa-check text-xs"></i> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('roleFilter', 'admin')" @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $roleFilter === 'admin' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                {{ __('Admin') }}
                                @if($roleFilter === 'admin') <i class="fas fa-check text-xs"></i> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('roleFilter', 'teacher')" @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $roleFilter === 'teacher' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                {{ __('Teacher') }}
                                @if($roleFilter === 'teacher') <i class="fas fa-check text-xs"></i> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('roleFilter', 'student')" @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $roleFilter === 'student' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                {{ __('Student') }}
                                @if($roleFilter === 'student') <i class="fas fa-check text-xs"></i> @endif
                            </button>
                        </div>
                    </div>
                </div>

            <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                        class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 hover:bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i class="fas fa-circle text-[6px] {{ $statusFilter === 'active' ? 'text-green-500' : ($statusFilter === 'inactive' ? 'text-red-400' : 'text-gray-400') }}"></i>
                        <span>
                            @if($statusFilter === '') {{ __('admin.users.filter_status') }}
                            @elseif($statusFilter === 'active') {{ __('admin.users.status_active') }}
                            @else {{ __('admin.users.status_inactive') }}
                            @endif
                        </span>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
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
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $statusFilter === '' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                {{ __('admin.users.filter_status') }}
                                @if($statusFilter === '') <i class="fas fa-check text-xs"></i> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('statusFilter', 'active')" @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $statusFilter === 'active' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                {{ __('admin.users.status_active') }}
                                @if($statusFilter === 'active') <i class="fas fa-check text-xs"></i> @endif
                            </button>
                            <button type="button" role="menuitem" wire:click="$set('statusFilter', 'inactive')" @click="open = false"
                                class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $statusFilter === 'inactive' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                {{ __('admin.users.status_inactive') }}
                                @if($statusFilter === 'inactive') <i class="fas fa-check text-xs"></i> @endif
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
                        <th class="px-6 py-3 text-left">{{ __('admin.users.col_user') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.users.col_role') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.users.col_status') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.users.col_joined') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('admin.users.col_actions') }}</th>
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
                                                    class="text-[9px] px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded-full font-bold">YOU</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="relative" x-data="{ open: false }">
                                    <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                                        class="flex items-center gap-1.5 text-xs font-bold rounded py-1 px-2 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $user->id === auth()->id() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}"
                                        @if($user->id === auth()->id()) disabled @endif>
                                        <span>
                                            @if($user->role === 'admin') {{ __('Admin') }}
                                            @elseif($user->role === 'teacher') {{ __('Teacher') }}
                                            @else {{ __('Student') }}
                                            @endif
                                        </span>
                                        <i class="fas fa-chevron-down text-[8px] text-gray-400"></i>
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
                                                class="flex w-full items-center justify-between px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors cursor-pointer {{ $user->role === 'admin' ? 'text-indigo-700 bg-indigo-50 font-bold' : 'text-gray-700' }}">
                                                {{ __('Admin') }}
                                                @if($user->role === 'admin') <i class="fas fa-check text-[10px]"></i> @endif
                                            </button>
                                            <button type="button" role="menuitem" wire:click="updateRole({{ $user->id }}, 'teacher')" @click="open = false"
                                                class="flex w-full items-center justify-between px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors cursor-pointer {{ $user->role === 'teacher' ? 'text-indigo-700 bg-indigo-50 font-bold' : 'text-gray-700' }}">
                                                {{ __('Teacher') }}
                                                @if($user->role === 'teacher') <i class="fas fa-check text-[10px]"></i> @endif
                                            </button>
                                            <button type="button" role="menuitem" wire:click="updateRole({{ $user->id }}, 'student')" @click="open = false"
                                                class="flex w-full items-center justify-between px-3 py-1.5 text-xs hover:bg-gray-50 transition-colors cursor-pointer {{ $user->role === 'student' ? 'text-indigo-700 bg-indigo-50 font-bold' : 'text-gray-700' }}">
                                                {{ __('Student') }}
                                                @if($user->role === 'student') <i class="fas fa-check text-[10px]"></i> @endif
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
                                    <i class="fas fa-circle mr-1.5 text-[6px]"></i>
                                    {{ $user->is_active ? __('admin.users.status_active') : __('admin.users.status_inactive') }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-3 text-gray-400">
                                    <a href="{{ route('profile') }}" class="hover:text-indigo-600 transition-colors p-1"
                                        title="{{ __('admin.users.view_profile') }}">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                        <button type="button"
                            @click="$dispatch('open-delete-user', { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}' })"
                            class="hover:text-red-600 transition-colors p-1"
                            title="{{ __('admin.users.delete_user') }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-users text-4xl mb-3 opacity-20"></i>
                                    <p>{{ __('admin.users.empty') }}</p>
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
                                @click="$wire.deleteUser(deleteId); showDeleteModal = false"
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