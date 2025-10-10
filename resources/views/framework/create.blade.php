{{--
    View path: framework/create.blade.php.
    Purpose: Renders the create.blade view for the Framework module.
    This template does not rely on dynamic variables.
    Included partials or components: framework.form, tablar::common.alert.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Crear Framework')

@section('content')
    <!-- Encabezado de la página -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Navegación breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('frameworks.index') }}">Frameworks</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear Nuevo</li>
                        </ol>
                    </nav>
                    <!-- Título principal -->
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <line x1="9" y1="9" x2="15" y2="9"/>
                            <line x1="9" y1="15" x2="15" y2="15"/>
                        </svg>
                        Crear Nuevo Framework
                    </h2>
                    <p class="text-muted">Crea un nuevo marco curricular para organizar el contenido educativo</p>
                </div>
                
                <!-- Botones de acción -->
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Detalles del Framework
                            </h3>
                            <div class="card-actions">
                                <small class="text-muted">Todos los campos marcados con * son obligatorios</small>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            {{-- Form element sends the captured data to the specified endpoint. --}}
                            <form method="POST" action="{{ route('frameworks.store') }}" id="frameworkForm" role="form" enctype="multipart/form-data">
                                @csrf
                                @include('framework.form')
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
            // Validación en tiempo real para el año de inicio
            const startYearInput = document.getElementById('start_year');
            const endYearInput = document.getElementById('end_year');
            
            if (startYearInput && endYearInput) {
                startYearInput.addEventListener('change', function() {
                    const startYear = parseInt(this.value);
                    if (startYear) {
                        endYearInput.min = startYear;
                        const endYear = parseInt(endYearInput.value);
                        if (endYear && endYear < startYear) {
                            endYearInput.value = '';
                        }
                    }
                });
            }
            
            const nameInput = document.getElementById('name');
            if (nameInput) nameInput.focus();
        });
    </script>
    @endpush

    @push('css')
    <style>
        .form-label.required::after { content: " *"; color: #d63384; }
        .card { box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); transition: box-shadow .15s; }
        .card:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.15); }
        .form-control:focus { border-color:#0d6efd; box-shadow:0 0 0 .2rem rgba(13,110,253,.25);}
    </style>
    @endpush
@endsection
