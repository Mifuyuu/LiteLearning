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
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh_-_3rem)]">
        <div class="relative p-6 lg:p-8">
            <img src="{{ asset('images/market.svg') }}" alt=""
                class="absolute right-4 top-4 hidden h-[calc(100%_-_2rem)] w-auto select-none object-contain sm:block lg:right-8" />

            <div class="relative max-w-sm">
                <h1 class="text-3xl font-black tracking-tight text-[#101114] sm:text-4xl">{{ 'ร้านค้าของตกแต่ง' }}</h1>
                <p class="mt-2 text-md leading-6 text-[#686b82]">{{ 'ใช้เหรียญแลกของตกแต่งสุดพิเศษ' }}</p>

                <div class="mt-4 flex items-center gap-2">
                    <x-icon name="star-solid" class="text-amber-500 h-5 w-5 shrink-0" />
                    <span class="text-xl font-black text-[#101114]">{{ number_format($coins) }}</span>
                    <span class="text-sm text-[#686b82]">{{ 'เหรียญของคุณ' }}</span>
                </div>
            </div>
        </div>

        <div class="border-t border-[#dedee5] space-y-8 p-6 lg:p-8">
            <div>
                <h2 class="text-xl font-bold text-[#101114] mb-6 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6 text-[var(--ll-blue)] mr-2 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                    </svg>
                    {{ 'สีชื่อ' }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($storeItems->where('type', 'name_color') as $item)
                        @php
                            $isOwned = in_array($item->id, $ownedItemIds);
                        @endphp
                        <div class="relative border rounded-xl p-5 flex flex-col items-center text-center border-[#dedee5]">
                            @if($isOwned)
                                <span class="absolute top-3 right-3 flex items-center justify-center h-5 w-5 rounded-full bg-[var(--ll-blue)] text-white">
                                    <x-icon name="check" class="h-3 w-3" />
                                </span>
                            @endif
                            <div class="h-16 flex items-center justify-center font-bold text-3xl {{ $item->value }}">
                                {{ 'Aa' }}
                            </div>
                            <h3 class="font-bold text-[#101114] mt-2">{{ $item->name }}</h3>
                            <p class="text-xs text-[#686b82] mt-1 h-10 line-clamp-2 px-2">{{ $item->description }}</p>
                            <div class="mt-4 w-full">
                                @if($isOwned)
                                    <a href="{{ route('inventory') }}" wire:navigate
                                        class="inline-flex items-center justify-center gap-1 w-full py-2.5 bg-gray-200 text-[#686b82] hover:bg-gray-300 font-medium rounded-[8px] text-sm transition-colors">
                                        <x-icon name="check" class="h-4 w-4 mr-1" /> {{ 'เป็นเจ้าของแล้ว' }}
                                    </a>
                                @else
                                    <button @click="confirmPurchase({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->type }}', '{{ $item->value }}')" class="w-full py-2.5 bg-[var(--ll-blue)] text-white hover:bg-[var(--ll-blue-dark)] font-medium rounded-[8px] text-sm transition-colors cursor-pointer flex justify-center items-center gap-2 {{ $coins < $item->price ? 'opacity-70' : '' }}">
                                        <x-icon name="star-solid" class="text-white h-4 w-4 shrink-0" />
                                        <span class="font-bold">{{ $item->price }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <h2 class="text-xl font-bold text-[#101114] mb-6 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6 text-[var(--ll-blue)] mr-2 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                    {{ 'กรอบรูปโปรไฟล์' }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($storeItems->where('type', 'avatar_frame') as $item)
                        @php
                            $isOwned = in_array($item->id, $ownedItemIds);
                        @endphp
                        <div class="relative border rounded-xl p-5 flex flex-col items-center text-center border-[#dedee5]">
                            @if($isOwned)
                                <span class="absolute top-3 right-3 flex items-center justify-center h-5 w-5 rounded-full bg-[var(--ll-blue)] text-white">
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
                            <div class="mt-4 w-full">
                                @if($isOwned)
                                    <a href="{{ route('inventory') }}" wire:navigate
                                        class="inline-flex items-center justify-center gap-1 w-full py-2.5 bg-gray-200 text-[#686b82] hover:bg-gray-300 font-medium rounded-[8px] text-sm transition-colors">
                                        <x-icon name="check" class="h-4 w-4 mr-1" /> {{ 'เป็นเจ้าของแล้ว' }}
                                    </a>
                                @else
                                    <button @click="confirmPurchase({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->type }}', '{{ asset($item->value) }}')" class="w-full py-2.5 bg-[var(--ll-blue)] text-white hover:bg-[var(--ll-blue-dark)] font-medium rounded-[8px] text-sm transition-colors cursor-pointer flex justify-center items-center gap-2 {{ $coins < $item->price ? 'opacity-70' : '' }}">
                                        <x-icon name="star-solid" class="text-white h-4 w-4 shrink-0" />
                                        <span class="font-bold">{{ $item->price }}</span>
                                    </button>
                                @endif
                            </div>
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
                    <div class="mx-auto flex flex-col items-center justify-center p-4 bg-[var(--ll-blue-faint)] rounded-[8px] mb-6 min-h-[120px] border border-[#dedee5]">

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
                        <span class="mt-2 flex items-center justify-center gap-1 text-[var(--ll-blue)] font-bold">
                            {{ 'ในราคา' }} <x-icon name="star-solid" class="text-amber-500 h-4 w-4 shrink-0" /> <span x-text="selectedItemPrice"></span> {{ 'เหรียญ?' }}
                        </span>
                    </p>

                    <div class="flex flex-col gap-3">
                        <button type="button" @click="$wire.purchase(selectedItemId); showModal = false"
                            class="w-full py-2.5 bg-[var(--ll-blue)] text-white hover:bg-[var(--ll-blue-dark)] font-bold rounded-[8px] text-sm transition-all">
                            <x-icon name="shopping-bag" class="h-4 w-4 mr-1" /> {{ 'ยืนยันการซื้อ' }}
                        </button>
                        <button type="button" @click="showModal = false"
                            class="w-full py-2.5 text-sm font-medium text-[#686b82] border border-[#dedee5] hover:bg-[var(--ll-blue-faint)] rounded-[8px] transition-colors">
                            <x-icon name="x-mark" class="h-4 w-4 mr-1" /> {{ 'ยกเลิก' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
