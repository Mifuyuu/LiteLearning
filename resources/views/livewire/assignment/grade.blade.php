@section('page-title', 'ให้คะแนน')
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classroom.show', $classroom) }}"
            class="text-gray-500 hover:text-blue-600 transition-colors">...</a>
        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
        <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}"
            class="text-gray-500 hover:text-blue-600 transition-colors"
            title="{{ $assignment->title }}">{{ \Illuminate\Support\Str::limit($assignment->title, 10, '..') }}</a>
        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
        <span class="text-gray-800 font-semibold">ให้คะแนน</span>
    </nav>
@endsection

@php
    $themeColor = $classroom->themeCategory?->color ?? \App\Models\ThemeCategory::fallbackFor($classroom->id)['color'];
@endphp

<div class="max-w-4xl mx-auto" style="--cw-color: {{ $themeColor }}; --cw-subtle: {{ $themeColor }}26; --cw-faint: {{ $themeColor }}12;">
    <div class="rounded-2xl border-3 border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh-3rem)] flex flex-col">
        {{-- Header --}}
        <div class="p-4 sm:p-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] shrink-0"
                    style="background-color: {{ $themeColor }}20; color: {{ $themeColor }};">
                    <x-icon name="check-circle" class="h-5 w-5" />
                </div>
                <div class="min-w-0">
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">ให้คะแนน</h1>
                    <p class="text-sm text-gray-500 truncate">{{ $assignment->title }}</p>
                </div>
            </div>
            <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}"
                class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] shrink-0 border border-[#dedee5] text-[#686b82] hover:text-[#101114] hover:bg-gray-100 transition-colors ml-auto"
                title="กลับไปที่ {{ $assignment->title }}">
                <x-icon name="arrow-left" class="h-5 w-5" />
            </a>
        </div>

        <div class="border-t border-[#dedee5] p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1">
            {{-- Submission Content --}}
            <div class="lg:col-span-2">
                <div class="flex items-center mb-4">
                    <img src="{{ $submission->user->avatar_url }}" class="w-10 h-10 rounded-full mr-3">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $submission->user->name }}</p>
                        <p class="text-xs text-gray-500">
                            ส่งแล้ว {{ $submission->turned_in_at?->translatedFormat('j M Y, H:i') }}
                        </p>
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-700 mb-2">คำตอบของนักเรียน</h3>
                @if($submission->content)
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $submission->content }}</p>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">ไม่มีคำตอบที่เป็นข้อความ</p>
                @endif

                @if($submission->attachments->count())
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">ไฟล์แนบ</h3>
                        <div class="space-y-2">
                            @foreach($submission->attachments as $attachment)
                                <a href="{{ $attachment->url }}" target="_blank"
                                    class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100">
                                    <x-icon :name="$attachment->icon" class="h-4 w-4 text-gray-400 mr-3" />
                                    <span class="text-sm text-gray-700">{{ $attachment->file_name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Grading Panel --}}
            <div class="lg:border-l lg:border-[#dedee5] lg:pl-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">ให้คะแนน</h3>

                <div class="space-y-4">
                    {{-- Score --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">คะแนน</label>
                        <div class="flex items-center gap-2">
                            <input wire:model="score" type="number" min="0" max="{{ $assignment->max_score }}"
                                class="w-24 border border-gray-300 rounded-lg px-3 py-2.5 text-center text-lg font-bold focus:ring-1 focus:ring-blue-500">
                            <span class="text-gray-500 text-lg">/</span>
                            <span class="text-lg font-bold text-gray-900">{{ $assignment->max_score }}</span>
                        </div>
                        @error('score') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Feedback --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ข้อเสนอแนะ</label>
                        <textarea wire:model="feedback" rows="4"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-blue-500"
                            placeholder="เพิ่มข้อเสนอแนะสำหรับนักเรียน..."></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-2 pt-2">
                        <button wire:click="grade"
                            class="btn-3d btn-3d--blue w-full py-2.5 text-sm font-medium rounded-lg transition-colors">
                            <span wire:loading.remove wire:target="grade">
                                    {{ $submission->isGraded() ? 'อัปเดตคะแนน' : 'บันทึกคะแนน' }}
                            </span>
                            <span wire:loading wire:target="grade"><x-icon name="spinner" class="h-4 w-4 mr-1 animate-spin" />
                                กำลังบันทึก...</span>
                        </button>

                        @if($submission->isTurnedIn() || $submission->status === 'returned')
                            <button wire:click="returnSubmission"
                                class="w-full py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                {{ $submission->status === 'returned' ? 'ส่งคืนพร้อมข้อเสนอแนะใหม่' : 'ส่งคืนนักเรียน' }}
                            </button>
                        @endif
                    </div>

                    @if($submission->isGraded())
                        <div class="mt-2 p-3 bg-green-50 rounded-lg text-center">
                            <x-icon name="check-circle" class="h-5 w-5 text-green-500 mb-1" />
                            <p class="text-sm text-green-700 font-medium">ให้คะแนนแล้ว
                                {{ $submission->graded_at?->translatedFormat('j M, H:i') }}
                            </p>
                        </div>
                    @elseif($submission->status === 'returned')
                        <div class="mt-2 p-3 bg-amber-50 rounded-lg text-center">
                            <x-icon name="arrow-uturn-left" class="h-5 w-5 text-amber-500 mb-1 inline-block" />
                            <p class="text-sm text-amber-700 font-medium">ส่งคืนงานให้นักเรียนแก้ไขแล้ว</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
