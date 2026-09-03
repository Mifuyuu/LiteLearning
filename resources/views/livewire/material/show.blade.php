@section('page-title', $material->title . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)">...</a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-[#686b82] transition-colors hover:text-(--ll-blue)" title="{{ $classroom->name }}">{{ \Illuminate\Support\Str::limit($classroom->name, 10, '..') }}</a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]" title="{{ $material->title }}">{{ \Illuminate\Support\Str::limit($material->title, 10, '..') }}</span>
    </nav>
@endsection

@php
    $themeColor = $classroom->themeCategory?->color ?? \App\Models\ThemeCategory::fallbackFor($classroom->id)['color'];
@endphp

<div class="max-w-4xl mx-auto" style="--cw-color: {{ $themeColor }}; --cw-subtle: {{ $themeColor }}26; --cw-faint: {{ $themeColor }}12;"
    x-data="{ copiedToast: false }">

    {{-- Single Container Card --}}
    <div class="rounded-2xl border-3 border-[#dedee5] bg-white relative z-10 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh-3rem)] flex flex-col">
        @if(!$isEditTab)
            {{-- VIEW MODE --}}
            {{-- Header --}}
            <div class="p-4 sm:p-6 border-b border-[#dedee5]">
                <div class="flex flex-row items-start justify-between gap-3">
                    <div class="flex items-start min-w-0">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] shrink-0"
                            style="background-color: {{ $themeColor }}20; color: {{ $themeColor }}">
                            <x-icon name="book-open" class="h-5 w-5" />
                        </div>
                        <div class="ml-3 sm:ml-4 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 min-w-0">
                                <h1 class="truncate text-lg sm:text-xl font-bold text-[#101114]">
                                    {{ $material->title }}</h1>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold shrink-0"
                                    style="background-color: {{ $themeColor }}20; color: {{ $themeColor }}">
                                    {{ 'สื่อการสอน' }}
                                </span>
                                @if($material->topic)
                                    <span class="inline-flex items-center gap-1 rounded-md px-2.5 py-0.5 text-xs font-medium"
                                        style="background-color: {{ $themeColor }}15; color: {{ $themeColor }};">
                                        <x-icon name="tag" class="h-3 w-3" />
                                        {{ $material->topic }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mt-1 text-xs text-[#686b82]">
                                <span class="font-medium text-[#101114]">{{ $material->user->name ?? 'ไม่ทราบชื่อ' }}</span>
                                <span>&middot;</span>
                                <span>{{ $material->created_at->translatedFormat('j M Y, H:i') }}</span>
                                @if($material->updated_at->gt($material->created_at))
                                    <span>&middot;</span>
                                    <span>{{ 'แก้ไขเมื่อ ' . $material->updated_at->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 ml-auto shrink-0">
                        @if($classroom->canManageClassroom(auth()->user()) || auth()->user()->isAdmin())
                            <div class="relative" x-data="{ open: false }">
                                <button @click.stop="open = !open" type="button"
                                    class="w-10 h-10 flex items-center justify-center rounded-[10px] text-[#686b82] hover:text-[#101114] hover:bg-gray-100 border border-[#dedee5] transition-colors cursor-pointer"
                                    title="ตัวเลือก">
                                    <x-icon name="ellipsis-vertical" class="h-5 w-5" />
                                </button>
                                <div x-show="open" x-cloak @click.outside="open = false"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute right-0 top-full mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                    <button
                                        @click.stop="navigator.clipboard.writeText('{{ route('material.show', ['classroom' => $classroom, 'material' => $material]) }}'); open = false; copiedToast = true; setTimeout(() => copiedToast = false, 2000)"
                                        class="w-full flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <x-icon name="link" class="h-5 w-5 text-gray-400" />
                                        <span class="ml-2">คัดลอกลิงก์</span>
                                    </button>
                                    <button wire:click="openEditTab" @click.stop="open = false"
                                        class="w-full flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <x-icon name="pencil" class="h-5 w-5 text-gray-400" />
                                        <span class="ml-2">แก้ไข</span>
                                    </button>
                                    <button wire:click="openDeleteModal" @click.stop="open = false"
                                        class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <x-icon name="trash" class="h-5 w-5 text-red-400" />
                                        <span class="ml-2">ลบ</span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <a href="{{ route('classroom.work', ['classroom' => $classroom, 'scope' => 'all']) }}" wire:navigate
                            class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] shrink-0 border border-[#dedee5] text-[#686b82] hover:text-[#101114] hover:bg-gray-100 transition-colors"
                            title="{{ 'กลับไปที่งานในชั้นเรียน' }}">
                            <x-icon name="arrow-left" class="h-5 w-5" />
                        </a>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            @if($material->description)
                <div class="p-6 prose prose-sm max-w-none text-[#101114] [&>p]:my-0 [&>p]:leading-relaxed border-b border-[#dedee5]">
                    {!! $material->description !!}
                </div>
            @endif

            {{-- Attachments --}}
            @if($material->attachments->count())
                <div class="p-6 border-b border-[#dedee5]">
                    <h3 class="text-sm font-bold text-[#101114] mb-3 flex items-center gap-1.5">
                        <x-icon name="paperclip" class="h-4 w-4 text-[#9497a9]" />
                        <span>{{ 'ไฟล์แนบ' }}</span>
                        <span class="text-xs font-normal text-[#9497a9]">({{ $material->attachments->count() }})</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($material->attachments as $attachment)
                            <a href="{{ $attachment->url }}" target="_blank"
                                class="flex items-center gap-3 bg-[#f9f9fb] border border-[#dedee5] rounded-xl p-3.5 hover:bg-(--cw-faint) hover:border-(--cw-color)/30 transition-colors group">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                                    style="background-color: {{ $themeColor }}20; color: {{ $themeColor }};">
                                    <x-icon name="document" class="h-5 w-5" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-[#101114] truncate group-hover:text-(--cw-color) transition-colors">{{ $attachment->file_name }}</p>
                                    <p class="text-xs text-[#9497a9]">{{ number_format($attachment->file_size / 1024, 0) }} KB</p>
                                </div>
                                <x-icon name="arrow-top-right-on-square" class="h-4 w-4 text-[#9497a9] group-hover:text-(--cw-color)" />
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Comments Section --}}
            <div class="p-6 pt-0 flex-1">
                @livewire('classroom.stream-comment', ['contentId' => $material->id, 'contentType' => \App\Models\Material::class], key('material-comment-' . $material->id))
            </div>

        @else
            {{-- EDIT MODE --}}
            <div class="p-6 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] shrink-0"
                        style="background-color: {{ $themeColor }}20; color: {{ $themeColor }};">
                        <x-icon name="pencil-square" class="h-5 w-5" />
                    </div>
                    <h2 class="text-lg font-bold text-[#101114] truncate">{{ 'แก้ไขสื่อการสอน' }}</h2>
                </div>
                <button type="button" wire:click="cancelEditTab"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] shrink-0 border border-[#dedee5] text-[#686b82] hover:text-[#101114] hover:bg-gray-100 transition-colors ml-auto cursor-pointer"
                    title="{{ 'ยกเลิก' }}">
                    <x-icon name="arrow-left" class="h-5 w-5" />
                </button>
            </div>

            <form wire:submit.prevent="saveMaterial" class="flex-1 flex flex-col justify-between"
                x-data="{
                    initialTitle: @js($material->title ?? ''),
                    initialDescription: @js($material->description ?? ''),
                    initialTopic: @js($material->topic?->name ?? ''),
                    get isDirty() {
                        return ($wire.editTitle ?? '') !== this.initialTitle ||
                               ($wire.editDescription ?? '') !== this.initialDescription ||
                               ($wire.editTopic ?? '') !== this.initialTopic;
                    }
                }">
                <div class="p-6 pt-0 space-y-6 flex-1">
                    {{-- Title + Topic --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-[#101114] mb-2">{{ 'ชื่องาน' }} <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.live.debounce.100ms="editTitle"
                                class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color) text-sm">
                            @error('editTitle') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <x-topic-input :topics="$this->topics" :value="$editTopic" wire:model.live.debounce.100ms="editTopic"
                            class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color) text-sm"
                            placeholder="{{ 'เลือกหรือพิมพ์ชื่อหัวข้อใหม่' }}">
                            <label class="block text-sm font-bold text-[#101114] mb-2">
                                <x-icon name="tag" class="h-4 w-4 mr-1 text-[#9497a9]" />{{ 'หัวข้อ / หมวดหมู่' }}
                                <span class="text-[#9497a9] font-normal">({{ 'ไม่บังคับ' }})</span>
                            </label>
                        </x-topic-input>
                    </div>

                    {{-- Description (Tiptap editor) --}}
                    <div>
                        <label class="block text-sm font-bold text-[#101114] mb-2">{{ 'รายละเอียด' }}
                            <span class="text-[#9497a9] font-normal">({{ 'ไม่บังคับ' }})</span>
                        </label>
                        <div wire:ignore x-data="tiptapEditor({ wireModel: 'editDescription', placeholder: '{{ 'เพิ่มคำอธิบายหรือเนื้อหา...' }}' })">
                            <x-tiptap-toolbar />
                            <div x-ref="editorEl"
                                class="min-h-37.5 border border-gray-200 rounded-b-lg p-3 focus:outline-none prose prose-sm max-w-none">
                            </div>
                        </div>
                        @error('editDescription') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Edit Actions Footer (2D Buttons with Dirty State) --}}
                <div class="p-6 bg-[#f9f9fb] border-t border-[#dedee5] flex items-center justify-end gap-3 mt-auto">
                    <button type="button" wire:click="cancelEditTab"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-lg border border-[#dedee5] bg-white text-[#686b82] hover:bg-gray-100 hover:text-[#101114] transition-colors cursor-pointer">
                        {{ 'ยกเลิก' }}
                    </button>
                    <button type="submit" :disabled="!isDirty"
                        class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold rounded-lg text-white transition-all cursor-pointer shadow-sm disabled:cursor-not-allowed disabled:opacity-40"
                        :class="isDirty ? 'bg-(--cw-color) hover:opacity-90 active:scale-[0.98]' : 'bg-gray-400'">
                        <x-icon name="check" class="h-4 w-4 mr-1.5" />
                        <span wire:loading.remove wire:target="saveMaterial">{{ 'บันทึกการแก้ไข' }}</span>
                        <span wire:loading wire:target="saveMaterial"><x-icon name="spinner" class="h-4 w-4 mr-1.5 animate-spin" />{{ 'กำลังบันทึก...' }}</span>
                    </button>
                </div>
            </form>
        @endif
    </div>

    {{-- Delete modal --}}
    <template x-teleport="body">
        <x-confirm-modal show="$wire.showDeleteModal" cancel="$wire.closeDeleteModal()" heading="ยืนยันการลบสื่อการสอน" message="สื่อการสอนนี้และไฟล์แนบทั้งหมดจะถูกลบอย่างถาวร การดำเนินการนี้ไม่สามารถยกเลิกได้">
            <button type="button" wire:click="deleteMaterial"
                class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                {{ 'ลบสื่อการสอน' }}
            </button>
        </x-confirm-modal>
    </template>
</div>
