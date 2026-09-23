@extends('layouts.admin')

@section('title', 'Fee Structures')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Fee Structures
        </h4>

        <div class="text-muted">
            Manage class-wise fee structures.
        </div>

    </div>


    <div class="d-flex gap-2">

        @can('fee-structure.import')

            <button
                type="button"
                class="btn btn-outline-success"
                disabled
                title="Excel import will be added next"
            >
                <i class="bi bi-file-earmark-excel me-1"></i>
                Import Excel
            </button>

        @endcan


        @can('fee-structure.create')

            <a
                href="{{ route('fee-structures.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-circle me-1"></i>
                Add Fee Structure
            </a>

        @endcan

    </div>

</div>


<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('fee-structures.index') }}"
        >

            <div class="row g-2">

                <div class="col-md-3">

                    <select
                        name="academic_year_id"
                        class="form-select"
                    >

                        <option value="">
                            All Academic Years
                        </option>

                        @foreach($academicYears as $year)

                            <option
                                value="{{ $year->id }}"
                                @selected(
                                    request('academic_year_id')
                                        == $year->id
                                )
                            >
                                {{ $year->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-3">

                    <select
                        name="school_class_id"
                        class="form-select"
                    >

                        <option value="">
                            All Classes
                        </option>

                        @foreach($classes as $class)

                            <option
                                value="{{ $class->id }}"
                                @selected(
                                    request('school_class_id')
                                        == $class->id
                                )
                            >
                                {{ $class->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-3">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Structure name..."
                    >

                </div>


                <div class="col-md-3 d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Search
                    </button>

                    <a
                        href="{{ route('fee-structures.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>

</div>


<div class="card border-0 shadow-sm">

    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">

                <tr>
                    <th>#</th>
                    <th>Structure</th>
                    <th>Academic Year</th>
                    <th>Class</th>
                    <th>Fee Heads</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th width="140">Action</th>
                </tr>

            </thead>


            <tbody>

                @forelse($feeStructures as $structure)

                    <tr>

                        <td>
                            {{ $feeStructures->firstItem() + $loop->index }}
                        </td>


                        <td>

                            <div class="fw-semibold">
                                {{ $structure->name }}
                            </div>

                        </td>


                        <td>
                            {{ $structure->academicYear?->name }}
                        </td>


                        <td>
                            {{ $structure->schoolClass?->name }}
                        </td>


                        <td>

                            <span class="badge bg-secondary">

                                {{ $structure->items->count() }}

                                Fee Heads

                            </span>

                        </td>


                        <td class="fw-semibold">

                            ₹{{ number_format(
                                $structure->items->sum('amount'),
                                2
                            ) }}

                        </td>


                        <td>

                            @if($structure->status)

                                <span class="badge bg-success">
                                    Active
                                </span>

                            @else

                                <span class="badge bg-danger">
                                    Inactive
                                </span>

                            @endif

                        </td>


                        <td>

                            <div class="d-flex gap-1">

                                @can('fee-installment.view')

                                    <a
                                        href="{{ route(
                                            'fee-installments.index',
                                            $structure
                                        ) }}"
                                        class="btn btn-sm btn-outline-success"
                                        title="Fee Schedule"
                                    >
                                        <i class="bi bi-calendar3"></i>
                                    </a>

                                @endcan

                                @can('fee-structure.edit')

                                    <a
                                        href="{{ route(
                                            'fee-structures.edit',
                                            $structure
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('fee-structure.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'fee-structures.destroy',
                                            $structure
                                        ) }}"
                                        onsubmit="
                                            return confirm(
                                                'Delete this Fee Structure?'
                                            );
                                        "
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

                                @endcan

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="text-center py-5"
                        >

                            <i class="bi bi-cash-coin fs-1 text-muted"></i>

                            <h5 class="mt-3">
                                No Fee Structures Found
                            </h5>

                            <div class="text-muted">
                                Configure your first class-wise fee structure.
                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($feeStructures->hasPages())

        <div class="card-footer bg-white">
            {{ $feeStructures->links() }}
        </div>

    @endif

</div>

@endsection