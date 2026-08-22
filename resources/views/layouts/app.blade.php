<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    @php
        $routeTitles = [
            'dashboard' => 'แดชบอร์ด',
            'store' => 'ร้านค้า',
            'inventory' => 'คลังเก็บของ',
            'leaderboard' => 'กระดานผู้นำ',
            'achievements' => 'ความสำเร็จ',
            'classrooms' => 'ห้องเรียน',
            'classroom.show' => 'หน้าหลัก',
            'classroom.stream' => 'กระดานสนทนา',
            'classroom.settings' => 'ตั้งค่าห้องเรียน',
            'classroom.work' => 'งานในชั้นเรียน',
            'classroom.roster' => 'สมาชิก',
            'classroom.gradebook' => 'สมุดเกรด',
            'assignment.create' => 'สร้างงานใหม่',
            'material.create' => 'สร้างสื่อการสอน',
            'assignment.show' => 'รายละเอียดงาน',
            'material.show' => 'สื่อการสอน',
            'assignment.grade' => 'ตรวจงาน',
            'calendar' => 'ปฏิทิน',
            'to-review' => 'รอตรวจ',
            'profile' => 'โปรไฟล์',
            'settings' => 'ตั้งค่า',
            'admin.dashboard' => 'จัดการระบบ',
            'admin.users' => 'จัดการผู้ใช้',
            'admin.classrooms' => 'จัดการห้องเรียน',
            'admin.store' => 'จัดการร้านค้า',
            'admin.achievements' => 'จัดการความสำเร็จ',
            'admin.reports' => 'รายงานปัญหา',
            'admin.theme-categories' => 'หมวดหมู่ธีม',
        ];
        $pageTitle = $title ?? ($routeTitles[Route::currentRouteName()] ?? 'แดชบอร์ด');
    @endphp
    <title>{{ $pageTitle }} | LiteLearning</title>

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

