@extends('layouts.admin')
@section('title','Candidates')
@section('content')
<div class="container-fluid">
<h4 class="mb-3">Candidates</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card shadow-sm"><div class="card-body">
<form method="GET" action="{{ route('admission-candidates.index') }}" class="row g-2 mb-3">
<div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(\App\Models\AdmissionCandidate::STATUSES as $value=>$label)<option value="{{ $value }}" @selected(request('status')===$value)>{{ $label }}</option>@endforeach</select></div>
<div class="col-md-5"><label class="form-label">Search</label><input name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, parent or mobile"></div>
<div class="col-md-4 d-flex align-items-end gap-2"><button class="btn btn-primary">Search</button><a class="btn btn-outline-secondary" href="{{ route('admission-candidates.index') }}">Reset</a>@can('admission-candidate.create')<a class="btn btn-success" href="{{ route('admission-candidates.create') }}">Add Candidate</a>@endcan</div>
</form>
<div class="table-responsive"><table class="table table-bordered table-striped table-hover align-middle mb-0"><thead class="table-light"><tr><th>#</th><th>Status</th><th>Image</th><th>Candidate's Name</th><th>Father's Name</th><th>Mother's Name</th><th>Mobile No.</th><th>Date Of Birth</th><th>Action</th></tr></thead><tbody>
@forelse($candidates as $candidate)<tr><td>{{ $candidates->firstItem()+$loop->index }}</td><td><span class="badge bg-secondary">{{ \App\Models\AdmissionCandidate::STATUSES[$candidate->status]??$candidate->status }}</span></td><td>@if($candidate->photo_path)<img src="{{ asset('storage/'.$candidate->photo_path) }}" width="40" height="40" style="object-fit:cover;border-radius:50%" alt="">@else<i class="bi bi-person-circle fs-2 text-secondary"></i>@endif</td><td>{{ $candidate->candidate_name }}</td><td>{{ $candidate->father_name?:'-' }}</td><td>{{ $candidate->mother_name?:'-' }}</td><td>{{ $candidate->mobile?:'-' }}</td><td>{{ $candidate->date_of_birth?->format('d-m-Y')?:'-' }}</td><td><div class="d-flex gap-1">@can('admission-candidate.view')<a class="btn btn-sm btn-outline-primary" href="{{ route('admission-candidates.track',$candidate->id) }}">Status Track</a>@endcan @can('admission-candidate.edit')<a class="btn btn-sm btn-warning" href="{{ route('admission-candidates.edit',$candidate->id) }}">Edit</a>@endcan</div></td></tr>
@empty<tr><td colspan="9" class="text-center py-4">No candidates found.</td></tr>@endforelse
</tbody></table></div><div class="mt-3">{{ $candidates->links() }}</div></div></div></div>
@endsection
