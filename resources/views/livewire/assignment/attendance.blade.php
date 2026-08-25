<div class="">
    @if($classroom->canManageClassroom(auth()->user()) || auth()->user()->isAdmin())
        {{-- ──────────────────────────────────────────────
        Teacher: Attendance Session Controls
        ────────────────────────────────────────────── --}}
        <div>
            <div class="p-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">เซสชันเช็คชื่อ</h3>
                    @if($session?->is_active)
                        <span class="flex items-center text-xs font-medium text-green-600 bg-green-50 px-2.5 py-1 rounded-full">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                            ใช้งานอยู่
                        </span>
                    @else
                        <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">ไม่ได้ใช้งาน</span>
                    @endif
                </div>
            </div>

            <div class="p-5">
                @if($session?->is_active)
                    {{-- Active session: show rotating code --}}
                    @php
                        $codeSecondsLeft = (int) max(0, \App\Models\AttendanceSession::CODE_VALIDITY_SECONDS - ($session->code_rotated_at?->diffInSeconds(now()) ?? 0));
                    @endphp
                    <div
                        wire:poll.1s="rotateCode"
                        wire:key="attendance-code-{{ $session->current_code }}"
                    >
                        <div class="text-center py-6">
                            {{-- <p class="text-md text-gray-500 mb-2">รหัสปัจจุบัน</p> --}}
                            <div class="text-6xl font-mono font-bold tracking-[0.3em] text-[#101114] mb-3">
                                {{ $session->current_code }}
                            </div>
                            <p class="text-md text-gray-400">
                                <x-icon name="arrow-path" class="h-4 w-4 mr-1" />เปลี่ยนรหัสใน {{ $codeSecondsLeft }} วินาที
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

            {{-- ข้อมูลงาน (Assignment info) --}}
            <div class="border-t border-[#dedee5] p-6">
                <h4 class="text-sm font-bold text-[#101114] mb-3">{{ 'ข้อมูลงาน' }}</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="rounded-xl border border-[#dedee5] bg-[#f9f9fb] p-3.5">
                        <span class="flex items-center gap-1.5 text-sm text-[#686b82] mb-1 font-medium">
                            <x-icon name="academic-cap" class="h-4 w-4 text-[#9497a9]" />{{ 'คะแนนเช็คชื่อ' }}
                        </span>
                        <p class="text-lg font-bold text-[#101114]">{{ $assignment->max_score }}</p>
                    </div>
                    <div class="rounded-xl border border-[#dedee5] bg-[#f9f9fb] p-3.5">
                        <span class="flex items-center gap-1.5 text-sm text-[#686b82] mb-1 font-medium">
                            <x-icon name="bolt" class="h-4 w-4 text-blue-600" />{{ 'รางวัล EXP' }}
                        </span>
                        <p class="text-lg font-bold text-blue-700">{{ $assignment->exp_reward }}</p>
                    </div>
                    <div class="rounded-xl border border-[#dedee5] bg-[#f9f9fb] p-3.5">
                        <span class="flex items-center gap-1.5 text-sm text-[#686b82] mb-1 font-medium">
                            <x-icon name="star-solid" class="h-4 w-4 text-amber-500" />{{ 'รางวัลเหรียญ' }}
                        </span>
                        <p class="text-lg font-bold text-amber-600">{{ $assignment->coin_reward }}</p>
                    </div>
                </div>
            </div>

            {{-- Checked-in students list --}}
            @if($this->checkedInStudents->isNotEmpty())
                <div class="border-t border-[#dedee5] p-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">เช็คชื่อแล้ว ({{ $this->checkedInStudents->count() }})</h4>
                    <div class="space-y-2.5">
                        @foreach($this->checkedInStudents as $sub)
                            @php
                                $isLate = str_contains($sub->content ?? '', 'เช็คชื่อสาย');
                            @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl border border-[#dedee5] bg-[#f9f9fb]" wire:key="checkin-{{ $sub->id }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img src="{{ $sub->user->avatar_url }}" class="w-8 h-8 rounded-full shrink-0">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-[#101114] truncate">{{ $sub->user->name }}</p>
                                        <p class="text-xs text-[#9497a9]">{{ $sub->turned_in_at?->translatedFormat('H:i น.') }}</p>
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    @if($isLate)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 flex items-center gap-1">
                                            <x-icon name="clock" class="h-3.5 w-3.5" />{{ 'เช็คชื่อสาย' }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 flex items-center gap-1">
                                            <x-icon name="check-circle" class="h-3.5 w-3.5" />{{ 'ตรงเวลา' }}
                                        </span>
                                    @endif
                                </div>
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
        <div>
            <div class="p-5">
                @if($alreadyCheckedIn)
                    {{-- Already checked in --}}
                    @php
                        $userSub = $assignment->submissionFor(auth()->user());
                        $isLateCheckin = str_contains($userSub?->content ?? '', 'เช็คชื่อสาย');
                    @endphp
                    <div class="text-center py-6">
                        <div class="w-16 h-16 rounded-full {{ $isLateCheckin ? 'bg-amber-100 text-amber-600' : 'bg-green-100 text-green-500' }} flex items-center justify-center mx-auto mb-3">
                            <x-icon :name="$isLateCheckin ? 'clock' : 'check'" class="h-8 w-8" />
                        </div>
                        <p class="text-base font-bold {{ $isLateCheckin ? 'text-amber-800' : 'text-green-700' }}">
                            {{ $isLateCheckin ? 'เช็คชื่อเรียบร้อยแล้ว (เช็คชื่อสาย)' : 'เช็คชื่อเรียบร้อยแล้ว (ตรงเวลา)' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            บันทึกการเช็คชื่อเวลา {{ $userSub?->turned_in_at?->translatedFormat('H:i:s น.') }}
                        </p>
                    </div>
                @elseif($session?->is_active)
                    {{-- Session active: show code entry --}}
                    <div wire:poll.5s class="text-center py-4">
                        <p class="text-sm text-gray-500 mb-4">ใส่รหัสที่ครูแสดงบนหน้าจอ</p>
                        <div class="max-w-xs mx-auto">
                            <input wire:model="enteredCode" type="text" maxlength="6"
                                class="w-full text-center text-3xl font-mono tracking-[0.3em] border border-[#dedee5] rounded-lg px-3 py-4 focus:ring-1 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="000000" autofocus>

                            @if(session('attendance_error'))
                                <p class="mt-2 text-sm text-red-500">
                                    <x-icon name="exclamation-circle" class="h-4 w-4 mr-1" />{{ session('attendance_error') }}
                                </p>
                            @endif

                            <button wire:click="checkin"
                                class="w-full mt-4 py-2.5 bg-amber-500 hover:bg-amber-600 active:scale-[0.98] text-white text-sm font-bold rounded-lg transition-all cursor-pointer shadow-sm">
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
                    @if($assignment->allow_late_submission)
                        <div class="text-center py-6">
                            <div class="w-14 h-14 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-3">
                                <x-icon name="clock" class="h-7 w-7" />
                            </div>
                            <h4 class="text-base font-bold text-[#101114] mb-1">เซสชันเช็คชื่อปิดแล้ว</h4>
                            <p class="text-sm text-[#686b82] max-w-sm mx-auto mb-5">
                                คุณไม่ได้เช็คชื่อในช่วงเปิดเซสชันในห้องเรียน สามารถกดเช็คชื่อสายด้วยตนเองได้
                            </p>

                            @if(session('attendance_error'))
                                <p class="mb-3 text-sm text-red-500">
                                    <x-icon name="exclamation-circle" class="h-4 w-4 mr-1" />{{ session('attendance_error') }}
                                </p>
                            @endif

                            <button wire:click="checkinLate"
                                class="inline-flex items-center justify-center px-6 py-2.5 bg-amber-500 hover:bg-amber-600 active:scale-[0.98] text-white text-sm font-bold rounded-lg transition-all cursor-pointer shadow-sm">
                                <span wire:loading.remove wire:target="checkinLate">
                                    <x-icon name="clock" class="h-4 w-4 mr-1.5" />เช็คชื่อสาย (เช็คชื่อด้วยตนเอง)
                                </span>
                                <span wire:loading wire:target="checkinLate">
                                    <x-icon name="spinner" class="h-4 w-4 mr-1.5 animate-spin" />กำลังบันทึก...
                                </span>
                            </button>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <x-icon name="lock" class="h-9 w-9 text-gray-300 mb-3 mx-auto" />
                            <p class="text-gray-700 font-medium">ปิดรับการเช็คชื่อแล้ว</p>
                            <p class="text-gray-400 text-xs mt-1">ไม่อนุญาตให้เช็คชื่อย้อนหลัง</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endif
</div>