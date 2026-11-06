@extends('layouts.admin')
@section('title','Skill Descriptors')
@section('content')
<div class="container-fluid">
<h4 class="mb-3">Skill Descriptors</h4>
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
<form method="POST" action="{{ route('teacher.examination.skills.add') }}" class="row g-2 mb-3">
@csrf
<input type="hidden" name="assignment_id" value="{{ $assignment->id }}"><input type="hidden" name="term" value="{{ $term }}">
<div class="col-md-6"><input name="skill" maxlength="160" class="form-control" required placeholder="Add skill (e.g. Teamwork)"></div><div class="col-md-2"><button class="btn btn-outline-primary">Add Skill</button></div>
</form>
@if($descriptors->isNotEmpty())
<form method="POST" action="{{ route('teacher.examination.skills.save') }}">@csrf
<input type="hidden" name="assignment_id" value="{{ $assignment->id }}"><input type="hidden" name="term" value="{{ $term }}">
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Student</th>
@foreach($descriptors as $skill)
<th>{{ $skill->skill }}</th>
@endforeach
</tr></thead><tbody>
@foreach($students as $enrollment)
<tr><td>{{ $enrollment->roll_no }} — {{ $enrollment->student?->first_name }} {{ $enrollment->student?->last_name }}</td>
@foreach($descriptors as $skill)
@php $rating=$ratings->get($skill->id.':'.$enrollment->id)?->rating; @endphp
<td><select class="form-select" name="ratings[{{ $skill->id }}][{{ $enrollment->id }}]"><option value="">Not Rated</option>
@foreach(['Excellent','Good','Satisfactory','Needs Improvement'] as $choice)
<option value="{{ $choice }}" @selected($rating===$choice)>{{ $choice }}</option>
@endforeach
</select></td>
@endforeach
</tr>
@endforeach
</tbody></table></div><button class="btn btn-primary">Save Ratings</button></form>
@endif
@endif
</div>
@endsection
