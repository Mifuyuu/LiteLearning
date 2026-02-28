<div class="animate__animated animate__fadeIn">
    {{-- Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-2">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-amber-100 text-amber-600">
                <i class="fas fa-envelope text-lg"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">{{ __('ยืนยันอีเมล') }}</h2>
        </div>

        <p class="text-gray-500 mb-6 leading-relaxed">
            {{ __('เราได้ส่งลิงก์ยืนยันไปยัง') }}
            <span class="font-semibold text-gray-800">{{ auth()->user()->email }}</span>
            {{ __('แล้ว') }}<br>
            {{ __('กรุณาตรวจสอบกล่องจดหมาย (และโฟลเดอร์ Spam) แล้วคลิกลิงก์เพื่อยืนยัน') }}
        </p>

        {{-- Success alert --}}
        @if ($status === 'sent')
            <div
                class="mb-5 flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 animate__animated animate__fadeIn">
                <i class="fas fa-check-circle"></i>
                <span>{{ __('ส่งลิงก์ยืนยันใหม่เรียบร้อยแล้ว!') }}</span>
            </div>
        @endif

        {{-- Resend button --}}
        <div x-data="{
            cooldown: 0,
            interval: null,
            start() {
                this.cooldown = 60;
                this.interval = setInterval(() => {
                    this.cooldown--;
                    if (this.cooldown <= 0) {
                        clearInterval(this.interval);
                        this.cooldown = 0;
                    }
                }, 1000);
            },
            send() {
                if (this.cooldown > 0) return;
                $wire.resend();
                this.start();
            }
        }">
            <button @click="send()" :disabled="cooldown > 0"
                class="btn-3d btn-3d--indigo w-full justify-center items-center gap-2 px-4 py-2 text-sm font-bold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <template x-if="cooldown > 0">
                    <span>
                        <i class="fas fa-clock mr-2"></i>
                        {{ __('ส่งอีกครั้งใน') }} <span x-text="cooldown"></span> {{ __('วินาที') }}
                    </span>
                </template>
                <template x-if="cooldown <= 0">
                    <span wire:loading.remove wire:target="resend">
                        <i class="fas fa-paper-plane mr-2"></i>
                        {{ __('ส่งลิงก์ยืนยันอีกครั้ง') }}
                    </span>
                </template>
                <span wire:loading wire:target="resend">
                    <i class="fas fa-circle-notch fa-spin mr-2"></i>
                    {{ __('กำลังส่ง...') }}
                </span>
            </button>
        </div>

        {{-- Divider --}}
        <div class="relative py-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
        </div>

        {{-- Logout --}}
        <div class="text-center">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-sign-out-alt mr-1"></i>
                    {{ __('ออกจากระบบ') }}
                </button>
            </form>
        </div>

    </div>
</div>