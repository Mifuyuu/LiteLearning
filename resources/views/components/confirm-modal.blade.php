@props(['show', 'cancel', 'heading' => '', 'message' => '', 'icon' => 'trash'])

<div x-data x-show="{{ $show }}" x-cloak
    class="fixed inset-0 z-70 flex items-center justify-center bg-black/50 p-4"
    @click.self="{{ $cancel }}">
    <div x-show="{{ $show }}"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="w-full max-w-md rounded-[12px] border border-[#dedee5] bg-white p-6 shadow-[rgba(0,0,0,0.08)_0px_8px_32px]">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-50">
            <x-icon :name="$icon" class="h-7 w-7 text-rose-500" />
        </div>
        <h4 class="mt-4 text-center text-lg font-black text-[#101114]">{{ $heading }}</h4>
        <p class="mt-2 text-center text-sm text-[#686b82]">{{ $message }}</p>
        <div class="mt-5 flex justify-center gap-2">
            <button type="button" @click="{{ $cancel }}"
                class="flex-1 rounded-[10px] border border-[#dedee5] px-4 py-2.5 text-sm font-bold text-[#686b82] transition hover:bg-[rgba(37,99,235,0.04)]">
                {{ 'ยกเลิก' }}
            </button>
            {{ $slot }}
        </div>
    </div>
</div>
