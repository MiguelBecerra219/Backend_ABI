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
            <!-- Incluir alertas si están configuradas -->
            @if(config('tablar','display_alert'))
                @include('tablar::common.alert')
            @endif

            <!-- Tarjetas de estadísticas -->
            <div class="row mb-4">
                <!-- Total de frameworks -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total de Frameworks</div>
                                <div class="ms-auto lh-1">
                                    <div class="dropdown">
                                        <a class="dropdown-toggle text-muted" href="#" data-bs-toggle="dropdown">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="1"/>
                                                <circle cx="12" cy="5" r="1"/>
                                                <circle cx="12" cy="19" r="1"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="h1 mb-3">{{ $frameworks->total() }}</div>
                            <div class="d-flex mb-2">
                                <div>Registros totales</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Frameworks activos -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Frameworks Activos</div>
                            </div>
                            @php
                                // Contar frameworks activos
                                $activeCount = $frameworks->where('is_active', true)->count();
                            @endphp
                            <div class="h1 mb-3 text-success">{{ $activeCount }}</div>
                            <div class="d-flex mb-2">
                                <div>En funcionamiento</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Frameworks vigentes actualmente -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Vigentes Actualmente</div>
                            </div>
                            @php
                                // Calcular frameworks que están vigentes en el año actual
                                $currentYear = date('Y');
                                $currentCount = $frameworks->filter(function($framework) use ($currentYear) {
                                    return $framework->start_year <= $currentYear && 
                                           ($framework->end_year === null || $framework->end_year >= $currentYear);
                                })->count();
                            @endphp
                            <div class="h1 mb-3 text-info">{{ $currentCount }}</div>
                            <div class="d-flex mb-2">
                                <div>En período válido</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Frameworks inactivos -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Frameworks Inactivos</div>
                            </div>
                            @php
                                // Contar frameworks inactivos
                                $inactiveCount = $frameworks->where('is_active', false)->count();
                            @endphp
                            <div class="h1 mb-3 text-warning">{{ $inactiveCount }}</div>
                            <div class="d-flex mb-2">
                                <div>Deshabilitados</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de filtros -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                </svg>
                                Filtros y Búsqueda
                            </h3>
                            <!-- Botón para colapsar/expandir los filtros -->
                            <div class="card-actions">
                                <a href="#" class="btn-action" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="{{ ($search || $status || $year) ? 'true' : 'false' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Panel colapsable de filtros -->
                        <div class="collapse {{ ($search || $status || $year) ? 'show' : '' }}" id="filterCollapse">
                            <div class="card-body">
                                <form method="GET" action="{{ route('frameworks.index') }}" class="row g-3" id="filterForm">
                                    <!-- Campo de búsqueda -->
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="11" cy="11" r="8"/>
                                                <path d="m21 21-4.35-4.35"/>
                                            </svg>
                                            Buscar Framework
                                        </label>
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="Nombre o descripción..." value="{{ $search }}" id="searchInput">
                                            @if($search)
                                                <!-- Botón para limpiar búsqueda -->
                                                <a href="{{ route('frameworks.index') }}" class="input-group-text text-decoration-none" title="Limpiar búsqueda">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Filtro por estado -->
                                    <div class="col-md-2">
                                        <label class="form-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="3"/>
                                                <path d="M12 1v6m0 6v6"/>
                                            </svg>
                                            Estado
                                        </label>
                                        <select name="status" class="form-select">
                                            <option value="">Todos los estados</option>
                                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>🟢 Activos</option>
                                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>🔴 Inactivos</option>
                                        </select>
                                    </div>

                                    <!-- Filtro por año -->
                                    <div class="col-md-2">
                                        <label class="form-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                <line x1="16" y1="2" x2="16" y2="6"/>
                                                <line x1="8" y1="2" x2="8" y2="6"/>
                                                <line x1="3" y1="10" x2="21" y2="10"/>
                                            </svg>
                                            Año de Vigencia
                                        </label>
                                        <input type="number" name="year" class="form-control" placeholder="Ej: {{ date('Y') }}" value="{{ $year }}" min="1900" max="{{ date('Y') + 50 }}">
                                    </div>

                                    <!-- Selector de elementos por página (máximo 10) -->
                                    <div class="col-md-2">
                                        <label class="form-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                <line x1="9" y1="9" x2="15" y2="9"/>
                                                <line x1="9" y1="15" x2="15" y2="15"/>
                                            </svg>
                                            Mostrar
                                        </label>
                                        <select name="per_page" class="form-select" onchange="this.form.submit()">
                                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5 por página</option>
                                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 por página</option>
                                        </select>
                                    </div>

                                    <!-- Botones de acción -->
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="btn-group w-100" role="group">
                                            <!-- Botón aplicar filtros -->
                                            <button type="submit" class="btn btn-primary" title="Aplicar filtros">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                                </svg>
                                            </button>
                                            @if($search || $status || $year)
                                                <!-- Botón limpiar filtros -->
                                                <a href="{{ route('frameworks.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <line x1="4" y1="7" x2="20" y2="7"/>
                                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla principal con pestañas -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- Pestañas de navegación -->
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                                <!-- Pestaña todos los frameworks -->
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-all" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab" data-filter="all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                            <line x1="9" y1="9" x2="15" y2="9"/>
                                            <line x1="9" y1="15" x2="15" y2="15"/>
                                        </svg>
                                        Todos (<span id="count-all">{{ $frameworks->total() }}</span>)
                                    </a>
                                </li>
                                <!-- Pestaña frameworks activos -->
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-active" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1" data-filter="active">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="M9 12l2 2l4 -4"/>
                                        </svg>
                                        Activos (<span id="count-active">{{ $activeCount }}</span>)
                                    </a>
                                </li>
                                <!-- Pestaña frameworks inactivos -->
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-inactive" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1" data-filter="inactive">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-warning" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="9"/>
                                            <line x1="9" y1="9" x2="15" y2="15"/>
                                            <line x1="15" y1="9" x2="9" y2="15"/>
                                        </svg>
                                        Inactivos (<span id="count-inactive">{{ $inactiveCount }}</span>)
                                    </a>
                                </li>
                                <!-- Pestaña frameworks vigentes -->
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-current" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1" data-filter="current">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-info" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="9"/>
                                            <polyline points="12 7 12 12 15 15"/>
                                        </svg>
                                        Vigentes (<span id="count-current">{{ $currentCount }}</span>)
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Contenido de la tabla principal -->
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap">
                                <thead>
                                    <tr>
                                        <!-- Checkbox para seleccionar todos -->
                                        <th class="w-1">
                                            <input class="form-check-input m-0 align-middle" type="checkbox" aria-label="Seleccionar todos" id="select-all">
                                        </th>
                                        <th class="w-1">#</th>
                                        <!-- Columna nombre con ordenamiento -->
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
                                    @forelse ($frameworks as $framework)
                                        <!-- Fila de framework con clases para filtrado -->
                                        <tr class="framework-row {{ !$framework->is_active ? 'table-secondary' : '' }}" 
                                            data-status="{{ $framework->is_active ? 'active' : 'inactive' }}"
                                            data-current="{{ ($framework->start_year <= $currentYear && ($framework->end_year === null || $framework->end_year >= $currentYear)) ? 'true' : 'false' }}">
                                            
                                            <!-- Checkbox de selección -->
                                            <td>
                                                <input class="form-check-input m-0 align-middle" type="checkbox" value="{{ $framework->id }}" aria-label="Seleccionar framework">
                                            </td>
                                            
                                            <!-- Número de fila -->
                                            <td>
                                                <span class="text-muted">{{ ++$i }}</span>
                                            </td>
                                            
                                            <!-- Información del framework -->
                                            <td>
                                                <div class="d-flex py-1 align-items-center">
                                                    <!-- Avatar con icono -->
                                                    <div class="avatar me-2 bg-blue-lt">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                            <line x1="9" y1="9" x2="15" y2="9"/>
                                                            <line x1="9" y1="15" x2="15" y2="15"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <!-- Nombre del framework -->
                                                        <div class="font-weight-medium">{{ $framework->name }}</div>
                                                        <!-- Fecha de creación -->
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
                                            
                                            <!-- Descripción truncada -->
                                            <td>
                                                <div class="text-truncate" style="max-width: 250px;" title="{{ $framework->description }}">
                                                    {{ Str::limit($framework->description, 80) }}
                                                </div>
                                            </td>
                                            
                                            <!-- Período de vigencia -->
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
                                            
                                            <!-- Estado del framework -->
                                            <td>
                                                @if($framework->is_active)
                                                    @php
                                                        $currentYear = date('Y');
                                                        $isCurrent = $framework->start_year <= $currentYear && ($framework->end_year === null || $framework->end_year >= $currentYear);
                                                        $isFuture = $framework->start_year > $currentYear;
                                                    @endphp
                                                    
                                                    @if($isCurrent)
                                                        <!-- Framework vigente -->
                                                        <span class="badge bg-success-lt">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="9"/>
                                                                <path d="M9 12l2 2l4 -4"/>
                                                            </svg>
                                                            Vigente
                                                        </span>
                                                    @elseif($isFuture)
                                                        <!-- Framework futuro -->
                                                        <span class="badge bg-info-lt">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="9"/>
                                                                <polyline points="12 7 12 12 15 15"/>
                                                            </svg>
                                                            Programado
                                                        </span>
                                                    @else
                                                        <!-- Framework finalizado -->
                                                        <span class="badge bg-yellow-lt">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="9"/>
                                                                <path d="M12 8v4"/>
                                                                <path d="M12 16h.01"/>
                                                            </svg>
                                                            Finalizado
                                                        </span>
                                                    @endif
                                                @else
                                                    <!-- Framework inactivo -->
                                                    <span class="badge bg-red-lt">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="9"/>
                                                            <line x1="9" y1="9" x2="15" y2="15"/>
                                                            <line x1="15" y1="9" x2="9" y2="15"/>
                                                        </svg>
                                                        Inactivo
                                                    </span>
                                                @endif
                                            </td>
                                            
                                            <!-- Duración con barra de progreso -->
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        // Calcular duración y progreso
                                                        $currentYear = date('Y');
                                                        $endYear = $framework->end_year ?? $currentYear;
                                                        $totalYears = $endYear - $framework->start_year + 1;
                                                        $elapsedYears = min($currentYear - $framework->start_year + 1, $totalYears);
                                                        $progress = $totalYears > 0 ? ($elapsedYears / $totalYears) * 100 : 0;
                                                        $duration = $totalYears;
                                                    @endphp
                                                    
                                                    <!-- Barra de progreso pequeña -->
                                                    <div class="progress progress-sm flex-fill me-2" style="width: 60px;">
                                                        <div class="progress-bar bg-primary" style="width: {{ min($progress, 100) }}%" role="progressbar"></div>
                                                    </div>
                                                    
                                                    <!-- Texto de duración -->
                                                    <span class="text-muted small">{{ $duration }} {{ $duration == 1 ? 'año' : 'años' }}</span>
                                                </div>
                                            </td>
                                            
                                            <!-- Botones de acciones -->
                                            <td>
                                                <div class="btn-list flex-nowrap">
                                                    <!-- Botón ver -->
                                                    <a href="{{ route('frameworks.show', $framework->id) }}" class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="2"/>
                                                            <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                                        </svg>
                                                    </a>
                                                    
                                                    <!-- Botón editar -->
                                                    <a href="{{ route('frameworks.edit', $framework->id) }}" class="btn btn-sm btn-outline-success" title="Editar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                            <path d="M16 5l3 3"/>
                                                        </svg>
                                                    </a>

                                                    <!-- Dropdown para más acciones -->
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
                                                            
                                                            <!-- Ver detalles -->
                                                            <a class="dropdown-item" href="{{ route('frameworks.show', $framework->id) }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <circle cx="12" cy="12" r="2"/>
                                                                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                                                </svg>
                                                                Ver Detalles
                                                            </a>
                                                            
                                                            <!-- Editar framework -->
                                                            <a class="dropdown-item" href="{{ route('frameworks.edit', $framework->id) }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                                    <path d="M16 5l3 3"/>
                                                                </svg>
                                                                Editar Framework
                                                            </a>

                                                            <div class="dropdown-divider"></div>
                                                            <h6 class="dropdown-header">Cambiar Estado</h6>
                                                            
                                                            <!-- Cambiar estado activo/inactivo -->
                                                            <a class="dropdown-item" href="#" onclick="toggleStatus({{ $framework->id }}, '{{ $framework->is_active ? 'desactivar' : 'activar' }}')">
                                                                @if($framework->is_active)
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon text-warning" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                        <line x1="3" y1="3" x2="21" y2="21"/>
                                                                        <path d="M10.584 10.587a2 2 0 0 0 2.828 2.83"/>
                                                                        <path d="M9.363 5.365a9.466 9.466 0 0 1 2.637 -.365c4 0 7.333 2.333 10 7c-.778 1.361 -1.612 2.524 -2.503 3.488m-2.14 1.861c-1.631 1.1 -3.415 1.651 -5.357 1.651c-4 0 -7.333 -2.333 -10 -7c1.369 -2.395 2.913 -4.175 4.632 -5.341"/>
                                                                    </svg>
                                                                    Desactivar Framework
                                                                @else
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon text-success" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <circle cx="12" cy="12" r="2"/>
                                                                        <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                                                    </svg>
                                                                    Activar Framework
                                                                @endif
                                                            </a>

                                                            <div class="dropdown-divider"></div>
                                                            <h6 class="dropdown-header text-danger">Zona Peligrosa</h6>
                                                            
                                                            <!-- Eliminar framework -->
                                                            <a class="dropdown-item text-red" href="#" onclick="confirmDelete({{ $framework->id }}, '{{ $framework->name }}')">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                    <line x1="4" y1="7" x2="20" y2="7"/>
                                                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                                </svg>
                                                                Eliminar Framework
                                                            </a>

                                                            <!-- Formularios ocultos para acciones POST -->
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
                                        <!-- Mensaje cuando no hay frameworks -->
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="empty">
                                                    <!-- Icono de estado vacío -->
                                                    <div class="empty-img">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="128" height="128" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="3" y="4" width="18" height="16" rx="2"/>
                                                            <line x1="7" y1="8" x2="17" y2="8"/>
                                                            <line x1="7" y1="12" x2="17" y2="12"/>
                                                            <line x1="7" y1="16" x2="13" y2="16"/>
                                                        </svg>
                                                    </div>
                                                    <!-- Título del mensaje -->
                                                    <p class="empty-title h3">No se encontraron frameworks</p>
                                                    <!-- Descripción del mensaje -->
                                                    <p class="empty-subtitle text-muted">
                                                        @if($search || $status || $year)
                                                            No hay frameworks que coincidan con los criterios de búsqueda aplicados.
                                                            <br>Intenta ajustar los filtros para obtener más resultados.
                                                        @else
                                                            Aún no has creado ningún framework.
                                                            <br>Comienza creando tu primer marco curricular.
                                                        @endif
                                                    </p>
                                                    <!-- Botones de acción -->
                                                    <div class="empty-action">
                                                        @if($search || $status || $year)
                                                            <!-- Botones cuando hay filtros activos -->
                                                            <div class="btn-group">
                                                                <a href="{{ route('frameworks.index') }}" class="btn btn-outline-primary">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                        <line x1="4" y1="7" x2="20" y2="7"/>
                                                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                                    </svg>
                                                                    Limpiar Filtros
                                                                </a>
                                                                <a href="{{ route('frameworks.create') }}" class="btn btn-primary">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <line x1="12" y1="5" x2="12" y2="19"/>
                                                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                                                    </svg>
                                                                    Crear Framework
                                                                </a>
                                                            </div>
                                                        @else
                                                            <!-- Botón cuando no hay frameworks -->
                                                            <a href="{{ route('frameworks.create') }}" class="btn btn-primary btn-lg">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                                                </svg>
                                                                Crear mi Primer Framework
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        @if($frameworks->hasPages())
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <!-- Información de paginación -->
                                <div class="text-muted">
                                    Mostrando 
                                    <strong>{{ $frameworks->firstItem() }}</strong>
                                    a 
                                    <strong>{{ $frameworks->lastItem() }}</strong>
                                    de 
                                    <strong>{{ $frameworks->total() }}</strong> 
                                    frameworks
                                </div>
                                <!-- Enlaces de paginación -->
                                <div class="d-flex align-items-center">
                                    {!! $frameworks->links('tablar::pagination') !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div class="modal modal-blur fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-title">¿Estás seguro?</div>
                    <div>Esta acción eliminará permanentemente el framework <strong id="frameworkName"></strong> y no se podrá deshacer.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Sí, eliminar</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para actualizar contadores de las pestañas
            function updateTabCounters() {
                const allRows = document.querySelectorAll('.framework-row');
                const activeRows = document.querySelectorAll('.framework-row[data-status="active"]');
                const inactiveRows = document.querySelectorAll('.framework-row[data-status="inactive"]');
                const currentRows = document.querySelectorAll('.framework-row[data-current="true"]');
                
                // Actualizar contadores en las pestañas
                document.getElementById('count-all').textContent = allRows.length;
                document.getElementById('count-active').textContent = activeRows.length;
                document.getElementById('count-inactive').textContent = inactiveRows.length;
                document.getElementById('count-current').textContent = currentRows.length;
            }

            // Funcionalidad de las pestañas con filtrado
            const tabs = document.querySelectorAll('[data-filter]');
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filter = this.getAttribute('data-filter');
                    const rows = document.querySelectorAll('.framework-row');
                    
                    // Mostrar/ocultar filas según el filtro seleccionado
                    rows.forEach(row => {
                        let shouldShow = false;
                        
                        switch(filter) {
                            case 'all':
                                shouldShow = true;
                                break;
                            case 'active':
                                shouldShow = row.getAttribute('data-status') === 'active';
                                break;
                            case 'inactive':
                                shouldShow = row.getAttribute('data-status') === 'inactive';
                                break;
                            case 'current':
                                shouldShow = row.getAttribute('data-current') === 'true';
                                break;
                        }
                        
                        row.style.display = shouldShow ? '' : 'none';
                    });
                    
                    // Actualizar clase activa de las pestañas
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Checkbox para seleccionar todos
            const selectAllCheckbox = document.getElementById('select-all');
            const individualCheckboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    // Solo seleccionar checkboxes de filas visibles
                    const visibleCheckboxes = Array.from(individualCheckboxes).filter(checkbox => {
                        return checkbox.closest('.framework-row').style.display !== 'none';
                    });
                    
                    visibleCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });

                // Actualizar estado del checkbox principal cuando cambian los individuales
                individualCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const visibleCheckboxes = Array.from(individualCheckboxes).filter(checkbox => {
                            return checkbox.closest('.framework-row').style.display !== 'none';
                        });
                        const checkedVisible = visibleCheckboxes.filter(cb => cb.checked);
                        
                        selectAllCheckbox.checked = checkedVisible.length === visibleCheckboxes.length && visibleCheckboxes.length > 0;
                        selectAllCheckbox.indeterminate = checkedVisible.length > 0 && checkedVisible.length < visibleCheckboxes.length;
                    });
                });
            }

            // Búsqueda en tiempo real (opcional)
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    // Enviar formulario después de 500ms de inactividad
                    searchTimeout = setTimeout(() => {
                        if (this.value.length >= 3 || this.value.length === 0) {
                            document.getElementById('filterForm').submit();
                        }
                    }, 500);
                });
            }

            // Inicializar contadores
            updateTabCounters();
        });

        // Función para confirmar eliminación
        function confirmDelete(id, name) {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            document.getElementById('frameworkName').textContent = name;
            
            // Configurar botón de confirmación
            document.getElementById('confirmDeleteBtn').onclick = function() {
                document.getElementById('delete-form-' + id).submit();
            };
            
            modal.show();
        }

        // Función para cambiar estado activo/inactivo
        function toggleStatus(id, action) {
            if (confirm(`¿Estás seguro de que quieres ${action} este framework?`)) {
                document.getElementById('toggle-form-' + id).submit();
            }
        }
    </script>
    @endpush

    @push('css')
    <style>
        /* Estilos personalizados para la tabla */
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }
        
        /* Efecto hover en las filas */
        .framework-row:hover {
            background-color: var(--tblr-bg-surface-secondary) !important;
        }
        
        /* Estilos para las pestañas */
        .nav-tabs .nav-link {
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
        }
        
        .nav-tabs .nav-link.active {
            border-bottom-color: var(--tblr-primary);
            font-weight: 600;
            background-color: transparent;
        }
        
        /* Estilos para badges */
        .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Estilos para subtítulos */
        .subheader {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--tblr-muted);
        }
        
        /* Estilos para tabs en el header */
        .card-header-tabs {
            margin-bottom: 0;
            border-bottom: 1px solid var(--tblr-border-color);
        }
        
        /* Barra de progreso pequeña */
        .progress-sm {
            height: 0.375rem;
            border-radius: 0.25rem;
        }
        
        /* Avatar personalizado */
        .avatar {
            width: 2rem;
            height: 2rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Estado vacío */
        .empty-img svg {
            opacity: 0.5;
        }
        
        /* Botones en lista */
        .btn-list .btn {
            margin-right: 0.25rem;
        }
        
        .btn-list .btn:last-child {
            margin-right: 0;
        }
        
        /* Estilos para dropdowns */
        .dropdown-header {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: var(--tblr-muted);
            padding: 0.5rem 1rem 0.25rem;
        }
        
        /* Efecto de transición suave */
        .framework-row {
            transition: background-color 0.2s ease;
        }
        
        /* Responsivo para pantallas pequeñas */
        @media (max-width: 768px) {
            .btn-list .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
            
            .nav-tabs .nav-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }
        }
    </style>
    @endpush
@endsection