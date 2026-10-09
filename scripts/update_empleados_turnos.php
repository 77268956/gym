<?php
$f = 'c:/laragon/www/GymX/resources/views/empleados/index.blade.php';
$c = file_get_contents($f);

// 1. Replace Panel Title
$oldTitle = <<<EOD
            {{-- Panel Turnos --}}
            <div class="ic-card flex-grow-1" style="min-height:0;">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-shrink-0">
                    <span class="ic-card-title mb-0"><i class="fas fa-calendar-alt text-info mr-1"></i> Turnos</span>
                    <span class="ic-badge-active">HOY</span>
                </div>
                <div style="overflow-y:auto; padding-right:4px;">
                    @php
                        \$listaTurnos = [
                            (object)['nombre' => 'Admin General', 'plan' => 'Turno Completo', 'dias' => 0, 'nivel' => 'active'],
                            (object)['nombre' => 'Recepción 1', 'plan' => 'Mañana', 'dias' => 0, 'nivel' => 'active'],
                        ];
                    @endphp
                    @forelse (\$listaTurnos as \$item)
                    <div class="ic-list-item">
                        <div>
                            <div class="ic-list-title">{{ \$item->nombre }}</div>
                            <div class="ic-list-sub">{{ \$item->plan }}</div>
                        </div>
                        <div>
                            <span class="ic-badge-active">EN CURSO</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted small">No hay turnos registrados.</p>
                    @endforelse
                </div>
            </div>
EOD;

$newTitle = <<<EOD
            {{-- Panel Asistencias (Pendientes) --}}
            <div class="ic-card flex-grow-1" style="min-height:0;">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-shrink-0">
                    <span class="ic-card-title mb-0"><i class="fas fa-clock text-warning mr-1"></i> Faltan por llegar</span>
                    <span class="ic-badge-warn">HOY</span>
                </div>
                <div style="overflow-y:auto; padding-right:4px;">
                    @php
                        // DATOS DE PRUEBA: Empleados que no han marcado entrada
                        \$listaFaltantes = [
                            (object)['nombre' => 'Carlos Javier', 'turno' => 'Turno Mañana', 'hora_esperada' => '07:00 AM'],
                            (object)['nombre' => 'María José',    'turno' => 'Turno Tarde',  'hora_esperada' => '02:00 PM'],
                        ];
                    @endphp
                    @forelse (\$listaFaltantes as \$item)
                    <div class="ic-list-item">
                        <div>
                            <div class="ic-list-title">{{ \$item->nombre }}</div>
                            <div class="ic-list-sub">{{ \$item->turno }} ({{ \$item->hora_esperada }})</div>
                        </div>
                        <div>
                            <span class="ic-badge-critical">PENDIENTE</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted small">Todos los empleados han marcado llegada.</p>
                    @endforelse
                </div>
            </div>
EOD;

$c = str_replace($oldTitle, $newTitle, $c);

// If for some reason the exact str_replace failed due to slight differences, we can use regex.
if (strpos($c, 'Faltan por llegar') === false) {
    // Regex replace
    $c = preg_replace('/\{\{-- Panel Turnos --\}\}.*?<\/div>\s*<\/div>/is', $newTitle, $c);
}

file_put_contents($f, $c);
echo "Panel updated.";