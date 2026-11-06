@extends('layouts.admin')
@section('title','Teacher Attendance Reports')
@section('content')
@php
  $titles=['detail'=>'Attendance Detail Report','percentage'=>'Attendance Percentage Report','absentees'=>'Regular Absentees'];
@endphp
<div class="container-fluid">
 <h4 class="mb-3">{{ $titles[$report] }}</h4>
 <div class="mb-3 d-flex flex-wrap gap-2">
  <a class="btn btn-sm {{ $report==='detail'?'btn-primary':'btn-outline-primary' }}" href="{{ route('teacher.attendance.reports',['report'=>'detail']) }}">Detail</a>
  <a class="btn btn-sm {{ $report==='percentage'?'btn-primary':'btn-outline-primary' }}" href="{{ route('teacher.attendance.reports',['report'=>'percentage']) }}">Percentage</a>
  <a class="btn btn-sm {{ $report==='absentees'?'btn-primary':'btn-outline-primary' }}" href="{{ route('teacher.attendance.reports',['report'=>'absentees']) }}">Regular Absentees</a>
 </div>
 <form method="GET" class="card card-body mb-3"><div class="row g-2 align-items-end">
  <div class="col-md-4"><label class="form-label">Assigned Class / Section</label><select name="assignment_id" class="form-select" required><option value="">Select Assignment</option>@foreach($assignments as $a)<option value="{{ $a->id }}" @selected($selected?->id===$a->id)>{{ $a->academicYear?->name }} — {{ $a->schoolClass?->name }} / {{ $a->section?->name }}</option>@endforeach</select></div>
  <div class="col-md-2"><label class="form-label">From</label><input type="date" name="from" class="form-control" value="{{ $from }}" required></div>
  <div class="col-md-2"><label class="form-label">To</label><input type="date" name="to" class="form-control" value="{{ $to }}" required></div>
  @if($report==='absentees')<div class="col-md-2"><label class="form-label">Below %</label><input type="number" name="threshold" min="1" max="100" class="form-control" value="{{ $threshold }}"></div>@endif
  <div class="col-md-2"><button class="btn btn-primary w-100">Show Report</button></div>
 </div></form>
 @if($selected)
 <div class="alert alert-info">{{ $sessions }} attendance sessions in selected period. Percentage is calculated from <strong>recorded attendance entries</strong> only; Present and Late count as 1, Half Day as 0.5, and Absent/Leave as 0.</div>
 <div class="table-responsive"><table class="table table-bordered table-striped table-hover align-middle">
 <thead class="table-light"><tr><th>Roll No.</th><th>Student</th>
 @if($report==='detail')<th>Date</th><th>Status</th><th>Remarks</th>
 @else<th>Present</th><th>Late</th><th>Half Day</th><th>Absent</th><th>Leave</th><th>Recorded Days</th><th>Attendance %</th>@endif
 </tr></thead><tbody>
 @forelse($students as $student)
 @php $s=$summary[$student->id]??null; $detailRows=$records->where('student_enrollment_id',$student->id); @endphp
 @if($report==='absentees' && ($s['percentage']===null || $s['percentage'] >= $threshold)) @continue @endif
 @if($report==='detail')
   @forelse($detailRows as $record)
   <tr><td>{{ $student->roll_no }}</td><td>{{ $student->student?->full_name ?? $student->student?->name ?? ('Student #'.$student->student_id) }}</td><td>{{ $record->attendance_date }}</td><td>{{ ucfirst(str_replace('_',' ',$record->attendance_status)) }}</td><td>{{ $record->remarks }}</td></tr>
   @empty
   <tr><td>{{ $student->roll_no }}</td><td>{{ $student->student?->full_name ?? $student->student?->name ?? ('Student #'.$student->student_id) }}</td><td colspan="3" class="text-muted">No attendance recorded</td></tr>
   @endforelse
 @else
 <tr><td>{{ $student->roll_no }}</td><td>{{ $student->student?->full_name ?? $student->student?->name ?? ('Student #'.$student->student_id) }}</td><td>{{ $s['present'] }}</td><td>{{ $s['late'] }}</td><td>{{ $s['half_day'] }}</td><td>{{ $s['absent'] }}</td><td>{{ $s['leave'] }}</td><td>{{ $s['marked'] }}</td><td>{{ $s['percentage']===null?'N/A':number_format($s['percentage'],2).'%' }}</td></tr>
 @endif
 @empty<tr><td colspan="9" class="text-center">No students in this section.</td></tr>@endforelse
 </tbody></table></div>
 @else<p class="text-muted">Select an assigned class and date range to view the report.</p>@endif
</div>
@endsection
