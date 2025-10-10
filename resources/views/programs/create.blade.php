{{--
    View path: programs/create.blade.php.
    Purpose: Renders the create.blade view for the Programs module.
    This template does not rely on dynamic variables.
    Included partials or components: programs.form, tablar::common.alert.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Registrar programa académico')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('programs.index') }}">Programas académicos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Nuevo</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-teal" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20l-8 -4l8 -4l8 4l-8 4" />
                            <path d="M4 12l8 -4l8 4" />
                        </svg>
                        Registrar programa académico
                    </h2>
                    <p class="text-muted mb-0">Ingresa la información del programa bilingüe y relaciónalo con un grupo de investigación.</p>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <a href="{{ route('programs.index') }}" class="btn btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M15 6l-6 6l6 6" />
                        </svg>
                        Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar','display_alert'))
                @include('tablar::common.alert')
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información del programa</h3>
                    <div class="card-actions">
                        <small class="text-muted">Campos marcados con * son obligatorios</small>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Form element sends the captured data to the specified endpoint. --}}
                    <form method="POST" action="{{ route('programs.store') }}">
                        @csrf
                        @include('programs.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
