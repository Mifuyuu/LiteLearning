<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Security extends Component
{
    use WithPagination;

    public string $tab = 'audit-log';

    public function revokeSession(string $sessionId): void
    {
        if ($sessionId === session()->getId()) {
            $this->dispatch('notify', message: __('messages.admin.cannot_revoke_own_session'));

            return;
        }

        DB::table('sessions')->where('id', $sessionId)->delete();

        AuditLog::record('session_revoked', "เพิกถอน session (id: {$sessionId})");

        $this->dispatch('notify', message: __('messages.admin.session_revoked'));
    }

    public function render()
    {
        return view('livewire.admin.security', [
            'auditLogs' => $this->tab === 'audit-log'
                ? AuditLog::with('user')->latest()->paginate(15)
                : null,
            'sessions' => $this->tab === 'sessions'
                ? DB::table('sessions')
                    ->join('users', 'users.id', '=', 'sessions.user_id')
                    ->select('sessions.*', 'users.name as user_name', 'users.email as user_email')
                    ->orderByDesc('sessions.last_activity')
                    ->paginate(15)
                : null,
        ]);
    }
}
