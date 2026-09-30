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

            

                <div class="col-md-4">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Search name or code..."
                    >

                </div>


                <div class="col-md-3">

                    <select name="group_id"
                            class="form-select">

                        <option value="">
                            All Component Groups
                        </option>

                        @foreach($groups as $group)

                            <option value="{{ $group->id }}"
                                {{ request('group_id') == $group->id
                                    ? 'selected'
                                    : '' }}>

                                {{ $group->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-3">

                    <select name="cycle_id"
                            class="form-select">

                        <option value="">
                            All Fee Cycles
                        </option>

                        @foreach($cycles as $cycle)

                            <option value="{{ $cycle->id }}"
                                {{ request('cycle_id') == $cycle->id
                                    ? 'selected'
                                    : '' }}>

                                {{ $cycle->name }}

                            </option>

                        @endforeach

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
                    <th>#</th>
                    <th>Component</th>
                    <th>Code</th>
                    <th>Group</th>
                    <th>Fee Cycle</th>
                    <th>Type</th>
                    <th>Concession</th>
                    <th>Waiver</th>
                    <th>Status</th>
                    <th width="120">Action</th>
                </tr>
                </thead>

                <tbody>

                @forelse($feeHeads as $feeHead)

                <tr>

                    <td>
                        {{ $feeHeads->firstItem() + $loop->index }}
                    </td>

                    <td>
                        <strong>{{ $feeHead->name }}</strong>
                    </td>

                    <td>
                        {{ $feeHead->code ?: '—' }}
                    </td>

                    <td>
                        {{ optional($feeHead->componentGroup)->name ?: '—' }}
                    </td>

                    <td>
                        {{ optional($feeHead->feeCycle)->name ?: '—' }}
                    </td>

                    <td>

                        @if($feeHead->is_optional)

                            <span class="badge bg-info">
                                Optional
                            </span>

                        @else

                            <span class="badge bg-primary">
                                Regular
                            </span>

                        @endif

                        @if($feeHead->is_refundable)

                            <span class="badge bg-warning text-dark">
                                Refundable
                            </span>

                        @endif

                    </td>

                    <td>
                        {{ $feeHead->allow_concession ? 'Yes' : 'No' }}
                    </td>

                    <td>
                        {{ $feeHead->allow_waiver ? 'Yes' : 'No' }}
                    </td>

                    <td>

                        @if($feeHead->status)

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

                            @can('fee-head.edit')

                            <a href="{{ route(
                                    'fee-heads.edit',
                                    $feeHead
                                ) }}"
                            class="btn btn-sm btn-outline-primary">

                                <i class="bi bi-pencil"></i>

                            </a>

                            @endcan


                            @can('fee-head.delete')

                            <form method="POST"
                                action="{{ route(
                                    'fee-heads.destroy',
                                    $feeHead
                                ) }}"
                                onsubmit="return confirm(
                                    'Delete this Fee Component?'
                                );">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-outline-danger">

                                    <i class="bi bi-trash"></i>

                                </button>

                            </form>

                            @endcan

                        </div>

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="10"
                        class="text-center text-muted py-5">

                        No Fee Components found.

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