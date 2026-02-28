@section('page-title', __('store.title'))

<div class="space-y-6 animate__animated animate__fadeIn">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2"> {{ __('store.title') }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ __('store.subtitle') }}</p>
        </div>
        <div class="flex items-center space-x-2 bg-indigo-50 px-4 py-2 rounded-full border border-indigo-100">
            <i class="gsi-gemstone-blue text-blue-500 text-xl"></i>
            <span class="font-bold text-indigo-700">{{ number_format($coins) }}</span>
            <span class="text-xs text-indigo-500 font-medium ml-1 shrink-0">{{ __('store.your_coins') }}</span>
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
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="{{ $info['icon'] }} mr-2"></i> {{ $info['label'] }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @foreach($storeItems->where('type', $type) as $item)
                    @php
                        $isOwned    = in_array($item->id, $ownedItemIds);
                        $isEquipped = ($type === 'name_color'   && $activeNameColor   === $item->value) ||
                                      ($type === 'avatar_frame' && $activeAvatarFrame === $item->value);
                    @endphp
                    <div class="border rounded-xl p-5 flex flex-col items-center text-center transition-all {{ $isEquipped ? 'border-indigo-400 bg-indigo-50/50 shadow-sm' : 'border-gray-200 hover:border-indigo-200 hover:shadow-md' }}">

                        @if($type === 'name_color')
                            <div class="h-16 flex items-center justify-center font-bold text-xl {{ $item->value }}">
                                {{ auth()->user()->name }}
                            </div>
                        @else
                            <div class="h-20 flex items-center justify-center mb-2 mt-4">
                                <div class="relative flex items-center justify-center">
                                    <img src="{{ auth()->user()->avatar_url }}" class="w-16 h-16 rounded-full object-cover bg-white pointer-events-none border border-gray-100" alt="{{ __('Avatar') }}">
                                    @if(!str_starts_with($item->value, 'border'))
                                        <img src="{{ asset($item->value) }}" 
                                             class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                                    @else
                                        <div class="absolute inset-0 rounded-full {{ $item->value }} pointer-events-none"></div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <h3 class="font-bold text-gray-900 mt-2">{{ $item->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1 h-10 line-clamp-2 px-2">{{ __($item->description) }}</p>

                        <div class="mt-4 w-full">
                            @if($isEquipped)
                                <button disabled class="w-full py-2.5 bg-indigo-100 text-indigo-700 font-medium rounded-lg text-sm cursor-not-allowed">
                                    <i class="fas fa-check mr-1"></i> {{ __('store.equipped') }}
                                </button>
                            @elseif($isOwned)
                                <button wire:click="equip({{ $item->id }})" wire:loading.attr="disabled" class="w-full py-2.5 bg-white border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-medium rounded-lg text-sm transition-colors cursor-pointer">
                                    <i class="fas fa-tshirt mr-1"></i> {{ __('store.equip') }}
                                </button>
                            @endif
                            @php
                                $itemDisplayValue = $type === 'name_color' ? $item->value : asset($item->value);
                            @endphp
                            @if(!$isOwned)
                                <button @click="confirmPurchase({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->type }}', '{{ $itemDisplayValue }}')" class="btn-3d btn-3d--indigo w-full py-2.5 font-medium rounded-lg text-sm transition-colors cursor-pointer flex justify-center items-center {{ $coins < $item->price ? 'opacity-70' : '' }}">
                                    <i class="gsi-gemstone-blue mr-2 text-white"></i> {{ $item->price }}
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
                <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform"
                    @click.stop x-show="showModal"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <div class="p-6 text-center">
                        <div class="mx-auto flex flex-col items-center justify-center p-4 bg-gray-50 rounded-xl mb-6 min-h-[120px] border border-gray-100">
                            
                            <!-- Name Color Preview -->
                            <template x-if="selectedItemType === 'name_color'">
                                <div class="font-bold text-2xl" :class="selectedItemValue">
                                    {{ auth()->user()->name }}
                                </div>
                            </template>

                            <!-- Avatar Frame Preview -->
                            <template x-if="selectedItemType === 'avatar_frame'">
                                <div class="relative flex items-center justify-center mt-2 mb-2">
                                    <img src="{{ auth()->user()->avatar_url }}" class="w-16 h-16 rounded-full object-cover bg-white pointer-events-none border border-gray-100" alt="{{ __('Avatar') }}">
                                    
                                    <template x-if="!selectedItemValue.startsWith('border')">
                                        <img :src="selectedItemValue" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                                    </template>
                                    <template x-if="selectedItemValue.startsWith('border')">
                                        <div class="absolute inset-0 rounded-full pointer-events-none" :class="selectedItemValue"></div>
                                    </template>
                                </div>
                            </template>

                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('store.modal_title') }}</h3>
                        <p class="text-gray-500 mb-6">
                            {{ __('store.modal_desc_1') }} 
                            <span class="font-bold text-gray-900 block mt-2 text-lg" x-text="selectedItemName"></span> 
                            <span class="mt-2 flex items-center justify-center gap-1 text-indigo-600 font-bold">
                                {{ __('store.modal_desc_2') }} <i class="gsi-gemstone-blue text-sm"></i> <span x-text="selectedItemPrice"></span> {{ __('store.modal_desc_3') }}
                            </span>
                        </p>
                        
                        <div class="flex flex-col gap-3">
                            <button type="button" @click="$wire.purchase(selectedItemId); showModal = false"
                                class="btn-3d btn-3d--indigo w-full py-2.5 font-bold rounded-lg text-sm transition-all">
                                <i class="fas fa-bag-shopping mr-1"></i> {{ __('store.modal_confirm') }}
                            </button>
                            <button type="button" @click="showModal = false"
                                class="w-full py-2.5 text-sm font-medium text-gray-600 border border-gray-300 hover:bg-gray-100 rounded-lg transition-colors">
                                <i class="fas fa-times mr-1"></i> {{ __('store.modal_cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
