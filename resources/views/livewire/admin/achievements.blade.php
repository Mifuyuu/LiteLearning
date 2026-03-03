@section('page-title', __('admin.achievements.title'))

<div class="space-y-6 animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
            <div class="relative w-full sm:w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all"
                    placeholder="{{ __('admin.achievements.search_placeholder') }}">
            </div>

            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-400 font-medium">{{ __('admin.achievements.hint') }}</span>
                <button wire:click="openCreate"
                    class="btn-3d btn-3d--indigo inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-lg transition-colors">
                    <i class="fas fa-plus"></i>
                    {{ __('admin.achievements.create') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 uppercase text-sm font-bold text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">{{ __('admin.achievements.col_achievement') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.achievements.col_rewards') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.achievements.col_target') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.achievements.col_status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('admin.achievements.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($achievements as $achievement)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-lg shrink-0">
                                        <i class="{{ $achievement->icon ?: 'fas fa-award' }}"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 truncate max-w-48">{{ $achievement->name }}</div>
                                        <div class="text-xs text-gray-500 line-clamp-1">{{ $achievement->description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center text-xs font-bold text-amber-600">
                                        <i class="fas fa-coins mr-1"></i> {{ $achievement->coin_reward }}
                                    </span>
                                    <span class="inline-flex items-center text-xs font-bold text-blue-600">
                                        <i class="fas fa-bolt mr-1"></i> {{ $achievement->xp_reward }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600">
                                    {{ $achievement->target_role ?: __('admin.achievements.target_all') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleActive({{ $achievement->id }})"
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-colors
                                    {{ $achievement->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    {{ $achievement->is_active ? __('admin.achievements.status_active') : __('admin.achievements.status_inactive') }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-3 text-gray-400">
                                    <button wire:click="openEdit({{ $achievement->id }})" class="hover:text-indigo-600 transition-colors p-1" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button"
                                        @click="$dispatch('open-delete-achievement', { id: {{ $achievement->id }}, name: '{{ addslashes($achievement->name) }}' })"
                                        class="hover:text-red-600 transition-colors p-1" title="{{ __('Delete') }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                                {{ __('admin.achievements.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($achievements->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $achievements->links() }}
            </div>
        @endif
    </div>

<div x-data="{ showDeleteModal: false, deleteId: null, deleteName: '' }"
    @open-delete-achievement.window="deleteId = $event.detail.id; deleteName = $event.detail.name; showDeleteModal = true"
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
                            @click="$wire.delete(deleteId); showDeleteModal = false"
                            class="px-4 py-2 text-sm text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors inline-flex items-center">
                            <i class="fas fa-trash-alt mr-1.5"></i>{{ __('ลบ') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

    <!-- Create / Edit Modal -->
    <template x-teleport="body">
        <div x-data x-show="$wire.showModal" x-cloak
            x-on:keydown.escape.window="$wire.showModal = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60"
            @click.self="$wire.showModal = false">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">
                    {{ $editingId ? __('admin.achievements.edit_title') : __('admin.achievements.create_title') }}
                </h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.achievements.field_code') }}</label>
                        <input type="text" wire:model="form.code" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        @error('form.code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.achievements.field_icon') }}</label>
                        <input type="text" wire:model="form.icon" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 font-mono">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.achievements.field_name') }}</label>
                    <input type="text" wire:model="form.name" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    @error('form.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.achievements.field_description') }}</label>
                    <textarea wire:model="form.description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.achievements.field_coin_reward') }}</label>
                        <input type="number" wire:model="form.coin_reward" min="0" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.achievements.field_xp_reward') }}</label>
                        <input type="number" wire:model="form.xp_reward" min="0" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="flex items-end gap-4">
                        <div class="flex-1" x-data="{ open: false }">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.achievements.field_target_role') }}</label>
                            <div class="relative">
                                <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                                    class="flex w-full items-center justify-between px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <span>
                                        @if($form['target_role'] === '' || $form['target_role'] === null) {{ __('admin.achievements.target_all') }}
                                        @elseif($form['target_role'] === 'student') {{ __('Student') }}
                                        @else {{ __('Teacher') }}
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
                                    class="absolute left-0 mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                                    <div role="menu">
                                        <button type="button" role="menuitem" wire:click="$set('form.target_role', '')" @click="open = false"
                                            class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ ($form['target_role'] ?? '') === '' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                            {{ __('admin.achievements.target_all') }}
                                            @if(($form['target_role'] ?? '') === '') <i class="fas fa-check text-xs"></i> @endif
                                        </button>
                                        <button type="button" role="menuitem" wire:click="$set('form.target_role', 'student')" @click="open = false"
                                            class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ ($form['target_role'] ?? '') === 'student' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                            {{ __('Student') }}
                                            @if(($form['target_role'] ?? '') === 'student') <i class="fas fa-check text-xs"></i> @endif
                                        </button>
                                        <button type="button" role="menuitem" wire:click="$set('form.target_role', 'teacher')" @click="open = false"
                                            class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ ($form['target_role'] ?? '') === 'teacher' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                            {{ __('Teacher') }}
                                            @if(($form['target_role'] ?? '') === 'teacher') <i class="fas fa-check text-xs"></i> @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <label class="flex items-center gap-2 pb-2 cursor-pointer select-none">
                        <input type="checkbox" wire:model="form.is_active" class="w-4 h-4 text-indigo-600 rounded">
                        <span class="text-sm font-semibold text-gray-700">{{ __('admin.achievements.field_active') }}</span>
                    </label>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="$set('showModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    {{ __('Cancel') }}
                </button>
                <button wire:click="save" class="btn-3d btn-3d--indigo px-6 py-2 text-sm font-bold rounded-lg transition-colors">
                    {{ __('admin.achievements.save') }}
                </button>
            </div>
        </div>
        </div>
    </template>
</div>