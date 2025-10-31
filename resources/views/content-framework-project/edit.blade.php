{{--
    View path: content-framework-project/edit.blade.php.
    Purpose: Presents the edit screen for framework contents using the shared form fragment.
--}}
@extends('tablar::page')

@section('title', 'Editar contenido de framework')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('content-framework-projects.index') }}">Contenidos de framework</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-success" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                            <path d="M16 5l3 3" />
                        </svg>
                        Editar contenido de framework
                        <span class="badge bg-success-lt ms-2">#{{ $contentFrameworkProject->id }}</span>
                    </h2>
                    <p class="text-muted mb-0">Actualiza la información del contenido sin perder la relación con su framework.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('content-framework-projects.show', $contentFrameworkProject) }}" class="btn btn-outline-primary">Ver detalle</a>
                        <a href="{{ route('content-framework-projects.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar.display_alert'))
                @include('tablar::common.alert')
            @endif

            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información del contenido</h3>
                            <div class="card-actions">
                                <small class="text-secondary">Los campos marcados con * son obligatorios</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('content-framework-projects.update', $contentFrameworkProject) }}" novalidate>
                                @csrf
                                @method('PATCH')
                                @include('content-framework-project.form', [
                                    'contentFrameworkProject' => $contentFrameworkProject,
                                    'frameworks' => $frameworks ?? null,
                                    'prefw' => null,
                                ])
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
