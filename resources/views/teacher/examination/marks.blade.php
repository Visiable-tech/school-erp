@extends('layouts.admin')
@section('title','Assessment Marks')
@section('content')
<div class="container-fluid"><h4>{{ $assessment->title }} — {{ $assessment->term }}</h4><p>Maximum Marks: {{ $assessment->max_marks }}</p>
 @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
 @if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
 <form method="POST" action="{{ route('teacher.examination.marks.save',$assessment->id) }}">@csrf
 <div class="table-responsive"><table class="table table-bordered table-striped table-hover align-middle"><thead><tr><th>Roll</th><th>Student</th><th>Marks</th><th>Remarks</th></tr></thead><tbody>
 @forelse($students as $s)
 <tr><td>{{ $s->roll_no }}</td><td>{{ $s->student?->student_name }}</td><td><input type="number" step="0.01" min="0" max="{{ $assessment->max_marks }}" class="form-control" name="marks[{{ $s->id }}]" value="{{ old('marks.'.$s->id,$marks->get($s->id)?->marks) }}"></td><td><input class="form-control" name="remarks[{{ $s->id }}]" value="{{ old('remarks.'.$s->id,$marks->get($s->id)?->remarks) }}"></td></tr>
 @empty
 <tr><td colspan="4" class="text-center">No current enrollments in this section.</td></tr>
 @endforelse
 </tbody></table></div><button class="btn btn-primary" @disabled($students->isEmpty())>Save Marks</button> <a href="{{ route('teacher.examination.index') }}" class="btn btn-secondary">Back</a></form></div>
@endsection