<body class="h-full overflow-hidden backdrop-bg font-sans antialiased text-[#101114]" x-data="{ sidebarOpen: true, mobileSidebar: false }">

    {{-- safelist: dynamic name-color classes from store_items.value (Tailwind v4 JIT scan) --}}
    <div class="hidden text-red-500 text-blue-500 text-amber-500 text-purple-600"></div>

    <div class="app-shell relative z-10 flex overflow-clip">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-30 flex w-72 -translate-x-full flex-col overflow-hidden bg-white border-r-3 border-[#dedee5] transition-transform duration-300 ease-in-out lg:static lg:inset-auto lg:translate-x-0 lg:shrink-0"
            :class="{ '-translate-x-full': !mobileSidebar, 'translate-x-0': mobileSidebar }">
            <div class="flex h-[88px] shrink-0 items-center px-6">
                <img src="{{ asset('images/LiteLearn.svg') }}" alt="LiteLearning"
                    class="h-11 w-auto">
            </div>

            <!-- Navigation -->
            @php
                $classroom = request()->route('classroom');
                $isInClassroom = $classroom instanceof \App\Models\Classroom && (
                    request()->routeIs('classroom.show') ||
                    request()->routeIs('classroom.stream') ||
                    request()->routeIs('classroom.work') ||
                    request()->routeIs('classroom.roster') ||
                    request()->routeIs('classroom.settings') ||
                    request()->routeIs('classroom.gradebook') ||
                    request()->routeIs('assignment.*') ||
                    request()->routeIs('material.*')
                );
                $navItemClass = fn(bool $isActive): string => $isActive
                    ? 'bg-[rgba(59,130,246,0.16)] text-[#2563eb] rounded-lg'
                    : 'text-[#686b82] hover:bg-[rgba(59,130,246,0.08)] hover:text-[#2563eb] rounded-lg';
                $isSettingsActive = request()->routeIs('settings');
            @endphp
            @php
                $isDashboardActive    = request()->routeIs('dashboard');
                $isClassroomsActive   = request()->routeIs('classrooms');
                $isCalendarActive     = request()->routeIs('calendar');
                $isToReviewActive     = request()->routeIs('to-review');
                $isStoreActive        = request()->routeIs('store');
                $isInventoryActive    = request()->routeIs('inventory');
                $isLeaderboardActive  = request()->routeIs('leaderboard');
                $isAchievementsActive = request()->routeIs('achievements');

                $isAdminDashboardActive      = request()->routeIs('admin.dashboard');
                $isAdminUsersActive          = request()->routeIs('admin.users');
                $isAdminClassroomsActive     = request()->routeIs('admin.classrooms');
                $isAdminStoreActive          = request()->routeIs('admin.store');
                $isAdminAchievementsActive   = request()->routeIs('admin.achievements');
                $isAdminReportsActive        = request()->routeIs('admin.reports');
                $isAdminThemeCategoriesActive = request()->routeIs('admin.theme-categories');
            @endphp
            <nav class="sidebar-scroll flex-1 min-h-0 overflow-y-auto px-4 pb-5 space-y-4">

                {{-- ======================================================= --}}
                {{-- Section: ทั่วไป / การสอน / การจัดการระบบ                --}}
                {{-- ======================================================= --}}
                @if(!auth()->user()->isAdmin())
                    <div>
                        <p class="px-3 mb-1.5 text-xs font-semibold text-[#9497a9]">ทั่วไป</p>
                        <div class="space-y-1">
                            <a href="{{ route('dashboard') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isDashboardActive) }}">
                                <x-icon name="home{{ $isDashboardActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                แดชบอร์ด
                            </a>

                            @if(auth()->user()->isTeacher())
                                <a href="{{ route('classrooms') }}" wire:navigate
                                    class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isClassroomsActive) }}">
                                    <x-icon name="academic-cap{{ $isClassroomsActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                    ชั้นเรียนของฉัน
                                </a>
                                <a href="{{ route('to-review') }}" wire:navigate
                                    class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isToReviewActive) }}">
                                    <x-icon name="clipboard-document-list{{ $isToReviewActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                    รอตรวจ
                                </a>
                            @endif

                            @if(auth()->user()->isStudent())
                                <a href="{{ route('calendar') }}" wire:navigate
                                    class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isCalendarActive) }}">
                                    <x-icon name="calendar-days{{ $isCalendarActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                    ปฏิทิน
                                </a>
                                <a href="{{ route('classrooms') }}" wire:navigate
                                    class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isClassroomsActive) }}">
                                    <x-icon name="academic-cap{{ $isClassroomsActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                    ห้องเรียน
                                </a>
                                <a href="{{ route('achievements') }}" wire:navigate
                                    class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isAchievementsActive) }}">
                                    <x-icon name="star{{ $isAchievementsActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                    ความสำเร็จ
                                </a>
                                <a href="{{ route('leaderboard') }}" wire:navigate
                                    class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isLeaderboardActive) }}">
                                    <x-icon name="trophy{{ $isLeaderboardActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                    กระดานผู้นำ
                                </a>
                                <a href="{{ route('store') }}" wire:navigate
                                    class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isStoreActive) }}">
                                    <x-icon name="shopping-bag{{ $isStoreActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                    ร้านค้า
                                </a>
                                <a href="{{ route('inventory') }}" wire:navigate
                                    class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isInventoryActive) }}">
                                    <x-icon name="backpack{{ $isInventoryActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                    คลังเก็บของ
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Admin navigation --}}
                    <div>
                        <p class="px-3 mb-1.5 text-xs font-semibold text-[#9497a9]">การจัดการระบบ</p>
                        <div class="space-y-1">
                            <a href="{{ route('admin.dashboard') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isAdminDashboardActive) }}">
                                <x-icon name="chart-bar{{ $isAdminDashboardActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                แดชบอร์ดผู้ดูแล
                            </a>
                            <a href="{{ route('admin.users') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isAdminUsersActive) }}">
                                <x-icon name="users{{ $isAdminUsersActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                จัดการผู้ใช้
                            </a>
                            <a href="{{ route('admin.classrooms') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isAdminClassroomsActive) }}">
                                <x-icon name="academic-cap{{ $isAdminClassroomsActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                จัดการห้องเรียน
                            </a>
                            <a href="{{ route('admin.store') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isAdminStoreActive) }}">
                                <x-icon name="shopping-bag{{ $isAdminStoreActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                จัดการร้านค้า
                            </a>
                            <a href="{{ route('admin.achievements') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isAdminAchievementsActive) }}">
                                <x-icon name="trophy{{ $isAdminAchievementsActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                ความสำเร็จ
                            </a>
                            <a href="{{ route('admin.theme-categories') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isAdminThemeCategoriesActive) }}">
                                <x-icon name="sparkles{{ $isAdminThemeCategoriesActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                หมวดหมู่ธีม
                            </a>
                            <a href="{{ route('admin.reports') }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($isAdminReportsActive) }}">
                                <x-icon name="flag{{ $isAdminReportsActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                รายงานปัญหา
                            </a>
                        </div>
                    </div>
                @endif

                {{-- ======================================================= --}}
                {{-- Section: ภายในห้องเรียน (แสดงเฉพาะเมื่ออยู่ใน classroom) --}}
                {{-- ======================================================= --}}
                @if($isInClassroom && $classroom)
                    <div>
                        <p class="px-3 mb-1.5 text-xs font-semibold text-[#9497a9]">ภายในห้องเรียน</p>
                        <div class="space-y-1">
                            @php $active = request()->routeIs('classroom.show'); @endphp
                            <a href="{{ route('classroom.show', $classroom) }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($active) }}">
                                <x-icon :name="'home'.($active ? '-solid' : '')" class="mr-3 h-5 w-5" />
                                หน้าหลัก
                            </a>

                            @php $active = request()->routeIs('classroom.stream'); @endphp
                            <a href="{{ route('classroom.stream', $classroom) }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($active) }}">
                                <x-icon :name="'chat-bubble-left-ellipsis'.($active ? '-solid' : '')" class="mr-3 h-5 w-5" />
                                กระดานสนทนา
                            </a>

                            @php $active = request()->routeIs('classroom.work') || request()->routeIs('assignment.*') || request()->routeIs('material.*'); @endphp
                            <a href="{{ route('classroom.work', ['classroom' => $classroom, 'scope' => 'all']) }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($active) }}">
                                <x-icon :name="'clipboard-document-list'.($active ? '-solid' : '')" class="mr-3 h-5 w-5" />
                                งานในชั้นเรียน
                            </a>

                            @php $active = request()->routeIs('classroom.roster'); @endphp
                            <a href="{{ route('classroom.roster', $classroom) }}" wire:navigate
                                class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($active) }}">
                                <x-icon :name="'users'.($active ? '-solid' : '')" class="mr-3 h-5 w-5" />
                                สมาชิก
                            </a>

                            @if($classroom->canManageClassroom(auth()->user()))
                                @php $active = request()->routeIs('classroom.gradebook'); @endphp
                                <a href="{{ route('classroom.gradebook', $classroom) }}" wire:navigate
                                    class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($active) }}">
                                    <x-icon :name="'chart-bar'.($active ? '-solid' : '')" class="mr-3 h-5 w-5" />
                                    สมุดเกรด
                                </a>
                            @endif

                            @if($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
                                @php $active = request()->routeIs('classroom.settings'); @endphp
                                <a href="{{ route('classroom.settings', $classroom) }}" wire:navigate
                                    class="flex items-center px-3 py-2.5 text-sm font-bold rounded-[12px] transition-colors {{ $navItemClass($active) }}">
                                    <x-icon name="cog-6-tooth{{ $active ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                                    ตั้งค่าห้องเรียน
                                </a>
                            @endif

                            @if(auth()->user()->isStudent())
                                <button type="button" x-data @click="$dispatch('open-leave-classroom', { classroomId: {{ $classroom->id }} })"
                                    class="flex w-full items-center px-3 py-2.5 text-sm font-bold rounded-lg transition-colors text-red-500 hover:bg-red-50 cursor-pointer">
                                    <x-icon name="arrow-right-on-rectangle" class="mr-3 h-5 w-5" />
                                    ออกจากห้องเรียน
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

            </nav>


            <!-- Sidebar Footer -->
            <div class="shrink-0 bg-white p-4 space-y-3">
                <!-- Create/Join class buttons moved from navbar -->
                <div class="w-full">
                    @if(auth()->user()->isTeacher())
                        <button x-data @click="$dispatch('open-create-classroom')"
                            class="btn-3d btn-3d--blue w-full flex items-center justify-center gap-1.5 py-2.5 text-sm font-bold rounded-xl cursor-pointer">
                            <x-icon name="plus" class="h-4 w-4" />{{ 'สร้างห้องเรียน' }}
                        </button>
                    @endif
                </div>

                <!-- Footer Menu Items Group -->
                <div class="space-y-1">
                    <!-- Settings Button (attached to bottom) -->
                    <a href="{{ route('settings') }}" wire:navigate
                        class="flex w-full items-center px-3 py-2.5 text-sm font-bold rounded-[8px] transition-colors {{ $isSettingsActive ? 'bg-[rgba(59,130,246,0.16)] text-[#2563eb]' : 'text-[#686b82] hover:bg-[rgba(59,130,246,0.08)] hover:text-[#2563eb]' }}">
                        <x-icon name="cog-6-tooth{{ $isSettingsActive ? '-solid' : '' }}" class="mr-3 h-5 w-5" />
                        {{ 'ตั้งค่า' }}
                    </a>

                    <!-- Report Issue Button -->
                    <button x-data @click="$dispatch('openReportModal')"
                        class="flex w-full items-center px-3 py-2.5 text-sm font-bold text-[#686b82] hover:bg-[rgba(59,130,246,0.08)] hover:text-[#2563eb] rounded-[8px] transition-colors cursor-pointer">
                        <x-icon name="flag" class="mr-3 h-5 w-5" />
                        {{ 'รายงานปัญหา / เสนอแนะ' }}
                    </button>

                    <!-- Profile footer -->
                    <div class="mt-1.5 flex w-full items-center justify-between rounded-lg border border-[#dedee5] bg-white p-1">
                        <!-- Profile Link -->
                        <a href="{{ route('profile') }}" wire:navigate
                            class="flex flex-1 items-center space-x-2.5 bg-transparent p-1.5 transition-colors hover:bg-[rgba(59,130,246,0.08)] cursor-pointer rounded-[6px] min-w-0">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                class="w-8 h-8 rounded-full object-cover shrink-0">
                            <span class="text-sm font-medium text-[#101114] truncate max-w-27.5">{{ auth()->user()->name }}</span>
                        </a>

                        <!-- Sign Out Button -->
                        <form method="POST" action="{{ route('logout') }}" class="shrink-0 flex items-center">
                            @csrf
                            <button type="submit"
                                class="relative rounded-md p-3 text-[#686b82] transition-colors hover:bg-red-50 hover:text-red-600 cursor-pointer shrink-0"
                                title="ออกจากระบบ">
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
        <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
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
                                @yield('page-title', $pageTitle)
                            @endif
                        </h1>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 min-h-0 overflow-x-hidden overflow-y-auto px-4 pt-6 pb-28 sm:px-6 sm:pb-6" style="scrollbar-gutter: stable">
                <div data-content-width="{{ request()->routeIs('store') ? 'full' : 'contained' }}"
                    class="w-full {{ request()->routeIs('store') ? 'max-w-none' : 'mx-auto max-w-7xl' }}">
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
            toasts: [],
            addToast(message, type = 'success', badgeImage = '') {
                const id = Date.now() + Math.random();
                this.toasts.push({ id, message, type, badgeImage, leaving: false, visible: false });
                requestAnimationFrame(() => {
                    const t = this.toasts.find(t => t.id === id);
                    if (t) t.visible = true;
                });
                const duration = badgeImage ? 5000 : 3000;
                setTimeout(() => this.dismissToast(id), duration);
            },
            dismissToast(id) {
                const t = this.toasts.find(t => t.id === id);
                if (t) t.leaving = true;
                setTimeout(() => this.removeToast(id), 300);
            },
            removeToast(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }
        }" @notify.window="
            addToast($event.detail.message, $event.detail.type || 'success');
        " x-init="
            @if(session()->has('message'))
                addToast('{{ session('message') }}', '{{ session('type', 'success') }}');
            @endif
        ">
        <div class="fixed bottom-4 right-4 z-100 flex flex-col items-end space-y-3">
            <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.visible && !toast.leaving" x-cloak
                x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="rounded-lg border border-[#dedee5] bg-white p-4 text-sm text-[#101114] shadow-lg">
                <div class="flex items-center gap-2">
                    <span class="shrink-0" x-show="!toast.badgeImage" :class="{
                        'text-green-500': toast.type === 'success',
                        'text-red-500': toast.type === 'error',
                        'text-[var(--ll-blue)]': toast.type === 'info'
                    }">
                        <x-icon name="check-circle" class="h-5 w-5" x-show="toast.type === 'success'" />
                        <x-icon name="exclamation-circle" class="h-5 w-5" x-show="toast.type === 'error'" />
                        <x-icon name="information-circle" class="h-5 w-5" x-show="toast.type === 'info'" />
                    </span>
                    <img x-show="toast.badgeImage" :src="toast.badgeImage" class="h-10 w-10 mt-0.5" />
                    <p class="flex-1" x-text="toast.message"></p>
                    <button type="button" @click.stop="dismissToast(toast.id)" class="cursor-pointer transition-colors shrink-0 text-[#9497a9] hover:text-[#686b82]">
                        <x-icon name="x-mark" class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </template>
        </div>
    </div>

    <script>
        // achievementsModal() must stay here: achievement-detail-modal.blade.php renders inside a
        // #[Lazy] Livewire page whose inline <script> tags are NOT executed on hydrate.
        function achievementsModal() {
            return {
                selected: null,
                visible: false,
                open(achievement) {
                    this.selected = achievement;
                    this.visible = true;
                },
                close() {
                    this.visible = false;
                    // ponytail: 150ms matches the card leave transition so the badge image stays during the fade-out
                    setTimeout(() => { this.selected = null; }, 150);
                },
            }
        }
    </script>

    <x-achievement-celebration />

    @livewireScripts




    @livewire('classroom.create')
    @livewire('classroom.leave-classroom')
    @livewire('report-bug')

</body>

</html>
