@props(['item'])

<div class="mt-4 w-full">
    @if($item['is_active'])
        <button wire:click="unequip({{ $item['id'] }})" wire:loading.attr="disabled"
            class="w-full py-2.5 bg-gray-200 text-[#686b82] hover:bg-gray-300 font-medium rounded-[8px] text-sm transition-colors cursor-pointer">
            <x-icon name="x-mark" class="h-4 w-4 mr-1" /> {{ 'ถอดออก' }}
        </button>
    @else
        <button wire:click="equip({{ $item['id'] }})" wire:loading.attr="disabled"
            class="w-full py-2.5 bg-[var(--ll-blue)] text-white hover:bg-[var(--ll-blue-dark)] font-medium rounded-[8px] text-sm transition-colors cursor-pointer">
            <x-icon name="swatch" class="h-4 w-4 mr-1" /> {{ 'สวมใส่' }}
        </button>
    @endif
</div>
