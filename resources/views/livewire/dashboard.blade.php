@section('page-title', __('Dashboard'))

<div class="animate__animated animate__fadeIn">
    @if(auth()->user()->isStudent() && $gamification)
        <div class="flex flex-col xl:flex-row gap-4 xl:h-[calc(100vh-6rem)]">
            <!-- Left Content: Gamification Stats & Classrooms -->
            <div class="flex-1 space-y-6 xl:overflow-y-auto xl:pr-4 custom-scrollbar pb-6 xl:pb-0">
    @endif

            <!-- Welcome Banner -->
            <div
                class="bg-linear-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 {{ (!auth()->user()->isStudent() || !$gamification) ? 'mb-6' : '' }} text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold">
                            {{ __('Welcome back, :name!', ['name' => auth()->user()->name]) }} 👋
                        </h2>
                        <p class="mt-1 text-indigo-100">{{ now()->translatedFormat('l, j F Y') }}</p>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-graduation-cap text-6xl text-white/20"></i>
                    </div>
                </div>
            </div>

            @if(auth()->user()->isStudent() && $gamification)
                <!-- Gamification Stats -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center shrink-0">
                                <i class="gsi-gemstone-blue text-blue-500 text-3xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-bold text-gray-900">{{ $gamification['coins'] }}</p>
                                <p class="text-sm text-gray-500">{{ __('Coins') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                                <i class="gsi-flash-lime text-green-500 text-3xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-bold text-gray-900">{{ $gamification['level'] }}</p>
                                <p class="text-sm text-gray-500">{{ __('Level') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                                <i class="gsi-cup-gold text-amber-500 text-3xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-bold text-gray-900">{{ $gamification['achievements'] }}</p>
                                <p class="text-sm text-gray-500">{{ __('Achievements') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center shrink-0">
                                <i class="gsi-star-pirple text-purple-500 text-3xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-bold text-gray-900">{{ $gamification['badges'] }}</p>
                                <p class="text-sm text-gray-500">{{ __('Badges') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-5 list-none">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-semibold text-gray-700">{{ __('Level Progress') }}</h3>
                        <span class="text-xs text-gray-500">{{ $gamification['xp_to_next'] }}
                            {{ __('XP to next level') }}</span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-b from-blue-400 to-blue-600 rounded-full transition-all"
                            style="width: {{ $gamification['progress_percent'] }}%"></div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">{{ __('Total XP') }}: {{ $gamification['xp'] }}</p>
                </div>
            @endif

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
                                <p class="text-sm text-gray-500">{{ __('Classrooms') }}</p>
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
                                <p class="text-sm text-gray-500">{{ __('Students') }}</p>
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
                                <p class="text-sm text-gray-500">{{ __('Assignments') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-purple-600 text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                                <p class="text-sm text-gray-500">{{ __('Pending Review') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="{{ auth()->user()->isStudent() ? '' : 'mt-6' }}">
                <!-- Classrooms -->
                <div class="w-full">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('My Classrooms') }}</h3>
                        <a href="{{ route('classrooms') }}"
                            class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                            {{ __('View all') }} <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    @if($classrooms->isEmpty())
                        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-chalkboard text-gray-400 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No classrooms yet') }}</h3>
                            <p class="text-gray-500 mb-4">
                                @if(auth()->user()->isTeacher())
                                    {{ __('Create your first classroom to get started.') }}
                                @else
                                    {{ __('Join a classroom using a class code.') }}
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($classrooms->take(6) as $classroom)
                                <a href="{{ route('classroom.show', $classroom) }}" class="group">
                                    <div
                                        class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                                        <!-- Cover -->
                                        <div class="h-24 relative" style="background-color: {{ $classroom->theme_color }}">
                                            <div class="absolute inset-0 bg-black/10"></div>
                                            <div class="absolute bottom-3 left-4">
                                                <h4 class="text-white font-bold text-lg leading-tight truncate max-w-50">
                                                    {{ $classroom->name }}
                                                </h4>
                                                <p class="text-white/80 text-sm">{{ $classroom->section }}</p>
                                            </div>
                                            @if($classroom->isOwnedBy(auth()->user()))
                                                <span
                                                    class="absolute top-3 right-3 bg-white/20 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-full">
                                                    <i class="fas fa-crown mr-1"></i> {{ __('Owner') }}
                                                </span>
                                            @endif
                                        </div>
                                        <!-- Info -->
                                        <div class="p-4">
                                            <p class="text-sm text-gray-500 truncate">{{ $classroom->teacher->name }}</p>
                                            <div class="flex items-center justify-between mt-3">
                                                <div class="flex items-center text-xs text-gray-400">
                                                    <i class="fas fa-users mr-1"></i>
                                                    {{ $classroom->students()->count() }} {{ __('students') }}
                                                </div>
                                                <div class="flex items-center text-xs text-gray-400">
                                                    <i class="fas fa-file-alt mr-1"></i>
                                                    {{ $classroom->assignments()->published()->count() }}
                                                    {{ __('assignments') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if(auth()->user()->isStudent() && $gamification)
                    </div>

                    <!-- Right Profile Card (Student) -->
                    <div class="w-full xl:w-80 shrink-0">
                        <div class="sticky top-0">
                            <div
                                class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                <div class="h-24 bg-linear-to-r from-indigo-500 to-purple-500"></div>
                                <div class="w-full px-6 flex justify-start -mt-12 mb-4 relative z-10">
                                    <x-user-avatar :user="auth()->user()" size="w-24 h-24" border="border-4 border-white" />
                                </div>
                                <div class="px-6 pb-6 text-left relative z-10">
                                    <h3 class="text-xl font-bold mb-1 {{ auth()->user()->active_name_color ?? 'text-gray-900' }}">
                                        {{ auth()->user()->name }}</h3>
                                    <p class="text-sm text-gray-500 mb-4">{{ auth()->user()->email }}</p>

                                    <div
                                        class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-sm font-medium mb-4 border border-indigo-100">
                                        <i class="fas fa-user-graduate mr-2"></i> {{ __('Student') }}
                                    </div>

                                    <a href="{{ route('profile') }}"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 transition-colors">
                                        <i class="fas fa-edit mr-2"></i> {{ __('View Profile') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
</div>