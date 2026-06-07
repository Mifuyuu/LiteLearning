<div>
    <div>

        {{-- Breadcrumb --}}
        <nav class="flex items-center text-sm text-gray-500 mb-6 flex-wrap gap-1">
            <a href="{{ route('classrooms') }}" class="hover:text-indigo-600 transition-colors">{{ auth()->user()->isTeacher() ? __('ชั้นเรียนของฉัน') : __('ห้องเรียน') }}</a>
            <i class="fas fa-chevron-right text-[10px] mx-1"></i>
            <a href="{{ route('classroom.show', $classroom) }}"
                class="hover:text-indigo-600 transition-colors truncate max-w-[200px]">{{ $classroom->name }}</a>
            <i class="fas fa-chevron-right text-[10px] mx-1"></i>
            <span class="text-gray-800 font-medium">{{ __('Create Material') }}</span>
        </nav>

        {{-- Back link --}}
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate
            class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('Back to classroom') }}
        </a>

        <form wire:submit.prevent="save" class="space-y-5">

            {{-- Title --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Title') }}</label>
                <input type="text" wire:model="title"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="{{ __('Material title') }}">
                @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Description (Tiptap editor) --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description') }}
                    <span class="text-gray-400 font-normal">({{ __('Optional') }})</span>
                </label>
                <div wire:ignore x-data="tiptapEditor({ wireModel: 'description', placeholder: '{{ __('Add a description...') }}' })">
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
                    <i class="fas fa-paperclip mr-1 text-gray-400"></i> {{ __('Attachments') }}
                    <span class="text-gray-400 font-normal">({{ __('Optional') }})</span>
                </label>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-400 transition-colors"
                    x-data="{ dragging: false }"
                    x-on:dragover.prevent="dragging = true"
                    x-on:dragleave.prevent="dragging = false"
                    x-on:drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                    :class="{ 'border-indigo-400 bg-indigo-50': dragging }">
                    <i class="fas fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-500">{{ __('Drag & drop files or') }}</p>
                    <label class="cursor-pointer text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                        {{ __('Browse') }}
                        <input type="file" wire:model="file" class="hidden" x-ref="fileInput">
                    </label>
                    <p class="text-xs text-gray-400 mt-1">{{ __('Max 25MB per file') }}</p>
                </div>

                @if(count($uploadedFiles) > 0)
                    <div class="mt-3 space-y-2">
                        @foreach($uploadedFiles as $index => $uploaded)
                            <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <i class="fas fa-file text-gray-400"></i>
                                    <span class="text-sm text-gray-700 truncate">{{ $uploaded['name'] }}</span>
                                    <span class="text-xs text-gray-400">({{ number_format($uploaded['size'] / 1024, 0) }} KB)</span>
                                </div>
                                <button type="button" wire:click="removeFile({{ $index }})"
                                    class="text-red-400 hover:text-red-600 transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Topic --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-bookmark mr-1 text-gray-400"></i> {{ __('Topic') }}
                    <span class="text-gray-400 font-normal">({{ __('Optional') }})</span>
                </label>
                <input type="text" wire:model="topic" list="topics-list"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="{{ __('Choose or create a topic') }}">
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
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                    class="btn-3d btn-3d--indigo inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-check mr-2"></i> {{ __('Post Material') }}
                </button>
            </div>
        </form>
    </div>
</div>
