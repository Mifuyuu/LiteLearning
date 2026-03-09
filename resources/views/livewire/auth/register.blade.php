<div class="animate__animated animate__fadeIn">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">

        {{-- Header --}}
        <div class="flex items-center space-x-3 mb-2">
            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                <i class="fas fa-user-plus text-white text-lg"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">{{ __('auth.register.title') }}</h2>
        </div>
        <p class="text-gray-500 mb-6">{{ __('auth.register.description') }}</p>

        @if (!$otpSent)
            {{-- Step 1: Registration Form --}}
            <form wire:submit="register" class="space-y-5">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('auth.register.name') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400 text-sm"></i>
                        </div>
                        <input id="name" type="text" wire:model="name" autocomplete="name"
                            placeholder="{{ __('auth.register.name_placeholder') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('name') border-red-500 @enderror">
                    </div>
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('auth.register.email') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400 text-sm"></i>
                        </div>
                        <input id="email" type="email" wire:model="email" autocomplete="email" placeholder="you@example.com"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('email') border-red-500 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('auth.register.password') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 text-sm"></i>
                        </div>
                        <input id="password" type="password" wire:model="password" autocomplete="new-password"
                            placeholder="{{ __('auth.register.password_placeholder') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('password') border-red-500 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('auth.register.password_confirmation') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 text-sm"></i>
                        </div>
                        <input id="password_confirmation" type="password" wire:model="password_confirmation"
                            autocomplete="new-password" placeholder="{{ __('auth.register.password_confirmation') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('password_confirmation') border-red-500 @enderror">
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="btn-3d btn-3d--indigo w-full flex justify-center items-center py-2.5 px-4 rounded-lg text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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

                <div class="bg-indigo-50 border border-indigo-100 rounded-lg px-4 py-3 text-sm text-indigo-700">
                    <i class="fas fa-envelope mr-1.5"></i>
                    {{ __('auth.register.otp.sent_to') }} <span class="font-semibold">{{ $email }}</span>
                </div>

                {{-- OTP Input --}}
                <div>
                    <label for="otp" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('auth.register.otp.code') }}
                    </label>
                    <input id="otp" type="text" wire:model="otp" inputmode="numeric" pattern="\d{6}" maxlength="6" autofocus
                        autocomplete="one-time-code" placeholder="000000"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-2xl font-bold text-center tracking-[0.5em] focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('otp') border-red-500 @enderror">
                    @error('otp')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="btn-3d btn-3d--indigo w-full flex justify-center items-center py-2.5 px-4 rounded-lg text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
                        <p class="text-sm text-gray-500">
                            {{ __('auth.register.otp.resend_in') }}
                            <span x-text="cooldown" class="font-semibold text-indigo-600"></span>
                            {{ __('auth.register.otp.seconds') }}
                        </p>
                    </template>
                    <template x-if="cooldown <= 0">
                        <button type="button" wire:click="sendOtp"
                            class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                            <i class="fas fa-redo mr-1 text-xs"></i>{{ __('auth.register.otp.resend') }}
                        </button>
                    </template>
                </div>

                {{-- Back --}}
                <div class="text-center">
                    <button type="button" wire:click="$set('otpSent', false)"
                        class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-1 text-xs"></i>{{ __('auth.register.otp.back') }}
                    </button>
                </div>
            </form>
        @endif

        {{-- Footer --}}
        @if (!$otpSent)
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    {{ __('auth.register.already_have_account') }}
                    <a href="{{ route('login') }}" wire:navigate
                        class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                        {{ __('auth.login.title') }}
                    </a>
                </p>
            </div>
        @endif

    </div>
</div>