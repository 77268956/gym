<?php
$f = 'c:/laragon/www/GymX/resources/views/configuracion/index.blade.php';
$c = file_get_contents($f);

$fields = <<<'EOD'
                    <!-- Moneda -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="font-weight-bold">Moneda (Nombre)</label>
                            <input type="text" name="moneda" class="form-control" value="{{ old('moneda', $configuracion->moneda ?? 'Lempira') }}" placeholder="Lempira">
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold">Símbolo</label>
                            <input type="text" name="simbolo_moneda" class="form-control" value="{{ old('simbolo_moneda', $configuracion->simbolo_moneda ?? 'L.') }}" placeholder="L.">
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold">Código ISO</label>
                            <input type="text" name="codigo_moneda" class="form-control" value="{{ old('codigo_moneda', $configuracion->codigo_moneda ?? 'HNL') }}" placeholder="HNL">
                        </div>
                    </div>
EOD;

$c = str_replace('<hr class="my-4">', $fields . "\n                    <hr class=\"my-4\">", $c);
file_put_contents($f, $c);
echo "Fixed configuracion\n";