

@if(!request()->is('dashboard'))
    <header class="admin-header">
@else 
    <header class="admin-header" style="left:0;">
@endif

    <div class="container-fluid">

        <div class="d-flex
                    align-items-center
                    justify-content-between">

            <div class="d-flex align-items-center">

                <button
                    id="sidebarToggle"
                    class="btn btn-light me-3 d-lg-none">

                    <i class="bi bi-list"></i>

                </button>


                <div>

                    <div class="dropdown d-inline-block me-2">
<button class="btn btn-light dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-grid-3x3-gap-fill"></i> All Modules</button>
<div class="dropdown-menu shadow p-2" style="max-height:70vh;overflow-y:auto;min-width:260px">
<a class="dropdown-item" href="{{ route('modules.index') }}">View all modules</a><hr class="dropdown-divider">
@if((auth()->user()?->can('academic-year.view') ?? false) && \Illuminate\Support\Facades\Route::has('academic-years.index'))
<a class="dropdown-item" href="{{ route('academic-years.index') }}"><i class="bi bi-gear me-2"></i>Academic Setup</a>
@endif
@if((auth()->user()?->hasAnyPermission(['admission-application.view','admission-candidate.view']) ?? false) && \Illuminate\Support\Facades\Route::has('admission.dashboard'))
<a class="dropdown-item" href="{{ route('admission.dashboard') }}"><i class="bi bi-person-plus me-2"></i>Admission Management</a>
@endif
@if((auth()->user()?->can('front-office.view') ?? false) && \Illuminate\Support\Facades\Route::has('front-office.index'))
<a class="dropdown-item" href="{{ route('front-office.index') }}"><i class="bi bi-people me-2"></i>Front Office Management</a>
@endif
@if((auth()->user()?->can('student.view') ?? false) && \Illuminate\Support\Facades\Route::has('student-information.dashboard'))
<a class="dropdown-item" href="{{ route('student-information.dashboard') }}"><i class="bi bi-people me-2"></i>Student Information System</a>
@endif
@php
    $financeLanding = null;
    if ((auth()->user()?->can('fee-head.view') ?? false) && \Illuminate\Support\Facades\Route::has('fees.dashboard')) {
        $financeLanding = 'fees.dashboard';
    } elseif ((auth()->user()?->can('accounts.view') ?? false) && \Illuminate\Support\Facades\Route::has('accounts.index')) {
        $financeLanding = 'accounts.index';
    }
@endphp
@if($financeLanding)
<a class="dropdown-item" href="{{ route($financeLanding) }}"><i class="bi bi-cash-stack me-2"></i>Accounts &amp; Fee Management</a>
@endif

@if((auth()->user()?->hasAnyRole(['Admin','Super Admin']) ?? false) && \Illuminate\Support\Facades\Route::has('transport.index'))
<a class="dropdown-item" href="{{ route('transport.dashboard') }}"><i class="bi bi-bus-front me-2"></i>Transport Management</a>
@endif
@if((auth()->user()?->hasAnyRole(['Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('employee-info.dashboard'))
<a class="dropdown-item" href="{{ route('employee-info.dashboard') }}"><i class="bi bi-person-vcard me-2"></i>Employee Information System</a>
@endif
@if((auth()->user()?->hasAnyRole(['Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('hr-payroll.attendance.daily'))
<a class="dropdown-item" href="{{ route('hr-payroll.attendance.daily') }}"><i class="bi bi-wallet2 me-2"></i>HR Payroll / Attendance</a>
@endif
@if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.dashboard'))
<a class="dropdown-item" href="{{ route('teacher.dashboard') }}"><i class="bi bi-person-workspace me-2"></i>Teacher Portal</a>
@endif
@if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.classwork.index'))
<a class="dropdown-item" href="{{ route('teacher.classwork.index') }}"><i class="bi bi-journal-text me-2"></i>Classwork / Assignments</a>
@endif
@if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.examination.index'))
<a class="dropdown-item" href="{{ route('teacher.examination.index') }}"><i class="bi bi-journal-check me-2"></i>Examination Management</a>
@endif
@if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.schedule.lessons'))
<a class="dropdown-item" href="{{ route('teacher.schedule.lessons') }}"><i class="bi bi-calendar-week me-2"></i>Lesson Planning</a>
@endif
@if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.schedule.timetable'))
<a class="dropdown-item" href="{{ route('teacher.schedule.timetable') }}"><i class="bi bi-clock me-2"></i>Teacher Timetable</a>
@endif
@if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.attendance.index'))
<a class="dropdown-item" href="{{ route('teacher.attendance.index') }}"><i class="bi bi-calendar-check me-2"></i>Student Attendance</a>
@endif
@if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.communication.inbox'))
<a class="dropdown-item" href="{{ route('teacher.communication.inbox') }}"><i class="bi bi-chat-dots me-2"></i>Communications</a>
@endif
@if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.library.search'))
<a class="dropdown-item" href="{{ route('teacher.library.search') }}"><i class="bi bi-book me-2"></i>Library</a>
@endif
@if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.learning.resources'))
<a class="dropdown-item" href="{{ route('teacher.learning.resources') }}"><i class="bi bi-mortarboard me-2"></i>E-Learning</a>
@endif
@if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.operations.inventory'))
<a class="dropdown-item" href="{{ route('teacher.operations.inventory') }}"><i class="bi bi-box-seam me-2"></i>Inventory Management</a>
@endif
@if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.operations.tickets'))
<a class="dropdown-item" href="{{ route('teacher.operations.tickets') }}"><i class="bi bi-ticket me-2"></i>Ticketing / Tasks</a>
@endif
@if((auth()->user()?->hasAnyRole(['Teacher','Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.operations.gallery'))
<a class="dropdown-item" href="{{ route('teacher.operations.gallery') }}"><i class="bi bi-images me-2"></i>Image Gallery</a>
@endif
@if((auth()->user()?->hasAnyRole(['Admin','Super Admin'])) && \Illuminate\Support\Facades\Route::has('teacher.performance.team'))
<a class="dropdown-item" href="{{ route('teacher.performance.team') }}"><i class="bi bi-clipboard-data me-2"></i>Employee Assessment</a>
@endif
</div></div>

                    <a href="/dashboard"><img src="/assets/images/header_logo.png" style="height:auto; width:30%"></a>

                    

                </div>

            </div>


            <div class="dropdown">

                <button
                    class="btn dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown">

                    <i class="bi bi-person-circle me-1"></i>

                    {{ auth()->user()?->name ?? 'ERP User' }}

                </button>


                <ul class="dropdown-menu dropdown-menu-end">

                    <li>

                        <span class="dropdown-item-text">

                            <small class="text-muted">
                                Role
                            </small>

                            <br>

                            <strong>
                                {{ auth()->user()?->getRoleNames()->implode(', ') ?? 'Guest' }}
                            </strong>

                        </span>

                    </li>


                    <li>
                        <hr class="dropdown-divider">
                    </li>


                    <li>

                        <form
                            method="POST"
                            action="{{ route('logout') }}">

                            @csrf

                            <button
                                type="submit"
                                class="dropdown-item text-danger">

                                <i class="bi bi-box-arrow-right me-2"></i>

                                Logout

                            </button>

                        </form>

                    </li>

                </ul>

            </div>

        </div>

    </div>

</header>