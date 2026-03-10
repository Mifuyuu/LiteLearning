<div>
    <template x-teleport="body">
        <div x-data x-show="$wire.showModal" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
                @click.outside="$wire.set('showModal', false)">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">{{ __('Create Classroom') }}</h3>
                    <button wire:click="$set('showModal', false)"
                        class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="create" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Class Name *') }}</label>
                        <input wire:model="name" type="text"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="{{ __('e.g., Mathematics 101') }}">
                        @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Section') }}</label>
                            <input wire:model="section" type="text"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="{{ __('e.g., Section A') }}">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                        <textarea wire:model="description" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="{{ __('Add a description...') }}"></textarea>
                    </div>

                    {{-- Planet / Theme Picker --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('เลือกธีมห้องเรียน') }}</label>
                        <div class="grid grid-cols-8 gap-2 max-h-48 overflow-y-auto rounded-lg">
                            @foreach($themes as $theme)
                                @php $pn = str_pad($theme->planet_number, 2, '0', STR_PAD_LEFT); @endphp
                                <button type="button" wire:click="$set('theme_category_id', {{ $theme->id }})"
                                    title="{{ $theme->name }}"
                                    @class([
                                        'aspect-square rounded-xl border-2 p-1.5 transition-all',
                                        'border-indigo-500 bg-indigo-50' => $theme_category_id == $theme->id,
                                        'border-gray-200 hover:border-indigo-300 hover:bg-gray-50' => $theme_category_id != $theme->id,
                                    ])>
                                    <img src="/images/planets/planet_{{ $pn }}.svg"
                                        alt="{{ $theme->name }}" class="w-full h-full object-contain" />
                                </button>
                            @endforeach
                        </div>
                        @if($theme_category_id)
                            @php $selected = $themes->find($theme_category_id); @endphp
                            @if($selected)
                                <p class="mt-1.5 text-xs text-indigo-600 font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>{{ $selected->name }}
                                </p>
                            @endif
                        @endif
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                            class="btn-3d btn-3d--indigo px-6 py-2.5 text-sm font-medium rounded-lg transition-colors">
                            <span wire:loading.remove wire:target="create">{{ __('Create Class') }}</span>
                            <span wire:loading wire:target="create"><i class="fas fa-spinner fa-spin mr-1"></i>
                                {{ __('Creating...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>