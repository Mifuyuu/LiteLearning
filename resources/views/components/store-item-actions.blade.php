@props(['item', 'isOwned', 'coins'])
@php
    $previewValue = $item->type === 'avatar_frame' ? asset($item->value) : $item->value;
@endphp

<div class="mt-4 w-full">
    @if($isOwned)
        <a href="{{ route('inventory') }}" wire:navigate
            class="inline-flex items-center justify-center gap-1 w-full py-2.5 bg-gray-200 text-[#686b82] hover:bg-gray-300 font-medium rounded-lg text-sm transition-colors">
            <x-icon name="check" class="h-4 w-4 mr-1" /> {{ 'เป็นเจ้าของแล้ว' }}
        </a>
    @else
        <span class="absolute top-3 left-3 inline-flex items-center gap-1 bg-white border border-[#dedee5] rounded-full px-2 py-1 text-xs font-bold text-[#101114]">
            <img src="{{ asset('images/Coin.svg') }}" class="h-3 w-3 shrink-0" alt=""> {{ $item->price }}
        </span>
        <button @click="confirmPurchase({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->type }}', '{{ $previewValue }}')" class="w-full py-2.5 bg-(--ll-blue) text-white hover:bg-(--ll-blue-dark) font-medium rounded-lg text-sm transition-colors cursor-pointer flex justify-center items-center gap-1.5 {{ $coins < $item->price ? 'opacity-70' : '' }}">
            <x-icon name="shopping-bag" class="h-4 w-4 shrink-0" /> {{ 'ซื้อ' }}
        </button>
    @endif
</div>
