{{-- Achievement celebration modal — self-contained Alpine component.
     Shows queued unlocks from the AppServiceProvider dehydrate hook ('achievement-unlocked' event)
     or from the 'new_achievements' session flash on page load. --}}
<script>
    function achievementCelebration() {
        return {
            queue: [],
            current: null,
            visible: false,
            pushUnlocks(unlocks) {
                if (!Array.isArray(unlocks) || unlocks.length === 0) return;
                this.queue.push(...unlocks);
                if (!this.visible) this.showNext();
            },
            showNext() {
                this.current = this.queue.shift() ?? null;
                this.visible = this.current !== null;
            },
            close() {
                this.visible = false;
                // ponytail: 200ms matches the card leave transition before the next one enters
                setTimeout(() => this.showNext(), 200);
            },
        }
    }
</script>

<div x-data="achievementCelebration()" @achievement-unlocked.window="pushUnlocks($event.detail)"
    x-init="pushUnlocks({{ json_encode(session()->pull('new_achievements', []), JSON_UNESCAPED_UNICODE) }})">

    <div x-show="visible" x-cloak
        class="fixed inset-0 z-100 flex items-center justify-center overflow-hidden bg-black/50 p-4"
        @click.self="close()">
        {{-- Sun burst layer --}}
        <div class="achievement-burst" aria-hidden="true"></div>

        {{-- Card layer --}}
        <div x-show="visible"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-sm rounded-[12px] border border-[#dedee5] bg-white p-6 text-center shadow-[rgba(0,0,0,0.08)_0px_8px_32px]">
            <p class="text-md text-[#9497a9]">ปลดล็อคความสำเร็จ</p>

            <div class="relative mx-auto mt-4 h-28 w-28">
                <img :src="current ? '/' + current.badge_image : ''" :alt="current?.name"
                    class="h-28 w-28 object-contain" />
                <div class="achievement-badge-shine absolute inset-0"
                    :style="current ? '--badge-mask: url(' + '/' + current.badge_image + ')' : ''"></div>
            </div>

            <h4 class="mt-4 text-lg font-black text-[#101114]" x-text="current?.name"></h4>
            <p class="mt-2 text-sm leading-6 text-[#686b82]" x-text="current?.description"></p>

            <div class="mt-4 flex flex-wrap justify-center gap-2" x-cloak
                x-show="visible && (current?.coin_reward > 0 || current?.xp_reward > 0)">
                <span x-show="current?.coin_reward > 0" x-text="current ? '+' + current.coin_reward + ' เหรียญ' : ''"
                    class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600"></span>
                <span x-show="current?.xp_reward > 0" x-text="current ? '+' + current.xp_reward + ' XP' : ''"
                    class="inline-flex items-center gap-1 rounded-full bg-[rgba(37,99,235,0.12)] px-3 py-1 text-xs font-bold text-[var(--ll-blue)]"></span>
            </div>

            <button type="button" @click="close()"
                class="mt-5 w-full rounded-[10px] bg-[var(--ll-blue)] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[var(--ll-blue-dark)]">
                เก็บรางวัล
            </button>
        </div>
    </div>
</div>