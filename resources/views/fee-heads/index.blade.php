@extends('layouts.admin')

@section('title', 'Fee Heads')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Fee Heads
        </h4>

        <div class="text-muted">
            Manage school fee categories.
        </div>

    </div>


    @can('fee-head.create')

        <a
            href="{{ route('fee-heads.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-circle me-1"></i>
            Add Fee Head
        </a>

    @endcan

</div>


{{-- FILTER --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('fee-heads.index') }}"
        >

            <div class="row g-2">

                <div class="col-md-5">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Search name or code..."
                    >

                </div>


                <div class="col-md-3">

                    <select
                        name="frequency"
                        class="form-select"
                    >

                        <option value="">
                            All Frequencies
                        </option>

                        <option
                            value="one_time"
                            @selected(request('frequency') == 'one_time')
                        >
                            One Time
                        </option>

                        <option
                            value="monthly"
                            @selected(request('frequency') == 'monthly')
                        >
                            Monthly
                        </option>

                        <option
                            value="quarterly"
                            @selected(request('frequency') == 'quarterly')
                        >
                            Quarterly
                        </option>

                        <option
                            value="half_yearly"
                            @selected(request('frequency') == 'half_yearly')
                        >
                            Half Yearly
                        </option>

                        <option
                            value="annual"
                            @selected(request('frequency') == 'annual')
                        >
                            Annual
                        </option>

                    </select>

                </div>


                <div class="col-md-2">

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="">
                            All Status
                        </option>

                        <option
                            value="1"
                            @selected(request('status') === '1')
                        >
                            Active
                        </option>

                        <option
                            value="0"
                            @selected(request('status') === '0')
                        >
                            Inactive
                        </option>

                    </select>

                </div>


                <div class="col-md-2 d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Search
                    </button>

                    <a
                        href="{{ route('fee-heads.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- LIST --}}

<div class="card border-0 shadow-sm">

    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">

                <tr>

                    <th width="80">
                        #
                    </th>

                    <th>
                        Fee Head
                    </th>

                    <th width="130">
                        Code
                    </th>

                    <th width="160">
                        Frequency
                    </th>

                    <th width="120">
                        Optional
                    </th>

                    <th width="120">
                        Status
                    </th>

                    <th width="150">
                        Action
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($feeHeads as $feeHead)

                    <tr>

                        <td>
                            {{ $feeHeads->firstItem() + $loop->index }}
                        </td>


                        <td class="fw-semibold">
                            {{ $feeHead->name }}
                        </td>


                        <td>
                            {{ $feeHead->code ?: '-' }}
                        </td>


                        <td>

                            @switch($feeHead->frequency)

                                @case('one_time')
                                    One Time
                                    @break

                                @case('monthly')
                                    Monthly
                                    @break

                                @case('quarterly')
                                    Quarterly
                                    @break

                                @case('half_yearly')
                                    Half Yearly
                                    @break

                                @case('annual')
                                    Annual
                                    @break

                            @endswitch

                        </td>


                        <td>

                            @if($feeHead->is_optional)

                                <span class="badge bg-info text-dark">
                                    Optional
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    Mandatory
                                </span>

                            @endif

                        </td>


                        <td>

                            @if($feeHead->status)

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

                                @can('fee-head.edit')

                                    <a
                                        href="{{ route(
                                            'fee-heads.edit',
                                            $feeHead
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('fee-head.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'fee-heads.destroy',
                                            $feeHead
                                        ) }}"
                                        onsubmit="
                                            return confirm(
                                                'Delete this Fee Head?'
                                            );
                                        "
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete"
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
                            colspan="7"
                            class="text-center py-5"
                        >

                            <i class="bi bi-cash-stack fs-1 text-muted"></i>

                            <h5 class="mt-3">
                                No Fee Heads Found
                            </h5>

                            <div class="text-muted mb-3">
                                Create your first fee head to start configuring fees.
                            </div>

                            @can('fee-head.create')

                                <a
                                    href="{{ route('fee-heads.create') }}"
                                    class="btn btn-primary"
                                >
                                    Add Fee Head
                                </a>

                            @endcan

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($feeHeads->hasPages())

        <div class="card-footer bg-white">

            {{ $feeHeads->links() }}

        </div>

    @endif

</div>

@endsection