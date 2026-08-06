<div>
    @if($showModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center px-4" x-data
            x-init="$el.querySelector('textarea').focus()">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeModal"></div>

            {{-- Modal --}}
            <div
                class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 z-10 Up animate__faster">

                {{-- Header --}}
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <x-icon name="flag" class="h-5 w-5 text-blue-500" />
                        {{ 'รายงานปัญหา / เสนอแนะ' }}
                    </h2>
                    <button wire:click="closeModal"
                        class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                        <x-icon name="x-mark" class="h-5 w-5" />
                    </button>
                </div>

                {{-- Form --}}
                <form wire:submit.prevent="submit" class="space-y-4">

                    {{-- Type --}}
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">{{ 'ประเภท' }}</label>
                        <div class="flex gap-2">
                            @foreach([
                                                'bug' => ['label' => 'บั๊ก (Bug)', 'icon' => 'bug', 'color' => 'red'],
                                                'suggestion' => ['label' => 'ข้อเสนอแนะ', 'icon' => 'lightbulb', 'color' => 'amber'],
                                                'other' => ['label' => 'อื่นๆ', 'icon' => 'question-mark-circle', 'color' => 'gray']
                                            ] as $val => $opt)
                                                <button type="button" wire:click="$set('type', '{{ $val }}')"
                                                    class="flex-1 flex flex-col items-center gap-1 py-2.5 rounded-xl border text-xs font-semibold transition-all cursor-pointer
                                                        {{ $type === $val
                                ? 'border-blue-400 bg-blue-50 text-blue-700'
                                : 'border-gray-200 text-gray-500 hover:border-gray-300' }}">
                                                <x-icon :name="$opt['icon']" class="h-4 w-4" />
                                                    {{ $opt['label'] }}
                                                </button>
                            @endforeach
                        </div>
                        @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Title
                            --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">{{ 'หัวข้อ' }}</label>
                        <input type="text" wire:model="title"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition"
                            placeholder="{{ 'เกิดปัญหาอะไรขึ้น?' }}" maxlength="100">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Messa
          g                 e --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">{{ 'รายละเอียด' }}</label>
                        <textarea wire:model="message" rows="4"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition resize-none"
                            placeholder="{{ 'อธิบายรายละเอียดของปัญหา...' }}" maxlength="2000"></textarea>
                        @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2 pt-1">
                        <button type="button" wire:click="closeModal"
                            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition cursor-pointer">
                            {{ 'ยกเลิก' }}
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="btn-3d btn-3d--blue flex-1 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer disabled:opacity-60">
                            <span wire:loading.remove>{{ 'ส่งรายงาน' }}</span>
                            <span wire:loading><x-icon name="spinner" class="h-4 w-4 mr-1 animate-spin" />{{ 'กำลังส่ง...' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
