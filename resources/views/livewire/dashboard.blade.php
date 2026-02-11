@section('page-title', 'Dashboard')

<div>
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 mb-6 text-white animate__animated animate__fadeIn">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                <p class="mt-1 text-indigo-100">{{ now()->format('l, F j, Y') }}</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-graduation-cap text-6xl text-white/20"></i>
            </div>
        </div>
    </div>

    @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
    <!-- Teacher Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard text-indigo-600 text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['classrooms'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500">Classrooms</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-green-600 text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['students'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500">Students</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600 text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['assignments'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500">Assignments</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600 text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500">Pending Review</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Classrooms -->
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">My Classrooms</h3>
                <a href="{{ route('classrooms') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                    View all <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            @if($classrooms->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chalkboard text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No classrooms yet</h3>
                    <p class="text-gray-500 mb-4">
                        @if(auth()->user()->isTeacher())
                            Create your first classroom to get started.
                        @else
                            Join a classroom using a class code.
                        @endif
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($classrooms->take(4) as $classroom)
                    <a href="{{ route('classroom.show', $classroom) }}" class="group">
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <!-- Cover -->
                            <div class="h-24 relative" style="background-color: {{ $classroom->theme_color }}">
                                <div class="absolute inset-0 bg-black/10"></div>
                                <div class="absolute bottom-3 left-4">
                                    <h4 class="text-white font-bold text-lg leading-tight truncate max-w-[200px]">{{ $classroom->name }}</h4>
                                    <p class="text-white/80 text-sm">{{ $classroom->section }}</p>
                                </div>
                                @if($classroom->isOwnedBy(auth()->user()))
                                    <span class="absolute top-3 right-3 bg-white/20 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-crown mr-1"></i> Owner
                                    </span>
                                @endif
                            </div>
                            <!-- Info -->
                            <div class="p-4">
                                <p class="text-sm text-gray-500 truncate">{{ $classroom->teacher->name }}</p>
                                <div class="flex items-center justify-between mt-3">
                                    <div class="flex items-center text-xs text-gray-400">
                                        <i class="fas fa-users mr-1"></i>
                                        {{ $classroom->students()->count() }} students
                                    </div>
                                    <div class="flex items-center text-xs text-gray-400">
                                        <i class="fas fa-file-alt mr-1"></i>
                                        {{ $classroom->assignments()->published()->count() }} tasks
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Upcoming Assignments -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming</h3>
            <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
                @forelse($upcomingAssignments as $assignment)
                <a href="{{ route('assignment.show', ['classroom' => $assignment->classroom, 'assignment' => $assignment->id]) }}"
                   class="block p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                             style="background-color: {{ $assignment->classroom->theme_color }}20; color: {{ $assignment->classroom->theme_color }}">
                            @if($assignment->type === 'quiz')
                                <i class="fas fa-question-circle"></i>
                            @else
                                <i class="fas fa-file-alt"></i>
                            @endif
                        </div>
                        <div class="ml-3 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $assignment->title }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $assignment->classroom->name }}</p>
                            <div class="flex items-center mt-1">
                                <i class="fas fa-clock text-xs mr-1 {{ $assignment->due_date->isPast() ? 'text-red-400' : 'text-gray-400' }}"></i>
                                <span class="text-xs {{ $assignment->due_date->isPast() ? 'text-red-500 font-medium' : 'text-gray-400' }}">
                                    {{ $assignment->due_date->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="p-8 text-center">
                    <i class="fas fa-check-circle text-green-400 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-500">No upcoming assignments!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
