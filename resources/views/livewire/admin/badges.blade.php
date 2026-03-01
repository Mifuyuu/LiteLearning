@section('page-title', __('admin.badges.title'))

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
                    placeholder="{{ __('admin.badges.search_placeholder') }}">
            </div>

            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-400 font-medium">{{ __('admin.badges.hint') }}</span>
                <button wire:click="openCreate"
                    class="btn-3d btn-3d--indigo inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-lg transition-colors">
                    <i class="fas fa-plus"></i>
                    {{ __('admin.badges.create') }}
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
                        <th class="px-6 py-3 text-left">{{ __('admin.badges.col_badge') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.badges.col_appearance') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.badges.col_target') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('admin.badges.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($badges as $badge)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-xl shadow-inner border border-gray-100 shrink-0"
                                        style="background-color: {{ $badge->color }}20; color: {{ $badge->color }}">
                                        <i class="{{ $badge->icon ?: 'fas fa-id-badge' }}"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 truncate max-w-48">{{ $badge->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $badge->description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full shadow-sm border border-gray-200"
                                        style="background-color: {{ $badge->color }}"></span>
                                    <code class="text-[10px] text-gray-400 font-mono">{{ $badge->color }}</code>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600">
                                    {{ $badge->target_role ?: __('admin.badges.target_all') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-3 text-gray-400">
                                    <button wire:click="openEdit({{ $badge->id }})"
                                        class="hover:text-indigo-600 transition-colors p-1" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="delete({{ $badge->id }})"
                                        wire:confirm="{{ __('admin.badges.delete_confirm') }}"
                                        class="hover:text-red-600 transition-colors p-1" title="{{ __('Delete') }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">
                                {{ __('admin.badges.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($badges->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $badges->links() }}
            </div>
        @endif
    </div>

    <!-- Create / Edit Modal -->
    <template x-teleport="body">
        <div x-data x-show="$wire.showModal" x-cloak x-on:keydown.escape.window="$wire.showModal = false"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60"
            @click.self="$wire.showModal = false">
            <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $editingId ? __('admin.badges.edit_title') : __('admin.badges.create_title') }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.badges.field_code') }}</label>
                            <input type="text" wire:model="form.code"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            @error('form.code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.badges.field_icon') }}</label>
                            <input type="text" wire:model="form.icon"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 font-mono">
                        </div>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.badges.field_name') }}</label>
                        <input type="text" wire:model="form.name"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        @error('form.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.badges.field_description') }}</label>
                        <textarea wire:model="form.description" rows="2"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.badges.field_color') }}</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model="form.color"
                                    class="w-10 h-10 rounded border border-gray-300 p-1 cursor-pointer">
                                <input type="text" wire:model="form.color"
                                    class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 font-mono">
                            </div>
                        </div>
                        <div x-data="{ open: false }">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.badges.field_target_role') }}</label>
                            <div class="relative">
                                <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                                    class="flex w-full items-center justify-between px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <span>
                                        @if($form['target_role'] === '' || $form['target_role'] === null)
                                            {{ __('admin.badges.target_all') }}
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
                                        <button type="button" role="menuitem" wire:click="$set('form.target_role', '')"
                                            @click="open = false"
                                            class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ ($form['target_role'] ?? '') === '' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                            {{ __('admin.badges.target_all') }}
                                            @if(($form['target_role'] ?? '') === '') <i
                                                class="fas fa-check text-xs"></i>
                                            @endif
                                        </button>
                                        <button type="button" role="menuitem"
                                            wire:click="$set('form.target_role', 'student')" @click="open = false"
                                            class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ ($form['target_role'] ?? '') === 'student' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                            {{ __('Student') }}
                                            @if(($form['target_role'] ?? '') === 'student') <i
                                            class="fas fa-check text-xs"></i> @endif
                                        </button>
                                        <button type="button" role="menuitem"
                                            wire:click="$set('form.target_role', 'teacher')" @click="open = false"
                                            class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ ($form['target_role'] ?? '') === 'teacher' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                            {{ __('Teacher') }}
                                            @if(($form['target_role'] ?? '') === 'teacher') <i
                                            class="fas fa-check text-xs"></i> @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                    <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="save"
                        class="btn-3d btn-3d--indigo px-6 py-2 text-sm font-bold rounded-lg transition-colors">
                        {{ __('admin.badges.save') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>