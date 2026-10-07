<?php

namespace Database\Seeders;

use App\Models\Empleado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmpleadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $username = (string) env('GYMX_ADMIN_USERNAME', 'admin');
        $adminPassword = (string) env('GYMX_ADMIN_PASSWORD', Str::random(32));

        if (Empleado::where('usuario', $username)->exists()) {
            return;
        }

        Empleado::create([
            'nombre' => 'Administrador',
            'cedula' => 'ADMIN-BOOTSTRAP',
            'usuario' => $username,
            'email' => env('GYMX_ADMIN_EMAIL', 'admin@example.com'),
            'password_hash' => Hash::make($adminPassword),
            'rol' => 'admin',
            'estado' => 'activo',
        ]);

        $this->command?->info("Cuenta inicial creada: {$username}");
        $this->command?->warn("Contraseña inicial (guárdala ahora): {$adminPassword}");
    }
}
