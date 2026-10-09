<?php
$f = 'c:/laragon/www/GymX/resources/views/configuracion/index.blade.php';
$c = file_get_contents($f);

$puntos_section = <<<'EOD'

                    <hr class="my-4">

                    <!-- Sistema de Puntos -->
                    <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="font-size:.75rem;">Sistema de Recompensas por Asistencia</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="puntos_por_visita" class="font-weight-bold">Puntos por visita diaria</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-star"></i></span>
                                    </div>
                                    <input type="number"
                                           name="puntos_por_visita"
                                           id="puntos_por_visita"
                                           class="form-control"
                                           min="0"
                                           max="1000"
                                           value="{{ old('puntos_por_visita', $configuracionPuntos->puntos_por_visita ?? 10) }}"
                                           required>
                                </div>
                                <small class="form-text text-muted">
                                    Puntos que gana un cliente cada vez que escanea su rostro en recepción. El valor actual es <strong>{{ $configuracionPuntos->puntos_por_visita ?? 10 }} puntos</strong> por visita.
                                </small>
                            </div>
                        </div>
                    </div>
EOD;

// Insert before the last <hr class="my-4"> before buttons
$c = str_replace('<hr class="my-4">

                    <!-- Botones de Acci', $puntos_section . '

                    <hr class="my-4">

                    <!-- Botones de Acci', $c);

file_put_contents($f, $c);
echo "View updated.\n";