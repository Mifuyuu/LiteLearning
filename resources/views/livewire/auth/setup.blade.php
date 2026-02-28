<div class="animate__animated animate__fadeIn">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex items-center space-x-3 mb-2">
            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                <i class="fas fa-graduation-cap text-white text-lg"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">{{ __('ตั้งค่าโปรไฟล์ของคุณ') }}</h2>
        </div>
        <p class="text-gray-500 mb-6">{{ __('กรุณาเลือกบทบาทของคุณก่อนเริ่มใช้งาน') }}</p>

        <form wire:submit="completeSetup" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('เลือกบทบาทของคุณ') }}</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative cursor-pointer">
                        <input wire:model="role" type="radio" value="student" class="peer sr-only">
                        <div
                            class="flex flex-col items-center justify-center py-4 px-4 border-2 rounded-lg transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50 border-gray-200 hover:bg-gray-50">
                            <i
                                class="fas fa-user-graduate text-2xl mb-2 text-gray-400 peer-checked:text-indigo-600"></i>
                            <span class="text-sm font-semibold">{{ __('นักเรียน') }}</span>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input wire:model="role" type="radio" value="teacher" class="peer sr-only">
                        <div
                            class="flex flex-col items-center justify-center py-4 px-4 border-2 rounded-lg transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50 border-gray-200 hover:bg-gray-50">
                            <i
                                class="fas fa-chalkboard-teacher text-2xl mb-2 text-gray-400 peer-checked:text-indigo-600"></i>
                            <span class="text-sm font-semibold">{{ __('ครูผู้สอน') }}</span>
                        </div>
                    </label>
                </div>
                @error('role') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                class="btn-3d btn-3d--indigo w-full flex justify-center items-center py-3 px-4 rounded-lg text-sm font-bold transition-colors">
                <span wire:loading.remove wire:target="completeSetup">{{ __('เริ่มใช้งาน') }}</span>
                <span wire:loading wire:target="completeSetup"><i
                        class="fas fa-spinner fa-spin mr-2"></i>{{ __('กำลังบันทึก...') }}</span>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
            @csrf
            <button type="submit"
                class="text-sm text-gray-500 hover:text-gray-700 font-medium">{{ __('ออกจากระบบ') }}</button>
        </form>
    </div>
</div>