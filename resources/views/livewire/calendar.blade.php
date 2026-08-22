@section('page-title', 'ปฏิทิน')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh-3rem)]">
        <div class="relative p-6 lg:p-8">
            <img src="{{ asset('images/mailbox.svg') }}" alt=""
                class="absolute right-4 top-4 hidden h-[calc(100%-2rem)] w-auto select-none object-contain sm:block lg:right-8" />

            <div>
                <h1 class="text-3xl font-black tracking-tight text-[#101114] sm:text-4xl">{{ 'ปฏิทิน' }}</h1>
                <p class="mt-2 max-w-2xl text-md leading-6 text-[#686b82]">
                    {{ 'ติดตามภารกิจที่ยังไม่ได้ทำทั้งหมด' }}
                </p>
            </div>
        </div>

        @if($upcoming->isEmpty())
            <div class="border-t border-[#dedee5] p-16 text-center">
                {{-- <img src="{{ asset('images/spacesuit_sleep.webp') }}" alt=""
                    class="w-44 h-auto sm:w-52 mx-auto mb-5 select-none" /> --}}
                <p class="text-base font-medium text-[#686b82]">{{ 'ไม่มีภารกิจที่กำลังจะหมดอายุ!' }}</p>
            </div>
        @else
            <div class="border-t border-[#dedee5] space-y-6 p-6 lg:p-8">
                @php
                    $i = 0;
                @endphp
                @foreach($upcoming->groupBy(fn($a) => $a->due_date->format('Y-m-d')) as $date => $assignments)
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}
                            </span>
                            <span class="text-sm font-medium text-gray-500">
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('j F Y') }}
                            </span>
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <span class="text-xs text-gray-500 bg-white border border-gray-200 rounded-full px-2 py-0.5">
                                {{ $assignments->count() }} {{ 'รายการ' }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            @foreach($assignments as $a)
                                @php
                                    $isOverdue = $a->due_date->isPast();
                                    $isUrgent = $isOverdue || $a->due_date->lt(now()->addDay());
                                    $room = $a->classworkItem->classroom;
                                    $themeColor = $room->themeCategory?->color ?? \App\Models\ThemeCategory::fallbackFor($room->id)['color'];
                                @endphp
                                <a href="{{ route('assignment.show', ['classroom' => $a->classworkItem->classroom, 'assignment' => $a]) }}"
                                   class="rounded-lg border border-[#dedee5] bg-white hover:shadow-[0_0_0_2px_var(--room-color)] flex items-center gap-4 p-4 transition-shadow duration-150 group animate__animated animate__fadeInUp"
                                   style="--room-color: {{ $themeColor }}; animation-delay: {{ $i++ * 60 }}ms;">
                                    <div class="w-3 h-3 rounded-full shrink-0 ring-2 ring-white"
                                         style="background-color: {{ $themeColor }}"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-base font-medium text-gray-900 truncate">
                                            {{ $a->title }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate mt-0.5">
                                            {{ $a->classworkItem->classroom->name }}
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-sm font-medium {{ $isUrgent ? 'text-red-600' : 'text-gray-500' }}">
                                            {{ $a->due_date->translatedFormat('H:i') }}
                                        </span>
                                        @if($isOverdue)
                                            <p class="text-sm text-red-500 mt-0.5">{{ 'เลยกำหนดแล้ว' }}</p>
                                        @elseif($isUrgent)
                                            <p class="text-sm text-red-500 mt-0.5">{{ 'ใกล้กำหนดส่ง' }}</p>
                                        @endif
                                    </div>
                                    <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
