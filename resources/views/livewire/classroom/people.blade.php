@section('page-title', __('People') . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ __('Classrooms') }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('classroom.show', $classroom) }}"
            class="text-gray-500 hover:text-indigo-600 transition-colors">{{ $classroom->name }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-semibold">{{ __('People') }}</span>
    </nav>
@endsection

<div class="max-w-3xl mx-auto">
    <!-- Back -->
    <a href="{{ route('classroom.show', $classroom) }}"
        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i> {{ __('Back to :name', ['name' => $classroom->name]) }}
    </a>

    <!-- Teacher -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Teacher') }}</h3>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center">
                <img src="{{ $classroom->teacher->avatar_url }}" class="w-12 h-12 rounded-full mr-4">
                <div>
                    <p class="font-semibold text-gray-900">{{ $classroom->teacher->name }}</p>
                    <p class="text-sm text-gray-500">{{ $classroom->teacher->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Students -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ __('Students') }} <span class="text-gray-400 font-normal">({{ $classroom->members->count() }})</span>
            </h3>

            @if($classroom->members->count() > 0 && ($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin()))
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors cursor-pointer">
                        <i class="fas fa-ellipsis-v px-1"></i>
                    </button>
                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-10">
                        <button wire:click="removeAllMembers" wire:confirm="{{ __('classrooms.remove_all_confirm') }}"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-users-slash w-4"></i> {{ __('classrooms.remove_all') }}
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
            @forelse($classroom->members as $member)
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <img src="{{ $member->avatar_url }}" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                            <p class="text-xs text-gray-500">{{ $member->email }}</p>
                        </div>
                    </div>
                    @if($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.outside="open = false"
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-colors cursor-pointer">
                                <i class="fas fa-ellipsis-v px-1"></i>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms x-cloak
                                class="absolute right-0 top-10 w-44 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-10">
                                <button wire:click="removeMember({{ $member->id }})"
                                    wire:confirm="{{ __('classrooms.remove_confirm', ['name' => $member->name]) }}"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2 cursor-pointer">
                                    <i class="fas fa-user-times w-4"></i> {{ __('classrooms.remove_student') }}
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">{{ __('No students enrolled yet.') }}</div>
            @endforelse
        </div>
    </div>
</div>