<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $gymConfig->nombre_gimnasio ?? 'GymX' }} - Reporte {{ $rangoFechas }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <style>
        :root {
            --primary: {{ $gymConfig->color_primario ?? '#2563EB' }};
            --primary-hover: {{ $gymConfig->color_primario_hover ?? '#1D4ED8' }};
            --sidebar-bg: {{ $gymConfig->color_sidebar ?? '#1E293B' }};
            --text-main: #334155;
            --text-muted: #64748B;
        }
        * { box-sizing:border-box; }
        body { margin:0; padding:24px; color:var(--text-main); background:#f8fafc; font:13px Arial,sans-serif; overflow-wrap:anywhere; }
        .report-document { max-width:1120px; margin:0 auto; }
        .text-muted { color:var(--text-muted)!important; }
        .print-actions { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:16px; padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; background:#fff; }
        .print-actions .btn { border-color:var(--primary); background:var(--primary); }
        .report-print-header { display:flex; justify-content:space-between; align-items:center; gap:20px; margin-bottom:18px; padding:18px 20px; border:1px solid #e2e8f0; border-left:5px solid var(--primary); border-radius:10px; background:#fff; }
        .report-brand { display:flex; min-width:0; align-items:center; gap:14px; }
        .report-logo { max-width:64px; max-height:64px; object-fit:contain; }
        .report-brand-icon { display:flex; width:52px; height:52px; flex:0 0 52px; align-items:center; justify-content:center; border-radius:12px; background:color-mix(in srgb, var(--primary) 10%, #fff); color:var(--primary); font-size:23px; }
        .report-kicker { margin:0 0 4px; color:var(--primary); font-size:9px; font-weight:700; letter-spacing:.12em; text-transform:uppercase; }
        .report-print-header h1 { margin:0; color:var(--text-main); font-size:21px; font-weight:700; overflow-wrap:anywhere; }
        .report-print-header p { margin:4px 0 0; color:var(--text-muted); }
        .report-period { flex:0 0 auto; padding:9px 12px; border:1px solid color-mix(in srgb, var(--primary) 25%, #fff); border-radius:8px; color:var(--primary); text-align:right; }
        .report-period small { display:block; margin-bottom:3px; color:var(--text-muted); font-size:8px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
        .report-period strong { font-size:11px; }
        .report-generated { margin:0 0 14px; color:var(--text-muted); font-size:9px; text-align:right; }
        .report-stat { display:flex; justify-content:space-between; min-width:0; min-height:76px; margin-bottom:12px; padding:13px; border:1px solid color-mix(in srgb, var(--primary) 20%, #fff); border-top:3px solid var(--primary); border-radius:9px; color:var(--text-main); background:#fff; }
        .report-stat-label { color:var(--text-muted); font-size:9px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
        .report-stat-value { margin:6px 0 0; color:var(--primary); font-size:18px; font-weight:700; }
        .report-stat-icon { color:var(--primary); opacity:.45; }
        .report-chart-panel { height:205px; padding:12px; }
        .report-chart-title { margin-bottom:8px; color:var(--text-muted); font-size:9px; font-weight:700; text-transform:uppercase; overflow-wrap:anywhere; }
        .ic-card { margin-bottom:14px; border:1px solid #dbe3ed; border-radius:9px; overflow:visible; background:#fff; }
        .report-accordion-toggle { display:flex; width:100%; justify-content:space-between; padding:10px 12px; border:0; border-bottom:1px solid #e2e8f0; border-left:4px solid var(--primary); border-radius:8px 8px 0 0; background:color-mix(in srgb, var(--primary) 6%, #fff); color:var(--text-main); font-size:12px; font-weight:700; text-align:left; list-style:none; }
        .report-accordion-toggle::-webkit-details-marker { display:none; }
        .report-accordion-icon { display:none; }
        .report-accordion-content { display:block !important; }
        .ic-card-header { padding:8px 10px; border-bottom:1px solid #e2e8f0; background:#f8fafc; color:var(--text-main); font-size:10px; font-weight:700; overflow-wrap:anywhere; }
        .ic-table { width:100%; margin-bottom:0; table-layout:fixed; }
        .ic-table th, .ic-table td { padding:7px 8px; border-color:#e8edf3; font-size:9px; vertical-align:top; overflow-wrap:anywhere; }
        .ic-table th { background:color-mix(in srgb, var(--primary) 8%, #fff); color:var(--primary); font-size:8px; letter-spacing:.04em; text-transform:uppercase; }
        .ic-table tbody tr:nth-child(even) { background:#f8fafc; }
        .ic-table td { color:var(--text-main); }
        .table-responsive { width:100%; overflow:visible!important; }
        .report-empty { padding:14px!important; color:var(--text-muted); text-align:center; }
        .report-footer { margin-top:16px; padding-top:8px; border-top:1px solid #e2e8f0; color:var(--text-muted); font-size:8px; text-align:center; }
        .report-chart-panel, .report-stat, tr { break-inside:avoid; }
        thead { display:table-header-group; }
        @page { size:A4 portrait; margin:13mm 12mm 15mm; }
        @media print {
            body { padding:0; background:#fff; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            .report-document { max-width:none; }
            .print-actions { display:none !important; }
            .report-print-header, .ic-card, .report-stat { box-shadow:none!important; }
            .ic-card { break-inside:auto; }
            .report-accordion-toggle { break-after:avoid; }
            .report-chart-panel { break-inside:avoid; }
            a { color:inherit; text-decoration:none; }
        }
        @media(max-width:640px) {
            body { padding:12px; }
            .report-print-header { align-items:flex-start; flex-direction:column; }
            .report-period { width:100%; text-align:left; }
            .print-actions { align-items:flex-start; flex-direction:column; }
        }
    </style>
</head>
<body>
    <main class="report-document">
        <div class="print-actions">
            <span class="text-muted small">En el diálogo de impresión, selecciona “Guardar como PDF”.</span>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="fas fa-file-pdf mr-1"></i>Guardar como PDF</button>
        </div>
        <header class="report-print-header">
            <div class="report-brand">
                @if($gymConfig->logo_path)
                    <img class="report-logo" src="{{ asset('storage/'.$gymConfig->logo_path) }}" alt="Logo de {{ $gymConfig->nombre_gimnasio }}" onerror="this.hidden=true">
                @else
                    <span class="report-brand-icon"><i class="fas fa-dumbbell"></i></span>
                @endif
                <div>
                    <p class="report-kicker">Informe de gestión</p>
                    <h1>{{ $gymConfig->nombre_gimnasio ?? 'GymX' }}</h1>
                    <p>Resumen financiero, clientes, empleados y sistema</p>
                </div>
            </div>
            <div class="report-period"><small>Periodo del reporte</small><strong>{{ $rangoFechas }}</strong></div>
        </header>
        <p class="report-generated">Generado el {{ now()->format('d/m/Y H:i') }}</p>
    @include('reportes.partials.content')
    <footer class="report-footer">{{ $gymConfig->nombre_gimnasio ?? 'GymX' }} · Informe generado desde el sistema de gestión.</footer>
    </main>
    <script>
        let reportChartsReady = false;
        let reportPrintScheduled = false;

        const printReportWhenReady = () => {
            if (!reportChartsReady || document.readyState !== 'complete' || reportPrintScheduled) return;

            reportPrintScheduled = true;
            setTimeout(() => window.print(), 900);
        };

        document.addEventListener('reportChartsReady', () => {
            reportChartsReady = true;
            printReportWhenReady();
        }, { once: true });

        window.addEventListener('load', () => {
            if (reportChartsReady) {
                printReportWhenReady();
                return;
            }

            setTimeout(() => {
                if (reportPrintScheduled) return;

                reportPrintScheduled = true;
                window.print();
            }, 900);
        }, { once: true });
    </script>
    @include('reportes.partials.charts-script')
</body>
</html>
