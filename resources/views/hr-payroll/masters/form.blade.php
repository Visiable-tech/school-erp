@extends('layouts.admin')
@section('title',($item?'Edit ':'Add ').$title)
@section('content')
<div class="container-fluid"><h4>{{ $item?'Edit':'Add' }} {{ $title }}</h4>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ $item?route('hr-payroll.masters.update',[$category,$item->id]):route('hr-payroll.masters.store',$category) }}">@csrf
@if($item)@method('PUT')@endif
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Name *</label><input name="name" class="form-control" maxlength="150" required value="{{ old('name',$item->name??'') }}"></div>
<div class="col-md-3"><label class="form-label">Code</label><input name="code" class="form-control" maxlength="40" value="{{ old('code',$item->code??'') }}" {{ $table==='hr_master_options'?'':'required' }}></div>
<div class="col-md-3"><label class="form-label">Active *</label><select name="is_active" class="form-select"><option value="1" @selected(old('is_active',$item->is_active??1)==1)>Yes</option><option value="0" @selected(old('is_active',$item->is_active??1)==0)>No</option></select></div>
@if($table==='hr_attendance_statuses')
<div class="col-md-4"><label class="form-label">Count As *</label><select name="count_as" class="form-select" required>
@foreach(['present','absent','leave','late','off','half_day','on_duty','holiday','other'] as $value)
<option value="{{ $value }}" @selected(old('count_as',$item->count_as??'present')===$value)>{{ ucwords(str_replace('_',' ',$value)) }}</option>
@endforeach
</select></div>
<div class="col-md-4"><label class="form-label">Extra Deduction (days)</label><input type="number" name="extra_deduction_days" step=".5" min="0" max="31" class="form-control" value="{{ old('extra_deduction_days',$item->extra_deduction_days??0) }}"></div>
<div class="col-md-4"><label class="form-label">Document Required</label><select name="document_required" class="form-select"><option value="0" @selected(old('document_required',$item->document_required??0)==0)>No</option><option value="1" @selected(old('document_required',$item->document_required??0)==1)>Yes</option></select></div>
<div class="col-md-3"><label class="form-label">Background Colour</label><input type="color" name="bg_colour" class="form-control form-control-color" value="{{ old('bg_colour',$item->bg_colour??'#ffffff') }}"></div>
<div class="col-md-3"><label class="form-label">Font Colour</label><input type="color" name="font_colour" class="form-control form-control-color" value="{{ old('font_colour',$item->font_colour??'#000000') }}"></div>
<div class="col-md-2"><label class="form-label">Monthly Request Limit</label><input type="number" name="monthly_request_limit" min="0" class="form-control" value="{{ old('monthly_request_limit',$item->monthly_request_limit??'') }}"></div>
<div class="col-md-2"><label class="form-label">Quarterly Request Limit</label><input type="number" name="quarterly_request_limit" min="0" class="form-control" value="{{ old('quarterly_request_limit',$item->quarterly_request_limit??'') }}"></div>
<div class="col-md-2"><label class="form-label">Yearly Request Limit</label><input type="number" name="yearly_request_limit" min="0" class="form-control" value="{{ old('yearly_request_limit',$item->yearly_request_limit??'') }}"></div>
@elseif($table==='hr_shift_definitions')
<div class="col-md-3"><label class="form-label">Start Time *</label><input type="time" name="start_time" class="form-control" value="{{ old('start_time',isset($item)?substr($item->start_time,0,5):'09:00') }}" required></div>
<div class="col-md-3"><label class="form-label">End Time *</label><input type="time" name="end_time" class="form-control" value="{{ old('end_time',isset($item)?substr($item->end_time,0,5):'17:00') }}" required></div>
<div class="col-md-3"><label class="form-label">Grace Minutes</label><input type="number" name="grace_minutes" class="form-control" min="0" max="600" value="{{ old('grace_minutes',$item->grace_minutes??0) }}" required></div>
@else
<div class="col-12"><label class="form-label">Description / Configuration Notes</label><textarea name="description" rows="3" class="form-control">{{ old('description',$item->description??'') }}</textarea></div>
@endif
<div class="col-12"><button class="btn btn-danger">Save</button> <a href="{{ route('hr-payroll.masters.index',$category) }}" class="btn btn-outline-secondary">Cancel</a></div>
</div></div></div></form></div>
@endsection
