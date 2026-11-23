{{-- Admission --}}

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
