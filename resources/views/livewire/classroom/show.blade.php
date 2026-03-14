@section('page-title', $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ auth()->user()->isTeacher() ? __('ชั้นเรียนของฉัน') : __('ห้องเรียน') }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-semibold"
            title="{{ $classroom->name }}">{{ \Illuminate\Support\Str::limit($classroom->name, 30) }}</span>
    </nav>
@endsection

<div class="animate__animated animate__fadeIn">
    <!-- Classroom Header -->
    <div class="rounded-2xl overflow-hidden mb-6 relative"
        style="background-color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}">
        <div class="absolute inset-0 bg-linear-to-b from-black/10 to-black/40"></div>
        <div class="relative p-6 sm:p-8">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $classroom->name }}</h1>
                    <p class="text-white/80 mt-1">{{ $classroom->section }}</p>
                    <p class="text-white/60 text-sm mt-2 truncate max-w-[200px]">{{ $classroom->teacher->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs-scroll flex border-b border-gray-200 mb-6 overflow-x-auto">
        <button wire:click="setTab('stream')"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'stream' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <i class="fas fa-stream mr-2"></i> {{ __('Stream') }}
        </button>
        <button wire:click="setTab('classwork')"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'classwork' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <i class="fas fa-book-open mr-2"></i> {{ __('Classwork') }}
        </button>
        <button wire:click="setTab('people')"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'people' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <i class="fas fa-users mr-2"></i> {{ __('People') }}
        </button>
        @if($classroom->canManageClassroom(auth()->user()))
            <button wire:click="setTab('grades')"
                class="px-6 py-3 text-sm font-medium border-b-2 transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'grades' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fas fa-chart-bar mr-2"></i> {{ __('Grades') }}
            </button>
        @endif
        @if($classroom->canManageClassroom(auth()->user()))
            <a href="{{ route('classroom.grades', $classroom) }}"
                class="px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-indigo-600 hover:border-indigo-300 transition-colors cursor-pointer whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-chart-line"></i> {{ __('Grade Report') }}
            </a>
        @endif
        @if($classroom->isOwnedBy(auth()->user()))
            <button wire:click="setTab('settings')"
                class="px-6 py-3 text-sm font-medium border-b-2 transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'settings' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fas fa-sliders-h mr-2"></i> {{ __('Settings') }}
            </button>
        @endif
    </div>

    <!-- Stream Tab -->
    @if($activeTab === 'stream')
        <div class="max-w-3xl mx-auto">
            <!-- Class Code Card (Teacher only) -->
            @if($classroom->canManageClassroom(auth()->user()))
                <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Class code') }}</p>
                        <p class="text-2xl font-mono font-bold text-indigo-600">{{ $classroom->code }}</p>
                    </div>
                    <button onclick="navigator.clipboard.writeText('{{ $classroom->code }}')"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg" title="Copy code">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            @endif

            @if($classroom->description)
                <!-- Description Card -->
                <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('รายละเอียด') }}</p>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $classroom->description }}</p>
                </div>
            @endif


            <!-- Announcements -->
            <div class="space-y-4">
                @foreach($classroom->announcements as $announcement)
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden animate__animated animate__fadeIn"
                        wire:key="announcement-{{ $announcement->id }}">
                        <div class="p-5">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center">
                                    <img src="{{ $announcement->user->avatar_url }}" class="w-10 h-10 rounded-full mr-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 truncate max-w-[200px]">
                                            {{ $announcement->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $announcement->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @if($announcement->user_id === auth()->id() || $classroom->canManageClassroom(auth()->user()))
                                    <button wire:click="confirmDeleteAnnouncement({{ $announcement->id }})"
                                        class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="ql-snow mt-2">
                                <div
                                    class="ql-editor prose prose-sm max-w-none text-gray-700 [&_p]:my-0 [&_p]:leading-relaxed p-0!">
                                    {!! $announcement->content !!}</div>
                            </div>
                        </div>

                        <!-- Comments -->
                        @livewire('classroom.stream-comment', ['announcementId' => $announcement->id], "comment-{$announcement->id}")
                    </div>
                @endforeach
            </div>

            @if($classroom->announcements->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                    <i class="fas fa-bullhorn text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">{{ __('No announcements yet. Start the conversation!') }}</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Classwork Tab -->
    @if($activeTab === 'classwork')
        <div class="max-w-3xl mx-auto" x-data="{ copiedToast: false, activeAssignment: null }">
            <!-- Top bar: Create + Filter -->
            <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    @if($classroom->canManageClassroom(auth()->user()))
                        <!-- Create Assignment Popover -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open"
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i> {{ __('Create Assignment') }}
                                <i class="fas fa-chevron-down ml-2 text-xs opacity-70" :class="open ? 'rotate-180' : '"></i>
                            </button>

                            <!-- Popover panel -->
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden z-30">
                                @php
                                    $assignmentTypes = [
                                        'question' => ['icon' => 'fa-pen-to-square', 'label' => 'Question'],
                                        'file' => ['icon' => 'fa-cloud-arrow-up', 'label' => 'File Upload'],
                                        'attendance' => ['icon' => 'fa-clipboard-check', 'label' => 'Attendance'],
                                        'project' => ['icon' => 'fa-diagram-project', 'label' => 'Project'],
                                    ];
                                @endphp
                                <p class="px-3 pt-3 pb-1.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    {{ __('Assignment') }}</p>
                                @foreach($assignmentTypes as $typeKey => $info)
                                    <a href="{{ route('assignment.create', $classroom) }}?type={{ $typeKey }}" @click="open = false"
                                        class="flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 transition-colors group">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                            style="background-color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}20;">
                                            <i class="fas {{ $info['icon'] }} text-sm"
                                                style="color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}"></i>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ __($info['label']) }}</span>
                                    </a>
                                @endforeach
                                <div class="border-t border-gray-100 my-1"></div>
                                <p class="px-3 pt-1.5 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    {{ __('Announcement') }}</p>
                                <a href="{{ route('assignment.create', $classroom) }}?type=announcement" @click="open = false"
                                    class="flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 transition-colors group">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                        style="background-color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}20;">
                                        <i class="fas fa-bullhorn text-sm"
                                            style="color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}"></i>
                                    </div>
                                    <span
                                        class="text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ __('Announcement') }}</span>
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <p class="px-3 pt-1.5 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    {{ __('Material') }}</p>
                                <a href="{{ route('material.create', $classroom) }}" @click="open = false"
                                    class="flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 transition-colors group">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                        style="background-color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}20;">
                                        <i class="fas fa-book-open text-sm"
                                            style="color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}"></i>
                                    </div>
                                    <span
                                        class="text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ __('Material') }}</span>
                                </a>
                                <div class="pb-1"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @php
                $assignments = $classroom->assignments;
                $materials = $classroom->materials ?? collect();
                $grouped = $assignments->groupBy(fn($a) => $a->topic ?? '__none__');
                $topicNames = $grouped->keys()->filter(fn($k) => $k !== '__none__')->sort()->values();
                $noTopicAssignments = $grouped->get('__none__', collect());
                $canManage = $classroom->canManageClassroom(auth()->user());
                $hasContent = $assignments->isNotEmpty() || $materials->isNotEmpty();
            @endphp

            @if(!$hasContent)
                <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                    <i class="fas fa-clipboard text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">{{ __('No classwork yet.') }}</p>
                </div>
            @else
                <div class="space-y-6">
                    <!-- Assignments with topics -->
                    @foreach($topicNames as $topicName)
                        <div>
                            <!-- Topic header -->
                            <div class="flex items-center gap-3 mb-3">
                                <h3 class="text-base font-semibold text-gray-800 truncate max-w-[200px] shrink-0">{{ $topicName }}
                                </h3>
                                <div class="flex-1 border-t-2 border-gray-200"></div>
                            </div>

                            <!-- Assignments under this topic -->
                            <div class="space-y-1">
                                @foreach($grouped[$topicName]->sortByDesc('created_at') as $assignment)
                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:border-gray-300 transition-all duration-200"
                                        wire:key="assignment-{{ $assignment->id }}">

                                        <!-- Clickable Header -->
                                        <div @click="activeAssignment = activeAssignment === {{ $assignment->id }} ? null : {{ $assignment->id }}"
                                            class="flex items-center p-4 cursor-pointer group">
                                            <!-- Type Icon -->
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 mr-3"
                                                style="background-color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}15;">
                                                <i class="fas {{ $assignment->typeIcon() }}"
                                                    style="color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}"></i>
                                            </div>

                                            <!-- Title & Info -->
                                            <div class="flex-1 min-w-0">
                                                <p
                                                    class="text-sm font-medium text-gray-900 truncate group-hover:text-indigo-600 transition-colors flex items-center gap-2">
                                                    {{ $assignment->title }}
                                                    @if($assignment->status === 'draft')
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ __('Draft') }}
                                                        </span>
                                                    @endif
                                                </p>
                                                <div class="flex items-center gap-3 mt-0.5">
                                                    <span class="text-xs text-gray-500">
                                                        {{ $assignment->created_at->translatedFormat('j M') }}
                                                    </span>
                                                    @if($assignment->due_date)
                                                        <span class="text-xs text-gray-500 flex items-center gap-1">
                                                            <i class="fas fa-clock text-gray-400"></i>
                                                            {{ __('Due') }} {{ $assignment->due_date->translatedFormat('j M, H:i') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Expand Arrow -->
                                            <div class="ml-3 text-gray-400 transition-transform duration-200"
                                                :class="{ 'rotate-180': activeAssignment === {{ $assignment->id }} }">
                                                <i class="fas fa-chevron-down text-sm"></i>
                                            </div>
                                        </div>

                                        <!-- Expanded Content -->
                                        <div x-show="activeAssignment === {{ $assignment->id }}" x-collapse x-cloak
                                            class="border-t border-gray-100 bg-white">
                                            <div class="p-4 pl-18 max-h-64 overflow-y-auto overflow-x-hidden [scrollbar-width:thin]">
                                                @if($assignment->description)
                                                    <div
                                                        class="prose prose-sm max-w-none text-gray-700 [&>p]:my-0 [&>p]:leading-relaxed mb-3">
                                                        {!! $assignment->description !!}
                                                    </div>
                                                @endif

                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}"
                                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white rounded-md transition-colors"
                                                        style="background-color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}">
                                                        {{ __('View') }}
                                                    </a>

                                                    @if($canManage)
                                                        <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}"
                                                            class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                                            <i class="fas fa-pen mr-1"></i> {{ __('Edit') }}
                                                        </a>
                                                        <button
                                                            @click="$dispatch('open-delete-assignment', { id: {{ $assignment->id }}, title: '{{ addslashes($assignment->title) }}' })"
                                                            class="px-3 py-1.5 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-md hover:bg-red-50">
                                                            <i class="fas fa-trash-alt mr-1"></i> {{ __('Delete') }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <!-- Assignments without topic -->
                    @if($noTopicAssignments->isNotEmpty())
                        <div>
                            @if($topicNames->isNotEmpty())
                                <div class="flex items-center gap-3 mb-3">
                                    <h3 class="text-base font-semibold text-gray-400 shrink-0">{{ __('No topic') }}</h3>
                                    <div class="flex-1 border-t-2 border-gray-200"></div>
                                </div>
                            @endif

                            <div class="space-y-1">
                                @foreach($noTopicAssignments->sortByDesc('created_at') as $assignment)
                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:border-gray-300 transition-all duration-200"
                                        wire:key="assignment-{{ $assignment->id }}">

                                        <!-- Clickable Header -->
                                        <div @click="activeAssignment = activeAssignment === {{ $assignment->id }} ? null : {{ $assignment->id }}"
                                            class="flex items-center p-4 cursor-pointer group">
                                            <!-- Type Icon -->
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 mr-3"
                                                style="background-color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}15;">
                                                <i class="fas {{ $assignment->typeIcon() }}"
                                                    style="color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}"></i>
                                            </div>

                                            <!-- Title & Info -->
                                            <div class="flex-1 min-w-0">
                                                <p
                                                    class="text-sm font-medium text-gray-900 truncate group-hover:text-indigo-600 transition-colors flex items-center gap-2">
                                                    {{ $assignment->title }}
                                                    @if($assignment->status === 'draft')
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ __('Draft') }}
                                                        </span>
                                                    @endif
                                                </p>
                                                <div class="flex items-center gap-3 mt-0.5">
                                                    <span class="text-xs text-gray-500">
                                                        {{ $assignment->created_at->translatedFormat('j M') }}
                                                    </span>
                                                    @if($assignment->due_date)
                                                        <span class="text-xs text-gray-500 flex items-center gap-1">
                                                            <i class="fas fa-clock text-gray-400"></i>
                                                            {{ __('Due') }} {{ $assignment->due_date->translatedFormat('j M, H:i') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Expand Arrow -->
                                            <div class="ml-3 text-gray-400 transition-transform duration-200"
                                                :class="{ 'rotate-180': activeAssignment === {{ $assignment->id }} }">
                                                <i class="fas fa-chevron-down text-sm"></i>
                                            </div>
                                        </div>

                                        <!-- Expanded Content -->
                                        <div x-show="activeAssignment === {{ $assignment->id }}" x-collapse x-cloak
                                            class="border-t border-gray-100 bg-white">
                                            <div class="p-4 pl-18 max-h-64 overflow-y-auto overflow-x-hidden [scrollbar-width:thin]">
                                                @if($assignment->description)
                                                    <div
                                                        class="prose prose-sm max-w-none text-gray-700 [&>p]:my-0 [&>p]:leading-relaxed mb-3">
                                                        {!! $assignment->description !!}
                                                    </div>
                                                @endif

                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}"
                                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white rounded-md transition-colors"
                                                        style="background-color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}">
                                                        {{ __('View') }}
                                                    </a>

                                                    @if($canManage)
                                                        <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}"
                                                            class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                                            <i class="fas fa-pen mr-1"></i> {{ __('Edit') }}
                                                        </a>
                                                        <button
                                                            @click="$dispatch('open-delete-assignment', { id: {{ $assignment->id }}, title: '{{ addslashes($assignment->title) }}' })"
                                                            class="px-3 py-1.5 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-md hover:bg-red-50">
                                                            <i class="fas fa-trash-alt mr-1"></i> {{ __('Delete') }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Materials section --}}
            @if($materials->isNotEmpty())
                <div class="mt-6">
                    <div class="flex items-center gap-3 mb-3">
                        <h3 class="text-base font-semibold text-gray-800 shrink-0"><i
                                class="fas fa-book-open mr-2 text-teal-500"></i>{{ __('Materials') }}</h3>
                        <div class="flex-1 border-t-2 border-gray-200"></div>
                    </div>
                    <div class="space-y-1">
                        @foreach($materials->sortByDesc('created_at') as $material)
                            <a href="{{ route('material.show', ['classroom' => $classroom, 'material' => $material]) }}"
                                class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:border-gray-300 transition-all duration-200 flex items-center p-4 group"
                                wire:key="material-{{ $material->id }}">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 mr-3"
                                    style="background-color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}15;">
                                    <i class="fas fa-book-open"
                                        style="color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-medium text-gray-900 truncate group-hover:text-indigo-600 transition-colors">
                                        {{ $material->title }}</p>
                                    <span class="text-xs text-gray-500">{{ $material->created_at->translatedFormat('j M') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif


            <!-- Copied Toast -->
            <div x-show="copiedToast" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                class="fixed bottom-6 left-1/2 -translate-x-1/2 z-60 px-4 py-2.5 bg-gray-800 text-white text-sm rounded-lg shadow-lg flex items-center gap-2">
                <i class="fas fa-check-circle text-green-400"></i>
                {{ __('Link copied!') }}
            </div>
        </div>
    @endif

    <!-- People Tab -->
    @if($activeTab === 'people')
        <div class="max-w-3xl mx-auto" x-data="{ showAddTeacher: false, showAddStudent: false }"
            @keydown.escape.window="showAddTeacher = false; showAddStudent = false">

            <!-- Teacher Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center justify-between">
                    <span class="flex items-center">
                        <i class="fas fa-chalkboard-teacher mr-2 text-indigo-600"></i> {{ __('Teacher') }}
                    </span>
                    @if($classroom->canManageClassroom(auth()->user()))
                        <button type="button" @click="showAddTeacher = true"
                            class="px-2 text-gray-900 hover:text-indigo-600 transition-colors" title="{{ __('Add teacher') }}">
                            <i class="fas fa-user-plus text-sm"></i>
                        </button>
                    @endif
                </h3>

                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <div class="flex items-center">
                        <img src="{{ $classroom->teacher->avatar_url }}" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 truncate max-w-[200px]">
                                {{ $classroom->teacher->name }}</p>
                            <p class="text-xs text-gray-500">{{ $classroom->teacher->email }}</p>
                        </div>
                    </div>
                </div>

                @if($classroom->coTeachers->isNotEmpty())
                    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 mt-2">
                        @foreach($classroom->coTeachers as $coTeacher)
                            <div class="flex items-center justify-between p-4" wire:key="coteacher-{{ $coTeacher->id }}"
                                x-data="{ open: false }" @click.outside="open = false">
                                <div class="flex items-center">
                                    <img src="{{ $coTeacher->avatar_url }}" class="w-10 h-10 rounded-full mr-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $coTeacher->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $coTeacher->email }}</p>
                                    </div>
                                </div>
                                @if($classroom->canManageClassroom(auth()->user()))
                                    <div class="relative">
                                        <button @click.stop="open = !open"
                                            class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                            <i class="fas fa-ellipsis-vertical text-sm"></i>
                                        </button>
                                        <div x-show="open" x-cloak x-transition
                                            class="absolute right-0 mt-1 w-36 bg-white rounded-lg shadow-lg border border-gray-200 z-20 py-1">
                                            <button type="button" wire:click="removeMember({{ $coTeacher->id }})"
                                                wire:confirm="{{ __('Remove this teacher from the classroom?') }}" @click="open = false"
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                <i class="fas fa-user-minus"></i>{{ __('Kick') }}
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                @endif
                </div>

                <!-- Students Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center justify-between">
                        <span class="flex items-center">
                            <i class="fas fa-users mr-2 text-indigo-600"></i> {{ __('Students') }}
                            <span
                                class="ml-2 text-sm font-normal text-gray-500">({{ $classroom->students->count() }})</span>
                        </span>
                        @if($classroom->canManageClassroom(auth()->user()))
                            <button type="button" @click="showAddStudent = true"
                                class="px-2 text-gray-900 hover:text-indigo-600 transition-colors"
                                title="{{ __('Add student') }}">
                                <i class="fas fa-user-plus text-sm"></i>
                            </button>
                        @endif
                    </h3>

                    @if($classroom->students->isEmpty())
                        <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
                            <p class="text-gray-500">{{ __('No students enrolled yet.') }}</p>
                            @if($classroom->canManageClassroom(auth()->user()))
                                <p class="text-sm text-gray-400 mt-1">
                                    {!! __("Share the class code :code with your students.", ['code' => '<strong class=\"text-indigo-600 font-mono\">' . e($classroom->code) . '</strong>']) !!}
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
                            @foreach($classroom->students as $student)
                                <div class="flex items-center justify-between p-4" wire:key="student-{{ $student->id }}"
                                    x-data="{ open: false }" @click.outside="open = false">
                                    <div class="flex items-center">
                                        <img src="{{ $student->avatar_url }}" class="w-10 h-10 rounded-full mr-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $student->email }}</p>
                                        </div>
                                    </div>
                                    @if($classroom->canManageClassroom(auth()->user()))
                                        <div class="relative">
                                            <button @click.stop="open = !open"
                                                class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                                <i class="fas fa-ellipsis-vertical text-sm"></i>
                                            </button>
                                            <div x-show="open" x-cloak x-transition
                                                class="absolute right-0 mt-1 w-36 bg-white rounded-lg shadow-lg border border-gray-200 z-20 py-1">
                                                <button type="button" wire:click="removeMember({{ $student->id }})"
                                                    wire:confirm="{{ __('Remove this student from the classroom?') }}"
                                                    @click="open = false"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                    <i class="fas fa-user-minus"></i>{{ __('Kick') }}
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Add Teacher Modal --}}
                @if($classroom->canManageClassroom(auth()->user()))
                    <template x-teleport="body">
                        <div x-show="showAddTeacher" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60"
                            @click.self="showAddTeacher = false">
                            <div x-show="showAddTeacher" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900">{{ __('Add Teacher') }}</h4>
                                        <p class="text-sm text-gray-500 mt-0.5">
                                            {{ __('The user must already have a teacher account.') }}</p>
                                    </div>
                                    <button type="button" @click="showAddTeacher = false"
                                        class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <i class="fas fa-xmark text-lg"></i>
                                    </button>
                                </div>
                                <form wire:submit="addTeacher" class="px-6 py-5 space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email Address') }}</label>
                                        <input wire:model="addTeacherEmail" type="email"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="{{ __('teacher@example.com') }}" autocomplete="off">
                                        @error('addTeacherEmail') <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex justify-end gap-2 pt-1">
                                        <button type="button" @click="showAddTeacher = false"
                                            class="inline-flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-xmark mr-1.5"></i>{{ __('Cancel') }}
                                        </button>
                                        <button type="submit" wire:loading.attr="disabled"
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                                            <span wire:loading.remove wire:target="addTeacher" class="inline-flex items-center">
                                                <i class="fas fa-user-plus mr-1.5"></i>{{ __('Add Teacher') }}
                                            </span>
                                            <span wire:loading wire:target="addTeacher" class="inline-flex items-center">
                                                <i class="fas fa-spinner fa-spin mr-1.5"></i>{{ __('Adding...') }}
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>

                    {{-- Add Student Modal --}}
                    <template x-teleport="body">
                        <div x-show="showAddStudent" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60"
                            @click.self="showAddStudent = false">
                            <div x-show="showAddStudent" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900">{{ __('Add Student') }}</h4>
                                        <p class="text-sm text-gray-500 mt-0.5">
                                            {{ __('The user must already have a student account.') }}</p>
                                    </div>
                                    <button type="button" @click="showAddStudent = false"
                                        class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <i class="fas fa-xmark text-lg"></i>
                                    </button>
                                </div>
                                <form wire:submit="addStudent" class="px-6 py-5 space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email Address') }}</label>
                                        <input wire:model="addStudentEmail" type="email"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="{{ __('student@example.com') }}" autocomplete="off">
                                        @error('addStudentEmail') <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex justify-end gap-2 pt-1">
                                        <button type="button" @click="showAddStudent = false"
                                            class="inline-flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-xmark mr-1.5"></i>{{ __('Cancel') }}
                                        </button>
                                        <button type="submit" wire:loading.attr="disabled"
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                                            <span wire:loading.remove wire:target="addStudent" class="inline-flex items-center">
                                                <i class="fas fa-user-plus mr-1.5"></i>{{ __('Add Student') }}
                                            </span>
                                            <span wire:loading wire:target="addStudent" class="inline-flex items-center">
                                                <i class="fas fa-spinner fa-spin mr-1.5"></i>{{ __('Adding...') }}
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>
                @endif
    @endif

            <!-- Grades Tab -->
            @if($activeTab === 'grades')
                <div>
                    @if($classroom->students->isEmpty())
                        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                            <i class="fas fa-chart-bar text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">{{ __('No students enrolled yet.') }}</p>
                            <p class="text-sm text-gray-400 mt-1">{{ __('Share the class code') }} <strong
                                    class="text-indigo-600 font-mono">{{ $classroom->code }}</strong>
                                {{ __('with your students.') }}</p>
                        </div>
                    @else
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <div class="tabs-scroll overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th
                                                class="text-left px-4 py-3 font-semibold text-gray-700 sticky left-0 bg-gray-50">
                                                {{ __('Student') }}
                                            </th>
                                            @foreach($classroom->assignments->where('status', 'published') as $assignment)
                                                <th class="text-center px-4 py-3 font-medium text-gray-600 min-w-30">
                                                    <div class="truncate max-w-100" title="{{ $assignment->title }}">
                                                        {{ $assignment->title }}
                                                    </div>
                                                    <div class="text-xs font-normal text-gray-400">/ {{ $assignment->max_score }}
                                                    </div>
                                                </th>
                                            @endforeach
                                            <th class="text-center px-4 py-3 font-semibold text-gray-700">{{ __('Average') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($classroom->students as $student)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 sticky left-0 bg-white">
                                                    <div class="flex items-center min-w-0">
                                                        <img src="{{ $student->avatar_url }}"
                                                            class="hidden sm:block w-8 h-8 rounded-full mr-2">
                                                        <span
                                                            class="font-medium text-gray-900 truncate max-w-24 sm:max-w-none">{{ $student->name }}</span>
                                                    </div>
                                                </td>
                                                @php $grades = []; @endphp
                                                @foreach($classroom->assignments->where('status', 'published') as $assignment)
                                                    @php
                                                        $submission = $assignment->submissions->where('user_id', $student->id)->first();
                                                        $score = $submission?->score;
                                                        if ($score !== null)
                                                            $grades[] = ($score / $assignment->max_score) * 100;
                                                    @endphp
                                                    <td class="text-center px-4 py-3 min-w-30">
                                                        @if($submission)
                                                            @if($submission->status === 'graded')
                                                                <span
                                                                    class="font-medium {{ $score >= ($assignment->max_score * 0.7) ? 'text-green-600' : ($score >= ($assignment->max_score * 0.5) ? 'text-amber-600' : 'text-red-600') }}">
                                                                    {{ $score }}
                                                                </span>
                                                            @elseif($submission->status === 'turned_in')
                                                                <span class="text-blue-500 text-xs"><i class="fas fa-check"></i>
                                                                    {{ __('Turned in') }}</span>
                                                            @else
                                                                <span class="text-gray-400 text-xs">-</span>
                                                            @endif
                                                        @else
                                                            <span class="text-gray-300 text-xs">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="text-center px-4 py-3 font-semibold">
                                                    @if(count($grades) > 0)
                                                        {{ round(array_sum($grades) / count($grades)) }}%
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
            @endif

                <!-- Settings Tab -->
                @if($activeTab === 'settings' && $classroom->isOwnedBy(auth()->user()))
                            <div class="max-w-3xl mx-auto space-y-6">
                                <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ __('Classroom Settings') }}</h3>
                                    <p class="text-sm text-gray-500 mb-5">{{ __('Update classroom details and theme color.') }}</p>

                                    <form wire:submit="saveSettings" class="space-y-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('Class Name *') }}</label>
                                            <input wire:model="name" type="text"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('Section') }}</label>
                                                <input wire:model="section" type="text"
                                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                @error('section') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                                            <textarea wire:model="description" rows="3"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                            @error('description') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('เลือกธีมห้องเรียน') }}</label>
                                            <div class="grid grid-cols-7 gap-2">
                                                @foreach($themes as $theme)
                                                    @php $pn = str_pad($theme->planet_number, 2, '0', STR_PAD_LEFT); @endphp
                                                    <button type="button" wire:click="$set('theme_category_id', {{ $theme->id }})"
                                                        title="{{ $theme->name }}" @class([
                                                            'aspect-square rounded-xl border-2 p-1.5 transition-all',
                                                            'border-indigo-500 bg-indigo-50' => $theme_category_id == $theme->id,
                                                            'border-gray-200 hover:border-indigo-300 hover:bg-gray-50' => $theme_category_id != $theme->id,
                                                        ])>
                                                        <img src="/images/planets/planet_{{ $pn }}.svg" alt="{{ $theme->name }}"
                                                            class="w-full h-full object-contain" />
                                                    </button>
                                                @endforeach
                                            </div>
                                            @if($theme_category_id)
                                                @php $selected = $themes->find($theme_category_id); @endphp
                                                @if($selected)
                                                    <p class="mt-1.5 text-xs text-indigo-600 font-medium">
                                                        <i class="fas fa-check-circle mr-1"></i>{{ $selected->name }}
                                                    </p>
                                                @endif
                                            @endif
                                        </div>

                                        <div class="pt-2 flex justify-end">
                                            <button type="submit"
                                                class="btn-3d btn-3d--indigo px-5 py-2.5 text-sm font-medium rounded-lg transition-colors">
                                                <span wire:loading.remove wire:target="saveSettings">
                                                    <i class="fas fa-save mr-2"></i>{{ __('Save Settings') }}
                                                </span>
                                                <span wire:loading wire:target="saveSettings"><i
                                                        class="fas fa-spinner fa-spin mr-1"></i>
                                                    {{ __('Saving...') }}</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>


                                <div class="bg-white rounded-xl border border-amber-200 p-6">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-amber-600">
                                                {{ $classroom->is_archived ? __('Archived') : __('Archive Classroom') }}
                                            </h3>
                                            <p class="text-sm text-gray-500 mt-1">
                                                @if($classroom->is_archived)
                                                    {{ __('This classroom is archived. Restore it to make it visible to teachers and students again.') }}
                                                @else
                                                    {{ __('Archive this classroom to hide it from all teachers and students. You can restore it later.') }}
                                                @endif
                                            </p>
                                        </div>
                                        <button type="button" wire:click="toggleArchive" wire:loading.attr="disabled" class="shrink-0 px-4 py-2 text-sm font-medium border rounded-lg transition-colors
                                    {{ $classroom->is_archived
                    ? 'text-green-700 border-green-300 hover:bg-green-50'
                    : 'text-amber-700 border-amber-300 hover:bg-amber-50' }}">
                                            <span wire:loading.remove wire:target="toggleArchive">
                                                <i class="fas fa-{{ $classroom->is_archived ? 'box-open' : 'archive' }} mr-1.5"></i>
                                                {{ $classroom->is_archived ? __('Restore Classroom') : __('Archive Classroom') }}
                                            </span>
                                            <span wire:loading wire:target="toggleArchive">
                                                <i class="fas fa-spinner fa-spin mr-1.5"></i>{{ __('Please wait...') }}
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <div class="bg-white rounded-xl border border-red-200 p-6" x-data="{ showDeleteModal: false }"
                                    @keydown.escape.window="showDeleteModal = false">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-red-600">{{ __('Danger Zone') }}</h3>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ __('Delete this classroom permanently. This action cannot be undone.') }}
                                            </p>
                                        </div>
                                        <button type="button" @click="showDeleteModal = true"
                                            class="px-4 py-2 text-sm font-medium text-red-600 border border-red-300 rounded-lg hover:bg-red-50 transition-colors">
                                            <i class="fas fa-trash-alt mr-1.5"></i>{{ __('Delete Classroom') }}
                                        </button>
                                    </div>

                                    <template x-teleport="body">
                                        <div x-show="showDeleteModal" x-cloak
                                            class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60"
                                            @click.self="showDeleteModal = false">
                                            <div
                                                class="w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                                                <div class="px-6 py-5 border-b border-gray-100">
                                                    <h4 class="text-base font-semibold text-gray-900">{{ __('Delete Classroom') }}
                                                    </h4>
                                                    <p class="text-sm text-gray-500 mt-1">
                                                        {{ __('Type ":name" to confirm deletion.', ['name' => $classroom->name]) }}
                                                    </p>
                                                </div>

                                                <form wire:submit="deleteClassroom" class="px-6 py-5 space-y-4">
                                                    <div>
                                                        <input wire:model="deleteConfirm" type="text"
                                                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-0 focus:border-red-500"
                                                            placeholder="{{ __('Type classroom name here...') }}"
                                                            autocomplete="off">
                                                        @error('deleteConfirm') <p class="mt-1 text-sm text-red-500">{{ $message }}
                                                        </p> @enderror
                                                    </div>

                                                    <div class="flex justify-end gap-2">
                                                        <button type="button" @click="showDeleteModal = false"
                                                            class="inline-flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                                            <i class="fas fa-xmark mr-1.5"></i>{{ __('Cancel') }}
                                                        </button>
                                                        <button type="submit" wire:loading.attr="disabled"
                                                            class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50 transition-colors">
                                                            <span wire:loading.remove wire:target="deleteClassroom"
                                                                class="inline-flex items-center">
                                                                <i class="fas fa-trash-alt mr-1.5"></i>{{ __('Delete') }}
                                                            </span>
                                                            <span wire:loading wire:target="deleteClassroom"
                                                                class="inline-flex items-center">
                                                                <i class="fas fa-spinner fa-spin mr-1.5"></i>{{ __('Deleting...') }}
                                                            </span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                @endif

                <!-- Delete Assignment Modal (page-level, outside all tabs) -->
                <div x-data="{ showDeleteModal: false, deleteAssignmentId: null, deleteAssignmentTitle: '' }"
                    @open-delete-assignment.window="deleteAssignmentId = $event.detail.id; deleteAssignmentTitle = $event.detail.title; showDeleteModal = true"
                    @keydown.escape.window="showDeleteModal = false">
                    <template x-teleport="body">
                        <div x-show="showDeleteModal" x-cloak
                            class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60"
                            @click.self="showDeleteModal = false">
                            <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900">{{ __('Delete Assignment') }}
                                        </h4>
                                        <p class="text-sm font-medium text-gray-700 mt-1"
                                            x-text="deleteAssignmentTitle"></p>
                                    </div>
                                    <button type="button" @click="showDeleteModal = false"
                                        class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <i class="fas fa-xmark text-lg"></i>
                                    </button>
                                </div>
                                <div class="px-6 py-5">
                                    <p class="text-sm text-gray-500 mb-4">
                                        {{ __('Are you sure you want to delete this assignment? This action cannot be undone.') }}
                                    </p>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" @click="showDeleteModal = false"
                                            class="inline-flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-xmark mr-1.5"></i>{{ __('Cancel') }}
                                        </button>
                                        <button type="button"
                                            @click="$wire.deleteAssignment(deleteAssignmentId); showDeleteModal = false"
                                            class="px-4 py-2 text-sm text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors inline-flex items-center">
                                            <i class="fas fa-trash-alt mr-1.5"></i>{{ __('Delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-teleport="body">
                        <div x-data x-show="$wire.showDeleteAnnouncementModal" x-cloak
                            x-on:keydown.escape.window="$wire.set('showDeleteAnnouncementModal', false)"
                            class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60"
                            @click.self="$wire.set('showDeleteAnnouncementModal', false)">
                            <div x-show="$wire.showDeleteAnnouncementModal"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden"
                                @click.stop>
                                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900">
                                            {{ __('Delete Announcement') }}</h4>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ __('Are you sure you want to delete this announcement? This action cannot be undone.') }}
                                        </p>
                                    </div>
                                    <button type="button" @click="$wire.set('showDeleteAnnouncementModal', false)"
                                        class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <i class="fas fa-xmark text-lg"></i>
                                    </button>
                                </div>
                                <div class="px-6 py-5">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" @click="$wire.set('showDeleteAnnouncementModal', false)"
                                            class="inline-flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-xmark mr-1.5"></i>{{ __('Cancel') }}
                                        </button>
                                        <button type="button" wire:click="deleteAnnouncement"
                                            class="px-4 py-2 text-sm text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors inline-flex items-center">
                                            <i class="fas fa-trash-alt mr-1.5"></i>{{ __('Delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>