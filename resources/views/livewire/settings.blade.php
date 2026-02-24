@section('page-title', __('Settings'))

<div>
    <div class="max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Settings') }}</h2>

        <!-- Language Settings -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
                    <i class="fas fa-language text-indigo-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Language') }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ __('Choose your preferred language for the interface.') }}
                    </p>

                    <div class="mt-4" x-data="{ open: false }">
                        <label id="language-dropdown-label"
                            class="block text-sm font-medium text-gray-700 mb-2">{{ __('Select Language') }}</label>

                        <div class="relative dropdown-menu" id="language-dropdown-menu">
                            <button type="button" @click="open = !open" aria-haspopup="menu"
                                aria-controls="language-dropdown-menu-list" :aria-expanded="open ? 'true' : 'false'"
                                class="flex w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-language text-gray-500"></i>
                                    <span>{{ $locale === 'th' ? 'ไทย (Thai)' : 'English' }}</span>
                                </span>
                                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                            </button>

                            <div id="language-dropdown-menu-popover" data-popover x-show="open"
                                :aria-hidden="open ? 'false' : 'true'" x-cloak @click.outside="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                                <div role="menu" id="language-dropdown-menu-list"
                                    aria-labelledby="language-dropdown-label" class="outline-none">
                                    <button type="button" role="menuitem" wire:click="setLocale('en')"
                                        @click="open = false"
                                        class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $locale === 'en' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                        <span class="flex items-center gap-2">
                                            <i class="fas fa-language w-4 text-center"></i>
                                            <span>English</span>
                                        </span>
                                        @if($locale === 'en')
                                            <i class="fas fa-check text-xs"></i>
                                        @endif
                                    </button>

                                    <button type="button" role="menuitem" wire:click="setLocale('th')"
                                        @click="open = false"
                                        class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $locale === 'th' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                        <span class="flex items-center gap-2">
                                            <i class="fas fa-language w-4 text-center"></i>
                                            <span>ไทย (Thai)</span>
                                        </span>
                                        @if($locale === 'th')
                                            <i class="fas fa-check text-xs"></i>
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- UI Size Settings -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
                    <i class="fas fa-magnifying-glass-plus text-indigo-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('UI Size') }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ __('Adjust interface zoom size for better readability.') }}
                    </p>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Select UI Size') }}</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach([90, 100, 110, 125] as $scale)
                                <button type="button" wire:click="setUiScale({{ $scale }})"
                                    class="px-3 py-2.5 text-sm rounded-lg border transition-colors {{ $uiScale === $scale ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-medium' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                                    {{ $scale }}%
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Info (read-only summary) -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                    <i class="fas fa-user-circle text-gray-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Account Settings') }}</h3>

                    <div class="mt-4 space-y-4">
                        <div x-data="{ nameLen: {{ strlen(auth()->user()->name) }} }">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-500">{{ __('Name') }}</label>
                                <span class="text-xs"
                                    :class="nameLen >= 50 ? 'text-red-500 font-medium' : 'text-gray-400'">
                                    <span x-text="nameLen"></span>/50
                                </span>
                            </div>
                            <form wire:submit="updateName" class="flex gap-2">
                                <input wire:model="name" @input="nameLen = $event.target.value.length" type="text"
                                    maxlength="50"
                                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="{{ __('Your display name') }}">
                                <button type="submit"
                                    class="btn-3d btn-3d--indigo px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                                    {{ __('Save') }}
                                </button>
                            </form>
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">{{ __('Email') }}</label>
                            <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">{{ __('Role') }}</label>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 capitalize mt-1">
                                {{ __(ucfirst(auth()->user()->role)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>