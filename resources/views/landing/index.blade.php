<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $gymConfig->nombre_gimnasio ?? 'GymX' }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: {{ $config->primary_color ?? '#2563EB' }};
            --text-dark: #1E293B;
            --text-light: #64748B;
            --bg-light: #F8FAFC;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--text-dark);
            scroll-behavior: smooth;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
        }

        /* Navbar */
        .navbar {
            background-color: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary) !important;
        }
        .nav-link {
            font-weight: 600;
            color: var(--text-dark) !important;
            margin: 0 10px;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: var(--primary) !important;
        }

        /* Hero Section */
        .hero {
            position: relative;
            height: 100vh;
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('{{ $config->hero_image ? asset("storage/".$config->hero_image) : "https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1470&auto=format&fit=crop" }}') center/cover no-repeat;
            color: white;
            text-align: center;
            padding-top: 80px; /* Offset for navbar */
        }
        .hero h1 {
            font-size: 4rem;
            text-transform: uppercase;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            font-weight: 400;
        }
        .btn-custom {
            background-color: var(--primary);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            border: 2px solid var(--primary);
        }
        .btn-custom:hover {
            background-color: transparent;
            color: var(--primary);
        }

        /* Sections */
        section {
            padding: 5rem 0;
        }
        .section-title {
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
        }
        .section-title h2 {
            font-size: 2.5rem;
            text-transform: uppercase;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }
        .section-title::after {
            content: '';
            width: 80px;
            height: 4px;
            background-color: var(--primary);
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        /* About Section */
        .about {
            background-color: var(--bg-light);
        }
        .about-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-light);
        }
        .about-img {
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            height: auto;
        }

        /* Services Section */
        .service-card {
            background: white;
            padding: 2.5rem 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .service-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        .service-card h4 {
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }
        .service-card p {
            color: var(--text-light);
            margin-bottom: 0;
        }

        /* carta Section */


        .reward-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 250px), 1fr));
            justify-items: center;
            gap: 1.5rem;
        }
        .reward-card {
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 310px;
            aspect-ratio: 1 / 1;
            background: transparent;
            border: 1px solid rgba(255,255,255,.16);
            border-radius: 6px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .1);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .reward-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 30px rgba(0, 0, 0, .18);
        }
        .reward-image {
            position: absolute;
            z-index: 0;
            inset: 0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            color: #e5e5e5;
            font-size: 2.5rem;
        }
        .reward-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: none;
        }
        .reward-image img.reward-click-image {
            cursor: zoom-in;
        }
        .reward-body {
            position: absolute;
            z-index: 2;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 1rem;
            background: transparent;
            color: #fff;
            pointer-events: none;
        }
        .reward-name {
            overflow: hidden;
            margin: 0 0 .35rem;
            color: #fff;
            font-size: 1.08rem;
            font-weight: 800;
            line-height: 1.2;
            text-overflow: ellipsis;
            text-shadow: 0 1px 3px #000, 0 0 8px rgba(0,0,0,.95);
            white-space: nowrap;
        }
        .reward-description {
            display: -webkit-box;
            overflow: hidden;
            margin: 0 0 .65rem;
            color: #fff;
            font-size: .76rem;
            line-height: 1.35;
            text-shadow: 0 1px 3px #000, 0 0 8px rgba(0,0,0,.95);
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
        .reward-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
            margin-bottom: .55rem;
        }
        .reward-tag {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            max-width: 100%;
            padding: .25rem .5rem;
            border: 1px solid rgba(255,255,255,.38);
            border-radius: 50px;
            background: transparent;
            text-shadow: 0 1px 3px #000, 0 0 6px rgba(0,0,0,.95);
            color: #fff;
            font-size: .66rem;
            font-weight: 700;
        }
        .reward-tag-points {
            color: #fff;
        }
        .reward-tag-points i {
            color: #d97706;
        }
        .reward-action {
            display: inline-flex;
            align-items: center;
            align-self: flex-end;
            gap: .4rem;
            min-height: 30px;
            margin-top: auto;
            padding: .3rem .55rem;
            border-radius: 7px;
            background: transparent;
            border: 1px solid rgba(255,255,255,.72);
            color: #fff;
            font-size: .72rem;
            font-weight: 800;
            text-decoration: none;
            pointer-events: auto;
            text-shadow: 0 1px 3px #000, 0 0 6px rgba(0,0,0,.95);
            transition: border-color .2s ease, opacity .2s ease;
        }
        .reward-action:hover,
        .reward-action:focus-visible {
            border-color: #fff;
            color: #fff;
            opacity: .82;
            text-decoration: none;
        }
        /* Contact Section */
        .contact {
            background-color: var(--text-dark);
            color: white;
        }
        .contact .section-title h2 {
            color: white;
        }
        .contact-info-box {
            text-align: center;
            margin-bottom: 2rem;
        }
        .contact-info-box i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1rem;
            display: block;
        }
        .contact-info-box p, .contact-info-box a {
            color: #CBD5E1;
            text-decoration: none;
            font-size: 1.1rem;
        }
        .contact-info-box a:hover {
            color: white;
        }
        
        .social-icons a {
            display: inline-block;
            width: 50px;
            height: 50px;
            line-height: 50px;
            text-align: center;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
            color: white;
            font-size: 1.5rem;
            margin: 0 10px;
            transition: all 0.3s;
        }
        .social-icons a:hover {
            background-color: var(--primary);
            transform: translateY(-5px);
        }

        /* Footer */
        footer {
            background-color: #0F172A;
            color: #94A3B8;
            padding: 2rem 0;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                @if(isset($gymConfig) && $gymConfig->logo_path)
                    <img src="{{ asset('storage/' . $gymConfig->logo_path) }}" alt="Logo" style="height: 40px; object-fit: contain;">
                @else
                    {{ $gymConfig->nombre_gimnasio ?? 'GYMX' }}
                @endif
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#inicio">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#nosotros">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="#servicios">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="#planes">Planes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#recompensas">Tienda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold text-uppercase" style="letter-spacing:1px;">Acceso Socios</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="inicio" class="hero">
        <div class="container">
            <h1>{{ $config->hero_title ?? 'Transforma tu Vida' }}</h1>
            <p>{{ $config->hero_subtitle ?? 'El mejor gimnasio para alcanzar tus metas.' }}</p>
            <a href="#contacto" class="btn btn-custom text-decoration-none">Únete Ahora</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="nosotros" class="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="pe-lg-5">
                        <div class="section-title" style="text-align: left;">
                            <h2>Sobre Nosotros</h2>
                        </div>
                        <p class="about-text">
                            {{ $config->about_text ?? 'Somos más que un gimnasio, somos una comunidad. Nuestras instalaciones cuentan con equipo de última generación y un ambiente diseñado para motivarte a superar tus límites día a día.' }}
                        </p>
                        <a href="#servicios" class="btn btn-custom mt-3 text-decoration-none">Ver Servicios</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="{{ $config->about_image ? asset('storage/'.$config->about_image) : 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?q=80&w=1470&auto=format&fit=crop' }}" alt="Nosotros" class="about-img img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    @if(!empty($config->services))
        <section id="servicios">
            <div class="container">
                <div class="section-title">
                    <h2>Servicios</h2>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach($config->services as $service)
                        <div class="col-lg-4 col-md-6">
                            <div class="service-card h-100">
                                <div class="service-icon"><i class="{{ $service['icon'] ?? 'fas fa-check' }}"></i></div>
                                <h3 class="mb-3">{{ $service['title'] ?? '' }}</h3>
                                @if(!empty($service['desc']))
                                    <p class="text-muted mb-0">{{ $service['desc'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Plans Section -->
    <section id="planes">
        <div class="container">
            <div class="section-title">
                <h2>Planes de Membresía</h2>
            </div>
            <div class="row g-4 justify-content-center">
                @forelse($planes as $plan)
                    <div class="col-lg-4 col-md-6">
                        <div class="service-card text-center d-flex flex-column" style="border-top: 5px solid var(--primary);">
                            <h3 class="mb-3">{{ $plan->nombre }}</h3>
                            <h2 class="text-primary mb-4">${{ number_format($plan->precio, 2) }}</h2>
                            <p class="text-muted mb-4">{{ $plan->duracion_dias }} días de acceso</p>
                            @if($plan->descripcion)
                                <p class="small text-muted">{{ $plan->descripcion }}</p>
                            @endif
                            <div class="mt-auto pt-3">
                                <a href="#contacto" class="btn btn-outline-dark rounded-pill w-100">Elegir Plan</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">
                        <p>Planes no disponibles por el momento.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Puntos y Recompensas Section -->
    <section id="recompensas" class="about">
        <div class="container">
            <div class="section-title">
                <h2>Gana Puntos y Recompensas</h2>
                <p class="text-muted mt-3">¡Por cada asistencia ganas puntos EcoGim que puedes canjear por productos en nuestra tienda!</p>
            </div>
            <div class="reward-grid mt-2">
                @forelse($recompensas as $prod)
                    <article class="reward-card">
                        <div class="reward-body">
                            <h3 class="reward-name">{{ $prod->nombre }}</h3>
                            <p class="reward-description">{{ $prod->descripcion ?: 'Canjea este producto con tus puntos EcoGim.' }}</p>
                            <div class="reward-meta">
                                <span class="reward-tag reward-tag-points"><i class="fas fa-star" aria-hidden="true"></i>{{ number_format($prod->puntos_valor) }} pts</span>
                                @if($prod->categoria)
                                    <span class="reward-tag">{{ ucfirst($prod->categoria) }}</span>
                                @endif
                            </div>
                            <a href="{{ route('login') }}" class="reward-action">
                                Canjear ahora <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="reward-image">
                            @if($prod->imagen)
                                <img src="{{ asset('storage/'.$prod->imagen) }}" alt="{{ $prod->nombre }}" class="reward-click-image" data-bs-toggle="modal" data-bs-target="#modalImagenRecompensa" data-image="{{ asset('storage/'.$prod->imagen) }}">
                            @else
                                <i class="fas fa-box" aria-hidden="true"></i>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="text-center text-muted">
                        <p>Próximamente más recompensas.</p>
                    </div>
                @endforelse
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('login') }}" class="btn btn-custom text-decoration-none">Ver mis puntos</a>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalImagenRecompensa" tabindex="-1" aria-labelledby="modalImagenRecompensaTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 bg-dark">
                <div class="modal-header border-0 py-2">
                    <h2 class="visually-hidden" id="modalImagenRecompensaTitulo">Imagen del producto</h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-0 text-center">
                    <img id="imagenRecompensaModal" src="" alt="" style="width:100%;max-height:75vh;object-fit:contain;">
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <section id="contacto" class="contact">
        <div class="container">
            <div class="section-title">
                <h2>Contáctanos</h2>
            </div>
            
            <div class="row justify-content-center mb-5">
                <div class="col-md-4">
                    <div class="contact-info-box">
                        <i class="fas fa-map-marker-alt"></i>
                        <h4>Ubicación</h4>
                        <p>{{ $config->contact_address ?? 'Dirección del Gimnasio' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-info-box">
                        <i class="fas fa-phone-alt"></i>
                        <h4>Llámanos</h4>
                        <p><a href="tel:{{ $config->contact_phone }}">{{ $config->contact_phone ?? '+000 000 0000' }}</a></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-info-box">
                        <i class="fas fa-envelope"></i>
                        <h4>Email</h4>
                        <p><a href="mailto:{{ $config->contact_email }}">{{ $config->contact_email ?? 'contacto@gimnasio.com' }}</a></p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <h4 class="mb-4">Síguenos en nuestras redes</h4>
                <div class="social-icons">
                    @if($config->contact_facebook)
                        <a href="{{ $config->contact_facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    @if($config->contact_instagram)
                        <a href="{{ $config->contact_instagram }}" target="_blank"><i class="fab fa-instagram"></i></a>
                    @endif
                    @if($config->contact_whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $config->contact_whatsapp) }}" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} {{ $gymConfig->nombre_gimnasio ?? 'GymX' }}. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('modalImagenRecompensa').addEventListener('show.bs.modal', function (event) {
            var image = event.relatedTarget;
            var modalImage = document.getElementById('imagenRecompensaModal');
            modalImage.src = image.dataset.image;
            modalImage.alt = image.alt;
        });
    </script>
</body>
</html>
