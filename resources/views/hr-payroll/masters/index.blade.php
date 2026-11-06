@extends('layouts.admin')
@section('title',$title)
@section('content')
<div class="container-fluid">
<div class="d-flex justify-content-between align-items-center mb-3">
<div><h4 class="mb-1">{{ $title }}</h4><small class="text-muted">HR Payroll / Attendance</small></div>
<a href="{{ route('hr-payroll.masters.create',$category) }}" class="btn btn-danger"><i class="bi bi-plus-lg"></i> Add {{ $category==='statuses'?'Status':'Record' }}</a>
</div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card shadow-sm"><div class="card-body">
<form method="GET" class="row g-2 mb-3">
<div class="col-md-4"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or code"></div>
<div class="col-auto"><button class="btn btn-outline-danger"><i class="bi bi-search"></i> Search</button></div>
</form>
<div class="table-responsive"><table class="table table-bordered table-striped table-hover align-middle">
<thead class="table-danger"><tr><th>Name</th><th>Code</th>
@if($table==='hr_attendance_statuses')
<th>Count As</th><th>Extra Deduction</th><th>BG Colour</th><th>Font Colour</th><th>Document Required</th><th>Monthly Limit</th><th>Quarterly Limit</th><th>Yearly Limit</th>
@elseif($table==='hr_shift_definitions')
<th>Start</th><th>End</th><th>Grace (minutes)</th>
@else
<th>Description</th>
@endif
<th>Active</th><th>Action</th></tr></thead><tbody>
@forelse($items as $item)
<tr><td>{{ $item->name }}</td><td>{{ $item->code??'—' }}</td>
@if($table==='hr_attendance_statuses')
<td>{{ ucfirst(str_replace('_',' ',$item->count_as)) }}</td><td>{{ $item->extra_deduction_days }}</td>
<td>@if($item->bg_colour)<span class="badge" style="background:{{ $item->bg_colour }};color:#000">{{ $item->bg_colour }}</span>@else — @endif</td>
<td>{{ $item->font_colour??'—' }}</td><td>{{ $item->document_required?'YES':'NO' }}</td>
<td>{{ $item->monthly_request_limit??'—' }}</td><td>{{ $item->quarterly_request_limit??'—' }}</td><td>{{ $item->yearly_request_limit??'—' }}</td>
@elseif($table==='hr_shift_definitions')
<td>{{ substr($item->start_time,0,5) }}</td><td>{{ substr($item->end_time,0,5) }}</td><td>{{ $item->grace_minutes }}</td>
@else
<td>{{ \Illuminate\Support\Str::limit($item->description,80) }}</td>
@endif
<td>{{ $item->is_active?'YES':'NO' }}</td>
<td><div class="d-flex gap-1">
<a class="btn btn-sm btn-outline-primary" href="{{ route('hr-payroll.masters.edit',[$category,$item->id]) }}">Edit</a>
@if($item->is_active)
<form method="POST" action="{{ route('hr-payroll.masters.destroy',[$category,$item->id]) }}" onsubmit="return confirm('Deactivate this record?')">@csrf @method('DELETE')
<button class="btn btn-sm btn-outline-danger">Deactivate</button></form>
@endif
</div></td></tr>
@empty
<tr><td colspan="{{ $table==='hr_attendance_statuses'?12:($table==='hr_shift_definitions'?7:5) }}" class="text-center">No records found.</td></tr>
@endforelse
</tbody></table></div>{{ $items->links() }}
</div></div></div>
@endsection
