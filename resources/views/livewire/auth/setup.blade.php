<div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Complete your profile setup') }}</h2>
        <p class="text-gray-500 mb-6">{{ __('Please provide your basic information before using the platform.') }}</p>

        <form wire:submit="completeSetup" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Role') }}</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input wire:model="role" type="radio" value="student" class="peer sr-only">
                        <div
                            class="flex items-center justify-center py-3 px-4 border-2 rounded-lg transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50 border-gray-200 hover:bg-gray-50">
                            <i class="fas fa-user-graduate mr-2"></i>
                            <span class="text-sm font-medium">{{ __('Student') }}</span>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input wire:model="role" type="radio" value="teacher" class="peer sr-only">
                        <div
                            class="flex items-center justify-center py-3 px-4 border-2 rounded-lg transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50 border-gray-200 hover:bg-gray-50">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>
                            <span class="text-sm font-medium">{{ __('Teacher') }}</span>
                        </div>
                    </label>
                </div>
                @error('role') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="school" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Institution') }}</label>
                <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                    <input wire:model.live.debounce.250ms="school" id="school" type="text" @focus="open = true"
                        @input="open = true"
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="{{ __('Type your university name') }}" autocomplete="off">

                    @if(count($this->schoolSuggestions) > 0)
                        <div x-show="open" x-cloak @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20 max-h-64 overflow-y-auto">
                            @foreach($this->schoolSuggestions as $suggestion)
                                <button type="button" wire:click="$set('school', @js($suggestion))" @click="open = false"
                                    class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $school === $suggestion ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                    <span>{{ $suggestion }}</span>
                                    @if($school === $suggestion)
                                        <i class="fas fa-check text-xs"></i>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    {{ __('You can type to search or enter your own institution name.') }}</p>
                @error('school') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label id="study-year-dropdown-label"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('Study Year') }}</label>
                <div class="relative dropdown-menu" id="study-year-dropdown" x-data="{ open: false }">
                    <button type="button" @click="open = !open" aria-haspopup="menu"
                        aria-controls="study-year-dropdown-menu-list" :aria-expanded="open ? 'true' : 'false'"
                        class="flex w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span>{{ $study_year !== '' ? __($study_year) : __('Select study year') }}</span>
                        <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                    </button>

                    <div id="study-year-dropdown-menu-popover" data-popover x-show="open"
                        :aria-hidden="open ? 'false' : 'true'" x-cloak @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20 max-h-64 overflow-y-auto">
                        <div role="menu" id="study-year-dropdown-menu-list" aria-labelledby="study-year-dropdown-label"
                            class="outline-none">
                            @foreach($studyYearOptions as $option)
                                <button type="button" role="menuitem"
                                    @click="$wire.set('study_year', @js($option)); open = false"
                                    class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ $study_year === $option ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700' }}">
                                    <span>{{ __($option) }}</span>
                                    @if($study_year === $option)
                                        <i class="fas fa-check text-xs"></i>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                @error('study_year') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror

                @if($study_year === 'Other')
                    <input wire:model="study_year_other" type="text"
                        class="mt-3 block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="{{ __('Enter your study year') }}">
                    @error('study_year_other') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                @endif
            </div>

            <div>
                <label for="birth_date"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('Date of Birth') }}</label>
                <input wire:model="birth_date" type="date" id="birth_date"
                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('birth_date') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-start">
                <input wire:model="accept_tos" type="checkbox"
                    class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-600">
                    {{ __('I agree to the') }}
                    <a href="{{ route('tos') }}"
                        class="text-indigo-600 hover:text-indigo-500 font-medium">{{ __('Terms of Service') }}</a>
                </span>
            </label>
            @error('accept_tos') <p class="-mt-3 text-sm text-red-500">{{ $message }}</p> @enderror

            <button type="submit"
                class="btn-3d btn-3d--indigo w-full flex justify-center items-center py-2.5 px-4 rounded-lg text-sm font-semibold transition-colors">
                <span wire:loading.remove wire:target="completeSetup">{{ __('Complete Setup') }}</span>
                <span wire:loading wire:target="completeSetup"><i
                        class="fas fa-spinner fa-spin mr-2"></i>{{ __('Saving...') }}</span>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">{{ __('Sign out') }}</button>
        </form>
    </div>
</div>