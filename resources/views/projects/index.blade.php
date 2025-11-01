{{--
    View path: projects/index.blade.php.
    Purpose: Displays the project proposals available to the authenticated user.
--}}
@extends('tablar::page')

@section('title', 'Gestión de Proyectos')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Proyectos</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 21v-13l8 -4l8 4v13" />
                            <path d="M12 13l8 -4" />
                            <path d="M12 13l-8 -4" />
                            <path d="M12 13v8" />
                            <path d="M8 21h8" />
                        </svg>
                        Gestión de Proyectos
                    </h2>
                    <p class="text-muted mb-0">Consulta tus proyectos y registra nuevas ideas.</p>
                </div>
                @if ($isProfessor || $isStudent)
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Nuevo proyecto
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Buscar proyectos</h3>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="search" class="form-label">Título</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="10" cy="10" r="7" />
                                        <line x1="21" y1="21" x2="15" y2="15" />
                                    </svg>
                                </span>
                                <input type="search" id="search" name="search" value="{{ $search }}" class="form-control" placeholder="Título del proyecto">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-lg-2">
                            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                        </div>
                        <div class="col-12 col-md-4 col-lg-2">
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Listado de proyectos</h3>
                    <div class="card-actions">
                        <span class="badge bg-azure">{{ $projects->total() }}</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter align-middle">
                        <thead>
                            <tr>
                                <th class="w-1">ID</th>
                                <th>Título</th>
                                <th>Área temática</th>
                                <th>Estado</th>
                                <th>Profesores</th>
                                <th>Estudiantes</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($projects as $project)
                                <tr>
                                    <td>{{ $project->id }}</td>
                                    <td class="text-break">{{ $project->title }}</td>
                                    <td>{{ $project->thematicArea->name ?? 'Sin área' }}</td>
                                    <td><span class="badge bg-indigo">{{ $project->projectStatus->name ?? 'Sin estado' }}</span></td>
                                    <td>
                                        @forelse ($project->professors as $professor)
                                            <div>{{ $professor->name }} {{ $professor->last_name }}</div>
                                        @empty
                                            <span class="text-secondary">Sin profesores</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @forelse ($project->students as $student)
                                            <div>{{ $student->name }} {{ $student->last_name }}</div>
                                        @empty
                                            <span class="text-secondary">Sin estudiantes</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary btn-sm">Ver</a>
                                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary btn-sm">Editar</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-secondary">No se encontraron proyectos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-secondary">Mostrando {{ $projects->firstItem() ?? 0 }} a {{ $projects->lastItem() ?? 0 }} de {{ $projects->total() }} registros</div>
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
