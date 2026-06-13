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
    @section('page-title', __('Profile'))

    @php
        $completion = $profileStats['achievement_total'] > 0
            ? (int) round(($profileStats['achievements'] / $profileStats['achievement_total']) * 100)
            : 0;
    @endphp

    <div class="space-y-5">
        <section class="overflow-hidden rounded-[12px] border border-slate-200 bg-white">
            <div class="relative h-48 overflow-hidden bg-[radial-gradient(circle_at_20%_20%,rgba(255,255,255,0.35),transparent_24%),linear-gradient(135deg,#3b1fa8,#7132f5_48%,#855bfb)]">
                @if($user->cover_image)
                    <img src="{{ $user->cover_image_url }}" alt="{{ $user->name }}"
                        class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-slate-950/35"></div>
                @endif

                <label
                    class="absolute right-4 top-4 inline-flex cursor-pointer items-center gap-2 rounded-[12px] bg-white/90 px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-white">
                    <i class="fas fa-camera text-sky-600"></i>
                    {{ __('Cover') }}
                    <input type="file" @change="initCropper($event, 'cover')" class="hidden" accept="image/*">
                </label>

                <div wire:loading wire:target="cover_image"
                    class="absolute inset-0 flex items-center justify-center bg-slate-950/40">
                    <i class="fas fa-circle-notch fa-spin text-3xl text-white"></i>
                </div>
            </div>

            <div class="px-5 pb-6 lg:px-7">
                <div class="flex min-w-0 flex-col gap-20 sm:flex-row sm:items-start">
                    <div class="-mt-10 sm:-mt-12 ml-14 sm:ml-16 relative inline-block shrink-0 group">
                        <x-user-avatar :user="$user" size="w-36 h-36" border="border-4 border-white" shadow="shadow-none">
                            <label
                                class="absolute inset-0 flex cursor-pointer items-center justify-center rounded-full bg-slate-950/45 opacity-0 transition group-hover:opacity-100">
                                <i class="fas fa-camera text-2xl text-white"></i>
                                <input type="file" @change="initCropper($event, 'avatar')" class="hidden" accept="image/*">
                            </label>
                        </x-user-avatar>

                        <div wire:loading wire:target="avatar"
                            class="absolute inset-0 z-30 flex items-center justify-center rounded-full bg-slate-950/50">
                            <i class="fas fa-circle-notch fa-spin text-2xl text-white"></i>
                        </div>
                    </div>

                    <div class="min-w-0 pb-1 sm:pt-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="truncate text-3xl font-black text-slate-950 sm:text-4xl {{ $user->active_name_color ?? '' }}">
                                {{ $user->name }}
                            </h1>
                            <span class="inline-flex items-center gap-1 rounded-full bg-[rgba(133,91,251,0.16)] px-2.5 py-1 text-xs font-black uppercase tracking-wide text-[#7132f5]">
                                <x-icon name="user-solid" class="h-3.5 w-3.5 shrink-0" />
                                {{ __(ucfirst($user->role)) }}
                            </span>
                        </div>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                            {{ $user->bio ?: __('Learning profile, classroom progress, and badge collection.') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-5 xl:grid-cols-[320px_minmax(0,1fr)]">
            <aside class="space-y-5">


                <section class="rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                    <h2 class="text-lg font-black text-[#101114]">{{ __('Profile stats') }}</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-[#686b82]">{{ __('Badge collection') }}</span>
                                <span class="font-black text-[#7132f5]">{{ $completion }}%</span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-[rgba(133,91,251,0.08)]">
                                <div class="h-full rounded-full bg-[#7132f5]" style="width: {{ $completion }}%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-[12px] border border-[#dedee5] bg-white p-4">
                                <p class="text-2xl font-black text-[#101114]">{{ number_format($profileStats['classrooms']) }}</p>
                                <p class="text-xs font-semibold text-[#9497a9]">{{ __('Classrooms') }}</p>
                            </div>
                            <div class="rounded-[12px] border border-[#dedee5] bg-white p-4">
                                <p class="text-2xl font-black text-[#101114]">{{ number_format($profileStats['submissions']) }}</p>
                                <p class="text-xs font-semibold text-[#9497a9]">{{ __('Submissions') }}</p>
                            </div>
                            <div class="rounded-[12px] border border-[#dedee5] bg-white p-4">
                                <p class="text-2xl font-black text-[#101114]">{{ number_format($profileStats['average_score'], 1) }}</p>
                                <p class="text-xs font-semibold text-[#9497a9]">{{ __('Avg score') }}</p>
                            </div>
                            <div class="rounded-[12px] border border-[#dedee5] bg-white p-4">
                                <p class="text-2xl font-black text-[#101114]">{{ $profileStats['achievements'] }}/{{ $profileStats['achievement_total'] }}</p>
                                <p class="text-xs font-semibold text-[#9497a9]">{{ __('Unlocked') }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
                    <h2 class="text-lg font-black text-[#101114]">{{ __('Recent work') }}</h2>
                    <div class="mt-4 space-y-3">
                        @forelse($recentSubmissions as $submission)
                            @php
                                $assignment = $submission->assignment;
                                $classroom = $assignment?->classroom;
                            @endphp
                            <a href="{{ $assignment && $classroom ? route('assignment.show', [$classroom, $assignment]) : '#' }}" wire:navigate
                                class="block rounded-[12px] border border-[#dedee5] p-3 transition hover:border-[rgba(113,50,245,0.3)] hover:bg-[rgba(133,91,251,0.04)]">
                                <div class="flex items-start gap-3">
                                    <span class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-[12px] text-white"
                                        style="background-color: {{ $classroom?->themeCategory?->color ?? '#7132f5' }};">

                                        <i class="fas fa-file-lines text-sm"></i>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-bold text-[#101114]">{{ $assignment?->title ?? __('Assignment') }}</span>
                                        <span class="mt-1 block truncate text-xs text-[#9497a9]">{{ $submission->turned_in_at?->diffForHumans() }}</span>
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-[12px] border border-dashed border-[#dedee5] bg-[rgba(104,107,130,0.04)] px-4 py-8 text-center text-sm text-[#9497a9]">
                                {{ __('No submitted work yet.') }}
                            </div>
                        @endforelse
                    </div>
                </section>
            </aside>

            <div class="space-y-5">
                @if($user->isStudent() && !empty($chartPoints))
                    @php
                        $pathD = '';
                        $areaD = '';
                        foreach ($chartPoints as $i => $p) {
                            if ($i === 0) {
                                $pathD .= "M {$p['x']} {$p['y']}";
                            } else {
                                $prev = $chartPoints[$i - 1];
                                $offset = ($p['x'] - $prev['x']) / 3;
                                $cp1x = $prev['x'] + $offset;
                                $cp1y = $prev['y'];
                                $cp2x = $p['x'] - $offset;
                                $cp2y = $p['y'];
                                $pathD .= " C {$cp1x} {$cp1y}, {$cp2x} {$cp2y}, {$p['x']} {$p['y']}";
                            }
                        }
                        $areaD = $pathD . " L 400 80 L 0 80 Z";
                    @endphp
                    <section class="overflow-hidden rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px]"
                        x-data="{
                            activePoint: {{ count($chartPoints) - 1 }},
                            points: {{ json_encode($chartPoints) }}
                        }">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-[#dedee5] pb-4">
                            <div>
                                <h2 class="text-lg font-black text-slate-950">{{ __('อันดับและแนวโน้มการเรียน') }}</h2>
                                <p class="text-xs text-slate-500">{{ __('แสดงประวัติอันดับความคืบหน้าในช่วง 90 วันที่ผ่านมา') }}</p>
                            </div>
                            <div class="flex items-center gap-6">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('อันดับ') }}</p>
                                    <p class="text-3xl font-black text-slate-900" x-text="'#' + Number(points[activePoint].rank).toLocaleString()"></p>
                                </div>
                            </div>
                        </div>

                        <div class="relative mt-5 h-24">
                            <!-- SVG Chart (only paths) -->
                            <svg viewBox="0 0 400 80" class="w-full h-full overflow-visible" preserveAspectRatio="none">
                                <defs>
                                    <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#7132f5" stop-opacity="0.15" />
                                        <stop offset="100%" stop-color="#7132f5" stop-opacity="0" />
                                    </linearGradient>
                                </defs>
                                
                                <!-- Area Gradient Fill -->
                                <path d="{{ $areaD }}" fill="url(#chartGradient)" />

                                <!-- Bezier Line -->
                                <path d="{{ $pathD }}" fill="none" stroke="#7132f5" stroke-width="2.5" stroke-linecap="round" />
                            </svg>

                            <!-- HTML Guide Line (Always exactly 1px, never stretched) -->
                            <div class="absolute top-0 bottom-0 w-px border-l border-dashed pointer-events-none transition-all duration-75"
                                :style="`left: ${points[activePoint].x / 400 * 100}%; border-color: #7132f5;`"
                                style="opacity: 0.5;">
                            </div>

                            <!-- HTML Active Point Dot (Always a perfect circle!) -->
                            <div class="absolute pointer-events-none flex items-center justify-center -translate-x-1/2 -translate-y-1/2 transition-all duration-75"
                                :style="`left: ${points[activePoint].x / 400 * 100}%; top: ${points[activePoint].y / 80 * 100}%;`">
                                <!-- Outer Glow -->
                                <span class="absolute w-5 h-5 rounded-full animate-pulse" style="background-color: rgba(113, 50, 245, 0.3);"></span>
                                <!-- Inner Dot -->
                                <span class="relative w-3 h-3 rounded-full bg-white shadow-md" style="border: 2.5px solid #7132f5;"></span>
                            </div>

                            <!-- Invisible Hover Zones for interaction -->
                            <div class="absolute inset-0 flex">
                                @foreach($chartPoints as $index => $pt)
                                    <div class="h-full flex-1 cursor-pointer"
                                        @mouseenter="activePoint = {{ $index }}"
                                        @touchstart="activePoint = {{ $index }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between bg-slate-50 rounded-[12px] px-4 py-2 border border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: #7132f5;"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2" style="background-color: #7132f5;"></span>
                                </span>
                                <span class="font-bold text-slate-600 text-xs" x-text="points[activePoint].day"></span>
                            </div>
                            <span class="font-black text-xs" style="color: #7132f5;" x-text="'อันดับ #' + Number(points[activePoint].rank).toLocaleString()"></span>
                        </div>
                    </section>
                @endif

                <section class="rounded-[12px] border border-slate-200 bg-white p-5">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">{{ __('Badge collection') }}</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ __('Unlocked badges are highlighted. Locked badges stay visible for progress tracking.') }}
                            </p>
                        </div>
                        <span class="rounded-full bg-[rgba(133,91,251,0.16)] px-3 py-1 text-sm font-black text-[#7132f5]">
                            {{ $profileStats['achievements'] }} / {{ $profileStats['achievement_total'] }}
                        </span>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5">
                        @forelse($achievements as $achievement)
                            @php
                                $unlocked = isset($unlockedAchievementIds[$achievement->id]);
                            @endphp
                            <article
                                class="group rounded-[12px] border p-4 text-center transition {{ $unlocked ? 'border-purple-200 bg-purple-50/70 hover:-translate-y-0.5' : 'border-slate-200 bg-slate-50 opacity-70 grayscale' }}">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full {{ $unlocked ? 'bg-white' : 'bg-slate-100' }}">
                                    @if($achievement->badge_image)
                                        <img src="{{ asset($achievement->badge_image) }}" alt="{{ $achievement->name }}"
                                            class="h-12 w-12 object-contain">
                                    @else
                                        <i class="fas fa-medal text-2xl {{ $unlocked ? 'text-[#7132f5]' : 'text-slate-400' }}"></i>
                                    @endif
                                </div>
                                <h3 class="mt-3 line-clamp-2 text-sm font-black text-slate-900">{{ $achievement->name }}</h3>
                                <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">{{ $achievement->description }}</p>

                            </article>
                        @empty
                            <div class="col-span-full rounded-[12px] border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center text-sm text-slate-500">
                                {{ __('No badges configured yet.') }}
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-[12px] border border-slate-200 bg-white p-5">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-xl font-black text-slate-950">{{ __('Classrooms') }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ __('Rooms connected to this profile.') }}</p>
                        </div>
                        <a href="{{ route('classrooms') }}" wire:navigate class="text-sm font-bold text-sky-700 hover:text-sky-900">
                            {{ __('View all') }}
                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                        </a>
                    </div>

                    <div class="mt-5 grid gap-3 md:grid-cols-2">
                        @forelse($profileClassrooms as $entry)
                            @php
                                $classroom = $entry['model'];
                            @endphp
                            <a href="{{ route('classroom.show', $classroom) }}" wire:navigate
                                class="group overflow-hidden rounded-[12px] border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:border-sky-200">
                                <div class="h-20"
                                    style="background-color: {{ $classroom->themeCategory?->color ?? '#7132f5' }};"></div>
                                <div class="p-4">
                                    <div class="-mt-10 mb-3 flex h-12 w-12 items-center justify-center rounded-[12px] border-4 border-white bg-white text-sky-700">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <h3 class="truncate font-black text-slate-950 group-hover:text-sky-700">{{ $classroom->name }}</h3>
                                            <p class="mt-1 truncate text-sm text-slate-500">{{ $classroom->section ?: __('No section') }}</p>
                                        </div>
                                        <span class="shrink-0 rounded-full bg-sky-50 px-2.5 py-1 text-xs font-black text-sky-700">
                                            {{ $entry['role'] }}
                                        </span>
                                    </div>
                                    <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold text-slate-500">
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1">
                                            <i class="fas fa-users mr-1 text-slate-400"></i>{{ $entry['students_count'] }}
                                        </span>
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1">
                                            <i class="fas fa-file-lines mr-1 text-slate-400"></i>{{ $entry['assignments_count'] }}
                                        </span>
                                        <span class="min-w-0 truncate rounded-full bg-slate-100 px-2.5 py-1">
                                            <i class="fas fa-user mr-1 text-slate-400"></i>{{ $classroom->teacher?->name }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-[12px] border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center text-sm text-slate-500 md:col-span-2">
                                {{ __('No classrooms yet.') }}
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </section>
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
                        <span x-show="cropType === 'avatar'">{{ __('Crop Profile Picture') }}</span>
                        <span x-show="cropType === 'cover'">{{ __('Crop Cover Image') }}</span>
                    </h3>
                    <button @click="closeCropper()" class="text-slate-400 transition hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
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
                        {{ __('Cancel') }}
                    </button>
                    <button @click="saveCrop()"
                        class="btn-3d btn-3d--indigo rounded-[12px] px-6 py-2 text-sm font-bold">
                        {{ __('Save changes') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
