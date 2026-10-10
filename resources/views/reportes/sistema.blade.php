@extends('layouts.app')

@section('title', 'Reporte del Sistema')

@push('styles')
@include('reportes.partials.styles')
@endpush

@section('content')
<div class="container-fluid reports-page">
    <div class="report-detail-toolbar d-flex align-items-center mb-2 gap-2">
        <a href="{{ route('reportes.index', request()->only('desde', 'hasta')) }}" class="btn btn-light btn-sm border"><i class="fas fa-arrow-left mr-1"></i>Volver a Reportes</a>
        @include('reportes.partials.toolbar-filtros', [
            'filtroRoute' => 'reportes.sistema',
            'pdfTipo' => 'sistema',
            'soloFiltros' => true,
        ])
    </div>

    @include('reportes.partials.sistema')
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@include('reportes.partials.charts-script')
@endpush
