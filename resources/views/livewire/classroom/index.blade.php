@section('page-title', __('Classrooms'))

<div class="animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ __('My Classrooms') }}</h2>
            <p class="text-gray-500 mt-1">{{ __('Manage and access your classrooms') }}</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text"
                class="block w-full pl-10 pr-3 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="{{ __('Search classrooms...') }}">
        </div>
        <div class="flex gap-2">
            <button wire:click="$set('filter', 'all')"
                class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                {{ __('All') }}
            </button>
            @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                <button wire:click="$set('filter', 'teaching')"
                    class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $filter === 'teaching' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                    {{ __('Teaching') }}
                </button>
            @endif
            <button wire:click="$set('filter', 'enrolled')"
                class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $filter === 'enrolled' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                {{ __('Enrolled') }}
            </button>
            <button wire:click="$set('filter', 'archived')"
                class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $filter === 'archived' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                {{ __('Archived') }}
            </button>
        </div>
    </div>

    <!-- Classroom Grid -->
    @if($classrooms->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-16 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-wind text-gray-300 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('No classrooms found') }}</h3>
            <p class="text-gray-500 max-w-md mx-auto">
                @if(auth()->user()->isTeacher())
                    {{ __('Get started by creating your first classroom or join one with a code.') }}
                @else
                    {{ __('Join a classroom using the class code provided by your teacher.') }}
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($classrooms as $classroom)
                <a href="{{ route('classroom.show', $classroom) }}" class="group" wire:key="classroom-{{ $classroom->id }}">
                    <div
                        class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                        <!-- Cover -->
                        <div class="h-28 relative" style="background-color: {{ $classroom->theme_color }}">
                            <div class="absolute inset-0 bg-linear-to-b from-transparent to-black/30"></div>
                            @php $isPinned = in_array($classroom->id, $pinnedIds ?? [], true); @endphp
                            <button type="button" wire:click.prevent.stop="togglePin({{ $classroom->id }})"
                                title="{{ $isPinned ? __('Unpin from sidebar') : __('Pin to sidebar') }}"
                                class="absolute top-3 right-3 h-8 w-8 rounded-full backdrop-blur-sm flex items-center justify-center border transition-colors cursor-pointer {{ $isPinned ? 'bg-amber-400/90 border-amber-300 text-white hover:bg-amber-500' : 'bg-white/20 border-white/40 text-white hover:bg-white/30' }}">
                                @if($isPinned)
                                    <i class="fas fa-thumbtack-slash text-xs"></i>
                                @else
                                    <i class="fas fa-thumbtack text-xs"></i>
                                @endif
                            </button>
                            <div class="absolute bottom-3 left-4 right-4">
                                <h4 class="text-white font-bold text-lg leading-tight truncate">{{ $classroom->name }}</h4>
                                <p class="text-white/80 text-sm">{{ $classroom->section }}</p>
                            </div>
                            @if($classroom->isOwnedBy(auth()->user()))
                                <span
                                    class="absolute top-3 left-3 bg-white/20 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-full">
                                    <i class="fas fa-crown mr-1"></i> {{ __('Teacher') }}
                                </span>
                            @endif
                        </div>
                        <!-- Info -->
                        <div class="p-4">
                            <div class="flex items-center mb-3">
                                <img src="{{ $classroom->teacher->avatar_url }}" class="w-8 h-8 rounded-full mr-2" alt="">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 truncate max-w-[100px]">
                                        {{ $classroom->teacher->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $classroom->subject }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-400">
                                <span><i class="fas fa-users mr-1"></i> {{ $classroom->students()->count() }}
                                    {{ __('students') }}</span>
                                <span><i class="fas fa-file-alt mr-1"></i> {{ $classroom->assignments()->published()->count() }}
                                    {{ __('assignments') }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>