{{--
    View path: thematic-areas/index.blade.php.
    Purpose: Renders the index.blade view for the Thematic Areas module.
    Expected variables within this template: $area, $groupName, $id, $index, $investigationLineId, $investigationLines, $line, $perPage, $researchGroupId, $researchGroups, $search, $size, $thematicAreas.
    Included partials or components: tablar::common.alert.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Áreas temáticas')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Áreas temáticas</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-orange" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 21l-7 -18l7 4l7 -4z" />
                        </svg>
                        Áreas temáticas
                        <span class="badge bg-orange ms-2">{{ $thematicAreas->total() }}</span>
                    </h2>
                    <p class="text-muted mb-0">Administra las áreas temáticas vinculadas a las líneas de investigación.</p>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <a href="{{ route('thematic-areas.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Nueva área
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

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Filtros de búsqueda</h3>
                </div>
                <div class="card-body">
                    {{-- Form element sends the captured data to the specified endpoint. --}}
                    <form method="GET" action="{{ route('thematic-areas.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            {{-- Label describing the purpose of 'Buscar'. --}}
                            <label for="search" class="form-label">Buscar</label>
                            <div class="input-group">
                                {{-- Input element used to capture the 'search' value. --}}
                                <input type="text" name="search" id="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Nombre o descripción…">
                                @if(!empty($search) || !empty($researchGroupId) || !empty($investigationLineId) || ($perPage ?? 10) != 10)
                                    <a href="{{ route('thematic-areas.index') }}" class="input-group-text" title="Limpiar filtros">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18" />
                                            <line x1="6" y1="6" x2="18" y2="18" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            {{-- Label describing the purpose of 'Grupo de investigación'. --}}
                            <label for="research_group_id" class="form-label">Grupo de investigación</label>
                            {{-- Dropdown presenting the available options for 'research_group_id'. --}}
                            <select name="research_group_id" id="research_group_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach($researchGroups as $id => $groupName)
                                    <option value="{{ $id }}" {{ (string)($researchGroupId ?? '') === (string)$id ? 'selected' : '' }}>{{ $groupName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            {{-- Label describing the purpose of 'Línea de investigación'. --}}
                            <label for="investigation_line_id" class="form-label">Línea de investigación</label>
                            {{-- Dropdown presenting the available options for 'investigation_line_id'. --}}
                            <select name="investigation_line_id" id="investigation_line_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Todas</option>
                                @foreach($investigationLines as $line)
                                    <option value="{{ $line->id }}" {{ (string)($investigationLineId ?? '') === (string)$line->id ? 'selected' : '' }}>
                                        {{ $line->name }} @if($line->researchGroup) ({{ $line->researchGroup->name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            {{-- Label describing the purpose of 'Registros por página'. --}}
                            <label for="per_page" class="form-label">Registros por página</label>
                            {{-- Dropdown presenting the available options for 'per_page'. --}}
                            <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach([10, 25, 50] as $size)
                                    <option value="{{ $size }}" {{ (int)($perPage ?? 10) === $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            {{-- Button element of type 'submit' to trigger the intended action. --}}
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 6h16" />
                                    <path d="M4 12h10" />
                                    <path d="M4 18h4" />
                                </svg>
                                Aplicar filtros
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap">
                        <thead>
                            <tr>
                                <th class="w-1">#</th>
                                <th>Área</th>
                                <th>Descripción</th>
                                <th>Línea</th>
                                <th>Grupo</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($thematicAreas as $index => $area)
                            <tr>
                                <td class="text-muted">{{ $thematicAreas->firstItem() + $index }}</td>
                                <td>{{ $area->name }}</td>
                                <td>
                                    <div class="text-truncate" style="max-width: 360px;" title="{{ $area->description }}">
                                        {{ Str::limit($area->description, 120) }}
                                    </div>
                                </td>
                                <td>
                                    @if($area->investigationLine)
                                        <a href="{{ route('investigation-lines.show', $area->investigationLine) }}" class="text-decoration-none">
                                            {{ $area->investigationLine->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Sin línea</span>
                                    @endif
                                </td>
                                <td>
                                    @if($area->investigationLine && $area->investigationLine->researchGroup)
                                        <a href="{{ route('research-groups.show', $area->investigationLine->researchGroup) }}" class="text-decoration-none">
                                            {{ $area->investigationLine->researchGroup->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Sin grupo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('thematic-areas.show', $area) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="2" />
                                                <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('thematic-areas.edit', $area) }}" class="btn btn-sm btn-outline-success" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                        {{-- Form element sends the captured data to the specified endpoint. --}}
                                        <form action="{{ route('thematic-areas.destroy', $area) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar el área temática {{ $area->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            {{-- Button element of type 'submit' to trigger the intended action. --}}
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <line x1="4" y1="7" x2="20" y2="7" />
                                                    <line x1="10" y1="11" x2="10" y2="17" />
                                                    <line x1="14" y1="11" x2="14" y2="17" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3h6v3" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty">
                                        <div class="empty-img">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <rect x="3" y="4" width="18" height="12" rx="2" />
                                                <line x1="7" y1="20" x2="17" y2="20" />
                                                <line x1="9" y1="16" x2="9" y2="20" />
                                                <line x1="15" y1="16" x2="15" y2="20" />
                                            </svg>
                                        </div>
                                        <p class="empty-title">No hay áreas registradas</p>
                                        <p class="empty-subtitle text-muted">Crea un área temática para detallar los componentes del proyecto.</p>
                                        <div class="empty-action">
                                            <a href="{{ route('thematic-areas.create') }}" class="btn btn-primary">Crear área</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($thematicAreas->hasPages())
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div class="text-muted small">Mostrando {{ $thematicAreas->firstItem() }}-{{ $thematicAreas->lastItem() }} de {{ $thematicAreas->total() }} registros</div>
                        {{ $thematicAreas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
