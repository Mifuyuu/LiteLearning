@section('page-title', 'สร้างสื่อการสอน' . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)">{{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}</a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-[#686b82] transition-colors hover:text-(--ll-blue)" title="{{ $classroom->name }}">{{ \Illuminate\Support\Str::limit($classroom->name, 25, '..') }}</a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ 'สร้างสื่อการสอน' }}</span>
    </nav>
@endsection

@php
    $themeColor = $classroom->themeCategory?->color ?? \App\Models\ThemeCategory::fallbackFor($classroom->id)['color'];
@endphp

<div class="max-w-4xl mx-auto" style="--cw-color: {{ $themeColor }}; --cw-subtle: {{ $themeColor }}26; --cw-faint: {{ $themeColor }}12;" x-data="{ showDeleteFileModal: false, deleteFileIndex: null, deleteFileName: '' }">
    <form wire:submit.prevent="save" class="rounded-2xl border-3 border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh-3rem)] flex flex-col">
        {{-- Header --}}
        <div class="p-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] shrink-0"
                    style="background-color: {{ $themeColor }}20; color: {{ $themeColor }};">
                    <x-icon name="book-open" class="h-5 w-5" />
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-[#101114] truncate">{{ 'สร้างสื่อการสอน' }}</h1>
                    <p class="text-xs text-[#686b82] mt-0.5 truncate">{{ $classroom->name }}</p>
                </div>
            </div>

            <a href="{{ route('classroom.work', ['classroom' => $classroom, 'scope' => 'all']) }}" wire:navigate
                class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] shrink-0 border border-[#dedee5] text-[#686b82] hover:text-[#101114] hover:bg-gray-100 transition-colors ml-auto"
                title="{{ 'กลับไปที่งานในชั้นเรียน' }}">
                <x-icon name="arrow-left" class="h-5 w-5" />
            </a>
        </div>

        <div class="p-6 pt-0 space-y-6 flex-1">
            {{-- Title & Topic row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-[#101114] mb-2">{{ 'ชื่อสื่อการสอน' }} <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="title"
                        class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color) text-sm"
                        placeholder="{{ 'ชื่อเอกสารหรือสื่อการสอน' }}">
                    @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <x-topic-input :topics="$this->topics" :value="$topic" wire:model="topic"
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
                <div wire:ignore x-data="tiptapEditor({ wireModel: 'description', placeholder: '{{ 'เพิ่มคำอธิบายหรือเนื้อหา...' }}' })">
                    <x-tiptap-toolbar />
                    <div x-ref="editorEl"
                        class="min-h-37.5 border border-gray-200 rounded-b-lg p-3 focus:outline-none prose prose-sm max-w-none">
                    </div>
                </div>
                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- File Upload --}}
            <div x-data="{ uploading: false, progress: 0, uploadError: '' }"
                x-on:livewire-upload-start="uploading = true; progress = 0; uploadError = ''"
                x-on:livewire-upload-finish="uploading = false"
                x-on:livewire-upload-cancel="uploading = false"
                x-on:livewire-upload-error="uploading = false; uploadError = 'อัปโหลดไฟล์ไม่สำเร็จ ไฟล์อาจมีขนาดใหญ่เกินไป (สูงสุด 25MB) กรุณาลองใหม่อีกครั้ง'"
                x-on:livewire-upload-progress="progress = $event.detail.progress">
                <label class="block text-sm font-bold text-[#101114] mb-2">{{ 'ไฟล์แนบ' }}
                    <span class="text-[#9497a9] font-normal">({{ 'ไม่บังคับ' }})</span>
                </label>

                <div class="w-full">
                    <label for="file-upload"
                        class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-[#dedee5] border-dashed rounded-xl cursor-pointer bg-[#f9f9fb] hover:bg-(--cw-faint) hover:border-(--cw-color) transition-colors {{ $file ? 'border-(--cw-color) bg-(--cw-faint)' : '' }}">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <x-icon :name="$file ? 'check-circle' : 'arrow-up-tray'" class="h-7 w-7 mb-2 text-[#9497a9] {{ $file ? 'text-(--cw-color)' : '' }}" />
                            <p class="mb-1 text-sm text-[#686b82]">
                                @if($file)
                                    <span class="font-bold text-(--cw-color)">{{ 'เลือกไฟล์แล้ว' }}</span>
                                @else
                                    <span class="font-bold">{{ 'คลิกเพื่ออัปโหลด' }}</span> {{ 'หรือลากและวางไฟล์' }}
                                @endif
                            </p>
                            @if(!$file)
                                <p class="text-xs text-[#9497a9]">{{ 'PDF, DOCX, PPTX, JPG, PNG (สูงสุด 25MB)' }}</p>
                            @endif
                        </div>
                        <input id="file-upload" type="file" wire:model.live="file" multiple class="hidden" />
                    </label>
                </div>

                {{-- Loading State --}}
                <div x-show="uploading" x-cloak class="mt-3 w-full bg-blue-50 rounded-lg p-3">
                    <div class="flex items-center justify-between text-sm text-blue-600 mb-1.5">
                        <span class="flex items-center gap-2">
                            <x-icon name="spinner" class="h-4 w-4 animate-spin" />
                            {{ 'กำลังอัปโหลดไฟล์...' }}
                        </span>
                        <span class="font-semibold" x-text="progress + '%'"></span>
                    </div>
                    <div class="h-2 bg-blue-100 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full transition-all duration-150" :style="`width: ${progress}%`"></div>
                    </div>
                </div>

                @error('file')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror

                <p x-show="uploadError" x-cloak x-text="uploadError" class="mt-2 text-xs text-red-500"></p>

                {{-- Uploaded Files List --}}
                @if(count($uploadedFiles) > 0)
                    <div class="mt-3 space-y-2">
                        <h4 class="text-xs font-semibold text-[#686b82] uppercase tracking-wider mb-2">
                            {{ 'ไฟล์ที่จะแนบ' }} ({{ count($uploadedFiles) }})
                        </h4>
                        @foreach($uploadedFiles as $index => $uploadedFile)
                            <div class="flex items-center justify-between p-3 bg-[#f9f9fb] border border-[#dedee5] rounded-xl group hover:border-(--cw-color)/30 transition-colors">
                                <div class="flex items-center space-x-3 overflow-hidden">
                                    <div class="shrink-0 w-9 h-9 rounded-lg bg-(--cw-faint) text-(--cw-color) flex items-center justify-center">
                                        @php
                                            $mime = $uploadedFile['mime'];
                                            $icon = 'document';
                                            if (str_contains($mime, 'image'))
                                                $icon = 'photo';
                                            elseif (str_contains($mime, 'pdf') || str_contains($mime, 'word') || str_contains($mime, 'video') || str_contains($mime, 'audio'))
                                                $icon = 'document-text';
                                        @endphp
                                        <x-icon :name="$icon" class="h-5 w-5" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-[#101114] truncate">
                                            {{ $uploadedFile['name'] }}
                                        </p>
                                        <p class="text-xs text-[#9497a9]">
                                            {{ number_format($uploadedFile['size'] / 1024 / 1024, 2) }} MB
                                        </p>
                                    </div>
                                </div>
                                <button type="button"
                                    @click="deleteFileIndex = {{ $index }}; deleteFileName = @js($uploadedFile['name']); showDeleteFileModal = true"
                                    class="text-[#9497a9] hover:text-red-500 shrink-0 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 transition-colors cursor-pointer"
                                    title="{{ 'ลบ' }}">
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Auto-Publish At --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-[#101114] mb-1.5">
                        <x-icon name="clock" class="h-4 w-4 mr-1 text-[#9497a9]" />{{ 'กำหนดเวลาเผยแพร่' }}
                        <span class="text-[#9497a9] font-normal">({{ 'ไม่บังคับ' }})</span>
                    </label>
                    <div wire:ignore x-data="datetimePicker({ wireModel: 'published_at', placeholder: 'เลือกวันและเวลาเผยแพร่' })" class="relative">
                        <input x-ref="inputEl" type="text"
                            class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 pr-9 text-sm focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color) bg-white cursor-pointer"
                            placeholder="{{ 'เลือกวันและเวลาเผยแพร่' }}">
                        <button type="button" x-show="$wire.published_at" x-cloak @click="clear()"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[#9497a9] hover:text-[#101114]">
                            <x-icon name="x-mark" class="h-4 w-4" />
                        </button>
                    </div>
                    <p class="text-xs text-[#9497a9] mt-1">{{ 'เว้นว่างไว้เพื่อเผยแพร่ทันที' }}</p>
                    @error('published_at')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Actions Footer --}}
        <div class="p-6 bg-[#f9f9fb] border-t border-[#dedee5] flex items-center justify-end gap-3">
            <a href="{{ route('classroom.work', ['classroom' => $classroom, 'scope' => 'all']) }}" wire:navigate
                class="inline-flex items-center justify-center w-11 h-11 sm:w-auto sm:h-auto sm:px-5 sm:py-2.5 text-sm font-bold rounded-lg border border-[#dedee5] bg-white text-[#686b82] hover:bg-gray-100 hover:text-[#101114] transition-colors cursor-pointer"
                title="{{ 'ยกเลิก' }}">
                <x-icon name="x-mark" class="h-5 w-5 sm:hidden" />
                <span class="hidden sm:inline">{{ 'ยกเลิก' }}</span>
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center w-11 h-11 sm:w-auto sm:h-auto sm:px-6 sm:py-2.5 text-sm font-bold rounded-lg text-white bg-(--cw-color) hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer shadow-sm disabled:cursor-not-allowed disabled:opacity-40"
                title="{{ 'เผยแพร่สื่อการสอน' }}">
                <x-icon name="check" class="h-5 w-5 sm:mr-2" />
                <span class="hidden sm:inline">{{ 'เผยแพร่สื่อการสอน' }}</span>
            </button>
        </div>
    </form>

    {{-- Modal Delete File --}}
    <template x-teleport="body">
        <x-confirm-modal show="showDeleteFileModal" cancel="showDeleteFileModal = false" heading="ยืนยันการลบไฟล์">
            <x-slot:message>
                {{ 'คุณต้องการลบไฟล์' }} <span class="font-semibold text-[#101114]" x-text="deleteFileName"></span> {{ 'ใช่หรือไม่?' }}
            </x-slot:message>
            <button type="button"
                @click="$wire.removeFile(deleteFileIndex); showDeleteFileModal = false"
                class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                {{ 'ลบไฟล์' }}
            </button>
        </x-confirm-modal>
    </template>
</div>
