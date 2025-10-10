{{--
    View path: framework/show.blade.php.
    Purpose: Renders the show.blade view for the Framework module.
    Expected variables within this template: $currentYear, $duration, $elapsedYears, $endYear, $framework, $isCurrent, $isFuture, $progress, $startYear, $totalYears, $years, $yearsActive.
    Included partials or components: tablar::common.alert.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Ver Framework')

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
                            <li class="breadcrumb-item active" aria-current="page">{{ $framework->name }}</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-info" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="2"/>
                            <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                        </svg>
                        Detalles del Framework
                        <span class="badge bg-blue-lt ms-2">#{{ $framework->id }}</span>
                    </h2>
                    <p class="text-muted">Información completa del framework "{{ $framework->name }}"</p>
                </div>
                
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('frameworks.edit', $framework->id) }}" class="btn btn-outline-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                <path d="M16 5l3 3"/>
                            </svg>
                            Editar Framework
                        </a>
                        <a href="{{ route('frameworks.index') }}" class="btn btn-primary">
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
                <!-- Información principal -->
                <div class="col-lg-8">
                    <!-- Encabezado del framework -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="9" y1="9" x2="15" y2="9"/>
                                    <line x1="9" y1="15" x2="15" y2="15"/>
                                </svg>
                                {{ $framework->name }}
                            </h3>
                            <div class="card-actions">
                                @php
                                    $currentYear = date('Y');
                                    $isCurrent = $framework->start_year <= $currentYear && ($framework->end_year === null || $framework->end_year >= $currentYear);
                                    $isFuture = $framework->start_year > $currentYear;
                                @endphp
                                @if($isCurrent)
                                    <span class="badge bg-success-lt fs-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2l4 -4"/></svg>
                                        Vigente
                                    </span>
                                @elseif($isFuture)
                                    <span class="badge bg-info-lt fs-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 15"/></svg>
                                        Programado
                                    </span>
                                @else
                                    <span class="badge bg-yellow-lt fs-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                                        Finalizado
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        {{-- Label describing the purpose of 'DESCRIPCIÓN'. --}}
                                        <label class="form-label text-muted small">DESCRIPCIÓN</label>
                                        <div class="fs-5 text-dark">{{ $framework->description }}</div>
                                    </div>
                                </div>

                                @if(!empty($framework->link))
                                <div class="col-12">
                                    <div class="mb-2">
                                        {{-- Label describing the purpose of 'ENLACE'. --}}
                                        <label class="form-label text-muted small">ENLACE</label>
                                        <div class="d-flex align-items-center">
                                            <a href="{{ $framework->link }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-azure">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M10 14a3.5 3.5 0 0 0 5 0l3-3a3.5 3.5 0 1 0 -5 -5l-.5 .5"/>
                                                    <path d="M14 10a3.5 3.5 0 0 0 -5 0l-3 3a3.5 3.5 0 1 0 5 5l.5 -.5"/>
                                                </svg>
                                                Abrir enlace
                                            </a>
                                            <span class="ms-2 text-muted text-truncate" style="max-width: 70%;">
                                                {{ $framework->link }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Período de vigencia detallado -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                Período de Vigencia
                            </h3>
                        </div>
                        <div class="card-body">
                            {{-- (igual que tu archivo) --}}
                            @php
                                $startYear = $framework->start_year;
                                $endYear = $framework->end_year ?? $currentYear + 5;
                                $totalYears = $endYear - $startYear + 1;
                                $elapsedYears = min($currentYear - $startYear + 1, $totalYears);
                                $progress = $totalYears > 0 ? ($elapsedYears / $totalYears) * 100 : 0;
                            @endphp
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        {{-- Label describing the purpose of 'AÑO DE INICIO'. --}}
                                        <label class="form-label text-muted small">AÑO DE INICIO</label>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-green-lt me-2 fs-6">{{ $framework->start_year }}</span>
                                            <span class="text-muted small">Entrada en vigencia</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        {{-- Label describing the purpose of 'AÑO DE FINALIZACIÓN'. --}}
                                        <label class="form-label text-muted small">AÑO DE FINALIZACIÓN</label>
                                        <div class="d-flex align-items-center">
                                            @if($framework->end_year)
                                                <span class="badge bg-red-lt me-2 fs-6">{{ $framework->end_year }}</span>
                                                <span class="text-muted small">Finalización programada</span>
                                            @else
                                                <span class="badge bg-blue-lt me-2 fs-6">Sin límite</span>
                                                <span class="text-muted small">Vigencia indefinida</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                {{-- Label describing the purpose of 'LÍNEA DE TIEMPO'. --}}
                                <label class="form-label text-muted small">LÍNEA DE TIEMPO</label>
                                <div class="progress progress-lg mb-2">
                                    <div class="progress-bar bg-primary" style="width: {{ min($progress, 100) }}%" role="progressbar"></div>
                                </div>
                                <div class="d-flex justify-content-between text-muted small">
                                    <span>{{ $startYear }}</span>
                                    <span class="fw-bold text-primary">{{ $currentYear }} (Actual)</span>
                                    <span>{{ $framework->end_year ?? 'Indefinido' }}</span>
                                </div>
                                <div class="mt-2 text-center">
                                    @if($framework->end_year)
                                        @php
                                            $duration = $framework->end_year - $framework->start_year + 1;
                                            $yearsActive = min($currentYear - $framework->start_year + 1, $duration);
                                        @endphp
                                        <span class="badge bg-azure-lt">
                                            {{ $yearsActive }} de {{ $duration }} {{ $duration == 1 ? 'año' : 'años' }}
                                        </span>
                                    @else
                                        @php
                                            $yearsActive = max($currentYear - $framework->start_year + 1, 1);
                                        @endphp
                                        <span class="badge bg-green-lt">
                                            {{ $yearsActive }} {{ $yearsActive == 1 ? 'año' : 'años' }} activo
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel lateral -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-secondary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="M12 8v4"/>
                                    <path d="M12 16h.01"/>
                                </svg>
                                Información del Sistema
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <div class="text-muted small">ID del Framework</div>
                                        <div class="fw-bold">#{{ $framework->id }}</div>
                                    </div>
                                    <span class="badge bg-blue-lt">{{ str_pad($framework->id, 3, '0', STR_PAD_LEFT) }}</span>
                                </div>

                                @if(!empty($framework->link))
                                <div class="list-group-item px-0">
                                    <div class="text-muted small mb-1">Enlace</div>
                                    <a href="{{ $framework->link }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                        {{ $framework->link }}
                                    </a>
                                </div>
                                @endif
                                
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <div class="text-muted small">Fecha de Creación</div>
                                        <div class="fw-bold">{{ $framework->created_at?->format('d/m/Y') }}</div>
                                        <div class="text-muted small">{{ $framework->created_at?->format('H:i') }} hrs</div>
                                    </div>
                                    <span class="text-muted">{{ $framework->created_at?->diffForHumans() }}</span>
                                </div>
                                
                                @if($framework->updated_at && $framework->updated_at != $framework->created_at)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <div class="text-muted small">Última Actualización</div>
                                        <div class="fw-bold">{{ $framework->updated_at?->format('d/m/Y') }}</div>
                                        <div class="text-muted small">{{ $framework->updated_at?->format('H:i') }} hrs</div>
                                    </div>
                                    <span class="text-muted">{{ $framework->updated_at?->diffForHumans() }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas rápidas (igual que tu archivo) -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="20" x2="18" y2="10"/>
                                    <line x1="12" y1="20" x2="12" y2="4"/>
                                    <line x1="6" y1="20" x2="6" y2="14"/>
                                </svg>
                                Estadísticas
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="h1 mb-1 text-primary">
                                            @php
                                                $years = $framework->end_year ? 
                                                    ($framework->end_year - $framework->start_year + 1) : 
                                                    (date('Y') - $framework->start_year + 1);
                                            @endphp
                                            {{ $years }}
                                        </div>
                                        <div class="text-muted small">{{ $years == 1 ? 'Año' : 'Años' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="h1 mb-1 text-success">
                                            {{ $framework->end_year ? $framework->end_year : '∞' }}
                                        </div>
                                        <div class="text-muted small">Hasta</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title mb-1">Acciones Disponibles</h3>
                                    <p class="text-muted mb-0">Gestiona este framework</p>
                                </div>
                                <div class="btn-list">
                                    <a href="{{ route('frameworks.edit', $framework->id) }}" class="btn btn-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                            <path d="M16 5l3 3"/>
                                        </svg>
                                        Editar Framework
                                    </a>
                                    {{-- Button element of type 'button' to trigger the intended action. --}}
                                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete({{ $framework->id }}, '{{ $framework->name }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <line x1="4" y1="7" x2="20" y2="7"/>
                                            <line x1="10" y1="11" x2="10" y2="17"/>
                                            <line x1="14" y1="11" x2="14" y2="17"/>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                        </svg>
                                        Eliminar Framework
                                    </button>
                                    
                                    {{-- Form element sends the captured data to the specified endpoint. --}}
                                    <form id="delete-form-{{ $framework->id }}" action="{{ route('frameworks.destroy', $framework->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        </div>
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
                    {{-- Button element of type 'button' to trigger the intended action. --}}
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancelar</button>
                    {{-- Button element of type 'button' to trigger the intended action. --}}
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Sí, eliminar</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        function confirmDelete(id, name) {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            document.getElementById('frameworkName').textContent = name;
            document.getElementById('confirmDeleteBtn').onclick = function() {
                document.getElementById('delete-form-' + id).submit();
            };
            modal.show();
        }
    </script>
    @endpush

    @push('css')
    <style>
        .badge.fs-6 { font-size:.875rem!important; padding:.375rem .75rem; }
        .progress-lg { height:1rem; }
        .card { box-shadow:0 .125rem .25rem rgba(0,0,0,.075); transition:box-shadow .15s; }
        .list-group-flush .list-group-item { border-width:0 0 1px; }
        .list-group-flush .list-group-item:last-child { border-bottom-width:0; }
    </style>
    @endpush
@endsection
