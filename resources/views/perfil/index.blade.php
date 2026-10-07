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
    .profile-page { max-width: 1080px; height: calc(100vh - 122px); margin: 0 auto; overflow-y: auto; padding: 0 .5rem 1rem; }
    .profile-hero {
        background: linear-gradient(120deg, var(--sidebar-bg), var(--primary));
        color: #fff;
        border-radius: 12px;
        padding: 1.5rem 1.75rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        min-height: 150px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, .12);
        position: relative;
        overflow: hidden;
    }
    .profile-hero::after { content: ''; position: absolute; right: -35px; top: -85px; width: 280px; height: 280px; border: 36px solid rgba(255,255,255,.08); border-radius: 50%; pointer-events: none; }
    .profile-avatar {
        width: 92px;
        height: 92px;
        border-radius: 50%;
        background: rgba(255,255,255,.16);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        font-weight: 700;
        flex-shrink: 0;
        border: 3px solid rgba(255, 255, 255, .35);
        position: relative;
        z-index: 1;
        overflow: hidden;
    }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .profile-identity { min-width: 0; flex: 1; position: relative; z-index: 1; }
    .profile-eyebrow { display: block; color: rgba(255,255,255,.72); font-size: .72rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; margin-bottom: .25rem; }
    .profile-identity h2 { font-size: 1.65rem; line-height: 1.2; overflow-wrap: anywhere; }
    .profile-email { color: rgba(255,255,255,.82); font-size: .88rem; }
    .profile-hero-meta { display: flex; flex-direction: column; align-items: flex-end; gap: .5rem; position: relative; z-index: 1; }
    .profile-state { border: 1px solid rgba(255,255,255,.28); background: rgba(255,255,255,.12); color: #fff; padding: .35rem .75rem; border-radius: 50px; font-size: .72rem; font-weight: 700; }
    .profile-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .05);
        padding: 1.25rem 1.5rem;
    }
    .profile-label { color: #64748B; font-size: .75rem; font-weight: 600; text-transform: uppercase; }
    .profile-value { color: var(--sidebar-bg); font-size: .95rem; font-weight: 600; margin-bottom: 0; }
    .profile-role { background: var(--primary); color: #fff; border-radius: 50px; padding: .35rem .8rem; font-size: .75rem; font-weight: 600; }
    .profile-summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: .75rem; margin-bottom: 1.25rem; }
    .profile-summary-item { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: .8rem 1rem; min-width: 0; }
    .profile-summary-item i { color: var(--primary); font-size: 1.1rem; margin-bottom: .35rem; }
    .profile-summary-value { color: var(--sidebar-bg); font-size: 1rem; font-weight: 700; display: block; }
    .profile-summary-label { color: #64748B; font-size: .7rem; text-transform: uppercase; font-weight: 600; }
    .profile-details-heading { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding-bottom: .9rem; margin-bottom: 1rem; border-bottom: 1px solid #f0f2f5; }
    .profile-details-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .75rem; }
    .profile-detail-item { min-width: 0; padding: .85rem 1rem; border: 1px solid #E2E8F0; border-radius: 8px; background: #fff; }
    .profile-value { overflow-wrap: anywhere; }
    @media (max-width: 575.98px) {
        #page-wrapper main { overflow-y: auto; }
        .profile-page { height: auto; overflow: visible; }
        .profile-hero { padding: 1.1rem; gap: .85rem; align-items: flex-start; flex-wrap: wrap; }
        .profile-avatar { width: 64px; height: 64px; font-size: 1.25rem; }
        .profile-identity { flex: 1 1 calc(100% - 85px); }
        .profile-identity h2 { font-size: 1.25rem; }
        .profile-hero-meta { flex-direction: row; align-items: center; margin-left: 4.7rem; }
        .profile-summary { grid-template-columns: 1fr; }
        .profile-details-grid { grid-template-columns: 1fr; }
        .profile-card { padding: 1rem; }
        .profile-details-heading { align-items: flex-start; }
    }
</style>
@endpush

@section('content')
@php
    $nombre = $usuario->nombre ?? $usuario->name ?? 'Usuario';
    $usuarioNombre = $usuario->usuario ?? $usuario->email ?? 'No disponible';
    $correo = $usuario->email ?? 'No disponible';
    $rol = $usuario->rol ?? 'usuario';
    $partesNombre = preg_split('/\s+/', trim($nombre), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $iniciales = strtoupper(substr($partesNombre[0] ?? 'U', 0, 1).substr($partesNombre[1] ?? '', 0, 1));
    $estadoCuenta = $usuario->deleted_at ? 'Inactiva' : 'Activa';
    $correoVerificado = $usuario->email_verified_at ? 'Verificado' : 'Pendiente';
@endphp

<div class="container-fluid profile-page">
    <div class="profile-hero mb-3">
        <div class="profile-avatar">
            @if($usuario->foto_referencia)
                <img src="{{ asset('storage/' . $usuario->foto_referencia) }}" alt="Foto de perfil de {{ $nombre }}">
            @else
                {{ $iniciales }}
            @endif
        </div>
        <div class="profile-identity">
            <span class="profile-eyebrow">Mi cuenta GymX</span>
            <h2 class="mb-1 font-weight-bold">{{ $nombre }}</h2>
            <p class="profile-email mb-0"><i class="fas fa-envelope mr-2"></i>{{ $correo }}</p>
        </div>
        <div class="profile-hero-meta">
            <span class="profile-role"><i class="fas fa-user-tag mr-1"></i>{{ ucfirst($rol) }}</span>
            <span class="profile-state"><i class="fas fa-shield-alt mr-1"></i>{{ $estadoCuenta }}</span>
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

        <div class="profile-details-heading">
            <div>
                <h5 class="mb-1 font-weight-bold" style="color:var(--sidebar-bg);">Información personal</h5>
                <p class="text-muted small mb-0">Datos de la cuenta que inició sesión.</p>
            </div>
            <i class="fas fa-user-circle fa-2x text-primary"></i>
        </div>

        <div class="profile-details-grid">
            <div class="profile-detail-item">
                <span class="profile-label d-block mb-1">Nombre completo</span>
                <p class="profile-value">{{ $nombre }}</p>
            </div>
            <div class="profile-detail-item">
                <span class="profile-label d-block mb-1">Usuario o correo</span>
                <p class="profile-value">{{ $usuarioNombre }}</p>
            </div>
            <div class="profile-detail-item">
                <span class="profile-label d-block mb-1">Correo electrónico</span>
                <p class="profile-value">{{ $correo }}</p>
            </div>
            <div class="profile-detail-item">
                <span class="profile-label d-block mb-1">Rol</span>
                <span class="profile-role">{{ ucfirst($rol) }}</span>
            </div>
            <div class="profile-detail-item">
                <span class="profile-label d-block mb-1">Miembro desde</span>
                <p class="profile-value">{{ optional($usuario->created_at)->format('d/m/Y') ?? 'No disponible' }}</p>
            </div>
            <div class="profile-detail-item">
                <span class="profile-label d-block mb-1">Última actualización</span>
                <p class="profile-value">{{ optional($usuario->updated_at)->format('d/m/Y') ?? 'No disponible' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
