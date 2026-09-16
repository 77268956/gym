<?php
$f = 'c:/laragon/www/GymX/resources/views/pagos/index.blade.php';
$c = file_get_contents($f);

// Make the kpi cards thinner
$oldKpiCss = <<<EOD
    /* KPI Cards */
    .kpi-card {
        border-radius: 10px;
        border: none;
        padding: 0.8rem 1.25rem;
        color: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #1E293B, #0F172A);
        height: 100%;
    }
    .kpi-icon { font-size: 2.2rem; opacity: 0.4; }
    .kpi-value { font-size: 1.6rem; font-weight: 800; margin: 0; line-height: 1; }
    .kpi-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; opacity: 0.8; margin-top: 4px;}
EOD;

$newKpiCss = <<<EOD
    /* KPI Cards */
    .kpi-card {
        border-radius: 8px;
        border: none;
        padding: 0.5rem 1rem; /* Más delgadas */
        color: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #1E293B, #0F172A);
        height: 100%;
    }
    .kpi-icon { font-size: 1.6rem; opacity: 0.4; } /* Icono más pequeño */
    .kpi-value { font-size: 1.3rem; font-weight: 800; margin: 0; line-height: 1; } /* Texto más pequeño */
    .kpi-label { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; opacity: 0.8; margin-top: 2px;}
EOD;

$c = str_replace($oldKpiCss, $newKpiCss, $c);

// Update HTML for the cards
$oldCards = <<<EOD
    {{-- KPI Cards --}}
    <div class="row tight flex-shrink-0">
        <div class="col-md-6">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value text-success">{{ \$gymConfig->simbolo_moneda }} {{ number_format(\$pagosHoy, 2) }}</h3>
                    <div class="kpi-label">Ingresos de Hoy</div>
                </div>
                <i class="fas fa-hand-holding-usd kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-6">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ \$gymConfig->simbolo_moneda }} {{ number_format(\$totalIngresos, 2) }}</h3>
                    <div class="kpi-label">Ingresos Totales (Global)</div>
                </div>
                <i class="fas fa-wallet kpi-icon"></i>
            </div>
        </div>
    </div>
EOD;

$newCards = <<<EOD
    {{-- KPI Cards --}}
    <div class="row tight flex-shrink-0">
        <div class="col-md-4">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value text-success">{{ \$gymConfig->simbolo_moneda }} {{ number_format(\$pagosHoy, 2) }}</h3>
                    <div class="kpi-label">Ingresos de Hoy</div>
                </div>
                <i class="fas fa-hand-holding-usd kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value text-info">{{ \$gymConfig->simbolo_moneda }} {{ number_format(\$pagosMes ?? 0, 2) }}</h3>
                    <div class="kpi-label">Ingresos del Mes</div>
                </div>
                <i class="fas fa-calendar-check kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ \$gymConfig->simbolo_moneda }} {{ number_format(\$totalIngresos, 2) }}</h3>
                    <div class="kpi-label">Ingresos Históricos</div>
                </div>
                <i class="fas fa-wallet kpi-icon"></i>
            </div>
        </div>
    </div>
EOD;

$c = str_replace($oldCards, $newCards, $c);

file_put_contents($f, $c);
echo "Pagos cards updated.";