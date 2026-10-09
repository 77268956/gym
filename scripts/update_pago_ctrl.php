<?php
$f = 'c:/laragon/www/GymX/app/Http/Controllers/PagoController.php';
$c = file_get_contents($f);

$ajaxMethod = <<<'EOD'
    /** AJAX: devuelve clientes buscados para Select2 */
    public function buscarClientes(Request $request)
    {
        $term = $request->input('q');
        
        $query = Cliente::with(['membresias' => function ($q) {
            $q->where('estado', 'activa')->where('fecha_vencimiento', '>=', Carbon::today())->latest();
        }]);

        if ($term) {
            $query->where(function($q) use ($term) {
                $q->where('nombre', 'LIKE', '%' . $term . '%')
                  ->orWhere('cedula', 'LIKE', '%' . $term . '%');
            });
        }

        $clientes = $query->limit(20)->get();
        
        $resultados = [];
        foreach ($clientes as $c) {
            $membActiva = $c->membresias->first();
            $estadoStr = $c->estado;
            
            if ($membActiva) {
                $membStatus = 'ACTIVA';
            } else {
                $tieneVencida = $c->membresias()->where('estado', 'vencida')->exists();
                $membStatus = $tieneVencida ? 'VENCIDA' : 'SIN MEMBRESÍA';
            }
            
            $resultados[] = [
                'id' => $c->id,
                'text' => $c->nombre,
                'cedula' => $c->cedula,
                'estado' => $c->estado,
                'membresia_status' => $membStatus,
                'foto' => $c->foto_referencia ? asset('storage/' . $c->foto_referencia) : null
            ];
        }

        return response()->json(['results' => $resultados]);
    }
}
EOD;

$c = str_replace("}\n", "}\n" . $ajaxMethod . "\n", rtrim($c)); // replace last closing brace
// wait, easier to just do a strict replace at the end
$c = preg_replace('/}\s*$/', "\n$ajaxMethod\n", $c);

file_put_contents($f, $c);
echo "PagoController updated with AJAX search.\n";