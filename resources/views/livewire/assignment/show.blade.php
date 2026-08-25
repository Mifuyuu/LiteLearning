@section('page-title', $assignment->title)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}"
            class="text-gray-500 hover:text-blue-600 transition-colors">{{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}</a>
        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
        <a href="{{ route('classroom.show', $classroom) }}" class="text-gray-500 hover:text-blue-600 transition-colors"
            title="{{ $classroom->name }}">{{ \Illuminate\Support\Str::limit($classroom->name, 15, '..') }}</a>
        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
        <span class="text-gray-800 font-semibold"
            title="{{ $assignment->title }}">{{ \Illuminate\Support\Str::limit($assignment->title, 25, '..') }}</span>
    </nav>
@endsection

@php
    $themeColor = $classroom->themeCategory?->color ?? \App\Models\ThemeCategory::fallbackFor($classroom->id)['color'];
@endphp

<div class="max-w-4xl mx-auto" style="--cw-color: {{ $themeColor }}; --cw-subtle: {{ $themeColor }}26; --cw-faint: {{ $themeColor }}12;" x-data="{ copiedToast: false, showDeleteModal: false, showDeleteFileModal: false, deleteFileId: null, deleteFileName: '', deleteFileType: 'submission' }">
    @if(!$isEditTab)
        <div class="rounded-2xl border-3 border-[#dedee5] bg-white relative z-10 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh-3rem)] flex flex-col">
            <!-- Header -->
            <div class="p-4 sm:p-6">
                <div class="flex flex-row items-start justify-between gap-3">
                    <div class="flex items-start min-w-0">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] shrink-0"
                            style="background-color: {{ $themeColor }}20; color: {{ $themeColor }}">
                            @if($assignment->type === 'attendance')
                                <x-icon name="pencil-square" class="h-5 w-5" />
                            @elseif($assignment->type === 'announcement')
                                <x-icon name="megaphone" class="h-5 w-5" />
                            @elseif($assignment->type === 'material')
                                <x-icon name="book-open" class="h-5 w-5" />
                            @else
                                <x-icon name="document-text" class="h-5 w-5" />
                            @endif
                        </div>
                        <div class="ml-3 sm:ml-4 min-w-0">
                            <div class="flex items-center gap-2 min-w-0">
                                <h1 class="truncate text-lg sm:text-xl font-bold text-gray-900">
                                    {{ $assignment->title }}</h1>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold shrink-0"
                                    style="background-color: {{ $themeColor }}20; color: {{ $themeColor }}">
                                    {{ $assignment->typeLabel() }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-sm text-gray-500">{{ $assignment->user->name }}</span>
                                <span
                                    class="text-xs text-gray-400">{{ $assignment->created_at->translatedFormat('j M Y') }}</span>
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
                                        @click.stop="navigator.clipboard.writeText('{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}'); open = false; copiedToast = true; setTimeout(() => copiedToast = false, 2000)"
                                        class="w-full flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <x-icon name="link" class="h-5 w-5 text-gray-400" />
                                        <span class="ml-2">คัดลอกลิงก์</span>
                                    </button>
                                    <button wire:click="openEditTab" @click.stop="open = false"
                                        class="w-full flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <x-icon name="pencil" class="h-5 w-5 text-gray-400" />
                                        <span class="ml-2">แก้ไข</span>
                                    </button>
                                    <button @click="showDeleteModal = true; open = false"
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

                @if(!$assignment->isAttendance())
                    <div class="mt-4 flex items-center gap-4 text-sm flex-wrap">
                        <span class="text-gray-500">
                            <x-icon name="clock" class="h-4 w-4 mr-1" />
                            กำหนดส่ง:
                            {{ $assignment->due_date ? $assignment->due_date->translatedFormat('j M Y, H:i') : 'ไม่มีกำหนด' }}
                        </span>

                        {{-- Late submission indicators --}}
                        @if($assignment->isOverdue())
                            @if($assignment->canSubmitLate())
                                <span class="text-red-500 font-medium text-xs px-2 py-0.5 bg-red-50 rounded-full">
                                    <x-icon name="exclamation-triangle" class="h-4 w-4 mr-1" />
                                    {{ $assignment->overdueDescription() }}
                                </span>
                                <span class="text-amber-600 text-xs px-2 py-0.5 bg-amber-50 rounded-full">
                                    อนุญาตให้ส่งงานล่าช้า
                                </span>
                            @else
                                <span class="text-red-600 font-medium text-xs px-2 py-0.5 bg-red-50 rounded-full">
                                    <x-icon name="lock" class="h-4 w-4 mr-1" />ปิดรับงานแล้ว
                                </span>
                            @endif
                        @endif
                    </div>
                @endif
            </div>

            <!-- Description & Attachments -->
            @if($assignment->type !== 'attendance' && ($assignment->description || $assignment->attachments->count()))
                <div class="p-6">
                    @if($assignment->description)
                        <div class="prose prose-sm max-w-none text-gray-700 [&_p]:my-0 [&_p]:leading-relaxed">
                            {!! $assignment->description !!}
                        </div>
                    @endif

                <!-- Attachments -->
                @if($assignment->attachments->count())
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">ไฟล์แนบ</h3>
                        <div class="space-y-3">
                            @foreach($assignment->attachments as $attachment)
                                @php
                                    $url = $attachment->url;
                                    $ext = strtolower(pathinfo($attachment->file_name ?? '', PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                    $isVideo = in_array($ext, ['mp4', 'webm', 'mov']);
                                    $isPdf = $ext === 'pdf';
                                    $attachId = $attachment->id;
                                    $sizeKb = round(($attachment->file_size ?? 0) / 1024);
                                    $sizeLabel = $sizeKb >= 1024 ? round($sizeKb / 1024, 1) . ' MB' : $sizeKb . ' KB';
                                    $iconName = match (true) {
                                        $isImage => 'photo',
                                        $isPdf => 'document-text',
                                        $isVideo => 'document-text',
                                        in_array($ext, ['doc', 'docx']) => 'document-text',
                                        in_array($ext, ['xls', 'xlsx']) => 'document-text',
                                        in_array($ext, ['ppt', 'pptx']) => 'document-text',
                                        in_array($ext, ['zip', 'rar', '7z']) => 'document-text',
                                        default => 'document',
                                    };
                                    $iconColor = match (true) {
                                        $isImage => 'text-green-500',
                                        $isPdf => 'text-red-500',
                                        $isVideo => 'text-blue-500',
                                        in_array($ext, ['doc', 'docx']) => 'text-blue-500',
                                        in_array($ext, ['xls', 'xlsx']) => 'text-green-600',
                                        in_array($ext, ['ppt', 'pptx']) => 'text-orange-500',
                                        in_array($ext, ['zip', 'rar', '7z']) => 'text-yellow-600',
                                        default => 'text-gray-400',
                                    };
                                @endphp
                                <div class="border border-gray-200 rounded-xl overflow-hidden"
                                    wire:key="att-{{ $attachId }}">
                                    {{-- Image preview --}}
                                    @if($isImage)
                                        <a href="{{ $url }}" target="_blank" class="block">
                                            <img src="{{ $url }}" alt="{{ $attachment->file_name }}"
                                                class="w-full max-h-80 object-contain bg-gray-50">
                                        </a>
                                    @endif

                                    {{-- Video player --}}
                                    @if($isVideo)
                                        <video controls class="w-full max-h-96 bg-black">
                                            <source src="{{ $url }}" type="{{ $attachment->file_type ?? 'video/mp4' }}">
                                            เบราว์เซอร์ของคุณไม่รองรับการเล่นวิดีโอ
                                        </video>
                                    @endif

                                    {{-- PDF embed --}}
                                    @if($isPdf)
                                        <iframe src="{{ $url }}" class="w-full h-96 border-0"></iframe>
                                    @endif

                                    {{-- File info bar --}}
                                    <a href="{{ $url }}" target="_blank"
                                        class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 transition-colors {{ ($isImage || $isVideo || $isPdf) ? 'border-t border-gray-200' : '' }}">
                                        <x-icon :name="$iconName" class="mr-3 h-5 w-5 {{ $iconColor }}" />
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-700 truncate">{{ $attachment->file_name }}
                                            </p>
                                            <p class="text-xs text-gray-400">{{ $sizeLabel }}</p>
                                        </div>
                                        <x-icon name="arrow-top-right-on-square" class="h-4 w-4 text-gray-400 ml-2" />
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            @endif

            {{-- Assignment info / stats (shown for both students and teachers) --}}
            @if($assignment->requiresSubmission() && !$assignment->isAttendance())
                <div class="border-t border-[#dedee5] p-6">
                    <h3 class="text-sm font-bold text-[#101114] mb-3">{{ 'ข้อมูลงาน' }}</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="rounded-xl border border-[#dedee5] bg-[#f9f9fb] p-3.5">
                            <span class="flex items-center gap-1.5 text-sm text-[#686b82] mb-1 font-medium">
                                <x-icon name="academic-cap" class="h-4 w-4 text-[#9497a9]" />{{ 'คะแนนเต็ม' }}
                            </span>
                            <p class="text-lg font-bold text-[#101114]">{{ $assignment->max_score }}</p>
                        </div>
                        <div class="rounded-xl border border-[#dedee5] bg-[#f9f9fb] p-3.5">
                            <span class="flex items-center gap-1.5 text-sm text-[#686b82] mb-1 font-medium">
                                <x-icon name="bolt" class="h-4 w-4 text-blue-600" />{{ 'รางวัล EXP' }}
                            </span>
                            <p class="text-lg font-bold text-blue-700">{{ $assignment->exp_reward }}</p>
                        </div>
                        <div class="rounded-xl border border-[#dedee5] bg-[#f9f9fb] p-3.5">
                            <span class="flex items-center gap-1.5 text-sm text-[#686b82] mb-1 font-medium">
                                <img src="{{ asset('images/Coin.svg') }}" class="h-4 w-4 shrink-0" alt="">{{ 'รางวัลเหรียญ' }}
                            </span>
                            <p class="text-lg font-bold text-amber-600">{{ $assignment->coin_reward }}</p>
                        </div>
                        <div class="rounded-xl border border-[#dedee5] bg-[#f9f9fb] p-3.5">
                            <span class="flex items-center gap-1.5 text-sm text-[#686b82] mb-1 font-medium">
                                <x-icon name="clock" class="h-4 w-4 text-[#9497a9]" />{{ 'ส่งงานล่าช้า' }}
                            </span>
                            <p class="text-lg font-bold {{ $assignment->allow_late_submission ? 'text-green-600' : 'text-red-500' }}">
                                {{ $assignment->allow_late_submission ? 'อนุญาต' : 'ไม่อนุญาต' }}
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($assignment->type === 'material')
                <div class="border-t border-[#dedee5] p-6 text-center">
                    <x-icon name="book-open" class="h-7 w-7 text-gray-300 mb-2 mx-auto" />
                    <p class="text-sm text-gray-500">{{ 'นี่คือเอกสาร - ไม่ต้องส่งงาน' }}</p>
                </div>
            @endif

            {{-- Attendance session (embedded for attendance type) --}}
            @if($assignment->isAttendance())
                <div class="border-t border-[#dedee5]">
                    @livewire('assignment.attendance', ['classroom' => $classroom, 'assignment' => $assignment], 'attendance-' . $assignment->id)
                </div>
            @endif

            {{-- Student: Your Submission --}}
            @if(auth()->user()->isStudent() && $assignment->requiresSubmission() && !$assignment->isAttendance())
                <div class="border-t border-[#dedee5] p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">งานของคุณ</h3>
                        @if($userSubmission)
                            @php
                                $subStatusClasses = [
                                    'turned_in' => 'bg-blue-100 text-blue-700',
                                    'graded' => 'bg-green-100 text-green-700',
                                    'returned' => 'bg-amber-100 text-amber-800 font-semibold',
                                    'assigned' => 'bg-gray-100 text-gray-700',
                                ];
                                $subStatusLabels = [
                                    'turned_in' => 'ส่งแล้ว',
                                    'graded' => 'ให้คะแนนแล้ว',
                                    'returned' => 'ส่งคืนแล้ว',
                                    'assigned' => 'มอบหมายแล้ว',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $subStatusClasses[$userSubmission->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $subStatusLabels[$userSubmission->status] ?? $userSubmission->status }}
                            </span>
                        @else
                            <span
                                class="text-xs px-2.5 py-1 rounded-full font-medium bg-gray-100 text-gray-600">มอบหมายแล้ว</span>
                        @endif
                    </div>

                    {{-- Overdue warning --}}
                    @if($assignment->isOverdue())
                        <div
                            class="mt-3 p-2 rounded-lg {{ $assignment->canSubmitLate() ? 'bg-amber-50 border border-amber-200' : 'bg-red-50 border border-red-200' }}">
                            <p
                                class="text-xs font-medium {{ $assignment->canSubmitLate() ? 'text-amber-700' : 'text-red-700' }}">
                                <x-icon :name="$assignment->canSubmitLate() ? 'exclamation-triangle' : 'lock'" class="h-4 w-4 mr-1" />
                                {{ $assignment->canSubmitLate() ? $assignment->overdueDescription() : 'ปิดรับงานแล้ว' }}
                            </p>
                        </div>
                    @endif

                    @if($userSubmission?->isGraded())
                        <div class="mt-3 p-3.5 bg-green-50 border border-green-200 rounded-xl">
                            <p class="text-2xl font-bold text-green-700">{{ $userSubmission->score }}<span
                                    class="text-sm font-normal text-green-600">/{{ $assignment->max_score }}</span></p>
                            @if($userSubmission->feedback)
                                <div class="mt-2 pt-2 border-t border-green-200/60">
                                    <p class="text-xs font-semibold text-green-800">ข้อเสนอแนะจากผู้สอน:</p>
                                    <p class="text-sm text-green-700 mt-0.5 whitespace-pre-line leading-relaxed">{{ $userSubmission->feedback }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($userSubmission?->status === 'returned')
                        <div class="mt-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl">
                            <div class="flex items-center gap-2">
                                <x-icon name="arrow-uturn-left" class="h-4 w-4 text-amber-600 shrink-0" />
                                <p class="text-sm font-bold text-amber-800">ผู้สอนได้ส่งงานคืนให้แก้ไข</p>
                            </div>
                            @if($userSubmission->feedback)
                                <div class="mt-2.5 pt-2 border-t border-amber-200/70">
                                    <p class="text-xs font-semibold text-amber-800">ข้อเสนอแนะจากผู้สอน:</p>
                                    <p class="text-sm text-amber-900 mt-1 whitespace-pre-line leading-relaxed">{{ $userSubmission->feedback }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="mt-4 max-w-md">
                        @if(!$userSubmission || $userSubmission->status === 'assigned' || $userSubmission->status === 'returned')

                            {{-- File upload zone for 'file' type --}}
                            @if($assignment->isFile())
                                <div class="mb-3"
                                    x-data="{
                                        isDragging: false,
                                        uploadProgress: {},
                                        get uploading() {
                                             return Object.keys(this.uploadProgress).length > 0;
                                        },
                                        uploadFiles(files) {
                                            if (!files.length) return;
                                            let index = 0;
                                            const uploadNext = () => {
                                                if (index >= files.length) return;
                                                let file = files[index];
                                                this.uploadProgress[file.name] = 0;
                                                $wire.upload('uploadedFile', file,
                                                    () => {
                                                        delete this.uploadProgress[file.name];
                                                        index++;
                                                        uploadNext();
                                                    },
                                                    () => {
                                                        delete this.uploadProgress[file.name];
                                                        index++;
                                                        uploadNext();
                                                    },
                                                    (progress) => { this.uploadProgress[file.name] = progress.detail.progress; }
                                                );
                                            };
                                            uploadNext();
                                        }
                                    }"
                                    @dragenter.prevent="isDragging = true"
                                    @dragleave.prevent="isDragging = false"
                                    @dragover.prevent
                                    @drop.prevent="isDragging = false; uploadFiles($event.dataTransfer.files)">
                                    <label :class="isDragging ? 'border-blue-400 bg-blue-50' : 'border-gray-300 bg-gray-50'"
                                        class="flex flex-col items-center justify-center w-full py-6 border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                                        <x-icon name="arrow-up-tray" class="h-7 w-7 text-gray-400 mb-2" />
                                        <p class="text-sm text-gray-500">ลากไฟล์มาที่นี่หรือคลิกเพื่ออัปโหลด</p>
                                        <p class="text-xs text-gray-400 mt-1">ขนาดไฟล์สูงสุด: 25MB รองรับหลายไฟล์</p>
                                        <input x-ref="fileInput" type="file" class="hidden" multiple
                                            @change="uploadFiles($event.target.files); $event.target.value = ''">
                                    </label>

                                    {{-- Progress bars --}}
                                    <template x-for="(pct, name) in uploadProgress" :key="name">
                                        <div class="mt-2 p-2 bg-gray-50 border border-gray-200 rounded-lg">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs text-gray-600 truncate mr-2" x-text="name"></span>
                                                <span class="text-xs font-medium text-blue-600 shrink-0" x-text="pct + '%'"></span>
                                            </div>
                                            <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-blue-500 rounded-full transition-all duration-200"
                                                    :style="'width: ' + pct + '%'"></div>
                                            </div>
                                        </div>
                                    </template>

                                    @error('uploadedFile') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                </div>

                                {{-- Show uploaded submission files --}}
                                @if($userSubmission?->attachments?->count())
                                    <div class="space-y-2 mb-3">
                                        @foreach($userSubmission->attachments as $attachment)
                                            <div class="flex items-center p-2 bg-gray-50 border border-gray-200 rounded-lg"
                                                wire:key="file-{{ $attachment->id }}">
                                                <x-icon :name="$attachment->icon" class="h-4 w-4 text-gray-400 mr-2" />
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-medium text-gray-700 truncate">{{ $attachment->file_name }}</p>
                                                    <p class="text-xs text-gray-400">{{ $attachment->formatted_size }}</p>
                                                </div>
                                                <button type="button"
                                                    @click="deleteFileId = {{ $attachment->id }}; deleteFileName = @js($attachment->file_name); deleteFileType = 'submission'; showDeleteFileModal = true"
                                                    class="p-1 text-gray-400 hover:text-red-500 rounded transition-colors cursor-pointer"
                                                    title="ลบไฟล์">
                                                    <x-icon name="x-mark" class="h-4 w-4" />
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                {{-- Text submission for 'question' type --}}
                                <textarea wire:model="submissionContent" rows="6"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 mb-3"
                                    placeholder="พิมพ์คำตอบของคุณที่นี่..." @if(!$assignment->canAcceptSubmission())
                                    disabled @endif></textarea>
                            @endif

                            @if($assignment->canAcceptSubmission())
                                <div class="flex flex-col gap-2">
                                    <button wire:click="turnIn"
                                        class="btn-3d btn-3d--blue w-full py-2.5 text-sm font-medium rounded-lg transition-colors">
                                        <span wire:loading.remove wire:target="turnIn">ส่งงาน</span>
                                        <span wire:loading wire:target="turnIn"><x-icon name="spinner" class="h-4 w-4 mr-1 animate-spin" />
                                            กำลังส่ง...</span>
                                    </button>
                                    @if(!$assignment->isFile())
                                        <button wire:click="saveDraft"
                                            class="w-full py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                            บันทึกฉบับร่าง
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <x-icon name="lock" class="h-7 w-7 text-red-300 mb-2" />
                                    <p class="text-sm text-red-500 font-medium">ปิดรับงานแล้ว</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $assignment->overdueDescription() }}</p>
                                </div>
                            @endif

                        @elseif($userSubmission->status === 'turned_in')
                            <div class="text-center">
                                <x-icon name="check-circle" class="h-7 w-7 text-blue-500 mb-2" />
                                <p class="text-sm text-gray-700 font-medium">ส่งแล้ว</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $userSubmission->turned_in_at?->translatedFormat('j M, H:i') }}</p>
                                <button wire:click="unsubmit"
                                    class="mt-3 w-full py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                    ยกเลิกการส่ง
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Teacher: Submissions Table --}}
            @if(!$assignment->isAttendance() && $submissions !== null && ($classroom->canManageClassroom(auth()->user()) || auth()->user()->isAdmin()))
                <div class="border-t border-[#dedee5]">
                    <div class="p-4 sm:p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">งานนักเรียน</h3>
                        <div class="flex gap-4 mt-2 text-sm text-gray-500">
                            <span><x-icon name="check-circle" class="h-4 w-4 mr-1 text-green-500" />
                                 ส่งแล้ว {{ $assignment->submittedCount() }} คน</span>
                            <span><x-icon name="star" class="h-4 w-4 mr-1 text-amber-500" /> {{ $assignment->gradedCount() }}
                                ให้คะแนนแล้ว</span>
                            @if($assignment->averageScore())
                                <span><x-icon name="chart-bar" class="h-4 w-4 mr-1 text-blue-500" /> เฉลี่ย:
                                    {{ round($assignment->averageScore()) }}/{{ $assignment->max_score }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($submissions as $sub)
                            <div class="flex items-center justify-between p-4 sm:px-6 hover:bg-gray-50" wire:key="sub-{{ $sub->id }}">
                                <div class="flex items-center">
                                    <img src="{{ $sub->user->avatar_url }}" class="w-9 h-9 rounded-full mr-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $sub->user->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            @if($sub->status === 'turned_in')
                                                <span class="text-blue-600">ส่งแล้ว
                                                    {{ $sub->turned_in_at?->diffForHumans() }}</span>
                                            @elseif($sub->status === 'graded')
                                                <span class="text-green-600">ให้คะแนนแล้ว:
                                                    {{ $sub->score }}/{{ $assignment->max_score }}</span>
                                            @elseif($sub->status === 'returned')
                                                <span class="text-amber-600 font-semibold">ส่งคืนแล้ว</span>
                                            @else
                                                <span class="text-gray-400">ยังไม่ส่ง</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                @if($sub->isTurnedIn() || $sub->status === 'returned')
                                    <a href="{{ route('assignment.grade', ['classroom' => $classroom, 'assignment' => $assignment, 'submission' => $sub]) }}"
                                        class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                        <x-icon :name="$sub->isGraded() ? 'eye' : ($sub->status === 'returned' ? 'arrow-uturn-left' : 'pencil')" class="h-4 w-4 mr-1" />{{ $sub->isGraded() ? 'ดูคะแนน' : ($sub->status === 'returned' ? 'ดูงานที่ส่งคืน' : 'ให้คะแนน') }}
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        {{-- ────────────────────────────────────────────── --}}
        {{-- Edit Tab --}}
        {{-- ────────────────────────────────────────────── --}}
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

                @if($editType === 'attendance')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ชื่องาน</label>
                        <input wire:model.live.debounce.100ms="editTitle" type="text" readonly disabled
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50 text-gray-500 cursor-not-allowed select-none">
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ชื่องาน *</label>
                        <input wire:model.live.debounce.100ms="editTitle" type="text" maxlength="50"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <div class="mt-1 flex justify-between items-center">
                            @error('editTitle') <p class="text-sm text-red-500">{{ $message }}</p> @else <span></span> @enderror
                            <span class="text-xs" :class="$wire.editTitle.length >= 50 ? 'text-red-500 font-medium' : 'text-gray-400'">
                                <span x-text="$wire.editTitle.length">0</span>/50
                            </span>
                        </div>
                    </div>
                @endif

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
                                <div wire:ignore x-data="datetimePicker({ wireModel: 'editDueDate', placeholder: 'เลือกวันและเวลากำหนดส่ง' })">
                                    <input x-ref="inputEl" type="text"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white cursor-pointer"
                                        placeholder="{{ 'เลือกวันและเวลากำหนดส่ง' }}">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">ไฟล์แนบ</label>

                        @if($assignment->attachments->count())
                            <div class="mb-2 space-y-1">
                                @foreach($assignment->attachments as $attachment)
                                    <div class="flex items-center justify-between border border-gray-200 rounded-lg px-3 py-2"
                                        wire:key="edit-attach-{{ $attachment->id }}">
                                        <div class="flex items-center min-w-0">
                                            <x-icon name="document-text" class="h-4 w-4 text-gray-400 mr-2 shrink-0" />
                                            <span class="text-xs text-gray-700 truncate">{{ $attachment->file_name }}</span>
                                        </div>
                                        <button type="button"
                                            @click="deleteFileId = {{ $attachment->id }}; deleteFileName = @js($attachment->file_name); deleteFileType = 'edit'; showDeleteFileModal = true"
                                            class="text-red-400 hover:text-red-600 shrink-0 ml-2 cursor-pointer"
                                            title="ลบไฟล์">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <label for="edit-file-upload"
                            class="relative flex flex-col items-center justify-center w-full h-24 border-2 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors {{ $editFile ? 'border-blue-500 bg-blue-50' : '' }}">
                            <div class="flex flex-col items-center justify-center py-3">
                                <x-icon :name="$editFile ? 'check-circle' : 'arrow-up-tray'" class="h-6 w-6 mb-2 {{ $editFile ? 'text-blue-500' : 'text-gray-400' }}" />
                                @if($editFile)
                                    <p class="text-sm"><span class="font-semibold text-blue-600">{{ $editFile->getClientOriginalName() }}</span></p>
                                @else
                                    <p class="text-sm text-gray-500"><span class="font-semibold">เพิ่มไฟล์แนบ</span> (PDF, DOCX, PPTX, JPG, PNG · 25MB)</p>
                                @endif
                            </div>
                            <input id="edit-file-upload" type="file" wire:model.live="editFile" class="hidden" />
                        </label>

                        <div x-show="uploading" x-cloak class="mt-2 w-full bg-blue-50 rounded-lg p-2">
                            <div class="h-1.5 bg-blue-100 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 transition-all" :style="`width: ${progress}%`"></div>
                            </div>
                        </div>
                        <p x-show="uploadError" x-cloak x-text="uploadError" class="mt-1 text-xs text-red-500"></p>
                        @error('editFile') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if($editType === 'attendance')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">หัวข้อ</label>
                        <input type="text" readonly disabled value="เช็คชื่อ"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50 text-gray-500 cursor-not-allowed select-none">
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">หัวข้อ</label>
                        <input wire:model.live.debounce.100ms="editTopic" type="text"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        @error('editTopic') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ</label>
                    <select wire:model.live="editStatus"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="draft">ฉบับร่าง</option>
                        <option value="published">เผยแพร่แล้ว</option>
                    </select>
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
    @endif

    <!-- Delete File Modal -->
    <template x-teleport="body">
        <x-confirm-modal show="showDeleteFileModal" cancel="showDeleteFileModal = false" heading="ยืนยันการลบไฟล์">
            <x-slot:message>
                {{ 'คุณต้องการลบไฟล์' }} <span class="font-semibold text-[#101114]" x-text="deleteFileName"></span> {{ 'ใช่หรือไม่?' }}
            </x-slot:message>
            <button type="button"
                @click="
                    if (deleteFileType === 'submission') {
                        $wire.removeFile(deleteFileId);
                    } else if (deleteFileType === 'edit') {
                        $wire.removeEditAttachment(deleteFileId);
                    }
                    showDeleteFileModal = false;
                "
                class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                {{ 'ลบไฟล์' }}
            </button>
        </x-confirm-modal>
    </template>

    <!-- Delete Assignment Modal -->
    <template x-teleport="body">
        <x-confirm-modal show="showDeleteModal" cancel="showDeleteModal = false" heading="ยืนยันการลบงาน" message="คุณแน่ใจหรือว่าต้องการลบงานนี้? การกระทำนี้ไม่สามารถยกเลิกได้">
            <button type="button" @click="$wire.deleteAssignment(); showDeleteModal = false"
                class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                {{ 'ลบงาน' }}
            </button>
        </x-confirm-modal>
    </template>

    <!-- Copied Toast -->
    <div x-show="copiedToast" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-60 px-4 py-2.5 bg-gray-800 text-white text-sm rounded-lg shadow-lg flex items-center gap-2">
        <x-icon name="check-circle" class="h-4 w-4 text-green-400" />
        คัดลอกลิงก์แล้ว!
    </div>

</div>
