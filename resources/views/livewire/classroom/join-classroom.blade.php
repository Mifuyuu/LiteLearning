<div class="animate__animated animate__fadeIn" x-data>
    <button wire:click="openModal"
        class="btn-3d btn-3d--indigo inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg select-none">
        <i class="fas fa-rocket mr-2"></i> สำรวจดวงดาว
    </button>

    @if($showModal)
        <template x-teleport="body">
            <div class="fixed inset-0 z-80 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/60" wire:click="$set('showModal', false)"></div>

                <div
                    class="relative z-81 bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate__animated animate__fadeInUp animate__faster">
                    <div class="flex items-center justify-between p-6 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-900">{{ __('Join Classroom') }}</h3>
                        <button wire:click="$set('showModal', false)"
                            class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form wire:submit="join" class="p-6">
                        <p class="text-sm text-gray-500 mb-4">
                            {{ __('Ask your teacher for the class code, then enter it here.') }}
                        </p>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Class Code') }}</label>
                            <input wire:model="code" type="text" maxlength="6"
                                class="w-full border border-gray-300 rounded-lg px-3 py-3 text-center text-2xl font-mono tracking-wider uppercase focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="ABC123">
                            @error('code') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" wire:click="$set('showModal', false)"
                                class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                class="btn-3d btn-3d--indigo px-6 py-2.5 text-sm font-medium rounded-lg transition-colors">
                                <span wire:loading.remove wire:target="join">{{ __('Join') }}</span>
                                <span wire:loading wire:target="join"><i class="fas fa-spinner fa-spin mr-1"></i>
                                    {{ __('Joining...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif
</div>