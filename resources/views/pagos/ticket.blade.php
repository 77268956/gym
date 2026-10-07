<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recibo REC-{{ str_pad((string) $pago->id, 8, '0', STR_PAD_LEFT) }} · {{ $gymConfig->nombre_gimnasio ?? 'GymX' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary: {{ $gymConfig->color_primario ?? '#2563EB' }};
            --text-main: #1f2937;
            --text-muted: #64748b;
        }
        * { box-sizing:border-box; }
        body { min-height:100vh; margin:0; padding:2rem 1rem; background:#f1f5f9; color:var(--text-main); font:14px Arial,sans-serif; }
        .ticket-screen { width:min(100%, 440px); margin:0 auto; }
        .ticket-actions { display:flex; justify-content:center; gap:.5rem; margin-bottom:1rem; }
        .ticket-paper { width:100%; padding:1.5rem; border-radius:12px; background:#fff; box-shadow:0 8px 28px rgba(15,23,42,.1); }
        .ticket-brand { padding-bottom:1rem; border-bottom:1px dashed #cbd5e1; text-align:center; }
        .ticket-logo { display:block; max-width:100px; max-height:64px; margin:0 auto .5rem; object-fit:contain; }
        .ticket-brand-icon { display:inline-flex; width:48px; height:48px; align-items:center; justify-content:center; margin-bottom:.5rem; border-radius:50%; background:color-mix(in srgb, var(--primary) 10%, #fff); color:var(--primary); font-size:1.25rem; }
        .ticket-brand h1 { margin:0; font-size:1.15rem; font-weight:800; overflow-wrap:anywhere; }
        .ticket-brand p { margin:.25rem 0 0; color:var(--text-muted); font-size:.76rem; }
        .ticket-title { margin:1rem 0 .25rem; color:var(--primary); font-size:.8rem; font-weight:800; letter-spacing:.12em; text-align:center; }
        .ticket-number { margin:0; color:var(--text-muted); font-size:.75rem; text-align:center; }
        .ticket-details { margin:1rem 0; padding:1rem 0; border-top:1px dashed #cbd5e1; border-bottom:1px dashed #cbd5e1; }
        .ticket-line { display:flex; justify-content:space-between; gap:1rem; margin:.45rem 0; font-size:.8rem; }
        .ticket-line span:first-child { flex:0 0 40%; color:var(--text-muted); }
        .ticket-line strong { min-width:0; text-align:right; overflow-wrap:anywhere; }
        .ticket-plan { margin:1rem 0; padding:.85rem; border:1px solid color-mix(in srgb, var(--primary) 22%, #fff); border-radius:8px; background:color-mix(in srgb, var(--primary) 5%, #fff); }
        .ticket-plan-label { margin:0 0 .25rem; color:var(--text-muted); font-size:.68rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
        .ticket-plan-name { margin:0; color:var(--primary); font-size:1rem; font-weight:800; overflow-wrap:anywhere; }
        .ticket-total { display:flex; justify-content:space-between; align-items:center; padding:.8rem 0; border-bottom:1px dashed #cbd5e1; font-size:.9rem; font-weight:700; }
        .ticket-total strong { color:var(--primary); font-size:1.25rem; }
        .ticket-thanks { margin:1rem 0 0; color:var(--text-muted); font-size:.75rem; line-height:1.5; text-align:center; }
        .ticket-footer { margin-top:.75rem; color:#94a3b8; font-size:.65rem; text-align:center; }
        @page { size:80mm auto; margin:4mm; }
        @media print {
            body { min-height:0; padding:0; background:#fff; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            .ticket-screen { width:72mm; max-width:72mm; margin:0; }
            .ticket-actions { display:none!important; }
            .ticket-paper { padding:0; border-radius:0; box-shadow:none; }
            .ticket-plan, .ticket-brand-icon { break-inside:avoid; }
        }
        @media(max-width:480px) { body { padding:1rem .5rem; }.ticket-paper { padding:1.1rem; } }
    </style>
</head>
<body>
    <main class="ticket-screen">
        <div class="ticket-actions">
            <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="window.print()"><i class="fas fa-print mr-1"></i>Imprimir ticket</button>
            <a href="{{ route('pagos.index') }}" class="btn btn-light btn-sm border"><i class="fas fa-arrow-left mr-1"></i>Pagos</a>
            <a href="{{ route('pagos.create', ['cliente_id' => $pago->cliente_id]) }}" class="btn btn-light btn-sm border" title="Nuevo cobro"><i class="fas fa-plus"></i></a>
        </div>

        <article class="ticket-paper">
            <header class="ticket-brand">
                @if($gymConfig->logo_path)
                    <img class="ticket-logo" src="{{ asset('storage/'.$gymConfig->logo_path) }}" alt="Logo de {{ $gymConfig->nombre_gimnasio }}" onerror="this.hidden=true">
                @else
                    <span class="ticket-brand-icon"><i class="fas fa-dumbbell"></i></span>
                @endif
                <h1>{{ $gymConfig->nombre_gimnasio ?? 'GymX' }}</h1>
                <p>Comprobante de pago</p>
            </header>

            <h2 class="ticket-title">RECIBO DE PAGO</h2>
            <p class="ticket-number">N.º REC-{{ str_pad((string) $pago->id, 8, '0', STR_PAD_LEFT) }}</p>

            <section class="ticket-details" aria-label="Datos del pago">
                <div class="ticket-line"><span>Fecha</span><strong>{{ $pago->fecha_pago?->format('d/m/Y h:i A') }}</strong></div>
                <div class="ticket-line"><span>Cliente</span><strong>{{ trim(($pago->cliente?->nombre ?? '').' '.($pago->cliente?->apellido ?? '')) ?: 'Cliente no disponible' }}</strong></div>
                @if($pago->cliente?->cedula)
                    <div class="ticket-line"><span>Cédula</span><strong>{{ $pago->cliente->cedula }}</strong></div>
                @endif
                <div class="ticket-line"><span>Atendido por</span><strong>{{ $pago->empleado?->nombre ?? 'Personal' }}</strong></div>
                <div class="ticket-line"><span>Concepto</span><strong>{{ $pago->concepto ?? ($pago->tipo_pago === 'membresia' ? 'Membresía' : 'Pase diario') }}</strong></div>
                <div class="ticket-line"><span>Método</span><strong>{{ ucfirst($pago->metodo_pago) }}</strong></div>
            </section>

            @if($pago->membresia)
                <section class="ticket-plan" aria-label="Vigencia de membresía">
                    <p class="ticket-plan-label">Plan contratado</p>
                    <p class="ticket-plan-name">{{ $pago->membresia->tipoMembresia?->nombre ?? 'Membresía' }}</p>
                    <div class="ticket-line mb-0"><span>Vigencia</span><strong>{{ $pago->membresia->fecha_inicio?->format('d/m/Y') }} — {{ $pago->membresia->fecha_vencimiento?->format('d/m/Y') }}</strong></div>
                </section>
            @endif

            <div class="ticket-total"><span>Total pagado</span><strong>{{ $gymConfig->simbolo_moneda ?? '$' }} {{ number_format($pago->monto, 2) }}</strong></div>
            <p class="ticket-thanks">Gracias por confiar en {{ $gymConfig->nombre_gimnasio ?? 'GymX' }}.<br>Conserva este comprobante para cualquier consulta.</p>
            <footer class="ticket-footer">Comprobante de pago · {{ $gymConfig->nombre_gimnasio ?? 'GymX' }}</footer>
        </article>
    </main>
    <script>window.addEventListener('load', () => setTimeout(() => window.print(), 350));</script>
</body>
</html>
