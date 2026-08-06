<div class="">
    <div class="rounded-2xl border border-[#dedee5] bg-white p-8 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">

        {{-- Header --}}
        <div class="flex items-center space-x-3 mb-2">
            <div class="w-10 h-10 bg-[var(--ll-blue)] rounded-xl flex items-center justify-center shrink-0 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                <x-icon name="lock" class="h-5 w-5 text-white" />
            </div>
            <h2 class="text-2xl font-bold text-[#101114]" style="letter-spacing: -0.5px;">
                @if($step === 1) ลืมรหัสผ่าน
                @elseif($step === 2) ยืนยันรหัส OTP
                @else ตั้งรหัสผ่านใหม่
                @endif
            </h2>
        </div>
        <p class="text-[#9497a9] mb-6">
            @if($step === 1) กรอกอีเมลที่ใช้สมัครสมาชิกเพื่อรับรหัสยืนยัน
            @elseif($step === 2) ระบบส่งรหัส OTP 6 หลักไปที่ <strong class="text-gray-700">{{ $email }}</strong>
            @else กรอกรหัสผ่านใหม่ของคุณ
            @endif
        </p>

        {{-- Step 1: Email --}}
        @if($step === 1)
            <form wire:submit.prevent="submitEmail" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">อีเมล</label>
                    <input wire:model="email" type="email" autocomplete="email"
                        class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="your@email.com">
                    @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                    class="btn-3d btn-3d--blue w-full py-2.5 text-sm font-medium rounded-lg transition-colors">
                    <span wire:loading.remove wire:target="submitEmail">ส่งรหัส OTP</span>
                    <span wire:loading wire:target="submitEmail"><x-icon name="spinner" class="h-4 w-4 mr-1 animate-spin" /> กำลังส่ง...</span>
                </button>

                <p class="text-center">
                    <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        กลับไปหน้าเข้าสู่ระบบ
                    </a>
                </p>
            </form>
        @endif

        {{-- Step 2: OTP --}}
        @if($step === 2)
            <form wire:submit.prevent="verifyOtp" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">รหัส OTP</label>
                    <input wire:model="otp" type="text" maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                        class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-center text-2xl tracking-[0.5em] focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="000000">
                    @error('otp') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                    class="btn-3d btn-3d--blue w-full py-2.5 text-sm font-medium rounded-lg transition-colors">
                    <span wire:loading.remove wire:target="verifyOtp">ยืนยันรหัส</span>
                    <span wire:loading wire:target="verifyOtp"><x-icon name="spinner" class="h-4 w-4 mr-1 animate-spin" /> กำลังตรวจสอบ...</span>
                </button>

                <div class="text-center space-y-2">
                    @if($resendCooldown > 0)
                        <p class="text-sm text-gray-400">ส่งรหัสใหม่ได้ใน {{ $resendCooldown }} วินาที</p>
                    @else
                        <button type="button" wire:click="resendOtp"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            ส่งรหัสใหม่
                        </button>
                    @endif
                    <br>
                    <button type="button" wire:click="$set('step', 1)"
                        class="text-sm text-gray-500 hover:text-gray-700">
                        เปลี่ยนอีเมล
                    </button>
                </div>
            </form>
        @endif

        {{-- Step 3: New Password --}}
        @if($step === 3)
            <form wire:submit.prevent="resetPassword" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">รหัสผ่านใหม่</label>
                    <input wire:model="password" type="password" autocomplete="new-password"
                        class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="อย่างน้อย 8 ตัวอักษร">
                    @error('password') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ยืนยันรหัสผ่านใหม่</label>
                    <input wire:model="password_confirmation" type="password" autocomplete="new-password"
                        class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="ยืนยันรหัสผ่าน">
                    @error('password_confirmation') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                    class="btn-3d btn-3d--blue w-full py-2.5 text-sm font-medium rounded-lg transition-colors">
                    <span wire:loading.remove wire:target="resetPassword">บันทึกรหัสผ่านใหม่</span>
                    <span wire:loading wire:target="resetPassword"><x-icon name="spinner" class="h-4 w-4 mr-1 animate-spin" /> กำลังบันทึก...</span>
                </button>
            </form>
        @endif

    </div>
</div>
