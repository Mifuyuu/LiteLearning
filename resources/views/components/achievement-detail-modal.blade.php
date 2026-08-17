{{-- Achievement detail modal — opened from anywhere via $dispatch('achievement-show', {...}).
     The achievementsModal() Alpine function lives in layouts/app.blade.php: this component is
     rendered inside a #[Lazy] Livewire page whose inline <script> tags are NOT executed on hydrate. --}}
<div x-data="achievementsModal()" @achievement-show.window="open($event.detail)">
    <div x-show="visible" x-cloak
        class="fixed inset-0 z-100 flex items-center justify-center overflow-hidden bg-black/50 p-4"
        @click.self="close()">
        <div x-show="visible"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-sm rounded-[12px] border border-[#dedee5] bg-white p-6 text-center shadow-[rgba(0,0,0,0.08)_0px_8px_32px]">
            <div class="relative mx-auto h-24 w-24">
                <img :src="selected ? '/' + selected.badge_image : ''" :alt="selected?.name"
                    class="h-24 w-24 object-contain" />
                <div class="achievement-badge-shine absolute inset-0"
                    :style="selected ? '--badge-mask: url(' + '/' + selected.badge_image + ')' : ''"></div>
            </div>
            <h4 class="mt-4 text-lg font-black text-[#101114]" x-text="selected?.name"></h4>
            <p class="mt-2 text-sm leading-6 text-[#686b82]" x-text="selected?.description"></p>
            <p class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-sm font-bold text-[#686b82]">
                <x-icon name="calendar-days" class="h-4 w-4" />
                <span x-text="selected ? 'ได้รับเมื่อ ' + selected.unlocked_at : ''"></span>
            </p>
            <button type="button" @click="close()"
                class="mt-5 w-full rounded-[10px] bg-[var(--ll-blue)] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[var(--ll-blue-dark)]">
                {{ 'ปิด' }}
            </button>
        </div>
    </div>
</div>