@extends('layouts.admin')
@section('title',$kind==='leave'?'Leave Requests':'Attendance Change Requests')
@section('content')
<div class="container-fluid"><h4>{{ $kind==='leave'?'Leave Requests':'Attendance Change Requests' }}</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card mb-4"><div class="card-header">Submit {{ $kind==='leave'?'Leave':'Attendance Correction' }} Request</div>
<div class="card-body">
<form method="POST" action="{{ $kind==='leave'?route('teacher.staff-attendance.leaves.store'):route('teacher.staff-attendance.changes.store') }}">
@csrf
<div class="row g-3">
@if($kind==='leave')
<div class="col-md-3"><label class="form-label">From</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required></div>
<div class="col-md-3"><label class="form-label">To</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required></div>
<div class="col-md-3"><label class="form-label">Leave Type</label><select name="leave_type" class="form-select">
@foreach(['casual','sick','earned','unpaid','other'] as $t)<option value="{{ $t }}">{{ ucfirst($t) }}</option>@endforeach
</select></div>
@else
<div class="col-md-4"><label class="form-label">Attendance Date</label><input type="date" name="attendance_date" max="{{ date('Y-m-d') }}" class="form-control" value="{{ old('attendance_date') }}" required></div>
<div class="col-md-4"><label class="form-label">Requested Status</label><select name="requested_status" class="form-select">
@foreach(['present','absent','late','half_day','on_leave'] as $t)<option value="{{ $t }}">{{ ucwords(str_replace('_',' ',$t)) }}</option>@endforeach
</select></div>
@endif
<div class="col-12"><label class="form-label">Reason</label><textarea name="reason" class="form-control" rows="2" required>{{ old('reason') }}</textarea></div>
<div class="col-12"><button class="btn btn-primary">Submit Request</button></div>
</div></form></div></div>
<div class="table-responsive"><table class="table table-bordered table-striped align-middle">
<thead><tr><th>ID</th><th>User ID</th><th>Dates</th><th>Type / Requested Status</th><th>Reason</th><th>Status</th><th>Review Note</th>@hasanyrole('Admin|Super Admin')<th>Review</th>@endhasanyrole</tr></thead><tbody>
@forelse($requests as $item)
<tr><td>{{ $item->id }}</td><td>{{ $item->user_id }}</td>
<td>{{ $kind==='leave'?$item->start_date.' to '.$item->end_date:$item->attendance_date }}</td>
<td>{{ $kind==='leave'?$item->leave_type:$item->requested_status }}</td>
<td>{{ $item->reason }}</td><td>{{ ucfirst($item->status) }}</td><td>{{ $item->review_note??'—' }}</td>
@hasanyrole('Admin|Super Admin')
<td>
@if($item->status==='pending' && $item->user_id!==auth()->id())
<form method="POST" action="{{ route('teacher.staff-attendance.review',[$kind,$item->id]) }}">
@csrf
<input name="review_note" class="form-control form-control-sm mb-1" maxlength="500" placeholder="Review note">
<div class="d-flex gap-1"><button name="decision" value="approved" class="btn btn-sm btn-success">Approve</button>
<button name="decision" value="rejected" class="btn btn-sm btn-danger">Reject</button></div>
</form>
@endif
</td>
@endhasanyrole
</tr>
@empty
<tr><td colspan="8" class="text-center">No requests found.</td></tr>
@endforelse
</tbody></table></div>
{{ $requests->links() }}
</div>
@endsection
