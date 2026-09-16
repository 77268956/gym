<?php
$f = 'c:/laragon/www/GymX/resources/views/pagos/index.blade.php';
$c = file_get_contents($f);

// 1. Fix the HTML of the badges
$oldHtml = <<<'EOD'
                            <div class="position-relative d-inline-block mb-3">
                                <div id="infoFoto"></div>
                                <span id="infoEstado" class="position-absolute" style="bottom: 0; right: -5px; width: 16px; height: 16px; border-radius: 50%; border: 3px solid #f8f9fa;"></span>
                            </div>

                            <h5 id="infoNombre" class="font-weight-bold mb-0 text-dark" style="font-size: 1.25rem;">Nombre</h5>
                            <p id="infoCedula" class="text-muted mb-3 small">Cédula</p>
                            
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <span class="text-muted small mr-2">Membresía:</span>
                                <span id="infoMembresia" class="badge badge-pill px-3 py-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">ESTADO</span>
                            </div>
EOD;

$newHtml = <<<'EOD'
                            <div class="position-relative d-inline-block mb-3">
                                <div id="infoFoto"></div>
                            </div>

                            <h5 id="infoNombre" class="font-weight-bold mb-0 text-dark" style="font-size: 1.25rem;">Nombre</h5>
                            <p id="infoCedula" class="text-muted mb-3 small">Cédula</p>
                            
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <span class="text-muted small mr-2">Membresía:</span>
                                <span id="infoMembresia" class="badge badge-pill px-3 py-2 mr-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">ESTADO</span>
                                <span id="infoEstado" class="badge badge-pill px-3 py-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">ESTADO CLI</span>
                            </div>
EOD;

// There are probably encoding issues with "Cédula" and "Membresía", I'll use regex carefully
$c = preg_replace('/<div class="position-relative d-inline-block mb-3">.*?<\/div>\s*<a href="#" id="btnCobrar"/is', 
'<div class="position-relative d-inline-block mb-3">
    <div id="infoFoto"></div>
</div>

<h5 id="infoNombre" class="font-weight-bold mb-0 text-dark" style="font-size: 1.25rem;">Nombre</h5>
<p id="infoCedula" class="text-muted mb-3 small">Cédula</p>

<div class="d-flex justify-content-center align-items-center mb-4">
    <span class="text-muted small mr-2">Membresía:</span>
    <span id="infoMembresia" class="badge badge-pill px-3 py-2 mr-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">MEMB</span>
    <span class="text-muted small mr-2 ml-2">Cliente:</span>
    <span id="infoEstado" class="badge badge-pill px-3 py-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">ESTADO</span>
</div>

<a href="#" id="btnCobrar"', $c);


// 2. Fix the JS
$oldJs = <<<'EOD'
        // Estado
        $('#infoEstado').text(data.estado.toUpperCase());
        $('#infoEstado').className = 'badge ' + (data.estado === 'activo' ? 'ic-status-active' : 'ic-status-inactive') + ' ml-2';
EOD;

$newJs = <<<'EOD'
        // Estado
        $('#infoEstado').text(data.estado.toUpperCase());
        $('#infoEstado').removeClass().addClass('badge badge-pill px-3 py-2 ' + (data.estado === 'activo' ? 'ic-status-active' : 'ic-status-inactive'));
EOD;

$c = str_replace($oldJs, $newJs, $c);

file_put_contents($f, $c);
echo "Fixed.\n";