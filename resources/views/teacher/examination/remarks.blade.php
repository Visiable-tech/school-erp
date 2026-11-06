@extends('layouts.admin')
@section('title','Term Wise Remarks')
@section('content')
<div class="container-fluid"><h4>Term Wise Remarks</h4>
 @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
 @if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
 <form method="GET" action="{{ route('teacher.examination.remarks') }}" class="row g-2 mb-3"><div class="col-md-7"><select class="form-select" name="assignment_id" required><option value="">Select Assignment</option>@foreach($assignments as $a)<option value="{{ $a->id }}" @selected(request('assignment_id')==$a->id)>{{ $a->academicYear?->name }} — {{ $a->schoolClass?->name }} / {{ $a->section?->name }} / {{ $a->subject?->name ?? 'General' }}</option>@endforeach</select></div><div class="col-md-3"><input class="form-control" name="term" maxlength="80" value="{{ $term }}" required></div><div class="col-md-2"><button class="btn btn-outline-primary">Load Students</button></div></form>
 @if($assignment)
 <form method="POST" action="{{ route('teacher.examination.remarks.save') }}">@csrf<input type="hidden" name="assignment_id" value="{{ $assignment->id }}"><input type="hidden" name="term" value="{{ $term }}">
 <div class="table-responsive"><table class="table table-bordered table-striped table-hover align-middle"><thead><tr><th>Roll</th><th>Student</th><th>Term Remark</th></tr></thead><tbody>
 @forelse($students as $s)
 <tr><td>{{ $s->roll_no }}</td><td>{{ $s->student?->student_name }}</td><td><textarea class="form-control" rows="2" name="remarks[{{ $s->id }}]">{{ old('remarks.'.$s->id,$saved->get($s->id)?->remark) }}</textarea></td></tr>
 @empty
 <tr><td colspan="3" class="text-center">No current enrollments in this section.</td></tr>
 @endforelse
 </tbody></table></div><button class="btn btn-primary" @disabled($students->isEmpty())>Save Remarks</button></form>
 @endif
</div>
@endsection
