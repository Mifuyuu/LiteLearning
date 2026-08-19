@section('page-title', 'ตั้งค่า')

<div class="" x-data="{
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
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden">

        {{-- Page Header --}}
        <div class="p-6 lg:p-8">
            <h1 class="text-3xl font-black tracking-tight text-[#101114] sm:text-4xl">{{ 'ตั้งค่า' }}</h1>
            <p class="mt-2 text-md leading-6 text-[#686b82]">{{ 'จัดการการตั้งค่าบัญชีของคุณ' }}</p>
        </div>

        <div class="border-t border-[#dedee5] p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 md:divide-x md:divide-gray-200">

                {{-- Left: Avatar + Cover Image + Notifications --}}
                <div class="space-y-6 md:pr-6">

                    {{-- Avatar --}}
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 mb-4">{{ 'รูปโปรไฟล์' }}</h3>
                        <div class="flex items-center gap-5">
                            <div class="relative shrink-0">
                                <img src="{{ auth()->user()->avatar_url }}" alt="avatar"
                                    class="h-20 w-20 rounded-full object-cover border-2 border-gray-100">
                                <div wire:loading.flex wire:target="avatar"
                                    class="absolute inset-0 flex items-center justify-center rounded-full bg-slate-950/40">
                                    <x-icon name="spinner" class="h-5 w-5 text-white animate-spin" />
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="cursor-pointer">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:border-gray-300">
                                        <x-icon name="arrow-up-tray" class="h-4 w-4" />
                                        {{ 'อัปโหลด' }}
                                    </span>
                                    <input type="file" @change="initCropper($event, 'avatar')" class="hidden" accept="image/*">
                                </label>
                                @if(auth()->user()->avatar)
                                    <button wire:click="confirmReset('avatar')"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-sm font-medium text-red-600 transition hover:bg-red-50">
                                        <x-icon name="arrow-uturn-left" class="h-4 w-4" />
                                        {{ 'รีเซ็ต' }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Cover Image --}}
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 mb-4">{{ 'รูปปกโปรไฟล์' }}</h3>
                        <div>
                            <div class="relative mb-3 h-32 w-full overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                                <img src="{{ auth()->user()->cover_image ? auth()->user()->cover_image_url : asset('images/default_profile_banner.webp').'?v='.filemtime(public_path('images/default_profile_banner.webp')) }}" alt="cover"
                                    class="h-full w-full object-cover">
                                <div wire:loading.flex wire:target="cover_image"
                                    class="absolute inset-0 flex items-center justify-center bg-slate-950/40">
                                    <x-icon name="spinner" class="h-5 w-5 text-white animate-spin" />
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <label class="cursor-pointer">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:border-gray-300">
                                        <x-icon name="arrow-up-tray" class="h-4 w-4" />
                                        {{ 'อัปโหลด' }}
                                    </span>
                                    <input type="file" @change="initCropper($event, 'cover')" class="hidden" accept="image/*">
                                </label>
                                @if(auth()->user()->cover_image)
                                    <button wire:click="confirmReset('cover')"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-sm font-medium text-red-600 transition hover:bg-red-50">
                                        <x-icon name="arrow-uturn-left" class="h-4 w-4" />
                                        {{ 'รีเซ็ต' }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Dummy: Notifications --}}
                    <div class="pt-8">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">{{ 'การแจ้งเตือน' }}</h3>
                        <div class="space-y-3">
                            <label class="flex items-center justify-between">
                                <span class="text-sm text-gray-700">{{ 'แจ้งเตือนทางอีเมล' }}</span>
                                <input type="checkbox" checked class="toggle toggle-sm toggle-primary">
                            </label>
                            <label class="flex items-center justify-between">
                                <span class="text-sm text-gray-700">{{ 'แจ้งเตือนเมื่อมีงานใหม่' }}</span>
                                <input type="checkbox" checked class="toggle toggle-sm toggle-primary">
                            </label>
                            <label class="flex items-center justify-between">
                                <span class="text-sm text-gray-700">{{ 'แจ้งเตือนเมื่อได้คะแนน' }}</span>
                                <input type="checkbox" class="toggle toggle-sm toggle-primary">
                            </label>
                            <label class="flex items-center justify-between">
                                <span class="text-sm text-gray-700">{{ 'แสดงป๊อปอัพเมื่อทำภารกิจสำเร็จ' }}</span>
                                <input type="checkbox" checked class="toggle toggle-sm toggle-primary">
                            </label>
                        </div>
                    </div>

                </div>

                {{-- Right: Account Settings + Language + Privacy --}}
                <div class="space-y-6 md:pl-6">
                    <div class="md:pt-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">{{ 'ตั้งค่าบัญชี' }}</h3>
                    <div class="space-y-5">

                        {{-- Name --}}
                        <div x-data="{ nameLen: {{ strlen(auth()->user()->name) }} }">
                            <label
                                class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">{{ 'ชื่อที่แสดง' }}</label>
                            <form wire:submit.prevent="updateName" class="flex flex-wrap items-center gap-2">
                                <input wire:model="name" @input="nameLen = $event.target.value.length" type="text"
                                    maxlength="50"
                                    class="min-w-0 flex-1 rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 placeholder-gray-300
                                           focus:outline-none focus:ring-2 focus:ring-blue-400/40 focus:border-blue-400 transition-all"
                                    placeholder="{{ 'ชื่อที่แสดงของคุณ' }}">
                                <span class="text-xs transition-colors shrink-0"
                                    :class="nameLen >= 50 ? 'text-red-500 font-medium' : 'text-gray-400'">
                                    <span x-text="nameLen"></span>/50
                                </span>
                                <button type="submit"
                                    class="btn-3d btn-3d--blue px-4 py-2.5 text-sm font-medium rounded-xl whitespace-nowrap">
                                    {{ 'บันทึก' }}
                                </button>
                            </form>
                            @error('name')
                                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                    <x-icon name="exclamation-circle" class="h-4 w-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Email (read-only) --}}
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">{{ 'อีเมล' }}</label>
                            <div
                                class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-500">
                                <x-icon name="envelope" class="h-4 w-4 text-gray-400" />
                                <span>{{ auth()->user()->email }}</span>
                        </div>

                    </div>

                    </div>
                </div>

                    {{-- Dummy: Language & Region --}}
                    <div class="pt-8">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">{{ 'ภาษาและภูมิภาค' }}</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">{{ 'ภาษา' }}</label>
                                <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-500">
                                    <x-icon name="globe" class="h-4 w-4 text-gray-400" />
                                    <span>ไทย (Thai)</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">{{ 'โซนเวลา' }}</label>
                                <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-500">
                                    <x-icon name="clock" class="h-4 w-4 text-gray-400" />
                                    <span>Asia/Bangkok (UTC+7)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Dummy: Privacy --}}
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 mb-4">{{ 'ความเป็นส่วนตัว' }}</h3>
                        <div class="space-y-3">
                            <label class="flex items-center justify-between">
                                <span class="text-sm text-gray-700">{{ 'แสดงโปรไฟล์ต่อสาธารณะ' }}</span>
                                <input type="checkbox" checked class="toggle toggle-sm toggle-primary">
                            </label>
                            <label class="flex items-center justify-between">
                                <span class="text-sm text-gray-700">{{ 'แสดงความคืบหน้าบนกระดานผู้นำ' }}</span>
                                <input type="checkbox" checked class="toggle toggle-sm toggle-primary">
                            </label>
                        </div>
                        <p class="mt-3 text-xs text-gray-400"><x-icon name="code-branch" class="h-4 w-4 mr-1 inline" /> {{ 'กำลังพัฒนา' }}</p>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <template x-teleport="body">
        <div x-show="showCropper" class="fixed inset-0 z-70 flex items-center justify-center bg-slate-950/80 p-4" x-cloak
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            @keydown.escape.window="closeCropper()">
            <div class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-[12px] bg-white">
                <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="text-lg font-black text-slate-950">
                        <span x-show="cropType === 'avatar'">ครอบตัดรูปโปรไฟล์</span>
                        <span x-show="cropType === 'cover'">ครอบตัดรูปปก</span>
                    </h3>
                    <button @click="closeCropper()" class="text-slate-400 transition hover:text-slate-600">
                        <x-icon name="x-mark" class="h-5 w-5" />
                    </button>
                </div>
                <div class="w-full shrink-0 bg-white p-4" style="height: 60vh;">
                    <div class="relative h-full w-full">
                        <img x-ref="cropperImage" :src="imageUrl" class="block max-h-full max-w-full">
                    </div>
                </div>
                <div class="flex shrink-0 items-center justify-end gap-3 border-t border-slate-100 px-6 py-4">
                    <button @click="closeCropper()"
                        class="rounded-[12px] px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        ยกเลิก
                    </button>
                    <button @click="saveCrop()"
                        class="btn-3d btn-3d--blue rounded-[12px] px-6 py-2 text-sm font-bold">
                        บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </div>
        </div>
    </template>

    <x-confirm-modal show="$wire.pendingReset" cancel="$wire.set('pendingReset', null)"
        :heading="$pendingReset === 'cover' ? 'รีเซ็ตรูปปก' : 'รีเซ็ตรูปโปรไฟล์'"
        :message="$pendingReset === 'cover' ? 'คุณแน่ใจหรือว่าต้องการรีเซ็ตรูปปกกลับเป็นค่าเริ่มต้น?' : 'คุณแน่ใจหรือว่าต้องการรีเซ็ตรูปโปรไฟล์กลับเป็นค่าเริ่มต้น?'">
        <button type="button" wire:click="doReset"
            class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
            {{ 'รีเซ็ต' }}
        </button>
    </x-confirm-modal>
</div>
