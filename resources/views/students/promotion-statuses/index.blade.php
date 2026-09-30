@extends('layouts.admin')

@section('title', 'Promotion Statuses')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-0">Promotion Statuses</h4>

            <small class="text-muted">
                Student Information System → Masters
            </small>
        </div>

        @can('promotion-status.create')

            <a
                href="{{ route('promotion-statuses.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-circle"></i>
                Add Promotion Status
            </a>

        @endcan

    </div>


    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    <div class="card shadow-sm mb-3">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('promotion-statuses.index') }}"
            >

                <div class="row g-2">

                    <div class="col-md-4">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Search status or code..."
                        >

                    </div>


                    <div class="col-md-3">

                        <select name="type" class="form-select">

                            <option value="">All Types</option>

                            <option value="promoted"
                                {{ request('type') === 'promoted' ? 'selected' : '' }}>
                                Promoted
                            </option>

                            <option value="repeated"
                                {{ request('type') === 'repeated' ? 'selected' : '' }}>
                                Repeated
                            </option>

                            <option value="detained"
                                {{ request('type') === 'detained' ? 'selected' : '' }}>
                                Detained
                            </option>

                            <option value="conditional"
                                {{ request('type') === 'conditional' ? 'selected' : '' }}>
                                Conditional
                            </option>

                            <option value="other"
                                {{ request('type') === 'other' ? 'selected' : '' }}>
                                Other
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <select name="status" class="form-select">

                            <option value="">All Status</option>

                            <option
                                value="1"
                                {{ request('status') === '1' ? 'selected' : '' }}
                            >
                                Active
                            </option>

                            <option
                                value="0"
                                {{ request('status') === '0' ? 'selected' : '' }}
                            >
                                Inactive
                            </option>

                        </select>

                    </div>


                    <div class="col-md-3">

                        <button
                            type="submit"
                            class="btn btn-dark"
                        >
                            <i class="bi bi-search"></i>
                            Search
                        </button>

                        <a
                            href="{{ route('promotion-statuses.index') }}"
                            class="btn btn-light"
                        >
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <div class="card shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th width="70">#</th>
                        <th>Status Name</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th width="100">Sort</th>
                        <th width="120">Status</th>
                        <th width="180" class="text-end">Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($statuses as $promotionStatus)

                        <tr>

                            <td>
                                {{ $statuses->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <strong>
                                    {{ $promotionStatus->name }}
                                </strong>

                                @if($promotionStatus->description)
                                    <div class="small text-muted">
                                        {{ $promotionStatus->description }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                {{ $promotionStatus->code ?: '-' }}
                            </td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ ucfirst($promotionStatus->type) }}
                                </span>
                            </td>

                            <td>
                                {{ $promotionStatus->sort_order }}
                            </td>

                            <td>

                                @if($promotionStatus->status)

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            <td class="text-end">

                                @can('promotion-status.edit')

                                    <a
                                        href="{{ route(
                                            'promotion-statuses.edit',
                                            $promotionStatus
                                        ) }}"
                                        class="btn btn-sm btn-warning"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('promotion-status.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'promotion-statuses.destroy',
                                            $promotionStatus
                                        ) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this Promotion Status?');"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

                                @endcan

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="7"
                                class="text-center text-muted py-4"
                            >
                                No Promotion Statuses found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($statuses->hasPages())

            <div class="card-footer">
                {{ $statuses->links() }}
            </div>

        @endif

    </div>

</div>

@endsection