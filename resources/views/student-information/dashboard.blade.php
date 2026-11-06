@extends('layouts.admin')
@section('title','Student Information Dashboard')
@push('styles')
<style>
.sis-dashboard .sis-head{display:flex;justify-content:space-between;align-items:end;gap:18px;flex-wrap:wrap;margin-bottom:18px}
.sis-dashboard .sis-subtitle{font-size:12px;color:#7d8793}
.sis-dashboard .sis-filter{display:flex;align-items:end;gap:9px;flex-wrap:wrap}
.sis-dashboard .sis-filter label{font-size:11px;color:#6d7582;display:block;margin-bottom:4px}
.sis-dashboard .sis-filter .form-select,.sis-dashboard .sis-filter .form-control{min-width:135px;font-size:12px}
.sis-dashboard .stat-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin-bottom:16px}
.sis-dashboard .stat{min-height:92px;padding:16px 13px;border-radius:10px;color:white;display:flex;align-items:center;gap:12px;box-shadow:0 5px 14px rgba(36,54,92,.10)}
.sis-dashboard .stat .ico{font-size:25px;background:#ffffff28;border-radius:9px;width:43px;height:43px;display:grid;place-items:center}
.sis-dashboard .stat small{font-size:11px;opacity:.95;display:block}.sis-dashboard .stat strong{font-size:24px;line-height:1.3}
.sis-dashboard .stat-blue{background:linear-gradient(120deg,#62a5ff,#3579ee)}
.sis-dashboard .stat-green{background:linear-gradient(120deg,#48d987,#18bf65)}
.sis-dashboard .stat-orange{background:linear-gradient(120deg,#ffc53b,#f6a100)}
.sis-dashboard .stat-purple{background:linear-gradient(120deg,#ae88ff,#8053ed)}
.sis-dashboard .stat-cyan{background:linear-gradient(120deg,#65e8f8,#19b9dc)}
.sis-dashboard .sis-layout{display:grid;grid-template-columns:minmax(0,3fr) minmax(270px,1fr);gap:16px}
.sis-dashboard .sis-panel{background:#fff;border:1px solid #e6eaf0;border-radius:10px;box-shadow:0 2px 8px #1b34560b;overflow:hidden;margin-bottom:16px}
.sis-dashboard .sis-panel-head{padding:13px 15px;border-bottom:1px solid #edf0f5;font-weight:600;color:#334155;display:flex;justify-content:space-between}
.sis-dashboard .sis-panel-body{padding:15px}
.sis-dashboard .chart-wrap{height:320px;position:relative}
.sis-dashboard .cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center}
.sis-dashboard .cal-grid .day-name{font-size:11px;font-weight:700;color:#44719c;padding:8px 0}
.sis-dashboard .cal-grid .day{padding:8px 0;border-radius:5px;font-size:12px;color:#334155;position:relative}
.sis-dashboard .cal-grid .day.muted{opacity:.28}.sis-dashboard .cal-grid .day.today{background:#fff0cb}
.sis-dashboard .cal-grid .day.event:after{content:'';width:5px;height:5px;background:#ef5477;border-radius:50%;position:absolute;bottom:2px;left:calc(50% - 2px)}
@media(max-width:1200px){.sis-dashboard .stat-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:900px){.sis-dashboard .sis-layout{grid-template-columns:1fr}}
@media(max-width:600px){.sis-dashboard .stat-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.sis-dashboard .sis-filter{width:100%}.sis-dashboard .sis-filter>*{flex:1}.sis-dashboard .stat strong{font-size:19px}}
</style>
@endpush
@section('content')
<div class="container-fluid sis-dashboard py-3">
<div class="sis-head"><div><h3 class="mb-1 fw-light">Student Information Dashboard</h3><div class="sis-subtitle">Student Information System / Dashboard</div></div>
<form class="sis-filter" method="GET" action="{{ route('student-information.dashboard') }}">
<div><label>Academic Year</label><select name="academic_year_id" class="form-select">@foreach($years as $year)<option value="{{ $year->id }}" @selected($yearId==$year->id)>{{ $year->name }}</option>@endforeach</select></div>
<div><label>Date</label><input type="date" name="date" value="{{ $date }}" class="form-control"></div>
<div><label>Student Status</label><select name="student_status" class="form-select"><option value="active" @selected($status==='active')>Active</option><option value="inactive" @selected($status==='inactive')>Inactive</option><option value="all" @selected($status==='all')>All</option></select></div>
<div><button class="btn btn-danger" type="submit"><i class="bi bi-funnel"></i> Apply</button></div>
</form></div>
<div class="stat-grid">
<div class="stat stat-blue"><span class="ico"><i class="bi bi-people-fill"></i></span><div><small>Total Students</small><strong>{{ number_format($totalStudents) }}</strong></div></div>
<div class="stat stat-green"><span class="ico"><i class="bi bi-person-badge-fill"></i></span><div><small>Assigned Teachers</small><strong>{{ $totalTeachers===null?'—':number_format($totalTeachers) }}</strong></div></div>
<div class="stat stat-orange"><span class="ico"><i class="bi bi-journal-text"></i></span><div><small>Classes With Enrollments</small><strong>{{ number_format($totalClasses) }}</strong></div></div>
<div class="stat stat-purple"><span class="ico"><i class="bi bi-person-check-fill"></i></span><div><small>Present Students</small><strong>{{ number_format($presentStudents) }}</strong></div></div>
<div class="stat stat-cyan"><span class="ico"><i class="bi bi-people"></i></span><div><small>Present Employees</small><strong>{{ $presentEmployees===null?'—':number_format($presentEmployees) }}</strong></div></div>
</div>
<div class="sis-layout"><div>
<div class="sis-panel"><div class="sis-panel-head">Class Student Overview <span class="text-muted small">{{ number_format($totalStudents) }} students</span></div><div class="sis-panel-body"><div class="text-center mb-2">Gender Wise Student Count</div><div class="chart-wrap"><canvas id="sisGenderChart"></canvas></div></div></div>
<div class="sis-panel"><div class="sis-panel-head">Attendance Overview <span class="text-muted small">{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</span></div><div class="sis-panel-body"><div class="text-center mb-2">Section Wise Attendance</div><div class="chart-wrap"><canvas id="sisAttendanceChart"></canvas></div></div></div>
</div><div>
<div class="sis-panel"><div class="sis-panel-head">School Calendar</div><div class="sis-panel-body"><div class="d-flex justify-content-between align-items-center mb-3"><strong id="sisCalendarTitle"></strong><div><button class="btn btn-sm btn-outline-secondary" type="button" id="sisPrevMonth" aria-label="Previous month">‹</button><button class="btn btn-sm btn-outline-secondary" type="button" id="sisNextMonth" aria-label="Next month">›</button></div></div><div id="sisCalendar" class="cal-grid"></div><div class="small text-muted mt-3">Dot indicates a school event</div></div></div>
<div class="sis-panel"><div class="sis-panel-head">Month Events</div><div class="sis-panel-body" id="sisEventList"></div></div>
</div></div>
</div>
@endsection
@push('scripts')
<script>
(function(){
const gender=@json($genderRows);
const attendance=@json($attendanceRows);
const events=@json($events);
const selectedDate=@json($date);
if(window.Chart){
 const base={responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}};
 new Chart(document.getElementById('sisGenderChart'),{type:'bar',data:{labels:gender.map(x=>x.name),datasets:[{label:'Boys',data:gender.map(x=>Number(x.boys)),backgroundColor:'#3677e9',borderRadius:3},{label:'Girls',data:gender.map(x=>Number(x.girls)),backgroundColor:'#f5a623',borderRadius:3}]},options:base});
 new Chart(document.getElementById('sisAttendanceChart'),{type:'bar',data:{labels:attendance.map(x=>x.class_name+' - '+x.section_name),datasets:[{label:'Absent',data:attendance.map(x=>Number(x.absent_count)),backgroundColor:'#ed654b'},{label:'Leave',data:attendance.map(x=>Number(x.leave_count)),backgroundColor:'#3984ee'},{label:'Others',data:attendance.map(x=>Number(x.other_count)),backgroundColor:'#e0a527'},{label:'Present',data:attendance.map(x=>Number(x.present_count)),backgroundColor:'#36b47b'}]},options:base});
}
let cursor=new Date(selectedDate+'T12:00:00');
const grid=document.getElementById('sisCalendar'),title=document.getElementById('sisCalendarTitle'),eventList=document.getElementById('sisEventList');
function localDate(d){return [d.getFullYear(),String(d.getMonth()+1).padStart(2,'0'),String(d.getDate()).padStart(2,'0')].join('-')}
function render(){
 const year=cursor.getFullYear(),month=cursor.getMonth(),first=new Date(year,month,1),last=new Date(year,month+1,0);
 title.textContent=first.toLocaleString('en-IN',{month:'long',year:'numeric'});
 grid.replaceChildren();
 ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(s=>{let el=document.createElement('div');el.className='day-name';el.textContent=s;grid.append(el)});
 const days=events.filter(e=>{let start=String(e.start_at).slice(0,10),end=String(e.end_at).slice(0,10);return start<=localDate(last)&&end>=localDate(first)});
 for(let i=0;i<first.getDay();i++){let el=document.createElement('div');el.className='day muted';grid.append(el)}
 for(let n=1;n<=last.getDate();n++){
  const d=new Date(year,month,n),iso=localDate(d),el=document.createElement('div');el.className='day';el.textContent=n;
  if(iso===selectedDate)el.classList.add('today');
  if(days.some(e=>String(e.start_at).slice(0,10)<=iso&&String(e.end_at).slice(0,10)>=iso))el.classList.add('event');
  grid.append(el);
 }
 eventList.replaceChildren();
 if(!days.length){let el=document.createElement('p');el.className='text-muted text-center my-4';el.textContent='No events available';eventList.append(el)}
 else days.forEach(e=>{let div=document.createElement('div');div.className='border-bottom py-2';let strong=document.createElement('strong');strong.textContent=e.title;let small=document.createElement('div');small.className='small text-muted';small.textContent=String(e.start_at).slice(0,10)+(e.is_holiday?' · Holiday':'');div.append(strong,small);eventList.append(div)});
}
document.getElementById('sisPrevMonth').addEventListener('click',()=>{cursor=new Date(cursor.getFullYear(),cursor.getMonth()-1,1);render()});
document.getElementById('sisNextMonth').addEventListener('click',()=>{cursor=new Date(cursor.getFullYear(),cursor.getMonth()+1,1);render()});render();
})();
</script>
@endpush
