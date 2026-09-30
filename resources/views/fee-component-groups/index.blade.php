@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Fee Component Groups
            </h4>

            <small class="text-muted">
                Organize fee components into groups
            </small>

        </div>


        @can('fee-component-group.create')

            <a href="{{ route(
                    'fee-component-groups.create'
                ) }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Add Component Group

            </a>

        @endcan

    </div>


    <div class="card border-0 shadow-sm mb-3">

        <div class="card-body">

            <form method="GET">

                <div class="row g-2">

                    <div class="col-md-6">

                        <input type="text"
                               name="search"
                               class="form-control"
                               value="{{ request('search') }}"
                               placeholder="Search name or code">

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


                    <div class="col-md-2">

                        <a href="{{ route(
                                'fee-component-groups.index'
                            ) }}"
                           class="btn btn-light w-100">

                            Reset

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
                        <th width="60">#</th>
                        <th>Group Name</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th class="text-center">
                            Sort
                        </th>
                        <th>Status</th>
                        <th width="130">
                            Action
                        </th>
                    </tr>

                </thead>


                <tbody>

                @forelse($groups as $group)

                    <tr>

                        <td>
                            {{ $groups->firstItem()
                                + $loop->index }}
                        </td>


                        <td>
                            <strong>
                                {{ $group->name }}
                            </strong>
                        </td>


                        <td>

                            @if($group->code)

                                <span class="badge
                                             bg-light
                                             text-dark
                                             border">
                                    {{ $group->code }}
                                </span>

                            @else
                                —
                            @endif

                        </td>


                        <td>
                            {{ $group->description ?: '—' }}
                        </td>


                        <td class="text-center">
                            {{ $group->sort_order }}
                        </td>


                        <td>

                            @if($group->status)

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

                                @can('fee-component-group.edit')

                                    <a href="{{ route(
                                            'fee-component-groups.edit',
                                            $group
                                        ) }}"
                                       class="btn btn-sm
                                              btn-outline-primary">

                                        <i class="bi bi-pencil"></i>

                                    </a>

                                @endcan


                                @can('fee-component-group.delete')

                                    <form method="POST"
                                          action="{{ route(
                                              'fee-component-groups.destroy',
                                              $group
                                          ) }}"
                                          onsubmit="return confirm(
                                              'Delete this Fee Component Group?'
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

                        <td colspan="7"
                            class="text-center
                                   text-muted py-5">

                            No Fee Component Groups found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($groups->hasPages())

            <div class="card-footer bg-white">
                {{ $groups->links() }}
            </div>

        @endif

    </div>

</div>

@endsection