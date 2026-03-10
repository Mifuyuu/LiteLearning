@section('page-title', $type === 'announcement' ? __('Create Announcement') : __('Create Assignment'))
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ auth()->user()->isTeacher() ? __('ชั้นเรียนของฉัน') : __('ห้องเรียน') }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('classroom.show', $classroom) }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ $classroom->name }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span
            class="text-gray-800 font-semibold">{{ $type === 'announcement' ? __('Create Announcement') : __('Create Assignment') }}</span>
    </nav>
@endsection

<div class="max-w-3xl mx-auto animate__animated animate__fadeIn">
    <!-- Back -->
    <a href="{{ route('classroom.show', $classroom) }}"
        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i> {{ __('Back to :name', ['name' => $classroom->name]) }}
    </a>

    <form wire:submit="save" class="space-y-5">
        <!-- Type Badge (read-only) -->
        @php
            $typeInfo = [
                'announcement' => ['icon' => 'fa-bullhorn', 'label' => 'Announcement', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                'question' => ['icon' => 'fa-pen-to-square', 'label' => 'Question', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-700'],
                'file' => ['icon' => 'fa-cloud-arrow-up', 'label' => 'File Upload', 'bg' => 'bg-teal-100', 'text' => 'text-teal-700'],
                'attendance' => ['icon' => 'fa-clipboard-check', 'label' => 'Attendance', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
                'material' => ['icon' => 'fa-book-open', 'label' => 'Material', 'bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
                'topic' => ['icon' => 'fa-layer-group', 'label' => 'Topic', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
                'project' => ['icon' => 'fa-diagram-project', 'label' => 'Project', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
            ];
            $current = $typeInfo[$type] ?? ['icon' => 'fa-pen-to-square', 'label' => ucfirst($type), 'bg' => 'bg-gray-100', 'text' => 'text-gray-700'];
        @endphp
        <div class="flex items-center gap-3 bg-white px-5 py-3.5 rounded-xl border border-gray-200">
            <div
                class="w-9 h-9 rounded-full flex items-center justify-center {{ $current['bg'] }} {{ $current['text'] }}">
                <i class="fas {{ $current['icon'] }}"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 leading-none mb-0.5">{{ __('Type') }}</p>
                <p class="text-sm font-semibold {{ $current['text'] }}">{{ __($current['label']) }}</p>
            </div>
        </div>

        <!-- Title Card -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">{{ __('Title') }} *</h3>
            </div>
            <div class="p-6">
                <input wire:model="title" type="text" maxlength="50"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="{{ __('Title') }} *">
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
                        {{ $type === 'announcement' ? __('Content') : __('Description') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div wire:ignore
                        x-data="quillEditor({ wireModel: 'description', placeholder: '{{ __('Add a description or instructions for this assignment...') }}' })">
                        <div x-ref="editorEl" class="min-h-[150px]"></div>
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
                    <h3 class="text-sm font-semibold text-gray-700">{{ __('Attachments') }}</h3>
                </div>
                <div class="p-6">
                    <!-- Dropzone Area -->
                    <div class="w-full">
                        <label for="file-upload"
                            class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors {{ $file ? 'border-indigo-500 bg-indigo-50' : '' }}">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i
                                    class="fas {{ $file ? 'fa-check-circle text-indigo-500' : 'fa-cloud-arrow-up text-gray-400' }} text-3xl mb-3"></i>
                                <p class="mb-1 text-sm text-gray-500">
                                    @if($file)
                                        <span class="font-semibold text-indigo-600">{{ __('File selected') }}</span>
                                    @else
                                        <span class="font-semibold">{{ __('Click to upload') }}</span>
                                        {{ __('or drag and drop') }}
                                    @endif
                                </p>
                                @if(!$file)
                                    <p class="text-xs text-gray-400">{{ __('PDF, DOCX, PPTX, JPG, PNG (Max. 25MB)') }}</p>
                                @endif
                            </div>
                            <input id="file-upload" type="file" wire:model.live="file" class="hidden" />
                        </label>
                    </div>

                    <!-- Loading State -->
                    <div wire:loading wire:target="file" class="mt-4 w-full">
                        <div
                            class="flex items-center justify-center space-x-2 text-sm text-indigo-600 bg-indigo-50 rounded-lg p-3">
                            <i class="fas fa-circle-notch fa-spin"></i>
                            <span>{{ __('Uploading file...') }}</span>
                        </div>
                    </div>

                    @error('file')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror

                    <!-- Uploaded Files List -->
                    @if(count($uploadedFiles) > 0)
                        <div class="mt-4 space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">
                                {{ __('Files to attach') }} ({{ count($uploadedFiles) }})
                            </h4>
                            @foreach($uploadedFiles as $index => $uploadedFile)
                                <div
                                    class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg shadow-sm group hover:border-indigo-300 transition-colors">
                                    <div class="flex items-center space-x-3 overflow-hidden">
                                        <div
                                            class="shrink-0 w-10 h-10 rounded bg-indigo-50 flex items-center justify-center text-indigo-600">
                                            @php
                                                $mime = $uploadedFile['mime'];
                                                $icon = 'fa-file';
                                                if (str_contains($mime, 'image'))
                                                    $icon = 'fa-file-image';
                                                elseif (str_contains($mime, 'pdf'))
                                                    $icon = 'fa-file-pdf';
                                                elseif (str_contains($mime, 'word'))
                                                    $icon = 'fa-file-word';
                                                elseif (str_contains($mime, 'video'))
                                                    $icon = 'fa-file-video';
                                                elseif (str_contains($mime, 'audio'))
                                                    $icon = 'fa-file-audio';
                                            @endphp
                                            <i class="fas {{ $icon }} text-lg"></i>
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
                                        title="{{ __('Remove') }}">
                                        <i class="fas fa-xmark"></i>
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
                            <i class="fas fa-folder mr-1.5 text-gray-400"></i>{{ __('Topic') }}
                        </label>
                        <input wire:model="topic" type="text" list="topics-list"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="{{ __('Select or create a topic') }}">
                        <datalist id="topics-list">
                            @foreach($this->topics as $t)
                                <option value="{{ $t->name }}">
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Due Date + EXP + Coin row -->
                    @if(!in_array($type, ['material', 'topic']))
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    <i class="fas fa-clock mr-1.5 text-gray-400"></i>{{ __('Due Date') }}
                                </label>
                                <input wire:model="due_date" type="datetime-local"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    <i class="gsi-flash-lime text-green-500 mr-1.5"
                                        style="font-size:1.1em"></i>{{ __('EXP Reward') }}
                                </label>
                                <input wire:model="exp_reward" type="number" min="0" max="9999"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    <i class="gsi-gemstone-blue text-blue-500 mr-1.5"
                                        style="font-size:1.1em"></i>{{ __('Coin Reward') }}
                                </label>
                                <input wire:model="coin_reward" type="number" min="0" max="9999"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Allow late submission -->
                            <div class="flex items-center gap-3 pt-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input wire:model="allow_late_submission" type="checkbox" class="sr-only peer">
                                    <div
                                        class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                                    </div>
                                </label>
                                <span class="text-sm text-gray-700">
                                    {{ $type === 'attendance' ? __('Allow late attendance') : __('Allow late submission') }}
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
                    <button type="button" wire:click="$set('status', 'draft')" wire:click="save"
                        class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-file-lines mr-1.5"></i>{{ __("Save as Draft") }}
                    </button>
                @else
                    <div></div>
                @endif
                <button type="submit"
                    class="btn-3d btn-3d--indigo px-6 py-2.5 text-sm font-medium rounded-lg transition-colors">
                    <span wire:loading.remove wire:target="save"><i
                            class="fas fa-paper-plane mr-1.5"></i>{{ $type === 'announcement' ? __('Post') : __('Assign') }}</span>
                    <span wire:loading wire:target="save"><i
                            class="fas fa-spinner fa-spin mr-1.5"></i>{{ $type === 'announcement' ? __('Posting...') : __('Assigning...') }}</span>
                </button>
            </div>
        </div>
    </form>
</div>