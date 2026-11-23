@php
    $employeeLinks = [
        ['Dashboard', 'employee-information/dashboard', 'speedometer2'],
        ['Employee List', 'employee-information/employees', 'people'],
        ['Relieved Employees', 'employee-information/relieved', 'person-dash'],
        ['Employee Suspension', 'employee-information/suspensions', 'person-x'],
        ['Resignation', 'employee-information/resignations', 'box-arrow-right'],
        ['Employee Holidays', 'employee-information/holidays', 'calendar-event'],
        ['Extra Duties', 'employee-information/extra-duties', 'clipboard-check'],
        ['Employee Remarks', 'employee-information/remarks', 'chat-left-text'],
        ['Organization Mapping', 'employee-information/organization-mapping', 'diagram-3'],
        ['Certificates', 'employee-information/certificates', 'award'],
        ['Reports', 'employee-information/reports', 'bar-chart'],
    ];
@endphp
<div class="menu-title">Employee Information</div>
@foreach($employeeLinks as $item)
    @php
        $url = url($item[1]);
        $active = request()->is($item[1]) || request()->is($item[1] . '/*');
    @endphp
    <a href="{{ $url }}" class="{{ $active ? 'active' : '' }}">
        <i class="bi bi-{{ $item[2] }}"></i> {{ $item[0] }}
    </a>
@endforeach
