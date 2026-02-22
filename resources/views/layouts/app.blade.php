<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'LiteLearning' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Google+Sans+Text:wght@400;500;700&family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Quill.js Theme (Snow) -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">



    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="h-full overflow-hidden bg-gray-50 font-sans antialiased"
    x-data="{ sidebarOpen: true, mobileSidebar: false }" style="zoom: {{ (int) (auth()->user()->ui_scale ?? 100) }}%;">
    <div class="h-screen flex overflow-hidden">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:shrink-0 overflow-hidden flex flex-col"
            :class="{ '-translate-x-full': !mobileSidebar, 'translate-x-0': mobileSidebar }">
            <!-- Logo -->
            <div class="flex items-center h-16 px-6 border-b border-gray-200">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-sm"></i>
                    </div>
                    <span class="text-lg font-bold text-gray-900">LiteLearning</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-1 flex-1 min-h-0 overflow-y-auto" style="scrollbar-gutter: stable">
                @php
                    $navItemClass = fn(bool $isActive): string => $isActive
                        ? 'bg-indigo-50 text-indigo-700'
                        : 'text-gray-700 hover:bg-gray-100';

                    $isDashboardActive = request()->routeIs('dashboard');
                    $isClassroomsActive = request()->routeIs('classrooms');
                    $isCalendarActive = request()->routeIs('calendar');
                    $isToReviewActive = request()->routeIs('to-review');
                @endphp
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $navItemClass($isDashboardActive) }}">
                    <i class="fas fa-home w-5 mr-3 text-center"></i>
                    {{ __('Dashboard') }}
                </a>

                @if(!auth()->user()->isTeacher())
                    <a href="{{ route('classrooms') }}"
                        class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $navItemClass($isClassroomsActive) }}">
                        <i class="fas fa-chalkboard w-5 mr-3 text-center"></i>
                        {{ __('Classrooms') }}
                    </a>
                @endif

                <a href="{{ route('calendar') }}"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $navItemClass($isCalendarActive) }}">
                    <i class="fas fa-calendar-alt w-5 mr-3 text-center"></i>
                    {{ __('Calendar') }}
                </a>

                @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                    <div class="pt-4">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Teaching') }}
                        </p>
                        <div class="mt-2 space-y-1">
                            @php
                                $isMyClassesActive = request()->routeIs('classrooms') && request()->query('filter') === 'teaching';
                            @endphp
                            <a href="{{ route('classrooms') }}?filter=teaching"
                                class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $navItemClass($isMyClassesActive) }}">
                                <i class="fas fa-chalkboard-teacher w-5 mr-3 text-center"></i>
                                {{ __('My Classes') }}
                            </a>
                            <a href="{{ route('to-review') }}"
                                class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $navItemClass($isToReviewActive) }}">
                                <i class="fas fa-tasks w-5 mr-3 text-center"></i>
                                {{ __('To Review') }}
                            </a>
                        </div>
                    </div>
                @endif

                @if(auth()->user()->isStudent())
                    <div class="pt-4">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Enrolled') }}
                        </p>
                        <div class="mt-2 space-y-1" data-sortable-sidebar data-sidebar-list="enrolled">
                            @php
                                $pinnedEnrolledIds = \App\Models\ClassroomSidebarPreference::query()
                                    ->where('user_id', auth()->id())
                                    ->where('is_pinned', true)
                                    ->whereIn('classroom_id', auth()->user()->enrolledClassrooms()->where('is_archived', false)->pluck('classrooms.id'))
                                    ->orderBy('position')
                                    ->pluck('classroom_id')
                                    ->all();

                                $pinnedEnrolledMap = \App\Models\Classroom::query()
                                    ->whereIn('id', $pinnedEnrolledIds)
                                    ->get()
                                    ->keyBy('id');

                                $enrolledClasses = collect($pinnedEnrolledIds)
                                    ->map(fn($id) => $pinnedEnrolledMap->get($id))
                                    ->filter();
                            @endphp
                            @foreach($enrolledClasses as $ec)
                                <a href="{{ route('classroom.show', $ec) }}" data-classroom-id="{{ $ec->id }}"
                                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $navItemClass(request()->routeIs('classroom.show') && optional(request()->route('classroom'))->id === $ec->id) }}">
                                    <div class="w-5 h-5 rounded mr-3 shrink-0" style="background-color: {{ $ec->theme_color }}">
                                    </div>
                                    <span class="truncate">{{ $ec->name }}</span>
                                </a>
                            @endforeach
                            <p data-empty-pinned
                                class="px-3 py-2 text-xs text-gray-400 {{ $enrolledClasses->isEmpty() ? '' : 'hidden' }}">
                                {{ __('No pinned classrooms.') }}
                            </p>
                        </div>
                    </div>
                @endif

                @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                    <div class="pt-4">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Classes') }}</p>
                        <div class="mt-2 space-y-1" data-sortable-sidebar data-sidebar-list="teaching">
                            @php
                                $pinnedTeachingIds = \App\Models\ClassroomSidebarPreference::query()
                                    ->where('user_id', auth()->id())
                                    ->where('is_pinned', true)
                                    ->whereIn('classroom_id', auth()->user()->ownedClassrooms()->where('is_archived', false)->pluck('id'))
                                    ->orderBy('position')
                                    ->pluck('classroom_id')
                                    ->all();

                                $pinnedTeachingMap = \App\Models\Classroom::query()
                                    ->whereIn('id', $pinnedTeachingIds)
                                    ->get()
                                    ->keyBy('id');

                                $teachingClasses = collect($pinnedTeachingIds)
                                    ->map(fn($id) => $pinnedTeachingMap->get($id))
                                    ->filter();
                            @endphp
                            @foreach($teachingClasses as $tc)
                                <a href="{{ route('classroom.show', $tc) }}" data-classroom-id="{{ $tc->id }}"
                                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $navItemClass(request()->routeIs('classroom.show') && optional(request()->route('classroom'))->id === $tc->id) }}">
                                    <div class="w-5 h-5 rounded mr-3 shrink-0" style="background-color: {{ $tc->theme_color }}">
                                    </div>
                                    <span class="truncate">{{ $tc->name }}</span>
                                </a>
                            @endforeach
                            <p data-empty-pinned
                                class="px-3 py-2 text-xs text-gray-400 {{ $teachingClasses->isEmpty() ? '' : 'hidden' }}">
                                {{ __('No pinned classrooms.') }}
                            </p>
                        </div>
                    </div>
                @endif
            </nav>
        </aside>

        <!-- Mobile sidebar overlay -->
        <div x-show="mobileSidebar" x-cloak x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 bg-opacity-50 z-20 lg:hidden"
            @click="mobileSidebar = false"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="shrink-0 bg-white border-b border-gray-200 z-10">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <div class="flex items-center">
                        <button @click="mobileSidebar = !mobileSidebar"
                            class="lg:hidden mr-3 p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="text-lg font-semibold text-gray-800 hidden sm:block">
                            @hasSection('breadcrumb')
                                @yield('breadcrumb')
                            @else
                                @yield('page-title', __('Dashboard'))
                            @endif
                        </h1>
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Create / Join buttons -->
                        @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                            @livewire('classroom.create')
                        @endif
                        @if(auth()->user()->isStudent())
                            @livewire('classroom.join-classroom')
                        @endif

                        <!-- Notifications -->
                        <button
                            class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-bell"></i>
                        </button>

                        <!-- User Menu -->
                        <div x-data="{ open: false }" class="relative dropdown-menu" id="user-dropdown-menu">
                            <button @click="open = !open" aria-haspopup="menu" aria-controls="user-dropdown-menu-list"
                                :aria-expanded="open ? 'true' : 'false'"
                                class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                    class="w-8 h-8 rounded-full object-cover">
                                <span
                                    class="hidden sm:block text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 hidden sm:block"></i>
                            </button>

                            <div id="user-dropdown-menu-popover" data-popover x-show="open"
                                :aria-hidden="open ? 'false' : 'true'" x-cloak @click.outside="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                <div role="menu" id="user-dropdown-menu-list" aria-labelledby="user-dropdown-menu"
                                    class="outline-none">
                                    <div role="group" aria-labelledby="user-menu-account">
                                        <div class="px-4 py-3 border-b border-gray-100" role="heading"
                                            id="user-menu-account">
                                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mt-1 capitalize">
                                                {{ __(ucfirst(auth()->user()->role)) }}
                                            </span>
                                        </div>
                                        <a href="{{ route('profile') }}" role="menuitem"
                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">
                                            <i class="fas fa-user-circle w-4 mr-3"></i> {{ __('Profile') }}
                                        </a>
                                        <a href="{{ route('settings') }}" role="menuitem"
                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">
                                            <i class="fas fa-cog w-4 mr-3"></i> {{ __('Settings') }}
                                        </a>
                                    </div>
                                    <hr class="my-1 border-gray-100">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" role="menuitem"
                                            class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 cursor-pointer">
                                            <i class="fas fa-sign-out-alt w-4 mr-3"></i> {{ __('Sign Out') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 min-h-0 p-4 sm:p-6 overflow-y-auto" style="scrollbar-gutter: stable">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot }}
                @endif
            </main>
        </div>
    </div>

    @if(session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-cloak
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed bottom-4 right-4 z-100 w-[calc(100%-2rem)] max-w-sm rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700 shadow-lg">
            <div class="flex items-start gap-2">
                <i class="fas fa-check-circle mt-0.5"></i>
                <p class="flex-1">{{ session('message') }}</p>
                <button type="button" @click="show = false" class="text-green-600 hover:text-green-800 cursor-pointer">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        </div>
    @endif

    @livewireScripts

    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>


    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('classroom-updated', (event) => {
                const data = Array.isArray(event) ? event[0] : event;

                // Update sidebar items
                document.querySelectorAll(`a[data-classroom-id="${data.id}"]`).forEach(el => {
                    const colorIndicator = el.querySelector('div.w-5.h-5');
                    if (colorIndicator) colorIndicator.style.backgroundColor = data.color;

                    const nameSpan = el.querySelector('span.truncate');
                    if (nameSpan) nameSpan.textContent = data.name;
                });
            });
        });
    </script>
</body>

</html>