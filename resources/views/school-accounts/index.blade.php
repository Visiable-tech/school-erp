@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                School Accounts
            </h4>

            <small class="text-muted">
                Manage bank accounts used by the school
            </small>

        </div>


        @can('school-account.create')

            <a href="{{ route(
                'school-accounts.create'
            ) }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Add School Account

            </a>

        @endcan

    </div>


    <div class="card border-0 shadow-sm mb-3">

        <div class="card-body">

            <form method="GET">

                <div class="row g-2">

                    <div class="col-md-5">

                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Account, bank, UPI or purpose">

                    </div>


                    <div class="col-md-3">

                        <select name="bank_id"
                                class="form-select">

                            <option value="">
                                All Banks
                            </option>

                            @foreach($banks as $bank)

                                <option
                                    value="{{ $bank->id }}"
                                    {{ request('bank_id') == $bank->id
                                        ? 'selected'
                                        : '' }}>

                                    {{ $bank->bank_name }}

                                </option>

                            @endforeach

                        </select>

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


                    <div class="col-md-1">

                        <button class="btn
                                       btn-primary
                                       w-100">

                            <i class="bi bi-search"></i>

                        </button>

                    </div>


                    <div class="col-md-1">

                        <a href="{{ route(
                            'school-accounts.index'
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

            <table class="table table-hover
                          align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th>#</th>
                        <th>Account</th>
                        <th>Bank</th>
                        <th>Account No.</th>
                        <th>Type</th>
                        <th>Purpose</th>
                        <th>UPI</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>

                </thead>


                <tbody>

                @forelse($accounts as $account)

                    <tr>

                        <td>
                            {{ $accounts->firstItem()
                                + $loop->index }}
                        </td>


                        <td>

                            <strong>
                                {{ $account->account_name }}
                            </strong>

                            @if($account->is_default)

                                <div>
                                    <span class="badge bg-primary">
                                        Default
                                    </span>
                                </div>

                            @endif

                        </td>


                        <td>

                            {{ optional(
                                $account->bank
                            )->bank_name ?: '—' }}

                            @if(
                                optional(
                                    $account->bank
                                )->branch_name
                            )

                                <div class="small text-muted">

                                    {{ $account
                                        ->bank
                                        ->branch_name }}

                                </div>

                            @endif

                        </td>


                        <td>
                            {{ $account->account_number }}
                        </td>


                        <td>

                            {{ ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $account->account_type
                                )
                            ) }}

                        </td>


                        <td>
                            {{ $account->purpose ?: '—' }}
                        </td>


                        <td>
                            {{ $account->upi_id ?: '—' }}
                        </td>


                        <td>

                            @if($account->status)

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

                                @can('school-account.edit')

                                    <a href="{{ route(
                                        'school-accounts.edit',
                                        $account
                                    ) }}"
                                       class="btn btn-sm
                                              btn-outline-primary">

                                        <i class="bi bi-pencil"></i>

                                    </a>

                                @endcan


                                @can('school-account.delete')

                                    <form method="POST"
                                          action="{{ route(
                                            'school-accounts.destroy',
                                            $account
                                          ) }}"
                                          onsubmit="return confirm(
                                            'Delete this School Account?'
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

                            No School Accounts found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($accounts->hasPages())

            <div class="card-footer bg-white">
                {{ $accounts->links() }}
            </div>

        @endif

    </div>

</div>

@endsection