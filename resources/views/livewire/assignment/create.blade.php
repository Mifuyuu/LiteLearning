@section('page-title', $type === 'announcement' ? 'สร้างประกาศ' : 'สร้างงาน')
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}"
            class="text-gray-500 hover:text-blue-600 transition-colors">{{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}</a>
        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
        <a href="{{ route('classroom.show', $classroom) }}"
            class="text-gray-500 hover:text-blue-600 transition-colors">{{ $classroom->name }}</a>
        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
        <span
            class="text-gray-800 font-semibold">{{ $type === 'announcement' ? 'สร้างประกาศ' : 'สร้างงาน' }}</span>
    </nav>
@endsection

<div class="">
    <!-- Back -->
    <a href="{{ route('classroom.show', $classroom) }}"
        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
        <x-icon name="arrow-left" class="h-4 w-4 mr-2" /> กลับไปที่ {{ $classroom->name }}
    </a>

    <form wire:submit.prevent="save" class="space-y-5">
        <!-- Type Badge (read-only) -->
        @php
            $typeInfo = [
                'announcement' => ['icon' => 'megaphone', 'label' => 'ประกาศ', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                'question' => ['icon' => 'pencil-square', 'label' => 'คำถาม', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                'file' => ['icon' => 'arrow-up-tray', 'label' => 'อัปโหลดไฟล์', 'bg' => 'bg-teal-100', 'text' => 'text-teal-700'],
                'attendance' => ['icon' => 'clipboard-document-check', 'label' => 'เช็คชื่อ', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
                'material' => ['icon' => 'book-open', 'label' => 'เอกสาร', 'bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
                'topic' => ['icon' => 'cube', 'label' => 'หัวข้อ', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
                'project' => ['icon' => 'squares-2x2', 'label' => 'โปรเจกต์', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
            ];
            $current = $typeInfo[$type] ?? ['icon' => 'pencil-square', 'label' => ucfirst($type), 'bg' => 'bg-gray-100', 'text' => 'text-gray-700'];
        @endphp
        <div class="flex items-center gap-3 bg-white px-5 py-3.5 rounded-xl border border-gray-200">
            <div
                class="w-9 h-9 rounded-full flex items-center justify-center {{ $current['bg'] }} {{ $current['text'] }}">
                <x-icon :name="$current['icon']" class="h-4 w-4" />
            </div>
            <div>
                <p class="text-xs text-gray-400 leading-none mb-0.5">ประเภท</p>
                <p class="text-sm font-semibold {{ $current['text'] }}">{{ $current['label'] }}</p>
            </div>
        </div>

        <!-- Title Card -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">หัวข้อ *</h3>
            </div>
            <div class="p-6">
                <input wire:model="title" type="text" maxlength="50"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="หัวข้อ *">
                <div class="mt-1 flex justify-between items-center">
                    @error('title')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @else
                        <span></span>
                    @enderror
                    <span class="text-xs" :class="$wire.title.length >= 50 ? 'text-red-500 font-medium' : 'text-gray-400'">
                        <span x-text="$wire.title.length">0</span>/50
                    </span>
            </div>
        </div>

        <!-- Description Card -->
        @if($type !== 'attendance')
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700">
                        {{ $type === 'announcement' ? 'เนื้อหา' : 'รายละเอียด' }}
                    </h3>
                </div>
                <div class="p-6">
                    <div wire:ignore x-data="tiptapEditor({ wireModel: 'description', placeholder: 'เพิ่มรายละเอียดหรือคำแนะนำสำหรับงานนี้...' })">
                        <x-tiptap-toolbar />
                        <div x-ref="editorEl"
                            class="min-h-[150px] border border-gray-200 rounded-b-lg p-3 focus:outline-none prose prose-sm max-w-none">
                        </div>
                    </div>

                    @error('description')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif

        <!-- File Upload Section -->
        @if(!in_array($type, ['question', 'attendance']))
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-700">ไฟล์แนบ</h3>
                </div>
                <div class="p-6">
                    <!-- Dropzone Area -->
                    <div class="w-full">
                        <label for="file-upload"
                            class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors {{ $file ? 'border-blue-500 bg-blue-50' : '' }}">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <x-icon :name="$file ? 'check-circle' : 'arrow-up-tray'" class="h-7 w-7 mb-3 {{ $file ? 'text-blue-500' : 'text-gray-400' }}" />
                                <p class="mb-1 text-sm text-gray-500">
                                    @if($file)
                                        <span class="font-semibold text-blue-600">เลือกไฟล์แล้ว</span>
                                    @else
                                        <span class="font-semibold">คลิกเพื่ออัปโหลด</span>
                                        หรือลากและวางไฟล์
                                    @endif
                                </p>
                                @if(!$file)
                                    <p class="text-xs text-gray-400">PDF, DOCX, PPTX, JPG, PNG (สูงสุด 25MB)</p>
                                @endif
                            </div>
                            <input id="file-upload" type="file" wire:model.live="file" class="hidden" />
                        </label>
                    </div>

                    <!-- Loading State -->
                    <div wire:loading wire:target="file" class="mt-4 w-full">
                        <div
                            class="flex items-center justify-center space-x-2 text-sm text-blue-600 bg-blue-50 rounded-lg p-3">
                            <x-icon name="spinner" class="h-4 w-4 animate-spin" />
                            <span>กำลังอัปโหลดไฟล์...</span>
                        </div>
                    </div>

                    @error('file')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror

                    <!-- Uploaded Files List -->
                    @if(count($uploadedFiles) > 0)
                        <div class="mt-4 space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">
                                ไฟล์ที่จะแนบ ({{ count($uploadedFiles) }})
                            </h4>
                            @foreach($uploadedFiles as $index => $uploadedFile)
                                <div
                                    class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg shadow-sm group hover:border-blue-300 transition-colors">
                                    <div class="flex items-center space-x-3 overflow-hidden">
                                        <div
                                            class="shrink-0 w-10 h-10 rounded bg-blue-50 flex items-center justify-center text-blue-600">
                                            @php
                                                $mime = $uploadedFile['mime'];
                                                $icon = 'document';
                                                if (str_contains($mime, 'image'))
                                                    $icon = 'photo';
                                                elseif (str_contains($mime, 'pdf'))
                                                    $icon = 'document-text';
                                                elseif (str_contains($mime, 'word'))
                                                    $icon = 'document-text';
                                                elseif (str_contains($mime, 'video'))
                                                    $icon = 'document-text';
                                                elseif (str_contains($mime, 'audio'))
                                                    $icon = 'document-text';
                                            @endphp
                                            <x-icon :name="$icon" class="h-5 w-5" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $uploadedFile['name'] }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ number_format($uploadedFile['size'] / 1024 / 1024, 2) }} MB
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removeFile({{ $index }})"
                                        class="text-gray-400 hover:text-red-500 shrink-0 w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50 transition-colors"
                                        title="ลบ">
                                        <x-icon name="x-mark" class="h-4 w-4" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
        @if($type !== 'announcement')
            <!-- Options Card: Topic + Due Date + Points -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-6 space-y-4">
                    <!-- Topic -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            <x-icon name="folder" class="h-4 w-4 mr-1.5 text-gray-400" />หัวข้อ
                        </label>
                        <input wire:model="topic" type="text" list="topics-list"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="เลือกหรือสร้างหัวข้อ">
                        <datalist id="topics-list">
                            @foreach($this->topics as $t)
                                <option value="{{ $t->name }}">
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Auto-Publish At -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            <x-icon name="clock" class="h-4 w-4 mr-1.5 text-gray-400" />กำหนดเวลาเผยแพร่
                        </label>
                        <input type="datetime-local" wire:model="published_at"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <p class="text-xs text-gray-500 mt-1">เว้นว่างไว้เพื่อเผยแพร่ด้วยตนเอง</p>
                        @error('published_at')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date + EXP + Coin row -->
                    @if(!in_array($type, ['material', 'topic']))
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    <x-icon name="clock" class="h-4 w-4 mr-1.5 text-gray-400" />วันกำหนดส่ง
                                </label>
                                <input wire:model="due_date" type="datetime-local"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            </div>
                            <div>
                                <label class="flex items-center text-sm font-medium text-gray-700 mb-1.5">
                                    <x-icon name="bolt" class="text-blue-600 mr-1.5 h-4 w-4 shrink-0" />รางวัล EXP
                                </label>
                                <input wire:model="exp_reward" type="number" min="0" max="9999"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="flex items-center text-sm font-medium text-gray-700 mb-1.5">
                                    <x-icon name="star-solid" class="text-amber-500 mr-1.5 h-4 w-4 shrink-0" />รางวัลเหรียญ
                                </label>
                                <input wire:model="coin_reward" type="number" min="0" max="9999"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>

                            <!-- Allow late submission -->
                            <div class="flex items-center gap-3 pt-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input wire:model="allow_late_submission" type="checkbox" class="sr-only peer">
                                    <div
                                        class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600">
                                    </div>
                                </label>
                                <span class="text-sm text-gray-700">
                                    {{ $type === 'attendance' ? 'อนุญาตให้เช็คชื่อช้า' : 'อนุญาตให้ส่งงานล่าช้า' }}
                                </span>
                            </div>
                    @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                @if($type !== 'announcement')
                    <div x-show="!$wire.published_at">
                        <button type="button" wire:click="saveDraft"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <x-icon name="document-text" class="h-4 w-4 mr-1.5" />บันทึกฉบับร่าง
                        </button>
                    </div>
                @else
                    <div></div>
                @endif
                <button type="submit"
                    class="btn-3d btn-3d--blue px-6 py-2.5 text-sm font-medium rounded-lg transition-colors">
                    <span wire:loading.remove wire:target="save"><x-icon name="paper-airplane" class="h-4 w-4 mr-1.5" />{{ $type === 'announcement' ? 'โพสต์' : 'มอบหมาย' }}</span>
                    <span wire:loading wire:target="save"><x-icon name="spinner" class="h-4 w-4 mr-1.5 animate-spin" />{{ $type === 'announcement' ? 'กำลังโพสต์...' : 'กำลังมอบหมาย...' }}</span>
                </button>
            </div>
        </div>
    </form>
</div>
