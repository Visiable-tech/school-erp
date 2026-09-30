@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Late Fee Fine
            </h4>

            <small class="text-muted">
                Configure automatic late fee rules
            </small>

        </div>


        @can('late-fee-fine.create')

            <a href="{{ route(
                'late-fee-fines.create'
            ) }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Add Fine Rule

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
                               placeholder="Search rule or code">

                    </div>


                    <div class="col-md-3">

                        <select name="academic_year_id"
                                class="form-select">

                            <option value="">
                                All Academic Years
                            </option>

                            @foreach($academicYears as $year)

                                <option
                                    value="{{ $year->id }}"
                                    {{ request(
                                        'academic_year_id'
                                    ) == $year->id
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
                            'late-fee-fines.index'
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
                        <th>Rule</th>
                        <th>Academic Year</th>
                        <th>Fine Structure</th>
                        <th>Default</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>
                    </thead>

                    <tbody>

                    @forelse($rules as $rule)

                    <tr>

                        <td>
                            {{ $rules->firstItem() + $loop->index }}
                        </td>

                        <td>

                            <strong>
                                {{ $rule->name }}
                            </strong>

                            @if($rule->code)
                                <div class="small text-muted">
                                    {{ $rule->code }}
                                </div>
                            @endif

                        </td>

                        <td>
                            {{ optional(
                                $rule->academicYear
                            )->name ?: 'All Years' }}
                        </td>

                        <td>

                            @foreach($rule->slabs as $slab)

                                <div class="mb-1">

                                    @if(
                                        $slab->period_type
                                        === 'day_range'
                                    )

                                        Day {{ $slab->from_day }}

                                        -

                                        {{ $slab->to_day
                                            ?: 'Month End' }}

                                        :

                                        <strong>
                                            ₹{{ number_format(
                                                $slab->amount,
                                                2
                                            ) }}
                                        </strong>

                                    @else

                                        After First Month:

                                        <strong>
                                            ₹{{ number_format(
                                                $slab->amount,
                                                2
                                            ) }}/month
                                        </strong>

                                    @endif

                                </div>

                            @endforeach

                        </td>

                        <td>

                            @if($rule->is_default)

                                <span class="badge bg-primary">
                                    Default
                                </span>

                            @else
                                —
                            @endif

                        </td>

                        <td>

                            @if($rule->status)

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

                                @can('late-fee-fine.edit')

                                    <a href="{{ route(
                                        'late-fee-fines.edit',
                                        $rule
                                    ) }}"
                                    class="btn btn-sm
                                            btn-outline-primary">

                                        <i class="bi bi-pencil"></i>

                                    </a>

                                @endcan


                                @can('late-fee-fine.delete')

                                    <form method="POST"
                                        action="{{ route(
                                            'late-fee-fines.destroy',
                                            $rule
                                        ) }}"
                                        onsubmit="return confirm(
                                            'Delete this fine rule?'
                                        )">

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
                        <td colspan="7"
                            class="text-center py-5 text-muted">

                            No Late Fee Fine rules found.

                        </td>
                    </tr>

                    @endforelse

                    </tbody>

            </table>

        </div>


        @if($rules->hasPages())

            <div class="card-footer bg-white">
                {{ $rules->links() }}
            </div>

        @endif

    </div>

</div>

@endsection