@extends('layouts.admin')

@section('title', 'Boards / Universities')

@section('content')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                Boards / Universities
            </h5>

            @can('admission-board.create')
                <a
                    href="{{ route('admission-boards.create') }}"
                    class="btn btn-primary btn-sm"
                >
                    <i class="bi bi-plus-circle me-1"></i>
                    Add Board / University
                </a>
            @endcan

        </div>


        <div class="card-body">

            <form
                method="GET"
                action="{{ route('admission-boards.index') }}"
                class="row g-2 mb-3"
            >

                <div class="col-md-5">
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search name or code..."
                        value="{{ request('search') }}"
                    >
                </div>


                <div class="col-md-2">

                    <select
                        name="type"
                        class="form-select"
                    >
                        <option value="">
                            All Types
                        </option>

                        <option
                            value="board"
                            @selected(request('type') === 'board')
                        >
                            Board
                        </option>

                        <option
                            value="university"
                            @selected(request('type') === 'university')
                        >
                            University
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


                <div class="col-md-3">

                    <button
                        class="btn btn-primary"
                        type="submit"
                    >
                        Search
                    </button>

                    <a
                        href="{{ route('admission-boards.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Reset
                    </a>

                </div>

            </form>


            <div class="table-responsive">

                <table class="table table-bordered table-striped table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th width="60">#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th width="100">Order</th>
                            <th width="110">Status</th>
                            <th width="160">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($boards as $board)

                            <tr>

                                <td>
                                    {{
                                        $boards->firstItem()
                                        + $loop->index
                                    }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $board->name }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $board->code ?: '-' }}
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst($board->type) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $board->sort_order }}
                                </td>

                                <td>

                                    @if($board->status)

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

                                    @can('admission-board.edit')

                                        <a
                                            href="{{ route(
                                                'admission-boards.edit',
                                                $board
                                            ) }}"
                                            class="btn btn-sm btn-warning"
                                        >
                                            Edit
                                        </a>

                                    @endcan


                                    @can('admission-board.delete')

                                        <form
                                            action="{{ route(
                                                'admission-boards.destroy',
                                                $board
                                            ) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this Board/University?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                            >
                                                Delete
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
                                    No Board/University found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="mt-3">
                {{ $boards->links() }}
            </div>

        </div>

    </div>

</div>

@endsection