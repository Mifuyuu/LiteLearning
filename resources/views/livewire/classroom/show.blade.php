@section('page-title', $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-gray-500 hover:text-indigo-600 transition-colors">{{ __('Classrooms') }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-semibold">{{ $classroom->name }}</span>
    </nav>
@endsection

<div>
    <!-- Classroom Header -->
    <div class="rounded-2xl overflow-hidden mb-6 relative" style="background-color: {{ $classroom->theme_color }}">
        <div class="absolute inset-0 bg-gradient-to-b from-black/10 to-black/40"></div>
        <div class="relative p-6 sm:p-8">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $classroom->name }}</h1>
                    <p class="text-white/80 mt-1">{{ $classroom->section }} &middot; {{ $classroom->subject }}</p>
                    <p class="text-white/60 text-sm mt-2">{{ $classroom->teacher->name }}</p>
                </div>
                @if($classroom->isOwnedBy(auth()->user()))
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-1 z-10">
                        <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-cog w-4 mr-2"></i> {{ __('Class Settings') }}
                        </button>
                        <div class="px-4 py-2 text-sm text-gray-500">
                            <span class="font-medium">{{ __('Class code') }}:</span>
                            <span class="font-mono text-indigo-600">{{ $classroom->code }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-gray-200 mb-6 overflow-x-auto">
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
            <button onclick="navigator.clipboard.writeText('{{ $classroom->code }}')" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg" title="Copy code">
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
                            <button @click="expanded = false" wire:click="$set('newAnnouncement', '')" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">{{ __('Cancel') }}</button>
                            <button wire:click="postAnnouncement" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
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
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden animate__animated animate__fadeIn" wire:key="announcement-{{ $announcement->id }}">
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
                    <div class="mt-3 text-sm text-gray-700 whitespace-pre-wrap">{!! nl2br(e($announcement->content)) !!}</div>
                </div>

                <!-- Comments -->
                @livewire('classroom.stream-comment', ['announcementId' => $announcement->id], key('comment-'.$announcement->id))
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
    <div class="max-w-3xl mx-auto">
        @if($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
        <div class="mb-6">
            <a href="{{ route('assignment.create', $classroom) }}"
               class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i> {{ __('Create') }}
            </a>
        </div>
        @endif

        <!-- Assignments List -->
        <div class="space-y-3">
            @forelse($classroom->assignments as $assignment)
            <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}"
               class="block bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-all group"
               wire:key="assignment-{{ $assignment->id }}">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background-color: {{ $classroom->theme_color }}20; color: {{ $classroom->theme_color }}">
                        @if($assignment->type === 'quiz')
                            <i class="fas fa-question-circle text-lg"></i>
                        @elseif($assignment->type === 'material')
                            <i class="fas fa-book text-lg"></i>
                        @else
                            <i class="fas fa-file-alt text-lg"></i>
                        @endif
                    </div>
                    <div class="ml-4 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors truncate">{{ $assignment->title }}</h4>
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium capitalize ml-2 flex-shrink-0
                                {{ $assignment->type === 'quiz' ? 'bg-purple-100 text-purple-700' : ($assignment->type === 'material' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                {{ __(ucfirst($assignment->type)) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-4 mt-1">
                            @if($assignment->due_date)
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                {{ __('Due') }} {{ $assignment->due_date->translatedFormat('j M, H:i') }}
                                @if($assignment->isOverdue())
                                    <span class="text-red-500 font-medium">({{ __('Overdue') }})</span>
                                @endif
                            </span>
                            @endif
                            @if($assignment->type !== 'material')
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-users mr-1"></i>
                                {{ $assignment->submittedCount() }}/{{ $classroom->students()->count() }} {{ __('turned in') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <i class="fas fa-clipboard text-gray-300 text-4xl mb-3"></i>
                <p class="text-gray-500">{{ __('No assignments yet.') }}</p>
            </div>
            @endforelse
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
                    <p class="text-sm text-gray-400 mt-1">{{ __('Share the class code') }} <strong class="text-indigo-600 font-mono">{{ $classroom->code }}</strong> {{ __('with your students.') }}</p>
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
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 sticky left-0 bg-gray-50">{{ __('Student') }}</th>
                            @foreach($classroom->assignments->where('type', '!=', 'material') as $assignment)
                            <th class="text-center px-4 py-3 font-medium text-gray-600 min-w-[120px]">
                                <div class="truncate max-w-[100px]" title="{{ $assignment->title }}">{{ $assignment->title }}</div>
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
                                <div class="flex items-center">
                                    <img src="{{ $student->avatar_url }}" class="w-8 h-8 rounded-full mr-2">
                                    <span class="font-medium text-gray-900">{{ $student->name }}</span>
                                </div>
                            </td>
                            @php $grades = []; @endphp
                            @foreach($classroom->assignments->where('type', '!=', 'material') as $assignment)
                            @php
                                $submission = $assignment->submissions->where('user_id', $student->id)->first();
                                $score = $submission?->score;
                                if ($score !== null) $grades[] = ($score / $assignment->max_score) * 100;
                            @endphp
                            <td class="text-center px-4 py-3">
                                @if($submission)
                                    @if($submission->status === 'graded')
                                        <span class="font-medium {{ $score >= ($assignment->max_score * 0.7) ? 'text-green-600' : ($score >= ($assignment->max_score * 0.5) ? 'text-amber-600' : 'text-red-600') }}">
                                            {{ $score }}
                                        </span>
                                    @elseif($submission->status === 'turned_in')
                                        <span class="text-blue-500 text-xs"><i class="fas fa-check"></i> {{ __('Turned in') }}</span>
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
</div>
