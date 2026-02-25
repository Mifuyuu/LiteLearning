<div class="animate__animated animate__fadeIn">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex items-center space-x-3 mb-2">
            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                <i class="fas fa-graduation-cap text-white text-lg"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Create account') }}</h2>
        </div>
        <p class="text-gray-500 mb-6">{{ __('Join LiteLearning to get started') }}</p>

        <form wire:submit="register" class="space-y-5">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Full name') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input wire:model="name" type="text" id="name"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="John Doe">
                </div>
                @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email address') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input wire:model="email" type="email" id="email"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="you@example.com">
                </div>
                @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password') }}</label>
                <input wire:model="password" type="password" id="password"
                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="••••••••">
                @error('password') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('Confirm password') }}</label>
                <input wire:model="password_confirmation" type="password" id="password_confirmation"
                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="••••••••">
            </div>

            <!-- Submit -->
            <button type="submit"
                class="btn-3d btn-3d--indigo w-full flex justify-center items-center py-2.5 px-4 rounded-lg text-sm font-semibold transition-colors">
                <span wire:loading.remove wire:target="register">{{ __('Create Account') }}</span>
                <span wire:loading wire:target="register"><i
                        class="fas fa-spinner fa-spin mr-2"></i>{{ __('Creating...') }}</span>
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                {{ __('Already have an account?') }}
                <a href="{{ route('login') }}"
                    class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Sign in') }}</a>
            </p>
        </div>
    </div>
</div>