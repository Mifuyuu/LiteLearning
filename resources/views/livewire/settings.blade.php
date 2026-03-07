@section('page-title', __('Settings'))

<div class="animate__animated animate__fadeIn">
    <div class="max-w-2xl mx-auto space-y-4">

        {{-- Page Header --}}
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white">{{ __('Settings') }}</h2>
            <p class="text-sm text-blue-300/60 mt-1">{{ __('Manage your account preferences') }}</p>
        </div>

        {{-- Flash Message --}}
        @if (session()->has('message'))
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-emerald-300 text-sm">
                <i class="fas fa-circle-check"></i>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        {{-- UI Size Settings --}}
        <div class="rounded-2xl border border-white/[0.08] bg-white/[0.04] backdrop-blur-sm p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(34,211,238,0.15)); border: 1px solid rgba(99,102,241,0.35);">
                    <i class="fas fa-magnifying-glass-plus text-indigo-400"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-white">{{ __('UI Size') }}</h3>
                    <p class="text-sm text-blue-300/60 mt-0.5">{{ __('Adjust interface zoom size for better readability.') }}</p>

                    <div class="mt-4">
                        <label class="block text-xs font-medium text-blue-300/50 uppercase tracking-wider mb-3">{{ __('Select UI Size') }}</label>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach([90, 100, 110, 125] as $scale)
                                <button type="button" wire:click="setUiScale({{ $scale }})"
                                    class="group relative px-3 py-3 text-sm rounded-xl border transition-all duration-200
                                        {{ $uiScale === $scale
                                            ? 'border-indigo-400/60 bg-indigo-500/20 text-indigo-300 font-semibold shadow-[0_0_12px_rgba(99,102,241,0.2)]'
                                            : 'border-white/10 bg-white/[0.03] text-blue-200/70 hover:border-indigo-400/30 hover:bg-white/[0.06] hover:text-white' }}">
                                    @if($uiScale === $scale)
                                        <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                    @endif
                                    {{ $scale }}%
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Account Settings --}}
        <div class="rounded-2xl border border-white/[0.08] bg-white/[0.04] backdrop-blur-sm p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(34,211,238,0.15)); border: 1px solid rgba(99,102,241,0.35);">
                    <i class="fas fa-user-circle text-indigo-400"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-white">{{ __('Account Settings') }}</h3>

                    <div class="mt-5 space-y-5">

                        {{-- Name --}}
                        <div x-data="{ nameLen: {{ strlen(auth()->user()->name) }} }">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-medium text-blue-300/50 uppercase tracking-wider">{{ __('Name') }}</label>
                                <span class="text-xs transition-colors"
                                    :class="nameLen >= 50 ? 'text-red-400 font-medium' : 'text-blue-300/40'">
                                    <span x-text="nameLen"></span>/50
                                </span>
                            </div>
                            <form wire:submit="updateName" class="flex gap-2">
                                <input wire:model="name"
                                    @input="nameLen = $event.target.value.length"
                                    type="text"
                                    maxlength="50"
                                    class="flex-1 rounded-xl border border-white/10 bg-white/[0.06] px-3.5 py-2.5 text-sm text-white placeholder-blue-300/30
                                           focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all"
                                    placeholder="{{ __('Your display name') }}">
                                <button type="submit" class="btn-3d btn-3d--indigo px-4 py-2.5 text-sm font-medium rounded-xl whitespace-nowrap">
                                    {{ __('Save') }}
                                </button>
                            </form>
                            @error('name')
                                <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1.5">
                                    <i class="fas fa-circle-exclamation text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="border-t border-white/[0.06]"></div>

                        {{-- Email (read-only) --}}
                        <div>
                            <label class="block text-xs font-medium text-blue-300/50 uppercase tracking-wider mb-2">{{ __('Email') }}</label>
                            <div class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-white/[0.06] bg-white/[0.03] text-sm text-blue-200/70">
                                <i class="fas fa-envelope text-xs text-blue-300/40"></i>
                                <span>{{ auth()->user()->email }}</span>
                            </div>
                        </div>

                        {{-- Role --}}
                        <div>
                            <label class="block text-xs font-medium text-blue-300/50 uppercase tracking-wider mb-2">{{ __('Role') }}</label>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium
                                         bg-indigo-500/15 text-indigo-300 border border-indigo-400/25">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                {{ __(ucfirst(auth()->user()->role)) }}
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
