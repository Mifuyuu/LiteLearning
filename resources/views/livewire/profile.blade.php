<div x-data="{
    showCropper: false,
    cropType: 'avatar',
    imageUrl: null,
    cropper: null,
    
    initCropper(event, type) {
        const file = event.target.files[0];
        if (!file) return;
        
        this.cropType = type;
        this.imageUrl = URL.createObjectURL(file);
        this.showCropper = true;
        
        this.$nextTick(() => {
            if (this.cropper) this.cropper.destroy();
            
            const image = this.$refs.cropperImage;
            this.cropper = new Cropper(image, {
                aspectRatio: type === 'avatar' ? 1 : 16 / 5,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        });
        
        // Reset input so same file can be selected again
        event.target.value = '';
    },
    
    saveCrop() {
        if (!this.cropper) return;
        
        const canvas = this.cropper.getCroppedCanvas({
            width: this.cropType === 'avatar' ? 400 : 1200,
            height: this.cropType === 'avatar' ? 400 : 375,
            imageSmoothingHigh: true,
        });
        
        const base64 = canvas.toDataURL('image/jpeg', 0.9);
        
        if (this.cropType === 'avatar') {
            @this.set('avatar', base64);
        } else {
            @this.set('cover_image', base64);
        }
        
        this.closeCropper();
    },
    
    closeCropper() {
        this.showCropper = false;
        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }
        if (this.imageUrl) {
            URL.revokeObjectURL(this.imageUrl);
            this.imageUrl = null;
        }
    }
}">
    @section('page-title', __('Profile'))

    <div class="max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Profile') }}</h2>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden relative">

            <!-- Cover Image Area -->
            <div class="h-48 bg-linear-to-r from-indigo-600 to-purple-600 relative group overflow-hidden">
                @if($user->cover_image)
                    <img src="{{ $user->cover_image_url }}" class="w-full h-full object-cover">
                @endif

                <!-- Upload Cover Button -->
                <label
                    class="absolute bottom-4 right-4 bg-white/80 hover:bg-white text-gray-800 text-sm font-medium px-3 py-1.5 rounded-lg shadow-sm cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center gap-2">
                    <i class="fas fa-camera"></i>
                    {{ __('Change Cover') }}
                    <input type="file" @change="initCropper($event, 'cover')" class="hidden" accept="image/*">
                </label>

                <!-- Loading State for Cover -->
                <div wire:loading wire:target="cover_image"
                    class="absolute inset-0 bg-black/30 flex items-center justify-center">
                    <i class="fas fa-circle-notch fa-spin text-white text-2xl"></i>
                </div>
            </div>

            <div class="px-6 pb-6">
                <div class="-mt-16 mb-4 relative inline-block group">
                    <x-user-avatar :user="$user" size="w-32 h-32" border="border-4 border-white shadow-lg">
                        <!-- Upload Avatar Overlay -->
                        <label
                            class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 cursor-pointer rounded-full">
                            <i class="fas fa-camera text-white text-xl"></i>
                            <input type="file" @change="initCropper($event, 'avatar')" class="hidden" accept="image/*">
                        </label>

                        <!-- Loading State for Avatar -->
                        <div wire:loading wire:target="avatar"
                            class="absolute inset-0 bg-black/50 flex items-center justify-center z-10 rounded-full">
                            <i class="fas fa-circle-notch fa-spin text-white text-2xl"></i>
                        </div>
                    </x-user-avatar>
                </div>

                <div class="mt-2">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h3>
                    <p class="text-gray-500">{{ $user->email }}</p>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mt-2 capitalize">
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

    <!-- Cropping Modal -->
    <div x-show="showCropper" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @keydown.escape.window="closeCropper()">

        <div class="bg-white rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
                <h3 class="text-lg font-bold text-gray-900">
                    <span x-show="cropType === 'avatar'">{{ __('Crop Profile Picture') }}</span>
                    <span x-show="cropType === 'cover'">{{ __('Crop Cover Image') }}</span>
                </h3>
                <button @click="closeCropper()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="flex-1 bg-gray-900 overflow-hidden flex items-center justify-center p-4 min-h-0">
                <img x-ref="cropperImage" :src="imageUrl" class="max-w-full max-h-full block">
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3 shrink-0">
                <button @click="closeCropper()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    {{ __('Cancel') }}
                </button>
                <button @click="saveCrop()"
                    class="btn-3d btn-3d--indigo px-6 py-2 text-sm font-bold rounded-lg transition-all">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </div>
    </div>
</div>