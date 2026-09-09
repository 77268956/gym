<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\TipoMembresiaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/user', [UserController::class, 'userget'])->name('user');

    // Módulo de Clientes
    Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    Route::patch('/clientes/{cliente}/toggle', [ClienteController::class, 'toggleStatus'])->name('clientes.toggleStatus');

    // Módulo de Empleados
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados');
    Route::get('/empleados/crear', [EmpleadoController::class, 'create'])->name('empleados.create');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
    Route::get('/empleados/{empleado}/editar', [EmpleadoController::class, 'edit'])->name('empleados.edit');
    Route::put('/empleados/{empleado}', [EmpleadoController::class, 'update'])->name('empleados.update');
    Route::delete('/empleados/{empleado}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
    Route::patch('/empleados/{empleado}/toggle', [EmpleadoController::class, 'toggleStatus'])->name('empleados.toggleStatus');

    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');

    // Módulo Tipos de Membresía / Planes y Servicios
    Route::get('/membresias', [TipoMembresiaController::class, 'index'])->name('membresias.index');
    Route::post('/membresias', [TipoMembresiaController::class, 'store'])->name('membresias.store');
    Route::put('/membresias/{tipoMembresia}', [TipoMembresiaController::class, 'update'])->name('membresias.update');
    Route::delete('/membresias/{tipoMembresia}', [TipoMembresiaController::class, 'destroy'])->name('membresias.destroy');
    Route::patch('/membresias/{tipoMembresia}/toggle', [TipoMembresiaController::class, 'toggleStatus'])->name('membresias.toggleStatus');
});
