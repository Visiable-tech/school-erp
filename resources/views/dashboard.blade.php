@extends('layouts.admin')

@section('content')
<style>
.dashboard-page{background:#f7f9fc;padding:20px}.dash-card{background:#fff;border:1px solid #e8edf3;border-radius:12px;height:100%;box-shadow:0 2px 8px rgba(0,0,0,.03)}.dash-card-body{padding:20px}.stat-title{font-size:13px;color:#667085}.stat-value{font-size:25px;font-weight:700;color:#101828}.chart-header{padding:15px 20px;border-bottom:1px solid #edf0f4;display:flex;align-items:center;justify-content:space-between}.chart-container{height:290px;padding:15px}.muted-value{font-size:15px;color:#98a2b3}.icon-box{width:44px;height:44px;border-radius:10px;display:grid;place-items:center;background:#eaf4ff;color:#1683ff;font-size:20px}.progress{height:9px}.table td,.table th{vertical-align:middle}@media(max-width:767px){.dashboard-page{padding:10px}}
</style>
<div class="dashboard-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div><h4 class="mb-1">School Dashboard</h4><div class="text-muted">Academic session: {{ $yearName }} | Updated on {{ now()->format('d M Y') }}</div></div>
        @if(\Illuminate\Support\Facades\Route::has('modules.index'))
            <a class="btn btn-primary btn-sm" href="{{ route('modules.index') }}"><i class="bi bi-grid me-1"></i> My Modules</a>
        @endif
    </div>

    @hasanyrole('Super Admin|Admin')
    <div class="dash-card mb-3">
        <div class="chart-header"><strong><i class="bi bi-shield-lock me-2"></i>Administration & Access Control</strong></div>
        <div class="dash-card-body d-flex flex-wrap gap-2">
            @if(\Illuminate\Support\Facades\Route::has('access.users.index'))
                <a href="{{ route('access.users.index') }}" class="btn btn-outline-primary btn-sm">User Management</a>
            @endif
            @role('Super Admin')
            @if(\Illuminate\Support\Facades\Route::has('access.roles.index'))
                <a href="{{ route('access.roles.index') }}" class="btn btn-outline-secondary btn-sm">Roles & Permissions</a>
            @endif
            @endrole
        </div>
    </div>
    @endhasanyrole

    <div class="row g-3 mb-3">
        @foreach([
            ['Students', $totalStudents, 'bi-people'],
            ['Enrolled Students', $enrolledStudents, 'bi-person-check'],
            ['Classes', $totalClasses, 'bi-mortarboard'],
            ['Sections', $totalSections, 'bi-diagram-3'],
            ['System Users', $totalUsers, 'bi-person-gear'],
            ['Fees Collected This Month', $collections === null ? null : '₹'.number_format($collections, 2), 'bi-cash-stack'],
            ['Expenses This Month', $expenses === null ? null : '₹'.number_format($expenses, 2), 'bi-receipt'],
            ['Students Present Today', $attendance === null ? null : $attendance['present'], 'bi-calendar-check']
        ] as $stat)
            <div class="col-xl-3 col-md-6"><div class="dash-card"><div class="dash-card-body d-flex align-items-center gap-3">
                <div class="icon-box"><i class="bi {{ $stat[2] }}"></i></div>
                <div><div class="stat-title">{{ $stat[0] }}</div><div class="stat-value {{ $stat[1] === null ? 'muted-value' : '' }}">{{ $stat[1] === null ? 'Not configured' : $stat[1] }}</div></div>
            </div></div></div>
        @endforeach
    </div>

    <div class="row g-3 mb-3">
        <div class="col-xl-7"><div class="dash-card">
            <div class="chart-header"><strong>Monthly Fees Collection & Expenses</strong><small class="text-muted">Last 12 months</small></div>
            <div class="chart-container">
                @if($collections !== null || $expenses !== null)<canvas id="financeChart"></canvas>
                @else <div class="h-100 d-flex justify-content-center align-items-center text-muted text-center">No compatible fee collection or expense tables found. Connect finance tables to enable this chart.</div> @endif
            </div>
        </div></div>
        <div class="col-xl-5"><div class="dash-card">
            <div class="chart-header"><strong>Students by Class</strong><small class="text-muted">{{ $yearName }}</small></div>
            <div class="chart-container">
                @if($classDistribution->isNotEmpty())<canvas id="classChart"></canvas>
                @else <div class="h-100 d-flex justify-content-center align-items-center text-muted">No enrollment data available</div> @endif
            </div>
        </div></div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6"><div class="dash-card">
            <div class="chart-header"><strong>Student Attendance Today</strong><small class="text-muted">{{ now()->format('d M Y') }}</small></div>
            <div class="dash-card-body">
                @if($attendance !== null)
                    @foreach(['present'=>'success','late'=>'warning','absent'=>'danger'] as $status=>$color)
                        @php $percent = $attendance['recorded'] > 0 ? round($attendance[$status] * 100 / $attendance['recorded'], 1) : 0; @endphp
                        <div class="mb-3"><div class="d-flex justify-content-between mb-2"><span class="text-capitalize">{{ $status }}: {{ $attendance[$status] }}</span><span>{{ $percent }}%</span></div><div class="progress"><div class="progress-bar bg-{{ $color }}" style="width:{{ $percent }}%"></div></div></div>
                    @endforeach
                    <small class="text-muted">{{ $attendance['recorded'] }} student attendance records found today.</small>
                @else <div class="text-muted">Student attendance table or columns not configured.</div> @endif
            </div>
        </div></div>
        <div class="col-lg-6"><div class="dash-card">
            <div class="chart-header"><strong>Upcoming School Events</strong><small class="text-muted">School calendar</small></div>
            <div class="dash-card-body">
                @if($events->isNotEmpty())
                    <div class="table-responsive"><table class="table table-sm table-striped mb-0"><thead><tr><th>Date</th><th>Event</th></tr></thead><tbody>
                        @foreach($events as $event)<tr><td>{{ $event['date'] }}</td><td>{{ $event['title'] }}</td></tr>@endforeach
                    </tbody></table></div>
                @else <div class="text-muted">No upcoming events found.</div> @endif
            </div>
        </div></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;
    const months = @json($months);
    const collection = @json($monthlyCollections);
    const expense = @json($monthlyExpenses);
    const finance = document.getElementById('financeChart');
    if (finance) new Chart(finance, {type:'bar',data:{labels:months,datasets:[
        {label:'Fees Collected',data:collection,backgroundColor:'#47c98b',borderRadius:4},
        {label:'Expenses',data:expense,backgroundColor:'#ff9990',borderRadius:4}
    ]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true}}}});
    const classChart = document.getElementById('classChart');
    if (classChart) new Chart(classChart,{type:'bar',data:{labels:@json($classDistribution->pluck('name')),datasets:[{label:'Enrolled Students',data:@json($classDistribution->pluck('total')),backgroundColor:'#1683ff',borderRadius:4}]},options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',plugins:{legend:{display:false}},scales:{x:{beginAtZero:true}}}});
});
</script>
@endsection
