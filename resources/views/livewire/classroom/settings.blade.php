@section('page-title', __('Settings') . ' - ' . $classroom->name)
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-[#7132f5]">
            {{ auth()->user()->isTeacher() ? __('My classes') : __('Classrooms') }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" wire:navigate class="text-[#686b82] transition-colors hover:text-[#7132f5]">
            {{ $classroom->name }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ __('Settings') }}</span>
    </nav>
@endsection

<div class="space-y-5 animate__animated animate__fadeIn">
    @include('livewire.classroom.partials.subnav', ['classroom' => $classroom])

    <section class="rounded-[12px] border border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px]">
        <div class="border-b border-[#dedee5] p-5">
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#9497a9]">{{ __('Settings') }}</p>
            <h1 class="mt-1 text-2xl font-black text-[#101114]">{{ __('Classroom settings') }}</h1>
        </div>

        <form wire:submit="saveSettings" class="space-y-5 p-5">
            <div class="grid gap-4 md:grid-cols-2">
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-[#101114]">{{ __('Name') }}</span>
                    <input wire:model="name" type="text"
                        class="w-full rounded-[10px] border border-[#dedee5] bg-white px-4 py-3 text-sm text-[#101114] outline-none transition focus:border-[#7132f5] focus:ring-2 focus:ring-[rgba(133,91,251,0.16)]">
                    @error('name') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-[#101114]">{{ __('Section') }}</span>
                    <input wire:model="section" type="text"
                        class="w-full rounded-[10px] border border-[#dedee5] bg-white px-4 py-3 text-sm text-[#101114] outline-none transition focus:border-[#7132f5] focus:ring-2 focus:ring-[rgba(133,91,251,0.16)]">
                    @error('section') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </label>
            </div>

            <label class="block">
                <span class="mb-2 block text-sm font-bold text-[#101114]">{{ __('Description') }}</span>
                <textarea wire:model="description" rows="4"
                    class="w-full rounded-[10px] border border-[#dedee5] bg-white px-4 py-3 text-sm text-[#101114] outline-none transition focus:border-[#7132f5] focus:ring-2 focus:ring-[rgba(133,91,251,0.16)]"></textarea>
                @error('description') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
            </label>

            <div>
                <p class="mb-3 text-sm font-bold text-[#101114]">{{ __('Theme') }}</p>
                <div class="grid grid-cols-4 gap-3 sm:grid-cols-7">
                    @foreach($themes as $theme)
                        @php $planet = str_pad($theme->planet_number, 2, '0', STR_PAD_LEFT); @endphp
                        <button type="button" wire:click="$set('theme_category_id', {{ $theme->id }})"
                            class="rounded-[10px] border-2 p-2 transition {{ $theme_category_id == $theme->id ? 'border-[#7132f5] bg-[rgba(133,91,251,0.16)]' : 'border-[#dedee5] hover:border-[rgba(113,50,245,0.3)] hover:bg-[rgba(133,91,251,0.04)]' }}">
                            <img src="/images/planets/planet_{{ $planet }}.svg" alt="{{ $theme->name }}" class="mx-auto h-12 w-12 object-contain">
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-2 border-t border-[#dedee5] pt-5">
                <button type="button" wire:click="toggleArchive"
                    class="inline-flex items-center gap-2 rounded-[10px] border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-bold text-amber-700 transition hover:bg-amber-100">
                    <x-icon name="archive-box" class="h-4 w-4" />
                    {{ $classroom->is_archived ? __('Restore') : __('Archive') }}
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-[10px] bg-[#7132f5] px-5 py-2.5 text-sm font-extrabold text-white transition hover:bg-[#5741d8]">
                    <x-icon name="check" class="h-4 w-4" />
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </section>
</div>
