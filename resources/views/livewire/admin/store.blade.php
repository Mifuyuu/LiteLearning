@section('page-title', 'จัดการร้านค้า')

<div class="space-y-6 ">
    <div class="bg-white rounded-2xl border-3 border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
            <div class="relative w-full sm:w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-icon name="magnifying-glass" class="h-4 w-4" />
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                    placeholder="ค้นหาสินค้า...">
            </div>

            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500 font-medium">
                    สินค้าทั้งหมด: <span
                        class="text-gray-900 font-bold">{{ \App\Models\StoreItem::count() }}</span>
                </span>
                <button wire:click="openCreate"
                    class="btn-3d btn-3d--blue inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-lg transition-colors">
                    <x-icon name="plus" class="h-4 w-4" />
                    เพิ่มสินค้า
                </button>
            </div>
        </div>
    </div>

    <!-- Store Items Table -->
    <div class="border-t border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 uppercase text-sm font-bold text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">สินค้า</th>
                        <th class="px-6 py-3 text-left">รหัสสินค้า</th>
                        <th class="px-6 py-3 text-left">ประเภท</th>
                        <th class="px-6 py-3 text-left">ราคา</th>
                        <th class="px-6 py-3 text-left">สถานะ</th>
                        <th class="px-6 py-3 text-right">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center shrink-0 overflow-hidden">
                                        @if($item->type === 'avatar_frame')
                                            @if(!str_starts_with($item->value, 'border'))
                                                <img src="{{ asset($item->value) }}" class="w-full h-full object-contain" alt="">
                                            @else
                                                <div class="w-6 h-6 rounded-full {{ $item->value }}"></div>
                                            @endif
                                        @else
                                            <span class="font-bold text-sm {{ $item->value }}">Aa</span>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 truncate max-w-48">{{ $item->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 truncate max-w-xs">{{ $item->description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-mono text-gray-500">{{ $item->code }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wide bg-blue-50 text-blue-700">
                                    {{ $item->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 text-sm font-bold text-gray-900 truncate max-w-48">
                                    {{ $item->price }} <img src="{{ asset('images/Coin.svg') }}" class="h-4 w-4 shrink-0" alt="">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleActive({{ $item->id }})"
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-colors
                                                                                        {{ $item->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    {{ $item->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-3 text-gray-400">
                                    <button wire:click="openEdit({{ $item->id }})"
                                        class="hover:text-blue-600 transition-colors p-1" title="แก้ไข">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </button>
                                    <button type="button"
                                        @click="$dispatch('open-delete-item', { id: {{ $item->id }}, name: '{{ addslashes($item->name) }}' })"
                                        class="hover:text-red-600 transition-colors p-1" title="ลบ">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
                                ไม่พบสินค้าในร้านค้า
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $items->links() }}
            </div>
        @endif
    </div>
    </div>

<div x-data="{ showDeleteModal: false, deleteId: null, deleteName: '' }"
    @open-delete-item.window="deleteId = $event.detail.id; deleteName = $event.detail.name; showDeleteModal = true"
    @keydown.escape.window="showDeleteModal = false">
    <template x-teleport="body">
        <x-confirm-modal show="showDeleteModal" cancel="showDeleteModal = false" heading="ยืนยันการลบ">
            <x-slot:message>
                คุณแน่ใจหรือไม่ว่าต้องการลบ <span class="font-semibold text-[#101114]" x-text="deleteName"></span>? การกระทำนี้ไม่สามารถย้อนกลับได้
            </x-slot:message>
            <button type="button" @click="$wire.delete(deleteId); showDeleteModal = false"
                class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                ลบ
            </button>
        </x-confirm-modal>
    </template>
</div>

    <!-- Create / Edit Modal -->
    <template x-teleport="body">
        <div x-data x-show="$wire.showModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60">
            <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $editingId ? 'แก้ไขสินค้า' : 'เพิ่มสินค้าใหม่' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x-mark" class="h-5 w-5" />
                    </button>
                </div>

                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1">รหัสสินค้า</label>
                            <input type="text" wire:model="form.code"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500">
                            @error('form.code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div x-data="{ open: false }">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1">ประเภท</label>
                            <div class="relative">
                                <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                                    class="flex w-full items-center justify-between px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span>
                                        @if($form['type'] === 'name_color') Name Color
                                        @elseif($form['type'] === 'avatar_frame') Avatar Frame
                                        @else ประเภท
                                        @endif
                                    </span>
                                    <x-icon name="chevron-down" class="h-3.5 w-3.5 text-gray-400" />
                                </button>
                                <div x-show="open" x-cloak @click.outside="open = false"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute left-0 mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                                    <div role="menu">
                                        <button type="button" role="menuitem"
                                            wire:click="$set('form.type', 'name_color')" @click="open = false"
                                            class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ ($form['type'] ?? '') === 'name_color' ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }}">
                                            Name Color
                                            @if(($form['type'] ?? '') === 'name_color') <x-icon name="check" class="h-4 w-4" />
                                            @endif
                                        </button>
                                        <button type="button" role="menuitem"
                                            wire:click="$set('form.type', 'avatar_frame')" @click="open = false"
                                            class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 transition-colors cursor-pointer {{ ($form['type'] ?? '') === 'avatar_frame' ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }}">
                                            Avatar Frame
                                            @if(($form['type'] ?? '') === 'avatar_frame') <x-icon name="check" class="h-4 w-4" /> @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-1">ชื่อ</label>
                        <input type="text" wire:model="form.name"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500">
                        @error('form.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-1">คำอธิบาย</label>
                        <textarea wire:model="form.description" rows="2"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    @if($form['type'] === 'avatar_frame')
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">รูปกรอบอวตาร</label>
                            <label for="frame-upload"
                                class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                                @if($frameImageUpload)
                                    <img src="{{ $frameImageUpload->temporaryUrl() }}" class="h-16 w-16 object-contain rounded-lg">
                                @elseif(!empty($form['value']) && !str_starts_with($form['value'], 'border'))
                                    <img src="{{ asset($form['value']) }}" class="h-16 w-16 object-contain rounded-lg">
                                @else
                                    <div class="flex flex-col items-center gap-1 text-gray-400">
                                        <x-icon name="arrow-up-tray" class="h-5 w-5" />
                                        <span class="text-xs text-center leading-tight">PNG / SVG / WEBP<br>สูงสุด 2MB</span>
                                    </div>
                                @endif
                                <input id="frame-upload" type="file" accept=".png,.svg,.webp,image/png,image/svg+xml,image/webp"
                                    class="hidden" wire:model="frameImageUpload">
                            </label>
                            <div wire:loading wire:target="frameImageUpload" class="text-xs text-gray-400">กำลังอัปโหลด...</div>
                            @error('frameImageUpload') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            ค่า
                            @if($form['type'] === 'avatar_frame')
                                <span class="font-normal text-gray-400">(พาธรูปที่อัปโหลด หรือคลาส CSS เช่น border-4 border-yellow-400)</span>
                            @endif
                        </label>
                        <input type="text" wire:model="form.value"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 font-mono">
                        @error('form.value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-1">ราคา</label>
                            <input type="number" wire:model="form.price" min="0"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500">
                        </div>
                        <label class="flex items-center gap-2 pb-2 cursor-pointer select-none">
                            <input type="checkbox" wire:model="form.is_active" class="w-4 h-4 text-blue-600 rounded">
                            <span
                                class="text-sm font-semibold text-gray-700">เปิดใช้งาน</span>
                        </label>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                    <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        ยกเลิก
                    </button>
                    <button wire:click="save"
                        class="btn-3d btn-3d--blue px-6 py-2 text-sm font-bold rounded-lg transition-colors">
                        บันทึกสินค้า
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>