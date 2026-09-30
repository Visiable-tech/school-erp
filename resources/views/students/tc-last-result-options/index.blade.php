@extends('layouts.admin')

@section('title', 'TC Last Result Options')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-0">T.C. Last Result Options</h4>

            <small class="text-muted">
                Student Information System → Masters
            </small>
        </div>

        @can('tc-last-result-option.create')

            <a
                href="{{ route('tc-last-result-options.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-circle"></i>
                Add Result Option
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
                action="{{ route('tc-last-result-options.index') }}"
            >

                <div class="row g-2">

                    <div class="col-md-5">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Search result option or code..."
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
                            href="{{ route('tc-last-result-options.index') }}"
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
                        <th>Result Option</th>
                        <th>Code</th>
                        <th width="100">Sort</th>
                        <th width="120">Status</th>
                        <th width="180" class="text-end">
                            Action
                        </th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($results as $result)

                        <tr>

                            <td>
                                {{ $results->firstItem() + $loop->index }}
                            </td>

                            <td>

                                <strong>
                                    {{ $result->name }}
                                </strong>

                                @if($result->description)

                                    <div class="small text-muted">
                                        {{ $result->description }}
                                    </div>

                                @endif

                            </td>

                            <td>
                                {{ $result->code ?: '-' }}
                            </td>

                            <td>
                                {{ $result->sort_order }}
                            </td>

                            <td>

                                @if($result->status)

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

                                @can('tc-last-result-option.edit')

                                    <a
                                        href="{{ route(
                                            'tc-last-result-options.edit',
                                            $result
                                        ) }}"
                                        class="btn btn-sm btn-warning"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('tc-last-result-option.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'tc-last-result-options.destroy',
                                            $result
                                        ) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this option?');"
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
                                class="text-center text-muted py-4"
                            >
                                No T.C. Last Result Options found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($results->hasPages())

            <div class="card-footer">
                {{ $results->links() }}
            </div>

        @endif

    </div>

</div>

@endsection