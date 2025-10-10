{{--
    Dashboard landing page for authenticated users, providing a welcome message
    and quick access cards to frequently used areas of the system.
--}}
@extends('tablar::page')

@section('title', 'Dashboard')

@section('content')
    {{-- Header section introducing the dashboard and greeting the user. --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    {{-- Subheading gives context to the title below. --}}
                    <div class="page-pretitle">
                        Bienvenid@
                    </div>
                    <h2 class="page-title">
                        ABI - Sistema de Gestión
                    </h2>
                    <!--Perfil-->
                </div>
            </div>
        </div>
    </div>

    {{-- Welcome card summarizing the system purpose and current user details. --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="card-title">¡Bienvenido a ABI!</h1>
                    <p class="card-text">
                        Sistema de gestión de proyectos de grado.
                    </p>
                    <div class="mt-4">
                        <p class="text-muted">
                            Usuario conectado: <strong>{{ Auth::user()->name }}</strong>
                        </p>
                        <p class="text-muted">
                            Fecha: <strong>{{ date('d/m/Y H:i') }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick access cards highlighting key areas of the application. --}}
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    {{-- Icon visually reinforces the dashboard navigation item. --}}
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-dashboard" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <circle cx="12" cy="13" r="2"/>
                            <line x1="13.45" y1="11.55" x2="15.5" y2="9.5"/>
                            <path d="M6.4 20a9 9 0 1 1 11.2 0Z"/>
                        </svg>
                    </div>
                    <h5 class="card-title">Dashboard</h5>
                    <p class="card-text">Accede a tu panel de control principal</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    {{-- Each card follows the same structure but describes a different area. --}}
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/>
                        </svg>
                    </div>
                    <h5 class="card-title">Gestión</h5>
                    <p class="card-text">Administra tus recursos y procesos</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <h5 class="card-title">Configuración</h5>
                    <p class="card-text">Personaliza tu experiencia</p>
                </div>
            </div>
        </div>
    </div>

@endsection
