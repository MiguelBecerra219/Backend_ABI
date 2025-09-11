@extends('tablar::page')

@section('title', 'Contenidos de Framework')

@section('content')
    <!-- Header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Contenidos</li>
                        </ol>
                    </nav>

                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <line x1="9" y1="9" x2="15" y2="9"/>
                            <line x1="9" y1="15" x2="15" y2="15"/>
                        </svg>
                        Contenidos de Framework
                        <span class="badge bg-azure ms-2">{{ $contentFrameworkProjects->total() }}</span>
                    </h2>
                    <p class="text-muted">Lista y administración de contenidos asociados a frameworks.</p>
                </div>

                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('content-framework-projects.create') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Nuevo Contenido
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Body -->
    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar','display_alert'))
                @include('tablar::common.alert')
            @endif

            <!-- Filtros -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                        Filtros y Búsqueda
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('content-framework-projects.index') }}" class="row g-3" id="filterForm">
                        <div class="col-md-5">
                            <label class="form-label">Buscar</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Nombre o descripción…" value="{{ $search ?? '' }}" id="searchInput">
                                @if(!empty($search))
                                    <a href="{{ route('content-framework-projects.index') }}" class="input-group-text text-decoration-none" title="Limpiar búsqueda">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"/>
                                            <line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Framework</label>
                            <select name="framework_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @php
                                    // Ideal: el controlador pasa $frameworkOptions (id=>name). Fallback:
                                    if (!isset($frameworkOptions)) {
                                        try {
                                            $frameworkOptions = \App\Models\Framework::orderBy('name')->pluck('name','id')->toArray();
                                        } catch (\Throwable $e) {
                                            $frameworkOptions = [];
                                        }
                                    }
                                @endphp
                                @foreach($frameworkOptions as $fid => $fname)
                                    <option value="{{ $fid }}" {{ (string)($framework_id ?? '') === (string)$fid ? 'selected' : '' }}>
                                        {{ $fname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100" title="Aplicar filtros">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                </svg>
                                Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla -->
            <div class="card">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap">
                        <thead>
                        <tr>
                            <th class="w-1">#</th>
                            <th>Nombre</th>
                            <th>Framework</th>
                            <th>Descripción</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($contentFrameworkProjects as $i => $item)
                            <tr>
                                <td class="text-muted">{{ $contentFrameworkProjects->firstItem() + $i }}</td>
                                <td class="fw-medium">
                                    <a href="{{ route('content-framework-projects.show', $item->id) }}" class="text-decoration-none">
                                        {{ $item->name }}
                                    </a>
                                </td>
                                <td>
                                    @if($item->framework)
                                        <a class="badge bg-azure-lt text-decoration-none" href="{{ route('frameworks.show', $item->framework_id) }}">
                                            {{ Str::limit($item->framework->name, 40) }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary-lt">Sin framework</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 360px;" title="{{ $item->description }}">
                                        {{ Str::limit($item->description, 90) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('content-framework-projects.show', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="2"/>
                                                <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('content-framework-projects.edit', $item->id) }}" class="btn btn-sm btn-outline-success" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                <path d="M16 5l3 3"/>
                                            </svg>
                                        </a>

                                        <!-- Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm dropdown-toggle align-text-top" data-bs-toggle="dropdown" aria-expanded="false">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="1"/>
                                                    <circle cx="12" cy="5" r="1"/>
                                                    <circle cx="12" cy="19" r="1"/>
                                                </svg>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ route('content-framework-projects.show', $item->id) }}">Ver detalles</a>
                                                <a class="dropdown-item" href="{{ route('content-framework-projects.edit', $item->id) }}">Editar</a>

                                                <div class="dropdown-divider"></div>
                                                <a href="#" class="dropdown-item text-red"
                                                   data-confirm
                                                   data-action="#delete-form-{{ $item->id }}">
                                                    Eliminar
                                                </a>

                                                <!-- form delete -->
                                                <form id="delete-form-{{ $item->id }}" action="{{ route('content-framework-projects.destroy', $item->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty my-5">
                                        <div class="empty-img">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="128" height="128" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="16" rx="2"/>
                                                <line x1="7" y1="8" x2="17" y2="8"/>
                                                <line x1="7" y1="12" x2="17" y2="12"/>
                                                <line x1="7" y1="16" x2="13" y2="16"/>
                                            </svg>
                                        </div>
                                        <p class="empty-title h3">No hay contenidos</p>
                                        <p class="empty-subtitle text-muted">Crea el primero para comenzar.</p>
                                        <div class="empty-action">
                                            <a href="{{ route('content-framework-projects.create') }}" class="btn btn-primary">Crear Contenido</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($contentFrameworkProjects->hasPages())
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <div class="text-muted">
                            Mostrando
                            <strong>{{ $contentFrameworkProjects->firstItem() }}</strong>
                            a
                            <strong>{{ $contentFrameworkProjects->lastItem() }}</strong>
                            de
                            <strong>{{ $contentFrameworkProjects->total() }}</strong>
                            contenidos
                        </div>
                        {!! $contentFrameworkProjects->links('tablar::pagination') !!}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('js')
    <script>
        // Doble confirmación (reutiliza el listener global que tienes en layout)
        // Si no lo tuvieras, el siguiente bloque suple el comportamiento:
        document.addEventListener('click', (e) => {
          const btn = e.target.closest('[data-confirm]');
          if(!btn) return;
          e.preventDefault();
          const wrap = btn.parentElement;
          let confirmBtn = wrap.querySelector('.btn-confirm');
          if (confirmBtn) return;

          confirmBtn = document.createElement('button');
          confirmBtn.className = 'btn btn-danger btn-confirm';
          confirmBtn.style.opacity = .35;
          confirmBtn.textContent = 'Confirmar borrado';
          wrap.appendChild(confirmBtn);

          setTimeout(() => { confirmBtn.style.opacity = 1; confirmBtn.dataset.armed = '1'; }, 1000);

          confirmBtn.addEventListener('click', () => {
            if (!confirmBtn.dataset.armed) return;
            const formSel = btn.dataset.action;
            document.querySelector(formSel)?.submit();
          });

          const cancel = document.createElement('button');
          cancel.className = 'btn btn-link text-secondary';
          cancel.textContent = 'Cancelar';
          cancel.addEventListener('click', () => { confirmBtn.remove(); cancel.remove(); });
          wrap.appendChild(cancel);
        });

        // Búsqueda con retardo
        const searchInput = document.getElementById('searchInput');
        let searchTimeout;
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (this.value.length >= 3 || this.value.length === 0) {
                        document.getElementById('filterForm').submit();
                    }
                }, 500);
            });
        }
    </script>
    @endpush

    @push('css')
    <style>
        .table-responsive { max-height: 600px; overflow-y: auto; }
        .badge { font-size: .75rem; font-weight: 500; }
        .empty-img svg { opacity:.6; }
    </style>
    @endpush
@endsection
