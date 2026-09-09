@extends('layouts.app')

@section('title', 'Nuevo Empleado / Recepcionista')

@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('empleados') }}"><i class="fas fa-user-tie mr-1"></i> Empleados</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nuevo Empleado</li>
                </ol>
            </nav>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Por favor verifica los datos ingresados:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Card del Formulario --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 font-weight-bold text-primary">
                        <i class="fas fa-user-plus mr-2"></i> Registrar Nuevo Empleado o Recepcionista
                    </h5>
                    <a href="{{ route('empleados') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a la Lista
                    </a>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('empleados.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- SECCIÓN 1: DATOS PERSONALES Y DE ACCESO -->
                        <h6 class="text-uppercase text-muted font-weight-bold mb-3 border-bottom pb-2" style="letter-spacing: .05em; font-size: .75rem;">
                            1. Información Personal y Credenciales de Acceso
                        </h6>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="nombre" class="font-weight-bold small text-dark">Nombre Completo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej: Amanda Waller" required>
                                </div>
                            </div>

                            <div class="col-md-6 form-group mb-3">
                                <label for="cedula" class="font-weight-bold small text-dark">Cédula / Identificación <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    </div>
                                    <input type="text" name="cedula" id="cedula" class="form-control @error('cedula') is-invalid @enderror" value="{{ old('cedula') }}" placeholder="Ej: 0801-1992-45678" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="usuario" class="font-weight-bold small text-dark">Usuario de Acceso al Sistema <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                    </div>
                                    <input type="text" name="usuario" id="usuario" class="form-control @error('usuario') is-invalid @enderror" value="{{ old('usuario') }}" placeholder="Ej: awaller" required>
                                </div>
                            </div>

                            <div class="col-md-6 form-group mb-3">
                                <label for="password" class="font-weight-bold small text-dark">Contraseña Inicial <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 6 caracteres" required>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 2: ROL Y TURNO -->
                        <h6 class="text-uppercase text-muted font-weight-bold mt-4 mb-3 border-bottom pb-2" style="letter-spacing: .05em; font-size: .75rem;">
                            2. Rol en el Gimnasio y Horario de Turno
                        </h6>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="rol" class="font-weight-bold small text-dark">Rol en el Sistema <span class="text-danger">*</span></label>
                                <select name="rol" id="rol" class="form-control @error('rol') is-invalid @enderror" required>
                                    <option value="empleado" {{ old('rol') === 'empleado' ? 'selected' : '' }}>Recepcionista / Empleado</option>
                                    <option value="admin" {{ old('rol') === 'admin' ? 'selected' : '' }}>Administrador General</option>
                                </select>
                            </div>

                            <div class="col-md-6 form-group mb-3">
                                <label for="estado" class="font-weight-bold small text-dark">Estado del Empleado <span class="text-danger">*</span></label>
                                <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" required>
                                    <option value="activo" {{ old('estado', 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label for="hora_entrada_turno" class="font-weight-bold small text-dark">Hora de Entrada (Turno)</label>
                                <input type="time" name="hora_entrada_turno" id="hora_entrada_turno" class="form-control" value="{{ old('hora_entrada_turno', '08:00') }}">
                            </div>

                            <div class="col-md-4 form-group mb-3">
                                <label for="hora_salida_turno" class="font-weight-bold small text-dark">Hora de Salida (Turno)</label>
                                <input type="time" name="hora_salida_turno" id="hora_salida_turno" class="form-control" value="{{ old('hora_salida_turno', '16:00') }}">
                            </div>

                            <div class="col-md-4 form-group mb-3">
                                <label for="tolerancia_minutos" class="font-weight-bold small text-dark">Tolerancia (Minutos)</label>
                                <div class="input-group">
                                    <input type="number" name="tolerancia_minutos" id="tolerancia_minutos" class="form-control" value="{{ old('tolerancia_minutos', 10) }}" min="0" max="120">
                                    <div class="input-group-append">
                                        <span class="input-group-text small">min</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 3: FOTO DE REFERENCIA -->
                        <h6 class="text-uppercase text-muted font-weight-bold mt-4 mb-3 border-bottom pb-2" style="letter-spacing: .05em; font-size: .75rem;">
                            3. Fotografía de Referencia (Opcional)
                        </h6>

                        <div class="form-group mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center mb-3 mb-md-0">
                                    <div class="p-3 border rounded bg-light d-flex flex-column align-items-center justify-content-center" style="min-height: 120px;">
                                        <img id="fotoPreview" src="#" alt="Previsualización" class="rounded-circle d-none mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                                        <div id="fotoPlaceholder" class="text-muted text-center">
                                            <i class="fas fa-user-circle fa-3x mb-1"></i>
                                            <small class="d-block">Sin foto</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="custom-file mb-2">
                                        <input type="file" name="foto" id="foto" class="custom-file-input @error('foto') is-invalid @enderror" accept="image/*" onchange="previewFoto(this)">
                                        <label class="custom-file-label" for="foto" data-browse="Buscar">Seleccionar foto de perfil...</label>
                                    </div>
                                    <small class="form-text text-muted">Archivos permitidos: JPG, PNG, WEBP. Tamaño máximo: 2 MB.</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- BOTONES DE ACCIÓN -->
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('empleados') }}" class="btn btn-outline-secondary px-4 py-2 font-weight-bold mr-2">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold">
                                <i class="fas fa-save mr-2"></i> Guardar Empleado
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewFoto(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('fotoPreview');
                var placeholder = document.getElementById('fotoPlaceholder');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(input.files[0]);

            var label = input.nextElementSibling;
            if (label) {
                label.textContent = input.files[0].name;
            }
        }
    }
</script>
@endpush
