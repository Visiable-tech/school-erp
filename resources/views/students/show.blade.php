@extends('layouts.admin')
@section('title', 'Student Profile')
@section('content')
<style>
.student-profile-shell{--student-accent:#355bd6}
.student-profile-hero{background:linear-gradient(115deg,#f7faff,#fff);border:1px solid #e4eaf4;border-radius:18px;padding:20px}
.student-profile-avatar{width:76px;height:86px;object-fit:cover;border-radius:12px;border:2px solid white;box-shadow:0 3px 14px #1b326021}
.student-profile-placeholder{width:76px;height:86px;border-radius:12px;background:#e8edfc;color:#4564a0;display:flex;align-items:center;justify-content:center;font-size:28px}
.student-profile-stats .card{border:1px solid #e5eaf3;border-radius:14px;transition:box-shadow .15s,transform .15s;height:100%}
.student-profile-stats a:hover .card{box-shadow:0 8px 25px #142e5a17;transform:translateY(-2px)}
.student-profile-stats a{text-decoration:none;color:inherit}
.student-profile-stats .metric{font-size:1.55rem;font-weight:700;line-height:1.2}
.student-profile-tabs-wrap{position:sticky;top:0;z-index:15;background:var(--bs-body-bg,#fff);padding-top:10px;border-bottom:1px solid #e7ebf3}
.student-360-tabs{display:flex;flex-wrap:nowrap;overflow-x:auto;white-space:nowrap;scrollbar-width:thin;margin-bottom:0!important;padding-bottom:2px}
.student-360-tabs .nav-link{white-space:nowrap;border:0;border-bottom:3px solid transparent;border-radius:0;color:#5d687b;font-weight:600;padding:.75rem .95rem}
.student-360-tabs .nav-link.active{color:var(--student-accent);border-bottom-color:var(--student-accent);background:transparent}
.student-profile-shell .tab-content{padding-top:20px}
.student-profile-shell .card{border-radius:12px}
.student-profile-shell .card-header{border-radius:12px 12px 0 0}
@media(max-width:767px){.student-profile-hero{padding:14px}.student-profile-tabs-wrap{top:0}.student-profile-stats .metric{font-size:1.2rem}}
@media print{.student-profile-tabs-wrap,.student-profile-actions,.student-profile-stats{display:none!important}.tab-pane{display:block!important;opacity:1!important}.card{break-inside:avoid}}
</style>
@php
    $attendanceMarked = $attendance->count();
    $attendancePresent = $attendance->filter(fn ($record) => strtolower((string) $record->attendance_status) === 'present')->count();
    $attendanceRate = $attendanceMarked ? round($attendancePresent * 100 / $attendanceMarked, 1) : null;
    $canViewStudentFees = auth()->user()?->canAny(['fee-collection.view','fee-structure.view','fee-reports.view','student-fee.view']) ?? false;
    $canViewStudentDocuments = auth()->user()?->canAny(['student-document.view','student-document.index','student.view']) ?? false;
@endphp
<div class="student-profile-shell">
<section class="student-profile-hero mb-3" aria-label="Student profile header">
  <div class="d-flex flex-wrap gap-3 align-items-center">
    @if(!empty($photoPaths['student']))
      <img src="{{ asset('storage/'.$photoPaths['student']) }}" class="student-profile-avatar" alt="{{ $student->student_name }} photo">
    @else
      <div class="student-profile-placeholder" aria-hidden="true"><i class="bi bi-person"></i></div>
    @endif
    <div class="flex-grow-1">
      <div class="text-muted small text-uppercase fw-semibold mb-1">Student 360° Profile</div>
      <h4 class="mb-1">{{ $student->student_name }}</h4>
      <div class="d-flex flex-wrap gap-2 align-items-center text-muted small">
        <span><i class="bi bi-person-vcard me-1"></i>{{ $student->admission_no ?: 'Admission number pending' }}</span>
        <span class="d-none d-sm-inline">·</span>
        <span>{{ $student->currentEnrollment?->schoolClass?->name ?: 'Class not assigned' }} / {{ $student->currentEnrollment?->section?->name ?: 'Section not assigned' }}</span>
        <span class="d-none d-sm-inline">·</span>
        <span>{{ $student->currentEnrollment?->academicYear?->name ?: 'Session not assigned' }}</span>
      </div>
    </div>
    <div class="student-profile-actions d-flex flex-wrap gap-2">
      @can('student.edit')<a class="btn btn-primary btn-sm" href="{{ route('students.edit', $student) }}"><i class="bi bi-pencil-square me-1"></i>Edit Student</a>@endcan
      <button class="btn btn-outline-secondary btn-sm" type="button" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('students.index') }}">Student List</a>
    </div>
  </div>
</section>
<div class="row g-3 mb-3 student-profile-stats">
  <div class="col-6 col-xl-3"><a href="#tab1" data-student-tab="tab1"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small mb-2">Monthly Attendance</div><div class="metric">{{ $attendanceRate === null ? '—' : $attendanceRate.'%' }}</div><div class="small text-muted">{{ $attendancePresent }} present / {{ $attendanceMarked }} marked</div></div></div></a></div>
  @if($canViewStudentFees)
  <div class="col-6 col-xl-3"><a href="#tab2" data-student-tab="tab2"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small mb-2">Outstanding Fees</div><div class="metric">₹{{ number_format((float) $feeDues->sum('balance_amount'), 2) }}</div><div class="small text-muted">Based on recorded dues</div></div></div></a></div>
  @endif
  <div class="col-6 col-xl-3"><a href="#tab4" data-student-tab="tab4"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small mb-2">Enrollments</div><div class="metric">{{ $student->enrollments->count() }}</div><div class="small text-muted">Academic records</div></div></div></a></div>
  @if($canViewStudentDocuments)
  <div class="col-6 col-xl-3"><a href="#tab5" data-student-tab="tab5"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small mb-2">Documents</div><div class="metric">{{ $documents->count() }}</div><div class="small text-muted">Files on record</div></div></div></a></div>
  @endif
</div>
<div class="student-profile-tabs-wrap mb-2">
<ul class="nav nav-tabs student-360-tabs mb-3" id="student360Tabs" role="tablist">
<li class="nav-item" role="presentation"><button class="nav-link active" id="tab0-tab" data-bs-toggle="tab" data-bs-target="#tab0" type="button" role="tab" aria-controls="tab0" aria-selected="true">Overview</button></li>
<li class="nav-item" role="presentation"><button class="nav-link " id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1" aria-selected="false">Attendance</button></li>
@if($canViewStudentFees)<li class="nav-item" role="presentation"><button class="nav-link " id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" aria-controls="tab2" aria-selected="false">Fees</button></li>@endif
<li class="nav-item" role="presentation"><button class="nav-link " id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" type="button" role="tab" aria-controls="tab3" aria-selected="false">Academics</button></li>
<li class="nav-item" role="presentation"><button class="nav-link " id="tab4-tab" data-bs-toggle="tab" data-bs-target="#tab4" type="button" role="tab" aria-controls="tab4" aria-selected="false">Enrollment History</button></li>
@if($canViewStudentDocuments)<li class="nav-item" role="presentation"><button class="nav-link " id="tab5-tab" data-bs-toggle="tab" data-bs-target="#tab5" type="button" role="tab" aria-controls="tab5" aria-selected="false">Documents</button></li>@endif
<li class="nav-item" role="presentation"><button class="nav-link " id="tab6-tab" data-bs-toggle="tab" data-bs-target="#tab6" type="button" role="tab" aria-controls="tab6" aria-selected="false">Leave & Absence</button></li>
<li class="nav-item" role="presentation"><button class="nav-link " id="tab7-tab" data-bs-toggle="tab" data-bs-target="#tab7" type="button" role="tab" aria-controls="tab7" aria-selected="false">Transport</button></li>
<li class="nav-item" role="presentation"><button class="nav-link " id="tab8-tab" data-bs-toggle="tab" data-bs-target="#tab8" type="button" role="tab" aria-controls="tab8" aria-selected="false">Medical</button></li>
<li class="nav-item" role="presentation"><button class="nav-link " id="tab9-tab" data-bs-toggle="tab" data-bs-target="#tab9" type="button" role="tab" aria-controls="tab9" aria-selected="false">Activities</button></li>
<li class="nav-item" role="presentation"><button class="nav-link " id="tab10-tab" data-bs-toggle="tab" data-bs-target="#tab10" type="button" role="tab" aria-controls="tab10" aria-selected="false">Communication</button></li>
<li class="nav-item" role="presentation"><button class="nav-link " id="tab11-tab" data-bs-toggle="tab" data-bs-target="#tab11" type="button" role="tab" aria-controls="tab11" aria-selected="false">Activity History</button></li>
</ul>
</div>
<div class="tab-content" id="student360Content">
<div class="tab-pane fade show active" id="tab0" role="tabpanel" aria-labelledby="tab0-tab">
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><strong>Admission & Academic Information</strong></div><div class="card-body"><div class="row g-3">
<div class="col-md-3"><div class="text-muted small mb-1">Admission No.</div><div class="fw-medium text-break">{{ $student->admission_no ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Admission Date</div><div class="fw-medium text-break">{{ $student->admission_date ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Academic Year</div><div class="fw-medium text-break">{{ $student->currentEnrollment?->academicYear?->name ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Class</div><div class="fw-medium text-break">{{ $student->currentEnrollment?->schoolClass?->name ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Section</div><div class="fw-medium text-break">{{ $student->currentEnrollment?->section?->name ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Roll No.</div><div class="fw-medium text-break">{{ $student->currentEnrollment?->roll_no ?: '—' }}</div></div>
</div></div></div>
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><strong>Student Information</strong></div><div class="card-body"><div class="row g-3"><div class="col-lg-9 col-md-8"><div class="row g-3">
<div class="col-md-3"><div class="text-muted small mb-1">Student Name</div><div class="fw-medium text-break">{{ $student->student_name ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Date Of Birth</div><div class="fw-medium text-break">{{ $student->date_of_birth ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Gender</div><div class="fw-medium text-break">{{ $student->gender ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Blood Group</div><div class="fw-medium text-break">{{ $student->blood_group ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Nationality</div><div class="fw-medium text-break">{{ $student->nationality ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Religion</div><div class="fw-medium text-break">{{ $student->religion ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Category</div><div class="fw-medium text-break">{{ $student->category ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Mother Tongue</div><div class="fw-medium text-break">{{ $student->mother_tongue ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Student Aadhaar Last4</div><div class="fw-medium text-break">{{ (data_get($extraDetails, 'student_aadhaar_last4') ? '•••• '.data_get($extraDetails, 'student_aadhaar_last4') : null) ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Birth Place</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'birth_place') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Identification Mark</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'identification_mark') ?: '—' }}</div></div>
</div></div><div class="col-lg-3 col-md-4 order-first order-md-last"><div class="d-flex flex-wrap gap-3 justify-content-center"><div class="text-center"><div class="text-muted small mb-2">Student Photo</div>
@if(!empty($photoPaths['student']))
<img src="{{ asset('storage/'.$photoPaths['student']) }}" alt="Student Photo" class="img-thumbnail rounded" style="width:150px;height:180px;object-fit:cover;" loading="lazy">
@else
<div class="border rounded bg-light d-flex align-items-center justify-content-center text-muted small" style="width:150px;height:180px;">No photo uploaded</div>
@endif
</div></div></div></div></div></div>
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><strong>Father Information</strong></div><div class="card-body"><div class="row g-3"><div class="col-lg-9 col-md-8"><div class="row g-3">
<div class="col-md-3"><div class="text-muted small mb-1">Name</div><div class="fw-medium text-break">{{ $student->father_name ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Mobile</div><div class="fw-medium text-break">{{ $student->father_mobile ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Email</div><div class="fw-medium text-break">{{ $student->father_email ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Occupation</div><div class="fw-medium text-break">{{ $student->father_occupation ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Aadhaar Last4</div><div class="fw-medium text-break">{{ (data_get($extraDetails, 'father_aadhaar_last4') ? '•••• '.data_get($extraDetails, 'father_aadhaar_last4') : null) ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Nationality</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'father_nationality') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Qualification</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'father_qualification') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Organization</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'father_organization') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Work Address</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'father_work_address') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Address</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'father_address') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">City</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'father_city') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">State</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'father_state') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Pin Code</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'father_pin_code') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Alternate Mobile</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'father_alternate_mobile') ?: '—' }}</div></div>
</div></div><div class="col-lg-3 col-md-4 order-first order-md-last"><div class="d-flex flex-wrap gap-3 justify-content-center"><div class="text-center"><div class="text-muted small mb-2">Father Photo</div>
@if(!empty($photoPaths['father']))
<img src="{{ asset('storage/'.$photoPaths['father']) }}" alt="Father Photo" class="img-thumbnail rounded" style="width:150px;height:180px;object-fit:cover;" loading="lazy">
@else
<div class="border rounded bg-light d-flex align-items-center justify-content-center text-muted small" style="width:150px;height:180px;">No photo uploaded</div>
@endif
</div></div></div></div></div></div>
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><strong>Mother Information</strong></div><div class="card-body"><div class="row g-3"><div class="col-lg-9 col-md-8"><div class="row g-3">
<div class="col-md-3"><div class="text-muted small mb-1">Name</div><div class="fw-medium text-break">{{ $student->mother_name ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Mobile</div><div class="fw-medium text-break">{{ $student->mother_mobile ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Email</div><div class="fw-medium text-break">{{ $student->mother_email ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Occupation</div><div class="fw-medium text-break">{{ $student->mother_occupation ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Aadhaar Last4</div><div class="fw-medium text-break">{{ (data_get($extraDetails, 'mother_aadhaar_last4') ? '•••• '.data_get($extraDetails, 'mother_aadhaar_last4') : null) ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Nationality</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'mother_nationality') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Qualification</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'mother_qualification') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Organization</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'mother_organization') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Work Address</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'mother_work_address') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Address</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'mother_address') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">City</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'mother_city') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">State</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'mother_state') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Pin Code</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'mother_pin_code') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Alternate Mobile</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'mother_alternate_mobile') ?: '—' }}</div></div>
</div></div><div class="col-lg-3 col-md-4 order-first order-md-last"><div class="d-flex flex-wrap gap-3 justify-content-center"><div class="text-center"><div class="text-muted small mb-2">Mother Photo</div>
@if(!empty($photoPaths['mother']))
<img src="{{ asset('storage/'.$photoPaths['mother']) }}" alt="Mother Photo" class="img-thumbnail rounded" style="width:150px;height:180px;object-fit:cover;" loading="lazy">
@else
<div class="border rounded bg-light d-flex align-items-center justify-content-center text-muted small" style="width:150px;height:180px;">No photo uploaded</div>
@endif
</div></div></div></div></div></div>
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><strong>Guardian Information</strong></div><div class="card-body"><div class="row g-3"><div class="col-lg-9 col-md-8"><div class="row g-3">
<div class="col-md-3"><div class="text-muted small mb-1">Name</div><div class="fw-medium text-break">{{ $student->guardian_name ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Relation</div><div class="fw-medium text-break">{{ $student->guardian_relation ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Mobile</div><div class="fw-medium text-break">{{ $student->guardian_mobile ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Aadhaar Last4</div><div class="fw-medium text-break">{{ (data_get($extraDetails, 'guardian_aadhaar_last4') ? '•••• '.data_get($extraDetails, 'guardian_aadhaar_last4') : null) ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Nationality</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'guardian_nationality') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Occupation</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'guardian_occupation') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Email</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'guardian_email') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Qualification</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'guardian_qualification') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Address</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'guardian_address') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">City</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'guardian_city') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">State</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'guardian_state') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Pin Code</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'guardian_pin_code') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Alternate Mobile</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'guardian_alternate_mobile') ?: '—' }}</div></div>
</div></div><div class="col-lg-3 col-md-4 order-first order-md-last"><div class="d-flex flex-wrap gap-3 justify-content-center"><div class="text-center"><div class="text-muted small mb-2">Guardian Photo</div>
@if(!empty($photoPaths['guardian']))
<img src="{{ asset('storage/'.$photoPaths['guardian']) }}" alt="Guardian Photo" class="img-thumbnail rounded" style="width:150px;height:180px;object-fit:cover;" loading="lazy">
@else
<div class="border rounded bg-light d-flex align-items-center justify-content-center text-muted small" style="width:150px;height:180px;">No photo uploaded</div>
@endif
</div><div class="text-center"><div class="text-muted small mb-2">Parents Together Photo</div>
@if(!empty($photoPaths['parents']))
<img src="{{ asset('storage/'.$photoPaths['parents']) }}" alt="Parents Together Photo" class="img-thumbnail rounded" style="width:150px;height:180px;object-fit:cover;" loading="lazy">
@else
<div class="border rounded bg-light d-flex align-items-center justify-content-center text-muted small" style="width:150px;height:180px;">No photo uploaded</div>
@endif
</div></div></div></div></div></div>
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><strong>Present Address</strong></div><div class="card-body"><div class="row g-3">
<div class="col-md-3"><div class="text-muted small mb-1">Address</div><div class="fw-medium text-break">{{ $student->present_address ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">City</div><div class="fw-medium text-break">{{ $student->present_city ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">State</div><div class="fw-medium text-break">{{ $student->present_state ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Pin Code</div><div class="fw-medium text-break">{{ $student->present_pin_code ?: '—' }}</div></div>
</div></div></div>
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><strong>Permanent Address</strong></div><div class="card-body"><div class="row g-3">
<div class="col-md-3"><div class="text-muted small mb-1">Address</div><div class="fw-medium text-break">{{ $student->permanent_address ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">City</div><div class="fw-medium text-break">{{ $student->permanent_city ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">State</div><div class="fw-medium text-break">{{ $student->permanent_state ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Pin Code</div><div class="fw-medium text-break">{{ $student->permanent_pin_code ?: '—' }}</div></div>
</div></div></div>
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><strong>Previous School</strong></div><div class="card-body"><div class="row g-3">
<div class="col-md-3"><div class="text-muted small mb-1">School</div><div class="fw-medium text-break">{{ $student->previous_school ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Class</div><div class="fw-medium text-break">{{ $student->previous_class ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Board</div><div class="fw-medium text-break">{{ $student->previous_board ?: '—' }}</div></div>
</div></div></div>

</div>
<div class="tab-pane fade " id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
<div class="card shadow-sm"><div class="card-header"><strong>Daily & Monthly Attendance</strong></div><div class="card-body"><form method="GET" class="row g-2 align-items-end mb-3"><input type="hidden" name="profile_tab" value="tab1"><div class="col-auto"><label class="form-label" for="month">Month</label><input id="month" class="form-control" type="month" name="month" value="{{ $month }}"></div><div class="col-auto"><button class="btn btn-primary">Show</button></div></form><div class="row g-2 mb-3"><div class="col-md-4"><div class="alert alert-light border">Marked days: <strong>{{ $attendance->count() }}</strong></div></div><div class="col-md-4"><div class="alert alert-light border">Present: <strong>{{ $attendance->filter(fn($a) => strtolower((string)$a->attendance_status) === 'present')->count() }}</strong></div></div><div class="col-md-4"><div class="alert alert-light border">Absent: <strong>{{ $attendance->filter(fn($a) => strtolower((string)$a->attendance_status) === 'absent')->count() }}</strong></div></div></div><div class="mb-3"><div class="fw-semibold mb-2">Monthly Attendance Calendar <span class="text-muted fw-normal small">(unmarked days are not treated as absent)</span></div>
@php
 $monthStart = \Carbon\Carbon::parse($month.'-01');
 $monthEnd = $monthStart->copy()->endOfMonth();
 $dailyStatuses = $attendance->filter(fn($r) => $r->attendanceSession?->attendance_date)->keyBy(fn($r) => $r->attendanceSession->attendance_date->format('Y-m-d'));
@endphp
<div class="table-responsive"><table class="table table-bordered text-center align-middle" style="table-layout:fixed;min-width:480px"><thead class="table-light"><tr>@foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)<th class="small">{{ $day }}</th>@endforeach</tr></thead><tbody>
@for($weekStart = $monthStart->copy()->startOfWeek(); $weekStart->lte($monthEnd); $weekStart->addWeek())
<tr>@for($offset=0;$offset<7;$offset++)
@php
 $date = $weekStart->copy()->addDays($offset);
 $withinMonth = $date->format('Y-m') === $month;
 $record = $withinMonth ? $dailyStatuses->get($date->format('Y-m-d')) : null;
 $status = strtolower((string) ($record?->attendance_status ?? ''));
 $statusClass = $status === 'present' ? 'bg-success-subtle text-success-emphasis' : ($status === 'absent' ? 'bg-danger-subtle text-danger-emphasis' : ($status === 'late' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-light text-muted'));
@endphp
<td class="p-1 {{ $withinMonth ? '' : 'text-muted bg-light' }}"><div class="small">{{ $date->day }}</div>@if($withinMonth && $record)<span class="badge {{ $statusClass }}" title="{{ ucfirst($status) }}">{{ strtoupper(substr($status,0,1)) }}</span>@endif</td>
@endfor</tr>
@endfor
</tbody></table></div></div><div class="table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>Date</th><th>Status</th><th>Remarks</th></tr></thead><tbody>@forelse($attendance as $a)<tr><td>{{ $a->attendanceSession?->attendance_date?->format('d M Y') ?? '—' }}</td><td>{{ ucfirst($a->attendance_status) }}</td><td>{{ $a->remarks ?: '—' }}</td></tr>@empty<tr><td colspan="3" class="text-muted text-center">No attendance records for this month.</td></tr>@endforelse</tbody></table></div></div></div>
</div>
@if($canViewStudentFees)
<div class="tab-pane fade " id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
@canany(['fee-collection.view','fee-structure.view','fee-reports.view','student-fee.view'])<div class="card shadow-sm mb-3"><div class="card-header"><strong>Student Fee Dues</strong></div><div class="card-body"><div class="mb-3">Outstanding balance: <strong>₹{{ number_format((float)$feeDues->sum('balance_amount'),2) }}</strong></div><div class="table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>Fee Head</th><th>Installment</th><th>Due Date</th><th>Payable</th><th>Paid</th><th>Balance</th><th>Status</th></tr></thead><tbody>@forelse($feeDues as $due)<tr><td>{{ $due->fee_head_name }}</td><td>{{ $due->installment_name }}</td><td>{{ $due->due_date?->format('d M Y') }}</td><td>{{ number_format((float)$due->payable_amount,2) }}</td><td>{{ number_format((float)$due->paid_amount,2) }}</td><td>{{ number_format((float)$due->balance_amount,2) }}</td><td>{{ $due->payment_status }}</td></tr>@empty<tr><td colspan="7" class="text-center text-muted">No fee dues found.</td></tr>@endforelse</tbody></table></div></div></div><div class="card shadow-sm"><div class="card-header"><strong>Payment History</strong></div><div class="card-body table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>Receipt</th><th>Date</th><th>Amount</th><th>Mode</th><th>Status</th></tr></thead><tbody>@forelse($feePayments as $payment)<tr><td>{{ $payment->receipt_no }}</td><td>{{ $payment->payment_date?->format('d M Y') }}</td><td>₹{{ number_format((float)$payment->total_amount,2) }}</td><td>{{ $payment->payment_mode }}</td><td>{{ $payment->status }}</td></tr>@empty<tr><td colspan="5" class="text-muted text-center">No payments found.</td></tr>@endforelse</tbody></table></div></div>@else<div class="alert alert-warning">You do not have permission to view student financial records.</div>@endcanany
</div>
@endif
<div class="tab-pane fade " id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
<div class="alert alert-info">Academic results and report cards require integration with the examination/results module. No results are fabricated here.</div>
</div>
<div class="tab-pane fade " id="tab4" role="tabpanel" aria-labelledby="tab4-tab">
<div class="card shadow-sm"><div class="card-header"><strong>Academic Enrollment & Promotion History</strong></div><div class="card-body table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>Session</th><th>Class</th><th>Section</th><th>Roll</th><th>Enrollment Date</th><th>Current</th></tr></thead><tbody>@forelse($student->enrollments as $e)<tr><td>{{ $e->academicYear?->name }}</td><td>{{ $e->schoolClass?->name }}</td><td>{{ $e->section?->name }}</td><td>{{ $e->roll_no }}</td><td>{{ $e->enrollment_date?->format('d M Y') }}</td><td>{{ $e->is_current ? 'Yes' : 'No' }}</td></tr>@empty<tr><td colspan="6" class="text-center text-muted">No enrollment history found.</td></tr>@endforelse</tbody></table></div></div>
</div>
@if($canViewStudentDocuments)
<div class="tab-pane fade " id="tab5" role="tabpanel" aria-labelledby="tab5-tab">
@canany(['student-document.view','student-document.index','student.view'])<div class="card shadow-sm"><div class="card-header"><strong>Student Documents</strong></div><div class="card-body table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>Type</th><th>Name</th><th>Remarks</th><th>File</th></tr></thead><tbody>@forelse($documents as $doc)<tr><td>{{ $doc->document_type }}</td><td>{{ $doc->document_name }}</td><td>{{ $doc->remarks }}</td><td>@if($doc->file_path)<a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" rel="noopener">Open</a>@else — @endif</td></tr>@empty<tr><td colspan="4" class="text-muted text-center">No documents uploaded.</td></tr>@endforelse</tbody></table></div></div>@else<div class="alert alert-warning">No permission to view documents.</div>@endcanany
</div>
@endif
<div class="tab-pane fade " id="tab6" role="tabpanel" aria-labelledby="tab6-tab">
<div class="alert alert-info">Leave applications and absence reasons can be connected when a student leave register is available.</div>
</div>
<div class="tab-pane fade " id="tab7" role="tabpanel" aria-labelledby="tab7-tab">
<div class="card shadow-sm"><div class="card-header"><strong>Transport & Pickup</strong></div><div class="card-body"><p>Transport required: <strong>{{ data_get($extraDetails,'transport_required') ?: '—' }}</strong></p><p>Pickup person: {{ data_get($extraDetails,'pickup_person_name') ?: '—' }}</p><p>Pickup mobile: {{ data_get($extraDetails,'pickup_person_mobile') ?: '—' }}</p></div></div>
</div>
<div class="tab-pane fade " id="tab8" role="tabpanel" aria-labelledby="tab8-tab">
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><strong>Emergency, Medical & Pickup Information</strong></div><div class="card-body"><div class="row g-3">
<div class="col-md-3"><div class="text-muted small mb-1">Emergency Contact Name</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'emergency_contact_name') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Emergency Contact Mobile</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'emergency_contact_mobile') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Emergency Contact Relation</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'emergency_contact_relation') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Medical Allergies</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'medical_allergies') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Medical Conditions</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'medical_conditions') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Transport Required</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'transport_required') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Pickup Person Name</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'pickup_person_name') ?: '—' }}</div></div>
<div class="col-md-3"><div class="text-muted small mb-1">Pickup Person Mobile</div><div class="fw-medium text-break">{{ data_get($extraDetails, 'pickup_person_mobile') ?: '—' }}</div></div>
</div></div></div>

</div>
<div class="tab-pane fade " id="tab9" role="tabpanel" aria-labelledby="tab9-tab">
<div class="alert alert-info">Activities and achievements will appear here once the corresponding records are connected.</div>
</div>
<div class="tab-pane fade " id="tab10" role="tabpanel" aria-labelledby="tab10-tab">
<div class="alert alert-info">Parent communication will appear here once the corresponding records are connected.</div>
</div>
<div class="tab-pane fade " id="tab11" role="tabpanel" aria-labelledby="tab11-tab">
<div class="card shadow-sm"><div class="card-header"><strong>Student Timeline</strong></div><div class="card-body"><ul class="list-group"><li class="list-group-item">Student record created: {{ $student->created_at?->format('d M Y H:i') ?? '—' }}</li>@foreach($student->enrollments as $e)<li class="list-group-item">Enrollment: {{ $e->academicYear?->name }} — {{ $e->schoolClass?->name }} / {{ $e->section?->name }} ({{ $e->enrollment_date?->format('d M Y') ?? '—' }})</li>@endforeach</ul><div class="text-muted small mt-3">This timeline shows available enrollment events. A complete change-by-change audit log requires an activity logging module.</div></div></div>
</div>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const root = document.getElementById('student360Tabs');
  if (!root || !window.bootstrap || !window.bootstrap.Tab) return;
  const allowed = new Set(Array.from(root.querySelectorAll('[data-bs-toggle="tab"]')).map(el => el.getAttribute('data-bs-target').slice(1)));
  function openTab(id) {
    if (!allowed.has(id)) return;
    const trigger = document.getElementById(id + '-tab');
    if (trigger) window.bootstrap.Tab.getOrCreateInstance(trigger).show();
  }
  const requested = new URLSearchParams(window.location.search).get('profile_tab') || window.location.hash.replace('#', '');
  if (requested) openTab(requested);
  root.addEventListener('shown.bs.tab', function (event) {
    const id = event.target.getAttribute('data-bs-target').slice(1);
    if (history.replaceState) history.replaceState(null, '', window.location.pathname + window.location.search + '#' + id);
  });
  document.querySelectorAll('[data-student-tab]').forEach(link => link.addEventListener('click', function (event) {
    event.preventDefault();
    openTab(link.dataset.studentTab);
  }));
});
</script>
@endsection
