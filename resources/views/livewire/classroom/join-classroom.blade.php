<div class="" x-data>
    <button wire:click="openModal"
        class="btn-3d btn-3d--blue inline-flex items-center px-3 sm:px-4 py-2 text-sm font-medium rounded-lg select-none">
        <x-icon name="rocket-launch-solid" class="h-4 w-4 sm:mr-2" /> <span class="hidden sm:inline">สำรวจดวงดาว</span>
    </button>

    @if($showModal)
        <template x-teleport="body">
            <div class="fixed inset-0 z-80 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/60" wire:click="$set('showModal', false)"></div>

                <div
                    class="relative z-81 bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 Up animate__faster">
                    <div class="flex items-center justify-between p-6 pb-0">
                        <h3 class="text-xl font-bold text-gray-900">เข้าร่วมห้องเรียน</h3>
                        <button wire:click="$set('showModal', false)"
                            class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                            <x-icon name="x-mark" class="h-4 w-4" />
                        </button>
                    </div>

                    <form wire:submit.prevent="join" class="p-6">
                        <p class="text-sm text-gray-500 mb-4">
                            {{ 'ขอรหัสห้องเรียนจากครูของคุณ แล้วใส่ที่นี่' }}
                        </p>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ 'รหัสห้องเรียน' }}</label>
                            <input wire:model.live="code" type="text" maxlength="6"
                                class="w-full border rounded-lg px-3 py-3 text-center text-2xl font-mono tracking-wider uppercase focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $errors->has('code') ? 'border-red-500' : 'border-gray-300' }}"
                                placeholder="ABC123">
                            <div class="h-5 mt-1">
                                @error('code') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex gap-3 mt-6 w-full">
                            <button type="button" wire:click="$set('showModal', false)"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 text-sm font-bold rounded-lg transition-colors cursor-pointer">
                                {{ 'ยกเลิก' }}
                            </button>
                            <button type="submit"
                                class="flex-1 btn-3d btn-3d--blue py-2.5 text-sm font-bold rounded-lg transition-colors cursor-pointer">
                                <span wire:loading.remove wire:target="join">{{ 'เข้าร่วม' }}</span>
                                <span wire:loading wire:target="join"><x-icon name="spinner" class="h-4 w-4 mr-1 animate-spin" />
                                    {{ 'กำลังเข้าร่วม...' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif
</div>