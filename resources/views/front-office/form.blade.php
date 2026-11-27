@extends('layouts.admin')
@section('content')
<div class="container-fluid py-3"><h3>{{ $visit ? 'Edit Visitor' : 'Add New Visitor' }}</h3>
@include('front-office.messages')
<form class="card card-body" method="POST" action="{{ $visit ? route('front-office.update',$visit->id) : route('front-office.store') }}">@csrf @if($visit) @method('PUT') @endif
<div class="row g-3">
@foreach(['visit_date'=>'Visit Date','in_time'=>'In Time','out_time'=>'Out Time','visitor_name'=>'Visitor Name','mobile'=>'Mobile Number','team_size'=>'Total Team Size (including visitor)','meet_person'=>'Person to Meet','student_name'=>'Student Name (for parent visits)'] as $key=>$label)
<div class="col-md-4"><label class="form-label" for="{{ $key }}">{{ $label }}</label><input id="{{ $key }}" class="form-control" name="{{ $key }}" type="{{ $key==='visit_date'?'date':(in_array($key,['in_time','out_time'])?'time':($key==='team_size'?'number':'text')) }}" value="{{ old($key, in_array($key,['in_time','out_time']) ? substr($visit->$key ?? ($key==='in_time'?$time:''),0,5) : ($visit->$key ?? ($key==='visit_date'?$today:($key==='team_size'?1:'')))) }}" @if(!in_array($key,['out_time','mobile','student_name'])) required @endif @if($key==='team_size') min="1" max="1000" @endif @if($key==='visit_date') max="{{ $today }}" @endif></div>
@endforeach
<div class="col-md-4"><label class="form-label" for="visitor_type">Visitor Type</label><select id="visitor_type" name="visitor_type" class="form-select" required>@foreach($types as $type)<option @selected(old('visitor_type',$visit->visitor_type??'Parent / Guardian')===$type)>{{ $type }}</option>@endforeach</select></div>
<div class="col-md-6"><label for="purpose" class="form-label">Purpose of Visit</label><textarea id="purpose" name="purpose" class="form-control" required maxlength="2000">{{ old('purpose',$visit->purpose??'') }}</textarea></div>
<div class="col-md-6"><label for="remarks" class="form-label">Remarks</label><textarea id="remarks" name="remarks" class="form-control" maxlength="2000">{{ old('remarks',$visit->remarks??'') }}</textarea></div>
<div class="col-12"><button class="btn btn-success">Save Visitor</button> <a class="btn btn-secondary" href="{{ route('front-office.index') }}">Cancel</a></div></div></form></div>
@endsection
