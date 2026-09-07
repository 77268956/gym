@extends('layouts.app')

@section('title', 'Dashboard Principal')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">¡Bienvenido, {{ Auth::user()->nombre }}!</h5>
                <p class="card-text">Has iniciado sesión con el rol de <strong>{{ ucfirst(Auth::user()->rol) }}</strong>.</p>
                <p class="text-muted">Selecciona un módulo del menú lateral para comenzar a operar.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Placeholders para los futuros gráficos de Chart.js -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                Flujo de Asistencias (Próximamente)
            </div>
            <div class="card-body d-flex align-items-center justify-content-center bg-light">
                <span class="text-muted">Área de gráfico (Chart.js)</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                Ingresos Generados (Próximamente)
            </div>
            <div class="card-body d-flex align-items-center justify-content-center bg-light">
                <span class="text-muted">Área de gráfico (Chart.js)</span>
            </div>
        </div>
    </div>
</div>
@endsection