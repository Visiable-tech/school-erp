<?php

namespace App\Http\Controllers;

use App\Models\ErpActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserActivityLogController extends Controller
{
    private function authorizeViewer(Request $request): void
    {
        $user = $request->user('web');
        abort_unless($user && $user->hasAnyRole(['Super Admin', 'Admin']), 403);
        abort_unless(!is_null($user->school_id), 403, 'School context required');
    }

    private function filtered(Request $request)
    {
        $request->validate([
            'from' => ['nullable','date_format:Y-m-d'],
            'to' => ['nullable','date_format:Y-m-d','after_or_equal:from'],
            'user_id' => ['nullable','integer'],
            'event' => ['nullable',Rule::in(['login','logout','failed_login','view','submit','update_request','delete_request'])],
            'module' => ['nullable','string','max:100'],
        ]);
        $schoolId = $request->user('web')->school_id;
        $query = ErpActivityLog::query()->where('school_id', $schoolId)->with('user:id,name,email');
        $from = $request->input('from', now()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $query->where('created_at', '>=', Carbon::parse($from)->startOfDay())
            ->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        if ($request->filled('user_id')) $query->where('user_id', $request->integer('user_id'));
        if ($request->filled('event')) $query->where('event', $request->event);
        if ($request->filled('module')) $query->where('module', $request->module);
        return $query;
    }

    public function index(Request $request)
    {
        $this->authorizeViewer($request);
        $query = $this->filtered($request);
        $summary = (clone $query)->selectRaw('COUNT(*) as total, COUNT(DISTINCT user_id) as active_users, SUM(CASE WHEN event = ? THEN 1 ELSE 0 END) as logins', ['login'])->first();
        $logs = $query->latest('created_at')->latest('id')->paginate(30)->withQueryString();
        $users = User::where('school_id', $request->user('web')->school_id)->orderBy('name')->get(['id','name','email']);
        $modules = ErpActivityLog::where('school_id', $request->user('web')->school_id)->whereNotNull('module')->distinct()->orderBy('module')->pluck('module');
        return view('user-activity-logs.index', compact('logs','users','modules','summary'));
    }

    public function export(Request $request)
    {
        $this->authorizeViewer($request);
        $query = $this->filtered($request)->orderBy('id');
        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date/Time','User ID','Name','Email','Event','Module','Route','Method','Path','Status','IP']);
            foreach ($query->cursor() as $log) {
                // Guard against spreadsheet formula injection.
                $safe = static function ($v) {
                    $v = (string)($v ?? '');
                    return preg_match('/^[\s]*[=+\-@]/u', $v) ? "'".$v : $v;
                };
                fputcsv($out, array_map($safe, [
                    $log->created_at, $log->user_id, $log->user?->name, $log->user?->email,
                    $log->event, $log->module, $log->route_name, $log->http_method,
                    $log->path, $log->http_status, $log->ip_address,
                ]));
            }
            fclose($out);
        }, 'erp-activity-'.now()->format('Ymd-His').'.csv', ['Content-Type'=>'text/csv; charset=UTF-8']);
    }
}
