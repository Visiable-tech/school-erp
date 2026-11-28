<div class="menu-title">Transport Management</div>
<a href="{{ route('transport.dashboard') }}" class="{{ request()->routeIs('transport.dashboard')?'active':'' }}"><i class="bi bi-speedometer2"></i> Transport Dashboard</a>
<a href="{{ route('transport.index') }}#transport-routes"><i class="bi bi-signpost"></i> Route Management</a>
<a href="{{ route('transport.index') }}#transport-stops"><i class="bi bi-geo-alt"></i> Pickup &amp; Drop Points</a>
@foreach(['vehicles'=>'Vehicle Management','staff'=>'Drivers & Attendants'] as $key=>$label)<a href="{{ route('transport.register',$key) }}" class="{{ request()->route('type')===$key?'active':'' }}"><i class="bi bi-bus-front"></i> {{ $label }}</a>@endforeach
<a href="{{ route('transport.index') }}#transport-assignments"><i class="bi bi-people"></i> Student Transport Assignment</a>
<a href="{{ route('transport.index') }}"><i class="bi bi-wallet"></i> Transport Fee Management</a>
@foreach(['trips'=>'Daily Trip Register','attendance'=>'Transport Attendance','maintenance'=>'Vehicle Maintenance','fuel'=>'Fuel & Expense Register','documents'=>'Vehicle Documents','settings'=>'Transport Settings'] as $key=>$label)<a href="{{ route('transport.register',$key) }}" class="{{ request()->route('type')===$key?'active':'' }}"><i class="bi bi-list-check"></i> {{ $label }}</a>@endforeach
<a href="{{ route('transport.dashboard') }}"><i class="bi bi-bar-chart"></i> Transport Reports</a>
