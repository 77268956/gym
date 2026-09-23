@extends('layouts.app')

@section('title', 'Configuración General')

@section('skeleton')
    {{-- Centered config card --}}
    <div style="max-width: 85%; margin: 0 auto;">
        <div class="skel-box" style="height: 32px; width: 280px; margin-bottom: 1.5rem;"></div>
        {{-- Section: Identidad --}}
        <div class="skel-box" style="height: 16px; width: 120px; margin-bottom: 0.75rem;"></div>
        <div class="skel-row">
            <div style="flex: 2; display: flex; flex-direction: column; gap: 1rem;">
                <div class="skel-box" style="height: 40px;"></div>
                <div class="skel-box" style="height: 40px;"></div>
            </div>
            <div class="skel-box" style="flex: 1; height: 120px;"></div>
        </div>
        {{-- Section: Moneda --}}
        <div class="skel-box" style="height: 16px; width: 100px; margin-bottom: 0.75rem;"></div>
        <div class="skel-row">
            <div class="skel-box" style="height: 40px; flex: 1;"></div>
            <div class="skel-box" style="height: 40px; flex: 1;"></div>
            <div class="skel-box" style="height: 40px; flex: 1;"></div>
        </div>
        {{-- Section: Apariencia --}}
        <div class="skel-box" style="height: 16px; width: 110px; margin-bottom: 0.75rem;"></div>
        <div class="skel-row">
            <div class="skel-box" style="height: 90px; flex: 1;"></div>
            <div class="skel-box" style="height: 90px; flex: 1;"></div>
        </div>
        <div class="skel-box" style="height: 42px; width: 160px; margin-top: 1rem;"></div>
    </div>
@endsection

