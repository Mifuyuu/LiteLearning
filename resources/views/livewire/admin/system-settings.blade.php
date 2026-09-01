@section('page-title', 'จัดการระบบ')

<div x-data="{ showMaintenanceModal: false }">
    <div class="bg-white rounded-2xl border-3 border-[#dedee5] shadow-[rgba(0,0,0,0.03)_0px_4px_24px] overflow-hidden">

        {{-- System info --}}
        <div class="p-6 sm:p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-1">สถานะระบบ</h2>
            <p class="text-sm text-gray-500 mb-4">ข้อมูลสภาพแวดล้อมและการใช้งานของระบบ</p>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="rounded-xl border border-gray-100 p-3">
                    <p class="text-xs text-gray-400">Laravel</p>
                    <p class="text-sm font-bold text-gray-800">{{ $laravelVersion }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-3">
                    <p class="text-xs text-gray-400">PHP</p>
                    <p class="text-sm font-bold text-gray-800">{{ $phpVersion }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-3">
                    <p class="text-xs text-gray-400">Environment</p>
                    <p class="text-sm font-bold text-gray-800">{{ $environment }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-3">
                    <p class="text-xs text-gray-400">พื้นที่จัดเก็บ</p>
                    <p class="text-sm font-bold text-gray-800">{{ number_format($storageUsage['used_bytes'] / 1073741824, 2) }} GB / 1 TB</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-3">
                    <p class="text-xs text-gray-400">ฐานข้อมูล</p>
                    <p class="text-sm font-bold text-gray-800">{{ $dbDriver }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-3">
                    <p class="text-xs text-gray-400">Cache</p>
                    <p class="text-sm font-bold text-gray-800">{{ $cacheDriver }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-3">
                    <p class="text-xs text-gray-400">Queue</p>
                    <p class="text-sm font-bold text-gray-800">{{ $queueDriver }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-3">
                    <p class="text-xs text-gray-400">งานที่ล้มเหลว</p>
                    <p class="text-sm font-bold {{ $failedJobsCount > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $failedJobsCount }}</p>
                </div>
            </div>
        </div>

        {{-- Database Export / Backup --}}
        <div class="border-t border-[#dedee5] mx-6 sm:mx-8"></div>
        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">ส่งออกฐานข้อมูล (Database Export)</h2>
                    <p class="text-sm text-gray-500 mt-0.5">
                        สร้างและดาวน์โหลดไฟล์ SQL Dump สำหรับการสำรองข้อมูล (Backup) หรือย้ายฐานข้อมูลของระบบ
                    </p>
                    <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-medium border border-blue-100">
                            <x-icon name="circle-solid" class="h-1.5 w-1.5 text-blue-600" />
                            ไดรเวอร์: <strong class="font-bold">{{ strtoupper($dbDriver) }}</strong>
                        </span>
                        <span>• รูปแบบไฟล์: <strong class="text-gray-700 font-semibold">.sql (UTF-8)</strong></span>
                    </div>
                </div>
                <div class="w-full sm:w-auto">
                    <button wire:click="exportDatabase" wire:loading.attr="disabled"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 active:scale-[0.98] transition-all shrink-0 cursor-pointer shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <x-icon name="arrow-down-tray" class="h-4 w-4" wire:loading.remove wire:target="exportDatabase" />
                        <x-icon name="spinner" class="h-4 w-4 animate-spin" wire:loading wire:target="exportDatabase" />
                        <span wire:loading.remove wire:target="exportDatabase">ดาวน์โหลด SQL Backup</span>
                        <span wire:loading wire:target="exportDatabase">กำลังส่งออกฐานข้อมูล...</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="border-t border-[#dedee5] mx-6 sm:mx-8"></div>
        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">โหมดปิดปรับปรุงระบบ</h2>
                    <p class="text-sm text-gray-500 mt-0.5">
                        เมื่อเปิดใช้งาน ผู้ใช้ทุกคนยกเว้นคุณจะไม่สามารถเข้าใช้งานระบบได้ชั่วคราว
                    </p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-2
                        {{ $isDownForMaintenance ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-800' }}">
                        {{ $isDownForMaintenance ? 'กำลังปิดปรับปรุง' : 'ระบบทำงานปกติ' }}
                    </span>
                </div>
                @if($isDownForMaintenance)
                    <button wire:click="disableMaintenance" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-bold transition-colors shrink-0 cursor-pointer">
                        เปิดใช้งานระบบ
                    </button>
                @else
                    <button type="button" @click="showMaintenanceModal = true"
                        class="bg-red-500 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-bold transition-colors shrink-0">
                        ปิดปรับปรุงระบบ
                    </button>
                @endif
            </div>
        </div>

        {{-- Feature flags --}}
        <div class="border-t border-[#dedee5] mx-6 sm:mx-8"></div>
        <div class="p-6 sm:p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-1">ฟีเจอร์ของระบบ</h2>
            <p class="text-sm text-gray-500 mb-4">เปิด/ปิดความสามารถของระบบโดยไม่ต้องแก้ไขโค้ด</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3">
                <label class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">เปิดรับสมัครสมาชิกใหม่</span>
                    <input type="checkbox" wire:click="toggleFlag('registrationEnabled')" @checked($registrationEnabled)
                        class="toggle toggle-sm toggle-primary">
                </label>
                <label class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">เปิดร้านค้า (ซื้อไอเทม)</span>
                    <input type="checkbox" wire:click="toggleFlag('storeEnabled')" @checked($storeEnabled)
                        class="toggle toggle-sm toggle-primary">
                </label>
                <label class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">เปิดให้เข้าร่วมห้องเรียนใหม่</span>
                    <input type="checkbox" wire:click="toggleFlag('classroomJoinEnabled')" @checked($classroomJoinEnabled)
                        class="toggle toggle-sm toggle-primary">
                </label>
                <label class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">เปิดระบบรายงานปัญหา</span>
                    <input type="checkbox" wire:click="toggleFlag('bugReportEnabled')" @checked($bugReportEnabled)
                        class="toggle toggle-sm toggle-primary">
                </label>
            </div>
        </div>

        {{-- Game / scoring config --}}
        <div class="border-t border-[#dedee5] mx-6 sm:mx-8"></div>
        <div class="p-6 sm:p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-1">ค่าคอนฟิกเกม/คะแนน</h2>
            <p class="text-sm text-gray-500 mb-4">ตัวเลขที่ใช้คำนวณเลเวล เหรียญ และขีดจำกัดการใช้งาน</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">XP ต่อเลเวล (ตัวคูณ)</label>
                    <input type="number" wire:model="xpPerLevelMultiplier" min="1"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500">
                    @error('xpPerLevelMultiplier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">เหรียญเมื่อเช็คชื่อ</label>
                    <input type="number" wire:model="attendanceCoinReward" min="0"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500">
                    @error('attendanceCoinReward') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">XP เมื่อเช็คชื่อ</label>
                    <input type="number" wire:model="attendanceXpReward" min="0"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500">
                    @error('attendanceXpReward') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ขีดจำกัดรายงานปัญหา (ครั้ง/10 นาที)</label>
                    <input type="number" wire:model="bugReportRateLimit" min="1"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500">
                    @error('bugReportRateLimit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ขีดจำกัดเข้าร่วมห้องเรียน (ครั้ง/นาที)</label>
                    <input type="number" wire:model="classroomJoinRateLimit" min="1"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500">
                    @error('classroomJoinRateLimit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <button wire:click="saveGameConfig" disabled
                    wire:target="xpPerLevelMultiplier,attendanceCoinReward,attendanceXpReward,bugReportRateLimit,classroomJoinRateLimit"
                    wire:dirty.attr.remove="disabled"
                    wire:dirty.class="bg-blue-600 hover:bg-blue-700 text-white cursor-pointer"
                    wire:dirty.class.remove="bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed"
                    class="bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed px-6 py-2 text-sm font-bold rounded-lg transition-colors">
                    บันทึกค่าคอนฟิก
                </button>
            </div>
        </div>

    </div>

    <template x-teleport="body">
        <x-confirm-modal show="showMaintenanceModal" cancel="showMaintenanceModal = false" heading="ปิดปรับปรุงระบบ" icon="power"
            message="ผู้ใช้ทุกคนยกเว้นคุณจะไม่สามารถเข้าใช้งานระบบได้ จนกว่าคุณจะเปิดใช้งานอีกครั้ง ต้องการดำเนินการต่อหรือไม่?">
            <button type="button" @click="showMaintenanceModal = false" wire:click="enableMaintenance"
                class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                ยืนยันปิดปรับปรุง
            </button>
        </x-confirm-modal>
    </template>
</div>
