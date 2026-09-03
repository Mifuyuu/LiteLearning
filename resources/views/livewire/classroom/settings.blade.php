@section('page-title', 'ตั้งค่า' . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)">
            ...
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-[#686b82] transition-colors hover:text-(--ll-blue)" title="{{ $classroom->name }}">
            {{ \Illuminate\Support\Str::limit($classroom->name, 10, '..') }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ 'ตั้งค่า' }}</span>
    </nav>
@endsection

<div class="space-y-5 max-w-4xl mx-auto">
    <section class="rounded-xl border-3 border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        <div class="border-b border-[#dedee5] p-5">
            <h1 class="mt-1 text-2xl font-black text-[#101114]">{{ 'ตั้งค่าห้องเรียน' }}</h1>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#dedee5] p-5"
            x-data="{ copied: false, showCodeModal: false, copy() { navigator.clipboard.writeText('{{ $classroom->code }}'); this.copied = true; setTimeout(() => this.copied = false, 2000); } }"
            @keydown.escape.window="showCodeModal = false">
            <div>
                <p class="text-sm font-extrabold uppercase text-[#101114]">{{ 'รหัสเข้าห้องเรียน' }}</p>
                <p class="mt-1 font-mono text-2xl font-black text-(--ll-blue)">{{ $classroom->code }}</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="showCodeModal = true" title="{{ 'ขยาย' }}"
                    class="inline-flex items-center gap-2 rounded-[10px] border border-[#dedee5] px-4 py-2.5 text-sm font-bold text-[#101114] transition hover:bg-(--ll-blue-faint)">
                    <x-icon name="arrows-pointing-out" class="h-4 w-4" />
                </button>
                <button type="button" @click="copy()"
                    class="inline-flex items-center gap-2 rounded-[10px] border border-[#dedee5] px-4 py-2.5 text-sm font-bold text-[#101114] transition hover:bg-(--ll-blue-faint)">
                    <template x-if="!copied">
                        <span class="inline-flex items-center gap-2"><x-icon name="clipboard-document-list" class="h-4 w-4" />{{ 'คัดลอก' }}</span>
                    </template>
                    <template x-if="copied">
                        <span class="inline-flex items-center gap-2 text-green-600"><x-icon name="check" class="h-4 w-4" />{{ 'คัดลอกแล้ว' }}</span>
                    </template>
                </button>
            </div>

            <template x-teleport="body">
                <div x-show="showCodeModal" x-cloak
                    class="fixed inset-0 z-100 flex flex-col items-center justify-center bg-white p-6"
                    x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    @click="showCodeModal = false">
                    <button type="button" @click.stop="showCodeModal = false"
                        class="absolute right-6 top-6 text-[#9497a9] transition hover:text-[#101114]">
                        <x-icon name="x-mark" class="h-8 w-8" />
                    </button>
                    <p class="text-lg font-bold text-[#9497a9] sm:text-2xl">{{ 'รหัสเข้าห้องเรียน' }}</p>
                    <p class="mt-4 select-all break-all text-center font-mono text-[18vw] font-black leading-none text-(--ll-blue) sm:text-[12rem]">
                        {{ $classroom->code }}
                    </p>
                    <p class="mt-6 max-w-2xl text-center text-2xl font-bold text-[#9497a9] sm:text-3xl">{{ $classroom->name }}</p>
                </div>
            </template>
        </div>

        <div class="flex items-center justify-between gap-3 border-b border-[#dedee5] p-5">
            <div>
                <p class="text-sm font-bold text-[#101114]">{{ 'อนุญาตให้เข้าร่วมห้องเรียนด้วยรหัส' }}</p>
                <p class="mt-0.5 text-xs text-[#9497a9]">{{ 'ปิดชั่วคราวเพื่อไม่ให้มีใครเข้าร่วมห้องนี้ได้ แม้จะมีรหัสก็ตาม' }}</p>
            </div>
            <input type="checkbox" wire:click="toggleJoinEnabled" @checked($classroom->join_enabled)
                class="toggle toggle-primary shrink-0">
        </div>

        <form wire:submit.prevent="saveSettings" class="space-y-5 p-5">
            <div class="grid gap-4 md:grid-cols-2">
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-[#101114]">{{ 'ชื่อ' }}</span>
                    <input wire:model="name" type="text"
                        class="w-full rounded-[10px] border border-[#dedee5] bg-white px-4 py-3 text-sm text-[#101114] outline-none transition focus:border-(--ll-blue) focus:ring-1 focus:ring-(--ll-blue-subtle)">
                    @error('name') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-[#101114]">{{ 'ห้อง' }}</span>
                    <input wire:model="section" type="text"
                        class="w-full rounded-[10px] border border-[#dedee5] bg-white px-4 py-3 text-sm text-[#101114] outline-none transition focus:border-(--ll-blue) focus:ring-1 focus:ring-(--ll-blue-subtle)">
                    @error('section') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </label>
            </div>

            <label class="block">
                <span class="mb-2 block text-sm font-bold text-[#101114]">{{ 'รายละเอียด' }}</span>
                <textarea wire:model="description" rows="4"
                    class="w-full rounded-[10px] border border-[#dedee5] bg-white px-4 py-3 text-sm text-[#101114] outline-none transition focus:border-(--ll-blue) focus:ring-1 focus:ring-(--ll-blue-subtle)"></textarea>
                @error('description') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
            </label>

            <div>
                <p class="mb-3 text-sm font-bold text-[#101114]">{{ 'ธีม' }}</p>
                <div class="grid grid-cols-4 gap-3 sm:grid-cols-7">
                    @foreach($themes as $theme)
                        <button type="button" wire:click="$set('theme_category_id', {{ $theme->id }})"
                            class="relative rounded-[10px] border-2 p-2 transition {{ $theme_category_id == $theme->id ? 'border-(--ll-blue) bg-(--ll-blue-subtle)' : 'border-[#dedee5] hover:border-[rgba(37,99,235,0.3)] hover:bg-(--ll-blue-faint)' }}">
                            @if($theme_category_id == $theme->id)
                                <span class="absolute -right-1.5 -top-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-(--ll-blue) text-white shadow-sm">
                                    <x-icon name="check" class="h-3 w-3" />
                                </span>
                            @endif
                            <img src="/images/planets/planet_{{ $theme->planet_key }}.svg" alt="{{ $theme->name }}" class="mx-auto h-12 w-12 object-contain">
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-2 border-t border-[#dedee5] pt-5">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-[10px] bg-(--ll-blue) px-5 py-2.5 text-sm font-extrabold text-white transition hover:bg-(--ll-blue-dark)">
                    <x-icon name="check" class="h-4 w-4" />
                    {{ 'บันทึก' }}
                </button>
            </div>
        </form>

        <div class="flex flex-wrap items-center gap-3 p-5">
            <div class="max-w-xl">
                <h2 class="text-sm font-black text-amber-700">{{ $classroom->is_archived ? 'กู้คืนห้องเรียน' : 'เก็บถาวรห้องเรียน' }}</h2>
                <p class="mt-1 text-sm text-[#686b82]">
                    {{ $classroom->is_archived
                        ? 'ห้องเรียนนี้ถูกเก็บถาวรอยู่ นักเรียนและคุณจะไม่เห็นห้องนี้ในรายการห้องเรียนตามปกติ กู้คืนเพื่อให้กลับมาใช้งานได้เหมือนเดิม'
                        : 'ซ่อนห้องเรียนนี้จากรายการห้องเรียนของคุณและนักเรียนไว้ชั่วคราว โดยไม่ลบข้อมูลใด ๆ เหมาะสำหรับห้องที่จบเทอมแล้วแต่ยังไม่อยากลบทิ้ง และสามารถกู้คืนได้ทุกเมื่อ' }}
                </p>
            </div>
            <button type="button" wire:click="toggleArchive"
                class="ml-auto inline-flex shrink-0 items-center gap-2 rounded-[10px] px-4 py-2.5 text-sm font-bold text-white transition {{ $classroom->is_archived ? 'bg-gray-500 hover:bg-gray-600' : 'bg-amber-500 hover:bg-amber-600' }}">
                <x-icon name="archive-box" class="h-4 w-4" />
                {{ $classroom->is_archived ? 'กู้คืน' : 'เก็บถาวร' }}
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-3 rounded-b-xl p-5">
            <div class="max-w-xl">
                <h2 class="text-sm font-black text-rose-700">{{ 'จุดอันตราย' }}</h2>
                <p class="mt-1 text-sm text-[#686b82]">{{ 'ลบห้องเรียนนี้ นักเรียนและครูร่วมจะไม่เห็นห้องนี้อีก ข้อมูลจะยังไม่ถูกลบจริง แอดมินสามารถกู้คืนได้' }}</p>
            </div>
            <div x-data="{ showDeleteModal: false }" class="ml-auto shrink-0">
                <button type="button" @click="showDeleteModal = true"
                    class="inline-flex items-center gap-2 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700 shrink-0">
                    <x-icon name="trash" class="h-4 w-4" />
                    {{ 'ลบห้องเรียน' }}
                </button>

                <template x-teleport="body">
                    <x-confirm-modal show="showDeleteModal" cancel="showDeleteModal = false"
                        heading="ยืนยันการลบห้องเรียน">
                        <x-slot:message>
                            การกระทำนี้จะซ่อนห้อง <span class="font-semibold text-[#101114]">{{ $classroom->name }}</span> จากนักเรียนและครูร่วมทั้งหมด
                            พิมพ์ชื่อห้องเรียนด้านล่างเพื่อยืนยัน
                            <input type="text" wire:model="deleteConfirm"
                                placeholder="{{ $classroom->name }}"
                                class="mt-3 w-full rounded-[10px] border border-[#dedee5] bg-white px-3 py-2 text-sm text-[#101114] outline-none transition focus:border-rose-400 focus:ring-1 focus:ring-rose-200">
                            @error('deleteConfirm') <span class="mt-2 block text-left text-sm text-rose-500">{{ $message }}</span> @enderror
                        </x-slot:message>
                        <button type="button" wire:click="deleteClassroom"
                            class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                            {{ 'ลบห้องเรียน' }}
                        </button>
                    </x-confirm-modal>
                </template>
            </div>
        </div>
    </section>
</div>
