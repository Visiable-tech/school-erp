@extends('layouts.admin')
@section('title','Daily Staff Attendance')
@section('content')
<div class="container-fluid"><h4>Daily Staff Attendance</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="GET" class="row g-2 mb-3">
<div class="col-auto"><input type="date" name="date" value="{{ $date }}" class="form-control"></div>
<div class="col-auto"><button class="btn btn-primary">Show</button></div>
</form>
<div class="table-responsive"><table class="table table-bordered table-striped align-middle">
<thead><tr><th>Teacher</th><th>Date</th><th>Status</th><th>Note</th>@if($isManager)<th>Update</th>@endif</tr></thead><tbody>
@forelse($users as $user)
@php $rec=$records->get($user->id); @endphp
<tr>
<td>{{ $user->name }}</td><td>{{ $date }}</td>
@if($isManager)
<td colspan="3">
<form method="POST" action="{{ route('teacher.staff-attendance.daily.mark') }}" class="row g-2 align-items-center">
@csrf
<input type="hidden" name="user_id" value="{{ $user->id }}">
<input type="hidden" name="attendance_date" value="{{ $date }}">
<div class="col-md-3"><select name="status" class="form-select" required>
@foreach(['present','absent','late','half_day','on_leave'] as $status)
<option value="{{ $status }}" @selected(($rec->status??'present')===$status)>{{ ucwords(str_replace('_',' ',$status)) }}</option>
@endforeach
</select></div>
<div class="col-md-6"><input name="note" class="form-control" maxlength="500" value="{{ $rec->note??'' }}" placeholder="Note (optional)"></div>
<div class="col-md-3"><button class="btn btn-sm btn-primary">Save</button></div>
</form></td>
@else
<td>{{ $rec?->status ? ucwords(str_replace('_',' ',$rec->status)) : 'Not marked' }}</td>
<td>{{ $rec?->note??'—' }}</td>
@endif
</tr>
@empty
<tr><td colspan="5" class="text-center">No teachers found.</td></tr>
@endforelse
</tbody></table></div></div>
@endsection
