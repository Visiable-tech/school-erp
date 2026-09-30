@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Receipt No. Scheme
            </h4>

            <small class="text-muted">
                Configure automatic fee receipt numbering
            </small>
        </div>


        @can('fee-receipt-scheme.create')

            <a href="{{ route(
                'fee-receipt-schemes.create'
            ) }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Add Scheme

            </a>

        @endcan

    </div>


    <div class="card border-0 shadow-sm mb-3">

        <div class="card-body">

            <form method="GET">

                <div class="row g-2">

                    <div class="col-md-5">

                        <input type="text"
                               name="search"
                               class="form-control"
                               value="{{ request('search') }}"
                               placeholder="Search scheme, code or prefix">

                    </div>


                    <div class="col-md-3">

                        <select name="academic_year_id"
                                class="form-select">

                            <option value="">
                                All Academic Years
                            </option>

                            @foreach($academicYears as $year)

                                <option value="{{ $year->id }}"
                                    {{ request('academic_year_id') == $year->id
                                        ? 'selected'
                                        : '' }}>

                                    {{ $year->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-2">

                        <select name="status"
                                class="form-select">

                            <option value="">
                                All Status
                            </option>

                            <option value="1"
                                {{ request('status') === '1'
                                    ? 'selected'
                                    : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ request('status') === '0'
                                    ? 'selected'
                                    : '' }}>
                                Inactive
                            </option>

                        </select>

                    </div>


                    <div class="col-md-1">

                        <button class="btn btn-primary w-100">
                            <i class="bi bi-search"></i>
                        </button>

                    </div>


                    <div class="col-md-1">

                        <a href="{{ route(
                            'fee-receipt-schemes.index'
                        ) }}"
                           class="btn btn-light w-100">

                            <i class="bi bi-arrow-clockwise"></i>

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover
                          align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th>#</th>
                        <th>Scheme</th>
                        <th>Academic Year</th>
                        <th>Format / Next Receipt</th>
                        <th>Last No.</th>
                        <th>Default</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>

                </thead>

                <tbody>

                @forelse($schemes as $scheme)

                    <tr>

                        <td>
                            {{ $schemes->firstItem()
                                + $loop->index }}
                        </td>


                        <td>

                            <strong>
                                {{ $scheme->name }}
                            </strong>

                            @if($scheme->code)

                                <div class="small text-muted">
                                    {{ $scheme->code }}
                                </div>

                            @endif

                        </td>


                        <td>

                            {{ optional(
                                $scheme->academicYear
                            )->name ?: 'All Years' }}

                        </td>


                        <td>

                            <code>
                                {{ $scheme
                                    ->previewNextNumber() }}
                            </code>

                        </td>


                        <td>
                            {{ $scheme->last_number }}
                        </td>


                        <td>

                            @if($scheme->is_default)

                                <span class="badge bg-primary">
                                    Default
                                </span>

                            @else
                                —
                            @endif

                        </td>


                        <td>

                            @if($scheme->status)

                                <span class="badge bg-success">
                                    Active
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    Inactive
                                </span>

                            @endif

                        </td>


                        <td>

                            <div class="d-flex gap-1">

                                @can('fee-receipt-scheme.edit')

                                    <a href="{{ route(
                                        'fee-receipt-schemes.edit',
                                        $scheme
                                    ) }}"
                                       class="btn btn-sm
                                              btn-outline-primary">

                                        <i class="bi bi-pencil"></i>

                                    </a>

                                @endcan


                                @can('fee-receipt-scheme.delete')

                                    <form method="POST"
                                          action="{{ route(
                                            'fee-receipt-schemes.destroy',
                                            $scheme
                                          ) }}"
                                          onsubmit="return confirm(
                                            'Delete this Receipt Scheme?'
                                          );">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm
                                                       btn-outline-danger">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                @endcan

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8"
                            class="text-center text-muted py-5">

                            No Receipt No. Schemes found.

                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($schemes->hasPages())

            <div class="card-footer bg-white">
                {{ $schemes->links() }}
            </div>

        @endif

    </div>

</div>

@endsection