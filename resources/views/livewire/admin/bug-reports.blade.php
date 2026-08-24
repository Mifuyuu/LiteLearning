@section('page-title', 'รายงานปัญหา')

<div class="space-y-4 ">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2">
            ปัญหาและข้อเสนอแนะ
        </h1>
        <div class="inline-flex items-center justify-center px-3 py-1.5text-sm font-medium text-gray-600">
            {{ $reports->count() }} รายการ
        </div>
    </div>

    @if($reports->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
            <x-icon name="flag" class="h-9 w-9 text-gray-200 mb-3 block" />
            <p class="text-gray-400 text-sm">ไม่มีรายงานปัญหาเลยดีจัง!</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            @foreach($reports as $report)
                @php
                    $typeColors = [
                        'bug' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'bug'],
                        'suggestion' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'lightbulb'],
                        'other' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'question-mark-circle'],
                    ];
                    $tc = $typeColors[$report->type] ?? $typeColors['other'];
                @endphp
                <div wire:key="report-{{ $report->id }}"
                    class="flex flex-col sm:flex-row items-start gap-3 sm:gap-5 px-5 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }} transition-all {{ $report->status === 'resolved' ? 'opacity-50 bg-gray-50/50' : '' }}">

                    {{-- Type badge --}}
                    <div class="shrink-0 sm:w-32">
                        <span
                            class="inline-flex items-center justify-center w-full gap-1.5 text-xs font-bold px-2 py-1.5 rounded-lg {{ $tc['bg'] }} {{ $tc['text'] }}">
                            <x-icon :name="$tc['icon']" class="h-4 w-4" />
                            @if($report->type === 'bug') บั๊ก @elseif($report->type === 'suggestion') ข้อเสนอแนะ @else อื่นๆ @endif
                        </span>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0 w-full sm:pt-0.5" x-data="{ expanded: false }">
                        <div class="flex items-center justify-between gap-2 cursor-pointer" @click="expanded = !expanded">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-semibold text-sm text-gray-800 break-all">{{ $report->title }}</p>
                                @if($report->status === 'resolved')
                                    <span
                                        class="text-[10px] font-bold text-green-600 bg-green-100 px-1.5 py-0.5 rounded uppercase">แก้ไขแล้ว</span>
                                @endif
                            </div>
                            <button type="button" class="text-gray-400 hover:text-gray-600 transition-transform duration-200"
                                :class="{ 'rotate-180': expanded }">
                                <x-icon name="chevron-down" class="h-4 w-4" />
                            </button>
                        </div>

                        <div class="mt-1 transition-all overflow-hidden" :class="expanded ? 'max-h-96' : 'max-h-11'">
                            <p class="text-sm text-gray-600 leading-relaxed break-all"
                                :class="!expanded ? 'line-clamp-2 text-gray-500' : 'pb-2'">
                                {{ $report->message }}
                            </p>
                        </div>

                        <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-2">
                            <img src="{{ $report->user->avatar_url }}" class="w-4 h-4 rounded-full">
                            {{ $report->user->name }}
                            <span>·</span>
                            {{ $report->created_at->diffForHumans() }}
                        </p>

                        <div x-show="expanded" x-cloak class="mt-3 space-y-2">
                            @if($report->admin_reply)
                                <div class="rounded-lg bg-blue-50 border border-blue-100 px-3 py-2">
                                    <p class="text-[10px] font-bold text-blue-700 uppercase tracking-wide">การตอบกลับของแอดมิน · {{ $report->replied_at->diffForHumans() }}</p>
                                    <p class="text-sm text-blue-900 mt-0.5 break-all">{{ $report->admin_reply }}</p>
                                </div>
                            @endif
                            <div class="flex flex-col sm:flex-row gap-2">
                                <textarea wire:model="replyDrafts.{{ $report->id }}" rows="2" placeholder="พิมพ์ข้อความตอบกลับ..."
                                    class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                                <button type="button" wire:click="submitReply({{ $report->id }})"
                                    class="shrink-0 self-start px-4 py-2 text-xs font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                    ส่งการตอบกลับ
                                </button>
                            </div>
                            @error('replyDrafts.' . $report->id) <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Toggle button --}}
                    <button wire:click="toggleStatus({{ $report->id }})" class="shrink-0 text-xs font-medium px-3 py-1.5 rounded-lg border transition cursor-pointer
                                                                                    {{ $report->status === 'pending'
                    ? 'border-green-300 text-green-700 hover:bg-green-50'
                    : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                        {{ $report->status === 'pending' ? 'ทำเครื่องหมายว่าแก้ไขแล้ว' : 'เปลี่ยนกลับเป็นรอดำเนินการ' }}
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</div>