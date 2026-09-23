<aside
    class="admin-sidebar"
    id="adminSidebar">

    <div class="sidebar-brand">

        <div>

            <h4>
                <i class="bi bi-mortarboard-fill"></i>
                School ERP
            </h4>

        </div>

    </div>


    <div class="sidebar-menu">

        <div class="menu-title">
            Main
        </div>


        <a
            href="{{ route('dashboard') }}"
            class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

            <i class="bi bi-speedometer2"></i>

            Dashboard

        </a>


        {{-- Academic Setup --}}

        <div class="menu-title">
            Academic
        </div>


        <a
            data-bs-toggle="collapse"
            href="#academicSetup">

            <i class="bi bi-building"></i>

            <span class="flex-grow-1">
                Academic Setup
            </span>

            <i class="bi bi-chevron-down"></i>

        </a>


        <div
            class="collapse
            {{ request()->routeIs(
                    'academic-years.*',
                    'wings.*',
                    'school-classes.*',
                    'sections.*',
                    'section-groups.*',
                    'subjects.*',
                    'sections.*',
                    'subjects.*',
                    'class-subjects.*',
                    'calendar-event-types.*',
                    'school-calendar.*'
            ) ? 'true' : 'false' }}"
            id="academicSetup">

            <div class="sidebar-submenu">

                @can('academic-year.view')

                    <a
                        href="{{ route('academic-years.index') }}"
                        class="{{ request()->routeIs('academic-years.*') ? 'active' : '' }}">

                        Academic Years

                    </a>

                @endcan


                @can('wing.view')

                    <a
                        href="{{ route('wings.index') }}"
                        class="{{ request()->routeIs('wings.*') ? 'active' : '' }}">

                        Wings

                    </a>

                @endcan


                @can('class.view')

                    <a
                        href="{{ route('school-classes.index') }}"
                        class="{{ request()->routeIs('school-classes.*') ? 'active' : '' }}"
                    >
                        Classes
                    </a>

                @endcan

                @can('section.view')

                    <a
                        href="{{ route('sections.index') }}"
                        class="{{ request()->routeIs('sections.*') ? 'active' : '' }}"
                    >
                        Sections
                    </a>

                @endcan

                @can('section-group.view')

                    <a
                        href="{{ route('section-groups.index') }}"
                        class="{{ request()->routeIs('section-groups.*') ? 'active' : '' }}"
                    >
                        Section Groups
                    </a>

                @endcan

                @can('subject.view')

                    <a
                        href="{{ route('subjects.index') }}"
                        class="{{ request()->routeIs('subjects.*') ? 'active' : '' }}"
                    >
                        Subjects
                    </a>

                @endcan

                @can('class-subject.view')

                    <a
                        href="{{ route('class-subjects.index') }}"
                        class="{{ request()->routeIs('class-subjects.*') ? 'active' : '' }}"
                    >
                        Class Subject Mapping
                    </a>

                @endcan

                @can('calendar.view')

                    <a
                        href="{{ route('calendar-event-types.index') }}"
                        class="{{ request()->routeIs('calendar-event-types.*') ? 'active' : '' }}"
                    >
                        Calendar Event Types
                    </a>

                @endcan

                @can('calendar.view')

                    <a
                        href="{{ route('school-calendar.index') }}"
                        class="{{ request()->routeIs('school-calendar.*') ? 'active' : '' }}"
                    >
                        School Calendar
                    </a>

                @endcan

            </div>

        </div>


        {{-- Admission --}}

        <div class="menu-title">
            Students
        </div>


        @canany([
            'admission-session.view',
            'admission-enquiry.view',
            'admission-followup.view',
            'admission-application.view'
        ])

            <a
                class="d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse"
                href="#admissionMenu"
                role="button"
            >
                <span>
                    <i class="bi bi-person-plus me-2"></i>
                    Admission
                </span>

                <i class="bi bi-chevron-down small"></i>
            </a>

            <div
                class="collapse {{ request()->routeIs(
                    'admission-sessions.*',
                    'admission-enquiries.*',
                    'admission-followups.*',
                    'admission-applications.*'
                ) ? 'show' : '' }}"
                id="admissionMenu"
            >

                <div class="ms-3">

                    @can('admission-session.view')

                        <a
                            href="{{ route('admission-sessions.index') }}"
                            class="{{ request()->routeIs('admission-sessions.*') ? 'active' : '' }}"
                        >
                            Admission Sessions
                        </a>

                    @endcan

                    @can('admission-enquiry.view')

                        <a
                            href="{{ route('admission-enquiries.index') }}"
                            class="{{ request()->routeIs('admission-enquiries.*') ? 'active' : '' }}"
                        >
                            Admission Enquiries
                        </a>

                    @endcan

                    @can('admission-followup.view')

                        <a
                            href="{{ route('admission-followups.index') }}"
                            class="{{ request()->routeIs('admission-followups.*') ? 'active' : '' }}"
                        >
                            Admission Follow-ups
                        </a>

                    @endcan

                    @can('admission-application.view')

                        <a
                            href="{{ route('admission-applications.index') }}"
                            class="{{ request()->routeIs('admission-applications.*') ? 'active' : '' }}"
                        >
                            Admission Applications
                        </a>

                    @endcan
                </div>

            </div>

        @endcanany



        {{-- =========================================================
     STUDENT INFORMATION
========================================================= --}}

    @php

        $studentMenuOpen =
            request()->routeIs('students.*')
            || request()->routeIs('student-enrollments.*')
            || request()->routeIs('student-promotions.*')
            || request()->routeIs('student-documents.*');

    @endphp

    @canany([
        'student.view',
        'student.create',
        'student.edit',
        'student-enrollment.view',
        'student-enrollment.create',
        'student-enrollment.edit',
        'student-promotion.view',
        'student-promotion.create',
        'student-document.view',
        'student-document.create',
        'student-document.edit',
        'student-document.delete',
    ])

        <a
            data-bs-toggle="collapse"
            href="#studentMenu"
            role="button"
            aria-expanded="{{ $studentMenuOpen ? 'true' : 'false' }}"
            aria-controls="studentMenu"
            class="{{ $studentMenuOpen ? 'active' : '' }}"
        >
            <i class="bi bi-people"></i>

            <span class="flex-grow-1">
                Students
            </span>

            <i class="bi bi-chevron-down"></i>
        </a>


        <div
            class="collapse {{ $studentMenuOpen ? 'show' : '' }}"
            id="studentMenu"
        >

            <div class="sidebar-submenu">

                {{-- Student List --}}
                @can('student.view')

                    <a
                        href="{{ route('students.index') }}"
                        class="{{ request()->routeIs('students.*') ? 'active' : '' }}"
                    >
                        <i class="bi bi-list-ul me-2"></i>
                        Student List
                    </a>

                @endcan


                {{-- Add Student --}}
                @can('student.create')

                    <a
                        href="{{ route('students.create') }}"
                        class="{{ request()->routeIs('students.create') ? 'active' : '' }}"
                    >
                        <i class="bi bi-person-plus me-2"></i>
                        Add Student
                    </a>

                @endcan

                @can('student.create')

                    <a
                        href="{{ route('students.import') }}"
                        class="{{
                            request()->routeIs('students.import*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <i class="bi bi-file-earmark-excel me-2"></i>
                        Import Students
                    </a>

                @endcan


                {{-- Student Promotion --}}
                @can('student-promotion.view')

                    <a
                        href="{{ route('student-promotions.index') }}"
                        class="{{ request()->routeIs('student-promotions.*') ? 'active' : '' }}"
                    >
                        <i class="bi bi-arrow-up-circle me-2"></i>
                        Student Promotion
                    </a>

                @endcan

                @can('student-document.view')

                    <a href="{{ route('student-documents.index') }}"
                    class="{{ request()->routeIs('student-documents.*') ? 'active' : '' }}">

                        <i class="bi bi-folder2-open me-2"></i>
                        Student Documents

                    </a>

                @endcan

                @can('student-attendance.create')

                    <a
                        href="{{ route('student-attendance.create') }}"
                        class="nav-link
                        {{ request()->routeIs('student-attendance.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-calendar-check me-2"></i>

                        Student Attendance

                    </a>

                @endcan

            </div>

        </div>

    @endcanany


    {{-- Fees --}}

    @canany([
        'fee-head.view',
        'fee-structure.view',
        'fee-collection.view',
        'fee-report.view'
    ])

        <div class="menu-title">
            Finance
        </div>


        <a
            data-bs-toggle="collapse"
            href="#feeMenu"
            role="button"
            aria-expanded="{{ request()->routeIs('fee-*') ? 'true' : 'false' }}"
        >

            <i class="bi bi-cash-stack"></i>

            <span class="flex-grow-1">
                Fee Management
            </span>

            <i class="bi bi-chevron-down"></i>

        </a>


        <div
            class="collapse {{ request()->routeIs('fee-*') ? 'show' : '' }}"
            id="feeMenu"
        >

            <div class="sidebar-submenu">


                {{-- Fee Heads --}}

                @can('fee-head.view')

                    <a
                        href="{{ route('fee-heads.index') }}"
                        class="{{ request()->routeIs('fee-heads.*') ? 'active' : '' }}"
                    >
                        Fee Heads
                    </a>

                @endcan


                {{-- Fee Structure --}}

                @can('fee-structure.view')

                    <a
                        href="{{ route('fee-structures.index') }}"
                        class="{{ request()->routeIs('fee-structures.*') ? 'active' : '' }}"
                    >
                        Fee Structure
                    </a>

                @endcan


                {{-- Fee Collection --}}

                @can('fee-collection.view')

                    @if(Route::has('fee-collections.index'))

                        <a
                            href="{{ route('fee-collections.index') }}"
                            class="{{ request()->routeIs('fee-collections.*') ? 'active' : '' }}"
                        >
                            Fee Collection
                        </a>

                    @else

                        <a href="javascript:void(0)" class="text-muted">
                            Fee Collection
                        </a>

                    @endif

                @endcan


                {{-- Due Fees --}}

                @can('fee-collection.view')

                    @if(Route::has('fee-dues.index'))

                        <a
                            href="{{ route('fee-dues.index') }}"
                            class="{{ request()->routeIs('fee-dues.*') ? 'active' : '' }}"
                        >
                            Due Fees
                        </a>

                    @else

                        <a href="javascript:void(0)" class="text-muted">
                            Due Fees
                        </a>

                    @endif

                @endcan


                {{-- Fee Reports --}}

                @can('fee-report.view')

                    @if(Route::has('fee-reports.index'))

                        <a
                            href="{{ route('fee-reports.index') }}"
                            class="{{ request()->routeIs('fee-reports.*') ? 'active' : '' }}"
                        >
                            Fee Reports
                        </a>

                    @else

                        <a href="javascript:void(0)" class="text-muted">
                            Fee Reports
                        </a>

                    @endif

                @endcan


            </div>

        </div>

    @endcanany

        {{-- Accounts --}}

        <a
            data-bs-toggle="collapse"
            href="#accountMenu">

            <i class="bi bi-wallet2"></i>

            <span class="flex-grow-1">
                Accounts
            </span>

            <i class="bi bi-chevron-down"></i>

        </a>


        <div
            class="collapse"
            id="accountMenu">

            <div class="sidebar-submenu">

                <a href="#">
                    Invoices
                </a>

                <a href="#">
                    Payments
                </a>

                <a href="#">
                    Receipts
                </a>

                <a href="#">
                    Reports
                </a>

            </div>

        </div>


        {{-- Examination --}}

        <div class="menu-title">
            Examination
        </div>


        <a
            data-bs-toggle="collapse"
            href="#examMenu">

            <i class="bi bi-journal-check"></i>

            <span class="flex-grow-1">
                Examination
            </span>

            <i class="bi bi-chevron-down"></i>

        </a>


        <div
            class="collapse"
            id="examMenu">

            <div class="sidebar-submenu">

                <a href="#">
                    Exam Setup
                </a>

                <a href="#">
                    Marks Entry
                </a>

                <a href="#">
                    Results
                </a>

                <a href="#">
                    Report Cards
                </a>

            </div>

        </div>


        {{-- Teaching --}}

        <div class="menu-title">
            Teaching
        </div>


        <a href="#">

            <i class="bi bi-book"></i>

            Classwork

        </a>


        <a href="#">

            <i class="bi bi-pencil-square"></i>

            Assignments

        </a>


        <a href="#">

            <i class="bi bi-journal-text"></i>

            Lesson Planning

        </a>


        <a href="#">

            <i class="bi bi-table"></i>

            Timetable

        </a>


        {{-- HR --}}

        <div class="menu-title">
            Human Resource
        </div>


        <a
            data-bs-toggle="collapse"
            href="#employeeMenu">

            <i class="bi bi-person-badge"></i>

            <span class="flex-grow-1">
                Employees
            </span>

            <i class="bi bi-chevron-down"></i>

        </a>


        <div
            class="collapse"
            id="employeeMenu">

            <div class="sidebar-submenu">

                <a href="#">
                    Employee List
                </a>

                <a href="#">
                    Add Employee
                </a>

                <a href="#">
                    Employee Attendance
                </a>

                <a href="#">
                    Leave
                </a>

                <a href="#">
                    Payroll
                </a>

            </div>

        </div>


        {{-- Transport --}}

        <div class="menu-title">
            Operations
        </div>


        <a href="#">

            <i class="bi bi-bus-front"></i>

            Transport

        </a>


        <a href="#">

            <i class="bi bi-box-seam"></i>

            Inventory

        </a>


        <a href="#">

            <i class="bi bi-chat-left-text"></i>

            Communication

        </a>


        <a href="#">

            <i class="bi bi-ticket"></i>

            Ticketing

        </a>


        <a href="#">

            <i class="bi bi-person-vcard"></i>

            Visitor / Gate Pass

        </a>


        {{-- Administration --}}

        <div class="menu-title">
            Administration
        </div>


        <a href="#">

            <i class="bi bi-person-gear"></i>

            Users

        </a>


        <a href="#">

            <i class="bi bi-shield-lock"></i>

            Roles & Permissions

        </a>


        <a href="#">

            <i class="bi bi-gear"></i>

            Settings

        </a>

    </div>

</aside>