@extends('layouts.admin')
@section('title','Student Remarks')
@section('content')
<div class="container-fluid"><h4>Student Remarks / My Remarks</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('teacher.communication.remarks.store') }}">@csrf
<div class="card mb-3"><div class="card-body"><div class="row g-3">
<div class="col-md-5"><label class="form-label">Student</label><select name="student_enrollment_id" class="form-select" required><option value="">Select assigned student</option>
@foreach($students as $enrollment)<option value="{{ $enrollment->id }}">{{ $enrollment->student?->student_name ?? 'Student' }} ({{ $enrollment->student?->admission_no ?? $enrollment->id }})</option>@endforeach
</select></div>
<div class="col-md-4"><label class="form-label">Category</label><select name="category" class="form-select"><option value="academic">Academic</option><option value="behavior">Behavior</option><option value="appreciation">Appreciation</option><option value="other">Other</option></select></div>
<div class="col-md-3"><label class="form-label">Date</label><input type="date" name="remark_date" value="{{ date('Y-m-d') }}" class="form-control" required></div>
<div class="col-12"><label class="form-label">Remark</label><textarea name="remark" class="form-control" rows="3" required>{{ old('remark') }}</textarea></div>
</div><button class="btn btn-primary mt-3">Save Remark</button></div></div></form>
<div class="table-responsive"><table class="table table-bordered table-striped table-hover align-middle"><thead><tr><th>Date</th><th>Student</th><th>Category</th><th>Remark</th></tr></thead><tbody>
@forelse($remarks as $remark)
<tr><td>{{ $remark->remark_date }}</td><td>{{ $students->firstWhere('id',$remark->student_enrollment_id)?->student?->student_name ?? 'Student' }}</td><td>{{ ucfirst($remark->category) }}</td><td>{{ $remark->remark }}</td></tr>
@empty
<tr><td colspan="4" class="text-center">No remarks found.</td></tr>
@endforelse
</tbody></table></div>
{{ $remarks->links() }}
</div>
@endsection
