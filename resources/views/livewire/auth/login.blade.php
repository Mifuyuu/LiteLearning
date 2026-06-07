<div class="animate__animated animate__fadeIn">
    <div class="rounded-2xl border border-[#dedee5] bg-white p-8 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        <div class="flex items-center space-x-3 mb-2">
            <div class="w-10 h-10 bg-[#7132f5] rounded-[12px] flex items-center justify-center shrink-0 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                <i class="fas fa-graduation-cap text-white text-lg"></i>
            </div>
            <h2 class="text-2xl font-bold text-[#101114]" style="font-family: 'IBM Plex Sans', sans-serif; letter-spacing: -0.5px;">{{ __('Welcome back') }}</h2>
        </div>
        <p class="text-[#9497a9] mb-6">{{ __('Sign in to your account to continue') }}</p>

        <form wire:submit="login" class="space-y-5">
            {{-- Email --}}
            <div>
                <label for="email"
                    class="block text-sm font-medium text-[#686b82] mb-1">{{ __('Email address') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-[#9497a9]"></i>
                    </div>
                    <input wire:model="email" type="email" id="email"
                        class="block w-full pl-10 pr-3 py-2.5 border border-[#dedee5] rounded-[10px] text-sm text-[#101114] placeholder-[#9497a9] focus:ring-1 focus:ring-[#7132f5] focus:border-[#7132f5] transition-colors @error('email') border-red-500 @enderror"
                        placeholder="you@example.com">
                </div>
                @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-[#686b82] mb-1">{{ __('Password') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-[#9497a9]"></i>
                    </div>
                    <input wire:model="password" type="password" id="password"
                        class="block w-full pl-10 pr-3 py-2.5 border border-[#dedee5] rounded-[10px] text-sm text-[#101114] placeholder-[#9497a9] focus:ring-1 focus:ring-[#7132f5] focus:border-[#7132f5] transition-colors @error('password') border-red-500 @enderror"
                        placeholder="••••••••">
                </div>
                @error('password') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Remember me --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input wire:model="remember" type="checkbox"
                        class="rounded border-[#dedee5] text-[#7132f5] focus:ring-[#7132f5]">
                    <span class="ml-2 text-sm text-[#686b82]">{{ __('Remember me') }}</span>
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full flex justify-center items-center rounded-[12px] bg-[#7132f5] px-4 py-[13px] text-sm font-semibold text-white transition hover:bg-[#5741d8] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#7132f5]">
                <span wire:loading.remove wire:target="login"><i
                        class="fas fa-arrow-right-to-bracket mr-2"></i>{{ __('Sign in') }}</span>
                <span wire:loading wire:target="login"><i class="fas fa-spinner fa-spin mr-2"></i>
                    {{ __('Signing in...') }}</span>
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-[#686b82]">
                {{ __('Don\'t have an account?') }}
                <a href="{{ route('register') }}"
                    class="font-semibold text-[#7132f5] hover:text-[#5741d8] transition-colors">{{ __('Create one') }}</a>
            </p>
        </div>
    </div>
</div>