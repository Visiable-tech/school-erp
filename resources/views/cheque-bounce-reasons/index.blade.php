@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Cheque Bounce Reasons
            </h4>

            <small class="text-muted">
                Manage cheque/DD dishonour reasons
            </small>

        </div>


        @can('cheque-bounce-reason.create')

            <a href="{{ route(
                'cheque-bounce-reasons.create'
            ) }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Add Reason

            </a>

        @endcan

    </div>


    <div class="card border-0 shadow-sm mb-3">

        <div class="card-body">

            <form method="GET">

                <div class="row g-2">

                    <div class="col-md-7">

                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search reason or code">

                    </div>


                    <div class="col-md-3">

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


                    <div class="col-md-1">

                        <button type="submit"
                                class="btn btn-primary w-100">

                            <i class="bi bi-search"></i>

                        </button>

                    </div>


                    <div class="col-md-1">

                        <a href="{{ route(
                            'cheque-bounce-reasons.index'
                        ) }}"
                           class="btn btn-light w-100">

                            <i class="bi bi-arrow-clockwise"></i>

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th>#</th>
                        <th>Reason</th>
                        <th>Code</th>
                        <th>Bounce Charge</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>

                </thead>


                <tbody>

                @forelse($reasons as $reason)

                    <tr>

                        <td>
                            {{ $reasons->firstItem()
                                + $loop->index }}
                        </td>


                        <td>
                            <strong>
                                {{ $reason->name }}
                            </strong>
                        </td>


                        <td>
                            {{ $reason->code ?: '—' }}
                        </td>


                        <td>

                            @if($reason->apply_charge)

                                <strong>
                                    ₹{{ number_format(
                                        $reason->bounce_charge,
                                        2
                                    ) }}
                                </strong>

                            @else

                                <span class="text-muted">
                                    No Charge
                                </span>

                            @endif

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


                        <td>

                            <div class="d-flex gap-1">

                                @can('cheque-bounce-reason.edit')

                                    <a href="{{ route(
                                        'cheque-bounce-reasons.edit',
                                        $reason
                                    ) }}"
                                       class="btn btn-sm btn-outline-primary">

                                        <i class="bi bi-pencil"></i>

                                    </a>

                                @endcan


                                @can('cheque-bounce-reason.delete')

                                    <form method="POST"
                                          action="{{ route(
                                              'cheque-bounce-reasons.destroy',
                                              $reason
                                          ) }}"
                                          onsubmit="return confirm(
                                              'Delete this bounce reason?'
                                          );">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                @endcan

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6"
                            class="text-center text-muted py-5">

                            No Cheque Bounce Reasons found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($reasons->hasPages())

            <div class="card-footer bg-white">
                {{ $reasons->links() }}
            </div>

        @endif

    </div>

</div>

@endsection