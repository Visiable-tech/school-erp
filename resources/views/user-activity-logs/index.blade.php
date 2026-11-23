{{-- Replace layouts.app with your actual admin layout if different. --}}
@extends('layouts.admin')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Daily User Activity Tracking</h3>
        <a class="btn btn-outline-success" href="{{ route('user-activity-logs.export', request()->query()) }}">Export CSV</a>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card"><div class="card-body">Activities <h3>{{ number_format($summary->total ?? 0) }}</h3></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body">Active users <h3>{{ number_format($summary->active_users ?? 0) }}</h3></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body">Logins <h3>{{ number_format($summary->logins ?? 0) }}</h3></div></div></div>
    </div>
    <div class="card mb-3"><div class="card-body">
        <form method="GET" action="{{ route('user-activity-logs.index') }}" class="row g-2 align-items-end">
            <div class="col-md-2"><label class="form-label">From</label><input type="date" class="form-control" name="from" value="{{ request('from', now()->toDateString()) }}"></div>
            <div class="col-md-2"><label class="form-label">To</label><input type="date" class="form-control" name="to" value="{{ request('to', now()->toDateString()) }}"></div>
            <div class="col-md-3"><label class="form-label">User</label><select class="form-select" name="user_id"><option value="">All users</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }} ({{ $user->email }})</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Event</label><select class="form-select" name="event"><option value="">All events</option>@foreach(['login','logout','failed_login','view','submit','update_request','delete_request'] as $event)<option value="{{ $event }}" @selected(request('event') === $event)>{{ ucwords(str_replace('_',' ',$event)) }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Module</label><select class="form-select" name="module"><option value="">All modules</option>@foreach($modules as $module)<option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>@endforeach</select></div>
            <div class="col-md-1"><button class="btn btn-primary w-100">Filter</button></div>
        </form>
    </div></div>
    <div class="card"><div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover align-middle">
            <thead class="table-light"><tr><th>Date &amp; Time</th><th>User</th><th>Event</th><th>Module</th><th>Route / Path</th><th>IP</th><th>Status</th></tr></thead>
            <tbody>@forelse($logs as $log)
            <tr><td>{{ $log->created_at?->format('d M Y h:i:s A') }}</td><td>{{ $log->user?->name ?? 'Deleted user' }}<div class="small text-muted">{{ $log->user?->email }}</div></td><td><span class="badge bg-secondary">{{ $log->event }}</span></td><td>{{ $log->module }}</td><td><div>{{ $log->route_name }}</div><small class="text-muted">{{ $log->http_method }} {{ $log->path }}</small></td><td>{{ $log->ip_address }}</td><td>{{ $log->http_status }}</td></tr>
            @empty<tr><td colspan="7" class="text-center">No activity for the selected filters.</td></tr>@endforelse</tbody>
        </table>
        {{ $logs->links() }}
    </div></div>
</div>
@endsection