@push('styles')
<style>
    :root { --card-color: var(--sidebar-bg); }
    body, html { overflow: hidden; height: 100%; }
    #page-wrapper main { 
        padding: 1rem 1.5rem !important; 
        display: flex; flex-direction: column; 
        height: calc(100vh - 60px); overflow: hidden;
    }
    .page-header { flex-shrink: 0; margin-bottom: 0.75rem !important; }
    .main-container { flex: 1; overflow: hidden; display: flex; flex-direction: column; padding: 0 !important; }

    .config-card {
        background: #fff; border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.04); padding: 1.25rem;
        display: flex; flex-direction: column; height: 100%;
    }
    .config-scroll { flex: 1; min-height: 0; overflow-y: auto; padding-right: 8px; }

    .config-section {
        background: #F8FAFC; border: 1px solid #E2E8F0;
        border-radius: 10px; padding: 1.25rem; margin-bottom: 1rem;
    }
    .config-section-title {
        font-size: 0.8rem; font-weight: 700; text-transform: uppercase;
        color: var(--card-color); margin-bottom: 1rem; letter-spacing: 0.05em;
    }
    .config-section-title i, .config-card-icon { color: var(--card-color); }

    .config-label { font-weight: 600; font-size: 0.85rem; color: #334155; margin-bottom: 0.3rem; }
    .config-hint { font-size: 0.72rem; color: #94A3B8; margin-top: 0.2rem; }

    .logo-preview-box {
        background: linear-gradient(135deg, var(--sidebar-bg), var(--sidebar-hover));
        border-radius: 10px; padding: 1.5rem;
        display: flex; align-items: center; justify-content: center;
        min-height: 120px;
    }
    .color-preview { min-height: 92px; border-radius: 10px; padding: 1rem; color: #fff; display: flex; align-items: flex-end; justify-content: space-between; }
    .color-input { width: 100%; height: 38px; padding: 3px; cursor: pointer; }
</style>
@endpush

@section('content')
<div class="container-fluid main-container">
    <div class="row h-100">
        <div class="col-lg-10 offset-lg-1 h-100 pb-1">
            <div class="config-card">
                
                <div class="d-flex justify-content-between align-items-center mb-3 flex-shrink-0">
                    <h5 class="mb-0 font-weight-bold text-dark" style="font-size:0.95rem;">
                        <i class="fas fa-cogs config-card-icon mr-2"></i> Configuración del Gimnasio
                    </h5>
                </div>

                <div class="config-scroll">
                    <form action="{{ route('configuracion.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- SECCIÓN 1: Identidad --}}
                        <div class="config-section">
                            <div class="config-section-title"><i class="fas fa-store mr-2"></i> Identidad del Gimnasio</div>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group mb-3">
                                        <label for="nombre_gimnasio" class="config-label">Nombre del Gimnasio <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-dumbbell config-card-icon"></i></span></div>
                                            <input type="text" name="nombre_gimnasio" id="nombre_gimnasio"
                                                   class="form-control @error('nombre_gimnasio') is-invalid @enderror"
                                                   value="{{ old('nombre_gimnasio', $configuracion->nombre_gimnasio) }}"
                                                   required placeholder="Ej: Stars Gym">
                                        </div>
                                        <div class="config-hint">Se muestra en el menú, login y encabezados.</div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label class="config-label">Logo</label>
                                        <div class="custom-file mb-1">
                                            <input type="file" class="custom-file-input @error('logo') is-invalid @enderror"
                                                   id="logo" name="logo" accept="image/*" onchange="previewImage(this)">
                                            <label class="custom-file-label" for="logo" data-browse="Buscar">Seleccionar imagen...</label>
                                        </div>
                                        <div class="config-hint">PNG, JPG, WEBP. Máx 2 MB.</div>
                                        @if($configuracion->logo_path)
                                            <div class="custom-control custom-checkbox mt-2">
                                                <input type="checkbox" class="custom-control-input" id="eliminar_logo" name="eliminar_logo" value="1">
                                                <label class="custom-control-label text-danger small" for="eliminar_logo">
                                                    <i class="fas fa-trash-alt mr-1"></i> Eliminar logo actual
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-center justify-content-center">
                                    <div class="logo-preview-box w-100">
                                        @if($configuracion->logo_path)
                                            <img id="logoPreview" src="{{ asset('storage/' . $configuracion->logo_path) }}" alt="Logo" class="img-fluid rounded" style="max-height: 80px; object-fit: contain;">
                                        @else
                                            <div id="defaultLogoBadge" class="text-white font-weight-bold d-flex align-items-center" style="font-size:1.1rem;">
                                                <i class="fas fa-dumbbell mr-2"></i> {{ $configuracion->nombre_gimnasio }}
                                            </div>
                                            <img id="logoPreview" src="#" alt="Preview" class="img-fluid rounded d-none" style="max-height: 80px; object-fit: contain;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: Moneda --}}
                        <div class="config-section">
                            <div class="config-section-title"><i class="fas fa-coins mr-2"></i> Configuración de Moneda</div>
                            <div class="row">
                                <div class="col-md-4 form-group mb-2">
                                    <label class="config-label">Moneda (Nombre)</label>
                                    <input type="text" name="moneda" class="form-control form-control-sm"
                                           value="{{ old('moneda', $configuracion->moneda ?? 'Lempira') }}" placeholder="Lempira">
                                </div>
                                <div class="col-md-4 form-group mb-2">
                                    <label class="config-label">Símbolo</label>
                                    <input type="text" name="simbolo_moneda" class="form-control form-control-sm"
                                           value="{{ old('simbolo_moneda', $configuracion->simbolo_moneda ?? 'L.') }}" placeholder="L.">
                                </div>
                                <div class="col-md-4 form-group mb-2">
                                    <label class="config-label">Código ISO</label>
                                    <input type="text" name="codigo_moneda" class="form-control form-control-sm"
                                           value="{{ old('codigo_moneda', $configuracion->codigo_moneda ?? 'HNL') }}" placeholder="HNL">
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 3: Puntos --}}
                        <div class="config-section">
                            <div class="config-section-title"><i class="fas fa-star mr-2"></i> Sistema de Recompensas</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="puntos_por_visita" class="config-label">Puntos por visita diaria</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-star config-card-icon"></i></span></div>
                                            <input type="number" name="puntos_por_visita" id="puntos_por_visita"
                                                   class="form-control" min="0" max="1000"
                                                   value="{{ old('puntos_por_visita', $configuracionPuntos->puntos_por_visita ?? 10) }}" required>
                                        </div>
                                        <div class="config-hint">
                                            Puntos al escanear rostro. Actual: <strong>{{ $configuracionPuntos->puntos_por_visita ?? 10 }} pts</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 4: Apariencia --}}
                        <div class="config-section">
                            <div class="config-section-title"><i class="fas fa-palette mr-2"></i> Colores del sistema</div>
                            <div class="form-group mb-3">
                                <label for="tema_predefinido" class="config-label">Combinación rápida</label>
                                <select id="tema_predefinido" class="form-control form-control-sm">
                                    <option value="">Elegir una combinación...</option>
                                    <option value="azul">Azul profesional</option>
                                    <option value="verde">Verde energía</option>
                                    <option value="rojo">Rojo intenso</option>
                                    <option value="naranja">Naranja vital</option>
                                </select>
                                <div class="config-hint">Puedes elegir una combinación o ajustar cada color manualmente.</div>
                            </div>
                            <div class="row">
                                @foreach([
                                    ['color_primario', 'Color principal', 'Botones, enlaces y acentos'],
                                    ['color_primario_hover', 'Color al pasar el cursor', 'Estado hover de botones'],
                                    ['color_sidebar', 'Barra lateral y modales', 'Menú lateral y encabezados de modal'],
                                    ['color_sidebar_hover', 'Barra lateral activa', 'Elemento seleccionado del menú'],
                                ] as [$name, $label, $hint])
                                    <div class="col-md-3 form-group mb-2">
                                        <label for="{{ $name }}" class="config-label">{{ $label }}</label>
                                        <input type="color" id="{{ $name }}" name="{{ $name }}" class="form-control color-input"
                                               value="{{ old($name, $configuracion->{$name} ?? '#2563EB') }}">
                                        <div class="config-hint">{{ $hint }}</div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="color-preview mt-2" id="colorPreview">
                                <span><i class="fas fa-eye mr-2"></i> Vista previa</span>
                                <button type="button" class="btn btn-sm text-white font-weight-bold" id="previewButton">Botón principal</button>
                            </div>
                        </div>

                        {{-- Botón Guardar --}}
                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold" style="font-size:0.85rem; border-radius:8px;">
                                <i class="fas fa-save mr-2"></i> Guardar Cambios
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
    function previewImage(input) {
        var file = input.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var preview = document.getElementById('logoPreview');
                var defaultBadge = document.getElementById('defaultLogoBadge');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                if (defaultBadge) defaultBadge.classList.add('d-none');
            }
            reader.readAsDataURL(file);
            var label = input.nextElementSibling;
            if (label && label.classList.contains('custom-file-label')) label.textContent = file.name;
        }
    }

    (function () {
        var themes = {
            azul: ['#2563EB', '#1D4ED8', '#1E293B', '#334155'],
            verde: ['#059669', '#047857', '#064E3B', '#065F46'],
            rojo: ['#DC2626', '#B91C1C', '#450A0A', '#7F1D1D'],
            naranja: ['#EA580C', '#C2410C', '#431407', '#7C2D12']
        };
        var fields = ['color_primario', 'color_primario_hover', 'color_sidebar', 'color_sidebar_hover'];
        var preset = document.getElementById('tema_predefinido');
        var preview = document.getElementById('colorPreview');
        var previewButton = document.getElementById('previewButton');

        function updatePreview() {
            preview.style.background = 'linear-gradient(135deg, ' + document.getElementById('color_sidebar').value + ', ' + document.getElementById('color_sidebar_hover').value + ')';
            previewButton.style.backgroundColor = document.getElementById('color_primario').value;
        }

        preset.addEventListener('change', function () {
            if (!themes[this.value]) return;
            themes[this.value].forEach(function (color, index) {
                document.getElementById(fields[index]).value = color;
            });
            updatePreview();
        });
        fields.forEach(function (field) { document.getElementById(field).addEventListener('input', updatePreview); });
        updatePreview();
    }());
</script>
@endpush