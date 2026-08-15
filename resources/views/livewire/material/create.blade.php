<div class="max-w-4xl mx-auto">
    <div>

        {{-- Breadcrumb --}}
        <nav class="flex items-center text-sm text-gray-500 mb-6 flex-wrap gap-1">
            <a href="{{ route('classrooms') }}" class="hover:text-blue-600 transition-colors">{{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}</a>
            <x-icon name="chevron-right" class="h-3.5 w-3.5 mx-1" />
            <a href="{{ route('classroom.show', $classroom) }}"
                class="hover:text-blue-600 transition-colors truncate max-w-[200px]">{{ $classroom->name }}</a>
            <x-icon name="chevron-right" class="h-3.5 w-3.5 mx-1" />
            <span class="text-gray-800 font-medium">{{ 'สร้างเอกสาร' }}</span>
        </nav>

        {{-- Back link --}}
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate
            class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4 transition-colors">
            <x-icon name="arrow-left" class="h-4 w-4 mr-2" /> {{ 'กลับไปที่ชั้นเรียน' }}
        </a>

        <form wire:submit.prevent="save" class="space-y-5">

            {{-- Title --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ 'หัวข้อ' }}</label>
                <input type="text" wire:model="title"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    placeholder="{{ 'ชื่อเอกสาร' }}">
                @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Description (Tiptap editor) --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ 'รายละเอียด' }}
                    <span class="text-gray-400 font-normal">({{ 'ไม่บังคับ' }})</span>
                </label>
                <div wire:ignore x-data="tiptapEditor({ wireModel: 'description', placeholder: '{{ 'เพิ่มคำอธิบาย...' }}' })">
                    <x-tiptap-toolbar />
                    <div x-ref="editorEl"
                        class="min-h-[150px] border border-gray-200 rounded-b-lg p-3 focus:outline-none prose prose-sm max-w-none">
                    </div>
                </div>
                @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- File Upload --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <x-icon name="paperclip" class="h-4 w-4 mr-1 text-gray-400" /> {{ 'ไฟล์แนบ' }}
                    <span class="text-gray-400 font-normal">({{ 'ไม่บังคับ' }})</span>
                </label>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors"
                    x-data="{ dragging: false }"
                    x-on:dragover.prevent="dragging = true"
                    x-on:dragleave.prevent="dragging = false"
                    x-on:drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                    :class="{ 'border-blue-400 bg-blue-50': dragging }">
                    <x-icon name="arrow-up-tray" class="h-7 w-7 text-gray-400 mb-2" />
                    <p class="text-sm text-gray-500">{{ 'ลากและวางไฟล์หรือ' }}</p>
                    <label class="cursor-pointer text-sm text-blue-600 hover:text-blue-500 font-medium">
                        {{ 'เรียกดู' }}
                        <input type="file" wire:model="file" class="hidden" x-ref="fileInput">
                    </label>
                    <p class="text-xs text-gray-400 mt-1">{{ 'สูงสุด 25MB ต่อไฟล์' }}</p>
                </div>

                @if(count($uploadedFiles) > 0)
                    <div class="mt-3 space-y-2">
                        @foreach($uploadedFiles as $index => $uploaded)
                            <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <x-icon name="document" class="h-4 w-4 text-gray-400" />
                                    <span class="text-sm text-gray-700 truncate">{{ $uploaded['name'] }}</span>
                                    <span class="text-xs text-gray-400">({{ number_format($uploaded['size'] / 1024, 0) }} KB)</span>
                                </div>
                                <button type="button" wire:click="removeFile({{ $index }})"
                                    class="text-red-400 hover:text-red-600 transition-colors">
                                    <x-icon name="x-mark" class="h-4 w-4" />
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Topic --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <x-icon name="bookmark" class="h-4 w-4 mr-1 text-gray-400" /> {{ 'หัวข้อ' }}
                    <span class="text-gray-400 font-normal">({{ 'ไม่บังคับ' }})</span>
                </label>
                <input type="text" wire:model="topic" list="topics-list"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    placeholder="{{ 'เลือกหรือสร้างหัวข้อ' }}">
                <datalist id="topics-list">
                    @foreach($this->topics as $t)
                        <option value="{{ $t->name }}">
                    @endforeach
                </datalist>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('classroom.show', $classroom) }}" wire:navigate
                    class="btn-3d btn-3d--white inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-lg transition-colors">
                    {{ 'ยกเลิก' }}
                </a>
                <button type="submit"
                    class="btn-3d btn-3d--indigo inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-lg transition-colors">
                    <x-icon name="check" class="h-4 w-4 mr-2" /> {{ 'เผยแพร่เอกสาร' }}
                </button>
            </div>
        </form>
    </div>
</div>
