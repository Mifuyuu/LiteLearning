@section('page-title', $type === 'announcement' ? 'สร้างประกาศ' : 'สร้างงาน')
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}"
            class="text-[#686b82] hover:text-(--ll-blue) transition-colors">{{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}</a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate
            class="text-[#686b82] hover:text-(--ll-blue) transition-colors"
            title="{{ $classroom->name }}">{{ \Illuminate\Support\Str::limit($classroom->name, 25, '..') }}</a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span
            class="text-[#101114] font-semibold">{{ $type === 'announcement' ? 'สร้างประกาศ' : ($type === 'attendance' ? 'สร้างงานเช็คชื่อ' : 'สร้างงาน') }}</span>
    </nav>
@endsection

@php
    $themeColor = $classroom->themeCategory?->color ?? \App\Models\ThemeCategory::fallbackFor($classroom->id)['color'];
    $typeInfo = [
        'announcement' => ['icon' => 'megaphone', 'label' => 'ประกาศ', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
        'file' => ['icon' => 'document-text', 'label' => 'งานส่งไฟล์', 'bg' => 'bg-teal-100', 'text' => 'text-teal-700'],
        'attendance' => ['icon' => 'pencil-square', 'label' => 'งานเช็คชื่อ', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
        'material' => ['icon' => 'book-open', 'label' => 'สื่อการสอน', 'bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
    ];
    $current = $typeInfo[$type] ?? ['icon' => 'pencil-square', 'label' => ucfirst($type), 'bg' => 'bg-gray-100', 'text' => 'text-gray-700'];
@endphp

<div class="max-w-4xl mx-auto" style="--cw-color: {{ $themeColor }}; --cw-subtle: {{ $themeColor }}26; --cw-faint: {{ $themeColor }}12;" x-data="{ showDeleteFileModal: false, deleteFileIndex: null, deleteFileName: '' }">
    <form wire:submit.prevent="save" class="rounded-2xl border-3 border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh-3rem)] flex flex-col">
        {{-- Header --}}
        <div class="p-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] shrink-0"
                    style="background-color: {{ $themeColor }}20; color: {{ $themeColor }}">
                    <x-icon :name="$current['icon']" class="h-5 w-5" />
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-[#101114] truncate">
                            {{ $type === 'announcement' ? 'สร้างประกาศ' : ($type === 'attendance' ? 'สร้างงานเช็คชื่อ' : 'สร้างงาน') }}
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold shrink-0"
                            style="background-color: {{ $themeColor }}20; color: {{ $themeColor }}">
                            {{ $current['label'] }}
                        </span>
                    </div>
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
            {{-- Title --}}
            @if($type === 'attendance')
                <div>
                    <label class="block text-sm font-bold text-[#101114] mb-2">{{ 'ชื่องาน' }}</label>
                    <input type="text" readonly disabled
                        class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 text-sm bg-gray-50 text-gray-500 cursor-not-allowed select-none"
                        value="เช็คชื่อประจำวันที่ {{ now()->format('d/m/y') }}">
                </div>
            @else
                <div>
                    <label class="block text-sm font-bold text-[#101114] mb-2">{{ 'ชื่องาน *' }}</label>
                    <input wire:model="title" type="text" maxlength="50"
                        class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color)"
                        placeholder="{{ 'ชื่องาน *' }}">
                    <div class="mt-1 flex justify-between items-center">
                        @error('title')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @else
                            <span></span>
                        @enderror
                        <span class="text-xs" :class="$wire.title.length >= 50 ? 'text-red-500 font-medium' : 'text-gray-400'">
                            <span x-text="$wire.title.length">0</span>/50
                        </span>
                    </div>
                </div>
            @endif

            {{-- Description (Tiptap editor) --}}
            @if($type !== 'attendance')
                <div>
                    <label class="block text-sm font-bold text-[#101114] mb-2">
                        {{ $type === 'announcement' ? 'เนื้อหา' : 'รายละเอียด' }}
                        <span class="text-[#9497a9] font-normal">({{ 'ไม่บังคับ' }})</span>
                    </label>
                    <div wire:ignore x-data="tiptapEditor({ wireModel: 'description', placeholder: '{{ 'เพิ่มรายละเอียดหรือคำแนะนำสำหรับงานนี้...' }}' })">
                        <x-tiptap-toolbar />
                        <div x-ref="editorEl"
                            class="min-h-37.5 border border-gray-200 rounded-b-lg p-3 focus:outline-none prose prose-sm max-w-none">
                        </div>
                    </div>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            {{-- File Upload --}}
            @if($type !== 'attendance')
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
                            <input id="file-upload" type="file" wire:model.live="file" class="hidden" />
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
            @endif

            {{-- Options: Topic + Auto-Publish + Due Date + Points --}}
            @if($type !== 'announcement')
                <div class="space-y-4 pt-2 border-t border-[#dedee5]">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Topic --}}
                        @if($type === 'attendance')
                            <div>
                                 <label class="block text-sm font-bold text-[#101114] mb-1.5">
                                     <x-icon name="tag" class="h-4 w-4 mr-1 text-[#9497a9]" />{{ 'หัวข้อ' }}
                                 </label>
                                 <input type="text" readonly disabled
                                     class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 text-sm bg-gray-50 text-gray-500 cursor-not-allowed select-none"
                                     value="เช็คชื่อ">
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-bold text-[#101114] mb-1.5">
                                    <x-icon name="tag" class="h-4 w-4 mr-1 text-[#9497a9]" />{{ 'หัวข้อ' }}
                                    <span class="text-[#9497a9] font-normal">({{ 'ไม่บังคับ' }})</span>
                                </label>
                                <input wire:model="topic" type="text" list="topics-list"
                                    class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color)"
                                    placeholder="{{ 'เลือกหรือพิมพ์สร้างหัวข้อใหม่' }}">
                                <datalist id="topics-list">
                                    @foreach($this->topics as $t)
                                        <option value="{{ $t->name }}">
                                    @endforeach
                                </datalist>
                            </div>
                        @endif

                        {{-- Auto-Publish At --}}
                        <div>
                            <label class="block text-sm font-bold text-[#101114] mb-1.5">
                                <x-icon name="clock" class="h-4 w-4 mr-1 text-[#9497a9]" />{{ 'กำหนดเวลาเผยแพร่' }}
                                <span class="text-[#9497a9] font-normal">({{ 'ไม่บังคับ' }})</span>
                            </label>
                            <input type="datetime-local" wire:model="published_at"
                                class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color) bg-white">
                            <p class="text-xs text-[#9497a9] mt-1">{{ 'เว้นว่างไว้เพื่อเผยแพร่ทันที' }}</p>
                            @error('published_at')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Due Date + Points + EXP + Coin row --}}
                    @if(!in_array($type, ['material', 'topic']))
                        @if($type === 'attendance')
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="flex items-center text-sm font-bold text-[#101114] mb-1.5">
                                        <x-icon name="academic-cap" class="text-[#9497a9] mr-1.5 h-4 w-4 shrink-0" />{{ 'คะแนนเช็คชื่อ' }}
                                    </label>
                                    <input wire:model="max_score" type="number" min="0" max="100"
                                        class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color)">
                                    @error('max_score')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="flex items-center text-sm font-bold text-[#101114] mb-1.5">
                                        <x-icon name="bolt" class="text-blue-600 mr-1.5 h-4 w-4 shrink-0" />{{ 'รางวัล EXP' }}
                                    </label>
                                    <input wire:model="exp_reward" type="number" min="0" max="9999"
                                        class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color)">
                                </div>
                                <div>
                                    <label class="flex items-center text-sm font-bold text-[#101114] mb-1.5">
                                        <x-icon name="star-solid" class="text-amber-500 mr-1.5 h-4 w-4 shrink-0" />{{ 'รางวัลเหรียญ' }}
                                    </label>
                                    <input wire:model="coin_reward" type="number" min="0" max="9999"
                                        class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color)">
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                @if(!in_array($type, ['material', 'announcement', 'topic']))
                                    <div>
                                        <label class="flex items-center text-sm font-bold text-[#101114] mb-1.5">
                                            <x-icon name="academic-cap" class="text-[#9497a9] mr-1.5 h-4 w-4 shrink-0" />{{ 'คะแนนเต็ม' }}
                                        </label>
                                        <input wire:model="max_score" type="number" min="0" max="100"
                                            class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color)">
                                        @error('max_score')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-bold text-[#101114] mb-1.5">
                                        <x-icon name="clock" class="h-4 w-4 mr-1.5 text-[#9497a9]" />{{ 'วันกำหนดส่ง' }}
                                    </label>
                                    <input wire:model="due_date" type="datetime-local"
                                        class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color) bg-white">
                                </div>
                                <div>
                                    <label class="flex items-center text-sm font-bold text-[#101114] mb-1.5">
                                        <x-icon name="bolt" class="text-blue-600 mr-1.5 h-4 w-4 shrink-0" />{{ 'รางวัล EXP' }}
                                    </label>
                                    <input wire:model="exp_reward" type="number" min="0" max="9999"
                                        class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color)">
                                </div>
                                <div>
                                    <label class="flex items-center text-sm font-bold text-[#101114] mb-1.5">
                                        <x-icon name="star-solid" class="text-amber-500 mr-1.5 h-4 w-4 shrink-0" />{{ 'รางวัลเหรียญ' }}
                                    </label>
                                    <input wire:model="coin_reward" type="number" min="0" max="9999"
                                        class="w-full border border-[#dedee5] rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-(--cw-color) focus:border-(--cw-color)">
                                </div>
                            </div>
                        @endif

                        {{-- Allow late submission --}}
                        <div class="flex items-center gap-3 pt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input wire:model="allow_late_submission" type="checkbox" class="sr-only peer">
                                <div
                                    class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-1 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>
                            <span class="text-sm font-medium text-[#101114]">
                                {{ $type === 'attendance' ? 'อนุญาตให้เช็คชื่อสาย (หลังจากครูปิดเซสชัน)' : 'อนุญาตให้ส่งงานล่าช้า' }}
                            </span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Actions Footer --}}
        <div class="p-6 bg-[#f9f9fb] border-t border-[#dedee5] flex items-center justify-between gap-3 mt-auto">
            @if($type !== 'announcement')
                <div x-show="!$wire.published_at">
                    <button type="button" wire:click="saveDraft"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold rounded-lg border border-[#dedee5] bg-white text-[#686b82] hover:bg-gray-100 hover:text-[#101114] transition-colors cursor-pointer">
                        <x-icon name="document-text" class="h-4 w-4 mr-1.5" />{{ 'บันทึกฉบับร่าง' }}
                    </button>
                </div>
            @else
                <div></div>
            @endif

            <div class="flex items-center gap-3">
                <a href="{{ route('classroom.work', ['classroom' => $classroom, 'scope' => 'all']) }}" wire:navigate
                    class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-lg border border-[#dedee5] bg-white text-[#686b82] hover:bg-gray-100 hover:text-[#101114] transition-colors cursor-pointer">
                    {{ 'ยกเลิก' }}
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold rounded-lg text-white bg-blue-600 hover:bg-blue-700 active:scale-[0.98] transition-all cursor-pointer shadow-sm disabled:cursor-not-allowed disabled:opacity-40">
                    <span wire:loading.remove wire:target="save"><x-icon name="paper-airplane" class="h-4 w-4 mr-1.5" />{{ $type === 'announcement' ? 'โพสต์' : ($type === 'attendance' ? 'สร้างงานเช็คชื่อ' : 'มอบหมาย') }}</span>
                    <span wire:loading wire:target="save"><x-icon name="spinner" class="h-4 w-4 mr-1.5 animate-spin" />{{ $type === 'announcement' ? 'กำลังโพสต์...' : ($type === 'attendance' ? 'กำลังสร้าง...' : 'กำลังมอบหมาย...') }}</span>
                </button>
            </div>
        </div>
    </form>

    {{-- Delete Modal --}}
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
