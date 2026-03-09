@section('page-title', __('Settings'))

<div class="animate__animated animate__fadeIn">
    <div class="max-w-2xl mx-auto space-y-4">

        {{-- Page Header --}}
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Settings') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage your account preferences') }}</p>
        </div>



        {{-- Account Settings --}}
        <div class="card-3d card-3d--purple p-6">
            <div class="flex items-start gap-4">
                <div
                    class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-indigo-100 border border-indigo-200">
                    <i class="fas fa-user-circle text-indigo-500"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-white">{{ __('Account Settings') }}</h3>

                    <div class="mt-5 space-y-5">

                        {{-- Name --}}
                        <div x-data="{ nameLen: {{ strlen(auth()->user()->name) }} }">
                            <div class="flex items-center justify-between mb-2">
                                <label
                                    class="block text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Name') }}</label>
                                <span class="text-xs transition-colors"
                                    :class="nameLen >= 50 ? 'text-red-500 font-medium' : 'text-gray-400'">
                                    <span x-text="nameLen"></span>/50
                                </span>
                            </div>
                            <form wire:submit="updateName" class="flex gap-2">
                                <input wire:model="name" @input="nameLen = $event.target.value.length" type="text"
                                    maxlength="50"
                                    class="flex-1 rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 placeholder-gray-300
                                           focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition-all"
                                    placeholder="{{ __('Your display name') }}">
                                <button type="submit"
                                    class="btn-3d btn-3d--indigo px-4 py-2.5 text-sm font-medium rounded-xl whitespace-nowrap">
                                    {{ __('Save') }}
                                </button>
                            </form>
                            @error('name')
                                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                    <i class="fas fa-circle-exclamation text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="border-t border-gray-100"></div>

                        {{-- Email (read-only) --}}
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">{{ __('Email') }}</label>
                            <div
                                class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-500">
                                <i class="fas fa-envelope text-xs text-gray-400"></i>
                                <span>{{ auth()->user()->email }}</span>
                            </div>
                        </div>

                        {{-- Role --}}
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">{{ __('Role') }}</label>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium
                                         bg-indigo-100 text-indigo-700 border border-indigo-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                {{ __(ucfirst(auth()->user()->role)) }}
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>