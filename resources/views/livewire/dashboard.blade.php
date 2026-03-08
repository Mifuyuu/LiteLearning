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

            @if(auth()->user()->isStudent() && $gamification && $studentAnalytics)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
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
                            <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center shrink-0">
                                <i class="fas fa-file-circle-check text-sky-500 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-bold text-gray-900">{{ $studentAnalytics['totals']['assignments'] }}</p>
                                <p class="text-sm text-gray-500">{{ __('งานทั้งหมด') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center shrink-0">
                                <i class="fas fa-hourglass-half text-orange-500 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-bold text-gray-900">{{ $studentAnalytics['totals']['pending_review'] }}</p>
                                <p class="text-sm text-gray-500">{{ __('รอตรวจ') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0">
                                <i class="fas fa-chart-line text-emerald-500 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-bold text-gray-900">{{ $studentAnalytics['totals']['average_score'] }}</p>
                                <p class="text-sm text-gray-500">{{ __('คะแนนเฉลี่ย') }}</p>
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
                        <div class="h-full bg-linear-to-b from-blue-400 to-blue-600 rounded-full transition-all"
                            style="width: {{ $gamification['progress_percent'] }}%"></div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">{{ __('Total XP') }}: {{ $gamification['xp'] }}</p>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
                    <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ __('ภาพรวมสถานะงาน') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('ดูว่างานของคุณอยู่ในสถานะไหนบ้าง') }}</p>
                            </div>
                        </div>
                        <div class="h-72" wire:ignore>
                            <canvas id="student-assignment-status-chart"></canvas>
                        </div>
                    </div>

                    <div class="xl:col-span-3 bg-white rounded-xl border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ __('ความคืบหน้าแต่ละห้องเรียน') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('เทียบงานที่ส่งแล้วกับงานที่ยังเหลือในแต่ละห้อง') }}</p>
                            </div>
                        </div>
                        <div class="h-72" wire:ignore>
                            <canvas id="student-classroom-progress-chart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
                    <div class="xl:col-span-3 bg-white rounded-xl border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ __('กิจกรรมการส่งงาน 7 วันล่าสุด') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('ดูจำนวนงานที่คุณส่งในแต่ละวัน') }}</p>
                            </div>
                        </div>
                        <div class="h-72" wire:ignore>
                            <canvas id="student-recent-activity-chart"></canvas>
                        </div>
                    </div>

                    <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ __('ใกล้ถึงกำหนดส่ง') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('งานที่กำลังจะครบกำหนดเร็วที่สุด') }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @forelse($upcomingAssignments->take(5) as $assignment)
                                <a href="{{ route('assignment.show', [$assignment->classroom, $assignment]) }}"
                                    class="block rounded-xl border border-gray-200 bg-gray-50 p-4 hover:border-indigo-200 hover:bg-indigo-50/50 transition-colors">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-1 h-3 w-3 rounded-full shrink-0"
                                            style="background-color: {{ $assignment->classroom->themeCategory?->color ?? '#8B5CF6' }}"></div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-gray-900 truncate">{{ $assignment->title }}</p>
                                                    <p class="text-sm text-gray-500 truncate">{{ $assignment->classroom->name }}</p>
                                                </div>
                                                <span class="text-xs font-medium px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-600 shrink-0">
                                                    {{ $assignment->due_date?->diffForHumans() }}
                                                </span>
                                            </div>
                                            <p class="mt-2 text-xs text-gray-500">
                                                {{ __('ครบกำหนด') }}:
                                                {{ $assignment->due_date?->translatedFormat('j M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                                    {{ __('ตอนนี้ยังไม่มีงานที่ใกล้ถึงกำหนดส่ง') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
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
                                        <div class="h-24 relative" style="background-color: {{ $classroom->themeCategory?->color ?? '#8B5CF6' }}">
                                            <div class="absolute inset-0 bg-black/10"></div>
                                            <div class="absolute bottom-3 left-4">
                                                <h4 class="text-white font-bold text-lg leading-tight truncate max-w-50">
                                                    {{ $classroom->name }}
                                                </h4>
                                                <p class="text-white/80 text-sm truncate max-w-50">{{ $classroom->section }}</p>
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
                                            <p class="text-sm text-gray-500 truncate max-w-[200px]">{{ $classroom->teacher->name }}</p>
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
                                <div class="h-24 bg-linear-to-r from-indigo-500 to-purple-500 relative overflow-hidden">
                                    @if(auth()->user()->cover_image_url)
                                        <img src="{{ auth()->user()->cover_image_url }}" class="w-full h-full object-cover absolute inset-0">
                                    @endif
                                </div>
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
                                        <i class="fas fa-edit mr-2"></i> {{ __('ดูโปรไฟล์') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
    @if(auth()->user()->isStudent() && $studentAnalytics)
        @php
            $studentChartData = [
                'assignmentStatus' => $studentAnalytics['charts']['assignment_status'],
                'classroomProgress' => $studentAnalytics['charts']['classroom_progress'],
                'recentActivity' => $studentAnalytics['charts']['recent_activity'],
            ];
        @endphp

        @once
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                window.liteLearningDashboardCharts = window.liteLearningDashboardCharts ?? {};

                function destroyDashboardChart(key) {
                    if (window.liteLearningDashboardCharts[key]) {
                        window.liteLearningDashboardCharts[key].destroy();
                        delete window.liteLearningDashboardCharts[key];
                    }
                }

                function createOrUpdateDashboardChart(key, canvasId, config) {
                    const canvas = document.getElementById(canvasId);

                    if (!canvas || typeof Chart === 'undefined') {
                        return;
                    }

                    destroyDashboardChart(key);
                    window.liteLearningDashboardCharts[key] = new Chart(canvas, config);
                }

                function renderStudentDashboardCharts(data) {
                    if (!data) {
                        return;
                    }

                    createOrUpdateDashboardChart('assignmentStatus', 'student-assignment-status-chart', {
                        type: 'doughnut',
                        data: {
                            labels: data.assignmentStatus.labels,
                            datasets: [{
                                data: data.assignmentStatus.data,
                                backgroundColor: ['#F59E0B', '#10B981', '#8B5CF6', '#E5E7EB'],
                                borderWidth: 0,
                                hoverOffset: 6,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 10,
                                    },
                                },
                            },
                        },
                    });

                    createOrUpdateDashboardChart('classroomProgress', 'student-classroom-progress-chart', {
                        type: 'bar',
                        data: {
                            labels: data.classroomProgress.labels,
                            datasets: [
                                {
                                    label: 'ส่งแล้ว',
                                    data: data.classroomProgress.completed,
                                    backgroundColor: data.classroomProgress.colors,
                                    borderRadius: 8,
                                    borderSkipped: false,
                                },
                                {
                                    label: 'ยังเหลือ',
                                    data: data.classroomProgress.remaining,
                                    backgroundColor: '#E5E7EB',
                                    borderRadius: 8,
                                    borderSkipped: false,
                                }
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    stacked: true,
                                    grid: {
                                        display: false,
                                    },
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0,
                                    },
                                },
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                            },
                        },
                    });

                    createOrUpdateDashboardChart('recentActivity', 'student-recent-activity-chart', {
                        type: 'line',
                        data: {
                            labels: data.recentActivity.labels,
                            datasets: [{
                                label: 'จำนวนงานที่ส่ง',
                                data: data.recentActivity.submissions,
                                borderColor: '#4F46E5',
                                backgroundColor: 'rgba(79, 70, 229, 0.12)',
                                fill: true,
                                tension: 0.35,
                                pointRadius: 4,
                                pointHoverRadius: 5,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    grid: {
                                        display: false,
                                    },
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0,
                                    },
                                },
                            },
                            plugins: {
                                legend: {
                                    display: false,
                                },
                            },
                        },
                    });
                }

                function bootStudentDashboardCharts() {
                    renderStudentDashboardCharts(@js($studentChartData));
                }

                document.addEventListener('livewire:initialized', () => {
                    bootStudentDashboardCharts();
                    Livewire.hook('morph.updated', ({ component }) => {
                        if (component.el && component.el.querySelector('#student-assignment-status-chart')) {
                            bootStudentDashboardCharts();
                        }
                    });
                });

                document.addEventListener('livewire:navigated', bootStudentDashboardCharts);
            </script>
        @endonce
    @endif
</div>
