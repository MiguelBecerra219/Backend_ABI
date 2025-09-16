@extends('tablar::page')

@section('title', 'Editar Framework')

@section('content')
    <!-- Encabezado de la página -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('frameworks.index') }}">Frameworks</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar Framework</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-success" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                            <path d="M16 5l3 3"/>
                        </svg>
                        Editar Framework
                        <span class="badge bg-green-lt ms-2">#{{ $framework->id }}</span>
                    </h2>
                    <p class="text-muted">Modifica los detalles del framework "{{ $framework->name }}"</p>
                </div>
                
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('frameworks.show', $framework->id) }}" class="btn btn-outline-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="2"/>
                                <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                            </svg>
                            Ver Detalles
                        </a>
                        <a href="{{ route('frameworks.index') }}" class="btn btn-outline-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <polyline points="15 6 9 12 15 18"/>
                            </svg>
                            Volver a la Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cuerpo de la página -->
    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar','display_alert'))
                @include('tablar::common.alert')
            @endif
            
            <div class="row">
                <div class="col-12">
                    <!-- Card info -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="card-title mb-1">{{ $framework->name }}</h3>
                                    <div class="text-muted">
                                        <small>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="9"/>
                                                <polyline points="12 7 12 12 15 15"/>
                                            </svg>
                                            Creado {{ $framework->created_at?->diffForHumans() }}
                                        </small>
                                        @if($framework->updated_at && $framework->updated_at != $framework->created_at)
                                            <small class="ms-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                    <path d="M16 5l3 3"/>
                                                </svg>
                                                Actualizado {{ $framework->updated_at?->diffForHumans() }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    @php
                                        $currentYear = date('Y');
                                        $isCurrent = $framework->start_year <= $currentYear && ($framework->end_year === null || $framework->end_year >= $currentYear);
                                        $isFuture = $framework->start_year > $currentYear;
                                    @endphp
                                    @if($isCurrent)
                                        <span class="badge bg-success-lt fs-6">Vigente</span>
                                    @elseif($isFuture)
                                        <span class="badge bg-info-lt fs-6">Programado</span>
                                    @else
                                        <span class="badge bg-yellow-lt fs-6">Finalizado</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de edición -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                    <path d="M16 5l3 3"/>
                                </svg>
                                Editar Detalles del Framework
                            </h3>
                            <div class="card-actions">
                                <small class="text-muted">Todos los campos marcados con * son obligatorios</small>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <form method="POST" action="{{ route('frameworks.update', $framework->id) }}" id="frameworkForm" role="form" enctype="multipart/form-data">
                                {{ method_field('PATCH') }}
                                @csrf

                                @include('framework.form')

                                <!-- Footer del formulario con acciones -->
                                <div class="form-footer mt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('frameworks.index') }}" class="btn btn-outline-secondary btn-cancel">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="18" y1="6" x2="6" y2="18"/>
                                                <line x1="6" y1="6" x2="18" y2="18"/>
                                            </svg>
                                            Cancelar
                                        </a>

                                        <button type="submit" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z"/>
                                                <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-16a2 2 0 0 1 2 -2"/>
                                                <circle cx="12" cy="14" r="2"/>
                                                <polyline points="14 4 14 8 8 8 8 4"/>
                                            </svg>
                                            Guardar cambios
                                        </button>
                                    </div>
                                </div>
                                <!-- /Footer -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startYearInput = document.getElementById('start_year');
            const endYearInput = document.getElementById('end_year');
            if (startYearInput && endYearInput) {
                startYearInput.addEventListener('change', function() {
                    const startYear = parseInt(this.value);
                    if (startYear) {
                        endYearInput.min = startYear;
                        const endYear = parseInt(endYearInput.value);
                        if (endYear && endYear < startYear) endYearInput.value = '';
                    }
                });
            }
            const form = document.getElementById('frameworkForm');
            const cancelBtn = document.querySelector('.btn-cancel');
            let formChanged = false;
            if (form) {
                form.addEventListener('change', () => formChanged = true);
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', function(e) {
                        if (formChanged && !confirm('¿Estás seguro de que quieres cancelar? Los cambios no guardados se perderán.')) {
                            e.preventDefault();
                        }
                    });
                }
            }
        });
    </script>
    @endpush

    @push('css')
    <style>
        .form-label.required::after { content:" *"; color:#d63384; }
        .card { box-shadow:0 .125rem .25rem rgba(0,0,0,.075); transition:box-shadow .15s; }
        .card:hover { box-shadow:0 .5rem 1rem rgba(0,0,0,.15); }
        .form-control:focus { border-color:#0d6efd; box-shadow:0 0 0 .2rem rgba(13,110,253,.25); }
        .badge.fs-6 { font-size:.875rem!important; padding:.375rem .75rem; }
    </style>
    @endpush
@endsection
