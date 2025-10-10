{{-- View responsible for rendering the creation form for a new department. --}}
@extends('tablar::page')

@section('title', 'Nuevo departamento')

@section('content')
    {{-- Page header with breadcrumb navigation for contextual awareness. --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departamentos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Nuevo</li>
                        </ol>
                    </nav>
                    <h2 class="page-title">Registrar departamento</h2>
                    <p class="text-muted mb-0">Crea un nuevo departamento para asociar ciudades y programas.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main content container holding the department registration form. --}}
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Datos del departamento</h3>
                </div>
                <div class="card-body">
                    {{-- Form submits the department information to the store route. --}}
                    <form action="{{ route('departments.store') }}" method="POST" novalidate>
                        @csrf
                        {{-- Reuse the shared form partial for both create and edit flows. --}}
                        @include('departments.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
