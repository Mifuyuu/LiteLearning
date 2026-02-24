<div x-data>
    @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
        <button wire:click="openModal"
            class="btn-3d btn-3d--indigo inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i> {{ __('Create Class') }}
        </button>
    @endif

    <!-- Modal -->
    @if($showModal)
        <template x-teleport="body">
            <div class="fixed inset-0 z-80 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/60 transition-opacity" wire:click="$set('showModal', false)"></div>

                <div
                    class="relative z-81 bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 animate__animated animate__zoomIn animate__faster">
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
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Subject') }}</label>
                                <input wire:model="subject" type="text"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="{{ __('e.g., Math') }}">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                            <textarea wire:model="description" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="{{ __('Add a description...') }}"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Theme Color') }}</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['#DC2626', '#F97316', '#F59E0B', '#059669', '#0891B2', '#2563EB', '#4F46E5', '#9333EA', '#DB2777', '#475569'] as $color)
                                    <button type="button" wire:click="$set('theme_color', '{{ $color }}')"
                                        class="w-8 h-8 rounded-full border-2 transition-transform hover:scale-110 {{ $theme_color === $color ? 'border-gray-900 scale-110' : 'border-transparent' }}"
                                        style="background-color: {{ $color }}" title="{{ $color }}">
                                    </button>
                                @endforeach
                            </div>
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
    @endif
</div>