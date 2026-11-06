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



        