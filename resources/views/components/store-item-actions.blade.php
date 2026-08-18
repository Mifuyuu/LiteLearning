@props(['item', 'isOwned', 'coins'])
@php
    $previewValue = $item->type === 'avatar_frame' ? asset($item->value) : $item->value;
@endphp

<div class="mt-4 w-full">
    @if($isOwned)
        <a href="{{ route('inventory') }}" wire:navigate
            class="inline-flex items-center justify-center gap-1 w-full py-2.5 bg-gray-200 text-[#686b82] hover:bg-gray-300 font-medium rounded-[8px] text-sm transition-colors">
            <x-icon name="check" class="h-4 w-4 mr-1" /> {{ 'เป็นเจ้าของแล้ว' }}
        </a>
    @else
        <button @click="confirmPurchase({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->type }}', '{{ $previewValue }}')" class="w-full py-2.5 bg-[var(--ll-blue)] text-white hover:bg-[var(--ll-blue-dark)] font-medium rounded-[8px] text-sm transition-colors cursor-pointer flex justify-center items-center gap-2 {{ $coins < $item->price ? 'opacity-70' : '' }}">
            <x-icon name="star-solid" class="text-white h-4 w-4 shrink-0" />
            <span class="font-bold">{{ $item->price }}</span>
        </button>
    @endif
</div>
