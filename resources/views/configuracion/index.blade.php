@extends('layouts.app')

@section('title', 'Configuración General')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Por favor corrige los siguientes errores:</strong>
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

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 font-weight-bold text-primary">
                    <i class="fas fa-cogs mr-2"></i> Configuración del Gimnasio
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('configuracion.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Nombre del Gimnasio -->
                    <div class="form-group mb-4">
                        <label for="nombre_gimnasio" class="font-weight-bold">Nombre del Gimnasio <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-dumbbell"></i></span>
                            </div>
                            <input type="text" 
                                   name="nombre_gimnasio" 
                                   id="nombre_gimnasio" 
                                   class="form-control @error('nombre_gimnasio') is-invalid @enderror" 
                                   value="{{ old('nombre_gimnasio', $configuracion->nombre_gimnasio) }}" 
                                   required 
                                   placeholder="Ej: EcoGim Fitness">
                        </div>
                        <small class="form-text text-muted">Este nombre se mostrará en el encabezado, menú superior y pantalla de inicio de sesión.</small>
                    </div>

                    <!-- Logo del Gimnasio -->
                    <div class="form-group mb-4">
                        <label for="logo" class="font-weight-bold">Logo del Gimnasio</label>
                        
                        <div class="row align-items-center mb-3">
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                <div class="p-3 border rounded bg-light d-flex flex-column align-items-center justify-content-center" style="min-height: 140px;">
                                    <span class="text-muted small mb-2">Vista previa actual</span>
                                    @if($configuracion->logo_path)
                                        <img id="logoPreview" src="{{ asset('storage/' . $configuracion->logo_path) }}" alt="Logo" class="img-fluid rounded" style="max-height: 90px; object-fit: contain;">
                                    @else
                                        <div id="defaultLogoBadge" class="p-3 bg-dark text-white rounded font-weight-bold d-flex align-items-center">
                                            <i class="fas fa-dumbbell mr-2"></i> {{ $configuracion->nombre_gimnasio }}
                                        </div>
                                        <img id="logoPreview" src="#" alt="Vista previa" class="img-fluid rounded d-none" style="max-height: 90px; object-fit: contain;">
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="custom-file mb-2">
                                    <input type="file" 
                                           class="custom-file-input @error('logo') is-invalid @enderror" 
                                           id="logo" 
                                           name="logo" 
                                           accept="image/*"
                                           onchange="previewImage(this)">
                                    <label class="custom-file-label" for="logo" data-browse="Buscar">Seleccionar imagen...</label>
                                </div>
                                <small class="form-text text-muted mb-2">
                                    Formatos permitidos: PNG, JPG, JPEG, SVG, WEBP, GIF. Tamaño máximo: 2 MB.
                                </small>

                                @if($configuracion->logo_path)
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input type="checkbox" class="custom-control-input" id="eliminar_logo" name="eliminar_logo" value="1">
                                        <label class="custom-control-label text-danger" for="eliminar_logo">
                                            <i class="fas fa-trash-alt mr-1"></i> Eliminar logo actual y usar texto por defecto
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

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
                    <hr class="my-4">

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold">
                            <i class="fas fa-save mr-2"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        var file = input.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var preview = document.getElementById('logoPreview');
                var defaultBadge = document.getElementById('defaultLogoBadge');
                
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                if (defaultBadge) {
                    defaultBadge.classList.add('d-none');
                }
            }
            reader.readAsDataURL(file);
            
            // Actualizar etiqueta del archivo en Bootstrap
            var label = input.nextElementSibling;
            if (label && label.classList.contains('custom-file-label')) {
                label.textContent = file.name;
            }
        }
    }
</script>
@endpush
