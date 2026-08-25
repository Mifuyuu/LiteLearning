@php
    $isManager = $classroom->canManageClassroom(auth()->user());
    $submittedCount = $assignment->submittedCount();
    $studentCount = $classroom->students->count();
    $themeColor = $classroom->themeCategory?->color ?? \App\Models\ThemeCategory::fallbackFor($classroom->id)['color'];
@endphp

<a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}" wire:navigate
    class="group block rounded-lg border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-5 transition hover:shadow-[rgba(0,0,0,0.06)_0px_4px_24px]"
    style="--card-theme: {{ $themeColor }};"
    onmouseover="this.style.borderColor='{{ $themeColor }}60'"
    onmouseout="this.style.borderColor='#dedee5'">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-[10px]"
                    style="background-color: {{ $themeColor }}20; color: {{ $themeColor }};">
                    <x-icon name="document-text" class="h-5 w-5" />
                </span>
                <div class="min-w-0">
                    <h4 class="truncate text-lg font-semibold text-[#101114] transition-colors group-hover:text-(--cw-color)">{{ $assignment->title }}</h4>
                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-[#686b82]">
                        @if($assignment->due_date)
                            <span class="inline-flex items-center gap-1 rounded-md bg-[rgba(104,107,130,0.12)] px-2.5 py-1 text-[#484b5e]">
                                <x-icon name="clock" class="h-3 w-3" />
                                {{ 'กำหนดส่ง ' . $assignment->due_date->translatedFormat('j M Y H:i') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-md bg-[rgba(104,107,130,0.12)] px-2.5 py-1 text-[#484b5e]">
                                <x-icon name="clock" class="h-3 w-3" />
                                {{ 'ไม่มีกำหนดส่ง' }}
                            </span>
                        @endif
                            <span class="inline-flex items-center gap-1 rounded-md px-2.5 py-1"
                                style="background-color: {{ $themeColor }}20; color: {{ $themeColor }};">
                                <x-icon name="tag" class="h-3 w-3" />
                                {{ $assignment->typeLabel() }}
                            </span>
                    </div>
                </div>
            </div>

            @if($assignment->description)
                <p class="mt-4 line-clamp-2 text-sm leading-6 text-[#686b82]">{!! \Illuminate\Support\Str::limit(strip_tags($assignment->description), 180) !!}</p>
            @endif
        </div>

        <div class="flex shrink-0 flex-col items-start gap-2 sm:items-end">
            @if($isManager)
                <span class="inline-flex items-center gap-2 rounded-md bg-[rgba(104,107,130,0.12)] px-3 py-1 text-xs font-semibold text-[#484b5e]">
                    <x-icon name="users" class="h-4 w-4" />
                    {{ $submittedCount }}/{{ $studentCount }} {{ 'ส่งแล้ว' }}
                </span>
            @else
                @php
                    $isReturned = $submission?->status === 'returned';
                @endphp
                @if($isReturned)
                    <span class="inline-flex items-center gap-2 rounded-md bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-700">
                        <x-icon name="arrow-uturn-left" class="h-4 w-4" />
                        {{ 'ถูกตีกลับ' }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 rounded-md px-3 py-1 text-sm font-semibold {{ $isCompleted ? 'bg-[rgba(20,158,97,0.16)] text-[#026b3f]' : 'bg-[rgba(104,107,130,0.12)] text-[#484b5e]' }}">
                        <x-icon :name="$isCompleted ? 'check-circle' : 'clock'" class="h-4 w-4" />
                        {{ $isCompleted ? 'เสร็จแล้ว' : 'ยังไม่ทำ' }}
                    </span>
                @endif
            @endif

            @if($assignment->status !== 'published')
                <span class="inline-flex items-center gap-2 rounded-md bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">
                    <x-icon name="flag" class="h-4 w-4" />
                    {{ ucfirst($assignment->status) }}
                </span>
            @endif
        </div>
    </div>
</a>
