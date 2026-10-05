@extends('layouts.app')

@section('title', 'Control de Membresías')

@section('skeleton')
    <div class="skel-box" style="height: 32px; width: 220px; margin-bottom: 1.5rem;"></div>
    <div class="skel-box" style="height: 42px; width: 100%; margin-bottom: 0.5rem; border-radius: 8px 8px 0 0;"></div>
    <div class="skel-box" style="flex: 1; width: 100%; border-radius: 0 0 8px 8px;"></div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    body, html { overflow: hidden; height: 100%; }

    #page-wrapper main {
        padding: 1rem 1.5rem !important;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 60px);
        overflow: hidden;
    }

    .page-header { flex-shrink: 0; margin-bottom: 0.75rem !important; }
    .main-container { flex: 1; overflow: hidden; display: flex; flex-direction: column; padding: 0 !important; }

    :root {
        --ic-accent: var(--primary);
        --card-color: var(--sidebar-bg);
        --ic-green: var(--primary);
        --ic-red: var(--sidebar-hover);
        --ic-yellow: var(--primary-hover);
        --duration-purple: #8B5CF6;
        --duration-blue: #2563EB;
        --duration-good: #10B981;
        --duration-warning: #F59E0B;
        --duration-critical: #EF4444;
        --ic-muted: #64748B;
    }

    /* KPI Cards */
    .kpi-card {
        border-radius: 10px; border: none; padding: 0.6rem 1rem;
        color: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        display: flex; justify-content: space-between; align-items: center;
        background: var(--card-color); height: 100%;
    }
    .kpi-icon { font-size: 1.8rem; opacity: 0.4; }
    .kpi-value { font-size: 1.4rem; font-weight: 800; margin: 0; line-height: 1; }
    .kpi-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; opacity: 0.8; margin-top: 2px; }

    .ic-card {
        background: #fff; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.04);
        padding: 0.85rem; display: flex; flex-direction: column; margin-bottom: 0 !important;
    }
    .ic-card-title { font-weight: 700; font-size: 0.8rem; color: var(--sidebar-bg); margin-bottom: 0.5rem; text-transform: uppercase; }

    /* Table panel */
    .table-panel { flex: 1; min-height: 0; overflow-y: auto; }
    .dataTables_wrapper { display: flex; flex-direction: column; height: 100%; }
    .dataTables_wrapper > .row:first-child,
    .dataTables_wrapper > .row:last-child { flex-shrink: 0; }
    .dataTables_wrapper > .row:nth-child(2) { flex: 1; min-height: 0; overflow-y: auto; }
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid var(--card-color); border-radius: 6px;
        color: var(--card-color); font-size: 0.8rem;
    }
    .dataTables_wrapper .dataTables_paginate .page-link {
        color: var(--card-color); border-color: #E2E8F0; font-size: 0.8rem;
    }
    .dataTables_wrapper .dataTables_paginate .page-item.active .page-link,
    .dataTables_wrapper .dataTables_paginate .page-link:hover {
        background-color: var(--card-color); border-color: var(--card-color); color: #fff;
    }
    .dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {
        color: #94A3B8; background-color: #F8FAFC;
    }

    .ic-table thead th { font-size: 0.75rem; font-weight: 700; color: #4e73df; background: #eaecf4; border-bottom: 2px solid #4e73df; padding: 0.75rem 0.5rem; letter-spacing: 0.5px; text-transform: uppercase; position: sticky; top: 0; z-index: 10; }
    .ic-table td { font-size: 0.85rem; vertical-align: middle; white-space: nowrap; border-top: 1px solid #e3e6f0; padding: 0.6rem 0.5rem; color: #5a5c69; }
    .ic-avatar { width: 30px; height: 30px; background: var(--card-color); color: white; font-weight: bold; font-size:0.7rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

    /* Badges */
    .ic-badge-active { background: var(--ic-green); color: white; padding: 3px 8px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; }
    .ic-badge-warn { background: var(--ic-yellow); color: white; padding: 3px 8px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; }
    .ic-badge-expired { background: var(--ic-red); color: white; padding: 3px 8px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; }
    .ic-badge-plan { background: var(--card-color); color: white; padding: 3px 10px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; letter-spacing: 0.05em; }

    .progress-bar.bg-purple { background-color: var(--duration-purple) !important; }
    .progress-bar.bg-info { background-color: var(--duration-blue) !important; }
    .progress-bar.bg-success { background-color: var(--duration-good) !important; }
    .progress-bar.bg-warning { background-color: var(--duration-warning) !important; }
    .progress-bar.bg-danger { background-color: var(--duration-critical) !important; }
    
    .progress-sm { height: 6px; border-radius: 3px; background-color: #E2E8F0; overflow: hidden; margin-top: 4px; }
    .progress-sm .progress-bar { transition: width 0.4s ease; }

    .row.tight { margin-bottom: 0.75rem; }

    /* Filters panel */
    .filters-panel {
        background: #fff; border-radius: 10px; padding: 0.75rem 1rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.04); margin-bottom: 0.75rem; flex-shrink: 0;
    }
    .filters-panel .form-control, .filters-panel .custom-select {
        font-size: 0.8rem; height: 32px; border-radius: 6px;
    }

    /* Card View */
    .card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 0.75rem; overflow-y: auto; padding: 2px 5px 2px 2px; }
    .member-card {
        background: #fff; border-radius: 10px; padding: 0.85rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.04); border-left: 4px solid var(--ic-green);
        display: flex; flex-direction: column; min-height: 235px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .member-card:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
    .member-card.status-warn { border-left-color: var(--ic-yellow); }
    .member-card.status-expired { border-left-color: var(--ic-red); }
    .member-card-header { display: flex; align-items: center; gap: 0.75rem; min-height: 48px; margin-bottom: 0.75rem; }
    .member-card-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
    .member-card-avatar-placeholder {
        width: 48px; height: 48px; border-radius: 50%; background: var(--card-color);
        color: white; font-weight: bold; display: flex; align-items: center;
        justify-content: center; font-size: 1rem; flex-shrink: 0;
    }
    .member-card-info { flex: 1; min-width: 0; }
    .member-card-name { font-weight: 700; font-size: 0.95rem; color: var(--sidebar-bg); margin-bottom: 0; }
    .member-card-detail { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.75rem; color: var(--ic-muted); }
    .member-card-header > .ic-badge-active,
    .member-card-header > .ic-badge-warn,
    .member-card-header > .ic-badge-expired { flex-shrink: 0; }
    .member-card-body { display: flex; flex: 1; flex-direction: column; gap: 0.4rem; }
    .member-card-row { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; font-size: 0.8rem; }
    .member-card-label { color: var(--ic-muted); }
    .member-card-value { font-weight: 600; text-align: right; }
    .member-card-actions { display: flex; gap: 0.5rem; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #F1F5F9; }
    .member-card-actions .btn { flex: 1; min-width: 0; font-size: 0.75rem; padding: 0.3rem 0.5rem; border-radius: 6px; white-space: nowrap; }
    .member-card-actions .btn[title] { flex: 0 0 34px; padding-left: 0; padding-right: 0; }
    .member-action-icon { color: var(--primary) !important; }
    .member-card-actions .btn-outline-success,
    .member-card-actions .btn-outline-info,
    .member-card-actions .btn-outline-primary { color: var(--primary); border-color: var(--primary); }
    .member-card-actions .btn-outline-success:hover,
    .member-card-actions .btn-outline-info:hover,
    .member-card-actions .btn-outline-primary:hover { background: var(--primary); color: #fff; }

    /* Toggle View Buttons */
    .view-toggle .btn {
        min-height: 29px; padding: 0.2rem 0.4rem; font-size: 0.72rem;
        line-height: 1.25; font-weight: 600;
    }
    .ic-color-btn { color: var(--card-color) !important; border-color: var(--card-color) !important; }
    .ic-color-btn:hover, .ic-color-btn.active { background-color: var(--card-color) !important; color: #fff !important; border-color: var(--card-color) !important; }
</style>
@endpush

@section('content')
<div class="container-fluid main-container">

    {{-- KPI Cards --}}
    <div class="row tight flex-shrink-0">
        <div class="col">
            <div class="kpi-card">
                <div><h3 class="kpi-value">{{ $totalMembresias }}</h3><div class="kpi-label">Todas</div></div>
                <i class="fas fa-id-card kpi-icon"></i>
            </div>
        </div>
        <div class="col">
            <div class="kpi-card">
                <div><h3 class="kpi-value">{{ $recienCompradas }}</h3><div class="kpi-label">Recién Compradas</div></div>
                <i class="fas fa-star kpi-icon"></i>
            </div>
        </div>
        <div class="col">
            <div class="kpi-card">
                <div><h3 class="kpi-value">{{ $activas }}</h3><div class="kpi-label">Activas</div></div>
                <i class="fas fa-check-circle kpi-icon"></i>
            </div>
        </div>
        <div class="col">
            <div class="kpi-card">
                <div><h3 class="kpi-value">{{ $porVencer }}</h3><div class="kpi-label">Por Vencer</div></div>
                <i class="fas fa-exclamation-triangle kpi-icon"></i>
            </div>
        </div>
        <div class="col">
            <div class="kpi-card">
                <div><h3 class="kpi-value">{{ $vencidas }}</h3><div class="kpi-label">Inactivas / Vencidas</div></div>
                <i class="fas fa-times-circle kpi-icon"></i>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filters-panel">
        <div id="filtersForm">
            <input type="hidden" name="vista" value="{{ $vista }}">
            <div class="row align-items-end">
                <div class="col-md-2">
                    <label class="small font-weight-bold mb-1">Estado</label>
                    <select name="estado" class="custom-select js-filter-input">
                        <option value="todas" {{ $estado === 'todas' ? 'selected' : '' }}>Todas</option>
                        <option value="recien_compradas" {{ $estado === 'recien_compradas' ? 'selected' : '' }}>Recién Compradas</option>
                        <option value="activas" {{ $estado === 'activas' ? 'selected' : '' }}>Activas</option>
                        <option value="por_vencer" {{ $estado === 'por_vencer' ? 'selected' : '' }}>Por Vencer</option>
                        <option value="vencidas" {{ $estado === 'vencidas' ? 'selected' : '' }}>Inactivas / Vencidas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold mb-1">Plan</label>
                    <select name="tipo_membresia" class="custom-select js-filter-input">
                        <option value="">Todos</option>
                        @foreach($tiposMembresia as $tipo)
                            <option value="{{ $tipo->id }}" {{ request('tipo_membresia') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold mb-1">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control js-filter-input" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold mb-1">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control js-filter-input" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold mb-1">Buscar</label>
                    <input type="text" name="busqueda" class="form-control js-filter-input" placeholder="Nombre, DUI..." value="{{ request('busqueda') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-1 mt-2 mt-md-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary js-clear-filters" style="font-size:0.8rem;" title="Limpiar"><i class="fas fa-redo"></i></button>
                    <div class="view-toggle btn-group btn-group-sm ml-1" role="group">
                        <a href="{{ route('membresias-clientes.index', ['vista' => 'tabla']) }}" class="btn btn-outline-primary ic-color-btn {{ $vista === 'tabla' ? 'active' : '' }}" title="Vista Tabla"><i class="fas fa-list mr-1"></i>Tabla</a>
                        <a href="{{ route('membresias-clientes.index', ['vista' => 'cards']) }}" class="btn btn-outline-primary ic-color-btn {{ $vista === 'cards' ? 'active' : '' }}" title="Vista Tarjetas"><i class="fas fa-th-large mr-1"></i>Tarjetas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-grow-1" style="min-height: 0; overflow: hidden;">

        @if($vista === 'tabla')
        {{-- TABLE VIEW --}}
        <div class="ic-card h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-shrink-0">
                <div class="d-flex align-items-center">
                    <h5 class="ic-card-title mb-0 mr-3"><i class="fas fa-id-card-alt text-primary mr-2"></i> Control de Membresías</h5>
                    <div class="input-group input-group-sm" style="width: 220px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="customSearchMemClientes" class="form-control border-left-0" placeholder="Buscar cliente..." style="background-color: #F8FAFC;">
                    </div>
                </div>
                <span class="badge badge-light text-muted">{{ $membresias->count() }} registros</span>
            </div>

            <div class="table-panel">
                <table id="mainTable" class="table ic-table w-100">
                    <thead>
                        <tr>
                            <th class="text-uppercase"><i class="fas fa-user mr-1 text-primary"></i> Cliente</th>
                            <th class="text-uppercase"><i class="fas fa-phone-alt mr-1 text-primary"></i> Teléfono</th>
                            <th class="text-uppercase"><i class="fas fa-dumbbell mr-1 text-primary"></i> Plan</th>
                            <th class="text-uppercase"><i class="fas fa-calendar mr-1 text-primary"></i> Inicio</th>
                            <th class="text-uppercase"><i class="fas fa-calendar-times mr-1 text-primary"></i> Vencimiento</th>
                            <th class="text-uppercase"><i class="fas fa-hourglass-half mr-1 text-primary"></i> Duración</th>
                            <th class="text-uppercase"><i class="fas fa-info-circle mr-1 text-primary"></i> Estado</th>
                            <th class="text-center text-uppercase"><i class="fas fa-cogs mr-1 text-primary"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($membresias as $membresia)
                        @php
                            $cliente = $membresia->cliente;
                            if (!$cliente) continue;
                            $minutosTotales = max(1, $membresia->fecha_inicio->diffInMinutes($membresia->fecha_vencimiento));
                            $minutosTranscurridos = max(0, min($minutosTotales, $membresia->fecha_inicio->diffInMinutes(now(), false)));
                            
                            $horasRestantes = max(0, (int) ceil(now()->diffInMinutes($membresia->fecha_vencimiento, false) / 60));
                            $diasRestantes = (int) ceil($horasRestantes / 24);
                            
                            $estaActiva = $membresia->estado === 'activa' && $membresia->fecha_inicio <= now() && $membresia->fecha_vencimiento >= now();
                            $estaPorVencer = $estaActiva && $diasRestantes <= 7;
                            $estaVencida = $membresia->fecha_vencimiento < now() || $membresia->estado === 'vencida';
                            $esReciente = $estaActiva && $membresia->fecha_inicio >= now()->subDays(7) && $membresia->fecha_inicio <= now();
                            
                            $porcentajeRestante = 100 - (($minutosTranscurridos / $minutosTotales) * 100);
                            $colorClass = 'bg-danger';
                            if ($porcentajeRestante > 75) $colorClass = 'bg-purple';
                            elseif ($porcentajeRestante > 50) $colorClass = 'bg-info';
                            elseif ($porcentajeRestante > 25) $colorClass = 'bg-success';
                            elseif ($porcentajeRestante > 5) $colorClass = 'bg-warning';
                            
                            $estadoRows = ['todas'];
                            if ($estaVencida) {
                                $estadoRows[] = 'vencidas';
                            } else {
                                if ($estaPorVencer) $estadoRows[] = 'por_vencer';
                                if ($estaActiva) $estadoRows[] = 'activas';
                                if ($esReciente) $estadoRows[] = 'recien_compradas';
                            }
                        @endphp
                        <tr class="js-filterable-item" data-estado="{{ implode(' ', $estadoRows) }}" data-plan="{{ $membresia->tipo_membresia_id }}" data-inicio="{{ $membresia->fecha_inicio->format('Y-m-d') }}" data-vencimiento="{{ $membresia->fecha_vencimiento->format('Y-m-d') }}" data-search="{{ strtolower($cliente->nombre . ' ' . $cliente->cedula . ' ' . $cliente->telefono) }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($cliente->foto_referencia)
                                        <img src="{{ asset('storage/' . $cliente->foto_referencia) }}" class="rounded-circle mr-2" style="width:30px;height:30px;object-fit:cover;">
                                    @else
                                        <div class="ic-avatar mr-2">{{ strtoupper(substr($cliente->nombre, 0, 2)) }}</div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold text-dark">{{ $cliente->nombre }}</div>
                                        <div class="text-muted" style="font-size:0.7rem;">{{ $cliente->cedula }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $cliente->telefono ?? '—' }}</td>
                            <td><span class="ic-badge-plan">{{ $membresia->tipoMembresia->nombre ?? '—' }}</span></td>
                            <td>{{ $membresia->fecha_inicio->format('d/m/Y') }}</td>
                            <td>{{ $membresia->fecha_vencimiento->format('d/m/Y') }}</td>
                            <td style="width: 120px;">
                                <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:0.75rem;">
                                    <span>{{ $estaVencida ? 'Vencida' : ($horasRestantes < 48 ? $horasRestantes . ' hrs' : $diasRestantes . ' d') }}</span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar {{ $colorClass }}" style="width: {{ $porcentajeRestante }}%"></div>
                                </div>
                            </td>
                            <td>
                                @if($estaVencida)
                                    <span class="ic-badge-expired">VENCIDA</span>
                                @elseif($estaPorVencer)
                                    <span class="ic-badge-warn">POR VENCER</span>
                                @elseif($estaActiva)
                                    <span class="ic-badge-active">ACTIVA</span>
                                @else
                                    <span class="ic-badge-expired">{{ strtoupper($membresia->estado) }}</span>
                                @endif
                            </td>
                            <td class="text-center py-1">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light py-0 px-2" type="button" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="dropdown-menu dropdown-menu-right" style="font-size:0.8rem;">
                                        <a class="dropdown-item py-1" href="{{ route('clientes.show', $cliente) }}"><i class="fas fa-eye member-action-icon mr-2"></i> Ver Expediente</a>
                                        <a class="dropdown-item py-1" href="{{ route('pagos.create', ['cliente_id' => $cliente->id]) }}"><i class="fas fa-sync-alt member-action-icon mr-2"></i> Renovar Plan</a>
                                        @if($cliente->telefono)
                                            @php
                                                $mensajeWhatsApp = strtr($gymConfig->mensaje_whatsapp ?? 'Hola @usuario, te recordamos que tu plan @plan vence en @dias días, el @fecha_vencimiento. Saludos de @gimnasio.', [
                                                    '@usuario' => $cliente->nombre,
                                                    '@plan' => $membresia->tipoMembresia->nombre ?? 'Sin plan',
                                                    '@dias' => (string) $diasRestantes,
                                                    '@fecha_vencimiento' => $membresia->fecha_vencimiento->format('d/m/Y'),
                                                    '@gimnasio' => $gymConfig->nombre_gimnasio,
                                                ]);
                                                $whatsappUrl = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $cliente->telefono) . '?text=' . rawurlencode($mensajeWhatsApp);
                                            @endphp
                                            <a class="dropdown-item py-1" href="{{ $whatsappUrl }}" target="_blank"><i class="fab fa-whatsapp member-action-icon mr-2"></i> WhatsApp</a>
                                            <a class="dropdown-item py-1" href="tel:{{ $cliente->telefono }}"><i class="fas fa-phone member-action-icon mr-2"></i> Llamar</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @else
        {{-- CARD VIEW --}}
        <div class="card-grid h-100">
            @forelse($membresias as $membresia)
            @php
                $cliente = $membresia->cliente;
                if (!$cliente) continue;
                $diasTotales = max(1, $membresia->fecha_inicio->diffInDays($membresia->fecha_vencimiento));
                $diasTranscurridos = max(0, min($diasTotales, $membresia->fecha_inicio->diffInDays(now(), false)));
                $diasRestantes = max(0, now()->startOfDay()->diffInDays($membresia->fecha_vencimiento, false));
                
                $estaActiva = $membresia->estado === 'activa' && $membresia->fecha_inicio <= now() && $membresia->fecha_vencimiento >= now();
                $estaPorVencer = $estaActiva && $diasRestantes <= 7;
                $estaVencida = $membresia->fecha_vencimiento < now() || $membresia->estado === 'vencida';
                $esReciente = $estaActiva && $membresia->fecha_inicio >= now()->subDays(7) && $membresia->fecha_inicio <= now();
                $statusClass = $estaVencida ? 'status-expired' : ($estaPorVencer ? 'status-warn' : '');
                
                $porcentajeRestante = 100 - (($diasTranscurridos / $diasTotales) * 100);
                $colorClass = 'bg-danger';
                if ($porcentajeRestante > 75) $colorClass = 'bg-purple';
                elseif ($porcentajeRestante > 50) $colorClass = 'bg-info';
                elseif ($porcentajeRestante > 25) $colorClass = 'bg-success';
                elseif ($porcentajeRestante > 5) $colorClass = 'bg-warning';
                
                $estadoRows = ['todas'];
                if ($estaVencida) {
                    $estadoRows[] = 'vencidas';
                } else {
                    if ($estaPorVencer) $estadoRows[] = 'por_vencer';
                    if ($estaActiva) $estadoRows[] = 'activas';
                    if ($esReciente) $estadoRows[] = 'recien_compradas';
                }
            @endphp
            <div class="member-card js-filterable-item {{ $statusClass }}" data-estado="{{ implode(' ', $estadoRows) }}" data-plan="{{ $membresia->tipo_membresia_id }}" data-inicio="{{ $membresia->fecha_inicio->format('Y-m-d') }}" data-vencimiento="{{ $membresia->fecha_vencimiento->format('Y-m-d') }}" data-search="{{ strtolower($cliente->nombre . ' ' . $cliente->cedula . ' ' . $cliente->telefono) }}">
                <div class="member-card-header">
                    @if($cliente->foto_referencia)
                        <img src="{{ asset('storage/' . $cliente->foto_referencia) }}" class="member-card-avatar" alt="{{ $cliente->nombre }}">
                    @else
                        <div class="member-card-avatar-placeholder">{{ strtoupper(substr($cliente->nombre, 0, 2)) }}</div>
                    @endif
                    <div class="member-card-info">
                        <p class="member-card-name">{{ $cliente->nombre }}</p>
                        <span class="member-card-detail">{{ $cliente->cedula }} &bull; {{ $cliente->telefono ?? 'Sin teléfono' }}</span>
                    </div>
                    @if($estaVencida)
                        <span class="ic-badge-expired">VENCIDA</span>
                    @elseif($estaPorVencer)
                        <span class="ic-badge-warn">POR VENCER</span>
                    @elseif($estaActiva)
                        <span class="ic-badge-active">ACTIVA</span>
                    @else
                        <span class="ic-badge-expired">{{ strtoupper($membresia->estado) }}</span>
                    @endif
                </div>
                <div class="member-card-body">
                    <div class="member-card-row">
                        <span class="member-card-label">Plan</span>
                        <span class="member-card-value"><span class="ic-badge-plan">{{ $membresia->tipoMembresia->nombre ?? '—' }}</span></span>
                    </div>
                    <div class="member-card-row">
                        <span class="member-card-label">Inicio / Vencimiento</span>
                        <span class="member-card-value">{{ $membresia->fecha_inicio->format('d/m/Y') }} - {{ $membresia->fecha_vencimiento->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:0.75rem;">
                            <span class="member-card-label">Duración</span>
                            <span class="font-weight-bold">{{ $estaVencida ? 'Vencida' : $diasRestantes . ' días rest.' }}</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar {{ $colorClass }}" style="width: {{ $porcentajeRestante }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="member-card-actions">
                    <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-outline-primary"><i class="fas fa-eye mr-1"></i> Expediente</a>
                    <a href="{{ route('pagos.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-outline-success"><i class="fas fa-sync-alt mr-1"></i> Renovar</a>
                    @if($cliente->telefono)
                        @php
                            $mensajeWhatsApp = strtr($gymConfig->mensaje_whatsapp ?? 'Hola @usuario, te recordamos que tu plan @plan vence en @dias días, el @fecha_vencimiento. Saludos de @gimnasio.', [
                                '@usuario' => $cliente->nombre,
                                '@plan' => $membresia->tipoMembresia->nombre ?? 'Sin plan',
                                '@dias' => (string) $diasRestantes,
                                '@fecha_vencimiento' => $membresia->fecha_vencimiento->format('d/m/Y'),
                                '@gimnasio' => $gymConfig->nombre_gimnasio,
                            ]);
                            $whatsappUrl = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $cliente->telefono) . '?text=' . rawurlencode($mensajeWhatsApp);
                        @endphp
                        <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-outline-success" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="tel:{{ $cliente->telefono }}" class="btn btn-outline-info" title="Llamar"><i class="fas fa-phone"></i></a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-5 w-100">
                <i class="fas fa-search fa-3x mb-3 d-block opacity-50"></i>
                <p>No se encontraron membresías con los filtros seleccionados.</p>
            </div>
            @endforelse
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    // Variables de filtros
    var table;
    var $estado = $('select[name="estado"]');
    var $plan = $('select[name="tipo_membresia"]');
    var $desde = $('input[name="fecha_desde"]');
    var $hasta = $('input[name="fecha_hasta"]');
    var $busqueda = $('input[name="busqueda"]');

    // Configurar DataTables
    if ($('#mainTable').length) {
        table = $('#mainTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            pageLength: 25,
            paging: true,
            searching: true,
            info: true,
            order: [],
            dom: "<'row dataTables_scroll'<'col-sm-12'tr>>" +
                 "<'row mt-2 align-items-center'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'i><'col-sm-12 col-md-4'p>>"
        });

        // Custom search
        $('#customSearchMemClientes').on('keyup', function() {
            table.search(this.value).draw();
        });
    }

    // Filtro unificado
    function checkFilters(estado, plan, desde, hasta, search, el) {
        var $el = $(el);
        
        // Estado
        if (estado && estado !== 'todas') {
            var st = $el.attr('data-estado');
            if (!st || !st.split(' ').includes(estado)) return false;
        }
        
        // Plan
        if (plan) {
            if ($el.attr('data-plan') !== plan) return false;
        }
        
        // Fechas (se filtra si fecha_inicio >= desde y fecha_vencimiento <= hasta)
        if (desde) {
            if ($el.attr('data-inicio') < desde) return false;
        }
        if (hasta) {
            if ($el.attr('data-vencimiento') > hasta) return false;
        }
        
        // Búsqueda (ya lo hace DataTables por defecto, pero para cards lo hacemos manual)
        if (search && $el.hasClass('member-card')) {
            var searchData = $el.attr('data-search') || '';
            if (searchData.indexOf(search.toLowerCase()) === -1) return false;
        }
        
        return true;
    }

    // Filtro para DataTables
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex, rowData, counter) {
            return checkFilters(
                $estado.val(),
                $plan.val(),
                $desde.val(),
                $hasta.val(),
                $busqueda.val(), // DataTables search is mapped below, but we can do it here too or let DataTables handle it
                settings.aoData[dataIndex].nTr
            );
        }
    );

    function applyFilters() {
        if (table) {
            // DataTables buscar (el text input)
            table.search($busqueda.val()).draw();
        }
        if ($('.card-grid').length) {
            $('.member-card').hide().filter(function() {
                return checkFilters($estado.val(), $plan.val(), $desde.val(), $hasta.val(), $busqueda.val(), this);
            }).show();
        }
    }

    // Eventos
    $('.js-filter-input').on('change keyup', function() {
        applyFilters();
    });

    $('.js-clear-filters').on('click', function() {
        $estado.val('todas');
        $plan.val('');
        $desde.val('');
        $hasta.val('');
        $busqueda.val('');
        applyFilters();
    });

    // Filtro inicial
    applyFilters();
});
</script>
@endpush
