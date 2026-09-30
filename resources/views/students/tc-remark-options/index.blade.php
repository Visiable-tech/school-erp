@extends('layouts.admin')

@section('title', 'TC Remarks List')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                T.C. Remark Options
            </h4>

            <small class="text-muted">
                Student Information System → Masters
            </small>

        </div>


        @can('tc-remark-option.create')

            <a
                href="{{ route('tc-remark-options.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-circle"></i>
                Add Remark
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
                action="{{ route('tc-remark-options.index') }}"
            >

                <div class="row g-2">

                    <div class="col-md-5">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Search remark or code..."
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
                            class="btn btn-dark"
                            type="submit"
                        >
                            <i class="bi bi-search"></i>
                            Search
                        </button>


                        <a
                            href="{{ route('tc-remark-options.index') }}"
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

                        <th width="70">
                            #
                        </th>

                        <th>
                            Remark
                        </th>

                        <th>
                            Code
                        </th>

                        <th width="100">
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

                    @forelse($remarks as $remark)

                        <tr>

                            <td>
                                {{ $remarks->firstItem() + $loop->index }}
                            </td>


                            <td>

                                <strong>
                                    {{ $remark->name }}
                                </strong>

                                @if($remark->description)

                                    <div class="small text-muted">
                                        {{ $remark->description }}
                                    </div>

                                @endif

                            </td>


                            <td>
                                {{ $remark->code ?: '-' }}
                            </td>


                            <td>
                                {{ $remark->sort_order }}
                            </td>


                            <td>

                                @if($remark->status)

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

                                @can('tc-remark-option.edit')

                                    <a
                                        href="{{ route(
                                            'tc-remark-options.edit',
                                            $remark
                                        ) }}"
                                        class="btn btn-sm btn-warning"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('tc-remark-option.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'tc-remark-options.destroy',
                                            $remark
                                        ) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this T.C. Remark Option?');"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            class="btn btn-sm btn-danger"
                                            type="submit"
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
                                No T.C. Remark Options found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($remarks->hasPages())

            <div class="card-footer">

                {{ $remarks->links() }}

            </div>

        @endif

    </div>

</div>

@endsection