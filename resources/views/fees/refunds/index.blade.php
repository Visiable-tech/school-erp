@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">

    <h4 class="mb-4">Fee Refunds</h4>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Refund ID</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds ?? [] as $refund)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $refund->id }}</td>
                                <td>{{ $refund->amount ?? '-' }}</td>
                                <td>{{ $refund->status ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    No refunds found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection