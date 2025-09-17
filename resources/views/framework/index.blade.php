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
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                            <line x1="9" y1="9" x2="15" y2="9" />
                            <line x1="9" y1="15" x2="15" y2="15" />
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
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
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
            @if(config('tablar.display_alert'))
                @include('tablar::common.alert')
            @endif

            @php
                $search = $search ?? null;
                $year = $year ?? null;
                $perPage = $perPage ?? 10;
                $perPageOptions = $perPageOptions ?? [10, 20, 30];
            @endphp

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <form method="GET" action="{{ route('frameworks.index') }}" class="w-100">
                                <div class="row g-2 align-items-center">
                                    <div class="col-12 col-lg-5">
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <circle cx="10" cy="10" r="7" />
                                                    <line x1="21" y1="21" x2="15" y2="15" />
                                                </svg>
                                            </span>
                                            <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por nombre, descripción o ID">
                                        </div>
                                    </div>
                                    <div class="col-6 col-lg-2">
                                        <input type="number" name="year" value="{{ $year }}" class="form-control" placeholder="Año vigencia" min="1900" max="{{ date('Y') + 50 }}">
                                    </div>
                                    <div class="col-6 col-lg-2">
                                        <select name="per_page" class="form-select">
                                            @foreach($perPageOptions as $option)
                                                <option value="{{ $option }}" {{ (int) $perPage === (int) $option ? 'selected' : '' }}>Mostrar {{ $option }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-3 d-grid gap-2 d-lg-flex">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 5h16" />
                                                <path d="M7 10h10" />
                                                <path d="M10 15h4" />
                                            </svg>
                                            Filtrar
                                        </button>
                                        <a href="{{ route('frameworks.index') }}" class="btn btn-outline-secondary w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <line x1="18" y1="6" x2="6" y2="18" />
                                                <line x1="6" y1="6" x2="18" y2="18" />
                                            </svg>
                                            Limpiar
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap">
                                <thead>
                                    <tr>
                                        <th class="w-1">
                                            <input class="form-check-input m-0 align-middle" type="checkbox" aria-label="Seleccionar todos" id="select-all">
                                        </th>
                                        <th class="w-1">#</th>
                                        <th>Nombre del Framework</th>
                                        <th>Descripción</th>
                                        <th>Período de Vigencia</th>
                                        <th>Estado</th>
                                        <th>Duración</th>
                                        <th class="w-1">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $currentYear = (int) date('Y');
                                        $i = $frameworks->firstItem() ? $frameworks->firstItem() - 1 : 0;
                                    @endphp

                                    @if ($frameworks->count())
                                        @foreach ($frameworks as $framework)
                                            @php
                                                $startYear = (int) $framework->start_year;
                                                $endYear = $framework->end_year ? (int) $framework->end_year : null;
                                                $isCurrent = $startYear <= $currentYear && ($endYear === null || $endYear >= $currentYear);
                                                $isFuture = $startYear > $currentYear;
                                                $durationYears = $endYear ? max(1, $endYear - $startYear + 1) : null;
                                                $yearsElapsed = max(0, $currentYear - $startYear + 1);
                                                $remainingYears = $endYear ? $endYear - $currentYear : null;
                                            @endphp
                                            <tr class="framework-row">
                                                <td>
                                                    <input class="form-check-input m-0 align-middle framework-checkbox" type="checkbox" value="{{ $framework->id }}" aria-label="Seleccionar framework">
                                                </td>
                                                <td><span class="text-muted">{{ ++$i }}</span></td>
                                                <td>
                                                    <div class="d-flex py-1 align-items-center">
                                                        <div class="avatar me-2 bg-blue-lt">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                                                <line x1="9" y1="9" x2="15" y2="9" />
                                                                <line x1="9" y1="15" x2="15" y2="15" />
                                                            </svg>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <div class="font-weight-medium d-flex align-items-center flex-wrap gap-1">
                                                                {{ $framework->name }}
                                                                @if(!empty($framework->link))
                                                                    <a href="{{ $framework->link }}" target="_blank" rel="noopener noreferrer" class="text-muted" title="Abrir enlace">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path d="M10 14a3.5 3.5 0 0 0 5 0l3-3a3.5 3.5 0 1 0 -5 -5l-.5 .5" />
                                                                            <path d="M14 10a3.5 3.5 0 0 0 -5 0l-3 3a3.5 3.5 0 1 0 5 5l.5 -.5" />
                                                                        </svg>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                            <div class="text-muted">
                                                                <small>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <circle cx="12" cy="12" r="9" />
                                                                        <polyline points="12 7 12 12 15 15" />
                                                                    </svg>
                                                                    Creado {{ $framework->created_at?->diffForHumans() }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 250px;" title="{{ $framework->description }}">
                                                        {{ Str::limit($framework->description, 80) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-azure-lt me-1">{{ $framework->start_year }}</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-muted mx-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <line x1="5" y1="12" x2="19" y2="12" />
                                                            <polyline points="12 5 19 12 12 19" />
                                                        </svg>
                                                        @if($framework->end_year)
                                                            <span class="badge bg-azure-lt">{{ $framework->end_year }}</span>
                                                        @else
                                                            <span class="badge bg-green-lt">Presente</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($isCurrent)
                                                        <span class="badge bg-success-lt">En vigencia</span>
                                                    @elseif($isFuture)
                                                        <span class="badge bg-info-lt">Programado</span>
                                                    @else
                                                        <span class="badge bg-yellow-lt">Finalizado</span>
                                                    @endif
                                                    <div class="small text-muted mt-1">
                                                        @if($isCurrent)
                                                            Vigente desde {{ $framework->start_year }}
                                                            @if($framework->end_year)
                                                                hasta {{ $framework->end_year }}
                                                            @else
                                                                con vigencia indefinida
                                                            @endif
                                                        @elseif($isFuture)
                                                            Inicia en {{ $framework->start_year }}
                                                        @else
                                                            Finalizó en {{ $framework->end_year ?? $framework->start_year }}
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($endYear)
                                                        <span class="badge bg-azure-lt">
                                                            {{ $durationYears }} {{ Str::plural('año', $durationYears) }}
                                                        </span>
                                                        <div class="small text-muted mt-1">
                                                            @if($remainingYears > 0)
                                                                Restan {{ $remainingYears }} {{ Str::plural('año', $remainingYears) }}
                                                            @elseif($remainingYears === 0)
                                                                Último año de vigencia
                                                            @else
                                                                Expiró hace {{ abs($remainingYears) }} {{ Str::plural('año', abs($remainingYears)) }}
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="badge bg-azure-lt">Vigencia indefinida</span>
                                                        <div class="small text-muted mt-1">
                                                            @if($isFuture)
                                                                Inicia en {{ $framework->start_year }}
                                                            @else
                                                                {{ $yearsElapsed }} {{ Str::plural('año', $yearsElapsed) }} transcurridos
                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-list flex-nowrap">
                                                        <a href="{{ route('frameworks.show', $framework->id) }}" class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="2" />
                                                                <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                                            </svg>
                                                        </a>
                                                        <a href="{{ route('frameworks.edit', $framework->id) }}" class="btn btn-sm btn-outline-success" title="Editar">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                                <path d="M16 5l3 3" />
                                                            </svg>
                                                        </a>

                                                        <div class="dropdown">
                                                            <button class="btn btn-sm dropdown-toggle align-text-top" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <circle cx="12" cy="12" r="1" />
                                                                    <circle cx="12" cy="5" r="1" />
                                                                    <circle cx="12" cy="19" r="1" />
                                                                </svg>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <h6 class="dropdown-header">Acciones principales</h6>
                                                                <a class="dropdown-item" href="{{ route('frameworks.show', $framework->id) }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <circle cx="12" cy="12" r="2" />
                                                                        <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                                                    </svg>
                                                                    Ver detalles
                                                                </a>
                                                                <a class="dropdown-item" href="{{ route('frameworks.edit', $framework->id) }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                                        <path d="M16 5l3 3" />
                                                                    </svg>
                                                                    Editar framework
                                                                </a>
                                                                @if(!empty($framework->link))
                                                                    <a class="dropdown-item" href="{{ $framework->link }}" target="_blank" rel="noopener noreferrer">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path d="M10 14a3.5 3.5 0 0 0 5 0l3-3a3.5 3.5 0 1 0 -5 -5l-.5 .5" />
                                                                            <path d="M14 10a3.5 3.5 0 0 0 -5 0l-3 3a3.5 3.5 0 1 0 5 5l.5 -.5" />
                                                                        </svg>
                                                                        Abrir enlace
                                                                    </a>
                                                                @endif
                                                                <button type="button" class="dropdown-item text-danger" data-action="delete" data-framework-id="{{ $framework->id }}" data-framework-name="{{ $framework->name }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path d="M4 7h16" />
                                                                        <path d="M10 11v6" />
                                                                        <path d="M14 11v6" />
                                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                        <path d="M9 7v-2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v2" />
                                                                    </svg>
                                                                    Eliminar framework
                                                                </button>
                                                                <form id="delete-form-{{ $framework->id }}" action="{{ route('frameworks.destroy', $framework->id) }}" method="POST" class="d-none">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8">
                                                <div class="empty">
                                                    <div class="empty-icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                                            <line x1="3" y1="9" x2="21" y2="9" />
                                                            <line x1="9" y1="21" x2="9" y2="9" />
                                                        </svg>
                                                    </div>
                                                    <p class="empty-title">No se encontraron frameworks</p>
                                                    <p class="empty-subtitle text-muted">Ajusta los filtros de búsqueda o crea un nuevo framework para comenzar.</p>
                                                    <a href="{{ route('frameworks.create') }}" class="btn btn-primary">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <line x1="12" y1="5" x2="12" y2="19" />
                                                            <line x1="5" y1="12" x2="19" y2="12" />
                                                        </svg>
                                                        Nuevo framework
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                            <div class="text-muted">
                                @if($frameworks->total())
                                    Mostrando <span class="fw-semibold">{{ $frameworks->firstItem() }}</span> - <span class="fw-semibold">{{ $frameworks->lastItem() }}</span> de <span class="fw-semibold">{{ $frameworks->total() }}</span> frameworks
                                @else
                                    No hay frameworks para mostrar
                                @endif
                            </div>
                            {{ $frameworks->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');

        if (selectAll) {
          selectAll.addEventListener('change', function () {
            document.querySelectorAll('.framework-checkbox').forEach(function (checkbox) {
              checkbox.checked = selectAll.checked;
            });
          });
        }

        document.querySelectorAll('[data-action="delete"]').forEach(function (button) {
          button.addEventListener('click', function (event) {
            event.preventDefault();
            const frameworkId = this.dataset.frameworkId;
            const frameworkName = this.dataset.frameworkName || '';
            if (confirm('¿Deseas eliminar el framework "' + frameworkName + '"? Esta acción no se puede deshacer.')) {
              const form = document.getElementById('delete-form-' + frameworkId);
              if (form) { form.submit(); }
            }
          });
        });
      });
    </script>
    @endpush
@endsection
