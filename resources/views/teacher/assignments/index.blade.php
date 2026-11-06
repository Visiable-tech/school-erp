@extends('layouts.admin')
@section('title','Teacher Assignments')
@section('content')
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Teacher Class / Subject Assignments</h4>
  @can('teacher-assignment.create')<a class="btn btn-primary" href="{{ route('teacher-assignments.create') }}"><i class="bi bi-plus-circle"></i> Assign Teacher</a>@endcan
 </div>
 @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
 <form method="GET" class="row g-2 mb-3">
  <div class="col-md-4"><select name="teacher_user_id" class="form-select"><option value="">All Teachers</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}" @selected(request('teacher_user_id')==$teacher->id)>{{ $teacher->name }}</option>@endforeach</select></div>
  <div class="col-md-3"><select name="academic_year_id" class="form-select"><option value="">All Years</option>@foreach($years as $year)<option value="{{ $year->id }}" @selected(request('academic_year_id')==$year->id)>{{ $year->name }}</option>@endforeach</select></div>
  <div class="col-md-2"><button class="btn btn-outline-primary">Filter</button> <a class="btn btn-outline-secondary" href="{{ route('teacher-assignments.index') }}">Reset</a></div>
 </form>
 <div class="table-responsive"><table class="table table-bordered table-striped table-hover align-middle">
 <thead class="table-light"><tr><th>#</th><th>Teacher</th><th>Year</th><th>Class</th><th>Section</th><th>Subject</th><th>Class Teacher</th><th>Status</th><th>Action</th></tr></thead>
 <tbody>
 @forelse($assignments as $item)
 <tr><td>{{ $assignments->firstItem()+$loop->index }}</td><td>{{ $item->teacher?->name }}</td><td>{{ $item->academicYear?->name }}</td><td>{{ $item->schoolClass?->name }}</td><td>{{ $item->section?->name }}</td><td>{{ $item->subject?->name ?? 'General / All' }}</td><td>{{ $item->is_class_teacher ? 'Yes':'No' }}</td><td><span class="badge {{ $item->status ? 'bg-success':'bg-secondary' }}">{{ $item->status ? 'Active':'Inactive' }}</span></td>
 <td class="text-nowrap">@can('teacher-assignment.edit')<a href="{{ route('teacher-assignments.edit',$item) }}" class="btn btn-sm btn-outline-primary">Edit</a>@endcan
 @can('teacher-assignment.delete')<form method="POST" action="{{ route('teacher-assignments.destroy',$item) }}" class="d-inline" onsubmit="return confirm('Remove this assignment?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>@endcan</td></tr>
 @empty<tr><td colspan="9" class="text-center text-muted">No teacher assignments found.</td></tr>@endforelse
 </tbody></table></div>
 {{ $assignments->links() }}
</div>
@endsection
