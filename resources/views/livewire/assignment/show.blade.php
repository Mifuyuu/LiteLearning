@section('page-title', $assignment->title)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-gray-500 hover:text-indigo-600 transition-colors">{{ __('Classrooms') }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('classroom.show', $classroom) }}" class="text-gray-500 hover:text-indigo-600 transition-colors" title="{{ $classroom->name }}">{{ \Illuminate\Support\Str::limit($classroom->name, 20) }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-semibold" title="{{ $assignment->title }}">{{ \Illuminate\Support\Str::limit($assignment->title, 30) }}</span>
    </nav>
@endsection

<div class="animate__animated animate__fadeIn" x-data="{ copiedToast: false }">
    <!-- Back -->
    <a href="{{ route('classroom.show', $classroom) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i> {{ __('Back to :name', ['name' => $classroom->name]) }}
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            @if(!$isEditTab)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="flex items-start min-w-0">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center shrink-0"
                                 style="background-color: {{ $classroom->theme_color }}20; color: {{ $classroom->theme_color }}">
                                <i class="fas {{ $assignment->typeIcon() }} text-base sm:text-lg"></i>
                            </div>
                            <div class="ml-3 sm:ml-4 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 wrap-break-word">{{ $assignment->title }}</h1>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium shrink-0"
                                          style="background-color: {{ $classroom->theme_color }}20; color: {{ $classroom->theme_color }}">
                                        {{ $assignment->typeLabel() }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-sm text-gray-500">{{ $assignment->user->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $assignment->created_at->translatedFormat('j M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        @if($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
                        <div class="relative ml-auto sm:ml-0" x-data="{ open: false }">
                            <button @click.stop="open = !open" type="button"
                                class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                                <i class="fas fa-ellipsis-vertical"></i>
                            </button>
                            <div x-show="open" x-cloak @click.outside="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 top-full mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                <button @click.stop="navigator.clipboard.writeText('{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}'); open = false; copiedToast = true; setTimeout(() => copiedToast = false, 2000)"
                                        class="w-full flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-link w-5 text-gray-400"></i>
                                    <span class="ml-2">{{ __('Copy link') }}</span>
                                </button>
                                <button wire:click="openEditTab" @click.stop="open = false"
                                   class="w-full flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-pen w-5 text-gray-400"></i>
                                    <span class="ml-2">{{ __('Edit') }}</span>
                                </button>
                                <button wire:click="openDeleteModal" @click.stop="open = false"
                                        class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <i class="fas fa-trash-alt w-5 text-red-400"></i>
                                    <span class="ml-2">{{ __('Delete') }}</span>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-4 flex items-center gap-4 text-sm flex-wrap">
                        <span class="text-gray-500">
                            <i class="fas fa-clock mr-1"></i>
                            {{ __('Due') }}: {{ $assignment->due_date ? $assignment->due_date->translatedFormat('j M Y, H:i') : __('No due date') }}
                        </span>
                        @if($assignment->type !== 'material' && $assignment->type !== 'attendance')
                        <span class="text-gray-500">
                            <i class="fas fa-star mr-1"></i> {{ $assignment->max_score }} {{ __('Points') }}
                        </span>
                        @endif

                        {{-- Late submission indicators --}}
                        @if($assignment->isOverdue())
                            @if($assignment->canSubmitLate())
                            <span class="text-red-500 font-medium text-xs px-2 py-0.5 bg-red-50 rounded-full">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $assignment->overdueDescription() }}
                            </span>
                            <span class="text-amber-600 text-xs px-2 py-0.5 bg-amber-50 rounded-full">
                                {{ __('Late submission allowed') }}
                            </span>
                            @else
                            <span class="text-red-600 font-medium text-xs px-2 py-0.5 bg-red-50 rounded-full">
                                <i class="fas fa-lock mr-1"></i>{{ __('Submissions closed') }}
                            </span>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="p-6">
                    @if($assignment->description)
                    <div class="mb-4 ql-snow">
                        <div class="ql-editor !p-0 text-gray-700">
                            {!! $assignment->description !!}
                        </div>
                    </div>
                    @endif

                    <!-- Attachments -->
                    @if(!empty($assignment->attachments))
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ __('Attachments') }}</h3>
                        <div class="space-y-3">
                            @foreach($assignment->attachments as $attachment)
                            @php
                                /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                                $disk = Storage::disk('s3');
                                $url = $disk->url($attachment['path']);
                                $ext = strtolower(pathinfo($attachment['name'] ?? '', PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                $isVideo = in_array($ext, ['mp4', 'webm', 'mov']);
                                $isPdf = $ext === 'pdf';
                                $attachId = $attachment['id'] ?? 'att';
                                $sizeKb = round(($attachment['size'] ?? 0) / 1024);
                                $sizeLabel = $sizeKb >= 1024 ? round($sizeKb / 1024, 1) . ' MB' : $sizeKb . ' KB';
                                $icon = match(true) {
                                    $isImage => 'fa-image text-green-500',
                                    $isPdf => 'fa-file-pdf text-red-500',
                                    $isVideo => 'fa-file-video text-purple-500',
                                    in_array($ext, ['doc', 'docx']) => 'fa-file-word text-blue-500',
                                    in_array($ext, ['xls', 'xlsx']) => 'fa-file-excel text-green-600',
                                    in_array($ext, ['ppt', 'pptx']) => 'fa-file-powerpoint text-orange-500',
                                    in_array($ext, ['zip', 'rar', '7z']) => 'fa-file-zipper text-yellow-600',
                                    default => 'fa-file text-gray-400',
                                };
                            @endphp
                            <div class="border border-gray-200 rounded-xl overflow-hidden" wire:key="att-{{ $attachId }}">
                                {{-- Image preview --}}
                                @if($isImage)
                                    <a href="{{ $url }}" target="_blank" class="block">
                                        <img src="{{ $url }}" alt="{{ $attachment['name'] }}"
                                             class="w-full max-h-80 object-contain bg-gray-50">
                                    </a>
                                @endif

                                {{-- Video player --}}
                                @if($isVideo)
                                    <video controls class="w-full max-h-96 bg-black">
                                        <source src="{{ $url }}" type="{{ $attachment['mime'] ?? 'video/mp4' }}">
                                        {{ __('Your browser does not support the video tag.') }}
                                    </video>
                                @endif

                                {{-- PDF embed --}}
                                @if($isPdf)
                                    <iframe src="{{ $url }}" class="w-full h-96 border-0"></iframe>
                                @endif

                                {{-- File info bar --}}
                                <a href="{{ $url }}" target="_blank"
                                   class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 transition-colors {{ ($isImage || $isVideo || $isPdf) ? 'border-t border-gray-200' : '' }}">
                                    <i class="fas {{ $icon }} mr-3 text-lg"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-700 truncate">{{ $attachment['name'] }}</p>
                                        <p class="text-xs text-gray-400">{{ $sizeLabel }}</p>
                                    </div>
                                    <i class="fas fa-arrow-up-right-from-square text-gray-400 ml-2"></i>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Attendance session (embedded for attendance type) --}}
            @if($assignment->isAttendance())
            <div class="mt-6">
                @livewire('assignment.attendance', ['classroom' => $classroom, 'assignment' => $assignment], 'attendance-'.$assignment->id)
            </div>
            @endif

            <!-- Teacher: Submissions Table -->
            @if($submissions !== null && ($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin()))
            <div class="mt-6 bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Student Work') }}</h3>
                    <div class="flex gap-4 mt-2 text-sm text-gray-500">
                        <span><i class="fas fa-check-circle text-green-500 mr-1"></i> {{ $assignment->submittedCount() }} {{ __('Turned in') }}</span>
                        <span><i class="fas fa-star text-amber-500 mr-1"></i> {{ $assignment->gradedCount() }} {{ __('Graded') }}</span>
                        @if($assignment->averageScore())
                        <span><i class="fas fa-chart-line text-blue-500 mr-1"></i> {{ __('Average') }}: {{ round($assignment->averageScore()) }}/{{ $assignment->max_score }}</span>
                        @endif
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($submissions as $sub)
                    <div class="flex items-center justify-between p-4 hover:bg-gray-50" wire:key="sub-{{ $sub->id }}">
                        <div class="flex items-center">
                            <img src="{{ $sub->user->avatar_url }}" class="w-9 h-9 rounded-full mr-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $sub->user->name }}</p>
                                <p class="text-xs text-gray-500">
                                    @if($sub->status === 'turned_in')
                                        <span class="text-blue-600">{{ __('Turned in') }} {{ $sub->turned_in_at?->diffForHumans() }}</span>
                                    @elseif($sub->status === 'graded')
                                        <span class="text-green-600">{{ __('Graded') }}: {{ $sub->score }}/{{ $assignment->max_score }}</span>
                                    @elseif($sub->status === 'returned')
                                        <span class="text-purple-600">{{ __('Returned') }}</span>
                                    @else
                                        <span class="text-gray-400">{{ __('Not turned in') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($sub->isTurnedIn())
                        <a href="{{ route('assignment.grade', ['classroom' => $classroom, 'assignment' => $assignment, 'submission' => $sub]) }}"
                           class="px-3 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">
                            {{ $sub->isGraded() ? __('View Grade') : __('Grade') }}
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @else
            {{-- ────────────────────────────────────────────── --}}
            {{-- Edit Tab --}}
            {{-- ────────────────────────────────────────────── --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-5 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Edit Assignment') }}</h3>
                    <button wire:click="cancelEditTab" type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-1.5"></i>{{ __('Back') }}
                    </button>
                </div>

                <form wire:submit="saveAssignment" class="p-5 space-y-4">
                    <!-- Type selector (4 types) -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach([
                            'attendance' => ['icon' => 'fa-clipboard-check', 'label' => __('Attendance')],
                            'file' => ['icon' => 'fa-cloud-arrow-up', 'label' => __('File Upload')],
                            'question' => ['icon' => 'fa-pen-to-square', 'label' => __('Question')],
                            'material' => ['icon' => 'fa-book-open', 'label' => __('Material')],
                        ] as $t => $info)
                        <label class="relative flex flex-col items-center p-3 cursor-pointer rounded-lg border-2 transition-all
                            {{ $editType === $t ? 'border-indigo-500 bg-indigo-50/50' : 'border-gray-100 hover:border-gray-200 bg-gray-50/50 hover:bg-gray-50' }}">
                            <input wire:model.live="editType" type="radio" value="{{ $t }}" class="sr-only">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2 
                                {{ $editType === $t ? 'bg-indigo-100 text-indigo-600' : 'bg-white text-gray-400 shadow-sm' }}">
                                <i class="fas {{ $info['icon'] }} text-lg"></i>
                            </div>
                            <span class="text-xs font-medium text-center {{ $editType === $t ? 'text-indigo-700' : 'text-gray-600' }}">{{ $info['label'] }}</span>
                            @if($editType === $t)
                                <div class="absolute top-2 right-2 w-2 h-2 rounded-full bg-indigo-500"></div>
                            @endif
                        </label>
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title *') }}</label>
                        <input wire:model="editTitle" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('editTitle') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    @if($editType !== 'attendance')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                        <div x-data="{
                                content: @entangle('editDescription'),
                                initialized: false,
                                init() {
                                    const quill = new Quill($refs.editEditor, {
                                        theme: 'snow',
                                        placeholder: '{{ __('Add a description or instructions for this assignment...') }}',
                                        modules: {
                                            toolbar: [
                                                [{ 'header': [1, 2, 3, false] }],
                                                ['bold', 'italic', 'underline', 'strike'],
                                                [{ 'color': [] }],
                                                [{ 'align': [] }],
                                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                                ['link'],
                                                ['clean']
                                            ]
                                        }
                                    });
                                    
                                    // Set initial content if exists
                                    if (this.content) {
                                        quill.root.innerHTML = this.content;
                                    }
                                    
                                    // Listen for Livewire updates (e.g. when opening edit tab)
                                    $watch('content', value => {
                                        if (value !== quill.root.innerHTML && value !== '<p><br></p>') {
                                            quill.root.innerHTML = value || '';
                                        }
                                    });
                                    
                                    // Sync changes to Livewire
                                    quill.on('text-change', () => {
                                        let html = quill.root.innerHTML;
                                        if (html === '<p><br></p>') html = '';
                                        this.content = html;
                                    });
                                }
                            }" 
                            wire:ignore
                            class="border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500"
                        >
                            <div x-ref="editEditor" class="min-h-[150px] text-sm border-0 !border-t border-gray-200"></div>
                        </div>
                        
                        <style>
                            /* Custom Quill styling to better fit Tailwind */
                            .ql-toolbar.ql-snow { border: none !important; background-color: #f9fafb; padding: 10px; }
                            .ql-toolbar.ql-snow .ql-formats { margin-right: 4px !important; }
                            .ql-container.ql-snow { border: none !important; }
                            .ql-editor { font-family: inherit !important; font-size: 0.875rem; color: #374151; min-height: 150px; }
                            .ql-editor:focus { border: none; outline: none; box-shadow: none; }
                        </style>
                        @error('editDescription') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                    @endif

                    @if(!in_array($editType, ['material']))
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $editType === 'attendance' ? __('Attendance Points') : __('Points') }}
                            </label>
                            <input wire:model="editMaxScore" type="number" min="0" max="1000" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('editMaxScore') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        
                        @if($editType !== 'attendance')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Due Date') }}</label>
                            <input wire:model="editDueDate" type="datetime-local"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            @error('editDueDate') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        @endif
                    </div>

                    <!-- Allow late submission toggle -->
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input wire:model="editAllowLateSubmission" type="checkbox" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                        <span class="text-sm text-gray-700">
                            {{ $editType === 'attendance' ? __('Allow late attendance') : __('Allow late submission') }}
                        </span>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Topic') }}</label>
                        <input wire:model="editTopic" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('editTopic') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                        <select wire:model="editStatus" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="draft">{{ __('Draft') }}</option>
                            <option value="published">{{ __('Published') }}</option>
                        </select>
                        @error('editStatus') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button wire:click="cancelEditTab" type="button" class="px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-3d btn-3d--indigo px-5 py-2.5 text-sm font-medium rounded-lg transition-colors">
                            <span wire:loading.remove wire:target="saveAssignment">{{ __('Update Assignment') }}</span>
                            <span wire:loading wire:target="saveAssignment"><i class="fas fa-spinner fa-spin mr-1"></i> {{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        {{-- ────────────────────────────────────────────── --}}
        {{-- Sidebar: Student Submission --}}
        {{-- ────────────────────────────────────────────── --}}
        @if(!$isEditTab && auth()->user()->isStudent() && $assignment->requiresSubmission() && !$assignment->isAttendance())
        <div>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden sticky top-0">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Your Work') }}</h3>
                        @if($userSubmission)
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium capitalize
                                {{ $userSubmission->status === 'turned_in' ? 'bg-blue-100 text-blue-700' :
                                   ($userSubmission->status === 'graded' ? 'bg-green-100 text-green-700' :
                                   ($userSubmission->status === 'returned' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700')) }}">
                                {{ str_replace('_', ' ', $userSubmission->status) }}
                            </span>
                        @else
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-gray-100 text-gray-600">{{ __('Assigned') }}</span>
                        @endif
                    </div>

                    {{-- Overdue warning in sidebar --}}
                    @if($assignment->isOverdue())
                    <div class="mt-2 p-2 rounded-lg {{ $assignment->canSubmitLate() ? 'bg-amber-50 border border-amber-200' : 'bg-red-50 border border-red-200' }}">
                        <p class="text-xs font-medium {{ $assignment->canSubmitLate() ? 'text-amber-700' : 'text-red-700' }}">
                            <i class="fas {{ $assignment->canSubmitLate() ? 'fa-exclamation-triangle' : 'fa-lock' }} mr-1"></i>
                            {{ $assignment->canSubmitLate() ? $assignment->overdueDescription() : __('Submissions closed') }}
                        </p>
                    </div>
                    @endif

                    @if($userSubmission?->isGraded())
                    <div class="mt-3 p-3 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-700">{{ $userSubmission->score }}<span class="text-sm font-normal text-green-600">/{{ $assignment->max_score }}</span></p>
                        @if($userSubmission->feedback)
                        <p class="text-sm text-green-600 mt-1">{{ $userSubmission->feedback }}</p>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="p-4">
                    @if(!$userSubmission || $userSubmission->status === 'assigned' || $userSubmission->status === 'returned')

                    {{-- File upload zone for 'file' type --}}
                    @if($assignment->isFile())
                    <div class="mb-3" x-data="{ isDragging: false }"
                         @dragenter.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @dragover.prevent
                         @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))">
                        <label :class="isDragging ? 'border-indigo-400 bg-indigo-50' : 'border-gray-300 bg-gray-50'"
                               class="flex flex-col items-center justify-center w-full py-6 border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                            <i class="fas fa-cloud-arrow-up text-gray-400 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-500">{{ __('Drag files here or click to upload') }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ __('Max file size: 25MB') }}</p>
                            <input x-ref="fileInput" wire:model="uploadedFiles" type="file" class="hidden" multiple>
                        </label>
                        <div wire:loading wire:target="uploadedFiles" class="mt-2 text-center">
                            <i class="fas fa-spinner fa-spin text-indigo-500 mr-1"></i>
                            <span class="text-sm text-gray-500">{{ __('Uploading...') }}</span>
                        </div>
                        @error('uploadedFiles.*') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Show uploaded submission files --}}
                    @if($userSubmission?->attachments?->count())
                    <div class="space-y-2 mb-3">
                        @foreach($userSubmission->attachments as $attachment)
                        <div class="flex items-center p-2 bg-gray-50 border border-gray-200 rounded-lg" wire:key="file-{{ $attachment->id }}">
                            <i class="fas {{ $attachment->icon }} text-gray-400 mr-2 text-sm"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-700 truncate">{{ $attachment->file_name }}</p>
                                <p class="text-xs text-gray-400">{{ $attachment->formatted_size }}</p>
                            </div>
                            <button wire:click="removeFile({{ $attachment->id }})" wire:confirm="{{ __('Remove this file?') }}"
                                    class="p-1 text-gray-400 hover:text-red-500 rounded transition-colors">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    @else
                    {{-- Text submission for 'question' type --}}
                    <textarea wire:model="submissionContent" rows="6"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-3"
                              placeholder="{{ __('Type your answer here...') }}"
                              @if(!$assignment->canAcceptSubmission()) disabled @endif></textarea>
                    @endif

                    @if($assignment->canAcceptSubmission())
                    <div class="flex flex-col gap-2">
                        <button wire:click="turnIn"
                                class="btn-3d btn-3d--indigo w-full py-2.5 text-sm font-medium rounded-lg transition-colors">
                            <span wire:loading.remove wire:target="turnIn">{{ __('Turn In') }}</span>
                            <span wire:loading wire:target="turnIn"><i class="fas fa-spinner fa-spin mr-1"></i> {{ __('Submitting...') }}</span>
                        </button>
                        @if(!$assignment->isFile())
                        <button wire:click="saveDraft"
                                class="w-full py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            {{ __('Save Draft') }}
                        </button>
                        @endif
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-lock text-red-300 text-2xl mb-2"></i>
                        <p class="text-sm text-red-500 font-medium">{{ __('Submissions closed') }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $assignment->overdueDescription() }}</p>
                    </div>
                    @endif

                    @elseif($userSubmission->status === 'turned_in')
                    <div class="text-center">
                        <i class="fas fa-check-circle text-blue-500 text-3xl mb-2"></i>
                        <p class="text-sm text-gray-700 font-medium">{{ __('Turned in') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $userSubmission->turned_in_at?->translatedFormat('j M, H:i') }}</p>
                        <button wire:click="unsubmit"
                                class="mt-3 w-full py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                            {{ __('Unsubmit') }}
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Sidebar info for materials or teacher -->
        @if(!$isEditTab && ($assignment->type === 'material' || ($classroom->isOwnedBy(auth()->user()) && $assignment->requiresSubmission())))
        <div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 sticky top-0">
                @if($assignment->requiresSubmission())
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Max Score') }}</span>
                        <span class="font-semibold text-gray-900">{{ $assignment->max_score }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Turned in') }}</span>
                        <span class="font-semibold text-gray-900">{{ $assignment->submittedCount() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Graded') }}</span>
                        <span class="font-semibold text-gray-900">{{ $assignment->gradedCount() }}</span>
                    </div>
                    @if($assignment->averageScore())
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Average') }}</span>
                        <span class="font-semibold text-gray-900">{{ round($assignment->averageScore(), 1) }}</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <span class="text-gray-500">{{ __('Allow late submission') }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $assignment->allow_late_submission ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $assignment->allow_late_submission ? __('Yes') : __('No') }}
                        </span>
                    </div>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-book-open text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500">{{ __('This is a material - no submission required.') }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    @if($showDeleteModal)
    <div class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/60" wire:click="closeDeleteModal">
        <div class="w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden" wire:click.stop>
            <div class="px-6 py-5 border-b border-gray-100">
                <h4 class="text-base font-semibold text-gray-900">{{ __('Delete Assignment') }}</h4>
                <p class="text-sm text-gray-500 mt-1">{{ __('Are you sure you want to delete this assignment?') }}</p>
            </div>

            <div class="px-6 py-5">
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="closeDeleteModal" class="inline-flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-xmark mr-1.5"></i>{{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="deleteAssignment" wire:loading.attr="disabled" class="px-4 py-2 text-sm text-white bg-red-500 rounded-lg hover:bg-red-700 disabled:opacity-50 transition-colors">
                        <span wire:loading.remove wire:target="deleteAssignment" class="inline-flex items-center">
                            <i class="fas fa-trash-alt mr-1.5"></i>{{ __('Delete') }}
                        </span>
                        <span wire:loading wire:target="deleteAssignment" class="inline-flex items-center">
                            <i class="fas fa-spinner fa-spin mr-1.5"></i>{{ __('Deleting...') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Copied Toast -->
    <div x-show="copiedToast" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="fixed bottom-6 left-1/2 -translate-x-1/2 z-60 px-4 py-2.5 bg-gray-800 text-white text-sm rounded-lg shadow-lg flex items-center gap-2">
        <i class="fas fa-check-circle text-green-400"></i>
        {{ __('Link copied!') }}
    </div>

</div>
