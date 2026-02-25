@section('page-title', __('admin.users.title'))

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
                    placeholder="{{ __('admin.users.search_placeholder') }}">
            </div>

            <div class="flex items-center gap-3">
                <select wire:model.live="roleFilter"
                    class="px-3 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-lg bg-gray-50">
                    <option value="">{{ __('admin.users.filter_role') }}</option>
                    <option value="admin">{{ __('Admin') }}</option>
                    <option value="teacher">{{ __('Teacher') }}</option>
                    <option value="student">{{ __('Student') }}</option>
                </select>

                <select wire:model.live="statusFilter"
                    class="px-3 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-lg bg-gray-50">
                    <option value="">{{ __('admin.users.filter_status') }}</option>
                    <option value="active">{{ __('admin.users.status_active') }}</option>
                    <option value="inactive">{{ __('admin.users.status_inactive') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 uppercase text-[10px] font-bold text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">{{ __('admin.users.col_user') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.users.col_role') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.users.col_status') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.users.col_joined') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('admin.users.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img class="h-9 w-9 rounded-full object-cover ring-2 ring-gray-100 flex-shrink-0"
                                        src="{{ $user->avatar_url }}" alt="">
                                    <div class="ml-3">
                                        <div class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span
                                                    class="text-[9px] px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded-full font-bold">YOU</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select wire:change="updateRole({{ $user->id }}, $event.target.value)"
                                    class="text-xs font-bold border-0 bg-transparent focus:ring-2 focus:ring-indigo-400 rounded py-1"
                                    @if($user->id === auth()->id()) disabled @endif>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}
                                    </option>
                                    <option value="teacher" {{ $user->role === 'teacher' ? 'selected' : '' }}>
                                        {{ __('Teacher') }}
                                    </option>
                                    <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>
                                        {{ __('Student') }}
                                    </option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleStatus({{ $user->id }})"
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-colors
                                                {{ $user->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}"
                                    @if($user->id === auth()->id()) disabled @endif>
                                    <i class="fas fa-circle mr-1.5 text-[6px]"></i>
                                    {{ $user->is_active ? __('admin.users.status_active') : __('admin.users.status_inactive') }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-3 text-gray-400">
                                    <a href="{{ route('profile') }}" class="hover:text-indigo-600 transition-colors p-1"
                                        title="{{ __('admin.users.view_profile') }}">
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <button wire:click="deleteUser({{ $user->id }})"
                                            wire:confirm="{{ __('admin.users.delete_confirm') }}"
                                            class="hover:text-red-600 transition-colors p-1"
                                            title="{{ __('admin.users.delete_user') }}">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-users text-4xl mb-3 opacity-20"></i>
                                    <p>{{ __('admin.users.empty') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>