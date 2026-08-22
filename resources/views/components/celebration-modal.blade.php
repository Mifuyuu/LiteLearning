{{-- Unified celebration modal — achievement unlocks and level-ups share ONE queue so
     they always render in the order they were flashed (an achievement's XP can trigger
     a level-up in the same request; the achievement card must show before it).
     Delivery: Livewire dehydrate hook ('achievement-unlocked' / 'level-up' window events)
     or the matching session flash on a fresh page load. --}}
<script>
    function celebrationModal() {
        return {
            queue: [],
            current: null,
            visible: false,
            fillPercent: 0,
            displayLevel: null,
            popLevel: false,
            pushAchievements(items) {
                if (!Array.isArray(items) || items.length === 0) return;
                this.enqueue(items.map(item => ({ type: 'achievement', ...item })));
            },
            pushLevelUps(items) {
                if (!Array.isArray(items) || items.length === 0) return;
                this.enqueue(items.map(item => ({ type: 'level-up', ...item })));
            },
            enqueue(items) {
                this.queue.push(...items);
                if (!this.visible) this.showNext();
            },
            showNext() {
                const next = this.queue.shift() ?? null;
                this.current = next;
                this.visible = next !== null;
                if (!next) return;

                if (next.type === 'level-up') {
                    this.displayLevel = next.level_before;
                    this.fillPercent = 0;
                    this.popLevel = false;
                    // let the 0% width paint first, then transition to full
                    requestAnimationFrame(() => requestAnimationFrame(() => {
                        this.fillPercent = 100;
                    }));
                    setTimeout(() => {
                        this.displayLevel = next.level_after;
                        this.popLevel = true;
                    }, 1200);
                }
            },
            close() {
                this.visible = false;
                // ponytail: 200ms matches the card leave transition before the next one enters
                setTimeout(() => this.showNext(), 200);
            },
        }
    }
</script>

<div x-data="celebrationModal()"
    @achievement-unlocked.window="pushAchievements($event.detail)"
    @level-up.window="pushLevelUps($event.detail)"
    x-init="
        pushAchievements({{ json_encode(session()->pull('new_achievements', []), JSON_UNESCAPED_UNICODE) }});
        pushLevelUps({{ json_encode(session()->pull('new_level_ups', []), JSON_UNESCAPED_UNICODE) }});
    ">

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
            class="relative w-full max-w-sm rounded-xl border border-[#dedee5] bg-white p-6 text-center shadow-[rgba(0,0,0,0.08)_0px_8px_32px]">

            {{-- Achievement card --}}
            <template x-if="current?.type === 'achievement'">
                <div>
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
                        x-show="current?.coin_reward > 0 || current?.xp_reward > 0">
                        <span x-show="current?.coin_reward > 0" x-text="current ? '+' + current.coin_reward + ' เหรียญ' : ''"
                            class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600"></span>
                        <span x-show="current?.xp_reward > 0" x-text="current ? '+' + current.xp_reward + ' XP' : ''"
                            class="inline-flex items-center gap-1 rounded-full bg-[rgba(37,99,235,0.12)] px-3 py-1 text-xs font-bold text-(--ll-blue)"></span>
                    </div>
                </div>
            </template>

            {{-- Level-up card --}}
            <template x-if="current?.type === 'level-up'">
                <div>
                    <p class="text-md text-[#9497a9]">เลเวลเพิ่มขึ้น!</p>

                    <div class="mt-3 overflow-hidden">
                        <p class="text-6xl font-black text-(--ll-blue)" :class="{ 'level-number-pop': popLevel }"
                            @animationend="popLevel = false" x-text="displayLevel"></p>
                    </div>

                    <div class="dashboard-liquid-progress mt-5 border border-[rgba(37,99,235,0.3)]" role="progressbar"
                        aria-label="ความคืบหน้าเลเวล">
                        <span class="dashboard-liquid-fill transition-[width] duration-1100 ease-out"
                            :style="'width: ' + fillPercent + '%'"></span>
                    </div>
                </div>
            </template>

            <button type="button" @click="close()"
                class="mt-5 w-full rounded-[10px] bg-(--ll-blue) px-4 py-2.5 text-sm font-bold text-white transition hover:bg-(--ll-blue-dark)">
                <span x-text="current?.type === 'achievement' ? 'เก็บรางวัล' : 'ไปต่อ'"></span>
            </button>
        </div>
    </div>
</div>
