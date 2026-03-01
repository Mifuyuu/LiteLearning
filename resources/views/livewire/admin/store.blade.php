@section('page-title', __('admin.store.title'))

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
                    placeholder="{{ __('admin.store.search_placeholder') }}">
            </div>

            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500 font-medium">
                    {{ __('admin.store.total_items') }} <span
                        class="text-gray-900 font-bold">{{ \App\Models\StoreItem::count() }}</span>
                </span>
                <button wire:click="openCreate"
                    class="btn-3d btn-3d--indigo inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-lg transition-colors">
                    <i class="fas fa-plus"></i>
                    {{ __('admin.store.create') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Store Items Table -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 uppercase text-sm font-bold text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">{{ __('admin.store.col_item') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.store.col_type') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.store.col_price') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.store.col_status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('admin.store.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold shrink-0">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 truncate max-w-48">{{ $item->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 truncate max-w-xs">{{ $item->description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wide bg-indigo-50 text-indigo-700">
                                    {{ $item->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900 truncate max-w-48">
                                    {{ $item->price }} <i class="fas fa-coins text-amber-500 ml-1"></i>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleActive({{ $item->id }})"
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-colors
                                                                                        {{ $item->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    {{ $item->is_active ? __('admin.store.status_active') : __('admin.store.status_inactive') }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-3 text-gray-400">
                                    <button wire:click="openEdit({{ $item->id }})"
                                        class="hover:text-indigo-600 transition-colors p-1" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="delete({{ $item->id }})"
                                        wire:confirm="{{ __('admin.store.delete_confirm') }}"
                                        class="hover:text-red-600 transition-colors p-1" title="{{ __('Delete') }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                                {{ __('admin.store.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $items->links() }}
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
                        {{ $editingId ? __('admin.store.edit_title') : __('admin.store.create_title') }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.store.field_code') }}</label>
                            <input type="text" wire:model="form.code"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            @error('form.code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div x-data="{ open: false }">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.store.field_type') }}</label>
                            <div class="relative">
                                <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                                    class="flex w-full items-center justify-between px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <span>
                                        @if($form['type'] === 'name_color') Name Color
                                        @elseif($form['type'] === 'avatar_frame') Avatar Frame
                                        @else {{ __('admin.store.field_type') }}
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
                                        <button type="button" role="menuitem"
                                            wire:click="$set('form.type', 'name_color')" @click="open = false"
                                            class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ ($form['type'] ?? '') === 'name_color' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                            Name Color
                                            @if(($form['type'] ?? '') === 'name_color') <i
                                                class="fas fa-check text-xs"></i>
                                            @endif
                                        </button>
                                        <button type="button" role="menuitem"
                                            wire:click="$set('form.type', 'avatar_frame')" @click="open = false"
                                            class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ ($form['type'] ?? '') === 'avatar_frame' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                            Avatar Frame
                                            @if(($form['type'] ?? '') === 'avatar_frame') <i
                                            class="fas fa-check text-xs"></i> @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.store.field_name') }}</label>
                        <input type="text" wire:model="form.name"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        @error('form.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.store.field_description') }}</label>
                        <textarea wire:model="form.description" rows="2"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.store.field_value') }}</label>
                        <input type="text" wire:model="form.value"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 font-mono">
                        @error('form.value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.store.field_price') }}</label>
                            <input type="number" wire:model="form.price" min="0"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <label class="flex items-center gap-2 pb-2 cursor-pointer select-none">
                            <input type="checkbox" wire:model="form.is_active" class="w-4 h-4 text-indigo-600 rounded">
                            <span
                                class="text-sm font-semibold text-gray-700">{{ __('admin.store.field_active') }}</span>
                        </label>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                    <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="save"
                        class="btn-3d btn-3d--indigo px-6 py-2 text-sm font-bold rounded-lg transition-colors">
                        {{ __('admin.store.save') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>