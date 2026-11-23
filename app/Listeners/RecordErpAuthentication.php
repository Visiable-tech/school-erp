<?php

namespace App\Listeners;

use App\Models\ErpActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Log;

class RecordErpAuthentication
{
    public function handle(object $event): void
    {
        // Only log the web guard; student Sanctum authentication remains separate.
        if (!in_array($event->guard ?? null, ['web'], true)) return;
        $user = $event->user ?? null;
        $kind = $event instanceof Login ? 'login' :
            ($event instanceof Logout ? 'logout' :
            ($event instanceof Failed ? 'failed_login' : null));
        if (!$kind) return;
        // Failed attempts with no resolved user have no safe school scope, so omit them.
        if (!$user) return;
        try {
            ErpActivityLog::create([
                'school_id' => $user->school_id ?? null,
                'user_id' => $user->getAuthIdentifier(),
                'event' => $kind,
                'module' => 'authentication',
                'description' => $kind,
                'ip_address' => request()->ip(),
                'user_agent' => substr((string)request()->userAgent(), 0, 500),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('ERP authentication audit failed', ['error' => $e->getMessage()]);
        }
    }
}
