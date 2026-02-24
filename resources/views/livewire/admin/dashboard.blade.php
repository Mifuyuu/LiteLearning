@section('page-title', __('Admin Dashboard'))

<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Users Card -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <span class="text-xs font-bold px-2 py-1 bg-green-50 text-green-600 rounded-full">
                    +{{ $stats['new_users_month'] }} {{ __('this month') }}
                </span>
            </div>
            <p class="text-gray-500 text-sm font-medium">{{ __('Total Users') }}</p>
            <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_users']) }}</h3>
        </div>

        <!-- Classrooms Card -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-university text-indigo-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-400">
                    {{ $stats['active_classrooms'] }} {{ __('active') }}
                </span>
            </div>
            <p class="text-gray-500 text-sm font-medium">{{ __('Classrooms') }}</p>
            <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_classrooms']) }}</h3>
        </div>

        <!-- Assignments Card -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-file-alt text-purple-600 text-xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">{{ __('Total Assignments') }}</p>
            <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_assignments']) }}</h3>
        </div>

        <!-- Transactions Card -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-shopping-cart text-amber-600 text-xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">{{ __('Store Purchases') }}</p>
            <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['store_transactions']) }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Growth Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
            <h4 class="text-lg font-bold text-gray-900 mb-6">{{ __('User Growth (Last 6 Months)') }}</h4>
            <div class="h-64 flex items-end justify-between px-2">
                @foreach($growthData as $data)
                    <div class="flex flex-col items-center flex-1 gap-2">
                        <div class="w-full max-w-[40px] bg-indigo-500 rounded-t-lg transition-all hover:bg-indigo-600 relative group"
                            style="height: {{ $stats['total_users'] > 0 ? (max(1, ($data['count'] / $stats['total_users']) * 200)) : 1 }}px">
                            <div
                                class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                {{ $data['count'] }}
                            </div>
                        </div>
                        <span class="text-xs text-gray-500 font-medium">{{ $data['month'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activity / Bug Reports -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h4 class="text-lg font-bold text-gray-900">{{ __('Recent Bug Reports') }}</h4>
                <a href="{{ route('admin.reports') }}"
                    class="text-sm text-indigo-600 font-medium hover:text-indigo-700">
                    {{ __('View all') }}
                </a>
            </div>

            <div class="space-y-4">
                @forelse($stats['recent_bug_reports'] as $report)
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100 italic font-medium">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shrink-0">
                            <i class="fas fa-bug text-red-500 text-xs text-center"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-gray-700 truncate line-clamp-1">{{ $report->message }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $report->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-gray-200 text-4xl mb-3"></i>
                        <p class="text-gray-500 text-sm">{{ __('No recent bug reports!') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>