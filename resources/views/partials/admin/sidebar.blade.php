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
            STUDENT INFORMATION SYSTEM
        ========================================================= --}}

        @php

            /*
            |--------------------------------------------------------------------------
            | Student Masters
            |--------------------------------------------------------------------------
            */

            $studentMastersOpen = request()->routeIs(
                'tc-reasons.*',
                'tc-remark-options.*',
                'tc-last-result-options.*',
                'promotion-statuses.*'
            );


            /*
            |--------------------------------------------------------------------------
            | Student Management
            |--------------------------------------------------------------------------
            */

            $studentManagementOpen = request()->routeIs(
                'student-management.*',
                'student-promotions.*'
            );


            /*
            |--------------------------------------------------------------------------
            | Main Student Information System
            |--------------------------------------------------------------------------
            |
            | Keep the complete Student Information System open whenever
            | any student-related route is active.
            |
            */

            $studentInformationOpen = request()->routeIs(
                'students.*',

                'tc-reasons.*',
                'tc-remark-options.*',
                'tc-last-result-options.*',
                'promotion-statuses.*',

                'student-enrollments.*',
                'student-promotions.*',
                'student-documents.*',

                'student-management.*'
            );

        @endphp


        {{-- =========================================================
            MAIN STUDENT INFORMATION SYSTEM BUTTON
        ========================================================= --}}

        <a
            data-bs-toggle="collapse"
            href="#studentInformationSystemMenu"
            role="button"
            aria-expanded="{{ $studentInformationOpen ? 'true' : 'false' }}"
            aria-controls="studentInformationSystemMenu"
            class="{{ $studentInformationOpen ? 'active' : '' }}"
        >

            <i class="bi bi-mortarboard"></i>

            <span class="flex-grow-1">
                Student Information
            </span>

            <i
                class="bi bi-chevron-down sidebar-arrow"
            ></i>

        </a>


        {{-- =========================================================
            MAIN STUDENT INFORMATION SYSTEM COLLAPSE
        ========================================================= --}}

        <div
            class="collapse {{ $studentInformationOpen ? 'show' : '' }}"
            id="studentInformationSystemMenu"
        >

            <div class="sidebar-submenu">


                {{-- =====================================================
                    DASHBOARD
                ====================================================== --}}

                {{-- Student Dashboard route can be connected later --}}

                <a href="#">

                    <i class="bi bi-speedometer2 me-2"></i>

                    Dashboard

                </a>


                {{-- =====================================================
                    MASTERS
                ====================================================== --}}

                <a
                    data-bs-toggle="collapse"
                    href="#studentMastersMenu"
                    role="button"
                    aria-expanded="{{ $studentMastersOpen ? 'true' : 'false' }}"
                    aria-controls="studentMastersMenu"
                    class="{{ $studentMastersOpen ? 'active' : '' }}"
                >

                    <i class="bi bi-gear me-2"></i>

                    <span class="flex-grow-1">
                        Masters
                    </span>

                    <i
                        class="bi bi-chevron-down sidebar-arrow"
                    ></i>

                </a>


                <div
                    class="collapse {{ $studentMastersOpen ? 'show' : '' }}"
                    id="studentMastersMenu"
                >

                    <div class="sidebar-submenu">


                        @can('tc-reason.view')

                            <a
                                href="{{ route('tc-reasons.index') }}"
                                class="{{
                                    request()->routeIs('tc-reasons.*')
                                    ? 'active'
                                    : ''
                                }}"
                            >
                                T.C. Reasons
                            </a>

                        @endcan


                        @can('tc-remark-option.view')

                            <a
                                href="{{ route('tc-remark-options.index') }}"
                                class="{{
                                    request()->routeIs('tc-remark-options.*')
                                    ? 'active'
                                    : ''
                                }}"
                            >
                                T.C. Remark Options
                            </a>

                        @endcan


                        @can('tc-last-result-option.view')

                            <a
                                href="{{ route('tc-last-result-options.index') }}"
                                class="{{
                                    request()->routeIs('tc-last-result-options.*')
                                    ? 'active'
                                    : ''
                                }}"
                            >
                                T.C. Last Result Options
                            </a>

                        @endcan


                        @can('promotion-status.view')

                            <a
                                href="{{ route('promotion-statuses.index') }}"
                                class="{{
                                    request()->routeIs('promotion-statuses.*')
                                    ? 'active'
                                    : ''
                                }}"
                            >
                                Promotion Statuses
                            </a>

                        @endcan


                    </div>

                </div>


                {{-- =====================================================
                    STUDENT LIST
                ====================================================== --}}

                @can('student.view')

                    <a
                        href="{{ route('students.index') }}"
                        class="{{
                            request()->routeIs('students.index')
                            ||
                            request()->routeIs('students.show')
                            ? 'active'
                            : ''
                        }}"
                    >

                        <i class="bi bi-list-ul me-2"></i>

                        Student List

                    </a>

                @endcan


                {{-- =====================================================
                    EDITABLE STUDENT LIST
                ====================================================== --}}

                @can('student.edit')

                    <a
                        href="{{ route('students.editable-list') }}"
                        class="{{
                            request()->routeIs(
                                'students.editable-list*'
                            )
                            ? 'active'
                            : ''
                        }}"
                    >

                        <i class="bi bi-pencil-square me-2"></i>

                        Editable Student List

                    </a>

                @endcan


                {{-- =====================================================
                    UPDATE STUDENT IMAGES
                ====================================================== --}}

                @can('student.edit')

                    <a
                        href="{{ route('students.images') }}"
                        class="{{
                            request()->routeIs(
                                'students.images*'
                            )
                            ? 'active'
                            : ''
                        }}"
                    >

                        <i class="bi bi-camera me-2"></i>

                        Update Student Images

                    </a>

                @endcan


                {{-- =====================================================
                    STUDENT MANAGEMENT
                ====================================================== --}}

                <a
                    data-bs-toggle="collapse"
                    href="#studentManagementMenu"
                    role="button"
                    aria-expanded="{{ $studentManagementOpen ? 'true' : 'false' }}"
                    aria-controls="studentManagementMenu"
                    class="{{ $studentManagementOpen ? 'active' : '' }}"
                >

                    <i class="bi bi-diagram-3 me-2"></i>

                    <span class="flex-grow-1">
                        Student Management
                    </span>

                    <i
                        class="bi bi-chevron-down sidebar-arrow"
                    ></i>

                </a>


                <div
                    class="collapse {{ $studentManagementOpen ? 'show' : '' }}"
                    id="studentManagementMenu"
                >

                    <div class="sidebar-submenu">


                        {{-- Assign Roll No. --}}

                        <a
                            href="{{ route('student-management.assign-roll-no') }}"
                            class="{{
                                request()->routeIs(
                                    'student-management.assign-roll-no*'
                                )
                                ? 'active'
                                : ''
                            }}"
                        >
                            Assign Roll No.
                        </a>


                        {{-- Section Change --}}

                        <a
                            href="{{ route('student-management.section-change') }}"
                            class="{{
                                request()->routeIs(
                                    'student-management.section-change'
                                )
                                ? 'active'
                                : ''
                            }}"
                        >
                            Section Change
                        </a>


                        {{-- Section Change Multiple --}}

                        <a
                            href="{{
                                route(
                                    'student-management.section-change-multiple'
                                )
                            }}"
                            class="{{
                                request()->routeIs(
                                    'student-management.section-change-multiple*'
                                )
                                ? 'active'
                                : ''
                            }}"
                        >
                            Section Change (Multiple)
                        </a>


                        {{-- Class Change --}}

                        <a
                            href="{{ route('student-management.class-change') }}"
                            class="{{
                                request()->routeIs(
                                    'student-management.class-change*'
                                )
                                ? 'active'
                                : ''
                            }}"
                        >
                            Class Change
                        </a>


                        {{-- Type Change --}}

                        <a
                            href="{{ route('student-management.type-change') }}"
                            class="{{
                                request()->routeIs(
                                    'student-management.type-change*'
                                )
                                ? 'active'
                                : ''
                            }}"
                        >
                            Type Change
                        </a>


                        {{-- Promotions / Repetitions --}}

                        @can('student-promotion.view')

                            <a
                                href="{{ route('student-promotions.index') }}"
                                class="{{
                                    request()->routeIs(
                                        'student-promotions.*'
                                    )
                                    ? 'active'
                                    : ''
                                }}"
                            >
                                Promotions/Repetitions
                            </a>

                        @endcan


                        {{-- Suspension --}}

                        <a
                            href="{{ route('student-management.suspension') }}"
                            class="{{
                                request()->routeIs(
                                    'student-management.suspension*'
                                )
                                ? 'active'
                                : ''
                            }}"
                        >
                            Suspension
                        </a>


                        {{-- De-registration --}}

                        <a
                            href="{{ route('student-management.deregistration') }}"
                            class="{{
                                request()->routeIs(
                                    'student-management.deregistration*'
                                )
                                ? 'active'
                                : ''
                            }}"
                        >
                            De-registration
                        </a>


                        {{-- Transfer Certificate --}}

                        <a
                            href="{{
                                route(
                                    'student-management.transfer-certificate'
                                )
                            }}"
                            class="{{
                                request()->routeIs(
                                    'student-management.transfer-certificate*'
                                )
                                ? 'active'
                                : ''
                            }}"
                        >
                            Transfer Certificate
                        </a>


                        {{-- Manual Transfer Certificate --}}

                        <a
                            href="{{
                                route(
                                    'student-management.manual-transfer-certificate'
                                )
                            }}"
                            class="{{
                                request()->routeIs(
                                    'student-management.manual-transfer-certificate*'
                                )
                                ? 'active'
                                : ''
                            }}"
                        >
                            Manual Transfer Certificate
                        </a>


                        {{-- T.C Requests --}}

                        <a
                            href="{{ route('student-management.tc-requests') }}"
                            class="{{
                                request()->routeIs(
                                    'student-management.tc-requests*'
                                )
                                ? 'active'
                                : ''
                            }}"
                        >
                            T.C Requests
                        </a>


                        {{-- Profile Modify Requests --}}

                        <a
                            href="{{
                                route(
                                    'student-management.profile-modify-requests'
                                )
                            }}"
                            class="{{
                                request()->routeIs(
                                    'student-management.profile-modify-requests*'
                                )
                                ? 'active'
                                : ''
                            }}"
                        >
                            Profile Modify Requests
                        </a>


                    </div>

                </div>


                {{-- =====================================================
                    STUDENT DOCUMENTS
                ====================================================== --}}

                @can('student-document.view')

                    <a
                        href="{{ route('student-documents.index') }}"
                        class="{{
                            request()->routeIs(
                                'student-documents.*'
                            )
                            ? 'active'
                            : ''
                        }}"
                    >

                        <i class="bi bi-folder2-open me-2"></i>

                        Student Documents

                    </a>

                @endcan


            </div>

        </div>


        {{-- =========================================================
            FEE MANAGEMENT
        ========================================================= --}}

        @canany([
            'fee-cycle.view',
            'fee-head.view',
            'fee-structure.view',
            'fee-installment.view',

            'student-fee-assignment.view',
            'student-fee-assignment.create',
            'student-fee-assignment.bulk-create',

            'student-fee-due.view',

            'fee-collection.view',
            'fee-collection.create',
            'fee-collection.cancel',
            'fee-collection.receipt',

            'fee-component-group.view',

            'bank-master.view',
            'misc-fee-component.view',
            'school-account.view',
            'fee-receipt-scheme.view',
            'late-fee-fine.view',
            'concession-type.view',
            'payment-mode.view',
            'cheque-bounce-reason.view',

            'fee-compile.view',
            'fee-compile-queue.view',

            'student-fee-assignments.view',
            'optional-fee-assignments.view',

            'concession-assignment.view',
            'fee-waiver-assignment.view',
            'fine-waiver-assignment.view',
            'composite-concession.view',

            'fee-refunds.view',

            'cheque-dd.view',
            'cheque-dd.create',
            'cheque-dd.edit',
            'cheque-dd.deposit',
            'cheque-dd.clear',
            'cheque-dd.bounce',
            'cheque-dd.cancel'
        ])

            @php

                /*
                |--------------------------------------------------------------------------
                | Fee Setup
                |--------------------------------------------------------------------------
                */

                $feeSetupOpen = request()->routeIs(
                    'fee-cycles.*',
                    'fee-component-groups.*',
                    'fee-heads.*',
                    'misc-fee-components.*',
                    'bank-masters.*',
                    'school-accounts.*',
                    'fee-structures.*',
                    'fee-receipt-schemes.*',
                    'late-fee-fines.*',
                    'concession-types.*',
                    'fee-installments.*',
                    'payment-modes.*',
                    'cheque-bounce-reasons.*',
                    'fee-compile.*',
                    'fee-compile-queues.*'
                );


                /*
                |--------------------------------------------------------------------------
                | Optional Assignments
                |--------------------------------------------------------------------------
                */

                $feeOptionalOpen = request()->routeIs(
                    'student-fee-assignments.*',
                    'optional-fee-assignments.*',
                    'concession-assignments.*',
                    'fee-waiver-assignments.*',
                    'fine-waiver-assignments.*',
                    'composite-concessions.*'
                );


                /*
                |--------------------------------------------------------------------------
                | Fee Collection
                |--------------------------------------------------------------------------
                */

                $feeCollectionOpen = request()->routeIs(
                    'fee-collections.*',
                    'fee-receipts.*',
                    'fee-refunds.*',
                    'cheque-dd-details.*'
                );


                /*
                |--------------------------------------------------------------------------
                | Main Fee Management
                |--------------------------------------------------------------------------
                */

                $feeManagementOpen =
                    request()->routeIs('fees.*')
                    ||
                    $feeSetupOpen
                    ||
                    $feeOptionalOpen
                    ||
                    $feeCollectionOpen;

            @endphp


            {{-- =====================================================
                MAIN FEE MANAGEMENT BUTTON
            ====================================================== --}}

            <a
                data-bs-toggle="collapse"
                href="#feeManagementMenu"
                role="button"
                aria-expanded="{{ $feeManagementOpen ? 'true' : 'false' }}"
                aria-controls="feeManagementMenu"
                class="{{ $feeManagementOpen ? 'active' : '' }}"
            >

                <i class="bi bi-cash-stack"></i>

                <span class="flex-grow-1">
                    Fee Management
                </span>

                <i class="bi bi-chevron-down sidebar-arrow"></i>

            </a>


            {{-- =====================================================
                MAIN FEE MANAGEMENT COLLAPSE
            ====================================================== --}}

            <div
                class="collapse {{ $feeManagementOpen ? 'show' : '' }}"
                id="feeManagementMenu"
            >

                <div class="sidebar-submenu">


                    {{-- =================================================
                        FEE DASHBOARD
                    ================================================== --}}

                    <a
                        href="{{ route('fees.dashboard') }}"
                        class="{{
                            request()->routeIs('fees.dashboard')
                            ? 'active'
                            : ''
                        }}"
                    >

                        <i class="bi bi-speedometer2 me-2"></i>

                        Dashboard

                    </a>


                    {{-- =================================================
                        SETUP
                    ================================================== --}}

                    <a
                        data-bs-toggle="collapse"
                        href="#feeSetupMenu"
                        role="button"
                        aria-expanded="{{ $feeSetupOpen ? 'true' : 'false' }}"
                        aria-controls="feeSetupMenu"
                        class="{{ $feeSetupOpen ? 'active' : '' }}"
                    >

                        <i class="bi bi-gear me-2"></i>

                        <span class="flex-grow-1">
                            Setup
                        </span>

                        <i class="bi bi-chevron-down sidebar-arrow"></i>

                    </a>


                    <div
                        class="collapse {{ $feeSetupOpen ? 'show' : '' }}"
                        id="feeSetupMenu"
                    >

                        <div class="sidebar-submenu">


                            {{-- Fee Cycles --}}

                            @can('fee-cycle.view')

                                <a
                                    href="{{ route('fee-cycles.index') }}"
                                    class="{{
                                        request()->routeIs('fee-cycles.*')
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Fee Cycles
                                </a>

                            @endcan


                            {{-- Fee Component Groups --}}

                            @can('fee-component-group.view')

                                <a
                                    href="{{ route('fee-component-groups.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'fee-component-groups.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Fee Component Groups
                                </a>

                            @endcan


                            {{-- Fee Components --}}

                            @can('fee-head.view')

                                <a
                                    href="{{ route('fee-heads.index') }}"
                                    class="{{
                                        request()->routeIs('fee-heads.*')
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Fee Components
                                </a>

                            @endcan


                            {{-- Misc Components --}}

                            @can('misc-fee-component.view')

                                <a
                                    href="{{ route('misc-fee-components.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'misc-fee-components.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Misc. Components
                                </a>

                            @endcan


                            {{-- Bank Master --}}

                            @can('bank-master.view')

                                <a
                                    href="{{ route('bank-masters.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'bank-masters.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Banks Master
                                </a>

                            @endcan


                            {{-- School Accounts --}}

                            @can('school-account.view')

                                <a
                                    href="{{ route('school-accounts.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'school-accounts.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    School Accounts
                                </a>

                            @endcan


                            {{-- Fee Templates --}}

                            @can('fee-structure.view')

                                <a
                                    href="{{ route('fee-structures.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'fee-structures.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Fee Templates
                                </a>

                            @endcan


                            {{-- Receipt Number Scheme --}}

                            @can('fee-receipt-scheme.view')

                                <a
                                    href="{{ route('fee-receipt-schemes.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'fee-receipt-schemes.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Receipt No. Scheme
                                </a>

                            @endcan


                            {{-- Late Fee Fine --}}

                            @can('late-fee-fine.view')

                                <a
                                    href="{{ route('late-fee-fines.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'late-fee-fines.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Late Fee Fine
                                </a>

                            @endcan


                            {{-- Concession Types --}}

                            @can('concession-type.view')

                                <a
                                    href="{{ route('concession-types.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'concession-types.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Concession Types
                                </a>

                            @endcan

                            {{-- Payment Modes --}}

                            @can('payment-mode.view')

                                <a
                                    href="{{ route('payment-modes.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'payment-modes.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Payment Modes
                                </a>

                            @endcan


                            {{-- Cheque Bounce Reasons --}}

                            @can('cheque-bounce-reason.view')

                                <a
                                    href="{{ route('cheque-bounce-reasons.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'cheque-bounce-reasons.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Chq. Bounce Reasons
                                </a>

                            @endcan


                            {{-- Compile Fee --}}

                            @can('student-fee-assignment.bulk-create')

                                <a
                                    href="{{ route('fee-compile.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'fee-compile.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Compile Fee
                                </a>

                            @endcan


                            {{-- Fee Compile Queue --}}

                            @can('fee-compile-queue.view')

                                <a
                                    href="{{ route('fee-compile-queues.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'fee-compile-queues.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Fee Compile Queues
                                </a>

                            @endcan


                        </div>

                    </div>


                    {{-- =================================================
                        OPTIONAL ASSIGNMENTS
                    ================================================== --}}

                    <a
                        data-bs-toggle="collapse"
                        href="#feeOptionalMenu"
                        role="button"
                        aria-expanded="{{ $feeOptionalOpen ? 'true' : 'false' }}"
                        aria-controls="feeOptionalMenu"
                        class="{{ $feeOptionalOpen ? 'active' : '' }}"
                    >

                        <i class="bi bi-sliders me-2"></i>

                        <span class="flex-grow-1">
                            Optional Assignments
                        </span>

                        <i class="bi bi-chevron-down sidebar-arrow"></i>

                    </a>


                    <div
                        class="collapse {{ $feeOptionalOpen ? 'show' : '' }}"
                        id="feeOptionalMenu"
                    >

                        <div class="sidebar-submenu">


                            {{-- Fee Assignments --}}

                            @can('student-fee-assignment.view')

                                <a
                                    href="{{ route('student-fee-assignments.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'student-fee-assignments.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Fee Assignments
                                </a>

                            @endcan


                            {{-- Optional Fee Assignment --}}

                            @can('optional-fee-assignment.view')

                                <a
                                    href="{{ route('optional-fee-assignments.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'optional-fee-assignments.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Optional Fee Assignment
                                </a>

                            @endcan


                            {{-- Concession Assignment --}}

                            @can('concession-assignment.view')

                                <a
                                    href="{{ route('concession-assignments.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'concession-assignments.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Concession Assignment
                                </a>

                            @endcan


                            {{-- Fee Waiver Assignment --}}

                            @can('fee-waiver-assignment.view')

                                <a
                                    href="{{ route('fee-waiver-assignments.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'fee-waiver-assignments.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Fee Waiver Assignment
                                </a>

                            @endcan


                            {{-- Fine Waiver Assignment --}}

                            @can('fine-waiver-assignment.view')

                                <a
                                    href="{{ route('fine-waiver-assignments.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'fine-waiver-assignments.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Fine Waiver Assignment
                                </a>

                            @endcan


                            {{-- Composite Concession --}}

                            @can('composite-concession.view')

                                <a
                                    href="{{ route('composite-concessions.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'composite-concessions.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Composite Concession
                                </a>

                            @endcan


                        </div>

                    </div>


                    {{-- =================================================
                        FEE COLLECTION
                    ================================================== --}}

                    <a
                        data-bs-toggle="collapse"
                        href="#feeCollectionMenu"
                        role="button"
                        aria-expanded="{{ $feeCollectionOpen ? 'true' : 'false' }}"
                        aria-controls="feeCollectionMenu"
                        class="{{ $feeCollectionOpen ? 'active' : '' }}"
                    >

                        <i class="bi bi-currency-rupee me-2"></i>

                        <span class="flex-grow-1">
                            Fee Collection
                        </span>

                        <i class="bi bi-chevron-down sidebar-arrow"></i>

                    </a>


                    <div
                        class="collapse {{ $feeCollectionOpen ? 'show' : '' }}"
                        id="feeCollectionMenu"
                    >

                        <div class="sidebar-submenu">


                            {{-- Fee Receipts --}}

                            @can('fee-collection.view')

                                <a
                                    href="{{ route('fee-receipts.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'fee-receipts.*',
                                            'fee-collections.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Fee Receipts
                                </a>

                            @endcan


                            {{-- Refund --}}

                            @can('fee-refund.view')

                                <a
                                    href="{{ route('fee-refunds.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'fee-refunds.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Refund
                                </a>

                            @endcan


                            {{-- Cheque / DD --}}

                            @can('cheque-dd.view')

                                <a
                                    href="{{ route('cheque-dd-details.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'cheque-dd-details.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Cheque/DD Detail
                                </a>

                            @endcan


                        </div>

                    </div>


                    {{-- =================================================
                        INSTRUCTIONS
                    ================================================== --}}

                    <a href="#">

                        <i class="bi bi-info-circle me-2"></i>

                        Instructions

                    </a>


                </div>

            </div>

        @endcanany


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