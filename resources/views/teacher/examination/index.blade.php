@extends('layouts.admin')
@section('title','Assessment Activities')
@section('content')
<div class="container-fluid">
 <div class="d-flex justify-content-between mb-3"><h4>Assessment Activities</h4><a href="{{ route('teacher.examination.create') }}" class="btn btn-primary">Create Assessment</a></div>
 @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
 <div class="table-responsive"><table class="table table-bordered table-striped table-hover align-middle"><thead><tr><th>Date</th><th>Title</th><th>Term</th><th>Class / Section / Subject</th><th>Maximum Marks</th><th>Action</th></tr></thead><tbody>
 @forelse($assessments as $a)
 <tr><td>{{ $a->assessment_date->format('d M Y') }}</td><td>{{ $a->title }}</td><td>{{ $a->term }}</td><td>{{ $a->assignment?->schoolClass?->name }} / {{ $a->assignment?->section?->name }} / {{ $a->assignment?->subject?->name ?? 'General' }}</td><td>{{ $a->max_marks }}</td><td><a href="{{ route('teacher.examination.marks',$a->id) }}" class="btn btn-sm btn-outline-primary">Enter Marks</a></td></tr>
 @empty
 <tr><td colspan="6" class="text-center">No assessments found.</td></tr>
 @endforelse
 </tbody></table></div>{{ $assessments->links() }}
</div>
@endsection
