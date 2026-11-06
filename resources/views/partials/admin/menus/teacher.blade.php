{{-- TEACHER PORTAL - uses the existing ERP sidebar styling --}}


        @php
            $teacherPortalOpen = request()->routeIs(
                'teacher.*',
                'teacher-assignments.*'
            );

            $canManageTeacherAssignments =
                auth()->user()?->can('teacher-assignment.view') ?? false;

            $canAccessTeacherPortal =
                auth()->user()?->hasAnyRole(['Teacher', 'Super Admin'])
                ?? false;
        @endphp

        @if($canAccessTeacherPortal || $canManageTeacherAssignments)

            <div class="menu-title">
                Teacher Portal
            </div>

            <a data-bs-toggle="collapse"
            href="#teacherPortalMenu"
            role="button"
            aria-expanded="{{ $teacherPortalOpen ? 'true' : 'false' }}"
            aria-controls="teacherPortalMenu"
            class="{{ $teacherPortalOpen ? 'active' : '' }}">

                <i class="bi bi-person-workspace"></i>

                <span class="flex-grow-1">
                    Teacher Portal
                </span>

                <i class="bi bi-chevron-down sidebar-arrow"></i>
            </a>

            <div class="collapse {{ $teacherPortalOpen ? 'show' : '' }}"
                id="teacherPortalMenu">

                <div class="sidebar-submenu">

                    {{-- Teacher Dashboard --}}
                    @if($canAccessTeacherPortal)
                        <a href="{{ route('teacher.dashboard') }}"
                        class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>

                        {{-- My Profile --}}
                        <a href="{{ route('teacher.profile') }}"
                        class="{{ request()->routeIs('teacher.profile') ? 'active' : '' }}">
                            <i class="bi bi-person me-2"></i>
                            My Profile
                        </a>

                        {{-- Student List --}}
                        <a href="{{ route('teacher.students') }}"
                        class="{{ request()->routeIs('teacher.students') ? 'active' : '' }}">
                            <i class="bi bi-people me-2"></i>
                            Student List
                        </a>
                    @endif

                    {{-- Phase 3: Teacher Attendance --}}
                    @if($canAccessTeacherPortal)
                        <a href="{{ route('teacher.attendance.index') }}"
                           class="{{ request()->routeIs('teacher.attendance.index') ? 'active' : '' }}">
                            <i class="bi bi-calendar-check me-2"></i>
                            Day Wise Attendance
                        </a>
                        <a href="{{ route('teacher.attendance.report') }}"
                           class="{{ request()->routeIs('teacher.attendance.report') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart me-2"></i>
                            Monthly Attendance Report
                        </a>
                    @endif

                    {{-- Phase 4: Attendance Reports --}}
                    @if($canAccessTeacherPortal)
                        <a href="{{ route('teacher.attendance.reports', ['report'=>'detail']) }}" class="{{ request()->routeIs('teacher.attendance.reports') && request()->route('report')==='detail' ? 'active' : '' }}"><i class="bi bi-list-check me-2"></i> Attendance Detail Report</a>
                        <a href="{{ route('teacher.attendance.reports', ['report'=>'percentage']) }}" class="{{ request()->routeIs('teacher.attendance.reports') && request()->route('report')==='percentage' ? 'active' : '' }}"><i class="bi bi-percent me-2"></i> Percentage Report</a>
                        <a href="{{ route('teacher.attendance.reports', ['report'=>'absentees']) }}" class="{{ request()->routeIs('teacher.attendance.reports') && request()->route('report')==='absentees' ? 'active' : '' }}"><i class="bi bi-person-x me-2"></i> Regular Absentees</a>
                    @endif

                    {{-- Teacher Assignment Management --}}
                    @can('teacher-assignment.view')
                        <a href="{{ route('teacher-assignments.index') }}"
                        class="{{ request()->routeIs('teacher-assignments.*') ? 'active' : '' }}">

                            <i class="bi bi-diagram-3 me-2"></i>
                            Teacher Assignments
                        </a>
                    @endcan

                    {{-- Add INSIDE the existing Teacher Portal .sidebar-submenu. Do not replace your sidebar. --}}
                    <a href="{{ route('teacher.period-attendance.index') }}" class="{{ request()->routeIs('teacher.period-attendance.*') ? 'active' : '' }}"><i class="bi bi-calendar-check me-2"></i> Period Wise Attendance</a>
                    <a href="{{ route('teacher.student-leaves.index') }}" class="{{ request()->routeIs('teacher.student-leaves.*') ? 'active' : '' }}"><i class="bi bi-calendar-x me-2"></i> Student Leaves</a>

                    {{-- Paste INSIDE the existing Teacher Portal submenu. Do not replace the whole sidebar. --}}
                    @if(auth()->user()?->hasAnyRole(['Teacher','Super Admin','Admin']))
                    <a href="{{ route('teacher.classwork.index') }}" class="{{ request()->routeIs('teacher.classwork.*')?'active':'' }}"><i class="bi bi-journal-text me-2"></i> Classwork / Assignments</a>
                    @endif
                    {{-- Include 'teacher.classwork.*' in $teacherPortalOpen and ensure parent visibility includes Admin with permission. --}}

                    {{-- Merge inside your existing Teacher Portal sidebar submenu; DO NOT replace sidebar.blade.php --}}
                    @if(auth()->user()?->hasAnyRole(['Teacher','Super Admin','Admin']))
                    @php $teacherExamOpen = request()->routeIs('teacher.examination.*'); @endphp
                    <a data-bs-toggle="collapse" href="#teacherExaminationMenu" aria-expanded="{{ $teacherExamOpen ? 'true':'false' }}" class="{{ $teacherExamOpen ? 'active':'' }}"><i class="bi bi-journal-check me-2"></i><span class="flex-grow-1">Examination</span><i class="bi bi-chevron-down sidebar-arrow"></i></a>
                    <div id="teacherExaminationMenu" class="collapse {{ $teacherExamOpen ? 'show':'' }}"><div class="sidebar-submenu">
                    <a href="{{ route('teacher.examination.index') }}" class="{{ request()->routeIs('teacher.examination.index','teacher.examination.create','teacher.examination.marks','teacher.examination.marks.save') ? 'active':'' }}">Assessment Activities</a>
                    <a href="{{ route('teacher.examination.remarks') }}" class="{{ request()->routeIs('teacher.examination.remarks','teacher.examination.remarks.save') ? 'active':'' }}">Term Wise Remarks</a>
                    </div></div>
                    @endif

                    @if(auth()->user()?->hasAnyRole(['Teacher','Super Admin','Admin']))
                    <a href="{{ route('teacher.examination.health') }}" class="{{ request()->routeIs('teacher.examination.health*') ? 'active' : '' }}">Student Health Record</a>
                    <a href="{{ route('teacher.examination.skills') }}" class="{{ request()->routeIs('teacher.examination.skills*') ? 'active' : '' }}">Skill Descriptors</a>
                    <a href="{{ route('teacher.examination.student-reports') }}" class="{{ request()->routeIs('teacher.examination.student-reports') ? 'active' : '' }}">Student Wise Reports</a>
                    @endif

                    @php $communicationOpen = request()->routeIs('teacher.communication.*'); @endphp
                    <a data-bs-toggle="collapse" href="#teacherCommunicationMenu" role="button"
                    aria-expanded="{{ $communicationOpen ? 'true' : 'false' }}"
                    class="{{ $communicationOpen ? 'active' : '' }}">
                    <i class="bi bi-chat-left-text me-2"></i><span class="flex-grow-1">Communication</span>
                    <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div id="teacherCommunicationMenu" class="collapse {{ $communicationOpen ? 'show' : '' }}">
                    <div class="sidebar-submenu">
                    <a href="{{ route('teacher.communication.inbox') }}" class="{{ request()->routeIs('teacher.communication.inbox','teacher.communication.message') ? 'active':'' }}">Inbox</a>
                    <a href="{{ route('teacher.communication.sent') }}" class="{{ request()->routeIs('teacher.communication.sent') ? 'active':'' }}">Sent</a>
                    <a href="{{ route('teacher.communication.compose') }}" class="{{ request()->routeIs('teacher.communication.compose') ? 'active':'' }}">Compose</a>
                    <a href="{{ route('teacher.communication.notices','circular') }}">Circulars</a>
                    <a href="{{ route('teacher.communication.notices','newsletter') }}">News Letters</a>
                    <a href="{{ route('teacher.communication.notices','broadcast') }}">Class Broadcasts</a>
                    <a href="{{ route('teacher.communication.remarks') }}">Student Remarks / My Remarks</a>
                    </div>
                    </div>

                    @php $teacherLibraryOpen = request()->routeIs('teacher.library.*'); @endphp
                    <a data-bs-toggle="collapse" href="#teacherLibraryMenu" role="button" aria-expanded="{{ $teacherLibraryOpen ? 'true':'false' }}">Library</a>
                    <div class="collapse {{ $teacherLibraryOpen ? 'show':'' }}" id="teacherLibraryMenu">
                    <div class="sidebar-submenu">
                    <a href="{{ route('teacher.library.issued') }}">Issued Resources</a>
                    <a href="{{ route('teacher.library.history') }}">Issue/Return History</a>
                    <a href="{{ route('teacher.library.search') }}">Search In Library</a>
                    @hasanyrole('Admin|Super Admin')
                    <a href="{{ route('teacher.library.catalog') }}">Manage Catalog</a>
                    <a href="{{ route('teacher.library.issue') }}">Issue Resource</a>
                    @endhasanyrole
                    </div>
                    </div>

                    {{-- Merge INSIDE your existing Teacher Portal sidebar submenu. --}}
                    <a href="{{ route('teacher.performance.self') }}" class="{{ request()->routeIs('teacher.performance.self')?'active':'' }}">Self Assessment</a>
                    @hasanyrole('Admin|Super Admin')
                    <a href="{{ route('teacher.performance.team') }}" class="{{ request()->routeIs('teacher.performance.team')?'active':'' }}">Team's Assessment</a>
                    @endhasanyrole
                    {{-- Parent Teacher Portal open-state must include 'teacher.performance.*'. --}}

                    {{-- Phase 16: E-Learning --}}
                    @if(\Illuminate\Support\Facades\Route::has('teacher.learning.resources'))
                    <a href="{{ route('teacher.learning.resources') }}" class="{{ request()->routeIs('teacher.learning.resources') ? 'active' : '' }}"><i class="bi bi-book me-2"></i> E-Learning Resources</a>
                    @endif
                    @if(\Illuminate\Support\Facades\Route::has('teacher.learning.instructions'))
                    <a href="{{ route('teacher.learning.instructions') }}" class="{{ request()->routeIs('teacher.learning.instructions') ? 'active' : '' }}"><i class="bi bi-info-circle me-2"></i> Teacher Instructions</a>
                    @endif
                    @if(\Illuminate\Support\Facades\Route::has('teacher.learning.announcements'))
                    <a href="{{ route('teacher.learning.announcements') }}" class="{{ request()->routeIs('teacher.learning.announcements') ? 'active' : '' }}"><i class="bi bi-megaphone me-2"></i> Teacher Announcements</a>
                    @endif

                    {{-- Phase 17: Timetable, Substitutions and Lessons --}}
                    @if(\Illuminate\Support\Facades\Route::has('teacher.schedule.timetable'))
                    <a href="{{ route('teacher.schedule.timetable') }}" class="{{ request()->routeIs('teacher.schedule.timetable*') ? 'active' : '' }}"><i class="bi bi-calendar-week me-2"></i> Teacher Timetable</a>
                    @endif
                    @if(\Illuminate\Support\Facades\Route::has('teacher.schedule.substitutions'))
                    <a href="{{ route('teacher.schedule.substitutions') }}" class="{{ request()->routeIs('teacher.schedule.substitutions*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right me-2"></i> Substitutions</a>
                    @endif
                    @if(\Illuminate\Support\Facades\Route::has('teacher.schedule.lessons'))
                    <a href="{{ route('teacher.schedule.lessons') }}" class="{{ request()->routeIs('teacher.schedule.lessons*') ? 'active' : '' }}"><i class="bi bi-journal-bookmark me-2"></i> Lesson Planning</a>
                    @endif

                    {{-- Insert within existing Teacher Portal sidebar submenu. Add teacher.operations.* to menu open patterns. --}}
                    <a href="{{ route('teacher.operations.gallery') }}">Image Gallery</a>
                    <a href="{{ route('teacher.operations.inventory') }}">Inventory</a>
                    <a href="{{ route('teacher.operations.tickets') }}">Tickets / Tasks</a>


                </div>
            </div>

        @endif


        