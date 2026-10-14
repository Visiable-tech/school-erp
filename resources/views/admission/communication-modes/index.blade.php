@extends('layouts.admin')

@section('title', 'Communication Modes')

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
                Communication Modes
            </h5>

            @can('admission-communication-mode.create')

                <a
                    href="{{ route(
                        'admission-communication-modes.create'
                    ) }}"
                    class="btn btn-primary btn-sm"
                >
                    <i class="bi bi-plus-circle me-1"></i>
                    Add Communication Mode
                </a>

            @endcan

        </div>


        <div class="card-body">

            <form
                method="GET"
                action="{{ route(
                    'admission-communication-modes.index'
                ) }}"
                class="row g-2 mb-3"
            >

                <div class="col-md-6">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search name or code..."
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
                            @selected(
                                request('status') === '1'
                            )
                        >
                            Active
                        </option>

                        <option
                            value="0"
                            @selected(
                                request('status') === '0'
                            )
                        >
                            Inactive
                        </option>

                    </select>

                </div>


                <div class="col-md-3">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Search
                    </button>

                    <a
                        href="{{ route(
                            'admission-communication-modes.index'
                        ) }}"
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

                            <th width="70">
                                #
                            </th>

                            <th>
                                Communication Mode
                            </th>

                            <th width="150">
                                Code
                            </th>

                            <th>
                                Description
                            </th>

                            <th width="100">
                                Order
                            </th>

                            <th width="110">
                                Status
                            </th>

                            <th width="170">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse(
                            $communicationModes
                            as $mode
                        )

                            <tr>

                                <td>
                                    {{
                                        $communicationModes->firstItem()
                                        + $loop->index
                                    }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $mode->name }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $mode->code ?: '-' }}
                                </td>

                                <td>
                                    {{ $mode->description ?: '-' }}
                                </td>

                                <td>
                                    {{ $mode->sort_order }}
                                </td>

                                <td>

                                    @if($mode->status)

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

                                    @can('admission-communication-mode.edit')

                                        <a
                                            href="{{ route(
                                                'admission-communication-modes.edit',
                                                $mode
                                            ) }}"
                                            class="btn btn-warning btn-sm"
                                        >
                                            Edit
                                        </a>

                                    @endcan


                                    @can('admission-communication-mode.delete')

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admission-communication-modes.destroy',
                                                $mode
                                            ) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this communication mode?')"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-danger btn-sm"
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
                                    No communication modes found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="mt-3">

                {{ $communicationModes->links() }}

            </div>

        </div>

    </div>

</div>

@endsection