@extends('layouts.admin')
@section('title', 'Admission Dashboard')
@section('content')
@php
    $statCollection = collect($stats ?? []);
    $findStat = function ($needle) use ($statCollection) {
        $item = $statCollection->first(fn($s) => str_contains(strtolower((string)($s['title'] ?? '')), strtolower($needle)));
        return $item['value'] ?? null;
    };
    $kpis = [
        ['label'=>'Candidates','value'=>$findStat('candidate'),'icon'=>'bi-people','tone'=>'blue'],
        ['label'=>'Enquiries','value'=>$findStat('enquir'),'icon'=>'bi-chat-dots','tone'=>'purple'],
        ['label'=>'Registrations','value'=>$findStat('registration'),'icon'=>'bi-person-check','tone'=>'green'],
        ['label'=>'Form Sales','value'=>$findStat('form sale'),'icon'=>'bi-receipt','tone'=>'orange'],
    ];
    $linksCollection = collect($links ?? []);
    $candidateCount = $kpis[0]['value'];
    $enquiryCount = $kpis[1]['value'];
    $registrationCount = $kpis[2]['value'];
    $conversionRate = is_numeric($enquiryCount) && $enquiryCount > 0 && is_numeric($registrationCount)
        ? round(($registrationCount / $enquiryCount) * 100, 1) : null;
