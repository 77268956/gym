<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Canje CAN-{{ str_pad((string) $canje->id, 8, '0', STR_PAD_LEFT) }} · {{ $gymConfig->nombre_gimnasio ?? 'GymX' }}</title>
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
        .ticket-product-image { display:block; width:96px; height:82px; margin:1rem auto .6rem; border-radius:8px; object-fit:contain; }
        .ticket-product-placeholder { display:flex; width:64px; height:64px; align-items:center; justify-content:center; margin:1rem auto .6rem; border-radius:50%; background:color-mix(in srgb, var(--primary) 9%, #fff); color:var(--primary); font-size:1.35rem; }
        .ticket-product-name { margin:0; color:var(--text-main); font-size:1rem; font-weight:800; text-align:center; overflow-wrap:anywhere; }
        .ticket-product-category { margin:.2rem 0 .8rem; color:var(--text-muted); font-size:.7rem; text-align:center; }
        .ticket-details { margin:1rem 0; padding:1rem 0; border-top:1px dashed #cbd5e1; border-bottom:1px dashed #cbd5e1; }
        .ticket-line { display:flex; justify-content:space-between; gap:1rem; margin:.45rem 0; font-size:.8rem; }
        .ticket-line span:first-child { flex:0 0 40%; color:var(--text-muted); }
        .ticket-line strong { min-width:0; text-align:right; overflow-wrap:anywhere; }
        .ticket-points { padding:.85rem; border:1px solid color-mix(in srgb, var(--primary) 22%, #fff); border-radius:8px; background:color-mix(in srgb, var(--primary) 5%, #fff); text-align:center; }
        .ticket-points-label { margin:0 0 .25rem; color:var(--text-muted); font-size:.68rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
        .ticket-points-value { margin:0; color:var(--primary); font-size:1.25rem; font-weight:800; }
        .ticket-thanks { margin:1rem 0 0; color:var(--text-muted); font-size:.75rem; line-height:1.5; text-align:center; }
        .ticket-footer { margin-top:.75rem; color:#94a3b8; font-size:.65rem; text-align:center; }
        @page { size:80mm auto; margin:4mm; }
        @media print {
            body { min-height:0; padding:0; background:#fff; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            .ticket-screen { width:72mm; max-width:72mm; margin:0; }
            .ticket-actions { display:none!important; }
            .ticket-paper { padding:0; border-radius:0; box-shadow:none; }
            .ticket-product-image, .ticket-points { break-inside:avoid; }
        }
        @media(max-width:480px) { body { padding:1rem .5rem; }.ticket-paper { padding:1.1rem; } }
    </style>
</head>
<body>
    <main class="ticket-screen">
        <div class="ticket-actions">
            <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="window.print()"><i class="fas fa-print mr-1"></i>Imprimir ticket</button>
            <a href="{{ route('tienda.index') }}" class="btn btn-light btn-sm border"><i class="fas fa-arrow-left mr-1"></i>Tienda</a>
        </div>

        <article class="ticket-paper">
            <header class="ticket-brand">
                @if($gymConfig->logo_path)
                    <img class="ticket-logo" src="{{ asset('storage/'.$gymConfig->logo_path) }}" alt="Logo de {{ $gymConfig->nombre_gimnasio }}" onerror="this.hidden=true">
                @else
                    <span class="ticket-brand-icon"><i class="fas fa-dumbbell"></i></span>
                @endif
                <h1>{{ $gymConfig->nombre_gimnasio ?? 'GymX' }}</h1>
                <p>Comprobante de canje EcoGim</p>
            </header>

            <h2 class="ticket-title">TICKET DE ENTREGA</h2>
            <p class="ticket-number">N.º CAN-{{ str_pad((string) $canje->id, 8, '0', STR_PAD_LEFT) }}</p>

            @if($canje->producto?->imagen)
                <img class="ticket-product-image" src="{{ asset('storage/'.$canje->producto->imagen) }}" alt="{{ $canje->producto->nombre }}" onerror="this.hidden=true">
            @else
                <span class="ticket-product-placeholder"><i class="fas fa-box-open"></i></span>
            @endif
            <h3 class="ticket-product-name">{{ $canje->producto?->nombre ?? 'Producto no disponible' }}</h3>
            @if($canje->producto?->categoria)
                <p class="ticket-product-category">{{ ucfirst($canje->producto->categoria) }}</p>
            @endif

            <section class="ticket-details" aria-label="Datos del canje">
                <div class="ticket-line"><span>Fecha</span><strong>{{ $canje->fecha?->format('d/m/Y h:i A') }}</strong></div>
                <div class="ticket-line"><span>Cliente</span><strong>{{ $canje->cliente?->nombre ?? 'Cliente no disponible' }}</strong></div>
                @if($canje->cliente?->cedula)
                    <div class="ticket-line"><span>Cédula</span><strong>{{ $canje->cliente->cedula }}</strong></div>
                @endif
                <div class="ticket-line"><span>Entregado por</span><strong>{{ $canje->empleado?->nombre ?? 'Personal' }}</strong></div>
                <div class="ticket-line"><span>Periodo</span><strong>{{ $canje->periodo_canje }}</strong></div>
            </section>

            <div class="ticket-points">
                <p class="ticket-points-label">Puntos utilizados</p>
                <p class="ticket-points-value"><i class="fas fa-star mr-1"></i>{{ number_format($canje->puntos_utilizados) }} pts</p>
            </div>
            <p class="ticket-thanks">Canje registrado correctamente.<br>Gracias por participar en EcoGim.</p>
            <footer class="ticket-footer">Comprobante de entrega · {{ $gymConfig->nombre_gimnasio ?? 'GymX' }}</footer>
        </article>
    </main>
    <script>window.addEventListener('load', () => setTimeout(() => window.print(), 350));</script>
</body>
</html>
