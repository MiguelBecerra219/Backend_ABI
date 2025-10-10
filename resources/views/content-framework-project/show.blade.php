{{--
    View path: content-framework-project/show.blade.php.
    Purpose: Renders the show.blade view for the Content Framework Project module.
    Expected variables within this template: $contentFrameworkProject.
    Included partials or components: tablar::common.alert.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Ver Contenido del Framework')

@section('content')
    <!-- Encabezado -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('content-framework-projects.index') }}">Contenidos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $contentFrameworkProject->name }}</li>
                        </ol>
                    </nav>

                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-info" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="2"/>
                            <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                        </svg>
                        Detalles del Contenido
                        <span class="badge bg-blue-lt ms-2">#{{ $contentFrameworkProject->id }}</span>
                    </h2>
                    <p class="text-muted">Información completa del contenido asociado a un framework.</p>
                </div>

                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('content-framework-projects.edit', $contentFrameworkProject->id) }}" class="btn btn-outline-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                <path d="M16 5l3 3"/>
                            </svg>
                            Editar
                        </a>
                        <a href="{{ route('content-framework-projects.index') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 6 9 12 15 18"/>
                            </svg>
                            Volver a la lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cuerpo -->
    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar','display_alert'))
                @include('tablar::common.alert')
            @endif

            <div class="row">
                <!-- Columna principal -->
                <div class="col-lg-8">
                    <!-- Card: Información general -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="9" y1="9" x2="15" y2="9"/>
                                    <line x1="9" y1="15" x2="15" y2="15"/>
                                </svg>
                                {{ $contentFrameworkProject->name }}
                            </h3>
                            <div class="card-actions">
                                @if($contentFrameworkProject->framework)
                                    <a class="badge bg-azure-lt text-decoration-none" href="{{ route('frameworks.show', $contentFrameworkProject->framework_id) }}">
                                        Ver Framework: {{ \Illuminate\Support\Str::limit($contentFrameworkProject->framework->name, 40) }}
                                    </a>
                                @else
                                    <span class="badge bg-secondary-lt">Sin framework asociado</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                {{-- Label describing the purpose of 'DESCRIPCIÓN'. --}}
                                <label class="form-label text-muted small">DESCRIPCIÓN</label>
                                <div class="fs-5 text-dark">
                                    {{ $contentFrameworkProject->description }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Acciones rápidas -->
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title mb-1">Acciones</h3>
                                <p class="text-muted mb-0">Gestiona este contenido</p>
                            </div>
                            <div class="btn-list">
                                <a href="{{ route('content-framework-projects.edit', $contentFrameworkProject->id) }}" class="btn btn-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                        <path d="M16 5l3 3"/>
                                    </svg>
                                    Editar
                                </a>

                                <!-- Doble confirmación -->
                                <a href="#" class="btn btn-outline-danger" data-confirm data-action="#delete-form-{{ $contentFrameworkProject->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z"/>
                                        <line x1="4" y1="7" x2="20" y2="7"/>
                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                    </svg>
                                    Eliminar
                                </a>

                                {{-- Form element sends the captured data to the specified endpoint. --}}
                                <form id="delete-form-{{ $contentFrameworkProject->id }}" action="{{ route('content-framework-projects.destroy', $contentFrameworkProject->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna lateral -->
                <div class="col-lg-4">
                    <!-- Card: Información del sistema -->
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
                                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted small">ID</div>
                                        <div class="fw-bold">#{{ $contentFrameworkProject->id }}</div>
                                    </div>
                                    <span class="badge bg-blue-lt">{{ str_pad($contentFrameworkProject->id, 3, '0', STR_PAD_LEFT) }}</span>
                                </div>

                                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted small">Fecha de creación</div>
                                        <div class="fw-bold">{{ $contentFrameworkProject->created_at?->format('d/m/Y') }}</div>
                                        <div class="text-muted small">{{ $contentFrameworkProject->created_at?->format('H:i') }} hrs</div>
                                    </div>
                                    <span class="text-muted">{{ $contentFrameworkProject->created_at?->diffForHumans() }}</span>
                                </div>

                                @if($contentFrameworkProject->updated_at && $contentFrameworkProject->updated_at != $contentFrameworkProject->created_at)
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small">Última actualización</div>
                                            <div class="fw-bold">{{ $contentFrameworkProject->updated_at?->format('d/m/Y') }}</div>
                                            <div class="text-muted small">{{ $contentFrameworkProject->updated_at?->format('H:i') }} hrs</div>
                                        </div>
                                        <span class="text-muted">{{ $contentFrameworkProject->updated_at?->diffForHumans() }}</span>
                                    </div>
                                @endif

                                <div class="list-group-item px-0">
                                    <div class="text-muted small mb-1">Framework asociado</div>
                                    @if($contentFrameworkProject->framework)
                                        <a href="{{ route('frameworks.show', $contentFrameworkProject->framework_id) }}" class="btn btn-outline-primary w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                <line x1="9" y1="9" x2="15" y2="9"/>
                                            </svg>
                                            {{ $contentFrameworkProject->framework->name }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary-lt">No asignado</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Atajos -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Atajos</h3>
                        </div>
                        <div class="card-body">
                            <div class="btn-list">
                                <a class="btn btn-outline-azure" href="{{ route('content-framework-projects.create', ['framework_id' => $contentFrameworkProject->framework_id]) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="12" y1="5" x2="12" y2="19"/>
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                    Agregar otro contenido a este framework
                                </a>
                                <a class="btn btn-outline-secondary" href="{{ route('content-framework-projects.index', ['framework_id' => $contentFrameworkProject->framework_id]) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="2"/>
                                        <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                    </svg>
                                    Ver todos los contenidos de este framework
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        // Doble confirmación por si tu layout no lo incluye globalmente
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
    </script>
    @endpush

    @push('css')
    <style>
        .badge.fs-6 { font-size: .875rem !important; padding: .375rem .75rem; }
        .card { box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); transition: box-shadow .15s ease-in-out; }
        .card:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.15); }
        .list-group-flush .list-group-item { border-width: 0 0 1px; }
        .list-group-flush .list-group-item:last-child { border-bottom-width: 0; }
    </style>
    @endpush
@endsection
