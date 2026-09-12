<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use App\Models\Membresia;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SincronizarEstadoClientes extends Command
{
    protected $signature = 'clientes:sincronizar-estado';

    protected $description = 'Marca clientes como inactivos si su membresia vencio, y activos si tienen membresia vigente.';

    public function handle(): int
    {
        // 1. Marcar membrecias vencidas
        $membresiasVencidas = Membresia::where('estado', 'activa')
            ->where('fecha_vencimiento', '<', Carbon::today())
            ->get();

        foreach ($membresiasVencidas as $membresia) {
            $membresia->update(['estado' => 'vencida']);
        }

        $this->line("  -> {$membresiasVencidas->count()} membrecias marcadas como vencidas.");

        // 2. Clientes con membrecia activa -> activo
        $clientesAActivar = Cliente::whereHas('membresias', function ($q) {
            $q->where('estado', 'activa')->where('fecha_vencimiento', '>=', Carbon::today());
        })->where('estado', 'inactivo')->count();

        Cliente::whereHas('membresias', function ($q) {
            $q->where('estado', 'activa')->where('fecha_vencimiento', '>=', Carbon::today());
        })->where('estado', 'inactivo')->update(['estado' => 'activo']);

        $this->line("  -> {$clientesAActivar} clientes reactivados.");

        // 3. Clientes sin membrecia activa -> inactivo
        $clientesAInactivar = Cliente::whereDoesntHave('membresias', function ($q) {
            $q->where('estado', 'activa')->where('fecha_vencimiento', '>=', Carbon::today());
        })->where('estado', 'activo')->count();

        Cliente::whereDoesntHave('membresias', function ($q) {
            $q->where('estado', 'activa')->where('fecha_vencimiento', '>=', Carbon::today());
        })->where('estado', 'activo')->update(['estado' => 'inactivo']);

        $this->line("  -> {$clientesAInactivar} clientes marcados como inactivos.");

        $this->info('Sincronizacion completada.');

        return Command::SUCCESS;
    }
}
