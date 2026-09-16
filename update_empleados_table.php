<?php
$f = 'c:/laragon/www/GymX/resources/views/empleados/index.blade.php';
$c = file_get_contents($f);

// 1. Remove phone column header
$c = str_replace('<th>TELÉFONO</th>', '', $c);

// 2. Remove phone column data
$c = str_replace("<td>{{ \$empleado->telefono ?? '—' }}</td>", '', $c);

// 3. Update the Role badges in CSS
$oldRoleBadges = <<<EOD
    .ic-badge-admin { background: #E0E7FF; color: #4338CA; padding: 3px 8px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; }
    .ic-badge-staff { background: #F3E8FF; color: #7E22CE; padding: 3px 8px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; }
EOD;

$newRoleBadges = <<<EOD
    .ic-badge-role { background: linear-gradient(135deg, #1E293B, #0F172A); color: white; padding: 3px 10px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; letter-spacing: 0.05em; }
EOD;

$c = str_replace($oldRoleBadges, $newRoleBadges, $c);

// 4. Update the Role column logic in the HTML
$oldRoleHtml = <<<EOD
                                <td>
                                    <span class="{{ \$empleado->rol === 'admin' ? 'ic-badge-admin' : 'ic-badge-staff' }}">
                                        {{ strtoupper(\$empleado->rol === 'admin' ? 'Administrador' : 'Recepción') }}
                                    </span>
                                </td>
EOD;

$newRoleHtml = <<<EOD
                                <td>
                                    <span class="ic-badge-role">
                                        {{ strtoupper(\$empleado->rol === 'admin' ? 'Administrador' : 'Recepción') }}
                                    </span>
                                </td>
EOD;

$c = str_replace($oldRoleHtml, $newRoleHtml, $c);

file_put_contents($f, $c);
echo "Empleados table updated.";