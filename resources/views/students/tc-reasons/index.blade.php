@extends('layouts.admin')

@section('title', 'List TC')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                T.C. Reasons
            </h4>

            <small class="text-muted">
                Student Information System → Masters
            </small>

        </div>


        @can('tc-reason.create')

            <a
                href="{{ route('tc-reasons.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-circle"></i>
                Add T.C. Reason
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
                action="{{ route('tc-reasons.index') }}"
            >

                <div class="row g-2">

                    <div class="col-md-5">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search reason or code..."
                            value="{{ request('search') }}"
                        >

                    </div>


                    <div class="col-md-3">

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option value="">
                                All Status
                            </option>

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


                    <div class="col-md-4">

                        <button
                            type="submit"
                            class="btn btn-dark"
                        >
                            <i class="bi bi-search"></i>
                            Search
                        </button>

                        <a
                            href="{{ route('tc-reasons.index') }}"
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

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0 align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="70">
                                #
                            </th>

                            <th>
                                Reason
                            </th>

                            <th>
                                Code
                            </th>

                            <th width="120">
                                Sort
                            </th>

                            <th width="120">
                                Status
                            </th>

                            <th
                                width="180"
                                class="text-end"
                            >
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($reasons as $reason)

                            <tr>

                                <td>
                                    {{ $reasons->firstItem() + $loop->index }}
                                </td>

                                <td>

                                    <strong>
                                        {{ $reason->name }}
                                    </strong>

                                    @if($reason->description)

                                        <div class="small text-muted">
                                            {{ $reason->description }}
                                        </div>

                                    @endif

                                </td>

                                <td>
                                    {{ $reason->code ?: '-' }}
                                </td>

                                <td>
                                    {{ $reason->sort_order }}
                                </td>

                                <td>

                                    @if($reason->status)

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

                                    @can('tc-reason.edit')

                                        <a
                                            href="{{ route(
                                                'tc-reasons.edit',
                                                $reason
                                            ) }}"
                                            class="btn btn-sm btn-warning"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                    @endcan


                                    @can('tc-reason.delete')

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'tc-reasons.destroy',
                                                $reason
                                            ) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this T.C. Reason?');"
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
                                    colspan="6"
                                    class="text-center py-4 text-muted"
                                >
                                    No T.C. Reasons found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if($reasons->hasPages())

            <div class="card-footer">

                {{ $reasons->links() }}

            </div>

        @endif

    </div>

</div>

@endsection