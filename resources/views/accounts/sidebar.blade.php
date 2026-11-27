@can('accounts.view')
@php
    $reportRoute = 'accounts.reports.index';
    $reportKey = request('report', 'cashflow');
    $groups = [
        ['expenses','Income & Expenses','bi-receipt',[
            ['Expense Register','accounts.expenses',[],null],
            ['Expense Categories','accounts.categories',[],'accounts.category.manage'],
            ['Expense Payment Report',$reportRoute,['report'=>'expenses'],'accounts.reports.view'],
        ]],
        ['vendors','Vendor Management','bi-building',[
            ['Vendor List / Invoices / Payments','accounts.vendors.index',[],null],
            ['Vendor Outstanding',$reportRoute,['report'=>'vendors'],'accounts.reports.view'],
        ]],
        ['salary','Salary & Payroll','bi-cash-stack',[
            ['Salary Register & Disbursement','accounts.salary.index',[],'accounts.salary.view'],
            ['Pending Salaries',$reportRoute,['report'=>'salary'],'accounts.reports.view'],
        ]],
        ['ledger','Bank, Cash & Ledger','bi-bank',[
            ['Chart of Accounts & Journal Entries','accounts.ledger.index',[],'accounts.ledger.view'],
            ['Cash Book',$reportRoute,['report'=>'cash'],'accounts.reports.view'],
            ['Bank Book',$reportRoute,['report'=>'bank'],'accounts.reports.view'],
            ['General Ledger',$reportRoute,['report'=>'ledger'],'accounts.reports.view'],
            ['Trial Balance',$reportRoute,['report'=>'trial'],'accounts.reports.view'],
        ]],
        ['reports','Reports & MIS','bi-bar-chart',[
            ['Receipts & Payments',$reportRoute,['report'=>'cashflow'],'accounts.reports.view'],
            ['Income & Expenditure',$reportRoute,['report'=>'income'],'accounts.reports.view'],
            ['Day Book',$reportRoute,['report'=>'daybook'],'accounts.reports.view'],
            ['Balance Sheet',$reportRoute,['report'=>'balance'],'accounts.reports.view'],
            ['Fee Collection Report',$reportRoute,['report'=>'fees'],'accounts.reports.view'],
        ]],
    ];
@endphp
@if(\Illuminate\Support\Facades\Route::has('accounts.index'))
<a href="{{ route('accounts.index') }}" class="{{ request()->routeIs('accounts.index') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Accounts Dashboard</a>
@endif
@endcan

@include('partials.admin.menus.fees')

@can('accounts.view')
@foreach($groups as [$key,$label,$icon,$items])
    @php
        $visible = collect($items)->filter(fn($item) => \Illuminate\Support\Facades\Route::has($item[1]) && (!$item[3] || auth()->user()->can($item[3])));
        $isActive = function($item) use ($reportRoute,$reportKey) {
            if ($item[1] === $reportRoute) return request()->routeIs($reportRoute) && $reportKey === $item[2]['report'];
            if ($item[1] === 'accounts.vendors.index') return request()->routeIs('accounts.vendors.*');
            if ($item[1] === 'accounts.salary.index') return request()->routeIs('accounts.salary.*');
            if ($item[1] === 'accounts.ledger.index') return request()->routeIs('accounts.ledger.*');
            return request()->routeIs($item[1]);
        };
        $open = $visible->contains($isActive);
        $collapseId = 'finance-submenu-'.$key;
    @endphp
    @if($visible->isNotEmpty())
        <a href="#{{ $collapseId }}" data-bs-toggle="collapse" role="button" aria-controls="{{ $collapseId }}" aria-expanded="{{ $open ? 'true' : 'false' }}" class="{{ $open ? 'active' : '' }}">
            <i class="bi {{ $icon }}"></i><span class="flex-grow-1">{{ $label }}</span><i class="bi bi-chevron-down sidebar-arrow"></i>
        </a>
        <div id="{{ $collapseId }}" class="collapse {{ $open ? 'show' : '' }} ps-3">
            @foreach($visible as $item)
                <a href="{{ route($item[1],$item[2]) }}" class="{{ $isActive($item) ? 'active' : '' }}" @if($isActive($item)) aria-current="page" @endif><i class="bi bi-circle" style="font-size:.65rem"></i><span>{{ $item[0] }}</span></a>
            @endforeach
        </div>
    @endif
@endforeach
@endcan
