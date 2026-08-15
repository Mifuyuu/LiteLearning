<div>
    {{-- Folder tabs: role selector --}}
    <div class="relative z-10 -mb-px flex items-end gap-1.5 pl-5">
        <button type="button" wire:click="$set('role', 'student')"
            @class([
                'flex items-center gap-2 rounded-t-xl border px-5 pt-2.5 pb-3 text-sm transition-colors',
                $this->role !== 'teacher'
                    ? 'border-[#dedee5] border-b-0 bg-white font-semibold text-[#101114]'
                    : 'border-transparent bg-[#e8eaf0] font-medium text-[#686b82] hover:bg-[#dfe2ea]',
            ])>
            <x-icon name="user" class="h-4 w-4" />นักสำรวจ
        </button>
        <button type="button" wire:click="$set('role', 'teacher')"
            @class([
                'flex items-center gap-2 rounded-t-xl border px-5 pt-2.5 pb-3 text-sm transition-colors',
                $this->role === 'teacher'
                    ? 'border-[#dedee5] border-b-0 bg-white font-semibold text-[#101114]'
                    : 'border-transparent bg-[#e8eaf0] font-medium text-[#686b82] hover:bg-[#dfe2ea]',
            ])>
            <x-icon name="academic-cap" class="h-4 w-4" />ผู้สร้างโลก
        </button>
    </div>

    <div class="rounded-lg border border-[#dedee5] bg-white p-8 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">

        {{-- Header --}}
        <div class="mb-2">
            <h2 class="text-2xl font-bold text-[#101114] text-center" style="letter-spacing: -0.5px;">สมัครสมาชิก</h2>
        </div>
        <p class="text-[#9497a9] mb-6">
            @if ($this->isTeacher)
                สร้างบัญชี<span class="font-semibold text-[#101114] underline">ผู้สอน</span>เพื่อเริ่มต้นสร้างห้องเรียนและสื่อการสอน
            @else
                สร้างบัญชี<span class="font-semibold text-[#101114] underline">ผู้เรียน</span>เพื่อเข้าร่วมห้องเรียนและสะสมเหรียญรางวัล
            @endif
        </p>

        @if (!$otpSent)
            {{-- Step 1: Registration Form --}}
            <form wire:submit.prevent="register" class="space-y-5">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-[#686b82] mb-1">
                        ชื่อ-นามสกุล
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="user" class="h-4 w-4 text-[#9497a9]" />
                        </div>
                        <input id="name" type="text" wire:model="name" autocomplete="name"
                            placeholder="พิมพ์ชื่อ-นามสกุลของคุณ"
                            class="w-full pl-10 pr-4 py-2.5 border border-[#dedee5] rounded-[10px] text-sm text-[#101114] placeholder-[#9497a9] focus:ring-1 focus:ring-[var(--ll-blue)] focus:border-[var(--ll-blue)] transition-colors @error('name') border-red-500 @enderror">
                    </div>
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-[#686b82] mb-1">
                        อีเมล
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="envelope" class="h-4 w-4 text-[#9497a9]" />
                        </div>
                        <input id="email" type="email" wire:model="email" autocomplete="email" placeholder="you@example.com"
                            class="w-full pl-10 pr-4 py-2.5 border border-[#dedee5] rounded-[10px] text-sm text-[#101114] placeholder-[#9497a9] focus:ring-1 focus:ring-[var(--ll-blue)] focus:border-[var(--ll-blue)] transition-colors @error('email') border-red-500 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-[#686b82] mb-1">
                        รหัสผ่าน
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="lock" class="h-4 w-4 text-[#9497a9]" />
                        </div>
                        <input id="password" type="password" wire:model="password" autocomplete="new-password"
                            placeholder="อย่างน้อย 8 ตัวอักษร"
                            class="w-full pl-10 pr-4 py-2.5 border border-[#dedee5] rounded-[10px] text-sm text-[#101114] placeholder-[#9497a9] focus:ring-1 focus:ring-[var(--ll-blue)] focus:border-[var(--ll-blue)] transition-colors @error('password') border-red-500 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-[#686b82] mb-1">
                        ยืนยันรหัสผ่าน
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="lock" class="h-4 w-4 text-[#9497a9]" />
                        </div>
                        <input id="password_confirmation" type="password" wire:model="password_confirmation"
                            autocomplete="new-password" placeholder="ยืนยันรหัสผ่าน"
                            class="w-full pl-10 pr-4 py-2.5 border border-[#dedee5] rounded-[10px] text-sm text-[#101114] placeholder-[#9497a9] focus:ring-1 focus:ring-[var(--ll-blue)] focus:border-[var(--ll-blue)] transition-colors @error('password_confirmation') border-red-500 @enderror">
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="btn-3d btn-3d--blue w-full flex justify-center items-center rounded-xl px-4 py-[13px] text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--ll-blue)]">
                    <span wire:loading.remove wire:target="register">
                        <x-icon name="paper-airplane" class="h-4 w-4 mr-2" />สมัครสมาชิก
                    </span>
                    <span wire:loading wire:target="register">
                        <x-icon name="spinner" class="h-4 w-4 mr-2 animate-spin" />กำลังส่ง...
                    </span>
                </button>
            </form>

        @else
            {{-- Step 2: OTP Verification --}}
            <form wire:submit.prevent="verifyOtp" class="space-y-5">

                <div class="bg-[rgba(37,99,235,0.08)] border border-[rgba(37,99,235,0.2)] rounded-[12px] px-4 py-3 text-sm text-[var(--ll-blue)]">
                    <x-icon name="envelope" class="h-4 w-4 mr-1.5" />
                    ส่งรหัส OTP ไปที่ <span class="font-semibold">{{ $email }}</span>
                </div>

                {{-- OTP Input --}}
                <div>
                    <label for="otp" class="block text-sm font-medium text-[#686b82] mb-1">
                        รหัส OTP (6 หลัก)
                    </label>
                    <input id="otp" type="text" wire:model="otp" inputmode="numeric" pattern="\d{6}" maxlength="6" autofocus
                        autocomplete="one-time-code" placeholder="000000"
                        class="w-full px-4 py-3 border border-[#dedee5] rounded-[10px] text-2xl font-bold text-center text-[#101114] tracking-[0.5em] focus:ring-1 focus:ring-[var(--ll-blue)] focus:border-[var(--ll-blue)] transition-colors @error('otp') border-red-500 @enderror">
                    @error('otp')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="btn-3d btn-3d--blue w-full flex justify-center items-center rounded-[12px] px-4 py-[13px] text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--ll-blue)]">
                    <span wire:loading.remove wire:target="verifyOtp">
                        <x-icon name="check" class="h-4 w-4 mr-2" />ยืนยันและสมัครสมาชิก
                    </span>
                    <span wire:loading wire:target="verifyOtp">
                        <x-icon name="spinner" class="h-4 w-4 mr-2 animate-spin" />กำลังตรวจสอบ...
                    </span>
                </button>

                {{-- Resend OTP --}}
                <div x-data="{ cooldown: $wire.entangle('resendCooldown') }" class="text-center">
                    <template x-if="cooldown > 0">
                        <p class="text-sm text-[#9497a9]">
                            ส่งรหัสอีกครั้งได้ใน
                            <span x-text="cooldown" class="font-semibold text-[var(--ll-blue)]"></span>
                            วินาที
                        </p>
                    </template>
                    <template x-if="cooldown <= 0">
                        <button type="button" wire:click="sendOtp"
                            class="text-sm text-[var(--ll-blue)] hover:text-[var(--ll-blue-dark)] font-semibold transition-colors">
                            <x-icon name="arrow-path" class="h-4 w-4 mr-1" />ส่งรหัสอีกครั้ง
                        </button>
                    </template>
                </div>

                {{-- Back --}}
                <div class="text-center">
                    <button type="button" wire:click="$set('otpSent', false)"
                        class="text-sm text-[#9497a9] hover:text-[#686b82] transition-colors">
                        <x-icon name="arrow-left" class="h-4 w-4 mr-1" />แก้ไขข้อมูล
                    </button>
                </div>
            </form>
        @endif

        {{-- Footer --}}
        @if (!$otpSent)
            <div class="mt-6 flex items-center justify-between">
                <p class="text-sm text-[#686b82]">
                    มีบัญชีอยู่แล้ว?
                    <a href="{{ route('login') }}" wire:navigate
                        class="font-semibold text-[var(--ll-blue)] hover:text-[var(--ll-blue-dark)] hover:underline transition-colors">
                        เข้าสู่ระบบ
                    </a>
                </p>
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gap-1 text-sm text-[#9497a9] hover:text-[var(--ll-blue)] hover:underline transition-colors">
                    <x-icon name="arrow-left" class="h-4 w-4" />กลับหน้าหลัก
                </a>
            </div>
        @endif

    </div>
</div>
