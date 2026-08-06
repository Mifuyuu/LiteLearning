<div>
    <template x-teleport="body">
        <div x-data x-show="$wire.showModal" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
                @click.outside="$wire.set('showModal', false)">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">{{ 'สร้างห้องเรียน' }}</h3>
                    <button wire:click="$set('showModal', false)"
                        class="p-2.5 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-gray-100 transition">
                        <x-icon name="x-mark" class="h-5 w-5" />
                    </button>
                </div>

                <form wire:submit.prevent="create" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ 'ชื่อห้องเรียน *' }}</label>
                        <input wire:model="name" type="text"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="{{ 'เช่น คณิตศาสตร์ 101' }}">
                        @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ 'ห้อง' }}</label>
                            <input wire:model="section" type="text"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="{{ 'เช่น ห้อง 1/A' }}">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ 'รายละเอียด' }}</label>
                        <textarea wire:model="description" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="{{ 'เพิ่มคำอธิบาย...' }}"></textarea>
                    </div>

                    {{-- Planet / Theme Picker --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ 'เลือกธีมห้องเรียน' }}</label>
                        <div x-data="{
                                open: false,
                                top: 0, left: 0, width: 0,
                                toggle(btn) {
                                    if (!this.open) {
                                        const r = btn.getBoundingClientRect();
                                        this.top = r.bottom + 4;
                                        this.left = r.left;
                                        this.width = r.width;
                                    }
                                    this.open = !this.open;
                                }
                            }" class="relative w-full">
                                <button type="button"
                                    @click="toggle($el)"
                                    @click.outside="open = false"
                                    class="w-full flex items-center justify-between gap-3 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm transition hover:border-blue-300">
                                    @if($theme_category_id)
                                        @php $selected = $themes->find($theme_category_id); @endphp
                                        @if($selected)
                                            <span class="flex items-center gap-3">
                                                <img src="/images/planets/planet_{{ str_pad($selected->planet_number, 2, '0', STR_PAD_LEFT) }}.svg"
                                                    alt="{{ $selected->name }}" class="h-8 w-8 object-contain" />
                                                <span class="font-medium text-gray-900">{{ $selected->name }}</span>
                                            </span>
                                        @else
                                            <span class="text-gray-400">{{ 'เลือกธีม...' }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">{{ 'เลือกธีม...' }}</span>
                                    @endif
                                    <span :class="open ? 'rotate-180' : ''" class="inline-flex shrink-0 transition-transform duration-200">
                                        <x-icon name="chevron-down" class="h-4 w-4 text-gray-400" />
                                    </span>
                                </button>
                                <ul x-show="open"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-1"
                                    x-cloak
                                    :style="`position:fixed; top:${top}px; left:${left}px; width:${width}px; z-index:9999;`"
                                    class="max-h-40 overflow-y-auto rounded-lg border border-gray-200 bg-white p-1.5 shadow-lg [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                                @foreach($themes as $theme)
                                    @php $pn = str_pad($theme->planet_number, 2, '0', STR_PAD_LEFT); @endphp
                                    <li>
                                        <button type="button"
                                            wire:click="$set('theme_category_id', {{ $theme->id }})"
                                            @click="open = false"
                                            @class([
                                                'flex w-full items-center justify-between gap-3 rounded-md px-3 py-2 text-sm transition',
                                                'bg-blue-50 text-blue-700 font-medium' => $theme_category_id == $theme->id,
                                                'text-gray-700 hover:bg-gray-50' => $theme_category_id != $theme->id,
                                            ])>
                                            <span>{{ $theme->name }}</span>
                                            <img src="/images/planets/planet_{{ $pn }}.svg"
                                                alt="{{ $theme->name }}" class="h-10 w-10 shrink-0 rounded-lg object-contain" />
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            {{ 'ยกเลิก' }}
                        </button>
                        <button type="submit"
                            class="btn-3d btn-3d--blue px-6 py-2.5 text-sm font-medium rounded-lg transition-colors">
                            <span wire:loading.remove wire:target="create">{{ 'สร้างห้องเรียน' }}</span>
                            <span wire:loading wire:target="create"><x-icon name="spinner" class="h-4 w-4 mr-1 animate-spin" />
                                {{ 'กำลังสร้าง...' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>