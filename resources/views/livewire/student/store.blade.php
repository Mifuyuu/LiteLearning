@section('page-title', __('store.title'))

<div class="space-y-6 animate__animated animate__fadeIn">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#101114] flex items-center gap-2"> {{ __('store.title') }}</h1>
            <p class="text-sm text-[#9497a9] mt-1">{{ __('store.subtitle') }}</p>
        </div>
        <div class="flex items-center space-x-2 bg-[rgba(133,91,251,0.16)] px-4 py-2 rounded-[12px] border border-[rgba(113,50,245,0.2)]">
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
        <!-- Group by type: name_color vs avatar_frame -->
        @foreach([
            'name_color'    => ['label' => __('store.name_colors'),   'icon' => 'fas fa-palette text-pink-500'],
            'avatar_frame'  => ['label' => __('store.avatar_frames'), 'icon' => 'fas fa-border-all text-amber-500']
        ] as $type => $info)
        <div class="bg-white rounded-2xl border border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] p-6 mb-6">
            <h2 class="text-xl font-bold text-[#101114] mb-6 flex items-center">
                <i class="{{ $info['icon'] }} mr-2"></i> {{ $info['label'] }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @foreach($storeItems->where('type', $type) as $item)
                    @php
                        $isOwned    = in_array($item->id, $ownedItemIds);
                        $isEquipped = ($type === 'name_color'   && $activeNameColor   === $item->value) ||
                                      ($type === 'avatar_frame' && $activeAvatarFrame === $item->value);
                    @endphp
                    <div class="border rounded-2xl p-5 flex flex-col items-center text-center transition-all {{ $isEquipped ? 'border-[rgba(113,50,245,0.4)] bg-[rgba(133,91,251,0.08)] shadow-[rgba(0,0,0,0.03)_0px_4px_24px]' : 'border-[#dedee5] hover:border-[rgba(113,50,245,0.3)] hover:shadow-[rgba(0,0,0,0.06)_0px_4px_24px]' }}">

                        @if($type === 'name_color')
                            <div class="h-16 flex items-center justify-center font-bold text-xl {{ $item->value }}">
                                {{ auth()->user()->name }}
                            </div>
                        @else
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
                        @endif

                        <h3 class="font-bold text-[#101114] mt-2">{{ $item->name }}</h3>
                        <p class="text-xs text-[#686b82] mt-1 h-10 line-clamp-2 px-2">{{ __($item->description) }}</p>

                        <div class="mt-4 w-full">
                            @if($isEquipped)
                                <button disabled class="w-full py-2.5 bg-[rgba(133,91,251,0.16)] text-[#7132f5] font-medium rounded-[12px] text-sm cursor-not-allowed">
                                    <i class="fas fa-check mr-1"></i> {{ __('store.equipped') }}
                                </button>
                            @elseif($isOwned)
                                <button wire:click="equip({{ $item->id }})" wire:loading.attr="disabled" class="w-full py-2.5 bg-white border border-[#5741d8] text-[#5741d8] hover:bg-[rgba(133,91,251,0.08)] font-medium rounded-[12px] text-sm transition-colors cursor-pointer">
                                    <i class="fas fa-tshirt mr-1"></i> {{ __('store.equip') }}
                                </button>
                            @endif
                            @php
                                $itemDisplayValue = $type === 'name_color' ? $item->value : asset($item->value);
                            @endphp
                            @if(!$isOwned)
                                <button @click="confirmPurchase({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->type }}', '{{ $itemDisplayValue }}')" class="w-full py-2.5 bg-[#7132f5] text-white hover:bg-[#5741d8] font-medium rounded-[12px] text-sm transition-colors cursor-pointer flex justify-center items-center gap-2 {{ $coins < $item->price ? 'opacity-70' : '' }}">
                                    <x-icon name="star-solid" class="text-white h-4 w-4 shrink-0" />
                                    <span class="font-bold">{{ $item->price }}</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Purchase Confirmation Modal -->
        <template x-teleport="body">
            <div x-show="showModal" class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60" x-cloak
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @keydown.escape.window="showModal = false">
                <div class="w-full max-w-sm bg-white rounded-2xl shadow-[rgba(0,0,0,0.03)_0px_4px_24px] border border-[#dedee5] overflow-hidden transform"
                    @click.stop x-show="showModal"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <div class="p-6 text-center">
                        <div class="mx-auto flex flex-col items-center justify-center p-4 bg-[rgba(133,91,251,0.04)] rounded-[12px] mb-6 min-h-[120px] border border-[#dedee5]">
                            
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
                                class="w-full py-2.5 bg-[#7132f5] text-white hover:bg-[#5741d8] font-bold rounded-[12px] text-sm transition-all">
                                <i class="fas fa-bag-shopping mr-1"></i> {{ __('store.modal_confirm') }}
                            </button>
                            <button type="button" @click="showModal = false"
                                class="w-full py-2.5 text-sm font-medium text-[#686b82] border border-[#dedee5] hover:bg-[rgba(133,91,251,0.04)] rounded-[12px] transition-colors">
                                <i class="fas fa-times mr-1"></i> {{ __('store.modal_cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
