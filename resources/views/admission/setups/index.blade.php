@extends('layouts.admin')
@section('title','Admission Setup')
@section('content')
<div class="container-fluid">
<h4 class="mb-3">Admission Setup</h4>
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
<div class="card shadow-sm"><div class="card-body">
<form method="GET" action="{{ route('admission-setups.index') }}" class="row g-2 align-items-end mb-3">
<div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">All</option><option value="open" @selected(request('status')==='open')>Open</option><option value="closed" @selected(request('status')==='closed')>Closed</option></select></div>
<div class="col-md-3"><label class="form-label">Academic Year</label><select name="academic_year_id" class="form-select"><option value="">All</option>@foreach($years as $year)<option value="{{ $year->id }}" @selected((string)request('academic_year_id')===(string)$year->id)>{{ $year->name }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Class</label><select name="school_class_id" class="form-select"><option value="">All</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected((string)request('school_class_id')===(string)$class->id)>{{ $class->name }}</option>@endforeach</select></div>
<div class="col-md-3"><button class="btn btn-primary">Filter</button> <a href="{{ route('admission-setups.index') }}" class="btn btn-outline-secondary">Reset</a></div>
</form>
@can('admission-setup.create')
<form method="POST" action="{{ route('admission-setups.initiate') }}" class="row g-2 align-items-end border rounded p-3 mb-3">@csrf
<div class="col-md-4"><label class="form-label">Initiate Admission For Academic Year</label><select name="academic_year_id" class="form-select" required><option value="">Select Academic Year</option>@foreach($years as $year)<option value="{{ $year->id }}">{{ $year->name }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Default Seats Per Class</label><input type="number" name="no_of_seats" value="100" min="0" class="form-control" required></div>
<div class="col-md-3"><button class="btn btn-danger" onclick="return confirm('Create missing setups for every active class in this academic year?')">Initiate Admission</button></div>
</form>
@endcan
<div class="table-responsive"><table class="table table-bordered table-striped table-hover align-middle mb-0">
<thead class="table-light"><tr><th>#</th><th>Status</th><th>Admission Code</th><th>Academic Year</th><th>Class</th><th>Seats</th><th>Age (Months)</th><th>Form Fee</th><th>Registration Fee</th><th>Online Fee</th><th>DOB Start</th><th>DOB End</th><th>Exam Date</th><th>Action</th></tr></thead>
<tbody>@forelse($setups as $setup)<tr>
<td>{{ $setups->firstItem()+$loop->index }}</td><td><span class="badge {{ $setup->status==='open'?'bg-success':'bg-secondary' }}">{{ ucfirst($setup->status) }}</span></td>
<td>{{ $setup->admission_code }}</td><td>{{ $setup->academicYear?->name }}</td><td>{{ $setup->schoolClass?->name }}</td><td>{{ $setup->no_of_seats }}</td><td>{{ $setup->required_age_months ?? '-' }}</td>
<td>{{ number_format($setup->form_fee,2) }}</td><td>{{ number_format($setup->registration_fee,2) }}</td><td>{{ number_format($setup->online_registration_fee,2) }}</td>
<td>{{ $setup->dob_check_start?->format('d-m-Y') ?? '-' }}</td><td>{{ $setup->dob_check_end?->format('d-m-Y') ?? '-' }}</td><td>{{ $setup->default_exam_at?->format('d-m-Y H:i') ?? '-' }}</td>
<td>@can('admission-setup.edit')<a class="btn btn-sm btn-warning" href="{{ route('admission-setups.edit',$setup) }}">Edit</a>@endcan</td>
</tr>@empty<tr><td colspan="14" class="text-center py-4">No admission setups found.</td></tr>@endforelse</tbody></table></div>
<div class="mt-3">{{ $setups->links() }}</div>
</div></div></div>
@endsection
