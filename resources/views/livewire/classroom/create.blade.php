<div>
    @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
    <button wire:click="openModal"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
        <i class="fas fa-plus mr-2"></i> {{ __('Create Class') }}
    </button>
    @endif

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center" x-data>
        <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="$set('showModal', false)"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 animate__animated animate__fadeInUp animate__faster">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">{{ __('Create Classroom') }}</h3>
                <button wire:click="$set('showModal', false)" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form wire:submit="create" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Class Name *') }}</label>
                    <input wire:model="name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Mathematics 101">
                    @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Section') }}</label>
                        <input wire:model="section" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Section A">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Subject') }}</label>
                        <input wire:model="subject" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Math">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                    <textarea wire:model="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Add a description..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Theme Color') }}</label>
                    <div class="flex gap-2">
                        @foreach(['#4F46E5', '#059669', '#DC2626', '#D97706', '#7C3AED', '#2563EB', '#DB2777'] as $color)
                        <button type="button" wire:click="$set('theme_color', '{{ $color }}')"
                                class="w-8 h-8 rounded-full border-2 transition-transform hover:scale-110 {{ $theme_color === $color ? 'border-gray-900 scale-110' : 'border-transparent' }}"
                                style="background-color: {{ $color }}">
                        </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <span wire:loading.remove wire:target="create">{{ __('Create Class') }}</span>
                        <span wire:loading wire:target="create"><i class="fas fa-spinner fa-spin mr-1"></i> {{ __('Creating...') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
