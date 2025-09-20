@extends('tablar::page')

@section('title', 'Editar Contenido del Framework')

@section('content')
    <!-- Encabezado -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Migas -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('frameworks.index') }}">Frameworks</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('content-framework-projects.index') }}">Contenidos del Framework</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>

                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-success" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                            <path d="M16 5l3 3"/>
                        </svg>
                        Editar Contenido del Framework
                        <span class="badge bg-green-lt ms-2">#{{ $contentFrameworkProject->id }}</span>
                    </h2>
                    <p class="text-muted">Modifica los datos del contenido y su relación con un framework.</p>
                </div>

                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('content-framework-projects.show', $contentFrameworkProject->id) }}" class="btn btn-outline-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="2"/>
                                <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                            </svg>
                            Ver Detalles
                        </a>
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

    <!-- Cuerpo -->
    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar','display_alert'))
                @include('tablar::common.alert')
            @endif

            <div class="row">
                <div class="col-12">
                    <!-- Tarjeta info rápida -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="card-title mb-1">{{ $contentFrameworkProject->name }}</h3>
                                    <div class="text-muted">
                                        <small>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="9"/>
                                                <polyline points="12 7 12 12 15 15"/>
                                            </svg>
                                            Creado {{ $contentFrameworkProject->created_at?->diffForHumans() }}
                                        </small>
                                        @if($contentFrameworkProject->updated_at && $contentFrameworkProject->updated_at != $contentFrameworkProject->created_at)
                                            <small class="ms-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                    <path d="M16 5l3 3"/>
                                                </svg>
                                                Actualizado {{ $contentFrameworkProject->updated_at?->diffForHumans() }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    @if($contentFrameworkProject->framework)
                                        <a class="badge bg-azure-lt text-decoration-none" href="{{ route('frameworks.show', $contentFrameworkProject->framework_id) }}">
                                            Framework: {{ Str::limit($contentFrameworkProject->framework->name, 28) }}
                                        </a>

                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                    <path d="M16 5l3 3"/>
                                </svg>
                                Editar Detalles del Contenido
                            </h3>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('content-framework-projects.update', $contentFrameworkProject->id) }}" id="cfpForm" role="form">
                                @method('PATCH')
                                @csrf
                                @include('content-framework-project.form', [
                                    'contentFrameworkProject' => $contentFrameworkProject,
                                    'prefw' => null,
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
        .badge.fs-6 { font-size:.875rem!important; padding:.375rem .75rem; }
    </style>
    @endpush
@endsection