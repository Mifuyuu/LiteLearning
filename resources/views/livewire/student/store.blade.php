@section('page-title', 'ร้านค้าของตกแต่ง')

<div class="max-w-4xl mx-auto"
    x-data="{
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
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh-3rem)]">
        <div class="relative p-6 lg:p-8">
            <img src="{{ asset('images/market.svg') }}" alt=""
                class="absolute right-4 top-4 hidden h-[calc(100%-2rem)] w-auto select-none object-contain sm:block lg:right-8" />

            <div class="relative max-w-sm">
                <h1 class="text-3xl font-black tracking-tight text-[#101114] sm:text-4xl">{{ 'ร้านค้าของตกแต่ง' }}</h1>
                <p class="mt-2 text-md leading-6 text-[#686b82]">{{ 'ใช้เหรียญแลกของตกแต่งสุดพิเศษ' }}</p>

                <div class="mt-4 flex items-center gap-2">
                    <span class="text-sm text-[#686b82]">{{ 'เหรียญของคุณ' }}</span>
                    <span class="text-xl font-black text-[#101114]">{{ number_format($coins) }}</span>
                    <img src="{{ asset('images/Coin.svg') }}" class="h-5 w-5 shrink-0" alt="">
                </div>
            </div>
        </div>

        <div class="border-t border-[#dedee5] space-y-8 p-6 lg:p-8">
            <div>
                <h2 class="text-xl font-bold text-[#101114] mb-6 flex items-center">{{ 'สีชื่อ' }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($storeItems->where('type', 'name_color') as $item)
                        @php
                            $isOwned = in_array($item->id, $ownedItemIds);
                        @endphp
                        <div class="relative border rounded-xl p-5 flex flex-col items-center text-center border-[#dedee5]">
                            @if($isOwned)
                                <span class="absolute top-3 right-3 flex items-center justify-center h-5 w-5 rounded-full bg-(--ll-blue) text-white">
                                    <x-icon name="check" class="h-3 w-3" />
                                </span>
                            @endif
                            <div class="h-16 flex items-center justify-center font-bold text-3xl {{ $item->value }}">
                                {{ 'Aa' }}
                            </div>
                            <h3 class="font-bold text-[#101114] mt-2">{{ $item->name }}</h3>
                            <p class="text-xs text-[#686b82] mt-1 h-10 line-clamp-2 px-2">{{ $item->description }}</p>
                            <x-store-item-actions :item="$item" :isOwned="$isOwned" :coins="$coins" />
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <h2 class="text-xl font-bold text-[#101114] mb-6 flex items-center">{{ 'กรอบรูปโปรไฟล์' }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($storeItems->where('type', 'avatar_frame') as $item)
                        @php
                            $isOwned = in_array($item->id, $ownedItemIds);
                        @endphp
                        <div class="relative border rounded-xl p-5 flex flex-col items-center text-center border-[#dedee5]">
                            @if($isOwned)
                                <span class="absolute top-3 right-3 flex items-center justify-center h-5 w-5 rounded-full bg-(--ll-blue) text-white">
                                    <x-icon name="check" class="h-3 w-3" />
                                </span>
                            @endif
                            <div class="h-20 flex items-center justify-center mb-2 mt-4">
                                <div class="relative flex items-center justify-center">
                                    <img src="{{ auth()->user()->avatar_url }}" class="w-16 h-16 rounded-full object-cover bg-white pointer-events-none border border-[#dedee5]" alt="{{ 'รูปโปรไฟล์' }}">
                                    @if(!str_starts_with($item->value, 'border'))
                                        <img src="{{ asset($item->value) }}"
                                             class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                                    @else
                                        <div class="absolute inset-0 rounded-full {{ $item->value }} pointer-events-none"></div>
                                    @endif
                                </div>
                            </div>
                            <h3 class="font-bold text-[#101114] mt-2">{{ $item->name }}</h3>
                            <p class="text-xs text-[#686b82] mt-1 h-10 line-clamp-2 px-2">{{ $item->description }}</p>
                            <x-store-item-actions :item="$item" :isOwned="$isOwned" :coins="$coins" />
                        </div>
                    @endforeach
                </div>
            </div>
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
                    <div class="mx-auto flex flex-col items-center justify-center p-4 bg-(--ll-blue-faint) rounded-lg mb-6 min-h-30 border border-[#dedee5]">

                        <!-- Name Color Preview -->
                        <template x-if="selectedItemType === 'name_color'">
                            <div class="font-bold text-4xl" :class="selectedItemValue">
                                {{ 'Aa' }}
                            </div>
                        </template>

                        <!-- Avatar Frame Preview -->
                        <template x-if="selectedItemType === 'avatar_frame'">
                            <div class="relative flex items-center justify-center mt-2 mb-2">
                                <img src="{{ auth()->user()->avatar_url }}" class="w-16 h-16 rounded-full object-cover bg-white pointer-events-none border border-[#dedee5]" alt="{{ 'รูปโปรไฟล์' }}">

                                <template x-if="!selectedItemValue.startsWith('border')">
                                    <img :src="selectedItemValue" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                                </template>
                                <template x-if="selectedItemValue.startsWith('border')">
                                    <div class="absolute inset-0 rounded-full pointer-events-none" :class="selectedItemValue"></div>
                                </template>
                            </div>
                        </template>

                    </div>

                    <h3 class="text-xl font-bold text-[#101114] mb-2">{{ 'ยืนยันการซื้อ' }}</h3>
                    <p class="text-[#686b82] mb-6">
                        {{ 'คุณแน่ใจหรือไม่ว่าต้องการซื้อ' }}
                        <span class="font-bold text-[#101114] block mt-2 text-lg" x-text="selectedItemName"></span>
                        <span class="mt-2 flex items-center justify-center gap-1 text-(--ll-blue) font-bold">
                            {{ 'ในราคา' }} <img src="{{ asset('images/Coin.svg') }}" class="h-4 w-4 shrink-0" alt=""> <span x-text="selectedItemPrice"></span> {{ 'เหรียญ?' }}
                        </span>
                    </p>

                    <div class="flex gap-3">
                        <button type="button" @click="showModal = false"
                            class="w-full py-2.5 text-sm font-medium text-[#686b82] border border-[#dedee5] hover:bg-(--ll-blue-faint) rounded-lg transition-colors">
                            <x-icon name="x-mark" class="h-4 w-4 mr-1" /> {{ 'ยกเลิก' }}
                        </button>
                        <button type="button" @click="$wire.purchase(selectedItemId); showModal = false"
                            class="w-full py-2.5 bg-(--ll-blue) text-white hover:bg-(--ll-blue-dark) font-bold rounded-lg text-sm transition-all">
                            <x-icon name="shopping-bag" class="h-4 w-4 mr-1" /> {{ 'ยืนยันการซื้อ' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
