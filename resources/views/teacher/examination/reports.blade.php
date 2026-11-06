@extends('layouts.admin')
@section('title','Student-wise Examination Reports')
@section('content')
<div class="container-fluid">
<h4 class="mb-3">Student-wise Examination Reports</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="GET" class="row g-2 mb-3">
<div class="col-md-5"><select class="form-select" name="assignment_id" required><option value="">Choose assigned class/section</option>
@foreach($assignments as $a)
<option value="{{ $a->id }}" @selected(request('assignment_id')==$a->id)>{{ $a->schoolClass?->name }} / {{ $a->section?->name }} — {{ $a->subject?->name ?? 'General' }}</option>
@endforeach
</select></div>
<div class="col-md-3"><input class="form-control" name="term" value="{{ $term }}" maxlength="80" placeholder="Term"></div>
<div class="col-md-2"><button class="btn btn-outline-primary">Load</button></div>
</form>
@if($assignment)
<div class="table-responsive"><table class="table table-bordered table-striped align-middle"><thead><tr><th>Roll</th><th>Student</th>
@foreach($assessments as $assessment)
<th>{{ $assessment->title }}<small class="d-block">Max {{ $assessment->max_marks }}</small></th>
@endforeach
<th>Total</th><th>Percentage</th><th>Term Remark</th></tr></thead><tbody>
@forelse($students as $enrollment)
@php $earned=0;$possible=0; @endphp
<tr><td>{{ $enrollment->roll_no }}</td><td>{{ $enrollment->student?->first_name }} {{ $enrollment->student?->last_name }}</td>
@foreach($assessments as $assessment)
@php $mark=$marks->get($assessment->id.':'.$enrollment->id); if($mark && $mark->marks!==null){$earned+=(float)$mark->marks;$possible+=(float)$assessment->max_marks;} @endphp
<td>{{ $mark?->marks ?? '—' }}</td>
@endforeach
<td>{{ number_format($earned,2) }} / {{ number_format($possible,2) }}</td><td>{{ $possible>0 ? number_format($earned*100/$possible,1).'%' : '—' }}</td><td>{{ $remarks->get($enrollment->id)?->remark ?? '—' }}</td></tr>
@empty
<tr><td colspan="{{ $assessments->count()+5 }}">No students in this section.</td></tr>
@endforelse
</tbody></table></div><p class="text-muted small">Percentage uses only assessments with entered marks. This is a teacher-level summary, not an official report card.</p>
@endif
</div>
@endsection
