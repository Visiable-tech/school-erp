@extends('layouts.admin')
@section('content')
<style>
/* All Modules: vivid, responsive, isolated from existing ERP sidebar styles */
.erp-modules-page{--tile-shadow:0 7px 19px rgba(24,35,75,.14);padding:24px 12px 40px;background:linear-gradient(135deg,#f1f5ff 0%,#fff3f5 50%,#effbf7 100%);min-height:calc(100vh - 65px)}
.erp-modules-header{background:#ffffffd9;border:1px solid #e6eaf3;border-radius:18px;padding:20px 24px;box-shadow:0 4px 20px rgba(20,35,70,.05)}
.erp-module-search{max-width:350px;border-radius:30px;padding:12px 18px;border:1px solid #dce3ee}
.erp-module-grid{--bs-gutter-x:1rem;--bs-gutter-y:1rem}
.erp-module-tile{width:20%}
.erp-module-card{--tile-a:#5b5bd6;--tile-b:#8179ec;position:relative;isolation:isolate;min-height:152px;padding:22px 10px;border:0;border-radius:17px;background:linear-gradient(135deg,var(--tile-a),var(--tile-b));color:#fff!important;text-align:center;font-weight:700;box-shadow:var(--tile-shadow);transition:transform .2s ease,box-shadow .2s ease,filter .2s ease;overflow:hidden}
.erp-module-card::before{content:'';position:absolute;inset:-50% -50% auto auto;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.12);pointer-events:none}
.erp-module-card::after{content:'';position:absolute;bottom:-65px;left:-35px;width:140px;height:140px;border-radius:50%;background:rgba(0,0,0,.055);pointer-events:none}
.erp-module-card:hover,.erp-module-card:focus-visible{transform:translateY(-5px);box-shadow:0 14px 28px rgba(24,35,75,.22);filter:saturate(1.12);color:#fff!important}
.erp-module-card:focus-visible{outline:3px solid #1f2937;outline-offset:3px}
.erp-module-icon{font-size:2.45rem;margin-bottom:14px;color:#fff;line-height:1;filter:drop-shadow(0 2px 3px rgba(0,0,0,.14))}
.erp-module-card span{position:relative;z-index:1;font-size:13px;line-height:1.4;max-width:100%;overflow-wrap:anywhere}
.erp-module-tile:nth-child(12n + 1) .erp-module-card{--tile-a:#6656c8;--tile-b:#8d70e5}
.erp-module-tile:nth-child(12n + 2) .erp-module-card{--tile-a:#0eac69;--tile-b:#2bd483}
.erp-module-tile:nth-child(12n + 3) .erp-module-card{--tile-a:#2277b9;--tile-b:#52a3e3}
.erp-module-tile:nth-child(12n + 4) .erp-module-card{--tile-a:#364259;--tile-b:#596783}
.erp-module-tile:nth-child(12n + 5) .erp-module-card{--tile-a:#f47b36;--tile-b:#ffab4d}
.erp-module-tile:nth-child(12n + 6) .erp-module-card{--tile-a:#c21250;--tile-b:#f04476}
.erp-module-tile:nth-child(12n + 7) .erp-module-card{--tile-a:#7051c3;--tile-b:#a07be8}
.erp-module-tile:nth-child(12n + 8) .erp-module-card{--tile-a:#0e9a9c;--tile-b:#32c6c1}
.erp-module-tile:nth-child(12n + 9) .erp-module-card{--tile-a:#db5a31;--tile-b:#f78e52}
.erp-module-tile:nth-child(12n + 10) .erp-module-card{--tile-a:#3d69c4;--tile-b:#6a98e7}
.erp-module-tile:nth-child(12n + 11) .erp-module-card{--tile-a:#15875d;--tile-b:#3bbd8b}
.erp-module-tile:nth-child(12n + 12) .erp-module-card{--tile-a:#b43f9c;--tile-b:#e273c6}
@media(max-width:1199.98px){.erp-module-tile{width:25%}}
@media(max-width:991.98px){.erp-module-tile{width:33.3333%}}
@media(max-width:575.98px){.erp-module-tile{width:50%}.erp-module-card{min-height:130px;padding:16px 8px}.erp-module-icon{font-size:2rem;margin-bottom:10px}.erp-modules-header{padding:15px}}
@media(prefers-reduced-motion:reduce){.erp-module-card{transition:none}.erp-module-card:hover{transform:none}}
</style>
<div class="container-fluid erp-modules-page">
    <div class="erp-modules-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h3 class="mb-1">All Modules</h3><div class="text-muted">Select a module to open its existing dashboard or workspace.</div></div>
        <input type="search" id="erpModuleSearch" class="form-control erp-module-search" placeholder="Search modules..." aria-label="Search modules">
    </div>
    <div class="row erp-module-grid" id="erpModuleGrid">
        @if((auth()->user()?->can('academic-year.view') ?? false) && \Illuminate\Support\Facades\Route::has('academic-years.index'))
            <div class="erp-module-tile">
                <a href="{{ route('academic-years.index') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-gear erp-module-icon"></i>
                    <span>Academic Setup</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyPermission(['admission-application.view','admission-candidate.view']) ?? false) && \Illuminate\Support\Facades\Route::has('admission.dashboard'))
            <div class="erp-module-tile">
                <a href="{{ route('admission.dashboard') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-person-plus erp-module-icon"></i>
                    <span>Admission Management</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->can('student.view') ?? false) && \Illuminate\Support\Facades\Route::has('student-information.dashboard'))
            <div class="erp-module-tile">
                <a href="{{ route('student-information.dashboard') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-people erp-module-icon"></i>
                    <span>Student Information System</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->can('fee-head.view') ?? false) && \Illuminate\Support\Facades\Route::has('fees.dashboard'))
            <div class="erp-module-tile">
                <a href="{{ route('fees.dashboard') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-cash-stack erp-module-icon"></i>
                    <span>Fee Management</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('hr-payroll.attendance.daily'))
            <div class="erp-module-tile">
                <a href="{{ route('hr-payroll.attendance.daily') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-wallet2 erp-module-icon"></i>
                    <span>HR Payroll / Attendance</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.dashboard'))
            <div class="erp-module-tile">
                <a href="{{ route('teacher.dashboard') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-person-workspace erp-module-icon"></i>
                    <span>Teacher Portal</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.classwork.index'))
            <div class="erp-module-tile">
                <a href="{{ route('teacher.classwork.index') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-journal-text erp-module-icon"></i>
                    <span>Classwork / Assignments</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.examination.index'))
            <div class="erp-module-tile">
                <a href="{{ route('teacher.examination.index') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-journal-check erp-module-icon"></i>
                    <span>Examination Management</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.schedule.lessons'))
            <div class="erp-module-tile">
                <a href="{{ route('teacher.schedule.lessons') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-calendar-week erp-module-icon"></i>
                    <span>Lesson Planning</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.schedule.timetable'))
            <div class="erp-module-tile">
                <a href="{{ route('teacher.schedule.timetable') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-clock erp-module-icon"></i>
                    <span>Teacher Timetable</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.attendance.index'))
            <div class="erp-module-tile">
                <a href="{{ route('teacher.attendance.index') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-calendar-check erp-module-icon"></i>
                    <span>Student Attendance</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.communication.inbox'))
            <div class="erp-module-tile">
                <a href="{{ route('teacher.communication.inbox') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-chat-dots erp-module-icon"></i>
                    <span>Communications</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.library.search'))
            <div class="erp-module-tile">
                <a href="{{ route('teacher.library.search') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-book erp-module-icon"></i>
                    <span>Library</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.learning.resources'))
            <div class="erp-module-tile">
                <a href="{{ route('teacher.learning.resources') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-mortarboard erp-module-icon"></i>
                    <span>E-Learning</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.operations.inventory'))
            <div class="erp-module-tile">
                <a href="{{ route('teacher.operations.inventory') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-box-seam erp-module-icon"></i>
                    <span>Inventory Management</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.operations.tickets'))
            <div class="erp-module-tile">
                <a href="{{ route('teacher.operations.tickets') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-ticket erp-module-icon"></i>
                    <span>Ticketing / Tasks</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.operations.gallery'))
            <div class="erp-module-tile">
                <a href="{{ route('teacher.operations.gallery') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-images erp-module-icon"></i>
                    <span>Image Gallery</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.performance.team'))
            <div class="erp-module-tile">
                <a href="{{ route('teacher.performance.team') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-clipboard-data erp-module-icon"></i>
                    <span>Employee Assessment</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('hr-payroll.processing.runs'))
            <div class="erp-module-tile">
                <a href="{{ route('hr-payroll.processing.runs') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-receipt erp-module-icon"></i>
                    <span>Salary Processing</span>
                </a>
            </div>
        @endif
        @if((auth()->user()?->hasAnyRole(['Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('hr-payroll.processing.advances'))
            <div class="erp-module-tile">
                <a href="{{ route('hr-payroll.processing.advances') }}" class="erp-module-card text-decoration-none d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-currency-rupee erp-module-icon"></i>
                    <span>Salary Advances</span>
                </a>
            </div>
        @endif
    </div>
    <p id="erpModuleEmpty" class="text-muted mt-4 d-none">No modules are assigned to this account, or no modules match your search. Contact the administrator to review your permissions.</p>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('erpModuleSearch');
    const tiles = Array.from(document.querySelectorAll('#erpModuleGrid > div'));
    const empty = document.getElementById('erpModuleEmpty');
    function filter() {
        const q = search.value.trim().toLowerCase();
        let visible = 0;
        tiles.forEach(tile => { const match = tile.textContent.toLowerCase().includes(q); tile.classList.toggle('d-none', !match); if (match) visible++; });
        empty.classList.toggle('d-none', visible !== 0);
    }
    search.addEventListener('input', filter);
    filter();
});
</script>
@endsection
