@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Concession Types
            </h4>

            <small class="text-muted">
                Configure fee concession categories
            </small>

        </div>

        @can('concession-type.create')

            <a href="{{ route(
                'concession-types.create'
            ) }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Add Concession Type

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
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search name or code">

                    </div>


                    <div class="col-md-3">

                        <select name="mode"
                                class="form-select">

                            <option value="">
                                All Modes
                            </option>

                            <option value="percentage"
                                {{ request('mode')
                                    === 'percentage'
                                    ? 'selected'
                                    : '' }}>
                                Percentage
                            </option>

                            <option value="fixed"
                                {{ request('mode')
                                    === 'fixed'
                                    ? 'selected'
                                    : '' }}>
                                Fixed Amount
                            </option>

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

                        <button type="submit"
                                class="btn btn-primary w-100">

                            <i class="bi bi-search"></i>

                        </button>

                    </div>


                    <div class="col-md-1">

                        <a href="{{ route(
                            'concession-types.index'
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
                        <th>Concession</th>
                        <th>Mode</th>
                        <th>Default Value</th>
                        <th>Maximum</th>
                        <th>Approval</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>

                </thead>

                <tbody>

                @forelse(
                    $concessionTypes
                    as $concessionType
                )

                    <tr>

                        <td>
                            {{
                                $concessionTypes->firstItem()
                                + $loop->index
                            }}
                        </td>


                        <td>

                            <strong>
                                {{ $concessionType->name }}
                            </strong>

                            @if($concessionType->code)

                                <div class="small text-muted">
                                    {{ $concessionType->code }}
                                </div>

                            @endif

                        </td>


                        <td>

                            @if(
                                $concessionType->concession_mode
                                === 'percentage'
                            )

                                <span class="badge bg-info">
                                    Percentage
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    Fixed
                                </span>

                            @endif

                        </td>


                        <td>

                            @if(
                                $concessionType->default_value
                                === null
                            )

                                —

                            @elseif(
                                $concessionType->concession_mode
                                === 'percentage'
                            )

                                {{ number_format(
                                    $concessionType->default_value,
                                    2
                                ) }}%

                            @else

                                ₹{{ number_format(
                                    $concessionType->default_value,
                                    2
                                ) }}

                            @endif

                        </td>


                        <td>

                            @if(
                                $concessionType->maximum_amount
                                !== null
                            )

                                ₹{{ number_format(
                                    $concessionType->maximum_amount,
                                    2
                                ) }}

                            @else

                                No Limit

                            @endif

                        </td>


                        <td>

                            @if(
                                $concessionType->requires_approval
                            )

                                <span class="badge bg-warning text-dark">
                                    Required
                                </span>

                            @else

                                <span class="text-muted">
                                    No
                                </span>

                            @endif

                        </td>


                        <td>

                            @if($concessionType->status)

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

                                @can('concession-type.edit')

                                    <a href="{{ route(
                                        'concession-types.edit',
                                        $concessionType
                                    ) }}"
                                       class="btn btn-sm
                                              btn-outline-primary">

                                        <i class="bi bi-pencil"></i>

                                    </a>

                                @endcan


                                @can('concession-type.delete')

                                    <form method="POST"
                                          action="{{ route(
                                            'concession-types.destroy',
                                            $concessionType
                                          ) }}"
                                          onsubmit="return confirm(
                                            'Delete this concession type?'
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
                            class="text-center
                                   text-muted py-5">

                            No Concession Types found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($concessionTypes->hasPages())

            <div class="card-footer bg-white">
                {{ $concessionTypes->links() }}
            </div>

        @endif

    </div>

</div>

@endsection