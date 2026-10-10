<div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h5 font-weight-bold text-dark mb-1"><i class="{{ $icono }} text-primary mr-2"></i>{{ $titulo }}</h1>
        <p class="small text-muted mb-0">{{ $descripcion }}</p>
    </div>
    <span class="small text-muted mt-2 mt-md-0"><i class="far fa-calendar-alt mr-1 text-primary"></i>{{ $rangoFechas ?? '' }}</span>
</div>

<div class="ic-card reports-toolbar mb-3">
    <form method="GET" action="{{ route($filtroRoute) }}" class="reports-filter">
        <div>
            <label for="desde">DESDE</label>
            <input id="desde" name="desde" type="date" class="form-control form-control-sm" value="{{ $desde }}">
        </div>
        <div>
            <label for="hasta">HASTA</label>
            <input id="hasta" name="hasta" type="date" class="form-control form-control-sm" value="{{ $hasta }}">
        </div>
        <button type="submit" class="btn btn-primary btn-sm font-weight-bold"><i class="fas fa-filter mr-1"></i>Filtrar</button>
        <a href="{{ route($filtroRoute) }}" class="btn btn-light btn-sm border"><i class="fas fa-undo mr-1"></i>Mes actual</a>
    </form>
    <a href="{{ route('reportes.pdf', [...request()->only('desde', 'hasta'), 'tipo' => $pdfTipo]) }}" target="_blank" rel="noopener" class="btn btn-danger btn-sm font-weight-bold">
        <i class="fas fa-file-pdf mr-1"></i>Exportar a PDF
    </a>
</div>

@if($errors->has('desde') || $errors->has('hasta'))
    <div class="alert alert-danger py-2 small">{{ $errors->first('desde') ?: $errors->first('hasta') }}</div>
@endif