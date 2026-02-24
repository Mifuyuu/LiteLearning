<div>
    @section('page-title', __('Profile'))

    <div class="max-w-3xl mx-auto">
        @if (session()->has('message'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Profile') }}</h2>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden relative">
            
            <!-- Cover Image Area -->
            <div class="h-48 bg-linear-to-r from-indigo-600 to-purple-600 relative group">
                @if($user->cover_image_url)
                    <img src="{{ Storage::disk('public')->url($user->cover_image) }}" class="w-full h-full object-cover">
                @endif
                
                <!-- Upload Cover Button -->
                <label for="cover-upload" class="absolute bottom-4 right-4 bg-white/80 hover:bg-white text-gray-800 text-sm font-medium px-3 py-1.5 rounded-lg shadow-sm cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center gap-2">
                    <i class="fas fa-camera"></i>
                    {{ __('Change Cover') }}
                    <input type="file" id="cover-upload" wire:model="cover_image" class="hidden" accept="image/*">
                </label>
                
                <!-- Loading State for Cover -->
                <div wire:loading wire:target="cover_image" class="absolute inset-0 bg-black/30 flex items-center justify-center">
                    <i class="fas fa-circle-notch fa-spin text-white text-2xl"></i>
                </div>
            </div>

            <div class="px-6 pb-6">
                <div class="-mt-16 mb-4 relative inline-block group">
                    <x-user-avatar :user="$user" size="w-32 h-32" border="border-4 border-white shadow-lg">
                        <!-- Upload Avatar Overlay -->
                        <label for="avatar-upload" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 cursor-pointer">
                            <i class="fas fa-camera text-white text-xl"></i>
                            <input type="file" id="avatar-upload" wire:model="avatar" class="hidden" accept="image/*">
                        </label>
                        
                        <!-- Loading State for Avatar -->
                        <div wire:loading wire:target="avatar" class="absolute inset-0 bg-black/50 flex items-center justify-center z-10">
                            <i class="fas fa-circle-notch fa-spin text-white text-2xl"></i>
                        </div>
                    </x-user-avatar>
                </div>

                <div class="mt-2">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h3>
                    <p class="text-gray-500">{{ $user->email }}</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mt-2 capitalize">
                        {{ __(ucfirst($user->role)) }}
                    </span>
                </div>

                @if($user->bio)
                    <p class="mt-4 text-gray-600">{{ $user->bio }}</p>
                @endif

                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($user->isTeacher() || $user->isAdmin())
                        <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-100">
                            <p class="text-3xl font-bold text-indigo-600">{{ $user->ownedClassrooms()->count() }}</p>
                            <p class="text-sm font-medium text-gray-500 mt-1">{{ __('Classes Teaching') }}</p>
                        </div>
                    @endif
                    <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-100">
                        <p class="text-3xl font-bold text-indigo-600">{{ $user->enrolledClassrooms()->count() }}</p>
                        <p class="text-sm font-medium text-gray-500 mt-1">{{ __('Classes Enrolled') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
