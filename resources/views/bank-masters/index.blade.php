@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Banks Master
            </h4>

            <small class="text-muted">
                Manage banks used by the school
            </small>

        </div>


        @can('bank-master.create')

            <a href="{{ route(
                'bank-masters.create'
            ) }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Add Bank

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
                               placeholder="Bank, branch, code or IFSC">

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
                            Search
                        </button>

                    </div>


                    <div class="col-md-2">

                        <a href="{{ route(
                            'bank-masters.index'
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
                        <th>Bank</th>
                        <th>Branch</th>
                        <th>IFSC</th>
                        <th>City</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>

                </thead>


                <tbody>

                @forelse($banks as $bank)

                    <tr>

                        <td>
                            {{ $banks->firstItem()
                                + $loop->index }}
                        </td>


                        <td>

                            <strong>
                                {{ $bank->bank_name }}
                            </strong>

                            @if($bank->short_name)

                                <div class="small text-muted">
                                    {{ $bank->short_name }}
                                </div>

                            @endif

                        </td>


                        <td>
                            {{ $bank->branch_name ?: '—' }}
                        </td>


                        <td>

                            @if($bank->ifsc_code)

                                <span class="badge
                                             bg-light
                                             text-dark
                                             border">

                                    {{ $bank->ifsc_code }}

                                </span>

                            @else
                                —
                            @endif

                        </td>


                        <td>
                            {{ $bank->city ?: '—' }}
                        </td>


                        <td>

                            @if($bank->contact_person)

                                {{ $bank->contact_person }}

                            @endif

                            @if($bank->phone)

                                <div class="small text-muted">
                                    {{ $bank->phone }}
                                </div>

                            @endif

                            @if(
                                !$bank->contact_person &&
                                !$bank->phone
                            )
                                —
                            @endif

                        </td>


                        <td>

                            @if($bank->status)

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

                                @can('bank-master.edit')

                                    <a href="{{ route(
                                        'bank-masters.edit',
                                        $bank
                                    ) }}"
                                       class="btn btn-sm
                                              btn-outline-primary">

                                        <i class="bi bi-pencil"></i>

                                    </a>

                                @endcan


                                @can('bank-master.delete')

                                    <form method="POST"
                                          action="{{ route(
                                            'bank-masters.destroy',
                                            $bank
                                          ) }}"
                                          onsubmit="return confirm(
                                            'Delete this bank?'
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

                        <td colspan="8"
                            class="text-center
                                   text-muted py-5">

                            No banks found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($banks->hasPages())

            <div class="card-footer bg-white">
                {{ $banks->links() }}
            </div>

        @endif

    </div>

</div>

@endsection