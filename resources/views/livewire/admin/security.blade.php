@section('page-title', 'ความปลอดภัยของระบบ')

<div class="space-y-6">
    <div class="flex gap-2 border-b border-gray-200">
        <button type="button" wire:click="$set('tab', 'audit-log')"
            class="px-4 py-2.5 text-sm font-bold border-b-2 transition-colors {{ $tab === 'audit-log' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            บันทึกการใช้งาน
        </button>
        <button type="button" wire:click="$set('tab', 'sessions')"
            class="px-4 py-2.5 text-sm font-bold border-b-2 transition-colors {{ $tab === 'sessions' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            เซสชันที่กำลังใช้งาน
        </button>
    </div>

    @if($tab === 'audit-log')
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto [scrollbar-width:thin]">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 uppercase text-sm font-bold text-gray-500 tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">เวลา</th>
                            <th class="px-6 py-3 text-left">ผู้ดำเนินการ</th>
                            <th class="px-6 py-3 text-left">รายละเอียด</th>
                            <th class="px-6 py-3 text-left">IP</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($auditLogs as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $log->created_at->translatedFormat('j M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $log->user?->name ?? 'ระบบ' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $log->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <x-icon name="shield-check" class="h-9 w-9 mb-3 opacity-20" />
                                        <p>ยังไม่มีบันทึกการใช้งาน</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($auditLogs->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $auditLogs->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto [scrollbar-width:thin]">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 uppercase text-sm font-bold text-gray-500 tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">ผู้ใช้</th>
                            <th class="px-6 py-3 text-left">IP</th>
                            <th class="px-6 py-3 text-left">อุปกรณ์</th>
                            <th class="px-6 py-3 text-left">ใช้งานล่าสุด</th>
                            <th class="px-6 py-3 text-right">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">
                                    {{ $session->user_name }}
                                    @if($session->id === session()->getId())
                                        <span class="text-[9px] px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded-full font-bold ml-1">YOU</span>
                                    @endif
                                    <div class="text-xs font-normal text-gray-500">{{ $session->user_email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">{{ $session->ip_address ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs text-gray-500 truncate max-w-64">{{ $session->user_agent ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ \Illuminate\Support\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($session->id !== session()->getId())
                                        <button type="button" wire:click="revokeSession('{{ $session->id }}')"
                                            wire:confirm="เพิกถอนเซสชันนี้? ผู้ใช้จะถูกออกจากระบบทันที"
                                            class="text-gray-400 hover:text-red-600 transition-colors p-1" title="เพิกถอนเซสชัน">
                                            <x-icon name="x-mark" class="h-4 w-4" />
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <x-icon name="lock" class="h-9 w-9 mb-3 opacity-20" />
                                        <p>ไม่พบเซสชันที่กำลังใช้งาน</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($sessions->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
