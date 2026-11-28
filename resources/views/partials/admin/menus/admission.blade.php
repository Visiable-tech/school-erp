@php
$admissionLinks = [
 ['Admission Dashboard','admission.dashboard','admission-application.view','speedometer2',[]],
 ['Enquiries (Leads)','admission-enquiries.index','admission-enquiry.view','people',[]],
 ['Application Forms','admission-applications.index','admission-application.view','file-earmark-text',[]],
 ['Admissions','admission-applications.index','admission-application.view','person-check',['application_status'=>'admitted']],
 ['Waiting List','admission-applications.index','admission-application.view','hourglass-split',['application_status'=>'waiting_list']],
 ['Student Onboarding','admission-applications.index','admission-application.view','person-plus',['application_status'=>'approved']],
 ['Admission Reports','admission.dashboard','admission-application.view','bar-chart',['view'=>'reports']],
];
@endphp
<div class="sidebar-submenu">
@foreach($admissionLinks as $link)
 @if(auth()->user()->can($link[2]) && ($link[1]!=='admission.dashboard' || auth()->user()->can('admission-enquiry.view')))
 @php $active=request()->routeIs($link[1]) && request('application_status')===($link[4]['application_status'] ?? null) && request('view')===($link[4]['view'] ?? null); @endphp
 <a href="{{ route($link[1],$link[4]) }}" class="{{ $active?'active':'' }}"><i class="bi bi-{{ $link[3] }} me-2"></i>{{ $link[0] }}</a>
 @endif
@endforeach
<a data-bs-toggle="collapse" href="#admissionTools" role="button"><i class="bi bi-gear me-2"></i>Admission Settings & Tools</a>
<div class="collapse {{ request()->routeIs('admission-boards.*','admission-setups.*','admission-sessions.*','admission-number-schemes.*','admission-candidates.*','admission-form-sales.*','admission-followups.*','admission-information-sources.*','admission-communication-modes.*')?'show':'' }}" id="admissionTools">
@php $tools=[['Candidates','admission-candidates.index','admission-candidate.view'],['Form Sales','admission-form-sales.index','admission-form-sale.view'],['Follow-ups','admission-followups.index','admission-followup.view'],['Sessions','admission-sessions.index','admission-session.view'],['Admission Setup','admission-setups.index','admission-setup.view'],['Number Schemes','admission-number-schemes.index','admission-number-scheme.view'],['Boards','admission-boards.index','admission-board.view'],['Sources','admission-information-sources.index','admission-information-source.view'],['Communication Modes','admission-communication-modes.index','admission-communication-mode.view']]; @endphp
@foreach($tools as $tool)
 @can($tool[2])
 <a href="{{ route($tool[1]) }}">{{ $tool[0] }}</a>
 @endcan
@endforeach
</div></div>
