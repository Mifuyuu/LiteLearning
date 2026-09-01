@section('page-title', 'รายงานปัญหา')

<div class="space-y-6">
    <div class="bg-white rounded-2xl border-3 border-gray-200 overflow-hidden min-h-[calc(100vh-3rem)] flex flex-col">

        <div class="flex items-center justify-between gap-4 p-4 sm:p-6 border-b border-gray-200">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">ปัญหาและข้อเสนอแนะ</h1>
            <span class="text-sm font-medium text-gray-500">{{ $reports->count() }} รายการ</span>
        </div>

        <div x-data="{ showDeleteModal: false, deleteId: null, deleteTitle: '' }"
            @open-delete-bugreport.window="deleteId = $event.detail.id; deleteTitle = $event.detail.title; showDeleteModal = true"
            @keydown.escape.window="showDeleteModal = false">
            <template x-teleport="body">
                <x-confirm-modal show="showDeleteModal" cancel="showDeleteModal = false" heading="ยืนยันการลบ">
                    <x-slot:message>
                        คุณแน่ใจหรือไม่ว่าต้องการลบ <span class="font-semibold text-[#101114]" x-text="deleteTitle"></span>? การกระทำนี้ไม่สามารถย้อนกลับได้
                    </x-slot:message>
                    <button type="button" @click="$wire.delete(deleteId); showDeleteModal = false"
                        class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                        ลบ
                    </button>
                </x-confirm-modal>
            </template>
        </div>

        @if($reports->isEmpty())
            <div class="flex flex-1 flex-col items-center justify-center py-20 text-center">
                <x-icon name="flag" class="h-9 w-9 text-gray-200 mb-3" />
                <p class="text-gray-400 text-sm">ไม่มีรายงานปัญหาเลยดีจัง!</p>
            </div>
        @else
            <div class="divide-y divide-gray-100" x-data="{ openId: null }">
                @foreach($reports as $report)
                    @php
                        $typeColors = [
                            'bug' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                            'suggestion' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                            'other' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600'],
                        ];
                        $tc = $typeColors[$report->type] ?? $typeColors['other'];
                    @endphp
                    <div wire:key="report-{{ $report->id }}"
                        x-data="{ get expanded() { return openId === {{ $report->id }} } }"
                        class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-5 px-5 py-4 transition-colors"
                        :class="expanded ? 'bg-gray-100' : '{{ $report->status === 'resolved' ? 'bg-gray-50/50' : 'hover:bg-gray-50/60' }}'">

                        {{-- Type badge --}}
                        <div class="shrink-0 sm:w-28">
                            <span
                                class="inline-flex w-full items-center justify-center gap-1.5 text-xs font-bold px-2 py-1.5 rounded-lg {{ $tc['bg'] }} {{ $tc['text'] }} {{ $report->status === 'resolved' ? 'opacity-60' : '' }}">
                                @if($report->type === 'bug') บั๊ก @elseif($report->type === 'suggestion') ข้อเสนอแนะ @else อื่นๆ @endif
                            </span>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0 w-full {{ $report->status === 'resolved' ? 'opacity-60' : '' }}">
                            <div class="flex items-center justify-between gap-2 cursor-pointer" @click="openId = expanded ? null : {{ $report->id }}">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-semibold text-sm text-gray-800 break-all">{{ $report->title }}</p>
                                    @if($report->status === 'resolved')
                                        <span
                                            class="text-[10px] font-bold text-green-600 bg-green-100 px-1.5 py-0.5 rounded uppercase">แก้ไขแล้ว</span>
                                    @endif
                                </div>
                                <button type="button"
                                    class="shrink-0 flex items-center justify-center p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors [&>svg]:transition-transform [&>svg]:duration-200"
                                    :class="{ '[&>svg]:rotate-180': expanded }">
                                    <x-icon name="chevron-down" class="h-5 w-5" />
                                </button>
                            </div>

                            <div x-show="expanded" x-cloak class="mt-2 space-y-2"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="duration-0">
                                <p class="text-sm text-gray-600 leading-relaxed break-all">
                                    {{ $report->message }}
                                </p>

                                <p class="text-xs text-gray-400 flex items-center gap-2">
                                    <img src="{{ $report->user->avatar_url }}" class="w-4 h-4 rounded-full">
                                    {{ $report->user->name }}
                                    <span>·</span>
                                    {{ $report->created_at->diffForHumans() }}
                                </p>

                                @if($report->admin_reply)
                                    <div class="rounded-lg bg-blue-50 border border-blue-100 px-3 py-2">
                                        <p class="text-[10px] font-bold text-blue-700 uppercase tracking-wide">การตอบกลับ · {{ $report->replied_at->diffForHumans() }}</p>
                                        <p class="text-sm text-blue-900 mt-0.5 break-all">{{ $report->admin_reply }}</p>
                                    </div>
                                @endif
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <textarea wire:model="replyDrafts.{{ $report->id }}" rows="1" placeholder="พิมพ์ข้อความตอบกลับ..."
                                        x-data @input="$el.style.height = ''; if ($el.scrollHeight > $el.clientHeight) { $el.style.height = $el.scrollHeight + 'px' }"
                                        class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 resize-none overflow-hidden"></textarea>
                                    <button type="button" wire:click="submitReply({{ $report->id }})"
                                        class="shrink-0 self-start px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                        ตอบกลับ
                                    </button>
                                </div>
                                @error('replyDrafts.' . $report->id) <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex shrink-0 items-start gap-2 self-start">
                            <button type="button" wire:click="toggleStatus({{ $report->id }})"
                                class="shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 transition-colors"
                                title="{{ $report->status === 'pending' ? 'ทำเครื่องหมายว่าแก้ไขแล้ว' : 'เปลี่ยนกลับเป็นรอดำเนินการ' }}">
                                <x-icon name="{{ $report->status === 'pending' ? 'check' : 'arrow-uturn-left' }}" class="h-4 w-4" />
                            </button>
                            <button type="button"
                                @click="$dispatch('open-delete-bugreport', { id: {{ $report->id }}, title: '{{ addslashes($report->title) }}' })"
                                class="shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="ลบ">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
