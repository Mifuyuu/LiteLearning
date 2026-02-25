@section('page-title', __('admin.classrooms.title'))

<div class="space-y-6 animate__animated animate__fadeIn">
    <!-- Filters and Search -->
    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
            <div class="relative w-full sm:w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all"
                    placeholder="{{ __('admin.classrooms.search_placeholder') }}">
            </div>

            <div class="w-full sm:w-auto">
                <select wire:model.live="statusFilter"
                    class="block w-full sm:w-48 pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-lg bg-gray-50">
                    <option value="">{{ __('admin.classrooms.filter_all') }}</option>
                    <option value="active">{{ __('admin.classrooms.filter_active') }}</option>
                    <option value="archived">{{ __('admin.classrooms.filter_archived') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Classrooms Table -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 uppercase text-[10px] font-bold text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">{{ __('admin.classrooms.col_classroom') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.classrooms.col_teacher') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.classrooms.col_students') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.classrooms.col_status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('admin.classrooms.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($classrooms as $classroom)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg flex-shrink-0 flex items-center justify-center text-white font-bold text-lg"
                                        style="background-color: {{ $classroom->theme_color }}">
                                        {{ substr($classroom->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $classroom->name }}</div>
                                        <div class="text-xs text-gray-500">#{{ $classroom->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img class="h-6 w-6 rounded-full object-cover mr-2 flex-shrink-0"
                                        src="{{ $classroom->teacher->avatar_url }}" alt="">
                                    <span class="text-sm text-gray-700">{{ $classroom->teacher->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-users mr-1.5 opacity-50"></i>
                                    {{ $classroom->members->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                                {{ $classroom->is_archived ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $classroom->is_archived ? __('admin.classrooms.status_archived') : __('admin.classrooms.status_active') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3 text-gray-400">
                                    <a href="{{ route('classroom.show', $classroom) }}"
                                        class="hover:text-indigo-600 transition-colors p-1"
                                        title="{{ __('admin.classrooms.view') }}">
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                    <button wire:click="toggleArchive({{ $classroom->id }})"
                                        class="hover:text-amber-600 transition-colors p-1"
                                        title="{{ $classroom->is_archived ? __('admin.classrooms.restore') : __('admin.classrooms.archive') }}">
                                        <i
                                            class="fas fa-{{ $classroom->is_archived ? 'box-open' : 'archive' }} text-xs"></i>
                                    </button>
                                    <button wire:click="deleteClassroom({{ $classroom->id }})"
                                        wire:confirm="{{ __('admin.classrooms.delete_confirm') }}"
                                        class="hover:text-red-600 transition-colors p-1"
                                        title="{{ __('admin.classrooms.delete') }}">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-chalkboard text-4xl mb-3 opacity-20"></i>
                                    <p>{{ __('admin.classrooms.empty') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classrooms->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $classrooms->links() }}
            </div>
        @endif
    </div>
</div>