<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - {{ $gymConfig->nombre_gimnasio ?? 'EcoGim' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary: {{ $gymConfig->color_primario ?? '#2563EB' }};
            --primary-hover: {{ $gymConfig->color_primario_hover ?? '#1D4ED8' }};
            --sidebar-bg: {{ $gymConfig->color_sidebar ?? '#1E293B' }};
            --sidebar-hover: {{ $gymConfig->color_sidebar_hover ?? '#334155' }};
        }
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            padding: 2rem;
            background: #EAF0F6;
            color: #334155;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-shell {
            width: 100%;
            max-width: 1040px;
            min-height: 620px;
            display: grid;
            grid-template-columns: .9fr 1.1fr;
            overflow: hidden;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .16);
        }
        .login-brand-panel {
            position: relative;
            overflow: hidden;
            padding: 3rem;
            color: #fff;
            background: linear-gradient(145deg, var(--sidebar-bg), var(--sidebar-hover));
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .login-brand-panel::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            right: -130px;
            bottom: -120px;
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 50%;
            box-shadow: 0 0 0 32px rgba(255,255,255,.04), 0 0 0 64px rgba(255,255,255,.03);
        }
        .brand-mark {
            width: 58px;
            height: 58px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: var(--primary);
            color: #fff;
            font-size: 1.55rem;
            box-shadow: 0 10px 22px rgba(0,0,0,.18);
        }
        .brand-mark img { max-width: 42px; max-height: 42px; object-fit: contain; }
        .brand-name { margin-top: 1rem; font-size: 1.6rem; font-weight: 700; }
        .brand-copy { max-width: 320px; color: rgba(255,255,255,.72); line-height: 1.7; }
        .brand-footer { color: rgba(255,255,255,.58); font-size: .78rem; }
        .login-form-panel { display: flex; align-items: center; padding: 3.5rem 4.5rem; }
        .login-form-content { width: 100%; max-width: 410px; margin: 0 auto; }
        .eyebrow { color: var(--primary); font-size: .72rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .login-title { color: var(--sidebar-bg); font-size: 2rem; font-weight: 700; margin: .55rem 0 .5rem; }
        .login-subtitle { color: #64748B; font-size: .9rem; margin-bottom: 2rem; }
        .form-label { color: #334155; font-size: .8rem; font-weight: 700; margin-bottom: .45rem; }
        .input-wrap { position: relative; }
        .input-wrap > i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94A3B8; z-index: 2; }
        .form-control { height: 48px; border: 1px solid #D9E2EC; border-radius: 9px; padding-left: 2.8rem; color: #1E293B; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 16%, transparent); }
        .password-toggle { position: absolute; top: 0; right: 0; width: 46px; height: 48px; border: 0; background: transparent; color: #94A3B8; cursor: pointer; }
        .password-toggle:focus { outline: none; color: var(--primary); }
        .btn-login { height: 48px; border: 0; border-radius: 9px; background: var(--primary); color: #fff; font-weight: 700; transition: background .2s, transform .2s, box-shadow .2s; }
        .btn-login:hover { background: var(--primary-hover); color: #fff; transform: translateY(-1px); box-shadow: 0 8px 18px color-mix(in srgb, var(--primary) 28%, transparent); }
        .alert-login { border: 0; border-left: 4px solid #DC2626; border-radius: 8px; background: #FEF2F2; color: #991B1B; font-size: .82rem; }
        .login-help { color: #94A3B8; font-size: .75rem; text-align: center; margin-top: 1.5rem; }
        @media (max-width: 767.98px) {
            body { padding: 1rem; }
            .login-shell { display: block; min-height: auto; border-radius: 16px; }
            .login-brand-panel { min-height: 230px; padding: 1.75rem; }
            .brand-copy { display: none; }
            .brand-footer { margin-top: 2rem; }
            .login-form-panel { padding: 2.25rem 1.5rem 2rem; }
            .login-title { font-size: 1.7rem; }
        }
    </style>
</head>
<body>
<main class="login-shell">
    <section class="login-brand-panel">
        <div>
            <div class="brand-mark">
                @if(isset($gymConfig) && $gymConfig->logo_path)
                    <img src="{{ asset('storage/' . $gymConfig->logo_path) }}" alt="Logo">
                @else
                    <i class="fas fa-dumbbell"></i>
                @endif
            </div>
            <div class="brand-name">{{ $gymConfig->nombre_gimnasio ?? 'EcoGim' }}</div>
            <p class="brand-copy mb-0">Administra tu gimnasio con claridad, rapidez y control desde un solo lugar.</p>
        </div>
        <div class="brand-footer"><i class="fas fa-shield-alt mr-1"></i> Acceso seguro para personal autorizado</div>
    </section>

    <section class="login-form-panel">
        <div class="login-form-content">
            <div class="eyebrow">Panel administrativo</div>
            <h1 class="login-title">Bienvenido de nuevo</h1>
            <p class="login-subtitle">Ingresa tus datos para continuar al sistema.</p>

            @if ($errors->any())
                <div class="alert alert-login mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <div class="input-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control" id="usuario" name="usuario" value="{{ old('usuario') }}" placeholder="Escribe tu usuario" required autofocus autocomplete="username">
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Escribe tu contraseña" required autocomplete="current-password">
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Mostrar contraseña">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-login btn-block">
                    Ingresar al sistema <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </form>

            <p class="login-help mb-0">Si tienes problemas para ingresar, contacta al administrador.</p>
        </div>
    </section>
</main>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        var password = document.getElementById('password');
        var icon = this.querySelector('i');
        var visible = password.type === 'text';
        password.type = visible ? 'password' : 'text';
        icon.className = visible ? 'fas fa-eye' : 'fas fa-eye-slash';
        this.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
    });
</script>
</body>
</html>
