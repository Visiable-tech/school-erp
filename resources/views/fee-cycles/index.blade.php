@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Fee Cycles
            </h4>

            <small class="text-muted">
                Manage fee collection cycles
            </small>

        </div>


        @can('fee-cycle.create')

            <a href="{{ route('fee-cycles.create') }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Add Fee Cycle

            </a>

        @endcan

    </div>


    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-3">

        <div class="card-body">

            <form method="GET">

                <div class="row g-2">

                    <div class="col-md-4">

                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search name or code">

                    </div>


                    <div class="col-md-3">

                        <select name="cycle_type"
                                class="form-select">

                            <option value="">
                                All Cycle Types
                            </option>

                            <option value="monthly"
                                {{ request('cycle_type') === 'monthly'
                                    ? 'selected' : '' }}>
                                Monthly
                            </option>

                            <option value="quarterly"
                                {{ request('cycle_type') === 'quarterly'
                                    ? 'selected' : '' }}>
                                Quarterly
                            </option>

                            <option value="half_yearly"
                                {{ request('cycle_type') === 'half_yearly'
                                    ? 'selected' : '' }}>
                                Half Yearly
                            </option>

                            <option value="annual"
                                {{ request('cycle_type') === 'annual'
                                    ? 'selected' : '' }}>
                                Annual
                            </option>

                            <option value="one_time"
                                {{ request('cycle_type') === 'one_time'
                                    ? 'selected' : '' }}>
                                One Time
                            </option>

                            <option value="custom"
                                {{ request('cycle_type') === 'custom'
                                    ? 'selected' : '' }}>
                                Custom
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
                                    ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ request('status') === '0'
                                    ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <button class="btn btn-primary w-100">

                            <i class="bi bi-search"></i>
                            Search

                        </button>

                    </div>


                    <div class="col-md-1">

                        <a href="{{ route('fee-cycles.index') }}"
                           class="btn btn-light w-100">

                            <i class="bi bi-arrow-clockwise"></i>

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    @can('fee-cycle.edit')
    <div class="d-flex align-items-center gap-2 mb-3">
        @if(request()->boolean('sorting'))
            <a href="{{ route('fee-cycles.index') }}" class="btn btn-outline-secondary btn-sm">Exit sorting</a>
            <span class="text-muted small">Drag rows using the handle. Order saves automatically.</span>
        @elseif(!request()->hasAny(['search','cycle_type','status']))
            <a href="{{ route('fee-cycles.index', ['sorting' => 1]) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-grip-vertical"></i> Reorder rows
            </a>
        @endif
        <span id="sortFeedback" class="small" role="status" aria-live="polite"></span>
    </div>
    @endcan

    {{-- List --}}
    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover
                          align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        @if(request()->boolean('sorting'))<th width="44">Move</th>@endif
                        <th width="60">#</th>

                        <th>
                            Cycle Name
                        </th>

                        <th>
                            Code
                        </th>

                        <th>
                            Type
                        </th>

                        <th class="text-center">
                            Installments
                        </th>

                        <th class="text-center">
                            Sort
                        </th>

                        <th>
                            Status
                        </th>

                        <th width="130">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody id="feeCycleSortable" data-url="{{ route('fee-cycles.reorder') }}" data-enabled="{{ request()->boolean('sorting') && auth()->user()->can('fee-cycle.edit') ? '1' : '0' }}">

                @forelse($feeCycles as $cycle)

                    <tr data-id="{{ $cycle->id }}">
                        @if(request()->boolean('sorting'))
                            <td><span class="sort-handle" title="Drag to reorder" style="cursor:grab"><i class="bi bi-grip-vertical"></i></span></td>
                        @endif
                        <td>
                            {{ request()->boolean('sorting') ? $loop->iteration : $feeCycles->firstItem() + $loop->index }}
                        </td>


                        <td>

                            <strong>
                                {{ $cycle->name }}
                            </strong>

                        </td>


                        <td>

                            @if($cycle->code)

                                <span class="badge bg-light
                                             text-dark border">
                                    {{ $cycle->code }}
                                </span>

                            @else

                                —

                            @endif

                        </td>


                        <td>

                            {{ ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $cycle->cycle_type
                                )
                            ) }}

                        </td>


                        <td class="text-center">

                            {{ $cycle->installments_count }}

                        </td>


                        <td class="text-center">

                            {{ $cycle->sort_order }}

                        </td>


                        <td>

                            @if($cycle->status)

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

                                @can('fee-cycle.edit')

                                    <a href="{{ route(
                                            'fee-cycles.edit',
                                            $cycle
                                        ) }}"
                                       class="btn btn-sm
                                              btn-outline-primary">

                                        <i class="bi bi-pencil"></i>

                                    </a>

                                @endcan


                                @can('fee-cycle.delete')

                                    <form method="POST"
                                          action="{{ route(
                                              'fee-cycles.destroy',
                                              $cycle
                                          ) }}"
                                          onsubmit="return confirm(
                                              'Delete this Fee Cycle?'
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

                        <td colspan="{{ request()->boolean('sorting') ? 9 : 8 }}"
                            class="text-center
                                   text-muted py-5">

                            No Fee Cycles found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if(!request()->boolean('sorting') && $feeCycles->hasPages())

            <div class="card-footer bg-white">

                {{ $feeCycles->links() }}

            </div>

        @endif

    </div>

</div>


@push('scripts')
@if(request()->boolean('sorting') && auth()->user()->can('fee-cycle.edit'))
<script src="{{ asset('js/erp-row-sort.js') }}"></script>
<script>
  window.ERPRowSort?.init(document.getElementById('feeCycleSortable'), {
    token: @json(csrf_token()),
    feedback: document.getElementById('sortFeedback')
  });
</script>
@endif
@endpush

@endsection