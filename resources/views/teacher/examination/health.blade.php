@extends('layouts.admin')
@section('title','Student Health Records')
@section('content')
<div class="container-fluid">
<h4 class="mb-3">Student Health Records</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="GET" class="row g-2 mb-3">
<div class="col-md-5"><select class="form-select" name="assignment_id" required><option value="">Choose assigned class/section</option>
@foreach($assignments as $a)
<option value="{{ $a->id }}" @selected(request('assignment_id')==$a->id)>{{ $a->schoolClass?->name }} / {{ $a->section?->name }} — {{ $a->subject?->name ?? 'General' }}</option>
@endforeach
</select></div>
<div class="col-md-3"><input type="date" name="date" value="{{ $day }}" class="form-control"></div>
<div class="col-md-2"><button class="btn btn-outline-primary">Load</button></div>
</form>
@if($assignment)
<form method="POST" action="{{ route('teacher.examination.health.save') }}">
@csrf
<input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
<input type="hidden" name="recorded_on" value="{{ $day }}">
<div class="table-responsive"><table class="table table-bordered table-striped align-middle"><thead><tr><th>Roll</th><th>Student</th><th>Height (cm)</th><th>Weight (kg)</th><th>Blood Group</th><th>Notes</th></tr></thead><tbody>
@forelse($students as $enrollment)
@php $v=$records->get($enrollment->id); @endphp
<tr><td>{{ $enrollment->roll_no }}</td><td>{{ $enrollment->student?->first_name }} {{ $enrollment->student?->last_name }}</td><td><input class="form-control" type="number" step="0.01" min="0" max="300" name="records[{{ $enrollment->id }}][height_cm]" value="{{ old('records.'.$enrollment->id.'.height_cm',$v->height_cm??'') }}"></td><td><input class="form-control" type="number" step="0.01" min="0" max="500" name="records[{{ $enrollment->id }}][weight_kg]" value="{{ old('records.'.$enrollment->id.'.weight_kg',$v->weight_kg??'') }}"></td><td><input class="form-control" maxlength="8" name="records[{{ $enrollment->id }}][blood_group]" value="{{ old('records.'.$enrollment->id.'.blood_group',$v->blood_group??'') }}"></td><td><input class="form-control" maxlength="3000" name="records[{{ $enrollment->id }}][notes]" value="{{ old('records.'.$enrollment->id.'.notes',$v->notes??'') }}"></td></tr>
@empty
<tr><td colspan="6">No students in this section.</td></tr>
@endforelse
</tbody></table></div>
@if($students->isNotEmpty())<button class="btn btn-primary">Save Health Records</button>@endif
</form>
@endif
</div>
@endsection
