@section('page-title', __('report.admin_title'))

<div class="space-y-4 animate__animated animate__fadeIn">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-flag text-indigo-500"></i>
            {{ __('report.admin_title') }}
        </h1>
        <div class="inline-flex items-center justify-center px-3 py-1.5text-sm font-medium text-gray-600">
            {{ $reports->count() }} {{ __('report.admin_subtitle') }}
        </div>
    </div>

    @if($reports->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
            <i class="fas fa-flag text-4xl text-gray-200 mb-3 block"></i>
            <p class="text-gray-400 text-sm">{{ __('report.empty') }}</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            @foreach($reports as $report)
                @php
                    $typeColors = [
                        'bug' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-bug'],
                        'suggestion' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-lightbulb'],
                        'other' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'fa-circle-question'],
                    ];
                    $tc = $typeColors[$report->type] ?? $typeColors['other'];
                @endphp
                <div wire:key="report-{{ $report->id }}"
                    class="flex flex-col sm:flex-row items-start gap-3 sm:gap-5 px-5 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }} transition-all {{ $report->status === 'resolved' ? 'opacity-50 bg-gray-50/50' : '' }}">

                    {{-- Type badge --}}
                    <div class="shrink-0 sm:w-32">
                        <span
                            class="inline-flex items-center justify-center w-full gap-1.5 text-xs font-bold px-2 py-1.5 rounded-lg {{ $tc['bg'] }} {{ $tc['text'] }}">
                            <i class="fas {{ $tc['icon'] }}"></i>
                            {{ __('report.type_' . $report->type) }}
                        </span>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0 w-full sm:pt-0.5" x-data="{ expanded: false }">
                        <div class="flex items-center justify-between gap-2 cursor-pointer" @click="expanded = !expanded">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-semibold text-sm text-gray-800 break-all">{{ $report->title }}</p>
                                @if($report->status === 'resolved')
                                    <span
                                        class="text-[10px] font-bold text-green-600 bg-green-100 px-1.5 py-0.5 rounded uppercase">{{ __('report.resolved') }}</span>
                                @endif
                            </div>
                            <button type="button" class="text-gray-400 hover:text-gray-600 transition-transform duration-200"
                                :class="{ 'rotate-180': expanded }">
                                <i class="fas fa-chevron-down w-4"></i>
                            </button>
                        </div>

                        <div class="mt-1 transition-all overflow-hidden" :class="expanded ? 'max-h-96' : 'max-h-11'">
                            <p class="text-sm text-gray-600 leading-relaxed break-all"
                                :class="!expanded ? 'line-clamp-2 text-gray-500' : 'pb-2'">
                                {{ $report->message }}
                            </p>
                        </div>

                        <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-2">
                            <img src="{{ $report->user->avatar_url }}" class="w-4 h-4 rounded-full">
                            {{ $report->user->name }}
                            <span>·</span>
                            {{ $report->created_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Toggle button --}}
                    <button wire:click="toggleStatus({{ $report->id }})" class="shrink-0 text-xs font-medium px-3 py-1.5 rounded-lg border transition cursor-pointer
                                                                                    {{ $report->status === 'pending'
                    ? 'border-green-300 text-green-700 hover:bg-green-50'
                    : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                        {{ $report->status === 'pending' ? __('report.mark_resolved') : __('report.mark_pending') }}
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</div>