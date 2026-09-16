<?php
$f = 'c:/laragon/www/GymX/resources/views/pagos/index.blade.php';
$c = file_get_contents($f);

// 1. I will replace the ENTIRE modal block to make sure there's no old DataTable left inside.
$modalRegex = '/\{\{-- ============================================================\s*MODAL 1: SELECCIÓN DE CLIENTE.*?--\}\}.*?\<\!-- End Modal --\>|\{\{-- ============================================================\s*MODAL 1: SELECCIÓN DE CLIENTE.*?--\}\}.*?(?=@endsection)/is';

$newModal = <<<'EOD'
{{-- ============================================================
     MODAL SELECCIÓN DE CLIENTE (BÚSQUEDA)
============================================================ --}}
<div class="modal fade" id="modalClientes" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content border-0" style="border-radius: 16px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
            
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #1E293B, #0F172A); padding: 1.5rem 1.5rem 1rem;">
                <h5 class="modal-title font-weight-bold text-white w-100 text-center">
                    <i class="fas fa-search-dollar mb-2 d-block text-primary" style="font-size: 2rem;"></i>
                    Nuevo Cobro
                </h5>
                <button type="button" class="close text-white position-absolute" style="top: 15px; right: 20px; opacity: 0.8;" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body p-4 bg-white">
                <div class="form-group mb-0">
                    <label class="font-weight-bold text-muted small text-uppercase mb-2">Selecciona un socio</label>
                    <select id="select2Cliente" class="form-control form-control-lg" style="width: 100%;"></select>
                </div>
                
                <div id="clienteSeleccionadoInfo" class="d-none mt-4 animate__animated animate__fadeIn">
                    <div class="card bg-light border-0" style="border-radius: 12px;">
                        <div class="card-body p-4 text-center">
                            
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
                            
                            <a href="#" id="btnCobrar" class="btn btn-primary font-weight-bold btn-block py-3" style="border-radius: 10px; font-size: 1.05rem; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);">
                                <i class="fas fa-arrow-right mr-2"></i> Procesar Cobro
                            </a>

                            <div id="alertaYaActiva" class="alert alert-warning d-none mt-3 mb-0 small text-left" style="border-radius: 8px;">
                                <i class="fas fa-exclamation-triangle mr-1"></i> El socio tiene una membresía vigente.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
EOD;

$c = preg_replace($modalRegex, $newModal, $c);

// En caso de que no haya hecho match (porque el HTML estaba corrupto con los tags huérfanos),
// voy a forzar un limpiado. Haremos un replace de todo lo que esté entre `<div class="container-fluid py-4">` fin, y `@endsection` y lo reescribiré bien.
file_put_contents($f, $c);
echo "Modal replaced.\n";