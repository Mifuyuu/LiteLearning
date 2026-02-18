@section('page-title', __('Create Assignment'))
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ __('Classrooms') }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('classroom.show', $classroom) }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ $classroom->name }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-semibold">{{ __('Create Assignment') }}</span>
    </nav>
@endsection

<div class="max-w-3xl mx-auto">
    <!-- Back -->
    <a href="{{ route('classroom.show', $classroom) }}"
        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i> {{ __('Back to :name', ['name' => $classroom->name]) }}
    </a>

    <form wire:submit="save" class="space-y-5">
        <!-- Main Card: Title + Instructions -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <!-- Title -->
            <div class="relative">
                <input wire:model="title" type="text"
                    class="w-full px-6 py-4 text-lg font-medium text-gray-900 border-0 border-b-2 border-transparent focus:border-indigo-500 focus:ring-0 placeholder-gray-400 bg-gray-50 focus:bg-white transition-colors"
                    placeholder="{{ __('Title') }} *">
            </div>
            @error('title')
                <p class="px-6 py-1 text-sm text-red-500 bg-red-50">{{ $message }}</p>
            @enderror

            <!-- Instructions -->
            <!-- Instructions (Rich Text) -->
            <div class="relative" x-data="{
                content: @entangle('instructions'),
                init() {
                    // Update content if changed externally (e.g. initial load)
                    this.$watch('content', value => {
                        if (this.$refs.editor.innerHTML !== value) {
                            this.$refs.editor.innerHTML = value;
                        }
                    });
                },
                exec(cmd) {
                    document.execCommand(cmd, false, null);
                    this.content = this.$refs.editor.innerHTML;
                },
                update() {
                    this.content = this.$refs.editor.innerHTML;
                }
            }">
                <div
                    class="border border-gray-300 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-shadow">
                    <!-- Toolbar -->
                    <div class="flex items-center gap-1 p-2 bg-gray-50 border-b border-gray-200">
                        <button type="button" @click="exec('bold')"
                            class="p-1.5 rounded-lg text-gray-600 hover:bg-gray-200 hover:text-gray-900 transition-colors"
                            title="Bold">
                            <i class="fas fa-bold w-4 h-4 text-xs"></i>
                        </button>
                        <button type="button" @click="exec('italic')"
                            class="p-1.5 rounded-lg text-gray-600 hover:bg-gray-200 hover:text-gray-900 transition-colors"
                            title="Italic">
                            <i class="fas fa-italic w-4 h-4 text-xs"></i>
                        </button>
                        <button type="button" @click="exec('underline')"
                            class="p-1.5 rounded-lg text-gray-600 hover:bg-gray-200 hover:text-gray-900 transition-colors"
                            title="Underline">
                            <i class="fas fa-underline w-4 h-4 text-xs"></i>
                        </button>
                        <div class="w-px h-4 bg-gray-300 mx-1"></div>
                        <button type="button" @click="exec('insertUnorderedList')"
                            class="p-1.5 rounded-lg text-gray-600 hover:bg-gray-200 hover:text-gray-900 transition-colors"
                            title="Bullet List">
                            <i class="fas fa-list-ul w-4 h-4 text-xs"></i>
                        </button>
                        <button type="button" @click="exec('removeFormat')"
                            class="p-1.5 rounded-lg text-gray-600 hover:bg-gray-200 hover:text-gray-900 transition-colors"
                            title="Clear Formatting">
                            <i class="fas fa-eraser w-4 h-4 text-xs"></i>
                        </button>
                    </div>
                    <!-- Editor Area -->
                    <div x-ref="editor" contenteditable="true"
                        class="w-full px-6 py-4 text-sm text-gray-700 min-h-[160px] outline-none prose prose-sm max-w-none"
                        @input="update()" placeholder="{{ __('Instructions (optional)') }}">
                        {!! $instructions !!}
                    </div>
                </div>
                <input type="hidden" name="instructions" wire:model="instructions">
            </div>
        </div>

        <!-- Attachments Card -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">{{ __('Attachments') }}</h3>
            </div>

            <div class="p-6">
                <!-- Uploaded files preview -->
                @if(count($uploadedFiles))
                    <div class="space-y-2 mb-4">
                        @foreach($uploadedFiles as $index => $uploaded)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                        @php
                                            $ext = pathinfo($uploaded['name'], PATHINFO_EXTENSION);
                                            $icon = match (true) {
                                                in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']) => 'fa-image text-green-600',
                                                in_array($ext, ['pdf']) => 'fa-file-pdf text-red-600',
                                                in_array($ext, ['doc', 'docx']) => 'fa-file-word text-blue-600',
                                                in_array($ext, ['xls', 'xlsx']) => 'fa-file-excel text-green-700',
                                                in_array($ext, ['ppt', 'pptx']) => 'fa-file-powerpoint text-orange-600',
                                                in_array($ext, ['zip', 'rar', '7z']) => 'fa-file-zipper text-yellow-600',
                                                in_array($ext, ['mp4', 'mov', 'avi']) => 'fa-file-video text-purple-600',
                                                default => 'fa-file text-gray-500',
                                            };
                                            $sizeKb = round($uploaded['size'] / 1024);
                                            $sizeLabel = $sizeKb >= 1024 ? round($sizeKb / 1024, 1) . ' MB' : $sizeKb . ' KB';
                                        @endphp
                                        <i class="fas {{ $icon }} text-sm"></i>
                                    </div>
                                    <div class="ml-3 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $uploaded['name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $sizeLabel }}</p>
                                    </div>
                                </div>
                                <button type="button" wire:click="removeFile({{ $index }})"
                                    class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                                    <i class="fas fa-xmark"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Upload area (single file at a time) -->
                <label
                    class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition-all group">
                    <input type="file" wire:model="file" class="sr-only">
                    <div
                        class="w-12 h-12 rounded-full bg-gray-100 group-hover:bg-indigo-100 flex items-center justify-center transition-colors mb-3">
                        <i
                            class="fas fa-cloud-arrow-up text-xl text-gray-400 group-hover:text-indigo-500 transition-colors"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">{{ __('Upload files') }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">{{ __('Max file size: 25MB') }}</p>
                </label>
                @error('file')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror

                <!-- Upload progress -->
                <div wire:loading wire:target="file" class="mt-3">
                    <div class="flex items-center text-sm text-indigo-600">
                        <i class="fas fa-spinner fa-spin mr-2"></i> {{ __('Uploading...') }}
                    </div>
                </div>
            </div>
        </div>

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

                <!-- Due Date + Points row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            <i class="fas fa-clock mr-1.5 text-gray-400"></i>{{ __('Due Date') }}
                        </label>
                        <input wire:model="due_date" type="datetime-local"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            <i class="fas fa-star mr-1.5 text-gray-400"></i>{{ __('Points') }}
                        </label>
                        <input wire:model="max_score" type="number" min="0" max="1000"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Allow late submission -->
                <div class="flex items-center gap-3 pt-2">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input wire:model="allow_late_submission" type="checkbox" class="sr-only peer">
                        <div
                            class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                        </div>
                    </label>
                    <span class="text-sm text-gray-700">{{ __('Allow late submission') }}</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <button type="button" wire:click="$set('status', 'draft')" wire:click="save"
                class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-file-lines mr-1.5"></i>{{ __('Save as Draft') }}
            </button>
            <button type="submit"
                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                <span wire:loading.remove wire:target="save"><i
                        class="fas fa-paper-plane mr-1.5"></i>{{ __('Assign') }}</span>
                <span wire:loading wire:target="save"><i
                        class="fas fa-spinner fa-spin mr-1.5"></i>{{ __('Assigning...') }}</span>
            </button>
        </div>
    </form>
</div>