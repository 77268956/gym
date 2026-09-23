@extends('layouts.app')

@section('title', 'Mi perfil')

@section('skeleton')
    <div style="max-width: 980px; margin: 0 auto;">
        {{-- Hero banner --}}
        <div class="skel-box" style="height: 100px; width: 100%; margin-bottom: 1.25rem; background: linear-gradient(90deg, rgba(30,41,59,0.3) 25%, rgba(30,41,59,0.4) 50%, rgba(30,41,59,0.3) 75%); background-size: 400% 100%;"></div>
        {{-- 3 summary stats --}}
        <div class="skel-row">
            <div class="skel-box" style="height: 70px; flex: 1;"></div>
            <div class="skel-box" style="height: 70px; flex: 1;"></div>
            <div class="skel-box" style="height: 70px; flex: 1;"></div>
        </div>
        {{-- Details card --}}
        <div class="skel-box" style="height: 16px; width: 140px; margin-bottom: 0.75rem;"></div>
        <div class="skel-row">
            <div class="skel-box" style="height: 55px; flex: 1;"></div>
            <div class="skel-box" style="height: 55px; flex: 1;"></div>
            <div class="skel-box" style="height: 55px; flex: 1;"></div>
        </div>
        <div class="skel-row">
            <div class="skel-box" style="height: 55px; flex: 1;"></div>
            <div class="skel-box" style="height: 55px; flex: 1;"></div>
            <div class="skel-box" style="height: 55px; flex: 1;"></div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    #page-wrapper main { overflow: hidden; }
    .profile-page { max-width: 980px; height: calc(100vh - 122px); margin: 0 auto; }
    .profile-hero {
        background: linear-gradient(135deg, var(--sidebar-bg), var(--sidebar-hover));
        color: #fff;
        border-radius: 12px;
        padding: 1.35rem 1.75rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        box-shadow: 0 6px 18px rgba(15, 23, 42, .12);
    }
    .profile-avatar {
        width: 84px;
        height: 84px;
        border-radius: 50%;
        background: var(--primary);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        flex-shrink: 0;
        border: 4px solid rgba(255, 255, 255, .2);
    }
    .profile-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .05);
        padding: 1.25rem 1.5rem;
    }
    .profile-label { color: #64748B; font-size: .75rem; font-weight: 600; text-transform: uppercase; }
    .profile-value { color: var(--sidebar-bg); font-size: .95rem; font-weight: 600; margin-bottom: 0; }
    .profile-role { background: var(--primary); color: #fff; border-radius: 50px; padding: .35rem .8rem; font-size: .75rem; font-weight: 600; }
    .profile-summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: .75rem; margin-bottom: 1rem; }
    .profile-summary-item { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: .8rem 1rem; }
    .profile-summary-item i { color: var(--primary); font-size: 1.15rem; margin-bottom: .35rem; }
    .profile-summary-value { color: var(--sidebar-bg); font-size: 1rem; font-weight: 700; display: block; }
    .profile-summary-label { color: #64748B; font-size: .7rem; text-transform: uppercase; font-weight: 600; }
    @media (max-width: 575.98px) {
        .profile-page { height: auto; }
        .profile-hero { padding: 1.25rem; }
        .profile-avatar { width: 68px; height: 68px; font-size: 1.5rem; }
        .profile-summary { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    $nombre = $usuario->nombre ?? $usuario->name ?? 'Usuario';
    $usuarioNombre = $usuario->usuario ?? $usuario->email ?? 'No disponible';
    $correo = $usuario->email ?? 'No disponible';
    $rol = $usuario->rol ?? 'usuario';
    $iniciales = strtoupper(substr($nombre, 0, 1));
    $estadoCuenta = $usuario->deleted_at ? 'Inactiva' : 'Activa';
    $correoVerificado = $usuario->email_verified_at ? 'Verificado' : 'Pendiente';
@endphp

<div class="container-fluid profile-page">
    <div class="profile-hero mb-3">
        <div class="profile-avatar">{{ $iniciales }}</div>
        <div>
            <h2 class="mb-1 font-weight-bold">{{ $nombre }}</h2>
            <p class="mb-0 text-white-50">Información de tu cuenta</p>
        </div>
    </div>

    <div class="profile-card">
        <div class="profile-summary">
            <div class="profile-summary-item">
                <i class="fas fa-shield-alt d-block"></i>
                <span class="profile-summary-value">{{ $estadoCuenta }}</span>
                <span class="profile-summary-label">Estado de cuenta</span>
            </div>
            <div class="profile-summary-item">
                <i class="fas fa-envelope d-block"></i>
                <span class="profile-summary-value">{{ $correoVerificado }}</span>
                <span class="profile-summary-label">Correo electrónico</span>
            </div>
            <div class="profile-summary-item">
                <i class="fas fa-user-tag d-block"></i>
                <span class="profile-summary-value">{{ ucfirst($rol) }}</span>
                <span class="profile-summary-label">Nivel de acceso</span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1 font-weight-bold" style="color:var(--sidebar-bg);">Información personal</h5>
                <p class="text-muted small mb-0">Datos de la cuenta que inició sesión.</p>
            </div>
            <i class="fas fa-user-circle fa-2x text-primary"></i>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <span class="profile-label d-block mb-1">Nombre completo</span>
                <p class="profile-value">{{ $nombre }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <span class="profile-label d-block mb-1">Usuario o correo</span>
                <p class="profile-value">{{ $usuarioNombre }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <span class="profile-label d-block mb-1">Correo electrónico</span>
                <p class="profile-value">{{ $correo }}</p>
            </div>
            <div class="col-md-4 mb-2">
                <span class="profile-label d-block mb-1">Rol</span>
                <span class="profile-role">{{ ucfirst($rol) }}</span>
            </div>
            <div class="col-md-4 mb-2">
                <span class="profile-label d-block mb-1">Miembro desde</span>
                <p class="profile-value">{{ optional($usuario->created_at)->format('d/m/Y') ?? 'No disponible' }}</p>
            </div>
            <div class="col-md-4 mb-2">
                <span class="profile-label d-block mb-1">Última actualización</span>
                <p class="profile-value">{{ optional($usuario->updated_at)->format('d/m/Y') ?? 'No disponible' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection