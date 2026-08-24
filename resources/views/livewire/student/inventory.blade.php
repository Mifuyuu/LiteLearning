@section('page-title', 'คลังเก็บของ')

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden min-h-[calc(100vh-3rem)]">
        <div class="relative p-6 lg:p-8">
            <img src="{{ asset('images/Inventory.svg') }}" alt=""
                class="absolute right-4 top-4 hidden h-[calc(100%-2rem)] w-auto select-none object-contain sm:block lg:right-8" />

            <div class="relative max-w-sm">
                <h1 class="text-3xl font-black tracking-tight text-[#101114] sm:text-4xl">{{ 'คลังเก็บของ' }}</h1>
                <p class="mt-2 text-md leading-6 text-[#686b82]">{{ 'จัดการไอเทมที่คุณครอบครอง' }}</p>

                <div class="mt-4 inline-flex items-center gap-2 flex-wrap">
                    <div class="inline-flex items-center space-x-2 bg-gray-100 px-4 py-2 rounded-lg border border-[#dedee5]">
                        <x-icon name="archive-box" class="text-[#686b82] h-5 w-5 shrink-0" />
                        <span class="font-bold text-[#101114]">{{ count($nameColorItems) + count($avatarFrameItems) }}</span>
                        <span class="text-xs text-[#686b82] font-medium ml-1 shrink-0">{{ 'ไอเทมที่มี' }}</span>
                    </div>
                    @if(!empty($badgeItems))
                        <div class="inline-flex items-center space-x-2 bg-gray-100 px-4 py-2 rounded-lg border border-[#dedee5]">
                            <x-icon name="medal" class="text-[#686b82] h-5 w-5 shrink-0" />
                            <span class="font-bold text-[#101114]">{{ $displayedBadgeCount }}/{{ $maxDisplayedBadges }}</span>
                            <span class="text-xs text-[#686b82] font-medium ml-1 shrink-0">{{ 'ตราที่สวมใส่' }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(empty($nameColorItems) && empty($avatarFrameItems) && empty($badgeItems))
            <div class="border-t border-[#dedee5] p-16 text-center">
                <div class="flex justify-center mb-4">
                    <x-icon name="shopping-bag" class="h-16 w-16 text-[#dedee5]" />
                </div>
                <h3 class="text-lg font-bold text-[#101114] mb-2">{{ 'ยังไม่มีไอเทม' }}</h3>
                <p class="text-sm text-[#686b82] mb-6">{{ 'ไปที่ร้านค้าเพื่อซื้อไอเทมตกแต่งกัน!' }}</p>
                <a href="{{ route('store') }}" wire:navigate
                    class="inline-flex items-center gap-2 py-2.5 px-6 bg-(--ll-blue) text-white hover:bg-(--ll-blue-dark) font-medium rounded-lg text-sm transition-colors">
                    <x-icon name="shopping-bag" class="h-4 w-4" />
                    {{ 'ไปที่ร้านค้า' }}
                </a>
            </div>
        @else
            <div class="border-t border-[#dedee5] space-y-8 p-6 lg:p-8">
                @if(!empty($nameColorItems))
                <div>
                    <h2 class="text-xl font-bold text-[#101114] mb-6 flex items-center">{{ 'สีชื่อ' }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($nameColorItems as $item)
                            <div class="relative border rounded-xl p-5 flex flex-col items-center text-center border-[#dedee5]">
                                @if($item['is_active'])
                                    <span class="absolute top-3 right-3 flex items-center justify-center h-5 w-5 rounded-full bg-(--ll-blue) text-white">
                                        <x-icon name="check" class="h-3 w-3" />
                                    </span>
                                @endif
                                <div class="h-16 flex items-center justify-center font-bold text-3xl {{ $item['value'] }}">
                                    {{ 'Aa' }}
                                </div>
                                <h3 class="font-bold text-[#101114] mt-2">{{ $item['name'] }}</h3>
                                <p class="text-xs text-[#686b82] mt-1 h-10 line-clamp-2 px-2">{{ $item['description'] }}</p>
                                <x-inventory-item-actions :item="$item" />
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($avatarFrameItems))
                <div>
                    <h2 class="text-xl font-bold text-[#101114] mb-6 flex items-center">{{ 'กรอบรูปโปรไฟล์' }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($avatarFrameItems as $item)
                            <div class="relative border rounded-xl p-5 flex flex-col items-center text-center border-[#dedee5]">
                                @if($item['is_active'])
                                    <span class="absolute top-3 right-3 flex items-center justify-center h-5 w-5 rounded-full bg-(--ll-blue) text-white">
                                        <x-icon name="check" class="h-3 w-3" />
                                    </span>
                                @endif
                                <div class="h-20 flex items-center justify-center mb-2 mt-4">
                                    <div class="relative flex items-center justify-center">
                                        <img src="{{ auth()->user()->avatar_url }}" class="w-16 h-16 rounded-full object-cover bg-white pointer-events-none border border-[#dedee5]" alt="">
                                        @if(!str_starts_with($item['value'], 'border'))
                                            <img src="{{ asset($item['value']) }}"
                                                 class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[200%] h-[200%] max-w-none pointer-events-none drop-shadow-sm">
                                        @else
                                            <div class="absolute inset-0 rounded-full {{ $item['value'] }} pointer-events-none"></div>
                                        @endif
                                    </div>
                                </div>
                                <h3 class="font-bold text-[#101114] mt-2">{{ $item['name'] }}</h3>
                                <p class="text-xs text-[#686b82] mt-1 h-10 line-clamp-2 px-2">{{ $item['description'] }}</p>
                                <x-inventory-item-actions :item="$item" />
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($badgeItems))
                <div>
                    <h2 class="text-xl font-bold text-[#101114] mb-6 flex items-center">{{ 'ตราความสำเร็จ' }}
                        <span class="ml-2 text-xs font-medium text-[#9497a9]">{{ '(สวมใส่ได้สูงสุด '.$maxDisplayedBadges.' ตรา)' }}</span>
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($badgeItems as $item)
                            <div class="relative border rounded-xl p-5 flex flex-col items-center text-center border-[#dedee5]">
                                @if($item['is_active'])
                                    <span class="absolute top-3 right-3 flex items-center justify-center h-5 w-5 rounded-full bg-(--ll-blue) text-white">
                                        <x-icon name="check" class="h-3 w-3" />
                                    </span>
                                @endif
                                <div class="h-16 flex items-center justify-center">
                                    <img src="{{ asset($item['badge_image'] ?: 'images/achievements/Achievements_Novice.png') }}"
                                        alt="{{ $item['name'] }}" class="h-14 w-14 object-contain">
                                </div>
                                <h3 class="font-bold text-[#101114] mt-2">{{ $item['name'] }}</h3>
                                <p class="text-xs text-[#686b82] mt-1 h-10 line-clamp-2 px-2">{{ $item['description'] }}</p>
                                <x-inventory-badge-actions :item="$item" />
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        @endif
    </div>
</div>
