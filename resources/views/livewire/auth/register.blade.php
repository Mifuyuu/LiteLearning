<div class="animate__animated animate__fadeIn">
    <div class="rounded-2xl border border-[#dedee5] bg-white p-8 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">

        {{-- Header --}}
        <div class="flex items-center space-x-3 mb-2">
            <div class="w-10 h-10 bg-[#7132f5] rounded-[12px] flex items-center justify-center shrink-0 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                <i class="fas fa-user-plus text-white text-lg"></i>
            </div>
            <h2 class="text-2xl font-bold text-[#101114]" style="font-family: 'IBM Plex Sans', sans-serif; letter-spacing: -0.5px;">{{ __('auth.register.title') }}</h2>
        </div>
        <p class="text-[#9497a9] mb-6">{{ __('auth.register.description') }}</p>

        @if (!$otpSent)
            {{-- Step 1: Registration Form --}}
            <form wire:submit="register" class="space-y-5">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-[#686b82] mb-1">
                        {{ __('auth.register.name') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-[#9497a9] text-sm"></i>
                        </div>
                        <input id="name" type="text" wire:model="name" autocomplete="name"
                            placeholder="{{ __('auth.register.name_placeholder') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-[#dedee5] rounded-[10px] text-sm text-[#101114] placeholder-[#9497a9] focus:ring-2 focus:ring-[#7132f5] focus:border-[#7132f5] transition-colors @error('name') border-red-500 @enderror">
                    </div>
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-[#686b82] mb-1">
                        {{ __('auth.register.email') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-[#9497a9] text-sm"></i>
                        </div>
                        <input id="email" type="email" wire:model="email" autocomplete="email" placeholder="you@example.com"
                            class="w-full pl-10 pr-4 py-2.5 border border-[#dedee5] rounded-[10px] text-sm text-[#101114] placeholder-[#9497a9] focus:ring-2 focus:ring-[#7132f5] focus:border-[#7132f5] transition-colors @error('email') border-red-500 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-[#686b82] mb-1">
                        {{ __('auth.register.password') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-[#9497a9] text-sm"></i>
                        </div>
                        <input id="password" type="password" wire:model="password" autocomplete="new-password"
                            placeholder="{{ __('auth.register.password_placeholder') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-[#dedee5] rounded-[10px] text-sm text-[#101114] placeholder-[#9497a9] focus:ring-2 focus:ring-[#7132f5] focus:border-[#7132f5] transition-colors @error('password') border-red-500 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-[#686b82] mb-1">
                        {{ __('auth.register.password_confirmation') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-[#9497a9] text-sm"></i>
                        </div>
                        <input id="password_confirmation" type="password" wire:model="password_confirmation"
                            autocomplete="new-password" placeholder="{{ __('auth.register.password_confirmation') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-[#dedee5] rounded-[10px] text-sm text-[#101114] placeholder-[#9497a9] focus:ring-2 focus:ring-[#7132f5] focus:border-[#7132f5] transition-colors @error('password_confirmation') border-red-500 @enderror">
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role Selection --}}
                <div>
                    <label class="block text-sm font-medium text-[#686b82] mb-2">บทบาทของคุณ</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="role" value="student" class="sr-only peer">
                            <div class="border border-[#dedee5] rounded-[12px] p-3 text-center transition-all peer-checked:border-[#7132f5] peer-checked:bg-[rgba(133,91,251,0.08)] hover:bg-[rgba(133,91,251,0.04)] text-[#9497a9] peer-checked:text-[#7132f5]">
                                <i class="fas fa-user-graduate text-2xl mb-2"></i>
                                <div class="font-medium text-[#101114]">นักเรียน</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="role" value="teacher" class="sr-only peer">
                            <div class="border border-[#dedee5] rounded-[12px] p-3 text-center transition-all peer-checked:border-[#7132f5] peer-checked:bg-[rgba(133,91,251,0.08)] hover:bg-[rgba(133,91,251,0.04)] text-[#9497a9] peer-checked:text-[#7132f5]">
                                <i class="fas fa-chalkboard-teacher text-2xl mb-2"></i>
                                <div class="font-medium text-[#101114]">ครูผู้สอน</div>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full flex justify-center items-center rounded-[12px] bg-[#7132f5] px-4 py-[13px] text-sm font-semibold text-white transition hover:bg-[#5741d8] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#7132f5]">
                    <span wire:loading.remove wire:target="register">
                        <i class="fas fa-paper-plane mr-2"></i>{{ __('auth.register.otp.send') }}
                    </span>
                    <span wire:loading wire:target="register">
                        <i class="fas fa-spinner fa-spin mr-2"></i>{{ __('auth.register.otp.sending') }}
                    </span>
                </button>
            </form>

        @else
            {{-- Step 2: OTP Verification --}}
            <form wire:submit="verifyOtp" class="space-y-5">

                <div class="bg-[rgba(133,91,251,0.08)] border border-[rgba(113,50,245,0.2)] rounded-[12px] px-4 py-3 text-sm text-[#7132f5]">
                    <i class="fas fa-envelope mr-1.5"></i>
                    {{ __('auth.register.otp.sent_to') }} <span class="font-semibold">{{ $email }}</span>
                </div>

                {{-- OTP Input --}}
                <div>
                    <label for="otp" class="block text-sm font-medium text-[#686b82] mb-1">
                        {{ __('auth.register.otp.code') }}
                    </label>
                    <input id="otp" type="text" wire:model="otp" inputmode="numeric" pattern="\d{6}" maxlength="6" autofocus
                        autocomplete="one-time-code" placeholder="000000"
                        class="w-full px-4 py-3 border border-[#dedee5] rounded-[10px] text-2xl font-bold text-center text-[#101114] tracking-[0.5em] focus:ring-2 focus:ring-[#7132f5] focus:border-[#7132f5] transition-colors @error('otp') border-red-500 @enderror">
                    @error('otp')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full flex justify-center items-center rounded-[12px] bg-[#7132f5] px-4 py-[13px] text-sm font-semibold text-white transition hover:bg-[#5741d8] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#7132f5]">
                    <span wire:loading.remove wire:target="verifyOtp">
                        <i class="fas fa-check mr-2"></i>{{ __('auth.register.otp.verify') }}
                    </span>
                    <span wire:loading wire:target="verifyOtp">
                        <i class="fas fa-spinner fa-spin mr-2"></i>{{ __('auth.register.otp.verifying') }}
                    </span>
                </button>

                {{-- Resend OTP --}}
                <div x-data="{ cooldown: $wire.entangle('resendCooldown') }" class="text-center">
                    <template x-if="cooldown > 0">
                        <p class="text-sm text-[#9497a9]">
                            {{ __('auth.register.otp.resend_in') }}
                            <span x-text="cooldown" class="font-semibold text-[#7132f5]"></span>
                            {{ __('auth.register.otp.seconds') }}
                        </p>
                    </template>
                    <template x-if="cooldown <= 0">
                        <button type="button" wire:click="sendOtp"
                            class="text-sm text-[#7132f5] hover:text-[#5741d8] font-semibold transition-colors">
                            <i class="fas fa-redo mr-1 text-xs"></i>{{ __('auth.register.otp.resend') }}
                        </button>
                    </template>
                </div>

                {{-- Back --}}
                <div class="text-center">
                    <button type="button" wire:click="$set('otpSent', false)"
                        class="text-sm text-[#9497a9] hover:text-[#686b82] transition-colors">
                        <i class="fas fa-arrow-left mr-1 text-xs"></i>{{ __('auth.register.otp.back') }}
                    </button>
                </div>
            </form>
        @endif

        {{-- Footer --}}
        @if (!$otpSent)
            <div class="mt-6 text-center">
                <p class="text-sm text-[#686b82]">
                    {{ __('auth.register.already_have_account') }}
                    <a href="{{ route('login') }}" wire:navigate
                        class="font-semibold text-[#7132f5] hover:text-[#5741d8] transition-colors">
                        {{ __('auth.login.title') }}
                    </a>
                </p>
            </div>
        @endif

    </div>
</div>