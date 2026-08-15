<div class="">
    <div class="rounded-lg border border-[#dedee5] bg-white p-8 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        <div class="mb-2">
            <h2 class="text-2xl font-bold text-[#101114] text-center" style="letter-spacing: -0.5px;">เข้าสู่ระบบ</h2>
        </div>
        {{-- <p class="text-[#9497a9] mb-6">กรอกข้อมูลเพื่อเข้าสู่ระบบ</p> --}}

        <form wire:submit.prevent="login" class="space-y-5">
            {{-- Email --}}
            <div>
                <label for="email"
                    class="block text-sm font-medium text-[#686b82] mb-1">อีเมล</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-icon name="envelope" class="h-4 w-4 text-[#9497a9]" />
                    </div>
                    <input wire:model="email" type="email" id="email"
                        class="block w-full pl-10 pr-3 py-2.5 border border-[#dedee5] rounded-[10px] text-sm text-[#101114] placeholder-[#9497a9] focus:ring-1 focus:ring-[var(--ll-blue)] focus:border-[var(--ll-blue)] transition-colors @error('email') border-red-500 @enderror"
                        placeholder="you@example.com">
                </div>
                @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-[#686b82] mb-1">รหัสผ่าน</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-icon name="lock" class="h-4 w-4 text-[#9497a9]" />
                    </div>
                    <input wire:model="password" type="password" id="password"
                        class="block w-full pl-10 pr-3 py-2.5 border border-[#dedee5] rounded-[10px] text-sm text-[#101114] placeholder-[#9497a9] focus:ring-1 focus:ring-[var(--ll-blue)] focus:border-[var(--ll-blue)] transition-colors @error('password') border-red-500 @enderror"
                        placeholder="••••••••">
                </div>
                @error('password') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Remember me + Forgot password --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                <input wire:model="remember" type="checkbox" class="checkbox checkbox-sm">
                    <span class="ml-2 text-sm text-[#686b82]">จดจำฉัน</span>
                </label>
                <a href="{{ route('password.request') }}"
                    class="text-sm text-[var(--ll-blue)] hover:text-[var(--ll-blue-dark)] font-medium transition-colors">
                    ลืมรหัสผ่าน?
                </a>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="btn-3d btn-3d--blue w-full flex justify-center items-center rounded-[12px] px-4 py-[13px] text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--ll-blue)]">
                <span wire:loading.remove wire:target="login"><x-icon name="arrow-right-on-rectangle" class="h-4 w-4 mr-2" />เข้าสู่ระบบ</span>
                <span wire:loading wire:target="login"><x-icon name="spinner" class="h-4 w-4 mr-2 animate-spin" />
                    กำลังเข้าสู่ระบบ...</span>
            </button>
        </form>

        <div class="mt-6 flex items-center justify-between">
            <p class="text-sm text-[#686b82]">
                ยังไม่มีบัญชี?
                <a href="{{ route('register') }}"
                    class="font-semibold text-[var(--ll-blue)] hover:text-[var(--ll-blue-dark)] hover:underline transition-colors">สร้างบัญชี</a>
            </p>
            <a href="{{ url('/') }}"
                class="inline-flex items-center gap-1 text-sm text-[#9497a9] hover:text-[var(--ll-blue)] hover:underline transition-colors">
                <x-icon name="arrow-left" class="h-4 w-4" />กลับหน้าหลัก
            </a>
        </div>
    </div>
</div>