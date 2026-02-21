@section('page-title', $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ __('Classrooms') }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-semibold"
            title="{{ $classroom->name }}">{{ \Illuminate\Support\Str::limit($classroom->name, 30) }}</span>
    </nav>
@endsection

<div>
    <!-- Classroom Header -->
    <div class="rounded-2xl overflow-hidden mb-6 relative" style="background-color: {{ $classroom->theme_color }}">
        <div class="absolute inset-0 bg-linear-to-b from-black/10 to-black/40"></div>
        <div class="relative p-6 sm:p-8">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $classroom->name }}</h1>
                    <p class="text-white/80 mt-1">{{ $classroom->section }} &middot; {{ $classroom->subject }}</p>
                    <p class="text-white/60 text-sm mt-2">{{ $classroom->teacher->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs-scroll flex border-b border-gray-200 mb-6 overflow-x-auto">
        <button wire:click="setTab('stream')"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $activeTab === 'stream' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <i class="fas fa-stream mr-2"></i> {{ __('Stream') }}
        </button>
        <button wire:click="setTab('classwork')"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $activeTab === 'classwork' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <i class="fas fa-book-open mr-2"></i> {{ __('Classwork') }}
        </button>
        <button wire:click="setTab('people')"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $activeTab === 'people' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <i class="fas fa-users mr-2"></i> {{ __('People') }}
        </button>
        @if($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
            <button wire:click="setTab('grades')"
                class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $activeTab === 'grades' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fas fa-chart-bar mr-2"></i> {{ __('Grades') }}
            </button>
            <button wire:click="setTab('settings')"
                class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $activeTab === 'settings' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <i class="fas fa-sliders-h mr-2"></i> {{ __('Settings') }}
            </button>
        @endif
    </div>

    <!-- Stream Tab -->
    @if($activeTab === 'stream')
        <div class="max-w-3xl mx-auto">
            <!-- Class Code Card (Teacher only) -->
            @if($classroom->isOwnedBy(auth()->user()))
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

            <!-- New Announcement -->
            @if($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
                <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6" x-data="{ expanded: false }">
                    <div @click="expanded = true" class="cursor-text">
                        @if(!$newAnnouncement)
                            <div x-show="!expanded" class="flex items-center text-gray-400">
                                <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-full mr-3">
                                <span class="text-sm">{{ __('Announce something to your class...') }}</span>
                            </div>
                        @endif
                        <div x-show="expanded" x-cloak>
                            <textarea wire:model="newAnnouncement" rows="3"
                                class="w-full border-0 focus:ring-0 text-sm resize-none p-0"
                                placeholder="{{ __('Share something with your class...') }}"></textarea>
                            <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100">
                                <div class="flex gap-2">
                                    <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                    <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                                        <i class="fas fa-image"></i>
                                    </button>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="expanded = false" wire:click="$set('newAnnouncement', '')"
                                        class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">{{ __('Cancel') }}</button>
                                    <button wire:click="postAnnouncement"
                                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                        {{ __('Post') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                        <p class="text-sm font-semibold text-gray-900">{{ $announcement->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $announcement->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @if($announcement->user_id === auth()->id() || $classroom->isOwnedBy(auth()->user()))
                                    <button wire:click="deleteAnnouncement({{ $announcement->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this announcement?') }}"
                                        class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="mt-3 text-sm text-gray-700 whitespace-pre-wrap">{!! nl2br(e($announcement->content)) !!}
                            </div>
                        </div>

                        <!-- Comments -->
                        @livewire('classroom.stream-comment', ['announcementId' => $announcement->id], key('comment-' . $announcement->id))
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
        <div class="max-w-3xl mx-auto"
            x-data="{ showDeleteModal: false, deleteAssignmentId: null, deleteAssignmentTitle: '', copiedToast: false, activeAssignment: null }">
            <!-- Top bar: Create + Filter + View Work -->
            <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    @if($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
                        <a href="{{ route('assignment.create', $classroom) }}"
                            class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                            <i class="fas fa-plus mr-2"></i> {{ __('Create') }}
                        </a>
                    @endif
                </div>
            </div>

            @php
                $assignments = $classroom->assignments;
                $grouped = $assignments->groupBy(fn($a) => $a->topic ?? '__none__');
                $topicNames = $grouped->keys()->filter(fn($k) => $k !== '__none__')->sort()->values();
                $noTopicAssignments = $grouped->get('__none__', collect());
                $canManage = $classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin();
            @endphp

            @if($assignments->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                    <i class="fas fa-clipboard text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">{{ __('No assignments yet.') }}</p>
                </div>
            @else
                <div class="space-y-6">
                    <!-- Assignments with topics -->
                    @foreach($topicNames as $topicName)
                        <div>
                            <!-- Topic header -->
                            <div class="flex items-center justify-between pb-2 mb-2 border-b-2 border-gray-200">
                                <h3 class="text-base font-semibold text-gray-800">{{ $topicName }}</h3>
                            </div>

                            <!-- Assignments under this topic -->
                            <div class="space-y-1">
                                @foreach($grouped[$topicName]->sortByDesc('created_at') as $assignment)
                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:border-gray-300 transition-all duration-200"
                                        wire:key="assignment-{{ $assignment->id }}">

                                        <!-- Clickable Header -->
                                        <div @click="activeAssignment = activeAssignment === {{ $assignment->id }} ? null : {{ $assignment->id }}"
                                            class="flex items-center p-4 cursor-pointer group"
                                            :class="{ 'bg-gray-50': activeAssignment === {{ $assignment->id }} }">

                                            <!-- Type Icon -->
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 mr-3"
                                                style="background-color: {{ $classroom->theme_color }}15;">
                                                <i class="fas {{ $assignment->typeIcon() }}"
                                                    style="color: {{ $classroom->theme_color }}"></i>
                                            </div>

                                            <!-- Title & Info -->
                                            <div class="flex-1 min-w-0">
                                                <p
                                                    class="text-sm font-medium text-gray-900 truncate group-hover:text-indigo-600 transition-colors">
                                                    {{ $assignment->title }}
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
                                            class="border-t border-gray-100 bg-gray-50">
                                            <div class="p-4 pl-[4.5rem]">
                                                <p class="text-sm text-gray-600 mb-3">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags(html_entity_decode($assignment->description ?? $assignment->instructions)), 200) }}
                                                </p>

                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}"
                                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white rounded-md transition-colors"
                                                        style="background-color: {{ $classroom->theme_color }}">
                                                        {{ __('View') }}
                                                    </a>

                                                    @if($canManage)
                                                        <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}?edit=1"
                                                            class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                                            <i class="fas fa-pen mr-1"></i> {{ __('Edit') }}
                                                        </a>
                                                        <button
                                                            @click="deleteAssignmentId = {{ $assignment->id }}; deleteAssignmentTitle = '{{ addslashes($assignment->title) }}'; showDeleteModal = true"
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
                                <div class="flex items-center justify-between pb-2 mb-2 border-b-2 border-gray-200">
                                    <h3 class="text-base font-semibold text-gray-400">{{ __('No topic') }}</h3>
                                </div>
                            @endif

                            <div class="space-y-1">
                                @foreach($noTopicAssignments->sortByDesc('created_at') as $assignment)
                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:border-gray-300 transition-all duration-200"
                                        wire:key="assignment-{{ $assignment->id }}">

                                        <!-- Clickable Header -->
                                        <div @click="activeAssignment = activeAssignment === {{ $assignment->id }} ? null : {{ $assignment->id }}"
                                            class="flex items-center p-4 cursor-pointer group"
                                            :class="{ 'bg-gray-50': activeAssignment === {{ $assignment->id }} }">

                                            <!-- Type Icon -->
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 mr-3"
                                                style="background-color: {{ $classroom->theme_color }}15;">
                                                <i class="fas {{ $assignment->typeIcon() }}"
                                                    style="color: {{ $classroom->theme_color }}"></i>
                                            </div>

                                            <!-- Title & Info -->
                                            <div class="flex-1 min-w-0">
                                                <p
                                                    class="text-sm font-medium text-gray-900 truncate group-hover:text-indigo-600 transition-colors">
                                                    {{ $assignment->title }}
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
                                            class="border-t border-gray-100 bg-gray-50">
                                            <div class="p-4 pl-[4.5rem]">
                                                <p class="text-sm text-gray-600 mb-3">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags(html_entity_decode($assignment->description ?? $assignment->instructions)), 200) }}
                                                </p>

                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}"
                                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white rounded-md transition-colors"
                                                        style="background-color: {{ $classroom->theme_color }}">
                                                        {{ __('View') }}
                                                    </a>

                                                    @if($canManage)
                                                        <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}?edit=1"
                                                            class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                                            <i class="fas fa-pen mr-1"></i> {{ __('Edit') }}
                                                        </a>
                                                        <button
                                                            @click="deleteAssignmentId = {{ $assignment->id }}; deleteAssignmentTitle = '{{ addslashes($assignment->title) }}'; showDeleteModal = true"
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

            <!-- Delete Confirmation Modal -->
            <div x-show="showDeleteModal" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
                @click="showDeleteModal = false">
                <div class="w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden"
                    @click.stop x-show="showDeleteModal" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h4 class="text-base font-semibold text-gray-900">{{ __('Delete Assignment') }}</h4>
                        <p class="text-sm text-gray-500 mt-1">{{ __('Are you sure you want to delete this assignment?') }}
                        </p>
                        <p class="text-sm font-medium text-gray-700 mt-2" x-text="deleteAssignmentTitle"></p>
                    </div>
                    <div class="px-6 py-5">
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

            <!-- Copied Toast -->
            <div x-show="copiedToast" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] px-4 py-2.5 bg-gray-800 text-white text-sm rounded-lg shadow-lg flex items-center gap-2">
                <i class="fas fa-check-circle text-green-400"></i>
                {{ __('Link copied!') }}
            </div>
        </div>
    @endif

    <!-- People Tab -->
    @if($activeTab === 'people')
        <div class="max-w-3xl mx-auto">
            <!-- Teacher -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chalkboard-teacher mr-2 text-indigo-600"></i> {{ __('Teacher') }}
                </h3>
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <div class="flex items-center">
                        <img src="{{ $classroom->teacher->avatar_url }}" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $classroom->teacher->name }}</p>
                            <p class="text-xs text-gray-500">{{ $classroom->teacher->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-users mr-2 text-indigo-600"></i> {{ __('Students') }}
                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $classroom->students->count() }})</span>
                </h3>

                @if($classroom->students->isEmpty())
                    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
                        <p class="text-gray-500">{{ __('No students enrolled yet.') }}</p>
                        @if($classroom->isOwnedBy(auth()->user()))
                            <p class="text-sm text-gray-400 mt-1">{{ __('Share the class code') }} <strong
                                    class="text-indigo-600 font-mono">{{ $classroom->code }}</strong>
                                {{ __('with your students.') }}</p>
                        @endif
                    </div>
                @else
                    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
                        @foreach($classroom->students as $student)
                            <div class="flex items-center justify-between p-4" wire:key="student-{{ $student->id }}">
                                <div class="flex items-center">
                                    <img src="{{ $student->avatar_url }}" class="w-10 h-10 rounded-full mr-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $student->email }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Grades Tab -->
    @if($activeTab === 'grades')
        <div>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="tabs-scroll overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700 sticky left-0 bg-gray-50">
                                    {{ __('Student') }}
                                </th>
                                @foreach($classroom->assignments->where('type', '!=', 'material') as $assignment)
                                    <th class="text-center px-4 py-3 font-medium text-gray-600 min-w-30">
                                        <div class="truncate max-w-25" title="{{ $assignment->title }}">{{ $assignment->title }}
                                        </div>
                                        <div class="text-xs font-normal text-gray-400">/ {{ $assignment->max_score }}</div>
                                    </th>
                                @endforeach
                                <th class="text-center px-4 py-3 font-semibold text-gray-700">{{ __('Average') }}</th>
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
                                    @foreach($classroom->assignments->where('type', '!=', 'material') as $assignment)
                                        @php
                                            $submission = $assignment->submissions->where('user_id', $student->id)->first();
                                            $score = $submission?->score;
                                            if ($score !== null)
                                                $grades[] = ($score / $assignment->max_score) * 100;
                                        @endphp
                                        <td class="text-center px-4 py-3">
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
        </div>
    @endif

    <!-- Settings Tab -->
    @if($activeTab === 'settings' && ($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin()))
        <div class="max-w-3xl mx-auto space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ __('Classroom Settings') }}</h3>
                <p class="text-sm text-gray-500 mb-5">{{ __('Update classroom details and theme color.') }}</p>

                <form wire:submit="saveSettings" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Class Name *') }}</label>
                        <input wire:model="name" type="text"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Section') }}</label>
                            <input wire:model="section" type="text"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('section') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Subject') }}</label>
                            <input wire:model="subject" type="text"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('subject') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                        <textarea wire:model="description" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        @error('description') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Banner Color') }}</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['#2563EB', '#4F46E5', '#0EA5E9', '#16A34A', '#EAB308', '#F97316', '#DC2626', '#A855F7'] as $color)
                                <button type="button" wire:click="$set('theme_color', '{{ $color }}')"
                                    class="w-8 h-8 rounded-full border-2 transition-transform hover:scale-110 {{ $theme_color === $color ? 'border-gray-900 scale-110' : 'border-transparent' }}"
                                    style="background-color: {{ $color }}" title="{{ $color }}"></button>
                            @endforeach
                        </div>
                        @error('theme_color') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <span wire:loading.remove wire:target="saveSettings">{{ __('Save Settings') }}</span>
                            <span wire:loading wire:target="saveSettings"><i class="fas fa-spinner fa-spin mr-1"></i>
                                {{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
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

                <div x-show="showDeleteModal" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
                    @click.self="showDeleteModal = false">
                    <div class="w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100">
                            <h4 class="text-base font-semibold text-gray-900">{{ __('Delete Classroom') }}</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('Type ":name" to confirm deletion.', ['name' => $classroom->name]) }}
                            </p>
                        </div>

                        <form wire:submit="deleteClassroom" class="px-6 py-5 space-y-4">
                            <div>
                                <input wire:model="deleteConfirm" type="text"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-0 focus:border-red-500"
                                    placeholder="{{ __('Type classroom name here...') }}" autocomplete="off">
                                @error('deleteConfirm') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
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
                                    <span wire:loading wire:target="deleteClassroom" class="inline-flex items-center">
                                        <i class="fas fa-spinner fa-spin mr-1.5"></i>{{ __('Deleting...') }}
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>