@endphp
<style>
.adm-modern{--adm-ink:#15233d;--adm-muted:#74819a;--adm-line:#e7ecf4;--adm-blue:#2563eb;color:var(--adm-ink);padding:8px 0 30px}
.adm-modern .adm-top{display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap;margin-bottom:24px}
.adm-modern .adm-eyebrow{color:#64748b;font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;margin-bottom:5px}
.adm-modern h1{font-size:29px;letter-spacing:-.035em;font-weight:750;margin:0}
.adm-modern .adm-sub{color:var(--adm-muted);margin:7px 0 0;font-size:14px}
.adm-modern .adm-pill{background:#eaf1ff;color:#245bc4;border:1px solid #d9e5ff;border-radius:30px;padding:9px 14px;font-size:12px;font-weight:700}
.adm-modern .adm-kpi,.adm-modern .adm-panel{background:#fff;border:1px solid var(--adm-line);border-radius:16px;box-shadow:0 6px 24px rgba(24,40,72,.035);height:100%}
.adm-modern .adm-kpi{padding:21px 22px;position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s}
.adm-modern .adm-kpi:hover{transform:translateY(-3px);box-shadow:0 12px 30px rgba(24,40,72,.08)}
.adm-modern .adm-kpi-head{display:flex;align-items:center;justify-content:space-between;gap:8px}
.adm-modern .adm-kpi-label{font-size:13px;font-weight:650;color:#64748b}
.adm-modern .adm-icon{width:45px;height:45px;display:flex;align-items:center;justify-content:center;border-radius:13px;font-size:20px}
.adm-modern .adm-icon.blue{background:#eaf1ff;color:#2563eb}.adm-modern .adm-icon.purple{background:#f2edff;color:#8053d7}.adm-modern .adm-icon.green{background:#e7f8f0;color:#17996d}.adm-modern .adm-icon.orange{background:#fff2e7;color:#e88735}
.adm-modern .adm-number{font-size:35px;font-weight:750;letter-spacing:-.045em;line-height:1.2;margin-top:14px}
.adm-modern .adm-footnote{font-size:12px;color:#94a3b8;margin-top:7px}
.adm-modern .adm-panel{padding:23px}
.adm-modern .adm-panel-title{font-size:17px;font-weight:750;letter-spacing:-.015em;margin:0}
.adm-modern .adm-panel-desc{font-size:12px;color:#94a3b8;margin-top:5px}
.adm-modern .adm-section-head{display:flex;align-items:start;justify-content:space-between;gap:12px;margin-bottom:22px}
.adm-modern .adm-funnel-row{margin-bottom:20px}
.adm-modern .adm-funnel-line{display:flex;justify-content:space-between;align-items:center;font-size:13px;font-weight:650;margin-bottom:8px}
.adm-modern .adm-funnel-line span:last-child{font-weight:750}
.adm-modern .adm-track{height:10px;border-radius:20px;background:#eef2f8;overflow:hidden}
.adm-modern .adm-fill{height:100%;border-radius:20px;background:#2563eb}
.adm-modern .adm-empty{display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;min-height:220px;color:#8795aa;gap:10px}
.adm-modern .adm-empty i{font-size:34px;color:#c6d3e5}
.adm-modern .adm-empty strong{color:#5b6b85;font-size:14px}
.adm-modern .adm-quick{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:11px}
.adm-modern .adm-quick a{display:flex;align-items:center;gap:10px;padding:13px 14px;border:1px solid #e6ecf5;border-radius:11px;color:#304463;text-decoration:none;font-size:13px;font-weight:650;transition:background .2s}
.adm-modern .adm-quick a:hover{background:#f3f7ff;color:#1e5bcb}
.adm-modern .adm-quick i{font-size:17px;color:#487cda}
.adm-modern .adm-insight{background:linear-gradient(115deg,#183a77,#2563eb);color:#fff;border-radius:15px;padding:22px;height:100%}
.adm-modern .adm-insight .value{font-size:35px;font-weight:750;margin-top:14px}
.adm-modern .adm-insight .hint{font-size:12px;opacity:.8;margin-top:8px}
@media(max-width:575px){.adm-modern h1{font-size:24px}.adm-modern .adm-quick{grid-template-columns:1fr}.adm-modern .adm-panel{padding:18px}}
</style>
<div class="container-fluid adm-modern">
    <div class="adm-top">
        <div>
            <div class="adm-eyebrow">Admissions / Overview</div>
            <h1>Admission Dashboard</h1>
            <p class="adm-sub">Track enquiries, applications and admissions in one place.</p>
        </div>
        <div class="adm-pill"><i class="bi bi-activity me-1"></i> School overview</div>
    </div>
    <div class="row g-3 mb-4">
        @foreach($kpis as $kpi)
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="adm-kpi">
                <div class="adm-kpi-head">
                    <span class="adm-kpi-label">{{ $kpi['label'] }}</span>
                    <span class="adm-icon {{ $kpi['tone'] }}"><i class="bi {{ $kpi['icon'] }}"></i></span>
                </div>
                <div class="adm-number">{{ is_numeric($kpi['value']) ? number_format($kpi['value']) : '—' }}</div>
                <div class="adm-footnote">{{ $kpi['value'] === null ? 'Data not available' : 'School-wide total' }}</div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-7">
            <div class="adm-panel">
                <div class="adm-section-head">
                    <div><h2 class="adm-panel-title">Admission pipeline</h2><div class="adm-panel-desc">Current totals across the admission journey</div></div>
                    <span class="badge bg-light text-secondary border">Live totals</span>
                </div>
                @php
                    $funnel = [
                        ['label'=>'Enquiries','value'=>$enquiryCount,'color'=>'#2563eb'],
                        ['label'=>'Candidates','value'=>$candidateCount,'color'=>'#8b5cf6'],
                        ['label'=>'Registrations','value'=>$registrationCount,'color'=>'#14b8a6'],
                    ];
                    $funnelMax = max(1, ...array_map(fn($x) => is_numeric($x['value']) ? (int)$x['value'] : 0, $funnel));
                @endphp
                @foreach($funnel as $step)
                <div class="adm-funnel-row">
                    <div class="adm-funnel-line"><span>{{ $step['label'] }}</span><span>{{ is_numeric($step['value']) ? number_format($step['value']) : '—' }}</span></div>
                    <div class="adm-track"><div class="adm-fill" style="width:{{ is_numeric($step['value']) ? round(100 * $step['value'] / $funnelMax, 1) : 0 }}%;background:{{ $step['color'] }}"></div></div>
                </div>
                @endforeach
                <div class="small text-muted mt-3">Pipeline stages are shown as independent totals, not a verified cohort conversion.</div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="adm-insight">
                <div class="d-flex justify-content-between align-items-center"><strong>Registration-to-enquiry ratio</strong><i class="bi bi-graph-up-arrow fs-4"></i></div>
                <div class="value">{{ $conversionRate === null ? '—' : number_format($conversionRate, 1).'%' }}</div>
                <div class="hint">{{ $conversionRate === null ? 'Requires enquiry and registration totals.' : 'Registrations divided by enquiries. This is not a cohort conversion rate.' }}</div>
                <hr style="opacity:.2;margin:22px 0">
                <div class="d-flex justify-content-between align-items-center"><span>Form sales</span><strong>{{ is_numeric($kpis[3]['value']) ? number_format($kpis[3]['value']) : '—' }}</strong></div>
            </div>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-12 col-xl-5">
            <div class="adm-panel">
                <div class="adm-section-head"><div><h2 class="adm-panel-title">Quick actions</h2><div class="adm-panel-desc">Jump directly into admission workflows</div></div></div>
                <div class="adm-quick">
                    @forelse($linksCollection as $link)
                        @if(!empty($link['route']) && \Illuminate\Support\Facades\Route::has($link['route']))
                            <a href="{{ route($link['route']) }}"><i class="bi bi-arrow-up-right-square"></i><span>{{ $link['label'] ?? 'Open module' }}</span></a>
                        @endif
                    @empty
                        <span class="text-muted small">No quick actions configured.</span>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-7">
            <div class="adm-panel">
                <div class="adm-section-head"><div><h2 class="adm-panel-title">Monthly admission trend</h2><div class="adm-panel-desc">Connect month-wise admissions data to display a trend chart</div></div><i class="bi bi-bar-chart text-muted"></i></div>
                @if(!empty($monthlyAdmissionTrend) && count($monthlyAdmissionTrend))
                    <div style="height:235px"><canvas id="admissionTrendChart"></canvas></div>
                @else
                    <div class="adm-empty"><i class="bi bi-bar-chart-line"></i><strong>Monthly data not connected yet</strong><span class="small">No sample numbers are shown.</span></div>
                @endif
            </div>
        </div>
    </div>
</div>
@if(!empty($monthlyAdmissionTrend) && count($monthlyAdmissionTrend))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded',function(){
    const records = @json($monthlyAdmissionTrend);
    new Chart(document.getElementById('admissionTrendChart'),{
        type:'line',data:{labels:records.map(r=>r.month),datasets:[{label:'Admissions',data:records.map(r=>r.total),borderColor:'#2563eb',backgroundColor:'rgba(37,99,235,.08)',fill:true,tension:.35}]},
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}},x:{grid:{display:false}}}}
    });
});
</script>
@endif
@endsection
