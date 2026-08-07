@section('page-title', 'ตั้งค่า' . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-[var(--ll-blue)]">
            {{ auth()->user()->isTeacher() ? 'ชั้นเรียนของฉัน' : 'ห้องเรียน' }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-[#686b82] transition-colors hover:text-[var(--ll-blue)]">
            {{ $classroom->name }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ 'ตั้งค่า' }}</span>
    </nav>
@endsection

<div class="space-y-5 ">
    <section class="rounded-[12px] border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        <div class="border-b border-[#dedee5] p-5">
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#9497a9]">{{ 'ตั้งค่า' }}</p>
            <h1 class="mt-1 text-2xl font-black text-[#101114]">{{ 'ตั้งค่าห้องเรียน' }}</h1>
        </div>

        <form wire:submit.prevent="saveSettings" class="space-y-5 p-5">
            <div class="grid gap-4 md:grid-cols-2">
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-[#101114]">{{ 'ชื่อ' }}</span>
                    <input wire:model="name" type="text"
                        class="w-full rounded-[10px] border border-[#dedee5] bg-white px-4 py-3 text-sm text-[#101114] outline-none transition focus:border-[var(--ll-blue)] focus:ring-2 focus:ring-[var(--ll-blue-subtle)]">
                    @error('name') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-[#101114]">{{ 'ห้อง' }}</span>
                    <input wire:model="section" type="text"
                        class="w-full rounded-[10px] border border-[#dedee5] bg-white px-4 py-3 text-sm text-[#101114] outline-none transition focus:border-[var(--ll-blue)] focus:ring-2 focus:ring-[var(--ll-blue-subtle)]">
                    @error('section') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </label>
            </div>

            <label class="block">
                <span class="mb-2 block text-sm font-bold text-[#101114]">{{ 'รายละเอียด' }}</span>
                <textarea wire:model="description" rows="4"
                    class="w-full rounded-[10px] border border-[#dedee5] bg-white px-4 py-3 text-sm text-[#101114] outline-none transition focus:border-[var(--ll-blue)] focus:ring-2 focus:ring-[var(--ll-blue-subtle)]"></textarea>
                @error('description') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
            </label>

            <div>
                <p class="mb-3 text-sm font-bold text-[#101114]">{{ 'ธีม' }}</p>
                <div class="grid grid-cols-4 gap-3 sm:grid-cols-7">
                    @foreach($themes as $theme)
                        @php $planet = str_pad($theme->planet_number, 2, '0', STR_PAD_LEFT); @endphp
                        <button type="button" wire:click="$set('theme_category_id', {{ $theme->id }})"
                            class="rounded-[10px] border-2 p-2 transition {{ $theme_category_id == $theme->id ? 'border-[var(--ll-blue)] bg-[var(--ll-blue-subtle)]' : 'border-[#dedee5] hover:border-[rgba(37,99,235,0.3)] hover:bg-[var(--ll-blue-faint)]' }}">
                            <img src="/images/planets/planet_{{ $planet }}.svg" alt="{{ $theme->name }}" class="mx-auto h-12 w-12 object-contain">
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-2 border-t border-[#dedee5] pt-5">
                <button type="button" wire:click="toggleArchive"
                    class="inline-flex items-center gap-2 rounded-[10px] border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-bold text-amber-700 transition hover:bg-amber-100">
                    <x-icon name="archive-box" class="h-4 w-4" />
                    {{ $classroom->is_archived ? 'กู้คืน' : 'เก็บถาวร' }}
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-[10px] bg-[var(--ll-blue)] px-5 py-2.5 text-sm font-extrabold text-white transition hover:bg-[var(--ll-blue-dark)]">
                    <x-icon name="check" class="h-4 w-4" />
                    {{ 'บันทึก' }}
                </button>
            </div>
        </form>
    </section>
</div>
