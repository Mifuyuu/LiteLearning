@section('page-title', 'รอตรวจ')

<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-900">รอตรวจ</h2>
        <p class="text-sm text-gray-500 mt-1">งานที่รอการตรวจจากครูผู้สอน</p>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <div class="relative w-full sm:w-64">
            <x-icon name="magnifying-glass" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="ค้นหาชื่อนักเรียน..."
                class="w-full border border-gray-200 rounded-xl pl-9 pr-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-400/40 focus:border-blue-400 transition-all">
        </div>

        <select wire:model.live="classroomId"
            class="w-full sm:w-auto border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-400/40 focus:border-blue-400 transition-all bg-white">
            <option value="">ทุกห้องเรียน</option>
            @foreach($classrooms as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>

        <div class="flex items-center gap-2 text-sm text-gray-500 ml-auto">
            <span class="hidden sm:inline">เรียงโดย</span>
            <button wire:click="sortBy('turned_in_at')"
                class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors text-xs font-medium
                    {{ $sortField === 'turned_in_at' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'text-gray-600' }}">
                เวลาส่ง
                @if($sortField === 'turned_in_at')
                    <x-icon :name="'chevron-'.($sortDir === 'asc' ? 'up' : 'down')" class="h-4 w-4 ml-1" />
                @endif
            </button>
            <button wire:click="sortBy('user_id')"
                class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors text-xs font-medium
                    {{ $sortField === 'user_id' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'text-gray-600' }}">
                ชื่อ
                @if($sortField === 'user_id')
                    <x-icon :name="'chevron-'.($sortDir === 'asc' ? 'up' : 'down')" class="h-4 w-4 ml-1" />
                @endif
            </button>
        </div>
    </div>

    {{-- Results --}}
    @php $submissions = $pendingSubmissions; @endphp

    @if($submissions->isEmpty())
        <div class="bg-white rounded-xl border border-[#dedee5] p-12 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <x-icon name="check-circle" class="h-7 w-7 text-green-600" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">ตรวจงานครบแล้ว!</h3>
            <p class="text-gray-500">
                @if($classroomId || $search)
                    ไม่พบงานที่ตรงกับที่ค้นหา
                @else
                    ไม่มีงานที่รอตรวจ
                @endif
            </p>
        </div>
    @else
        <div class="bg-white rounded-xl border border-[#dedee5] overflow-hidden">
            <div class="divide-y divide-gray-100">
                @foreach($submissions as $sub)
                    <div class="flex items-center p-4 hover:bg-gray-50 transition-colors" wire:key="sub-{{ $sub->id }}">
                        <img src="{{ $sub->user->avatar_url }}" class="w-10 h-10 rounded-full mr-3 shrink-0">

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $sub->user->name }}
                            </p>
                            <p class="text-xs text-gray-500 truncate mt-0.5">
                                {{ $sub->assignment->title }}
                                <span class="text-gray-300 mx-1">·</span>
                                {{ $sub->assignment->classworkItem->classroom->name ?? '' }}
                            </p>
                        </div>

                        <div class="text-right shrink-0 mr-4">
                            <span class="text-xs text-blue-600 font-medium bg-blue-50 px-2 py-0.5 rounded-full">รอตรวจ</span>
                            <p class="text-xs text-gray-400 mt-1">{{ $sub->turned_in_at?->diffForHumans() }}</p>
                        </div>

                        <a href="{{ route('assignment.grade', [
                            'classroom' => $sub->assignment->classworkItem->classroom,
                            'assignment' => $sub->assignment,
                            'submission' => $sub,
                        ]) }}"
                            class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors shrink-0">
                            <x-icon name="pencil" class="h-4 w-4 mr-1" />ให้คะแนน
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $submissions->links(data: ['scrollTo' => false]) }}
        </div>
    @endif
</div>
