@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Misc. Components
            </h4>

            <small class="text-muted">
                Manage miscellaneous fee components
            </small>

        </div>


        @can('misc-fee-component.create')

            <a href="{{ route(
                'misc-fee-components.create'
            ) }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Add Misc. Component

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
                               value="{{ request('search') }}"
                               class="form-control"
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
                                    ? 'selected'
                                    : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ request('status') === '0'
                                    ? 'selected'
                                    : '' }}>
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
                            'misc-fee-components.index'
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
                        <th>#</th>
                        <th>Component</th>
                        <th>Code</th>
                        <th>Amount Type</th>
                        <th class="text-end">
                            Default Amount
                        </th>
                        <th>Refundable</th>
                        <th>Concession</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>

                </thead>


                <tbody>

                @forelse(
                    $components as $component
                )

                    <tr>

                        <td>
                            {{ $components->firstItem()
                                + $loop->index }}
                        </td>

                        <td>
                            <strong>
                                {{ $component->name }}
                            </strong>
                        </td>

                        <td>
                            {{ $component->code ?: '—' }}
                        </td>

                        <td>

                            @if($component->fixed_amount)

                                <span class="badge bg-primary">
                                    Fixed
                                </span>

                            @else

                                <span class="badge bg-info">
                                    Variable
                                </span>

                            @endif

                        </td>

                        <td class="text-end">

                            @if(
                                $component->default_amount
                                !== null
                            )

                                ₹{{ number_format(
                                    $component->default_amount,
                                    2
                                ) }}

                            @else
                                —
                            @endif

                        </td>

                        <td>
                            {{ $component->is_refundable
                                ? 'Yes'
                                : 'No' }}
                        </td>

                        <td>
                            {{ $component->allow_concession
                                ? 'Yes'
                                : 'No' }}
                        </td>

                        <td>

                            @if($component->status)

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

                                @can('misc-fee-component.edit')

                                    <a href="{{ route(
                                        'misc-fee-components.edit',
                                        $component
                                    ) }}"
                                       class="btn btn-sm
                                              btn-outline-primary">

                                        <i class="bi bi-pencil"></i>

                                    </a>

                                @endcan


                                @can('misc-fee-component.delete')

                                    <form method="POST"
                                          action="{{ route(
                                            'misc-fee-components.destroy',
                                            $component
                                          ) }}"
                                          onsubmit="return confirm(
                                            'Delete this Misc. Component?'
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

                        <td colspan="9"
                            class="text-center
                                   text-muted py-5">

                            No Misc. Components found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($components->hasPages())

            <div class="card-footer bg-white">
                {{ $components->links() }}
            </div>

        @endif

    </div>

</div>

@endsection