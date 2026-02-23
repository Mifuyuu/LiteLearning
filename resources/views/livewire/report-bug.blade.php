<div>
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center px-4"
         x-data x-init="$el.querySelector('textarea').focus()">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeModal"></div>

        {{-- Modal --}}
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 z-10 animate__animated animate__fadeInUp animate__faster">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-flag text-indigo-500"></i>
                    {{ __('report.title') }}
                </h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Form --}}
            <form wire:submit="submit" class="space-y-4">

                {{-- Type --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">{{ __('report.type_label') }}</label>
                    <div class="flex gap-2">
                        @foreach(['bug' => ['label' => __('report.type_bug'), 'icon' => 'fa-bug', 'color' => 'red'],
                                  'suggestion' => ['label' => __('report.type_suggestion'), 'icon' => 'fa-lightbulb', 'color' => 'amber'],
                                  'other' => ['label' => __('report.type_other'), 'icon' => 'fa-circle-question', 'color' => 'gray']] as $val => $opt)
                            <button type="button" wire:click="$set('type', '{{ $val }}')"
                                class="flex-1 flex flex-col items-center gap-1 py-2.5 rounded-xl border text-xs font-semibold transition-all cursor-pointer
                                    {{ $type === $val
                                        ? 'border-indigo-400 bg-indigo-50 text-indigo-700'
                                        : 'border-gray-200 text-gray-500 hover:border-gray-300' }}">
                                <i class="fas {{ $opt['icon'] }} text-sm"></i>
                                {{ $opt['label'] }}
                            </button>
                        @endforeach
                    </div>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">{{ __('report.title_label') }}</label>
                    <input type="text" wire:model="title"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition"
                        placeholder="{{ __('report.title_placeholder') }}" maxlength="100">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Message --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">{{ __('report.message_label') }}</label>
                    <textarea wire:model="message" rows="4"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition resize-none"
                        placeholder="{{ __('report.message_placeholder') }}" maxlength="2000"></textarea>
                    @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 pt-1">
                    <button type="button" wire:click="closeModal"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition cursor-pointer">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition cursor-pointer disabled:opacity-60">
                        <span wire:loading.remove>{{ __('report.submit') }}</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-1"></i>{{ __('report.submitting') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
