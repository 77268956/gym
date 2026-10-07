@extends('layouts.app')

@section('title', 'Reportes')

@push('styles')
<style>
    .reports-page { min-width:0; padding: 1rem 1.5rem 1.5rem; color:var(--text-main); }
    .reports-page .text-dark { color:var(--text-main)!important; }
    .reports-page .text-muted { color:var(--text-muted)!important; }
    .reports-page .page-header > div { min-width:0; }
    .reports-toolbar { display:flex; flex-wrap:wrap; align-items:flex-end; justify-content:space-between; gap:.85rem; padding:1rem 1.15rem; }
    .reports-filter { display:flex; flex-wrap:wrap; align-items:flex-end; gap:.65rem; }
    .reports-filter label { display:block; margin-bottom:.2rem; color:var(--text-muted); font-size:.68rem; font-weight:700; }
    .reports-filter .form-control { min-width:150px; border-radius:7px; font-size:.82rem; }
    .reports-page .ic-card { min-width:0; max-width:100%; border:0; border-radius:12px; box-shadow:0 4px 6px rgba(0,0,0,.04); overflow:hidden; }
    .reports-page .ic-card-header { padding:1rem 1.25rem; border-bottom:1px solid #f0f2f5; background:#fff; color:var(--text-main); font-weight:700; overflow-wrap:anywhere; }
    .reports-page .report-accordion-toggle { display:flex; width:100%; min-width:0; align-items:center; justify-content:space-between; gap:.75rem; padding:1rem 1.25rem; background:#fff; color:var(--text-main); text-align:left; font-weight:700; cursor:pointer; list-style:none; }
    .reports-page .report-accordion-toggle > span:first-child { min-width:0; overflow-wrap:anywhere; }
    .reports-page .report-accordion-toggle::-webkit-details-marker { display:none; }
    .reports-page .report-accordion-toggle:hover { background:color-mix(in srgb, var(--primary) 5%, #fff); }
    .reports-page .report-accordion-toggle:focus { outline:2px solid rgba(78,115,223,.3); outline-offset:-2px; }
    .reports-page .report-accordion-icon { display:inline-flex; width:26px; height:26px; align-items:center; justify-content:center; border-radius:7px; background:#eef2ff; color:var(--primary); }
    .reports-page .report-icon-minus { display:none; }
    .reports-page .report-accordion[open] .report-icon-plus { display:none; }
    .reports-page .report-accordion[open] .report-icon-minus { display:inline-block; }
    .reports-page .report-accordion-content { min-width:0; border-top:1px solid #f0f2f5; }
    .reports-page .report-stat { display:flex; align-items:center; justify-content:space-between; min-height:92px; padding:1rem 1.1rem; border-radius:10px; color:#fff; background:var(--sidebar-bg); box-shadow:0 4px 10px rgba(0,0,0,.08); }
    .reports-page .report-stat-label { font-size:.7rem; font-weight:700; opacity:.82; text-transform:uppercase; }
    .reports-page .report-stat-value { margin:0; font-size:1.45rem; font-weight:800; line-height:1.2; }
    .reports-page .report-stat-icon { font-size:1.6rem; opacity:.42; }
    .report-chart-panel { height:280px; padding:1rem; }
    .report-chart-title { margin-bottom:.75rem; color:var(--text-muted); font-size:.72rem; font-weight:700; text-transform:uppercase; overflow-wrap:anywhere; }
    .reports-page .ic-table { width:100%; margin:0; table-layout:fixed; }
    .reports-page .ic-table th { padding:.7rem .65rem; background:color-mix(in srgb, var(--primary) 7%, #fff); color:var(--primary); font-size:.7rem; text-transform:uppercase; overflow-wrap:anywhere; }
    .reports-page .ic-table td { padding:.65rem; color:var(--text-main); font-size:.82rem; vertical-align:middle; overflow-wrap:anywhere; word-break:normal; }
    .reports-page .report-empty { padding:1.4rem; color:var(--text-muted); text-align:center; font-size:.85rem; }
    .reports-page .row > [class*="col-"] { min-width:0; }
    @media(max-width:767.98px) { .reports-page{padding:.75rem}.reports-toolbar{align-items:stretch}.reports-filter{width:100%}.reports-filter>div{flex:1 1 130px}.reports-filter .form-control{width:100%;min-width:0} }
</style>
@endpush

@section('content')
<div class="container-fluid reports-page">
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h5 font-weight-bold text-dark mb-1"><i class="fas fa-chart-pie text-primary mr-2"></i>Centro de Reportes</h1>
            <p class="small text-muted mb-0">Consulta el rendimiento financiero, la actividad y las incidencias del gimnasio.</p>
        </div>
        <span class="small text-muted mt-2 mt-md-0"><i class="far fa-calendar-alt mr-1 text-primary"></i>{{ $rangoFechas ?? '' }}</span>
    </div>

    <div class="ic-card reports-toolbar mb-3">
        <form method="GET" action="{{ route('reportes.index') }}" class="reports-filter">
            <div>
                <label for="desde">DESDE</label>
                <input id="desde" name="desde" type="date" class="form-control form-control-sm" value="{{ $desde }}">
            </div>
            <div>
                <label for="hasta">HASTA</label>
                <input id="hasta" name="hasta" type="date" class="form-control form-control-sm" value="{{ $hasta }}">
            </div>
            <button type="submit" class="btn btn-primary btn-sm font-weight-bold"><i class="fas fa-filter mr-1"></i>Filtrar</button>
            <a href="{{ route('reportes.index') }}" class="btn btn-light btn-sm border"><i class="fas fa-undo mr-1"></i>Mes actual</a>
        </form>
        <a href="{{ route('reportes.pdf', request()->only('desde', 'hasta')) }}" target="_blank" rel="noopener" class="btn btn-danger btn-sm font-weight-bold">
            <i class="fas fa-file-pdf mr-1"></i>Exportar a PDF
        </a>
    </div>

    @if($errors->has('desde') || $errors->has('hasta'))
        <div class="alert alert-danger py-2 small">{{ $errors->first('desde') ?: $errors->first('hasta') }}</div>
    @endif

    @include('reportes.partials.content')
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@include('reportes.partials.charts-script')
@endpush
