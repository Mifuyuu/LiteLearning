@php
    $themeColor = $classroom->themeCategory?->color ?? \App\Models\ThemeCategory::fallbackFor($classroom->id)['color'];
@endphp

<a href="{{ route('material.show', ['classroom' => $classroom, 'material' => $material]) }}" wire:navigate
    class="group block rounded-lg border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-5 transition hover:shadow-[rgba(0,0,0,0.06)_0px_4px_24px]"
    style="--card-theme: {{ $themeColor }};"
    onmouseover="this.style.borderColor='{{ $themeColor }}60'"
    onmouseout="this.style.borderColor='#dedee5'">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] bg-(--cw-subtle) text-(--cw-color)"
                    style="background-color: {{ $themeColor }}20; color: {{ $themeColor }};">
                    <x-icon name="book-open" class="h-5 w-5" />
                </span>
                <div class="min-w-0">
                    <h4 class="truncate text-lg font-semibold text-[#101114] transition-colors group-hover:text-(--cw-color)">{{ $material->title }}</h4>
                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-[#686b82]">
                        @if($material->attachments->count())
                            <span class="inline-flex items-center gap-1 rounded-md bg-[rgba(104,107,130,0.12)] px-2.5 py-1 text-[#484b5e]">
                                <x-icon name="paperclip" class="h-3 w-3" />
                                {{ $material->attachments->count() }} {{ 'ไฟล์แนบ' }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 rounded-md px-2.5 py-1"
                            style="background-color: {{ $themeColor }}20; color: {{ $themeColor }};">
                            <x-icon name="tag" class="h-3 w-3" />
                            {{ 'สื่อการสอน' }}
                        </span>
                    </div>
                </div>
            </div>

            @if($material->description)
                <p class="mt-4 line-clamp-2 text-sm leading-6 text-[#686b82]">{!! \Illuminate\Support\Str::limit(strip_tags($material->description), 180) !!}</p>
            @endif
        </div>

        <div class="flex shrink-0 flex-col items-start gap-2 sm:items-end">
            <span class="inline-flex items-center gap-1.5 rounded-md bg-(--cw-subtle) text-(--cw-color) px-3 py-1 text-sm font-semibold">
                <x-icon name="eye" class="h-3.5 w-3.5" />
                {{ 'สื่อการสอน' }}
            </span>
        </div>
    </div>
</a>
