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
            
            <h5 class="font-weight-bold text-primary mb-3">Sección Principal (Hero)</h5>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Título Principal</label>
                    <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title', $config->hero_title) }}" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>Subtítulo</label>
                    <input type="text" name="hero_subtitle" class="form-control" value="{{ old('hero_subtitle', $config->hero_subtitle) }}" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>Imagen de Fondo</label>
                    <input type="file" name="hero_image" class="form-control-file" accept="image/*">
                    @if($config->hero_image)
                        <small class="text-success"><i class="fas fa-check"></i> Imagen actual cargada</small>
                    @endif
                </div>
                <div class="col-md-6 form-group">
                    <label>Color Principal de la Landing</label>
                    <input type="color" name="primary_color" class="form-control" value="{{ old('primary_color', $config->primary_color) }}" required style="height: 40px;">
                </div>
            </div>

            <hr>

            <h5 class="font-weight-bold text-primary mb-3 mt-4">Sección Nosotros</h5>
            <div class="row">
                <div class="col-md-12 form-group">
                    <label>Texto descriptivo</label>
                    <textarea name="about_text" class="form-control" rows="4">{{ old('about_text', $config->about_text) }}</textarea>
                </div>
                <div class="col-md-6 form-group">
                    <label>Imagen Nosotros</label>
                    <input type="file" name="about_image" class="form-control-file" accept="image/*">
                    @if($config->about_image)
                        <small class="text-success"><i class="fas fa-check"></i> Imagen actual cargada</small>
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
                                <input type="text" name="services_icon[]" class="form-control" value="{{ $service['icon'] ?? 'fas fa-check' }}" maxlength="100">
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
                                <input type="text" name="services_icon[]" class="form-control" value="fas fa-check" maxlength="100">
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
@endsection

@push('styles')
<style>
.ic-card { background: #fff; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.04); }
</style>
@endpush

@push('scripts')
<script>
    $(function () {
        $('#add-service').on('click', function () {
            const row = $('.service-config-row').first().clone();
            row.find('input').val('');
            row.find('input[name="services_icon[]"]').val('fas fa-check');
            $('#services-container').append(row);
        });

        $(document).on('click', '.remove-service', function () {
            if ($('.service-config-row').length > 1) {
                $(this).closest('.service-config-row').remove();
            } else {
                $(this).closest('.service-config-row').find('input').val('');
                $(this).closest('.service-config-row').find('input[name="services_icon[]"]').val('fas fa-check');
            }
        });
    });
</script>
@endpush
