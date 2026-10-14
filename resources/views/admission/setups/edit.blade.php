@extends('layouts.admin')
@section('title','Admission Setup Details')
@section('content')
<div class="container-fluid"><h4 class="mb-3">Admission Setup Details</h4>
<div class="card shadow-sm"><div class="card-body">
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" enctype="multipart/form-data" action="{{ route('admission-setups.update',$setup) }}">@csrf @method('PUT')
<h5 class="border-bottom pb-2 mb-3">Admission Setup</h5><div class="row g-3">
<div class="col-md-6"><label class="form-label">Status *</label><select class="form-select" name="status"><option value="open" @selected(old('status',$setup->status)==='open')>Open</option><option value="closed" @selected(old('status',$setup->status)==='closed')>Closed</option></select></div>
<div class="col-md-6"><label class="form-label">Admission Code *</label><input class="form-control" name="admission_code" maxlength="100" required value="{{ old('admission_code',$setup->admission_code) }}"></div>
<div class="col-md-6"><label class="form-label">Academic Year</label><input class="form-control" value="{{ $setup->academicYear?->name }}" readonly></div>
<div class="col-md-6"><label class="form-label">Class</label><input class="form-control" value="{{ $setup->schoolClass?->name }}" readonly></div>
<div class="col-md-6"><label class="form-label">No. of Seats *</label><input type="number" min="0" class="form-control" name="no_of_seats" required value="{{ old('no_of_seats',$setup->no_of_seats) }}"></div>
<div class="col-md-6"><label class="form-label">Required Age (Months)</label><input type="number" min="0" max="300" class="form-control" name="required_age_months" value="{{ old('required_age_months',$setup->required_age_months) }}"></div>
@foreach(['opens_on'=>'Opens On','closes_on'=>'Closes On','dob_check_date'=>'DOB Check Date','dob_check_start'=>'DOB Check Start','dob_check_end'=>'DOB Check End'] as $key=>$label)
<div class="col-md-6"><label class="form-label">{{ $label }}</label><input type="date" class="form-control" name="{{ $key }}" value="{{ old($key,$setup->$key?->format('Y-m-d')) }}"></div>
@endforeach
<div class="col-md-6"><label class="form-label">Default Exam Date/Time</label><input type="datetime-local" class="form-control" name="default_exam_at" value="{{ old('default_exam_at',$setup->default_exam_at?->format('Y-m-d\TH:i')) }}"></div>
<div class="col-md-6"><label class="form-label">Syllabus (PDF, max 5 MB)</label><input type="file" name="syllabus" accept=".pdf,application/pdf" class="form-control">@if($setup->syllabus_path)<a target="_blank" rel="noopener" href="{{ asset('storage/'.$setup->syllabus_path) }}">View existing syllabus</a>@endif</div>
<div class="col-md-6"><label class="form-label">Document Group</label><input class="form-control" value="Available after Document Groups module" disabled></div>
<div class="col-md-6"><label class="form-label">Exam Group</label><input class="form-control" value="Available after Exam Groups module" disabled></div>
</div>
<h5 class="border-bottom pb-2 mt-4 mb-3">Fee Info.</h5><div class="row g-3">
@foreach(['form_fee'=>'Form Fee','registration_fee'=>'Registration Fee','online_registration_fee'=>'Reg. Fee (Online)'] as $key=>$label)
<div class="col-md-6"><label class="form-label">{{ $label }}</label><input type="number" min="0" step="0.01" name="{{ $key }}" class="form-control" required value="{{ old($key,$setup->$key) }}"></div>
@endforeach
@foreach(['form_fee_label'=>'Form Fee Label','registration_fee_label'=>'Registration Fee Label'] as $key=>$label)
<div class="col-md-6"><label class="form-label">{{ $label }}</label><input name="{{ $key }}" class="form-control" value="{{ old($key,$setup->$key) }}"></div>
@endforeach
<div class="col-md-6"><label class="form-label">Accounting Ledger</label><input class="form-control" value="Available after ledger integration" disabled></div>
</div>
<h5 class="border-bottom pb-2 mt-4 mb-3">Email Configuration</h5>
<div class="alert alert-light border">Assessment, Re-Assessment, Interaction and Re-Interaction templates will be connected after the email-template master is available.</div>
<button class="btn btn-primary">Save</button><a href="{{ route('admission-setups.index') }}" class="btn btn-secondary ms-2">Cancel</a>
</form></div></div></div>
@endsection
