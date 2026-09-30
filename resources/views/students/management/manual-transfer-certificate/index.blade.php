@extends('layouts.admin')

@section('title', 'Manual Transfer Certificate')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Manual Transfer Certificate
            </h4>

            <div class="text-muted">
                Manage manually created Transfer Certificates.
            </div>

        </div>

        <a
            href="{{
                route(
                    'student-management.manual-transfer-certificate.create'
                )
            }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-circle me-1"></i>
            Create Manual TC
        </a>

    </div>


    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- FILTER --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{
                    route(
                        'student-management.manual-transfer-certificate'
                    )
                }}"
            >

                <div class="row g-3">

                    <div class="col-lg-6">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="TC No / Admission No / Student / Father"
                        >

                    </div>


                    <div class="col-lg-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option value="">
                                All Status
                            </option>

                            <option
                                value="draft"
                                @selected(
                                    request('status') === 'draft'
                                )
                            >
                                Draft
                            </option>

                            <option
                                value="issued"
                                @selected(
                                    request('status') === 'issued'
                                )
                            >
                                Issued
                            </option>

                            <option
                                value="cancelled"
                                @selected(
                                    request('status') === 'cancelled'
                                )
                            >
                                Cancelled
                            </option>

                        </select>

                    </div>


                    <div class="col-lg-3 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-primary me-2"
                        >
                            Search
                        </button>

                        <a
                            href="{{
                                route(
                                    'student-management.manual-transfer-certificate'
                                )
                            }}"
                            class="btn btn-outline-secondary"
                        >
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- TABLE --}}

    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table
                    class="table table-bordered table-striped table-hover align-middle mb-0"
                >

                    <thead class="table-dark">

                        <tr>

                            <th>#</th>

                            <th>
                                TC No.
                            </th>

                            <th>
                                Admission No.
                            </th>

                            <th>
                                Student
                            </th>

                            <th>
                                Father
                            </th>

                            <th>
                                Academic Year
                            </th>

                            <th>
                                Class
                            </th>

                            <th>
                                Issue Date
                            </th>

                            <th>
                                Reason
                            </th>

                            <th>
                                Status
                            </th>

                            <th style="width:180px;">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($certificates as $certificate)

                            <tr>

                                <td>

                                    {{
                                        $certificates->firstItem()
                                        +
                                        $loop->index
                                    }}

                                </td>


                                <td>

                                    <strong class="text-primary">
                                        {{ $certificate->tc_number }}
                                    </strong>

                                </td>


                                <td>

                                    {{
                                        $certificate
                                            ->manual_admission_no
                                        ?? '-'
                                    }}

                                </td>


                                <td>

                                    <strong>

                                        {{
                                            $certificate
                                                ->manual_student_name
                                        }}

                                    </strong>

                                </td>


                                <td>

                                    {{
                                        $certificate
                                            ->manual_father_name
                                        ?? '-'
                                    }}

                                </td>


                                <td>

                                    {{
                                        $certificate
                                            ->manual_academic_year
                                        ?? '-'
                                    }}

                                </td>


                                <td>

                                    {{
                                        $certificate
                                            ->manual_class
                                        ?? '-'
                                    }}

                                    @if(
                                        $certificate
                                            ->manual_section
                                    )

                                        -
                                        {{
                                            $certificate
                                                ->manual_section
                                        }}

                                    @endif

                                </td>


                                <td>

                                    {{
                                        $certificate
                                            ->issue_date
                                            ?->format('d-m-Y')
                                    }}

                                </td>


                                <td>

                                    {{
                                        $certificate
                                            ->reason
                                            ?->name
                                        ?? '-'
                                    }}

                                </td>


                                <td>

                                    @if(
                                        $certificate->status
                                        ===
                                        'draft'
                                    )

                                        <span
                                            class="badge bg-warning text-dark"
                                        >
                                            Draft
                                        </span>

                                    @elseif(
                                        $certificate->status
                                        ===
                                        'issued'
                                    )

                                        <span
                                            class="badge bg-success"
                                        >
                                            Issued
                                        </span>

                                    @else

                                        <span
                                            class="badge bg-danger"
                                        >
                                            Cancelled
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if(
                                        $certificate->status
                                        ===
                                        'draft'
                                    )

                                        <form
                                            method="POST"
                                            action="{{
                                                route(
                                                    'student-management.manual-transfer-certificate.issue',
                                                    $certificate
                                                )
                                            }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Issue this Manual Transfer Certificate?');"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-success"
                                            >
                                                Issue
                                            </button>

                                        </form>


                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#cancelModal{{ $certificate->id }}"
                                        >
                                            Cancel
                                        </button>

                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>

                            </tr>


                            @if(
                                $certificate->status
                                ===
                                'draft'
                            )

                                <div
                                    class="modal fade"
                                    id="cancelModal{{ $certificate->id }}"
                                    tabindex="-1"
                                >

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <form
                                                method="POST"
                                                action="{{
                                                    route(
                                                        'student-management.manual-transfer-certificate.cancel',
                                                        $certificate
                                                    )
                                                }}"
                                            >

                                                @csrf


                                                <div class="modal-header">

                                                    <h5 class="modal-title">
                                                        Cancel Manual TC
                                                    </h5>

                                                    <button
                                                        type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"
                                                    ></button>

                                                </div>


                                                <div class="modal-body">

                                                    <p>

                                                        TC:

                                                        <strong>
                                                            {{
                                                                $certificate
                                                                    ->tc_number
                                                            }}
                                                        </strong>

                                                        <br>

                                                        Student:

                                                        <strong>
                                                            {{
                                                                $certificate
                                                                    ->manual_student_name
                                                            }}
                                                        </strong>

                                                    </p>


                                                    <label class="form-label">
                                                        Cancellation Reason
                                                    </label>

                                                    <textarea
                                                        name="cancellation_reason"
                                                        class="form-control"
                                                        rows="3"
                                                        required
                                                    ></textarea>

                                                </div>


                                                <div class="modal-footer">

                                                    <button
                                                        type="button"
                                                        class="btn btn-secondary"
                                                        data-bs-dismiss="modal"
                                                    >
                                                        Close
                                                    </button>

                                                    <button
                                                        type="submit"
                                                        class="btn btn-danger"
                                                    >
                                                        Cancel TC
                                                    </button>

                                                </div>

                                            </form>

                                        </div>

                                    </div>

                                </div>

                            @endif

                        @empty

                            <tr>

                                <td
                                    colspan="11"
                                    class="text-center text-muted py-5"
                                >
                                    No Manual Transfer Certificates found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if($certificates->hasPages())

            <div class="card-footer bg-white">

                {{ $certificates->links() }}

            </div>

        @endif

    </div>

</div>

@endsection