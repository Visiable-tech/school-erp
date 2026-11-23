<?php

namespace App\Http\Middleware;

use App\Models\ErpActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackErpActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('ERP Activity Middleware Executed', [
            'user_id' => auth()->id(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        $response = $next($request);
        $user = $request->user('web');
        if (!$user || $request->is('user-management/activity-logs*')) return $response;

        $method = $request->method();
        $status = $response->getStatusCode();
        // Exclude assets, background polling and read-only GETs that aren't page views.
        $route = $request->route()?->getName();
        $isPageView = $method === 'GET' && $status === 200 &&
            !$request->expectsJson() && !$request->ajax();
        $isWrite = in_array($method, ['POST','PUT','PATCH','DELETE'], true) && $status < 400;
        if (!$isPageView && !$isWrite) return $response;

        $event = $isPageView ? 'view' : match ($method) {
            'POST' => 'submit', 'PUT', 'PATCH' => 'update_request', 'DELETE' => 'delete_request'
        };
        $module = $route ? explode('.', $route)[0] : explode('/', trim($request->path(), '/'))[0];
        try {
            ErpActivityLog::create([
                'school_id' => $user->school_id ?? null,
                'user_id' => $user->getAuthIdentifier(),
                'event' => $event,
                'module' => substr($module ?: 'erp', 0, 100),
                'description' => substr($route ?: $request->path(), 0, 500),
                'route_name' => $route,
                'http_method' => $method,
                'path' => substr($request->path(), 0, 500),
                'http_status' => $status,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string)$request->userAgent(), 0, 500),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('ERP activity log failed', ['error' => $e->getMessage()]);
        }
        return $response;
    }
}
