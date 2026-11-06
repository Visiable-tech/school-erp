{{-- Admission --}}

        <div class="menu-title">
            Students
        </div>


        {{-- =========================================================
            ADMISSION MANAGEMENT
        ========================================================= --}}

        @php
            // Admission Masters
            $admissionMastersOpen = request()->routeIs(
                'admission-boards.*',
                'admission-communication-modes.*',
                'admission-information-sources.*'
            );

            // Admission Setup
            $admissionSetupOpen = request()->routeIs(
                'admission-sessions.*',
                'admission-number-schemes.*',
                'admission-setups.*'
            );

            // Admission Candidates
            $admissionCandidatesOpen = request()->routeIs(
                'admission-candidates.*'
            );

            // Admission Enquiries
            $admissionEnquiryOpen = request()->routeIs(
                'admission-enquiries.*',
                'admission-followups.*'
            );

            // Admission Registrations
            $admissionRegistrationOpen = request()->routeIs(
                'admission-applications.*'
            );

            $admissionFormSalesOpen = request()->routeIs('admission-form-sales.*');

            // Main Admission Management Menu
            $admissionManagementOpen =
                $admissionMastersOpen
                || $admissionSetupOpen
                || $admissionCandidatesOpen
                || $admissionEnquiryOpen
                || $admissionRegistrationOpen
                || $admissionFormSalesOpen
                || request()->routeIs('admission.dashboard');
        @endphp


            {{-- MAIN ADMISSION MANAGEMENT --}}

            <a
                data-bs-toggle="collapse"
                href="#admissionManagementMenu"
                role="button"
                aria-expanded="{{ $admissionManagementOpen ? 'true' : 'false' }}"
                aria-controls="admissionManagementMenu"
                class="{{ $admissionManagementOpen ? 'active' : '' }}"
            >
                <i class="bi bi-person-plus"></i>

                <span class="flex-grow-1">
                    Admission
                </span>

                <i class="bi bi-chevron-down sidebar-arrow"></i>
            </a>


            <div
                class="collapse {{ $admissionManagementOpen ? 'show' : '' }}"
                id="admissionManagementMenu"
            >
                <div class="sidebar-submenu">


                    {{-- =====================================================
                        DASHBOARD
                    ====================================================== --}}

                    <a href="{{ route('admission.dashboard') }}" class="{{ request()->routeIs('admission.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard
                    </a>


                    {{-- =====================================================
                        MASTERS
                    ====================================================== --}}

                    <a
                        data-bs-toggle="collapse"
                        href="#admissionMastersMenu"
                        role="button"
                        aria-expanded="{{ $admissionMastersOpen ? 'true' : 'false' }}"
                        aria-controls="admissionMastersMenu"
                        class="{{ $admissionMastersOpen ? 'active' : '' }}"
                    >
                        <i class="bi bi-asterisk me-2"></i>

                        <span class="flex-grow-1">
                            Masters
                        </span>

                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>


                    <div
                        class="collapse {{ $admissionMastersOpen ? 'show' : '' }}"
                        id="admissionMastersMenu"
                    >
                        <div class="sidebar-submenu">


                            {{-- Board / Universities --}}

                            @can('admission-board.view')

                                <a
                                    href="{{ route('admission-boards.index') }}"
                                    class="{{ request()->routeIs('admission-boards.*') ? 'active' : '' }}"
                                >
                                    Board/Universities
                                </a>

                            @endcan


                            {{-- Future Master Modules --}}

                            @can('admission-communication-mode.view')

                                <a
                                    href="{{ route('admission-communication-modes.index') }}"
                                    class="{{
                                        request()->routeIs(
                                            'admission-communication-modes.*'
                                        )
                                        ? 'active'
                                        : ''
                                    }}"
                                >
                                    Communication Modes
                                </a>

                            @endcan

                            @can('admission-information-source.view')
                                <a
                                    href="{{ route('admission-information-sources.index') }}"
                                    class="{{ request()->routeIs('admission-information-sources.*') ? 'active' : '' }}"
                                >
                                    Sources Of Information
                                </a>
                            @endcan

                            <a href="#">
                                Document Types
                            </a>

                            <a href="#">
                                Exam Types
                            </a>

                        </div>
                    </div>


                    {{-- =====================================================
                        SETUP
                    ====================================================== --}}

                    <a
                        data-bs-toggle="collapse"
                        href="#admissionSetupMenu"
                        role="button"
                        aria-expanded="{{ $admissionSetupOpen ? 'true' : 'false' }}"
                        aria-controls="admissionSetupMenu"
                        class="{{ $admissionSetupOpen ? 'active' : '' }}"
                    >
                        <i class="bi bi-gear me-2"></i>

                        <span class="flex-grow-1">
                            Setup
                        </span>

                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>


                    <div
                        class="collapse {{ $admissionSetupOpen ? 'show' : '' }}"
                        id="admissionSetupMenu"
                    >
                        <div class="sidebar-submenu">

                            @can('admission-number-scheme.view')
                                <a
                                    href="{{ route('admission-number-schemes.index') }}"
                                    class="{{ request()->routeIs('admission-number-schemes.*') ? 'active' : '' }}"
                                >
                                    Number Gen. Schemes
                                </a>
                            @endcan

                            <a href="#">
                                Document Groups
                            </a>

                            <a href="#">
                                Exam Groups
                            </a>


                            {{-- Existing Admission Sessions --}}

                            @can('admission-setup.view')
                                <a 
                                    href="{{ route('admission-setups.index') }}" 
                                    class="{{ request()->routeIs('admission-setups.*') ? 'active' : '' }}"
                                >
                                    Admission Setup
                                </a>
                            @endcan



                            <a href="#">
                                Registration Levels
                            </a>

                        </div>
                    </div>


                    {{-- =====================================================
                        CANDIDATES
                    ====================================================== --}}

                    @can('admission-candidate.view')
                        <a href="{{ route('admission-candidates.index') }}"
                        class="{{ $admissionCandidatesOpen ? 'active' : '' }}">
                            <i class="bi bi-people me-2"></i>
                            Candidates
                        </a>
                    @endcan



                    {{-- =====================================================
                        ENQUIRIES
                    ====================================================== --}}

                    @can('admission-enquiry.view')

                        <a
                            href="{{ route('admission-enquiries.index') }}"
                            class="{{ request()->routeIs(
                                'admission-enquiries.*',
                                'admission-followups.*'
                            ) ? 'active' : '' }}"
                        >
                            <i class="bi bi-search me-2"></i>
                            Enquiries
                        </a>

                    @endcan


                    {{-- =====================================================
                        FORM SALES
                    ====================================================== --}}

                    @can('admission-form-sale.view')
                        <a 
                            href="{{ route('admission-form-sales.index') }}" 
                            class="{{ $admissionFormSalesOpen ? 'active' : '' }}"
                        >
                            <i class="bi bi-pencil-square me-2"></i>
                            Form Sales
                        </a>
                    @endcan


                    {{-- =====================================================
                        REGISTRATIONS
                    ====================================================== --}}

                    @can('admission-application.view')

                        <a
                            href="{{ route('admission-applications.index') }}"
                            class="{{ request()->routeIs('admission-applications.*') ? 'active' : '' }}"
                        >
                            <i class="bi bi-pencil-square me-2"></i>
                            Registrations
                        </a>

                    @endcan


                    {{-- =====================================================
                        ADMISSION CONFIRMATIONS
                    ====================================================== --}}

                    <a href="#">
                        <i class="bi bi-award me-2"></i>
                        Admission Confirmations
                    </a>


                    {{-- =====================================================
                        REPORTS
                    ====================================================== --}}

                    <a
                        data-bs-toggle="collapse"
                        href="#admissionReportsMenu"
                        role="button"
                        aria-expanded="false"
                        aria-controls="admissionReportsMenu"
                    >
                        <i class="bi bi-bar-chart me-2"></i>

                        <span class="flex-grow-1">
                            Reports
                        </span>

                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>


                    <div
                        class="collapse"
                        id="admissionReportsMenu"
                    >
                        <div class="sidebar-submenu">

                            <a href="#">
                                Dynamic Reports
                            </a>

                            <a href="#">
                                Admission Summary
                            </a>

                            <a href="#">
                                Vacancy Report
                            </a>

                            <a href="#">
                                Document Status
                            </a>

                            <a href="#">
                                Exam Status
                            </a>

                            <a href="#">
                                Follow Up Report
                            </a>

                            <a href="#">
                                Enquiries Without Followup
                            </a>

                            <a href="#">
                                Cheque Details
                            </a>

                        </div>
                    </div>


                    {{-- =====================================================
                        INSTRUCTIONS
                    ====================================================== --}}

                    <a href="#">
                        <i class="bi bi-info-circle me-2"></i>
                        Instructions
                    </a>


                    {{-- =====================================================
                        DATA PORTING
                    ====================================================== --}}

                    <a href="#">
                        <i class="bi bi-upload me-2"></i>
                        Data Porting
                    </a>


                </div>
            </div>



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

                'student-management.*',
                'student-information.dashboard'
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

                <a href="{{ route('student-information.dashboard') }}" class="{{ request()->routeIs('student-information.dashboard') ? 'active' : '' }}">

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


        