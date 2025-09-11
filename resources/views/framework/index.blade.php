@extends('tablar::page')

@section('title')
    Gestión de Frameworks
@endsection

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
                            <li class="breadcrumb-item active" aria-current="page">Frameworks</li>
                        </ol>
                    </nav>
                    <!-- Título principal -->
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <line x1="9" y1="9" x2="15" y2="9"/>
                            <line x1="9" y1="15" x2="15" y2="15"/>
                        </svg>
                        Gestión de Frameworks
                        <span class="badge bg-azure ms-2">{{ $frameworks->total() }}</span>
                    </h2>
                    <p class="text-muted">Administra los marcos curriculares y estructuras de contenido educativo</p>
                </div>
                
                <!-- Botón para crear nuevo framework -->
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('frameworks.create') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Nuevo Framework
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cuerpo principal de la página -->
    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar','display_alert'))
                @include('tablar::common.alert')
            @endif

            {{-- ... (todo lo anterior se mantiene igual) --}}

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        {{-- Pestañas y filtros: sin cambios --}}

                        <!-- Contenido de la tabla principal -->
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap">
                                <thead>
                                    <tr>
                                        <th class="w-1">
                                            <input class="form-check-input m-0 align-middle" type="checkbox" aria-label="Seleccionar todos" id="select-all">
                                        </th>
                                        <th class="w-1">#</th>
                                        <th>
                                            <a href="#" class="table-sort text-decoration-none" data-sort="name">
                                                Nombre del Framework
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm ms-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <polyline points="6 15 12 9 18 15"/>
                                                </svg>
                                            </a>
                                        </th>
                                        <th>Descripción</th>
                                        <th>Período de Vigencia</th>
                                        <th>Estado</th>
                                        <th>Duración</th>
                                        <th class="w-1">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
    @php($currentYear = (int) date('Y'))
    @forelse ($frameworks as $framework)
                                        <tr class="framework-row {{ !$framework->is_active ? 'table-secondary' : '' }}" 
                                            data-status="{{ $framework->is_active ? 'active' : 'inactive' }}"
                                            data-current="{{ ($framework->start_year <= $currentYear && ($framework->end_year === null || $framework->end_year >= $currentYear)) ? 'true' : 'false' }}">
                                            
                                            <td>
                                                <input class="form-check-input m-0 align-middle" type="checkbox" value="{{ $framework->id }}" aria-label="Seleccionar framework">
                                            </td>
                                            
                                            <td><span class="text-muted">{{ ++$i }}</span></td>
                                            
                                            <!-- Nombre + icono de enlace si existe -->
                                            <td>
                                                <div class="d-flex py-1 align-items-center">
                                                    <div class="avatar me-2 bg-blue-lt">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                            <line x1="9" y1="9" x2="15" y2="9"/>
                                                            <line x1="9" y1="15" x2="15" y2="15"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <div class="font-weight-medium">
                                                            {{ $framework->name }}
                                                            @if(!empty($framework->link))
                                                                <a href="{{ $framework->link }}" target="_blank" rel="noopener noreferrer" class="ms-2 text-muted" title="Abrir enlace">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path d="M10 14a3.5 3.5 0 0 0 5 0l3-3a3.5 3.5 0 1 0 -5 -5l-.5 .5"/>
                                                                        <path d="M14 10a3.5 3.5 0 0 0 -5 0l-3 3a3.5 3.5 0 1 0 5 5l.5 -.5"/>
                                                                    </svg>
                                                                </a>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted">
                                                            <small>
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <circle cx="12" cy="12" r="9"/>
                                                                    <polyline points="12 7 12 12 15 15"/>
                                                                </svg>
                                                                Creado {{ $framework->created_at?->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- (Resto de columnas sin cambios) -->
                                            <td>
                                                <div class="text-truncate" style="max-width: 250px;" title="{{ $framework->description }}">
                                                    {{ Str::limit($framework->description, 80) }}
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-azure-lt me-1">{{ $framework->start_year }}</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-muted mx-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                                        <polyline points="12 5 19 12 12 19"/>
                                                    </svg>
                                                    @if($framework->end_year)
                                                        <span class="badge bg-azure-lt">{{ $framework->end_year }}</span>
                                                    @else
                                                        <span class="badge bg-green-lt">Presente</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Estado y Duración: igual que tu archivo --}}
                                            {{-- ... --}}

                                            <!-- Acciones -->
                                            <td>
                                                <div class="btn-list flex-nowrap">
                                                    <a href="{{ route('frameworks.show', $framework->id) }}" class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="2"/>
                                                            <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('frameworks.edit', $framework->id) }}" class="btn btn-sm btn-outline-success" title="Editar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                            <path d="M16 5l3 3"/>
                                                        </svg>
                                                    </a>

                                                    <div class="dropdown">
                                                        <button class="btn btn-sm dropdown-toggle align-text-top" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="1"/>
                                                                <circle cx="12" cy="5" r="1"/>
                                                                <circle cx="12" cy="19" r="1"/>
                                                            </svg>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <h6 class="dropdown-header">Acciones Principales</h6>

                                                            <a class="dropdown-item" href="{{ route('frameworks.show', $framework->id) }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <circle cx="12" cy="12" r="2"/>
                                                                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                                                </svg>
                                                                Ver Detalles
                                                            </a>

                                                            <a class="dropdown-item" href="{{ route('frameworks.edit', $framework->id) }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                                    <path d="M16 5l3 3"/>
                                                                </svg>
                                                                Editar Framework
                                                            </a>

                                                            @if(!empty($framework->link))
                                                                <a class="dropdown-item" href="{{ $framework->link }}" target="_blank" rel="noopener noreferrer">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path d="M10 14a3.5 3.5 0 0 0 5 0l3-3a3.5 3.5 0 1 0 -5 -5l-.5 .5"/>
                                                                        <path d="M14 10a3.5 3.5 0 0 0 -5 0l-3 3a3.5 3.5 0 1 0 5 5l.5 -.5"/>
                                                                    </svg>
                                                                    Abrir enlace
                                                                </a>
                                                            @endif

                                                            <div class="dropdown-divider"></div>
                                                            {{-- Resto de acciones (activar/desactivar, borrar) quedan tal cual --}}
                                                            {{-- ... --}}
                                                            <form id="toggle-form-{{ $framework->id }}" action="{{ route('frameworks.destroy', $framework->id) }}" method="POST" style="display: none;">
                                                                @csrf
                                                                @method('PATCH')
                                                            </form>
                                                            <form id="delete-form-{{ $framework->id }}" action="{{ route('frameworks.destroy', $framework->id) }}" method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        {{-- Bloque vacío: sin cambios --}}
                                        {{-- ... --}}
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación: sin cambios --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal + scripts + css: sin cambios relevantes --}}
@endsection
