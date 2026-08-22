@php
    $themeColor = $classroom->themeCategory?->color ?? \App\Models\ThemeCategory::fallbackFor($classroom->id)['color'];
@endphp

<div>
    <div>

        {{-- Breadcrumb --}}
        <nav class="flex items-center text-sm text-gray-500 mb-6 flex-wrap gap-1">
            <a href="{{ route('classrooms') }}" class="hover:text-blue-600 transition-colors">{{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}</a>
            <x-icon name="chevron-right" class="h-3.5 w-3.5 mx-1" />
            <a href="{{ route('classroom.show', $classroom) }}"
                class="hover:text-blue-600 transition-colors truncate max-w-[200px]">{{ $classroom->name }}</a>
            <x-icon name="chevron-right" class="h-3.5 w-3.5 mx-1" />
            <span class="text-gray-800 font-medium truncate max-w-[250px]">{{ $material->title }}</span>
        </nav>

        {{-- Flash messages --}}
        @if (session()->has('message'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                <x-icon name="check-circle" class="h-4 w-4" /> {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main content --}}
            <div class="lg:col-span-2 space-y-5">

                @if(!$isEditTab)
                    {{-- VIEW MODE --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        {{-- Header --}}
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                        style="background-color: {{ $themeColor }}15;">
                                        <x-icon name="book-open" class="h-5 w-5" style="color: {{ $themeColor }}" />
                                    </div>
                                    <div>
                                        <h1 class="text-xl font-semibold text-gray-900">{{ $material->title }}</h1>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
                                            <span>{{ $material->user->name ?? 'ไม่ทราบชื่อ' }}</span>
                                            <span>&middot;</span>
                                            <span>{{ $material->created_at->translatedFormat('j M Y, H:i') }}</span>
                                            @if($material->updated_at->gt($material->created_at))
                                                <span>&middot;</span>
                                                <span>{{ 'แก้ไขเมื่อ ' . $material->updated_at->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($classroom->canManageClassroom(auth()->user()))
                                    <div class="flex items-center gap-2">
                                        <button wire:click="openEditTab"
                                            class="text-gray-400 hover:text-blue-600 transition-colors p-2 rounded-lg hover:bg-gray-100"
                                            title="{{ 'แก้ไข' }}">
                                            <x-icon name="pencil-square" class="h-4 w-4" />
                                        </button>
                                        <button wire:click="openDeleteModal"
                                            class="text-gray-400 hover:text-red-600 transition-colors p-2 rounded-lg hover:bg-gray-100"
                                            title="{{ 'ลบ' }}">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Description --}}
                        @if($material->description)
                            <div class="p-6 prose prose-sm max-w-none text-gray-700 [&>p]:my-0 [&>p]:leading-relaxed">
                                {!! $material->description !!}
                            </div>
                        @endif
                    </div>

                    {{-- Attachments --}}
                    @if($material->attachments->count())
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3">
                                <x-icon name="paperclip" class="h-4 w-4 mr-1 text-gray-400" /> {{ 'ไฟล์แนบ' }}
                            </h3>
                            <div class="space-y-2">
                                @foreach($material->attachments as $attachment)
                                    <a href="{{ $attachment->url }}" target="_blank"
                                        class="flex items-center gap-3 bg-gray-50 rounded-lg px-4 py-3 hover:bg-gray-100 transition-colors group">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                            <x-icon name="document" class="h-4 w-4 text-blue-500" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-700 truncate group-hover:text-blue-600 transition-colors">{{ $attachment->file_name }}</p>
                                            <p class="text-xs text-gray-400">{{ number_format($attachment->file_size / 1024, 0) }} KB</p>
                                        </div>
                                        <x-icon name="arrow-top-right-on-square" class="h-4 w-4 text-gray-400 group-hover:text-blue-500" />
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                @else
                    {{-- EDIT MODE --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-semibold text-gray-800"><x-icon name="pencil-square" class="h-4 w-4 mr-2 text-blue-500" />{{ 'แก้ไขเอกสาร' }}</h2>
                            <button wire:click="cancelEditTab" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                <x-icon name="x-mark" class="h-4 w-4 mr-1" /> {{ 'ยกเลิก' }}
                            </button>
                        </div>

                        <form wire:submit.prevent="saveMaterial" class="space-y-5">
                            {{-- Title --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ 'หัวข้อ' }}</label>
                                <input type="text" wire:model="editTitle"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @error('editTitle') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Description (Tiptap editor) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ 'รายละเอียด' }}</label>
                                <div wire:ignore x-data="tiptapEditor({ wireModel: 'editDescription', placeholder: '{{ 'เพิ่มคำอธิบาย...' }}' })">
                                    <x-tiptap-toolbar />
                                    <div x-ref="editorEl"
                                        class="min-h-[150px] border border-gray-200 rounded-b-lg p-3 focus:outline-none prose prose-sm max-w-none">
                                    </div>
                                </div>
                                @error('editDescription') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Topic --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ 'หัวข้อ' }}</label>
                                <input type="text" wire:model="editTopic" list="topics-list-edit"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                    placeholder="{{ 'เลือกหรือสร้างหัวข้อ' }}">
                                <datalist id="topics-list-edit">
                                    @foreach($this->topics as $t)
                                        <option value="{{ $t->name }}">
                                    @endforeach
                                </datalist>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button type="button" wire:click="cancelEditTab"
                                    class="btn-3d btn-3d--white inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-lg">
                                    {{ 'ยกเลิก' }}
                                </button>
                                <button type="submit"
                                    class="btn-3d btn-3d--indigo inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-lg">
                                    <x-icon name="check" class="h-4 w-4 mr-2" /> {{ 'บันทึก' }}
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Comments --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    @livewire('classroom.stream-comment', ['contentId' => $material->id, 'contentType' => \App\Models\Material::class], key('material-comment-' . $material->id))
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-5">
                {{-- Info card --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">{{ 'รายละเอียด' }}</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex items-start gap-2">
                            <dt class="text-gray-500 shrink-0 w-20">{{ 'โพสต์เมื่อ' }}</dt>
                            <dd class="text-gray-800">{{ $material->created_at->translatedFormat('j M Y, H:i') }}</dd>
                        </div>
                        <div class="flex items-start gap-2">
                            <dt class="text-gray-500 shrink-0 w-20">{{ 'โดย' }}</dt>
                            <dd class="text-gray-800">{{ $material->user->name ?? 'ไม่ทราบชื่อ' }}</dd>
                        </div>
                        @if($material->attachments->count())
                            <div class="flex items-start gap-2">
                                <dt class="text-gray-500 shrink-0 w-20">{{ 'ไฟล์' }}</dt>
                                <dd class="text-gray-800">{{ $material->attachments->count() }} {{ 'ไฟล์' }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Back to classroom --}}
                <a href="{{ route('classroom.show', $classroom) }}" wire:navigate
                    class="flex items-center justify-center gap-2 text-sm text-gray-600 hover:text-blue-600 bg-white rounded-xl border border-gray-200 shadow-sm p-3 transition-colors">
                    <x-icon name="arrow-left" class="h-4 w-4" /> {{ 'กลับไปที่ชั้นเรียน' }}
                </a>
            </div>
        </div>

        {{-- Delete modal --}}
        @if($showDeleteModal)
            <div class="fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center p-4" x-data x-cloak>
                <div class="bg-white rounded-xl shadow-xl p-6 max-w-sm w-full" @click.outside="$wire.closeDeleteModal()">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ 'ลบเอกสาร' }}</h3>
                    <p class="text-sm text-gray-600 mb-5">{{ 'เอกสารนี้และไฟล์แนบทั้งหมดจะถูกลบอย่างถาวร การดำเนินการนี้ไม่สามารถยกเลิกได้' }}</p>
                    <div class="flex justify-end gap-3">
                        <button wire:click="closeDeleteModal"
                            class="btn-3d btn-3d--white inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg">
                            {{ 'ยกเลิก' }}
                        </button>
                        <button wire:click="deleteMaterial"
                            class="btn-3d btn-3d--red inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg">
                            <x-icon name="trash" class="h-4 w-4 mr-2" /> {{ 'ลบ' }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
