@section('page-title', __('store.title'))

<div class="space-y-6 animate__animated animate__fadeIn">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-store text-indigo-500"></i> {{ __('store.title') }}
            </h1>
            <p class="text-sm text-gray-400 mt-1">{{ __('store.subtitle') }}</p>
        </div>
        <div class="flex items-center space-x-2 bg-indigo-50 px-4 py-2 rounded-full border border-indigo-100">
            <i class="gsi-gemstone-blue text-blue-500 text-xl"></i>
            <span class="font-bold text-indigo-700">{{ number_format($coins) }}</span>
            <span class="text-xs text-indigo-500 font-medium ml-1 flex-shrink-0">{{ __('store.your_coins') }}</span>
        </div>
    </div>

    <!-- Group by type: name_color vs avatar_frame -->
    @foreach([
        'name_color'    => ['label' => __('store.name_colors'),   'icon' => 'fas fa-palette text-pink-500'],
        'avatar_frame'  => ['label' => __('store.avatar_frames'), 'icon' => 'fas fa-border-all text-amber-500']
    ] as $type => $info)
    <div class="bg-white rounded-xl border border-gray-200 p-6">
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
                            <button wire:click="equip({{ $item->id }})" wire:loading.attr="disabled" class="w-full py-2.5 bg-white border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-medium rounded-lg text-sm transition-colors cursor-pointer focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('store.equip') }}
                            </button>
                        @endif
                        @if(!$isOwned)
                            <button wire:click="purchase({{ $item->id }})" wire:loading.attr="disabled" wire:confirm="{{ __('store.buy_confirm', ['item' => $item->name, 'price' => $item->price]) }}" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition-colors cursor-pointer flex justify-center items-center shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $coins < $item->price ? 'opacity-70' : '' }}">
                                <i class="gsi-gemstone-blue mr-2 text-white"></i> {{ $item->price }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
