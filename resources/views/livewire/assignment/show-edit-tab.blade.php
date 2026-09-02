<div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh-3rem)] flex flex-col">
    <div class="p-6 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] shrink-0"
                style="background-color: {{ $themeColor }}20; color: {{ $themeColor }};">
                <x-icon name="pencil-square" class="h-5 w-5" />
            </div>
            <h2 class="text-lg font-bold text-[#101114] truncate">{{ 'แก้ไขงาน' }}</h2>
        </div>
        <button wire:click="cancelEditTab" type="button"
            class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] shrink-0 border border-[#dedee5] text-[#686b82] hover:text-[#101114] hover:bg-gray-100 transition-colors ml-auto"
            title="{{ 'ยกเลิก' }}">
            <x-icon name="arrow-left" class="h-5 w-5" />
        </button>
    </div>

    <form wire:submit.prevent="saveAssignment" class="flex-1 flex flex-col justify-between"
        x-data="{
            initialTitle: @js($assignment->title ?? ''),
            initialDescription: @js($assignment->description ?? ''),
            initialMaxScore: {{ (int) ($assignment->max_score ?? 100) }},
            initialExpReward: {{ (int) ($assignment->exp_reward ?? 0) }},
            initialCoinReward: {{ (int) ($assignment->coin_reward ?? 0) }},
            initialDueDate: @js($assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : ''),
            initialStatus: @js($assignment->status ?? 'published'),
            initialTopic: @js($assignment->topic?->name ?? ''),
            initialAllowLate: {{ $assignment->allow_late_submission ? 'true' : 'false' }},
            get isDirty() {
                return ($wire.editTitle ?? '') !== this.initialTitle ||
                       ($wire.editDescription ?? '') !== this.initialDescription ||
                       Number($wire.editMaxScore ?? 0) !== this.initialMaxScore ||
                       Number($wire.editExpReward ?? 0) !== this.initialExpReward ||
                       Number($wire.editCoinReward ?? 0) !== this.initialCoinReward ||
                       ($wire.editDueDate ?? '') !== this.initialDueDate ||
                       ($wire.editStatus ?? '') !== this.initialStatus ||
                       ($wire.editTopic ?? '') !== this.initialTopic ||
                       Boolean($wire.editAllowLateSubmission) !== this.initialAllowLate ||
                       Boolean($wire.editFile);
            }
        }">
        <div class="p-6 pt-0 space-y-6 flex-1">
        <!-- Type Badge (read-only) -->
        @php
            $typeInfo = [
                'announcement' => ['icon' => 'megaphone', 'label' => 'ประกาศ'],
                'file' => ['icon' => 'document-text', 'label' => 'งานส่งไฟล์'],
                'attendance' => ['icon' => 'pencil-square', 'label' => 'งานเช็คชื่อ'],
                'material' => ['icon' => 'book-open', 'label' => 'สื่อการสอน'],
            ];
            $current = $typeInfo[$editType] ?? ['icon' => 'pencil-square', 'label' => ucfirst($editType)];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @if($editType === 'attendance')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ชื่องาน</label>
                    <input wire:model.live.debounce.100ms="editTitle" type="text" readonly disabled
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50 text-gray-500 cursor-not-allowed select-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <x-icon name="tag" class="h-4 w-4 mr-1 text-gray-400" />{{ 'หัวข้อ / หมวดหมู่' }}
                        <span class="text-gray-400 font-normal">({{ 'ไม่บังคับ' }})</span>
                    </label>
                    <input type="text" readonly disabled value="เช็คชื่อ"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50 text-gray-500 cursor-not-allowed select-none">
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ชื่องาน <span class="text-red-500">*</span></label>
                    <input wire:model.live.debounce.100ms="editTitle" type="text" maxlength="50"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <div class="mt-1 flex justify-between items-center">
                        @error('editTitle') <p class="text-sm text-red-500">{{ $message }}</p> @else <span></span> @enderror
                        <span class="text-xs" :class="$wire.editTitle.length >= 50 ? 'text-red-500 font-medium' : 'text-gray-400'">
                            <span x-text="$wire.editTitle.length">0</span>/50
                        </span>
                    </div>
                </div>
                <div>
                    <x-topic-input :topics="$this->topics" :value="$editTopic" wire:model.live.debounce.100ms="editTopic"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <x-icon name="tag" class="h-4 w-4 mr-1 text-gray-400" />{{ 'หัวข้อ / หมวดหมู่' }}
                            <span class="text-gray-400 font-normal">({{ 'ไม่บังคับ' }})</span>
                        </label>
                    </x-topic-input>
                    @error('editTopic') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            @endif
        </div>

        @if($editType !== 'attendance')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">รายละเอียด</label>
                <div wire:ignore x-data="tiptapEditor({ wireModel: 'editDescription', placeholder: 'เพิ่มรายละเอียดหรือคำแนะนำสำหรับงานนี้...' })">
                    <x-tiptap-toolbar />
                    <div x-ref="editorEl"
                        class="min-h-37.5 border border-gray-200 rounded-b-lg p-3 focus:outline-none prose prose-sm max-w-none">
                    </div>
                </div>
                @error('editDescription') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>
        @endif

        @if(!in_array($editType, ['material', 'announcement', 'topic']))
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $editType === 'attendance' ? 'คะแนนเช็คชื่อ' : 'คะแนน' }}
                    </label>
                    <input wire:model.live.debounce.100ms="editMaxScore" type="number" min="0" max="100"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    @error('editMaxScore') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                        <x-icon name="bolt" class="text-blue-600 mr-1.5 h-4 w-4 shrink-0" />รางวัล EXP
                    </label>
                    <input wire:model.live.debounce.100ms="editExpReward" type="number" min="0" max="9999"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    @error('editExpReward') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                        <img src="{{ asset('images/Coin.svg') }}" class="h-4 w-4 mr-1.5 shrink-0" alt="">รางวัลเหรียญ
                    </label>
                    <input wire:model.live.debounce.100ms="editCoinReward" type="number" min="0" max="9999"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
                    @error('editCoinReward') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                @if($editType !== 'attendance')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">วันกำหนดส่ง</label>
                        <div wire:ignore x-data="datetimePicker({ wireModel: 'editDueDate', placeholder: 'เลือกวันและเวลากำหนดส่ง' })" class="relative">
                            <input x-ref="inputEl" type="text"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 pr-9 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white cursor-pointer"
                                placeholder="{{ 'เลือกวันและเวลากำหนดส่ง' }}">
                            <button type="button" x-show="$wire.editDueDate" x-cloak @click="clear()"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <x-icon name="x-mark" class="h-4 w-4" />
                            </button>
                        </div>
                        @error('editDueDate') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>
        @endif

        @if(!in_array($editType, ['announcement', 'material', 'topic']))
            <!-- Allow late submission toggle -->
            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input wire:model.live="editAllowLateSubmission" type="checkbox" class="sr-only peer">
                    <div
                        class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-1 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600">
                    </div>
                </label>
                <span class="text-sm text-gray-700">
                    {{ $editType === 'attendance' ? 'อนุญาตให้เช็คชื่อสาย (หลังจากครูปิดเซสชัน)' : 'อนุญาตให้ส่งงานล่าช้า' }}
                </span>
            </div>
        @endif

        @if($editType !== 'attendance')
            <!-- Attachments (existing + new) -->
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
                    <label for="edit-file-upload"
                        class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-[#dedee5] border-dashed rounded-xl cursor-pointer bg-[#f9f9fb] hover:bg-(--cw-faint) hover:border-(--cw-color) transition-colors {{ $editFile ? 'border-(--cw-color) bg-(--cw-faint)' : '' }}">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <x-icon :name="$editFile ? 'check-circle' : 'arrow-up-tray'" class="h-7 w-7 mb-2 text-[#9497a9] {{ $editFile ? 'text-(--cw-color)' : '' }}" />
                            <p class="mb-1 text-sm text-[#686b82]">
                                @if($editFile)
                                    <span class="font-bold text-(--cw-color)">{{ 'เลือกไฟล์แล้ว' }}</span>
                                @else
                                    <span class="font-bold">{{ 'คลิกเพื่ออัปโหลด' }}</span> {{ 'หรือลากและวางไฟล์' }}
                                @endif
                            </p>
                            @if(!$editFile)
                                <p class="text-xs text-[#9497a9]">{{ 'PDF, DOCX, PPTX, JPG, PNG (สูงสุด 25MB)' }}</p>
                            @endif
                        </div>
                        <input id="edit-file-upload" type="file" wire:model.live="editFile" class="hidden" />
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

                @error('editFile')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror

                <p x-show="uploadError" x-cloak x-text="uploadError" class="mt-2 text-xs text-red-500"></p>

                @if($assignment->attachments->count())
                    <div class="mt-3 space-y-2">
                        <h4 class="text-xs font-semibold text-[#686b82] uppercase tracking-wider mb-2">
                            {{ 'ไฟล์แนบปัจจุบัน' }} ({{ $assignment->attachments->count() }})
                        </h4>
                        @foreach($assignment->attachments as $attachment)
                            <div class="flex items-center justify-between p-3 bg-[#f9f9fb] border border-[#dedee5] rounded-xl group hover:border-(--cw-color)/30 transition-colors"
                                wire:key="edit-attach-{{ $attachment->id }}">
                                <div class="flex items-center space-x-3 overflow-hidden">
                                    <div class="shrink-0 w-9 h-9 rounded-lg bg-(--cw-faint) text-(--cw-color) flex items-center justify-center">
                                        <x-icon :name="$attachment->icon" class="h-5 w-5" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-[#101114] truncate">
                                            {{ $attachment->file_name }}
                                        </p>
                                        <p class="text-xs text-[#9497a9]">
                                            {{ $attachment->formatted_size }}
                                        </p>
                                    </div>
                                </div>
                                <button type="button"
                                    @click="deleteFileId = {{ $attachment->id }}; deleteFileName = @js($attachment->file_name); deleteFileType = 'edit'; showDeleteFileModal = true"
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

        <div>
            @php
                $statusOptions = ['draft' => 'ฉบับร่าง', 'published' => 'เผยแพร่แล้ว', 'scheduled' => 'เผยแพร่ตามเวลา'];
            @endphp
            <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ</label>
            <div class="dropdown dropdown-top w-full">
                <div tabindex="0" role="button"
                    class="w-full flex items-center justify-between border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                    <span>{{ $statusOptions[$editStatus] ?? $editStatus }}</span>
                    <x-icon name="chevron-down" class="h-4 w-4 text-gray-400" />
                </div>
                <ul tabindex="0" class="dropdown-content z-10 mb-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-1">
                    @foreach($statusOptions as $value => $label)
                        <li>
                            <button type="button" wire:click="$set('editStatus', '{{ $value }}')"
                                onclick="document.activeElement.blur()"
                                class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors {{ $editStatus === $value ? 'text-blue-600 font-medium' : 'text-gray-700' }}">
                                {{ $label }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
            @error('editStatus') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-end gap-3 mt-auto">
            <button wire:click="cancelEditTab" type="button"
                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-lg border border-[#dedee5] bg-white text-[#686b82] hover:bg-gray-100 hover:text-[#101114] transition-colors cursor-pointer">{{ 'ยกเลิก' }}</button>
            <button type="submit" :disabled="!isDirty"
                class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold rounded-lg text-white transition-all cursor-pointer shadow-sm disabled:cursor-not-allowed disabled:opacity-40"
                :class="isDirty ? 'bg-(--cw-color) hover:opacity-90 active:scale-[0.98]' : 'bg-gray-400'">
                <x-icon name="check" class="h-4 w-4 mr-1.5" />
                <span wire:loading.remove wire:target="saveAssignment">{{ 'บันทึกการแก้ไข' }}</span>
                <span wire:loading wire:target="saveAssignment"><x-icon name="spinner" class="h-4 w-4 mr-1.5 animate-spin" />{{ 'กำลังบันทึก...' }}</span>
            </button>
        </div>
    </form>
</div>
