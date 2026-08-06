@section('page-title', 'หมวดหมู่ธีม')
<div class="space-y-6 " x-data="{
    deleteModal: false,
    deleteId: null,
    deleteName: '',
    openDelete(id, name) { this.deleteId = id; this.deleteName = name; this.deleteModal = true; }
}">


    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">หมวดหมู่ธีม</h2>
                <p class="text-sm text-gray-500 mt-0.5">จัดการธีมห้องเรียน</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <x-icon name="magnifying-glass" class="h-4 w-4 text-gray-400" />
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="ค้นหา..."
                        class="pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 w-48"
                    />
                </div>
                <button wire:click="openCreate" class="btn-3d btn-3d--indigo px-4 py-2 rounded-xl text-sm">
                    <x-icon name="plus" class="h-4 w-4 mr-1.5" />เพิ่มหมวดหมู่
                </button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-bold text-gray-500 uppercase tracking-wider w-20">ดาว</th>
                    <th class="px-4 py-3 text-left text-sm font-bold text-gray-500 uppercase tracking-wider w-32">สี</th>
                    <th class="px-4 py-3 text-left text-sm font-bold text-gray-500 uppercase tracking-wider w-40">ชื่อ</th>
                    <th class="px-4 py-3 text-left text-sm font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell w-28">สถานะ</th>
                    <th class="px-6 py-3 text-right text-sm font-bold text-gray-500 uppercase tracking-wider w-36">การดำเนินการ</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4">
                            @php $pn = str_pad($category->planet_number, 2, '0', STR_PAD_LEFT); @endphp
                            <img src="/images/planets/planet_{{ $pn }}.svg" alt="planet {{ $pn }}"
                                class="w-10 h-10 object-contain" />
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full border border-gray-200 shadow-sm flex-shrink-0"
                                    style="background: {{ $category->color }};"></div>
                                <span class="text-xs font-mono text-gray-500 hidden md:inline">{{ $category->color }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="text-sm font-semibold text-gray-800">{{ $category->name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">ดาวเคราะห์ #{{ $category->planet_number }}</div>
                        </td>
                        <td class="px-4 py-2 hidden sm:table-cell">
                            @if ($category->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <x-icon name="circle" class="h-2 w-2 mr-1.5" />ใช้งาน
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                    <x-icon name="circle" class="h-2 w-2 mr-1.5" />ปิดใช้งาน
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex justify-end gap-3 text-gray-400">
                                <button wire:click="openEdit({{ $category->id }})"
                                    class="hover:text-blue-600 transition-colors p-1">
                                    <x-icon name="pencil" class="h-4 w-4" />
                                </button>
                                <button
                                    @click="openDelete({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                    class="hover:text-red-600 transition-colors p-1">
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic text-sm">
                            {{ $search ? 'ไม่พบหมวดหมู่ที่ค้นหา' : 'ยังไม่มีหมวดหมู่ธีม' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($categories->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    {{-- Create / Edit Modal --}}
    <template x-teleport="body">
        <div
            x-show="$wire.showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 z-[200] flex items-center justify-center p-4"
            x-cloak
        >
            <div
                x-show="$wire.showModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
                @click.stop
                x-data="{
                    localColor: $wire.entangle('form.color').defer,
                    localPlanet: $wire.entangle('form.planet_number').defer,
                    syncColor(val) { this.localColor = val; },
                }"
                x-init="$watch('$wire.showModal', v => { if (v) { localColor = $wire.form.color; localPlanet = $wire.form.planet_number; } })"
            >
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-bold text-gray-800">
                        {{ $editingId ? 'แก้ไขหมวดหมู่ธีม' : 'เพิ่มหมวดหมู่' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <x-icon name="x-mark" class="h-4 w-4" />
                    </button>
                </div>

                <div class="px-6 py-5 space-y-5">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ <span class="text-red-500">*</span></label>
                        <input wire:model="form.name" type="text" placeholder="เช่น วิทยาศาสตร์, คณิตศาสตร์"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                        @error('form.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Color --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">สี <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <input type="color"
                                x-model="localColor"
                                @change="$wire.set('form.color', localColor)"
                                class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5" />
                            <input type="text" maxlength="7" placeholder="#6B3FBF"
                                x-model="localColor"
                                @blur="$wire.set('form.color', localColor)"
                                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono" />
                        </div>
                        @error('form.color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Planet Picker --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ดาวเคราะห์ <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-8 gap-2 max-h-48 overflow-y-auto">
                            @for ($p = 1; $p <= 23; $p++)
                                @php $pn = str_pad($p, 2, '0', STR_PAD_LEFT); @endphp
                                <button type="button"
                                    @click="localPlanet = {{ $p }}; $wire.set('form.planet_number', {{ $p }})"
                                    :class="localPlanet == {{ $p }} ? 'border-blue-500 bg-blue-50 shadow-md' : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50'"
                                    class="aspect-square rounded-xl border-2 p-1 transition-all">
                                    <img src="/images/planets/planet_{{ $pn }}.svg" alt="planet {{ $pn }}" class="w-full h-full object-contain" />
                                </button>
                            @endfor
                        </div>
                        @error('form.planet_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Active toggle --}}
                    <div class="flex items-center gap-3">
                        <input wire:model="form.is_active" type="checkbox" id="is_active"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                        <label for="is_active" class="text-sm font-medium text-gray-700">เปิดใช้งาน</label>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                        ยกเลิก
                    </button>
                    <button wire:click="save" class="btn-3d btn-3d--indigo px-5 py-2 rounded-xl text-sm">
                        {{ $editingId ? 'บันทึกการแก้ไข' : 'สร้างหมวดหมู่' }}
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Delete Modal --}}
    <template x-teleport="body">
        <div
            x-show="deleteModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 z-[200] flex items-center justify-center p-4"
            x-cloak
        >
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6" @click.stop>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <x-icon name="trash" class="h-4 w-4 text-red-500" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800">ลบหมวดหมู่</h3>
                        <p class="text-sm text-gray-500">ยืนยันการลบ "<span x-text="deleteName"></span>"?</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button @click="deleteModal = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                        ยกเลิก
                    </button>
                    <button @click="$wire.delete(deleteId); deleteModal = false"
                        class="btn-3d btn-3d--red px-4 py-2 rounded-xl text-sm">
                        ลบ
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
