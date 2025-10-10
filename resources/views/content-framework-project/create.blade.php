{{--
    View path: content-framework-project/create.blade.php.
    Purpose: Renders the create.blade view for the Content Framework Project module.
    Expected variables within this template: $contentFrameworkProject, $frameworks, $prefw.
    Included partials or components: content-framework-project.form, tablar::common.alert.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Crear Contenido del Framework')

@section('content')
    <!-- Encabezado -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Migas -->
                    {{-- Breadcrumb sequence clarifies the navigation path to this creation screen. --}}
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            {{-- Link back to the application home in case the user wants to abort. --}}
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            {{-- Shortcut to the frameworks catalog for context. --}}
                            <li class="breadcrumb-item"><a href="{{ route('frameworks.index') }}">Frameworks</a></li>
                            {{-- Link returns to the content listing without creating a new record. --}}
                            <li class="breadcrumb-item"><a href="{{ route('content-framework-projects.index') }}">Contenidos del Framework</a></li>
                            {{-- Active crumb highlights the current "create" step. --}}
                            <li class="breadcrumb-item active" aria-current="page">Crear</li>
                        </ol>
                    </nav>

                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <line x1="12" y1="8" x2="12" y2="16"/>
                            <line x1="8" y1="12" x2="16" y2="12"/>
                        </svg>
                        Crear Contenido del Framework
                    </h2>
                    <p class="text-muted">Registra un nuevo contenido y relaciónalo con un framework.</p>
                </div>

                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        {{-- Secondary action returns to the list without storing a new record. --}}
                        <a href="{{ route('content-framework-projects.index') }}" class="btn btn-outline-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 6 9 12 15 18"/>
                            </svg>
                            Volver a la Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cuerpo  hola -->
    <div class="page-body">
                <div class="container-xl">
            @if(config('tablar','display_alert'))
                @include('tablar::common.alert')
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            {{-- Header introduces the inputs needed to register the content. --}}
                            <h3 class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                </svg>
                                Detalles del Contenido
                            </h3>
                            <div class="card-actions">
                                {{-- Reminder emphasises which fields cannot be left blank. --}}
                                <small class="text-muted">Los campos marcados con * son obligatorios</small>
                            </div>
                        </div>

                        <div class="card-body">
                            {{-- Form element sends the captured data to the specified endpoint. --}}
                            <form method="POST" action="{{ route('content-framework-projects.store') }}" id="cfpForm" role="form">
                                @csrf
                                @php
                                    // Para preseleccionar framework cuando se llega desde /frameworks/{id}
                                    $prefw = $prefw ?? request('framework_id'); // ?framework_id=XX
                                @endphp
                                {{-- Shared partial contains the reusable fields for this resource. --}}
                                @include('content-framework-project.form', [
                                    'contentFrameworkProject' => $contentFrameworkProject ?? null,
                                    'prefw' => $prefw,
                                    'frameworks' => $frameworks ?? null
                                ])
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('css')
    <style>
        .form-label.required::after { content:" *"; color:#d63384; }
        .card { box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); transition: box-shadow .15s; }
        .card:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.15); }
        .form-control:focus { border-color:#0d6efd; box-shadow:0 0 0 .2rem rgba(13,110,253,.25); }
    </style>
    @endpush
@endsection
