{{-- Detail page presenting a single department and its related cities. --}}
@extends('tablar::page')

@section('title', 'Detalle del departamento')

@section('content')
    {{-- Header provides breadcrumb navigation and quick access actions. --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departamentos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $department->name }}</li>
                        </ol>
                    </nav>
                    <h2 class="page-title">{{ $department->name }}</h2>
                    <p class="text-muted mb-0">Consulta la información detallada del departamento y las ciudades asociadas.</p>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        {{-- Shortcut to edit the current department. --}}
                        <a href="{{ route('departments.edit', $department) }}" class="btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                <path d="M16 5l3 3" />
                            </svg>
                            Editar
                        </a>
                        {{-- Return link to the department index. --}}
                        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                            Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content area showing general department metadata and associated cities. --}}
    <div class="page-body">
        <div class="container-xl">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información general</h3>
                        </div>
                        <div class="card-body">
                            {{-- Definition list highlights key attributes for the department. --}}
                            <dl class="row mb-0">
                                <dt class="col-sm-5 text-muted">Nombre</dt>
                                <dd class="col-sm-7">{{ $department->name }}</dd>

                                <dt class="col-sm-5 text-muted">Ciudades registradas</dt>
                                <dd class="col-sm-7">
                                    <span class="badge bg-indigo-lt">{{ $department->cities->count() }}</span>
                                </dd>

                                <dt class="col-sm-5 text-muted">Creado</dt>
                                <dd class="col-sm-7">{{ $department->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>

                                <dt class="col-sm-5 text-muted">Última actualización</dt>
                                <dd class="col-sm-7">{{ $department->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Ciudades asociadas</h3>
                        </div>
                        <div class="card-body">
                            {{-- Conditional block lists cities or explains that none exist. --}}
                            @if($department->cities->isEmpty())
                                <p class="text-muted mb-0">Este departamento todavía no tiene ciudades registradas.</p>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($department->cities as $city)
                                        {{-- Each city entry links to its dedicated detail page. --}}
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $city->name }}</span>
                                            <a href="{{ route('cities.show', $city) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
