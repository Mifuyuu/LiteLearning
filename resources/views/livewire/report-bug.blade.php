<div>
    @if($showModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center px-4" x-data
            x-init="$el.querySelector('textarea')?.focus()">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeModal"></div>

            {{-- Modal --}}
            <div
                class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 z-10 Up animate__faster">

                {{-- Header --}}
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <x-icon name="flag" class="h-5 w-5 text-blue-500" />
                        {{ 'รายงานปัญหา / เสนอแนะ' }}
                    </h2>
                    <button wire:click="closeModal"
                        class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                        <x-icon name="x-mark" class="h-5 w-5" />
                    </button>
                </div>

                {{-- Tabs --}}
                <div class="flex gap-2 mb-5">
                    <button type="button" wire:click="$set('view', 'form')"
                        class="flex-1 py-2 rounded-lg text-xs font-bold transition-colors cursor-pointer
                            {{ $view === 'form' ? 'bg-blue-50 text-blue-700' : 'text-gray-400 hover:bg-gray-50' }}">
                        {{ 'แจ้งปัญหาใหม่' }}
                    </button>
                    <button type="button" wire:click="$set('view', 'history')"
                        class="flex-1 py-2 rounded-lg text-xs font-bold transition-colors cursor-pointer
                            {{ $view === 'history' ? 'bg-blue-50 text-blue-700' : 'text-gray-400 hover:bg-gray-50' }}">
                        {{ 'ประวัติของฉัน' }}
                    </button>
                </div>

                @if($view === 'history')
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($reports as $report)
                            @php
                                $typeColors = [
                                    'bug' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'bug'],
                                    'suggestion' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'lightbulb'],
                                    'other' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'question-mark-circle'],
                                ];
                                $tc = $typeColors[$report->type] ?? $typeColors['other'];
                            @endphp
                            <div class="border border-gray-200 rounded-xl p-3">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-1 rounded {{ $tc['bg'] }} {{ $tc['text'] }}">
                                        <x-icon :name="$tc['icon']" class="h-3 w-3" />
                                        @if($report->type === 'bug') บั๊ก @elseif($report->type === 'suggestion') ข้อเสนอแนะ @else อื่นๆ @endif
                                    </span>
                                    @if($report->status === 'resolved')
                                        <span class="text-[10px] font-bold text-green-600 bg-green-100 px-1.5 py-0.5 rounded uppercase">แก้ไขแล้ว</span>
                                    @endif
                                    <span class="text-[10px] text-gray-400 ml-auto">{{ $report->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-800 mt-1.5 break-all">{{ $report->title }}</p>
                                <p class="text-xs text-gray-500 mt-0.5 break-all">{{ $report->message }}</p>
                                @if($report->admin_reply)
                                    <div class="mt-2 rounded-lg bg-blue-50 border border-blue-100 px-3 py-2">
                                        <p class="text-[10px] font-bold text-blue-700 uppercase tracking-wide">การตอบกลับของแอดมิน · {{ $report->replied_at->diffForHumans() }}</p>
                                        <p class="text-sm text-blue-900 mt-0.5 break-all">{{ $report->admin_reply }}</p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-8">{{ 'คุณยังไม่เคยแจ้งปัญหา' }}</p>
                        @endforelse
                    </div>
                @endif

                {{-- Form --}}
                @if($view === 'form')
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
                                                    class="flex-1 flex flex-col items-center gap-1 py-2.5 rounded-xl border-2 text-xs font-semibold transition-all cursor-pointer
                                                        @error('type') border-red-300 @else
                                                        {{ $type === $val
                                ? 'border-blue-400 bg-blue-50 text-blue-700'
                                : 'border-gray-200 text-gray-500 hover:border-gray-300' }} @enderror">
                                                <x-icon :name="$opt['icon']" class="h-4 w-4" />
                                                    {{ $opt['label'] }}
                                                </button>
                            @endforeach
                        </div>
                        <div class="min-h-5 mt-1">@error('type') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror</div>
                    </div>

                    {{-- Title --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">{{ 'หัวข้อ' }}</label>
                        <input type="text" wire:model="title"
                            class="w-full border rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:border-transparent transition
                            @error('title') border-red-300 focus:ring-red-400 @else border-gray-200 focus:ring-blue-400 @enderror"
                            placeholder="{{ 'เกิดปัญหาอะไรขึ้น?' }}" maxlength="100">
                        <div class="min-h-5 mt-1">@error('title') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror</div>
                    </div>

                    {{-- Message --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">{{ 'รายละเอียด' }}</label>
                        <textarea wire:model="message" rows="4"
                            class="w-full border rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:border-transparent transition resize-none
                            @error('message') border-red-300 focus:ring-red-400 @else border-gray-200 focus:ring-blue-400 @enderror"
                            placeholder="{{ 'อธิบายรายละเอียดของปัญหา...' }}" maxlength="2000"></textarea>
                        <div class="min-h-5 mt-1.5">@error('message') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror</div>
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
                @endif
            </div>
        </div>
    @endif
</div>
