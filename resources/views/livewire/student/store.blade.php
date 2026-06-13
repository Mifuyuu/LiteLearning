@section('page-title', __('store.title'))

<div class="space-y-6 ">
    <style>
        /* Light-toned custom scrollbar styles for store horizontal containers */
        .store-scrollbar::-webkit-scrollbar {
            height: 6px;
        }
        .store-scrollbar::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 9999px;
        }
        .store-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 9999px;
            transition: background 0.2s ease;
        }
        .store-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #d1d5db;
        }
        .store-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #e5e7eb #f3f4f6;
        }
    </style>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#101114] flex items-center gap-2"> {{ __('store.title') }}</h1>
            <p class="text-sm text-[#9497a9] mt-1">{{ __('store.subtitle') }}</p>
        </div>
        <div class="flex items-center space-x-2 bg-[rgba(133,91,251,0.16)] px-4 py-2 rounded-[8px] border border-[rgba(113,50,245,0.2)]">
            <x-icon name="star-solid" class="text-amber-500 h-5 w-5 shrink-0" />
            <span class="font-bold text-[#7132f5]">{{ number_format($coins) }}</span>
            <span class="text-xs text-[#5741d8] font-medium ml-1 shrink-0">{{ __('store.your_coins') }}</span>
        </div>
    </div>

    <div x-data="{ 
        showModal: false, 
        selectedItemId: null, 
        selectedItemName: '', 
        selectedItemPrice: 0,
        selectedItemType: '',
        selectedItemValue: '',
        confirmPurchase(id, name, price, type, value) {
            this.selectedItemId = id;
            this.selectedItemName = name;
            this.selectedItemPrice = price;
            this.selectedItemType = type;
            this.selectedItemValue = value;
            this.showModal = true;
        }
    }">
        <div class="bg-white rounded-xl border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-6 mb-6">
            <!-- Name Colors Section -->
            <h2 class="text-xl font-bold text-[#101114] mb-6 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6 text-[#7132f5] mr-2 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                </svg>
                {{ __('store.name_colors') }}
            </h2>
            <div class="flex space-x-6 overflow-x-auto pb-4 mb-8 store-scrollbar" @wheel="if ($event.deltaY !== 0) { $event.preventDefault(); $el.scrollLeft += $event.deltaY; }">
                @foreach($storeItems->where('type', 'name_color') as $item)
                    @php
                        $isOwned    = in_array($item->id, $ownedItemIds);
                        $isEquipped = ($activeNameColor === $item->value);
                    @endphp
                    <div class="w-[240px] shrink-0 border rounded-xl p-5 flex flex-col items-center text-center transition-all {{ $isEquipped ? 'border-[rgba(113,50,245,0.4)] bg-[rgba(133,91,251,0.08)] shadow-[rgba(0,0,0,0.03)_0px_4px_24px]' : 'border-[#dedee5] hover:border-[rgba(113,50,245,0.3)] hover:shadow-[rgba(0,0,0,0.06)_0px_4px_24px]' }}">
                        <div class="h-16 flex items-center justify-center font-bold text-xl {{ $item->value }}">
                            {{ auth()->user()->name }}
                        </div>

                        <h3 class="font-bold text-[#101114] mt-2">{{ $item->name }}</h3>
                        <p class="text-xs text-[#686b82] mt-1 h-10 line-clamp-2 px-2">{{ __($item->description) }}</p>

                        <div class="mt-4 w-full">
                            @if($isEquipped)
                                <button disabled class="w-full py-2.5 bg-[rgba(133,91,251,0.16)] text-[#7132f5] font-medium rounded-[8px] text-sm cursor-not-allowed">
                                    <i class="fas fa-check mr-1"></i> {{ __('store.equipped') }}
                                </button>
                            @elseif($isOwned)
                                <button wire:click="equip({{ $item->id }})" wire:loading.attr="disabled" class="w-full py-2.5 bg-white border border-[#5741d8] text-[#5741d8] hover:bg-[rgba(133,91,251,0.08)] font-medium rounded-[8px] text-sm transition-colors cursor-pointer">
                                    <i class="fas fa-tshirt mr-1"></i> {{ __('store.equip') }}
                                </button>
                            @endif
                            @php
                                $itemDisplayValue = $item->value;
                            @endphp
                            @if(!$isOwned)
                                <button @click="confirmPurchase({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->type }}', '{{ $itemDisplayValue }}')" class="w-full py-2.5 bg-[#7132f5] text-white hover:bg-[#5741d8] font-medium rounded-[8px] text-sm transition-colors cursor-pointer flex justify-center items-center gap-2 {{ $coins < $item->price ? 'opacity-70' : '' }}">
                                    <x-icon name="star-solid" class="text-white h-4 w-4 shrink-0" />
                                    <span class="font-bold">{{ $item->price }}</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Avatar Frames Section -->
            <h2 class="text-xl font-bold text-[#101114] mb-6 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6 text-[#7132f5] mr-2 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
                {{ __('store.avatar_frames') }}
            </h2>
            <div class="flex space-x-6 overflow-x-auto pb-4 store-scrollbar" @wheel="if ($event.deltaY !== 0) { $event.preventDefault(); $el.scrollLeft += $event.deltaY; }">
                @foreach($storeItems->where('type', 'avatar_frame') as $item)
                    @php
                        $isOwned    = in_array($item->id, $ownedItemIds);
                        $isEquipped = ($activeAvatarFrame === $item->value);
                    @endphp
                    <div class="w-[240px] shrink-0 border rounded-xl p-5 flex flex-col items-center text-center transition-all {{ $isEquipped ? 'border-[rgba(113,50,245,0.4)] bg-[rgba(133,91,251,0.08)] shadow-[rgba(0,0,0,0.03)_0px_4px_24px]' : 'border-[#dedee5] hover:border-[rgba(113,50,245,0.3)] hover:shadow-[rgba(0,0,0,0.06)_0px_4px_24px]' }}">
                        <div class="h-20 flex items-center justify-center mb-2 mt-4">
                            <div class="relative flex items-center justify-center">
                                <img src="{{ auth()->user()->avatar_url }}" class="w-16 h-16 rounded-full object-cover bg-white pointer-events-none border border-[#dedee5]" alt="{{ __('Avatar') }}">
                                @if(!str_starts_with($item->value, 'border'))
                                    <img src="{{ asset($item->value) }}" 
                                         class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                                @else
                                    <div class="absolute inset-0 rounded-full {{ $item->value }} pointer-events-none"></div>
                                @endif
                            </div>
                        </div>

                        <h3 class="font-bold text-[#101114] mt-2">{{ $item->name }}</h3>
                        <p class="text-xs text-[#686b82] mt-1 h-10 line-clamp-2 px-2">{{ __($item->description) }}</p>

                        <div class="mt-4 w-full">
                            @if($isEquipped)
                                <button disabled class="w-full py-2.5 bg-[rgba(133,91,251,0.16)] text-[#7132f5] font-medium rounded-[8px] text-sm cursor-not-allowed">
                                    <i class="fas fa-check mr-1"></i> {{ __('store.equipped') }}
                                </button>
                            @elseif($isOwned)
                                <button wire:click="equip({{ $item->id }})" wire:loading.attr="disabled" class="w-full py-2.5 bg-white border border-[#5741d8] text-[#5741d8] hover:bg-[rgba(133,91,251,0.08)] font-medium rounded-[8px] text-sm transition-colors cursor-pointer">
                                    <i class="fas fa-tshirt mr-1"></i> {{ __('store.equip') }}
                                </button>
                            @endif
                            @php
                                $itemDisplayValue = asset($item->value);
                            @endphp
                            @if(!$isOwned)
                                <button @click="confirmPurchase({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->type }}', '{{ $itemDisplayValue }}')" class="w-full py-2.5 bg-[#7132f5] text-white hover:bg-[#5741d8] font-medium rounded-[8px] text-sm transition-colors cursor-pointer flex justify-center items-center gap-2 {{ $coins < $item->price ? 'opacity-70' : '' }}">
                                    <x-icon name="star-solid" class="text-white h-4 w-4 shrink-0" />
                                    <span class="font-bold">{{ $item->price }}</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Purchase Confirmation Modal -->
        <template x-teleport="body">
            <div x-show="showModal" class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60" x-cloak
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @keydown.escape.window="showModal = false">
                <div class="w-full max-w-sm bg-white rounded-xl shadow-[rgba(0,0,0,0.03)_0px_4px_24px] border border-[#dedee5] overflow-hidden transform"
                    @click.stop x-show="showModal"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <div class="p-6 text-center">
                        <div class="mx-auto flex flex-col items-center justify-center p-4 bg-[rgba(133,91,251,0.04)] rounded-[8px] mb-6 min-h-[120px] border border-[#dedee5]">
                            
                            <!-- Name Color Preview -->
                            <template x-if="selectedItemType === 'name_color'">
                                <div class="font-bold text-2xl" :class="selectedItemValue">
                                    {{ auth()->user()->name }}
                                </div>
                            </template>

                            <!-- Avatar Frame Preview -->
                            <template x-if="selectedItemType === 'avatar_frame'">
                                <div class="relative flex items-center justify-center mt-2 mb-2">
                                    <img src="{{ auth()->user()->avatar_url }}" class="w-16 h-16 rounded-full object-cover bg-white pointer-events-none border border-[#dedee5]" alt="{{ __('Avatar') }}">
                                    
                                    <template x-if="!selectedItemValue.startsWith('border')">
                                        <img :src="selectedItemValue" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                                    </template>
                                    <template x-if="selectedItemValue.startsWith('border')">
                                        <div class="absolute inset-0 rounded-full pointer-events-none" :class="selectedItemValue"></div>
                                    </template>
                                </div>
                            </template>

                        </div>
                        
                        <h3 class="text-xl font-bold text-[#101114] mb-2">{{ __('store.modal_title') }}</h3>
                        <p class="text-[#686b82] mb-6">
                            {{ __('store.modal_desc_1') }} 
                            <span class="font-bold text-[#101114] block mt-2 text-lg" x-text="selectedItemName"></span> 
                            <span class="mt-2 flex items-center justify-center gap-1 text-[#7132f5] font-bold">
                                {{ __('store.modal_desc_2') }} <x-icon name="star-solid" class="text-amber-500 h-4 w-4 shrink-0" /> <span x-text="selectedItemPrice"></span> {{ __('store.modal_desc_3') }}
                            </span>
                        </p>
                        
                        <div class="flex flex-col gap-3">
                            <button type="button" @click="$wire.purchase(selectedItemId); showModal = false"
                                class="w-full py-2.5 bg-[#7132f5] text-white hover:bg-[#5741d8] font-bold rounded-[8px] text-sm transition-all">
                                <i class="fas fa-bag-shopping mr-1"></i> {{ __('store.modal_confirm') }}
                            </button>
                            <button type="button" @click="showModal = false"
                                class="w-full py-2.5 text-sm font-medium text-[#686b82] border border-[#dedee5] hover:bg-[rgba(133,91,251,0.04)] rounded-[8px] transition-colors">
                                <i class="fas fa-times mr-1"></i> {{ __('store.modal_cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
