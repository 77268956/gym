<?php

namespace Database\Seeders;

use App\Models\Empleado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmpleadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empleados = [
            [
                'nombre' => 'Marcus Vance',
                'cedula' => '0801-1985-12345',
                'usuario' => 'mvance',
                'password_hash' => Hash::make('123'),
                'rol' => 'admin',
                'hora_entrada_turno' => '06:00:00',
                'hora_salida_turno' => '14:00:00',
                'tolerancia_minutos' => 10,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Sarah Connor',
                'cedula' => '0801-1990-54321',
                'usuario' => 'sconnor',
                'password_hash' => Hash::make('123'),
                'rol' => 'empleado',
                'hora_entrada_turno' => '14:00:00',
                'hora_salida_turno' => '22:00:00',
                'tolerancia_minutos' => 10,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Amanda Waller',
                'cedula' => '0801-1992-45678',
                'usuario' => 'awaller',
                'password_hash' => Hash::make('123'),
                'rol' => 'empleado',
                'hora_entrada_turno' => '08:00:00',
                'hora_salida_turno' => '16:00:00',
                'tolerancia_minutos' => 15,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'John Kreese',
                'cedula' => '0801-1980-98765',
                'usuario' => 'jkreese',
                'password_hash' => Hash::make('123'),
                'rol' => 'admin',
                'hora_entrada_turno' => '09:00:00',
                'hora_salida_turno' => '17:00:00',
                'tolerancia_minutos' => 5,
                'estado' => 'inactivo',
            ],
        ];

        foreach ($empleados as $emp) {
            Empleado::firstOrCreate(
                ['cedula' => $emp['cedula']],
                $emp
            );
        }
    }
}
