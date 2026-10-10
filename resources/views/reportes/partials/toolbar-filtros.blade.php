@if(empty($soloFiltros))
<div class="reports-toolbar d-flex flex-column flex-md-row align-items-stretch align-items-md-end justify-content-between mb-2">
    <form method="GET" action="{{ route($filtroRoute) }}" class="reports-filter d-flex flex-column flex-sm-row align-items-stretch align-items-sm-end">
        <div class="form-group mb-2 mb-sm-0 mr-sm-2">
            <label for="desde">DESDE</label>
            <input id="desde" name="desde" type="date" class="form-control form-control-sm" value="{{ $desde }}">
        </div>
        <div class="form-group mb-2 mb-sm-0 mr-sm-2">
            <label for="hasta">HASTA</label>
            <input id="hasta" name="hasta" type="date" class="form-control form-control-sm" value="{{ $hasta }}">
        </div>
        <button type="submit" class="btn btn-primary btn-sm font-weight-bold mb-2 mb-sm-0 mr-sm-2"><i class="fas fa-filter mr-1"></i>Filtrar</button>
        <a href="{{ route($filtroRoute) }}" class="btn btn-light btn-sm border mb-2 mb-sm-0"><i class="fas fa-undo mr-1"></i>Mes actual</a>
    </form>
    <a href="{{ route('reportes.pdf', [...request()->only('desde', 'hasta'), 'tipo' => $pdfTipo]) }}" target="_blank" rel="noopener" class="btn btn-danger btn-sm font-weight-bold ml-md-auto mt-2 mt-md-0">
        <i class="fas fa-file-pdf mr-1"></i>Exportar a PDF
    </a>
</div>
@else
<div class="reports-toolbar d-flex flex-column flex-lg-row align-items-stretch align-items-lg-end justify-content-between flex-grow-1">
    <form method="GET" action="{{ route($filtroRoute) }}" class="reports-filter d-flex flex-column flex-sm-row align-items-stretch align-items-sm-end">
        <div class="form-group mb-2 mb-sm-0 mr-sm-2">
            <label for="desde">DESDE</label>
            <input id="desde" name="desde" type="date" class="form-control form-control-sm" value="{{ $desde }}">
        </div>
        <div class="form-group mb-2 mb-sm-0 mr-sm-2">
            <label for="hasta">HASTA</label>
            <input id="hasta" name="hasta" type="date" class="form-control form-control-sm" value="{{ $hasta }}">
        </div>
        <button type="submit" class="btn btn-primary btn-sm font-weight-bold mb-2 mb-sm-0 mr-sm-2"><i class="fas fa-filter mr-1"></i>Filtrar</button>
        <a href="{{ route($filtroRoute) }}" class="btn btn-light btn-sm border mb-2 mb-sm-0"><i class="fas fa-undo mr-1"></i>Mes actual</a>
    </form>
    <a href="{{ route('reportes.pdf', [...request()->only('desde', 'hasta'), 'tipo' => $pdfTipo]) }}" target="_blank" rel="noopener" class="btn btn-danger btn-sm font-weight-bold ml-lg-auto mt-2 mt-lg-0">
        <i class="fas fa-file-pdf mr-1"></i>Exportar a PDF
    </a>
</div>
@endif

@if($errors->has('desde') || $errors->has('hasta'))
    <div class="alert alert-danger py-2 small mb-2">{{ $errors->first('desde') ?: $errors->first('hasta') }}</div>
@endif
