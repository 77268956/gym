@extends('layouts.app')

@section('title', 'Configurar Página Web')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 text-gray-800 mb-0">Configuración de Página Web (Landing Page)</h2>
        <a href="{{ route('home') }}" target="_blank" class="btn btn-primary ic-action-btn-primary">
            <i class="fas fa-external-link-alt mr-2"></i> Ver Página
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="ic-card">
        <form action="{{ route('admin.landing.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            @php
                $currentPrimaryColor = old('primary_color', $config->primary_color ?? '#2563EB');
                $currentSecondaryColor = old('secondary_color', $config->secondary_color ?? '#111214');
            @endphp
            <h5 class="font-weight-bold text-primary mb-3">Paleta de colores</h5>
            <p class="text-muted small">Elige una paleta prediseñada o selecciona “Personalizada” para definir tus propios colores.</p>
            <div class="form-group mb-4">
                <label for="color-palette">Paleta prediseñada</label>
                <select id="color-palette" class="form-control">
                    <option value="custom">Personalizada</option>
                    <option value="blue" data-primary="#2563EB" data-secondary="#111214">Azul GymX</option>
                    <option value="emerald" data-primary="#059669" data-secondary="#064E3B">Verde Esmeralda</option>
                    <option value="orange" data-primary="#EA580C" data-secondary="#431407">Naranja Energía</option>
                    <option value="purple" data-primary="#7C3AED" data-secondary="#2E1065">Morado Pro</option>
                    <option value="red" data-primary="#DC2626" data-secondary="#450A0A">Rojo Intensidad</option>
                    <option value="gold" data-primary="#D97706" data-secondary="#292524">Dorado Premium</option>
                </select>
                <div class="palette-preview mt-2">
                    <span class="palette-preview-swatch" id="primary-color-preview"></span>
                    <span class="palette-preview-swatch" id="secondary-color-preview"></span>
                    <span class="small text-muted">Vista previa de la combinación seleccionada</span>
                </div>
            </div>

            <h5 class="font-weight-bold text-primary mb-3">Sección Principal (Hero)</h5>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Color Principal de la Landing</label>
                    <input type="color" id="primary-color" name="primary_color" class="form-control" value="{{ $currentPrimaryColor }}" required style="height: 40px;">
                </div>
                <div class="col-md-6 form-group">
                    <label>Color Secundario (secciones alternadas)</label>
                    <input type="color" id="secondary-color" name="secondary_color" class="form-control" value="{{ $currentSecondaryColor }}" required style="height: 40px;">
                </div>
            </div>

            <hr>

            <h5 class="font-weight-bold text-primary mb-3 mt-4">Carrusel del Hero</h5>
            <p class="text-muted small">Agrega las diapositivas que aparecerán en el Hero. La primera diapositiva define el título y subtítulo principal de la página.</p>
            <div id="hero-slides-container">
                @foreach(($config->hero_slides ?? []) as $slide)
                    <div class="content-config-row border rounded p-3 mb-3">
                        <div class="row align-items-end">
                            <div class="col-md-4 form-group mb-md-0"><label>Título</label><input type="text" name="hero_slides_title[]" class="form-control" value="{{ $slide['title'] ?? '' }}" maxlength="255"></div>
                            <div class="col-md-4 form-group mb-md-0"><label>Subtítulo</label><input type="text" name="hero_slides_subtitle[]" class="form-control" value="{{ $slide['subtitle'] ?? '' }}" maxlength="255"></div>
                            <div class="col-md-3 form-group mb-md-0"><span class="d-block mb-1">Imagen</span><label class="image-upload-label"><i class="fas fa-cloud-upload-alt"></i><span>Subir imagen</span><input type="file" name="hero_slides_image[]" class="hero-image-input" accept="image/*"></label><input type="hidden" name="hero_slides_existing_image[]" value="{{ $slide['image'] ?? '' }}">@if(!empty($slide['image']))<img src="{{ asset('storage/'.$slide['image']) }}" alt="Vista previa del Hero" class="hero-image-preview mt-2">@else<img src="" alt="Vista previa del Hero" class="hero-image-preview mt-2 d-none">@endif</div>
                            <div class="col-md-1 form-group mb-md-0"><button type="button" class="btn btn-outline-danger remove-content-row"><i class="fas fa-trash"></i></button></div>
                        </div>
                    </div>
                @endforeach
                @if(empty($config->hero_slides))
                    <div class="content-config-row border rounded p-3 mb-3">
                        <div class="row align-items-end">
                            <div class="col-md-4 form-group mb-md-0"><label>Título</label><input type="text" name="hero_slides_title[]" class="form-control" maxlength="255"></div>
                            <div class="col-md-4 form-group mb-md-0"><label>Subtítulo</label><input type="text" name="hero_slides_subtitle[]" class="form-control" maxlength="255"></div>
                            <div class="col-md-3 form-group mb-md-0"><span class="d-block mb-1">Imagen</span><label class="image-upload-label"><i class="fas fa-cloud-upload-alt"></i><span>Subir imagen</span><input type="file" name="hero_slides_image[]" class="hero-image-input" accept="image/*"></label><input type="hidden" name="hero_slides_existing_image[]" value=""><img src="" alt="Vista previa del Hero" class="hero-image-preview mt-2 d-none"></div>
                            <div class="col-md-1 form-group mb-md-0"><button type="button" class="btn btn-outline-danger remove-content-row"><i class="fas fa-trash"></i></button></div>
                        </div>
                    </div>
                @endif
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm add-content-row" data-target="#hero-slides-container" data-template="hero-slide-template"><i class="fas fa-plus mr-1"></i> Agregar diapositiva</button>
            <template id="hero-slide-template"><div class="content-config-row border rounded p-3 mb-3"><div class="row align-items-end"><div class="col-md-4 form-group mb-md-0"><label>Título</label><input type="text" name="hero_slides_title[]" class="form-control" maxlength="255"></div><div class="col-md-4 form-group mb-md-0"><label>Subtítulo</label><input type="text" name="hero_slides_subtitle[]" class="form-control" maxlength="255"></div><div class="col-md-3 form-group mb-md-0"><span class="d-block mb-1">Imagen</span><label class="image-upload-label"><i class="fas fa-cloud-upload-alt"></i><span>Subir imagen</span><input type="file" name="hero_slides_image[]" class="hero-image-input" accept="image/*"></label><input type="hidden" name="hero_slides_existing_image[]" value=""><img src="" alt="Vista previa del Hero" class="hero-image-preview mt-2 d-none"></div><div class="col-md-1 form-group mb-md-0"><button type="button" class="btn btn-outline-danger remove-content-row"><i class="fas fa-trash"></i></button></div></div></div></template>

            <h5 class="font-weight-bold text-primary mb-3 mt-4">Sección Nosotros</h5>
            <div class="row">
                <div class="col-md-12 form-group">
                    <label>Texto descriptivo</label>
                    <textarea name="about_text" class="form-control" rows="4">{{ old('about_text', $config->about_text) }}</textarea>
                </div>
                <div class="col-md-6 form-group">
                    <label>Imagen Nosotros</label>
                    <label class="image-upload-label"><i class="fas fa-cloud-upload-alt"></i><span>Subir imagen</span><input type="file" name="about_image" class="content-image-input" accept="image/*"></label>
                    @if($config->about_image)
                        <img src="{{ asset('storage/'.$config->about_image) }}" alt="Vista previa de Nosotros" class="content-image-preview mt-2">
                    @else
                        <img src="" alt="Vista previa de Nosotros" class="content-image-preview mt-2 d-none">
                    @endif
                </div>
            </div>

            <hr>

            <h5 class="font-weight-bold text-primary mb-3 mt-4">Servicios</h5>
            <p class="text-muted small">Agrega los servicios que ofrece el gimnasio. Los planes de membresía se administran por separado.</p>
            <div id="services-container">
                @php($savedServices = $config->services ?? [])
                @php($services = old('services_title') ? collect(old('services_title'))->map(function ($title, $index) use ($savedServices) {
                    return [
                        'title' => $title,
                        'desc' => old('services_desc.'.$index, $savedServices[$index]['desc'] ?? ''),
                        'icon' => old('services_icon.'.$index, $savedServices[$index]['icon'] ?? 'fas fa-check'),
                    ];
                })->all() : $savedServices)
                @forelse($services as $index => $service)
                    <div class="service-config-row border rounded p-3 mb-3">
                        <div class="row align-items-end">
                            <div class="col-md-3 form-group mb-md-0">
                                <label>Título</label>
                                <input type="text" name="services_title[]" class="form-control" value="{{ $service['title'] ?? '' }}" maxlength="100">
                            </div>
                            <div class="col-md-5 form-group mb-md-0">
                                <label>Descripción</label>
                                <input type="text" name="services_desc[]" class="form-control" value="{{ $service['desc'] ?? '' }}" maxlength="500">
                            </div>
                            <div class="col-md-3 form-group mb-md-0">
                                <label>Ícono Font Awesome</label>
                                <input type="hidden" name="services_icon[]" value="{{ $service['icon'] ?? 'fas fa-check' }}">
                                <button type="button" class="btn btn-outline-primary icon-picker w-100" data-toggle="modal" data-target="#iconPickerModal"><i class="{{ $service['icon'] ?? 'fas fa-check' }} icon-preview"></i> Elegir ícono</button>
                            </div>
                            <div class="col-md-1 form-group mb-md-0 text-md-right">
                                <button type="button" class="btn btn-outline-danger remove-service" title="Eliminar servicio"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="service-config-row border rounded p-3 mb-3">
                        <div class="row align-items-end">
                            <div class="col-md-3 form-group mb-md-0">
                                <label>Título</label>
                                <input type="text" name="services_title[]" class="form-control" maxlength="100">
                            </div>
                            <div class="col-md-5 form-group mb-md-0">
                                <label>Descripción</label>
                                <input type="text" name="services_desc[]" class="form-control" maxlength="500">
                            </div>
                            <div class="col-md-3 form-group mb-md-0">
                                <label>Ícono Font Awesome</label>
                                <input type="hidden" name="services_icon[]" value="fas fa-check">
                                <button type="button" class="btn btn-outline-primary icon-picker w-100" data-toggle="modal" data-target="#iconPickerModal"><i class="fas fa-check icon-preview"></i> Elegir ícono</button>
                            </div>
                            <div class="col-md-1 form-group mb-md-0 text-md-right">
                                <button type="button" class="btn btn-outline-danger remove-service" title="Eliminar servicio"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
            <button type="button" id="add-service" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Agregar servicio
            </button>

            @php($contentSections = [
                'equipment' => ['label' => 'Equipos', 'title' => 'equipment_title', 'desc' => 'equipment_desc', 'icon' => 'equipment_icon', 'image' => 'equipment_image', 'existing' => 'equipment_existing_image'],
                'trainers' => ['label' => 'Entrenadores', 'title' => 'trainers_name', 'desc' => 'trainers_bio', 'icon' => 'trainers_role', 'image' => 'trainers_image', 'existing' => 'trainers_existing_image'],
                'facilities' => ['label' => 'Instalaciones', 'title' => 'facilities_title', 'desc' => 'facilities_desc', 'icon' => null, 'image' => 'facilities_image', 'existing' => 'facilities_existing_image'],
            ])
            @foreach($contentSections as $sectionKey => $section)
                <hr>
                <h5 class="font-weight-bold text-primary mb-3 mt-4">{{ $section['label'] }}</h5>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Título de la sección</label>
                        <input type="text" name="{{ $sectionKey }}_heading" class="form-control" value="{{ old($sectionKey.'_heading', $config->{$sectionKey.'_heading'}) }}" placeholder="{{ $section['label'] }}" maxlength="100">
                    </div>
                    <div class="col-md-8 form-group">
                        <label>Texto introductorio</label>
                        <textarea name="{{ $sectionKey }}_intro" class="form-control" rows="2" maxlength="1000" placeholder="Describe esta sección para tus visitantes">{{ old($sectionKey.'_intro', $config->{$sectionKey.'_intro'}) }}</textarea>
                    </div>
                </div>
                <div id="{{ $sectionKey }}-container">
                    @foreach(($config->{$sectionKey} ?? []) as $item)
                        <div class="content-config-row border rounded p-3 mb-3">
                            <div class="row align-items-end">
                                <div class="col-md-{{ $section['icon'] ? 3 : 4 }} form-group mb-md-0"><label>{{ $sectionKey === 'trainers' ? 'Nombre' : 'Título' }}</label><input type="text" name="{{ $section['title'] }}[]" class="form-control" value="{{ $item['name'] ?? $item['title'] ?? '' }}" maxlength="100"></div>
                                @if($section['icon'])<div class="col-md-{{ $sectionKey === 'trainers' ? 3 : 2 }} form-group mb-md-0"><label>{{ $sectionKey === 'trainers' ? 'Especialidad' : 'Ícono Font Awesome' }}</label>@if($sectionKey === 'trainers')<input type="text" name="{{ $section['icon'] }}[]" class="form-control" value="{{ $item['role'] ?? '' }}" maxlength="100">@else<input type="hidden" name="{{ $section['icon'] }}[]" value="{{ $item['icon'] ?? 'fas fa-dumbbell' }}"><button type="button" class="btn btn-outline-primary icon-picker w-100" data-toggle="modal" data-target="#iconPickerModal"><i class="{{ $item['icon'] ?? 'fas fa-dumbbell' }} icon-preview"></i> Elegir ícono</button>@endif</div>@endif
                                <div class="col-md-{{ $section['icon'] ? 3 : 4 }} form-group mb-md-0"><label>{{ $sectionKey === 'trainers' ? 'Biografía' : 'Descripción' }}</label><input type="text" name="{{ $section['desc'] }}[]" class="form-control" value="{{ $item[$sectionKey === 'trainers' ? 'bio' : 'desc'] ?? '' }}" maxlength="500"></div>
                                <div class="col-md-2 form-group mb-md-0"><span class="d-block mb-1">Imagen</span><label class="image-upload-label"><i class="fas fa-cloud-upload-alt"></i><span>Subir imagen</span><input type="file" name="{{ $section['image'] }}[]" class="content-image-input" accept="image/*"></label><input type="hidden" name="{{ $section['existing'] }}[]" value="{{ $item['image'] ?? '' }}">@if(!empty($item['image']))<img src="{{ asset('storage/'.$item['image']) }}" alt="Vista previa de {{ $section['label'] }}" class="content-image-preview mt-2">@else<img src="" alt="Vista previa de {{ $section['label'] }}" class="content-image-preview mt-2 d-none">@endif</div>
                                <div class="col-md-1 form-group mb-md-0"><button type="button" class="btn btn-outline-danger remove-content-row"><i class="fas fa-trash"></i></button></div>
                            </div>
                        </div>
                    @endforeach
                    @if(empty($config->{$sectionKey}))
                        <div class="content-config-row border rounded p-3 mb-3">
                            <div class="row align-items-end">
                                <div class="col-md-{{ $section['icon'] ? 3 : 4 }} form-group mb-md-0"><label>{{ $sectionKey === 'trainers' ? 'Nombre' : 'Título' }}</label><input type="text" name="{{ $section['title'] }}[]" class="form-control" maxlength="100"></div>
                                @if($section['icon'])<div class="col-md-{{ $sectionKey === 'trainers' ? 3 : 2 }} form-group mb-md-0"><label>{{ $sectionKey === 'trainers' ? 'Especialidad' : 'Ícono Font Awesome' }}</label>@if($sectionKey === 'trainers')<input type="text" name="{{ $section['icon'] }}[]" class="form-control" maxlength="100">@else<input type="hidden" name="{{ $section['icon'] }}[]" value="fas fa-dumbbell"><button type="button" class="btn btn-outline-primary icon-picker w-100" data-toggle="modal" data-target="#iconPickerModal"><i class="fas fa-dumbbell icon-preview"></i> Elegir ícono</button>@endif</div>@endif
                                <div class="col-md-{{ $section['icon'] ? 3 : 4 }} form-group mb-md-0"><label>{{ $sectionKey === 'trainers' ? 'Biografía' : 'Descripción' }}</label><input type="text" name="{{ $section['desc'] }}[]" class="form-control" maxlength="500"></div>
                                <div class="col-md-2 form-group mb-md-0"><span class="d-block mb-1">Imagen</span><label class="image-upload-label"><i class="fas fa-cloud-upload-alt"></i><span>Subir imagen</span><input type="file" name="{{ $section['image'] }}[]" class="content-image-input" accept="image/*"></label><input type="hidden" name="{{ $section['existing'] }}[]" value=""><img src="" alt="Vista previa de {{ $section['label'] }}" class="content-image-preview mt-2 d-none"></div>
                                <div class="col-md-1 form-group mb-md-0"><button type="button" class="btn btn-outline-danger remove-content-row"><i class="fas fa-trash"></i></button></div>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm add-content-row" data-target="#{{ $sectionKey }}-container" data-template="{{ $sectionKey }}-template"><i class="fas fa-plus mr-1"></i> Agregar {{ strtolower($section['label']) }}</button>
                <template id="{{ $sectionKey }}-template"><div class="content-config-row border rounded p-3 mb-3"><div class="row align-items-end"><div class="col-md-{{ $section['icon'] ? 3 : 4 }} form-group mb-md-0"><label>{{ $sectionKey === 'trainers' ? 'Nombre' : 'Título' }}</label><input type="text" name="{{ $section['title'] }}[]" class="form-control" maxlength="100"></div>@if($section['icon'])<div class="col-md-{{ $sectionKey === 'trainers' ? 3 : 2 }} form-group mb-md-0"><label>{{ $sectionKey === 'trainers' ? 'Especialidad' : 'Ícono Font Awesome' }}</label>@if($sectionKey === 'trainers')<input type="text" name="{{ $section['icon'] }}[]" class="form-control" maxlength="100">@else<input type="hidden" name="{{ $section['icon'] }}[]" value="fas fa-dumbbell"><button type="button" class="btn btn-outline-primary icon-picker w-100" data-toggle="modal" data-target="#iconPickerModal"><i class="fas fa-dumbbell icon-preview"></i> Elegir ícono</button>@endif</div>@endif<div class="col-md-{{ $section['icon'] ? 3 : 4 }} form-group mb-md-0"><label>{{ $sectionKey === 'trainers' ? 'Biografía' : 'Descripción' }}</label><input type="text" name="{{ $section['desc'] }}[]" class="form-control" maxlength="500"></div><div class="col-md-2 form-group mb-md-0"><span class="d-block mb-1">Imagen</span><label class="image-upload-label"><i class="fas fa-cloud-upload-alt"></i><span>Subir imagen</span><input type="file" name="{{ $section['image'] }}[]" class="content-image-input" accept="image/*"></label><input type="hidden" name="{{ $section['existing'] }}[]" value=""><img src="" alt="Vista previa de {{ $section['label'] }}" class="content-image-preview mt-2 d-none"></div><div class="col-md-1 form-group mb-md-0"><button type="button" class="btn btn-outline-danger remove-content-row"><i class="fas fa-trash"></i></button></div></div></div></template>
            @endforeach



            <hr>

            <h5 class="font-weight-bold text-primary mb-3 mt-4">Contacto</h5>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>Teléfono</label>
                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $config->contact_phone) }}">
                </div>
                <div class="col-md-4 form-group">
                    <label>Email</label>
                    <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $config->contact_email) }}">
                </div>
                <div class="col-md-4 form-group">
                    <label>Dirección</label>
                    <input type="text" name="contact_address" class="form-control" value="{{ old('contact_address', $config->contact_address) }}">
                </div>
                <div class="col-md-4 form-group">
                    <label>Facebook URL</label>
                    <input type="url" name="contact_facebook" class="form-control" value="{{ old('contact_facebook', $config->contact_facebook) }}">
                </div>
                <div class="col-md-4 form-group">
                    <label>Instagram URL</label>
                    <input type="url" name="contact_instagram" class="form-control" value="{{ old('contact_instagram', $config->contact_instagram) }}">
                </div>
                <div class="col-md-4 form-group">
                    <label>WhatsApp (solo números)</label>
                    <input type="text" name="contact_whatsapp" class="form-control" value="{{ old('contact_whatsapp', $config->contact_whatsapp) }}">
                </div>
            </div>

            <div class="text-right mt-4">
                <button type="submit" class="btn btn-success ic-action-btn-primary px-4 py-2" style="font-size: 1.1rem;">
                    <i class="fas fa-save mr-2"></i> Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="iconPickerModal" tabindex="-1" role="dialog" aria-labelledby="iconPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content icon-picker-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="iconPickerModalLabel">Selecciona un ícono</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="selected-icon-preview">
                    <span>Ícono seleccionado</span>
                    <i id="selectedIconPreview" class="fas fa-check"></i>
                </div>
                <div class="icon-picker-grid">
                    @foreach(['fas fa-dumbbell', 'fas fa-weight-hanging', 'fas fa-heartbeat', 'fas fa-running', 'fas fa-person-running', 'fas fa-person-walking', 'fas fa-biking', 'fas fa-person-swimming', 'fas fa-fire', 'fas fa-bolt', 'fas fa-trophy', 'fas fa-medal', 'fas fa-star', 'fas fa-crown', 'fas fa-gem', 'fas fa-users', 'fas fa-user', 'fas fa-user-check', 'fas fa-user-group', 'fas fa-people-group', 'fas fa-check', 'fas fa-check-circle', 'fas fa-circle-check', 'fas fa-clock', 'fas fa-calendar-check', 'fas fa-stopwatch', 'fas fa-shield-halved', 'fas fa-lock', 'fas fa-award', 'fas fa-flag-checkered', 'fas fa-bullseye', 'fas fa-crosshairs', 'fas fa-chart-line', 'fas fa-chart-simple', 'fas fa-arrow-trend-up', 'fas fa-apple-whole', 'fas fa-lemon', 'fas fa-carrot', 'fas fa-bottle-water', 'fas fa-glass-water', 'fas fa-droplet', 'fas fa-seedling', 'fas fa-leaf', 'fas fa-sun', 'fas fa-moon', 'fas fa-music', 'fas fa-headphones', 'fas fa-hand-fist', 'fas fa-shoe-prints', 'fas fa-shirt', 'fas fa-tag'] as $icon)
                        <button type="button" class="icon-option" data-icon="{{ $icon }}" title="{{ $icon }}"><i class="{{ $icon }}"></i></button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.ic-card { background: #fff; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.04); }
.icon-picker-modal { border: 0; border-radius: 12px; }
.selected-icon-preview { display: flex; align-items: center; justify-content: center; gap: .75rem; min-height: 72px; margin-bottom: 1rem; border: 2px solid #4e73df; border-radius: 10px; background: #eef2ff; color: #334155; font-size: .85rem; font-weight: 600; }
.selected-icon-preview i { color: #224abe; font-size: 2rem; }
.icon-picker-grid { display: grid; max-height: 360px; overflow-y: auto; grid-template-columns: repeat(7, 1fr); gap: .65rem; padding: .15rem; }
.icon-option { display: grid; width: 100%; min-height: 58px; place-items: center; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; color: #4e73df; font-size: 1.35rem; transition: .2s ease; }
.icon-option:hover, .icon-option:focus { border-color: #4e73df; background: #eef2ff; color: #224abe; transform: translateY(-2px); }
.icon-option.is-selected { border-color: #224abe; background: #dbeafe; box-shadow: 0 0 0 2px rgba(34,74,190,.18); color: #224abe; }
.icon-picker { min-height: 38px; }
.hero-image-preview { display: block; width: 100%; height: 78px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0; }
.content-image-preview { display: block; width: 100%; height: 78px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0; }
.image-upload-label { display: flex; align-items: center; justify-content: center; gap: .5rem; width: 100%; min-height: 40px; margin: 0; padding: .55rem .75rem; border: 1px dashed #4e73df; border-radius: 8px; background: #f8faff; color: #224abe; cursor: pointer; font-size: .82rem; font-weight: 700; transition: .2s ease; }
.image-upload-label:hover, .image-upload-label:focus-within { border-color: #224abe; background: #eef2ff; box-shadow: 0 0 0 3px rgba(78,115,223,.12); }
.image-upload-label i { font-size: 1.05rem; }
.image-upload-label input[type="file"] { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; clip-path: inset(50%); }
.palette-preview { display: flex; align-items: center; gap: .5rem; }
.palette-preview-swatch { display: inline-block; width: 28px; height: 28px; border: 1px solid #d1d5db; border-radius: 50%; }
@media (max-width: 767.98px) { .icon-picker-grid { grid-template-columns: repeat(5, 1fr); } }
@media (max-width: 575.98px) { .icon-picker-grid { grid-template-columns: repeat(4, 1fr); } }
</style>
@endpush

@push('scripts')
<script>
    $(function () {
        const paletteSelect = $('#color-palette');
        const primaryColor = $('#primary-color');
        const secondaryColor = $('#secondary-color');
        const primaryPreview = $('#primary-color-preview');
        const secondaryPreview = $('#secondary-color-preview');

        function updatePalettePreview() {
            primaryPreview.css('background-color', primaryColor.val());
            secondaryPreview.css('background-color', secondaryColor.val());
        }

        function selectMatchingPalette() {
            const matchingOption = paletteSelect.find('option').filter(function () {
                return $(this).data('primary') === primaryColor.val().toUpperCase()
                    && $(this).data('secondary') === secondaryColor.val().toUpperCase();
            }).first();

            paletteSelect.val(matchingOption.length ? matchingOption.val() : 'custom');
        }

        paletteSelect.on('change', function () {
            const option = $(this).find(':selected');
            const nextPrimary = option.data('primary');
            const nextSecondary = option.data('secondary');

            if (!nextPrimary || !nextSecondary) {
                updatePalettePreview();
                return;
            }

            primaryColor.val(nextPrimary);
            secondaryColor.val(nextSecondary);
            updatePalettePreview();
        });

        primaryColor.add(secondaryColor).on('input change', function () {
            selectMatchingPalette();
            updatePalettePreview();
        });

        selectMatchingPalette();
        updatePalettePreview();

        $('#add-service').on('click', function () {
            const row = $('.service-config-row').first().clone();
            row.find('input').val('');
            row.find('input[name="services_icon[]"]').val('fas fa-check');
            row.find('.icon-preview').attr('class', 'fas fa-check icon-preview');
            $('#services-container').append(row);
        });

        $(document).on('click', '.remove-service', function () {
            if ($('.service-config-row').length > 1) {
                $(this).closest('.service-config-row').remove();
            } else {
                $(this).closest('.service-config-row').find('input').val('');
                $(this).closest('.service-config-row').find('input[name="services_icon[]"]').val('fas fa-check');
                $(this).closest('.service-config-row').find('.icon-preview').attr('class', 'fas fa-check icon-preview');
            }
        });

        $(document).on('click', '.add-content-row', function () {
            const template = document.getElementById($(this).data('template'));
            $($(this).data('target')).append($(template.content.cloneNode(true)));
        });

        $(document).on('change', '.hero-image-input, .content-image-input', function () {
            const preview = $(this).closest('.form-group').find('.hero-image-preview, .content-image-preview').first();
            const file = this.files[0];

            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                preview.attr('src', event.target.result).removeClass('d-none');
            };
            reader.readAsDataURL(file);
        });

        $(document).on('click', '.remove-content-row', function () {
            const container = $(this).closest('[id$="-container"]');
            if (container.find('.content-config-row').length > 1) {
                $(this).closest('.content-config-row').remove();
                return;
            }

            $(this).closest('.content-config-row').find('input').not('[type="file"]').val('');
            $(this).closest('.content-config-row').find('input[type="file"]').val('');
        });

        let activeIconPicker = null;

        $(document).on('click', '.icon-picker', function () {
            activeIconPicker = $(this);
            const selectedIcon = activeIconPicker.closest('.form-group').find('input[type="hidden"]').val() || 'fas fa-check';
            $('#selectedIconPreview').attr('class', selectedIcon);
            $('.icon-option').removeClass('is-selected').filter('[data-icon="' + selectedIcon + '"]').addClass('is-selected');
        });

        $(document).on('click', '.icon-option', function () {
            if (!activeIconPicker) {
                return;
            }

            const icon = $(this).data('icon');
            activeIconPicker.closest('.form-group').find('input[type="hidden"]').val(icon);
            activeIconPicker.find('.icon-preview').attr('class', icon + ' icon-preview');
            $('#selectedIconPreview').attr('class', icon);
            $('.icon-option').removeClass('is-selected');
            $(this).addClass('is-selected');
            $('#iconPickerModal').modal('hide');
        });
    });
</script>
@endpush
