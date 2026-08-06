@php
    $isManager = $classroom->canManageClassroom(auth()->user());
    $submittedCount = $assignment->submittedCount();
    $studentCount = $classroom->students->count();
@endphp

<a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}" wire:navigate
    class="group block rounded-2xl border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-5 transition hover:border-[rgba(37,99,235,0.3)] hover:shadow-[rgba(0,0,0,0.06)_0px_4px_24px]">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] bg-[var(--ll-blue-subtle)] text-[var(--ll-blue)]">
                    <x-icon name="document-text" class="h-5 w-5" />
                </span>
                <div class="min-w-0">
                    <h4 class="truncate text-lg font-semibold text-[#101114] group-hover:text-[var(--ll-blue)]">{{ $assignment->title }}</h4>
                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-[#686b82]">
                        <span>{{ $assignment->typeLabel() }}</span>
                        @if($assignment->due_date)
                            <span class="inline-flex items-center gap-1 rounded-[6px] bg-[rgba(104,107,130,0.12)] px-2.5 py-1 text-[#484b5e]">
                                <x-icon name="clock" class="h-3 w-3" />
                                {{ 'กำหนดส่ง ' . $assignment->due_date->format('d M Y H:i') }}
                            </span>
                        @endif
                        @if($assignment->topic)
                            <span class="inline-flex items-center gap-1 rounded-[6px] bg-[var(--ll-blue-subtle)] px-2.5 py-1 text-[var(--ll-blue)]">
                                <x-icon name="tag" class="h-3 w-3" />
                                {{ $assignment->topic }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            @if($assignment->description)
                <p class="mt-4 line-clamp-2 text-sm leading-6 text-[#686b82]">{!! \Illuminate\Support\Str::limit(strip_tags($assignment->description), 180) !!}</p>
            @endif
        </div>

        <div class="flex shrink-0 flex-col items-start gap-2 sm:items-end">
            @if($isManager)
                <span class="inline-flex items-center gap-2 rounded-[6px] bg-[rgba(104,107,130,0.12)] px-3 py-1 text-xs font-semibold text-[#484b5e]">
                    <x-icon name="users" class="h-4 w-4" />
                    {{ $submittedCount }}/{{ $studentCount }} {{ 'ส่งแล้ว' }}
                </span>
            @else
                <span class="inline-flex items-center gap-2 rounded-[6px] px-3 py-1 text-xs font-semibold {{ $isCompleted ? 'bg-[rgba(20,158,97,0.16)] text-[#026b3f]' : 'bg-[rgba(104,107,130,0.12)] text-[#484b5e]' }}">
                    <x-icon :name="$isCompleted ? 'check-circle' : 'clock'" class="h-4 w-4" />
                    {{ $isCompleted ? 'เสร็จแล้ว' : 'ยังไม่ทำ' }}
                </span>
            @endif

            @if($assignment->status !== 'published')
                <span class="inline-flex items-center gap-2 rounded-[6px] bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">
                    <x-icon name="flag" class="h-4 w-4" />
                    {{ ucfirst($assignment->status) }}
                </span>
            @endif
        </div>
    </div>
</a>
