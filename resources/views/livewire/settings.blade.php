@section('page-title', __('Settings'))

<div>
    <div class="max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Settings') }}</h2>

        <!-- Language Settings -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-language text-indigo-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Language') }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ __('Choose your preferred language for the interface.') }}</p>

                    <div class="mt-4">
                        <label for="locale" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Select Language') }}</label>
                        <select
                            wire:model.live="locale"
                            id="locale"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 border"
                        >
                            <option value="en">🇺🇸 English</option>
                            <option value="th">🇹🇭 ไทย (Thai)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Info (read-only summary) -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-circle text-gray-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Account Settings') }}</h3>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">{{ __('Name') }}</label>
                            <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">{{ __('Email') }}</label>
                            <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">{{ __('Role') }}</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 capitalize mt-1">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
