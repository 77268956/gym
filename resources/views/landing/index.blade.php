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
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: {{ $config->primary_color ?? '#2563EB' }};
            --text-dark: #101113;
            --text-light: #aeb2b6;
            --bg-light: #f1f0ed;
            --surface-dark: #111214;
            --surface-raised: #191b1e;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            color: var(--text-dark);
            scroll-behavior: smooth;
            letter-spacing: 0;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 800;
        }

        /* Navbar */
        .navbar {
            min-height: 88px;
            padding: 0.75rem 0;
            background: rgba(9, 10, 12, 0.96) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(14px);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            padding: 0;
        }
        .navbar-brand img {
            display: block;
            width: auto;
            height: 68px;
            max-width: 132px;
            object-fit: contain;
        }
        .navbar .nav-link {
            margin: 0 0.45rem;
            color: #e9e8e5 !important;
            font-size: 0.75rem;
            letter-spacing: 0;
        }
        .navbar .nav-link:hover,
        .navbar .nav-link:focus-visible {
            color: var(--primary) !important;
        }
        .navbar .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.35);
        }
        .navbar .navbar-toggler-icon {
            filter: invert(1);
        }
        .navbar .btn-outline-dark {
            border-color: var(--primary);
            border-radius: 3px !important;
            color: #fff;
        }
        .navbar .btn-outline-dark:hover {
            background: var(--primary);
            color: #fff;
        }
        @media (max-width: 575.98px) {
            .navbar-brand img {
                height: 58px;
                max-width: 112px;
            }
        }
        .nav-link {
            font-weight: 600;
            text-transform: uppercase;
            transition: color 0.3s;
        }

        /* Hero Section */
        .hero {
            position: relative;
            height: min(820px, calc(100svh - 104px));
            min-height: 540px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background: linear-gradient(90deg, rgba(6, 7, 8, 0.94) 0%, rgba(6, 7, 8, 0.76) 48%, rgba(6, 7, 8, 0.22) 100%),
                        url('{{ $config->hero_image ? asset("storage/".$config->hero_image) : "https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1470&auto=format&fit=crop" }}') center/cover no-repeat;
            color: white;
            text-align: left;
            padding: 104px 0 2rem;
            overflow: hidden;
        }
        .hero .container {
            position: relative;
            z-index: 1;
        }
        .hero-copy {
            max-width: 760px;
            animation: hero-rise 0.8s ease-out both;
        }
        .hero .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            margin-bottom: 1.25rem;
            color: var(--primary);
            font-size: 0.78rem;
            font-weight: 700;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .hero .hero-kicker::before {
            width: 28px;
            height: 2px;
            background: var(--primary);
            content: '';
        }
        .hero h1 {
            max-width: 760px;
            margin-bottom: 1rem;
            font-size: 5.5rem;
            line-height: 0.92;
            text-transform: uppercase;
        }
        @media (max-width: 575.98px) {
            .hero h1 {
                font-size: 2.65rem;
            }
        }
        .hero p {
            max-width: 560px;
            margin-bottom: 1.75rem;
            color: rgba(255, 255, 255, 0.78);
            font-size: 1.1rem;
            line-height: 1.7;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1.25rem;
        }
        .btn-custom {
            background-color: var(--primary);
            color: white;
            padding: 12px 30px;
            border-radius: 3px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0;
            transition: background-color 0.2s ease, transform 0.2s ease;
            border: 2px solid var(--primary);
        }
        .btn-custom:hover,
        .btn-custom:focus-visible {
            background-color: #fff;
            border-color: #fff;
            color: var(--text-dark);
            transform: translateY(-2px);
        }
        .hero-secondary-link {
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
        }
        .hero-secondary-link:hover,
        .hero-secondary-link:focus-visible {
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
            font-size: 2.8rem;
            text-transform: uppercase;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }
        .section-title::after {
            width: 48px;
            height: 3px;
            background-color: var(--primary);
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            content: '';
        }

        /* About Section */
        .about {
            background-color: var(--surface-dark);
            color: #fff;
        }
        .about .section-title h2 {
            color: #fff;
        }
        .about-text {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #b8babd;
        }
        .about-img {
            max-height: 480px;
            border-left: 4px solid var(--primary);
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Services Section */
        .service-card {
            background: #fff;
            padding: 2.25rem 1.75rem;
            border: 1px solid rgba(16, 17, 19, 0.08);
            border-radius: 4px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(16, 17, 19, 0.06);
            transition: transform 0.25s ease, border-color 0.25s ease;
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
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
            background: linear-gradient(0deg, rgba(5, 6, 7, 0.92) 0%, rgba(5, 6, 7, 0.2) 72%);
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
            background-color: #191b1e;
            color: white;
        }
        .contact-heading {
            max-width: 700px;
            margin-bottom: 2.5rem;
        }
        .contact-eyebrow {
            margin-bottom: 0.75rem;
            color: var(--primary);
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .contact-heading h2 {
            margin-bottom: 0.75rem;
            color: #fff;
            font-size: 3.25rem;
            text-transform: uppercase;
        }
        .contact-heading > p:last-child {
            max-width: 540px;
            margin-bottom: 0;
            color: #b8babd;
            line-height: 1.7;
        }
        .contact-form-panel {
            padding: 1.25rem;
            border: 0;
            border-left: 4px solid var(--primary);
            background: var(--bg-light);
            color: var(--text-dark);
        }
        .contact-form-panel h3 {
            margin-bottom: 0.3rem;
            font-size: 1.5rem;
            text-transform: uppercase;
        }
        .contact-form-intro {
            margin-bottom: 0.9rem;
            color: #62666b;
            font-size: 0.86rem;
        }
        .contact-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.7rem;
        }
        .contact-field-wide {
            grid-column: 1 / -1;
        }
        .contact-field label {
            display: block;
            margin-bottom: 0.4rem;
            color: #292b2e;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .contact-field .form-control {
            min-height: 40px;
            border: 1px solid #d5d4d1;
            border-radius: 3px;
            background: #fff;
            color: #161719;
            box-shadow: none;
        }
        .contact-field textarea.form-control {
            min-height: 76px;
            resize: vertical;
        }
        .contact-field .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.14);
        }
        .contact-form-note {
            margin: 0.7rem 0 0.85rem;
            color: #62666b;
            font-size: 0.78rem;
        }
        .contact-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            min-height: 42px;
        }
        .contact-form-status {
            min-height: 1.5rem;
            margin: 0.8rem 0 0;
            color: #236b43;
            font-size: 0.82rem;
        }
        .contact-details {
            padding-top: 0;
        }
        .contact-side {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .contact-detail {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.2rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }
        .contact-detail-icon {
            display: grid;
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            place-items: center;
            background: rgba(255, 255, 255, 0.06);
            color: var(--primary);
        }
        .contact-detail h3 {
            margin: 0 0 0.35rem;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .contact-detail p,
        .contact-detail a {
            margin: 0;
            color: #c4c6c8;
            font-size: 0.92rem;
            text-decoration: none;
            overflow-wrap: anywhere;
        }
        .contact-detail a:hover,
        .contact-detail a:focus-visible {
            color: #fff;
        }
        .contact-social-title {
            margin: 0 0 0.8rem;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
        }
        .contact-social-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }
        .contact-social-block .social-icons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        .contact-map {
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: #111214;
        }
        .contact-map-heading {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.85rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }
        .contact-map-heading h3 {
            margin: 0 0 0.25rem;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .contact-map-heading p {
            margin: 0;
            color: #c4c6c8;
            font-size: 0.85rem;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }
        .contact-map iframe {
            display: block;
            width: 100%;
            height: 180px;
            border: 0;
            filter: grayscale(0.75) contrast(1.05);
        }
        .contact-map-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.7rem 0.85rem;
            color: #e8e8e6;
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
        }
        .contact-map-link:hover,
        .contact-map-link:focus-visible {
            color: var(--primary);
        }
        .social-icons a {
            display: inline-block;
            width: 42px;
            height: 42px;
            line-height: 40px;
            text-align: center;
            border-radius: 3px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            background: transparent;
            color: white;
            font-size: 1.1rem;
            margin-right: 0.5rem;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }
        .contact-social-block .social-icons a {
            margin-right: 0;
        }
        .social-icons a:hover {
            border-color: var(--primary);
            background-color: var(--primary);
        }
        @media (max-width: 575.98px) {
            .contact-heading h2 {
                font-size: 2.35rem;
            }
            .contact-layout {
                margin-right: 0;
                margin-left: 0;
            }
            .contact-form-panel {
                padding: 1.25rem;
            }
            .contact-map iframe {
                height: 200px;
            }
            .contact-form-grid {
                grid-template-columns: 1fr;
            }
            .contact-field-wide {
                grid-column: auto;
            }
            .contact-submit {
                width: 100%;
            }
        }

        /* Footer */
        footer {
            background-color: #090a0c;
            color: #94A3B8;
            padding: 2rem 0;
            text-align: center;
        }
        .services-section {
            background: var(--bg-light);
        }
        .plans-section {
            background: #090a0c;
            color: #fff;
        }
        .plans-section .section-title h2 {
            color: #fff;
        }
        .plans-section .service-card {
            display: flex;
            flex-direction: column;
            background: var(--surface-raised);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-top: 3px solid var(--primary) !important;
            box-shadow: none;
            color: #fff;
        }
        .plans-section .service-card h3,
        .plans-section .service-card h2 {
            color: #fff;
        }
        .plans-section .service-card .text-primary {
            color: var(--primary) !important;
        }
        .plans-section .service-card .text-muted {
            color: #aeb2b6 !important;
        }
        .plans-section .service-card .btn-outline-dark {
            border-color: rgba(255, 255, 255, 0.55);
            border-radius: 3px !important;
            color: #fff;
        }
        .plans-section .service-card .btn-outline-dark:hover {
            border-color: var(--primary);
            background: var(--primary);
        }
        .contact-info-box i {
            color: var(--primary);
        }
        .social-icons a {
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 3px;
            background: transparent;
        }
        .social-icons a:hover {
            border-color: var(--primary);
            background: var(--primary);
            transform: translateY(-3px);
        }
        @keyframes hero-rise {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 991.98px) {
            .navbar-collapse {
                padding-top: 1rem;
            }
            .navbar .nav-link {
                margin: 0.35rem 0;
            }
            .hero h1 {
                font-size: 4.5rem;
            }
        }
        @media (max-width: 575.98px) {
            .hero {
                height: min(740px, calc(100svh - 96px));
                min-height: 540px;
                padding-top: 96px;
                background-position: 58% center;
            }
            .hero h1 {
                font-size: 2.65rem;
            }
            .hero p {
                font-size: 1rem;
            }
            section {
                padding: 4rem 0;
            }
            .section-title h2 {
                font-size: 2.25rem;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top site-nav">
        <div class="container">
            <a class="navbar-brand" href="#">
                @if(isset($gymConfig) && $gymConfig->logo_path)
                    <img src="{{ asset('storage/' . $gymConfig->logo_path) }}" alt="Logo" class="navbar-logo">
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
            <div class="hero-copy">
                <p class="hero-kicker">Star Gym / Fitness System</p>
                <h1>{{ $config->hero_title ?? 'Transforma tu Vida' }}</h1>
                <p>{{ $config->hero_subtitle ?? 'El mejor gimnasio para alcanzar tus metas.' }}</p>
                <div class="hero-actions">
                    <a href="#contacto" class="btn btn-custom text-decoration-none">Únete Ahora</a>
                    <a href="#planes" class="hero-secondary-link">Explorar planes <i class="fas fa-arrow-down ms-1" aria-hidden="true"></i></a>
                </div>
            </div>
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
        <section id="servicios" class="services-section">
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
    <section id="planes" class="plans-section">
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
            <div class="contact-heading">
                <p class="contact-eyebrow">Estamos para ayudarte</p>
                <h2>Hablemos de tu próximo plan</h2>
                <p>Cuéntanos qué estás buscando y te orientamos con la información que necesitas.</p>
            </div>

            <div class="row g-5 align-items-start contact-layout">
                <div class="col-lg-7">
                    <form class="contact-form-panel" id="planRequestForm" data-whatsapp-number="{{ preg_replace('/[^0-9]/', '', $config->contact_whatsapp ?? '') }}">
                        <h3>Solicita información</h3>
                        <p class="contact-form-intro">Déjanos tus datos y prepara tu consulta por WhatsApp.</p>
                        <div class="contact-form-grid">
                            <div class="contact-field">
                                <label for="requestName">Nombre</label>
                                <input class="form-control" id="requestName" name="nombre" type="text" autocomplete="name" maxlength="100" required>
                            </div>
                            <div class="contact-field">
                                <label for="requestPhone">Teléfono</label>
                                <input class="form-control" id="requestPhone" name="telefono" type="tel" autocomplete="tel" maxlength="30" required>
                            </div>
                            <div class="contact-field">
                                <label for="requestEmail">Correo electrónico <span class="text-muted">(opcional)</span></label>
                                <input class="form-control" id="requestEmail" name="correo" type="email" autocomplete="email" maxlength="255">
                            </div>
                            <div class="contact-field">
                                <label for="requestType">¿Qué información buscas?</label>
                                <select class="form-control" id="requestType" name="tipo_solicitud" required>
                                    <option value="informacion">Información general</option>
                                    @if($planes->isNotEmpty())
                                        <option value="plan">Consultar un plan</option>
                                    @endif
                                </select>
                            </div>
                            @if($planes->isNotEmpty())
                                <div class="contact-field contact-field-wide" id="requestPlanField" hidden>
                                    <label for="requestPlan">Plan de interés</label>
                                    <select class="form-control" id="requestPlan" name="plan">
                                        <option value="">Selecciona un plan</option>
                                        @foreach($planes as $plan)
                                            <option value="{{ $plan->nombre }}">{{ $plan->nombre }} - ${{ number_format($plan->precio, 2) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="contact-field contact-field-wide">
                                <label for="requestMessage">Mensaje <span class="text-muted">(opcional)</span></label>
                                <textarea class="form-control" id="requestMessage" name="mensaje" maxlength="1000" placeholder="Escribe aquí tu consulta"></textarea>
                            </div>
                        </div>
                        <p class="contact-form-note">WhatsApp abrirá tu mensaje para que confirmes el envío.</p>
                        <button class="btn btn-custom contact-submit" type="submit">
                            <i class="fab fa-whatsapp" aria-hidden="true"></i> Preparar solicitud
                        </button>
                        <p class="contact-form-status" id="requestFormStatus" role="status" aria-live="polite"></p>
                    </form>
                </div>

                <div class="col-lg-5 contact-side">
                    @if($config->contact_address)
                        <div class="contact-map">
                            <div class="contact-map-heading">
                                <span class="contact-detail-icon"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></span>
                                <div><h3>Cómo llegar</h3><p>{{ $config->contact_address }}</p></div>
                            </div>
                            <iframe src="https://maps.google.com/maps?q={{ urlencode($config->contact_address) }}&amp;output=embed" title="Mapa de {{ $config->contact_address }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                            <a class="contact-map-link" href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($config->contact_address) }}" target="_blank" rel="noopener noreferrer">
                                Abrir indicaciones en Google Maps <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>
                            </a>
                        </div>
                    @endif
                    <div class="contact-details">
                    @if($config->contact_phone)
                        <div class="contact-detail">
                            <span class="contact-detail-icon"><i class="fas fa-phone-alt" aria-hidden="true"></i></span>
                            <div><h3>Llámanos</h3><a href="tel:{{ $config->contact_phone }}">{{ $config->contact_phone }}</a></div>
                        </div>
                    @endif
                    @if($config->contact_email)
                        <div class="contact-detail">
                            <span class="contact-detail-icon"><i class="fas fa-envelope" aria-hidden="true"></i></span>
                            <div><h3>Email</h3><a href="mailto:{{ $config->contact_email }}">{{ $config->contact_email }}</a></div>
                        </div>
                    @endif

                    </div>
                </div>
            </div>
            @if($config->contact_facebook || $config->contact_instagram || $config->contact_whatsapp)
                <div class="contact-social-block">
                    <h3 class="contact-social-title">Síguenos</h3>
                    <div class="social-icons">
                        @if($config->contact_facebook)
                            <a href="{{ $config->contact_facebook }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
                        @endif
                        @if($config->contact_instagram)
                            <a href="{{ $config->contact_instagram }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
                        @endif
                        @if($config->contact_whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $config->contact_whatsapp) }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} javier. Todos los derechos reservados.</p>
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

        const planRequestForm = document.getElementById('planRequestForm');

        if (planRequestForm) {
            const requestType = document.getElementById('requestType');
            const requestPlanField = document.getElementById('requestPlanField');
            const requestPlan = document.getElementById('requestPlan');
            const requestFormStatus = document.getElementById('requestFormStatus');

            if (requestType && requestPlanField && requestPlan) {
                requestType.addEventListener('change', function () {
                    const isPlanRequest = requestType.value === 'plan';
                    requestPlanField.hidden = !isPlanRequest;
                    requestPlan.required = isPlanRequest;

                    if (!isPlanRequest) {
                        requestPlan.value = '';
                    }
                });
            }

            planRequestForm.addEventListener('submit', function (event) {
                event.preventDefault();

                if (!planRequestForm.reportValidity()) {
                    return;
                }

                const whatsappNumber = planRequestForm.dataset.whatsappNumber;

                if (!whatsappNumber) {
                    requestFormStatus.textContent = 'El gimnasio aún no ha configurado un número de WhatsApp.';
                    return;
                }

                const formData = new FormData(planRequestForm);
                const requestLines = [
                    'Hola, quiero solicitar información de Star Gym.',
                    'Nombre: ' + formData.get('nombre'),
                    'Teléfono: ' + formData.get('telefono'),
                    'Correo: ' + (formData.get('correo') || 'No proporcionado'),
                    'Solicitud: ' + requestType.selectedOptions[0].text,
                ];

                if (formData.get('plan')) {
                    requestLines.push('Plan de interés: ' + formData.get('plan'));
                }

                if (formData.get('mensaje')) {
                    requestLines.push('Mensaje: ' + formData.get('mensaje'));
                }

                const whatsappUrl = 'https://wa.me/' + whatsappNumber + '?text=' + encodeURIComponent(requestLines.join('\n'));
                const whatsappWindow = window.open(whatsappUrl, '_blank', 'noopener,noreferrer');

                if (!whatsappWindow) {
                    window.location.assign(whatsappUrl);
                    return;
                }

                requestFormStatus.textContent = 'WhatsApp está listo con tu solicitud. Confirma el envío desde la aplicación.';
            });
        }
    </script>
</body>
</html>
