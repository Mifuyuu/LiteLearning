@section('page-title', __('admin.badges.title'))

<div class="space-y-6 animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6 shadow-sm">
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
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 uppercase text-[10px] font-bold text-gray-500 tracking-wider">
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
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-xl shadow-inner border border-gray-100 flex-shrink-0"
                                        style="background-color: {{ $badge->color }}20; color: {{ $badge->color }}">
                                        <i class="{{ $badge->icon ?: 'fas fa-id-badge' }}"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $badge->name }}</div>
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
    @if($showModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/60" x-data
            x-on:keydown.escape.window="$wire.showModal = false">
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
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1">{{ __('admin.badges.field_target_role') }}</label>
                            <select wire:model="form.target_role"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">{{ __('admin.badges.target_all') }}</option>
                                <option value="student">{{ __('Student') }}</option>
                                <option value="teacher">{{ __('Teacher') }}</option>
                            </select>
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
    @endif
</div>