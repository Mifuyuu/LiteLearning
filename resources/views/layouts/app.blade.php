<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('Dashboard') }}</title>

    <!-- Fonts (preconnect + preload for instant rendering) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap"
        rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="h-full overflow-hidden bg-[#f3f4f6] font-sans antialiased text-[#101114]" x-data="{ sidebarOpen: true, mobileSidebar: false }">

    <div class="relative z-10 flex h-screen overflow-clip">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-30 flex w-72 -translate-x-full flex-col overflow-hidden bg-white transition-transform duration-300 ease-in-out lg:static lg:inset-auto lg:translate-x-0 lg:shrink-0"
            :class="{ '-translate-x-full': !mobileSidebar, 'translate-x-0': mobileSidebar }">
            <div class="flex h-[88px] shrink-0 items-center gap-3 px-6">
                <div class="flex h-11 w-11 items-center justify-center rounded-[12px] bg-[#7132f5] text-white">
                    <x-icon name="academic-cap" class="h-6 w-6" />
                </div>
                <div class="min-w-0">
                    <p class="text-xl font-bold leading-tight tracking-tight text-[#7132f5]" style="letter-spacing: -0.5px">LiteLearning</p>
                    <p class="truncate text-xs font-medium text-[#9497a9]">{{ __(ucfirst(auth()->user()->role)) }}</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 min-h-0 space-y-1 overflow-y-auto px-4 pb-5">
                @php
                    $navItemClass = fn(bool $isActive): string => $isActive
                        ? 'bg-[rgba(133,91,251,0.16)] text-[#7132f5] rounded-[12px]'
                        : 'text-[#686b82] hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5] rounded-[12px]';

                    $isDashboardActive = request()->routeIs('dashboard');
                    $isClassroomsActive = request()->routeIs('classrooms')
                        || request()->routeIs('classroom.show')
                        || request()->routeIs('classroom.work')
                        || request()->routeIs('classroom.roster')
                        || request()->routeIs('classroom.gradebook')
                        || request()->routeIs('assignment.*')
                        || request()->routeIs('material.*');
                    $isCalendarActive = request()->routeIs('calendar');
                    $isToReviewActive = request()->routeIs('to-review');

                    $isStoreActive = request()->routeIs('store');
                    $isLeaderboardActive = request()->routeIs('leaderboard');
                    $isAchievementsActive = request()->routeIs('achievements');
                    $isProfileActive = request()->routeIs('profile');
                    $isSettingsActive = request()->routeIs('settings');

                    // Admin routes
                    $isAdminDashboardActive = request()->routeIs('admin.dashboard');
                    $isAdminUsersActive = request()->routeIs('admin.users');
                    $isAdminClassroomsActive = request()->routeIs('admin.classrooms');
                    $isAdminStoreActive = request()->routeIs('admin.store');
                    $isAdminAchievementsActive = request()->routeIs('admin.achievements');
                    $isAdminReportsActive = request()->routeIs('admin.reports');
                    $isAdminThemeCategoriesActive = request()->routeIs('admin.theme-categories');
                @endphp
                @if(!auth()->user()->isAdmin())
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isDashboardActive) }}">
                        <x-icon name="home{{ $isDashboardActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                        {{ __('Dashboard') }}
                    </a>

                    @if(!auth()->user()->isTeacher())
                        <a href="{{ route('classrooms') }}" wire:navigate
                            class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isClassroomsActive) }}">
                            <x-icon name="academic-cap{{ $isClassroomsActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                            {{ __('Classrooms') }}
                        </a>
                    @endif

                    @if(!auth()->user()->isTeacher())
                    <a href="{{ route('calendar') }}" wire:navigate
                        class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isCalendarActive) }}">
                        <x-icon name="calendar-days{{ $isCalendarActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                        {{ __('Calendar') }}
                    </a>
                    @endif

                    @if(auth()->user()->isStudent())
                        <a href="{{ route('achievements') }}" wire:navigate
                            class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isAchievementsActive) }}">
                            <x-icon name="star{{ $isAchievementsActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                            {{ __('Achievements & Badges') }}
                        </a>
                        <a href="{{ route('leaderboard') }}" wire:navigate
                            class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isLeaderboardActive) }}">
                            <x-icon name="trophy{{ $isLeaderboardActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                            {{ __('Leaderboard') }}
                        </a>
                        <a href="{{ route('store') }}" wire:navigate
                            class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isStoreActive) }}">
                            <x-icon name="shopping-bag{{ $isStoreActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                            {{ __('store.title') }}
                        </a>
                    @endif

                    @if(auth()->user()->isTeacher())
                        <div class="pt-4">
                            <p class="px-3 text-xs font-medium text-[#9497a9] uppercase tracking-widest">
                                {{ __('Teaching') }}
                            </p>
                            <div class="mt-2 space-y-1">
                @php
                    $isMyClassesActive = $isClassroomsActive;
                @endphp
                                <a href="{{ route('classrooms') }}" wire:navigate
                                    class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isMyClassesActive) }}">
                                    <x-icon name="academic-cap{{ $isMyClassesActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                    {{ __('ชั้นเรียนของฉัน') }}
                                </a>
                                <a href="{{ route('to-review') }}" wire:navigate
                                    class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isToReviewActive) }}">
                                    <x-icon name="clipboard-document-list{{ $isToReviewActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                    {{ __('To Review') }}
                                </a>
                            </div>
                        </div>

                    @endif
                @else
                    {{-- Admin-only navigation --}}
                    <div class="pt-2">
                        <p class="px-3 text-xs font-semibold text-gray-400/70 uppercase tracking-wider">
                            {{ __('Administration') }}
                        </p>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.dashboard') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isAdminDashboardActive) }}">
                                <x-icon name="chart-bar{{ $isAdminDashboardActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                {{ __('Admin Dashboard') }}
                            </a>
                            <a href="{{ route('admin.users') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isAdminUsersActive) }}">
                                <x-icon name="users{{ $isAdminUsersActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                {{ __('User Management') }}
                            </a>
                            <a href="{{ route('admin.classrooms') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isAdminClassroomsActive) }}">
                                <x-icon name="academic-cap{{ $isAdminClassroomsActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                {{ __('Classroom Management') }}
                            </a>
                            <a href="{{ route('admin.store') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isAdminStoreActive) }}">
                                <x-icon name="shopping-bag{{ $isAdminStoreActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                {{ __('Store Management') }}
                            </a>
                            <a href="{{ route('admin.achievements') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isAdminAchievementsActive) }}">
                                <x-icon name="trophy{{ $isAdminAchievementsActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                {{ __('Achievements') }}
                            </a>
                            <a href="{{ route('admin.theme-categories') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isAdminThemeCategoriesActive) }}">
                                <x-icon name="sparkles{{ $isAdminThemeCategoriesActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                {{ __('Theme Categories') }}
                            </a>
                            <a href="{{ route('admin.reports') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors {{ $navItemClass($isAdminReportsActive) }}">
                                <x-icon name="flag{{ $isAdminReportsActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                {{ __('Bug Reports') }}
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Account Section -->
            </nav>

            <!-- Sidebar Footer -->
            <div class="shrink-0 bg-white p-4 space-y-3">
                <!-- Create/Join class buttons moved from navbar -->
                <div class="w-full">
                    @if(auth()->user()->isTeacher())
                        <button x-data @click="$dispatch('open-create-classroom')"
                            class="btn-3d btn-3d--indigo w-full flex items-center justify-center gap-1.5 py-2.5 text-sm font-bold rounded-xl cursor-pointer">
                            <x-icon name="plus" class="h-4 w-4" />{{ __('Create Class') }}
                        </button>
                    @endif
                </div>

                <!-- Footer Menu Items Group -->
                <div class="space-y-1">
                    <!-- Settings Button (attached to bottom) -->
                    <a href="{{ route('settings') }}" wire:navigate
                        class="flex w-full items-center px-3 py-2.5 text-sm font-bold rounded-[8px] transition-colors {{ $isSettingsActive ? 'bg-[rgba(133,91,251,0.16)] text-[#7132f5]' : 'text-[#686b82] hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5]' }}">
                        <x-icon name="cog-6-tooth{{ $isSettingsActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                        {{ __('Settings') }}
                    </a>

                    <!-- Report Issue Button -->
                    <button x-data @click="$dispatch('openReportModal')"
                        class="flex w-full items-center px-3 py-2.5 text-sm font-bold text-[#686b82] hover:bg-[rgba(133,91,251,0.08)] hover:text-[#7132f5] rounded-[8px] transition-colors cursor-pointer">
                        <x-icon name="flag" class="mr-3 h-5 w-5" />
                        {{ __('report.button') }}
                    </button>

                    <!-- Profile footer -->
                    <div class="mt-1.5 flex w-full items-center justify-between rounded-[8px] border border-[#dedee5] bg-white p-1">
                        <!-- Profile Link -->
                        <a href="{{ route('profile') }}" wire:navigate
                            class="flex flex-1 items-center space-x-2.5 bg-transparent p-1.5 transition-colors hover:bg-[rgba(133,91,251,0.08)] cursor-pointer rounded-[6px] min-w-0">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                class="w-8 h-8 rounded-full object-cover shrink-0">
                            <span class="text-sm font-medium text-[#101114] truncate max-w-[110px]">{{ auth()->user()->name }}</span>
                        </a>

                        <!-- Sign Out Button -->
                        <form method="POST" action="{{ route('logout') }}" class="shrink-0 flex items-center">
                            @csrf
                            <button type="submit"
                                class="relative rounded-[6px] p-2 text-[#686b82] transition-colors hover:bg-red-50 hover:text-red-600 cursor-pointer shrink-0"
                                title="{{ __('Sign Out') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path fill-rule="evenodd" d="M16.5 3.75a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5h-6a1.5 1.5 0 0 1-1.5-1.5V15a.75.75 0 0 0-1.5 0v3.75a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V5.25a3 3 0 0 0-3-3h-6a3 3 0 0 0-3 3V9A.75.75 0 1 0 9 9V5.25a1.5 1.5 0 0 1 1.5-1.5h6ZM5.78 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 0 0 0 1.06l3 3a.75.75 0 0 0 1.06-1.06l-1.72-1.72H15a.75.75 0 0 0 0-1.5H4.06l1.72-1.72a.75.75 0 0 0 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>


        <!-- Mobile sidebar overlay -->
        <div x-show="mobileSidebar" x-cloak x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 bg-opacity-50 z-20 lg:hidden"
            @click="mobileSidebar = false"></div>

        <!-- Main Content -->
        <div class="flex min-w-0 flex-1 flex-col overflow-hidden bg-[#f3f4f6]">
            <!-- Mobile Header (Visible only on mobile/tablet screens) -->
            <header class="z-10 shrink-0 border-b border-[#dedee5] bg-white lg:hidden">
                <div class="mx-auto flex h-[60px] w-full items-center justify-between px-4 sm:px-6">
                    <div class="flex items-center">
                        <button @click="mobileSidebar = !mobileSidebar"
                            class="mr-3 p-2 rounded-md text-gray-500 hover:text-gray-800 hover:bg-gray-100 cursor-pointer">
                            <x-icon name="bars-3" class="h-5 w-5" />
                        </button>
                        <h1 id="mobile-page-title" class="text-base font-semibold text-slate-800">
                            @hasSection('breadcrumb')
                                @yield('breadcrumb')
                            @else
                                @yield('page-title', __('Dashboard'))
                            @endif
                        </h1>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 min-h-0 overflow-x-hidden overflow-y-auto px-4 py-6 sm:px-6" style="scrollbar-gutter: stable">
                <div data-content-width="{{ (request()->routeIs('dashboard') || request()->routeIs('store')) ? 'full' : 'contained' }}"
                    class="w-full {{ (request()->routeIs('dashboard') || request()->routeIs('store')) ? 'max-w-none' : 'mx-auto max-w-7xl' }}">
                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot }}
                    @endif
                </div>
            </main>
        </div>
    </div>

    <div x-data="{ 
            show: {{ session()->has('message') ? 'true' : 'false' }}, 
            message: '{{ session('message') }}',
            type: '{{ session('type', 'success') }}'
        }" @notify.window="
            message = $event.detail.message; 
            type = $event.detail.type || 'success';
            show = true; 
            setTimeout(() => show = false, 3000);
        " x-init="if(show) setTimeout(() => show = false, 3000)" x-show="show" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-4 right-4 z-100 w-[calc(100%-2rem)] max-w-sm rounded-lg border-2 p-4 text-sm"
        :class="{
            'bg-green-50 border-green-300 text-green-800': type === 'success',
            'bg-red-50 border-red-300 text-red-800': type === 'error',
            'bg-blue-50 border-blue-300 text-blue-800': type === 'info'
        }">
        <div class="flex items-start gap-2">
            <span class="mt-0.5">
                <x-icon name="check-circle" class="h-5 w-5" />
            </span>
            <p class="flex-1" x-text="message"></p>
            <button type="button" @click="show = false" class="cursor-pointer transition-colors" :class="{
                    'text-green-600 hover:text-green-800': type === 'success',
                    'text-red-600 hover:text-red-800': type === 'error',
                    'text-blue-600 hover:text-blue-800': type === 'info'
                }">
                <x-icon name="x-mark" class="h-4 w-4" />
            </button>
        </div>
    </div>

    @livewireScripts




    @livewire('classroom.create')
    @livewire('report-bug')
</body>

</html>
