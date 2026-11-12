<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand"><a href="/dashboard"><img src="/assets/images/logo.png" style="width:100%;"></a></div>
    <div class="sidebar-menu">
        <div class="menu-title">Navigation</div>
        <a href="{{ route('dashboard') }}"><i class="bi bi-grid-3x3-gap"></i> Main Dashboard</a>
        @if(\Illuminate\Support\Facades\Route::has('all-modules'))
            <a href="{{ route('all-modules') }}"><i class="bi bi-grid"></i> All Modules</a>
        @endif
        @php
            $routeName = \Illuminate\Support\Facades\Route::currentRouteName() ?? '';
            $path = request()->path();
            $module = 'administration';
            if (str_starts_with($routeName, 'hr-payroll.') || str_starts_with($path, 'hr-payroll/') || str_starts_with($routeName, 'teacher.payroll.') || str_starts_with($routeName, 'teacher.staff-attendance.')) {
                $module = 'hr';
            } elseif (str_starts_with($routeName, 'teacher.') || str_starts_with($routeName, 'teacher-assignments.') || str_starts_with($path, 'teacher/')) {
                $module = 'teacher';
            } elseif (request()->is('fees', 'fees/*') || str_starts_with($routeName, 'fees.') || preg_match('/^(fee-|fee_|fee\.|student-fee|optional-fee|concession-|fine-waiver|composite-concession|cheque-dd)/', $routeName)) {
                $module = 'fees';
            } elseif (str_starts_with($routeName, 'admission-') || str_starts_with($routeName, 'admission.') || str_starts_with($path, 'admission/')) {
                $module = 'students';
            } elseif (str_starts_with($routeName, 'student') || str_starts_with($routeName, 'tc-') || str_starts_with($routeName, 'promotion-statuses.')) {
                $module = 'students';
            } elseif (preg_match('/^(academic-year|school|wing|section|subject|class-subject|section-group|school-calendar)/', $routeName)) {
                $module = 'academic';
            } elseif (preg_match('/^(transport|inventory|communication|ticket|visitor)/', $routeName)) {
                $module = 'operations';
            }
        @endphp
        @php
            $currentUser = auth()->user();
            $permissionNames = $currentUser ? $currentUser->getAllPermissions()->pluck('name') : collect();
            $isAdministrator = $currentUser && $currentUser->hasAnyRole(['Super Admin', 'Admin']);
            $hasStudentMenuPermission = $isAdministrator || $permissionNames->contains(function ($permission) {
                return preg_match('/^(admission[.-]|student\.(?!fee)|student-admission\.|student-document\.|student-enrollment\.|student-promotion\.|student-attendance\.|promotion-status\.|tc-)/', $permission) === 1;
            });
            $hasAcademicMenuPermission = $isAdministrator || $permissionNames->contains(function ($permission) {
                return preg_match('/^(academic-year\.|school\.|wing\.|class\.|section\.|subject\.|class-subject\.|section-group\.|school-calendar\.)/', $permission) === 1;
            });
        @endphp
        @if($module === 'teacher')
            @include('partials.admin.menus.teacher')
        @elseif($module === 'academic')
            @if($hasAcademicMenuPermission)
                @include('partials.admin.menus.academic')
            @endif
        @elseif($module === 'students')
            @if($hasStudentMenuPermission)
                @include('partials.admin.menus.students')
            @endif
        @elseif($module === 'fees')
            @include('partials.admin.menus.fees')
        @elseif($module === 'hr')
            @include('partials.admin.menus.hr')
        @elseif($module === 'operations')
            @include('partials.admin.menus.operations')
        @else
            @include('partials.admin.menus.administration')
        @endif
    </div>
</aside>
