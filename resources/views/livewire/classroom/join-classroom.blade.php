<div class="" x-data>
    <button wire:click="openModal"
        class="btn-3d btn-3d--indigo inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg select-none">
        <x-icon name="rocket-launch" class="mr-2 h-4 w-4" /> สำรวจดวงดาว
    </button>

    @if($showModal)
        <template x-teleport="body">
            <div class="fixed inset-0 z-80 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/60" wire:click="$set('showModal', false)"></div>

                <div
                    class="relative z-81 bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 Up animate__faster">
                    <div class="flex items-center justify-between p-6 pb-0">
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

                        <div class="flex gap-3 mt-6 w-full">
                            <button type="button" wire:click="$set('showModal', false)"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 text-sm font-bold rounded-lg transition-colors cursor-pointer">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                class="flex-1 btn-3d btn-3d--indigo py-2.5 text-sm font-bold rounded-lg transition-colors cursor-pointer">
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