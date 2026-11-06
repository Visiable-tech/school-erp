{{-- HR Payroll / Attendance — Admin and Super Admin only --}}
        @hasanyrole('Admin|Super Admin')
            @php
                $hrOpen = request()->routeIs('hr-payroll.*', 'teacher.staff-attendance.*', 'teacher.payroll.*', 'teacher.performance.team');
                $hrMasterCategories = ['statuses', 'leave-types', 'designations', 'key-responsibility-areas', 'other-income-heads', 'kra-sections'];
                $hrMasterOpen = request()->routeIs('hr-payroll.masters.*') && in_array(request()->route('category'), $hrMasterCategories);
                $hrSetupOpen = request()->routeIs('hr-payroll.masters.*') && !$hrMasterOpen;
                $hrAttOpen = request()->routeIs('hr-payroll.attendance.*', 'teacher.staff-attendance.daily', 'teacher.staff-attendance.monthly', 'teacher.staff-attendance.changes');
                $hrLeaveOpen = request()->routeIs('teacher.staff-attendance.leaves', 'teacher.payroll.balances', 'teacher.payroll.resignations');
                $hrSalaryOpen = request()->routeIs('teacher.payroll.*', 'hr-payroll.processing.*');
            @endphp
            <div class="menu-title">HR Payroll / Attendance</div>
            <a data-bs-toggle="collapse" href="#hrPayrollMain" role="button"
               aria-expanded="{{ $hrOpen ? 'true' : 'false' }}" aria-controls="hrPayrollMain"
               class="{{ $hrOpen ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span class="flex-grow-1">HR Payroll / Attendance</span>
                <i class="bi bi-chevron-down sidebar-arrow"></i>
            </a>
            <div id="hrPayrollMain" class="collapse {{ $hrOpen ? 'show' : '' }}">
                <div class="sidebar-submenu">
                    <a data-bs-toggle="collapse" href="#hrMasters" role="button"
                       aria-expanded="{{ $hrMasterOpen ? 'true' : 'false' }}"
                       class="{{ $hrMasterOpen ? 'active' : '' }}">
                        <i class="bi bi-asterisk me-2"></i><span class="flex-grow-1">Masters</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div id="hrMasters" class="collapse {{ $hrMasterOpen ? 'show' : '' }}">
                        <div class="sidebar-submenu">
                            @foreach(['statuses'=>'Statuses','leave-types'=>'Leave Types','designations'=>'Designations','key-responsibility-areas'=>'Key Responsibility Areas','other-income-heads'=>'Other Income Heads','kra-sections'=>'KRA Sections'] as $slug=>$label)
                                @if(\Illuminate\Support\Facades\Route::has('hr-payroll.masters.index'))
                                    <a href="{{ route('hr-payroll.masters.index', $slug) }}" class="{{ request()->routeIs('hr-payroll.masters.*') && request()->route('category') === $slug ? 'active' : '' }}">{{ $label }}</a>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <a data-bs-toggle="collapse" href="#hrSetup" role="button"
                       aria-expanded="{{ $hrSetupOpen ? 'true' : 'false' }}"
                       class="{{ $hrSetupOpen ? 'active' : '' }}">
                        <i class="bi bi-gear me-2"></i><span class="flex-grow-1">Setup</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div id="hrSetup" class="collapse {{ $hrSetupOpen ? 'show' : '' }}">
                        <div class="sidebar-submenu">
                            @foreach(['financial-years'=>'Financial Years','loan-advance-types'=>'Loan/Adv. Types','shifts'=>'Shifts','leave-policies'=>'Leave Policies','component-groups'=>'Component Groups','salary-components'=>'Salary Components','packages'=>'Packages','configurations'=>'Configuration','leave-approval-process'=>'Leave Approval Process','attendance-change-approval'=>'Att. Ch. Approval Process','kra-groups'=>'KRA Groups','overtime-configurations'=>'Overtime Config.','notification-configurations'=>'Auto Notification Config','variable-compensation-rates'=>'Variable Comp. Rates','increment-groups'=>'Increment Groups'] as $slug=>$label)
                                @if(\Illuminate\Support\Facades\Route::has('hr-payroll.masters.index'))
                                    <a href="{{ route('hr-payroll.masters.index', $slug) }}" class="{{ request()->routeIs('hr-payroll.masters.*') && request()->route('category') === $slug ? 'active' : '' }}">{{ $label }}</a>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <a data-bs-toggle="collapse" href="#hrAttendance" role="button"
                       aria-expanded="{{ $hrAttOpen ? 'true' : 'false' }}"
                       class="{{ $hrAttOpen ? 'active' : '' }}">
                        <i class="bi bi-calendar-check me-2"></i><span class="flex-grow-1">Attendance</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div id="hrAttendance" class="collapse {{ $hrAttOpen ? 'show' : '' }}">
                        <div class="sidebar-submenu">
                            @foreach(['daily'=>'Daily Attendance','shifts'=>'Employee Shift Assignments','overtime'=>'Monthly Overtime','paid-days'=>'Salary Days Calculation'] as $route=>$label)
                                @if(\Illuminate\Support\Facades\Route::has('hr-payroll.attendance.'.$route))
                                    <a href="{{ route('hr-payroll.attendance.'.$route) }}" class="{{ request()->routeIs('hr-payroll.attendance.'.$route) ? 'active' : '' }}">{{ $label }}</a>
                                @endif
                            @endforeach
                            @if(\Illuminate\Support\Facades\Route::has('teacher.staff-attendance.monthly'))
                                <a href="{{ route('teacher.staff-attendance.monthly') }}" class="{{ request()->routeIs('teacher.staff-attendance.monthly') ? 'active' : '' }}">Monthly Attendance Detail</a>
                            @endif
                            @if(\Illuminate\Support\Facades\Route::has('teacher.staff-attendance.changes'))
                                <a href="{{ route('teacher.staff-attendance.changes') }}" class="{{ request()->routeIs('teacher.staff-attendance.changes') ? 'active' : '' }}">Attendance Change Requests / Approval</a>
                            @endif
                        </div>
                    </div>

                    <a data-bs-toggle="collapse" href="#hrLeaves" role="button"
                       aria-expanded="{{ $hrLeaveOpen ? 'true' : 'false' }}"
                       class="{{ $hrLeaveOpen ? 'active' : '' }}">
                        <i class="bi bi-calendar-x me-2"></i><span class="flex-grow-1">Employee Leaves</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div id="hrLeaves" class="collapse {{ $hrLeaveOpen ? 'show' : '' }}">
                        <div class="sidebar-submenu">
                            @if(\Illuminate\Support\Facades\Route::has('teacher.staff-attendance.leaves'))
                                <a href="{{ route('teacher.staff-attendance.leaves') }}">Leave Requests / Approve / Reject</a>
                            @endif
                            @if(\Illuminate\Support\Facades\Route::has('teacher.payroll.balances'))
                                <a href="{{ route('teacher.payroll.balances') }}">Leave Balances</a>
                            @endif
                            @if(\Illuminate\Support\Facades\Route::has('teacher.payroll.resignations'))
                                <a href="{{ route('teacher.payroll.resignations') }}">Resignation Requests</a>
                            @endif
                        </div>
                    </div>

                    <a data-bs-toggle="collapse" href="#hrSalary" role="button"
                       aria-expanded="{{ $hrSalaryOpen ? 'true' : 'false' }}"
                       class="{{ $hrSalaryOpen ? 'active' : '' }}">
                        <i class="bi bi-cash-stack me-2"></i><span class="flex-grow-1">Salary / Payroll</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div id="hrSalary" class="collapse {{ $hrSalaryOpen ? 'show' : '' }}">
                        <div class="sidebar-submenu">
                            @foreach(['profiles'=>'Employee Salary Structures','advances'=>'Salary Advances','adjustments'=>'Monthly Salary Adjustments','runs'=>'Salary Months / Payroll Runs','slips'=>'Payslips'] as $route=>$label)
                                @if(\Illuminate\Support\Facades\Route::has('hr-payroll.processing.'.$route))
                                    <a href="{{ route('hr-payroll.processing.'.$route) }}" class="{{ request()->routeIs('hr-payroll.processing.'.$route) ? 'active' : '' }}">{{ $label }}</a>
                                @endif
                            @endforeach
                            @foreach(['summary'=>'Attendance Payroll Summary','investments'=>'Investment Declarations'] as $route=>$label)
                                @if(\Illuminate\Support\Facades\Route::has('teacher.payroll.'.$route))
                                    <a href="{{ route('teacher.payroll.'.$route) }}" class="{{ request()->routeIs('teacher.payroll.'.$route) ? 'active' : '' }}">{{ $label }}</a>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    @if(\Illuminate\Support\Facades\Route::has('teacher.performance.team'))
                        <a href="{{ route('teacher.performance.team') }}" class="{{ request()->routeIs('teacher.performance.team') ? 'active' : '' }}">
                            <i class="bi bi-mortarboard me-2"></i> Employees Assessment
                        </a>
                    @endif
                </div>
            </div>
        @endhasanyrole


        