<div class="">
    @if($classroom->canManageClassroom(auth()->user()) || auth()->user()->isAdmin())
        {{-- ──────────────────────────────────────────────
        Teacher: Attendance Session Controls
        ────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <x-icon name="clipboard-document-check" class="h-4 w-4 text-amber-500 mr-2" />เซสชันเช็คชื่อ
                    </h3>
                    @if($session?->is_active)
                        <span class="flex items-center text-xs font-medium text-green-600 bg-green-50 px-2.5 py-1 rounded-full">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                            ใช้งานอยู่
                        </span>
                    @else
                        <span
                            class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">                            ไม่ได้ใช้งาน</span>
                    @endif
                </div>
            </div>

            <div class="p-5">
                @if($session?->is_active)
                    {{-- Active session: show rotating code --}}
                    <div wire:poll.10s="rotateCode">
                        <div class="text-center py-6">
                            <p class="text-sm text-gray-500 mb-2">รหัสปัจจุบัน</p>
                            <div class="text-6xl font-mono font-bold tracking-[0.3em] text-amber-600 mb-3">
                                {{ $session->current_code }}
                            </div>
                            <p class="text-xs text-gray-400">
                                <x-icon name="arrow-path" class="h-4 w-4 mr-1" />เปลี่ยนรหัสทุก 10 วินาที
                            </p>
                        </div>

                        <button wire:click="stopSession"
                            class="w-full mt-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg border border-red-200 transition-colors">
                            <x-icon name="stop-circle" class="h-4 w-4 mr-1.5" />หยุดเช็คชื่อ
                        </button>
                    </div>
                @else
                    {{-- Inactive: show start button --}}
                    <div class="text-center py-6">
                        <x-icon name="qr-code" class="h-12 w-12 text-gray-300 mb-3" />
                        <p class="text-gray-500 text-sm">เริ่มเซสชันเพื่อแสดงรหัสเช็คชื่อ</p>
                    </div>
                    <button wire:click="startSession"
                        class="w-full mt-2 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                        <x-icon name="play-circle" class="h-4 w-4 mr-1.5" />เริ่มเช็คชื่อ
                    </button>
                @endif
            </div>

            {{-- Checked-in students list --}}
            @if($this->checkedInStudents->isNotEmpty())
                <div class="border-t border-gray-200 p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">
                        <x-icon name="check-circle" class="h-4 w-4 text-green-500 mr-1" />
                        เช็คชื่อแล้ว ({{ $this->checkedInStudents->count() }})
                    </h4>
                    <div class="space-y-2">
                        @foreach($this->checkedInStudents as $sub)
                            <div class="flex items-center text-sm" wire:key="checkin-{{ $sub->id }}">
                                <img src="{{ $sub->user->avatar_url }}" class="w-7 h-7 rounded-full mr-2">
                                <span class="text-gray-700">{{ $sub->user->name }}</span>
                                <span class="ml-auto text-xs text-gray-400">{{ $sub->turned_in_at?->format('H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    @elseif(auth()->user()->isStudent())
        {{-- ──────────────────────────────────────────────
        Student: Attendance Check-in
        ────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <x-icon name="clipboard-document-check" class="h-4 w-4 text-amber-500 mr-2" />เช็คชื่อ
                </h3>
            </div>

            <div class="p-5">
                @if($alreadyCheckedIn)
                    {{-- Already checked in --}}
                    <div class="text-center py-6">
                        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-3">
                            <x-icon name="check" class="h-6 w-6 text-green-500" />
                        </div>
                        <p class="text-green-700 font-medium">เช็คชื่อเรียบร้อยแล้ว</p>
                        <p class="text-xs text-gray-400 mt-1">บันทึกการเช็คชื่อของคุณแล้ว</p>
                    </div>
                @elseif($session?->is_active)
                    {{-- Session active: show code entry --}}
                    <div wire:poll.5s class="text-center">
                        <p class="text-sm text-gray-500 mb-4">ใส่รหัสที่ครูแสดง</p>
                        <div class="max-w-xs mx-auto">
                            <input wire:model="enteredCode" type="text" maxlength="6"
                                class="w-full text-center text-3xl font-mono tracking-[0.3em] border border-gray-300 rounded-lg px-3 py-4 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="000000" autofocus>

                            @if(session('attendance_error'))
                                <p class="mt-2 text-sm text-red-500">
                                    <x-icon name="exclamation-circle" class="h-4 w-4 mr-1" />{{ session('attendance_error') }}
                                </p>
                            @endif

                            <button wire:click="checkin"
                                class="w-full mt-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                                <span wire:loading.remove wire:target="checkin">
                                    <x-icon name="check" class="h-4 w-4 mr-1.5" />เช็คชื่อ
                                </span>
                                <span wire:loading wire:target="checkin">
                                    <x-icon name="spinner" class="h-4 w-4 mr-1.5 animate-spin" />กำลังตรวจสอบ...
                                </span>
                            </button>
                        </div>
                    </div>
                @else
                    {{-- Session not active --}}
                    <div class="text-center py-6">
                        <x-icon name="clock" class="h-9 w-9 text-gray-300 mb-3" />
                        <p class="text-gray-500 text-sm">รอให้ครูเริ่มเซสชันเช็คชื่อ...</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>