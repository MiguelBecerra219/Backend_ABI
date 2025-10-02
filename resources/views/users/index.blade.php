@extends('tablar::page')

@section('title', 'Usuarios')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <circle cx="12" cy="7" r="4" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                        Usuarios
                        <span class="badge bg-indigo ms-2">{{ $users->total() }}</span>
                    </h2>
                    <p class="text-muted mb-0">Administra los usuarios del sistema según su rol y estado.</p>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Nuevo usuario
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
                    <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Buscar</label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Email, nombre, cédula…">
                                @if(!empty($search) || !empty($role) || !empty($state) || !empty($cityProgramId) || ($perPage ?? 10) != 10)
                                    <a href="{{ route('users.index') }}" class="input-group-text" title="Limpiar filtros">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18" />
                                            <line x1="6" y1="6" x2="18" y2="18" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="role" class="form-label">Rol</label>
                            <select name="role" id="role" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                <option value="student" {{ (string)($role ?? '') === 'student' ? 'selected' : '' }}>Estudiante</option>
                                <option value="professor" {{ (string)($role ?? '') === 'professor' ? 'selected' : '' }}>Profesor</option>
                                <option value="committee_leader" {{ (string)($role ?? '') === 'committee_leader' ? 'selected' : '' }}>Líder de Comité</option>
                                <option value="research_staff" {{ (string)($role ?? '') === 'research_staff' ? 'selected' : '' }}>Personal de Investigación</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="state" class="form-label">Estado</label>
                            <select name="state" id="state" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                <option value="1" {{ ($state ?? '') == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ ($state ?? '') == 0 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="city_program_id" class="form-label">Programa</label>
                            <select name="city_program_id" id="city_program_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach($cityPrograms as $program)
                                    <option value="{{ $program->id }}" {{ (string)($cityProgramId ?? '') === (string)$program->id ? 'selected' : '' }}>
                                        {{ $program->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="per_page" class="form-label">Registros por página</label>
                            <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach($perPageOptions as $size)
                                    <option value="{{ $size }}" {{ (int)($perPage ?? 10) === $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
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
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Nombre</th>
                                <th>Cédula</th>
                                <th>Programa</th>
                                <th>Semestre/Líder</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td class="text-muted">{{ $users->firstItem() + $index }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @switch($user->role)
                                        @case('student')
                                            <span class="badge bg-info-lt">Estudiante</span>
                                            @break
                                        @case('professor')
                                            <span class="badge bg-primary-lt">Profesor</span>
                                            @break
                                        @case('committee_leader')
                                            <span class="badge bg-warning-lt">Líder de Comité</span>
                                            @break
                                        @case('research_staff')
                                            <span class="badge bg-success-lt">Personal de Investigación</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary-lt">{{ $user->role }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if($user->state === '1' || $user->state === 1)
                                        <span class="badge bg-success-lt">Activo</span>
                                    @else
                                        <span class="badge bg-danger-lt">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->details)
                                        {{ $user->details->name }} {{ $user->details->last_name }}
                                    @endif
                                </td>
                                <td>
                                    @if($user->details)
                                        {{ $user->details->card_id }}
                                    @endif
                                </td>
                                <td>
                                    @if($user->details && ($user->role == 'student' || $user->role == 'professor' || $user->role == 'committee_leader'))
                                        @if(isset($user->details->cityProgram))
                                            {{ $user->details->cityProgram->program->name ?? 'N/A' }} - 
                                            {{ $user->details->cityProgram->city->name ?? 'N/A' }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($user->role == 'student' && $user->details)
                                        {{ $user->details->semester }}
                                    @elseif(($user->role == 'professor' || $user->role == 'committee_leader') && $user->details)
                                        {{ $user->details->committee_leader ? 'Sí' : 'No' }}
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="2" />
                                                <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-success" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                        @if($user->state == 1)
                                            <!-- Button to deactivate user -->
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas desactivar el usuario {{ $user->email }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Desactivar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <line x1="4" y1="6" x2="20" y2="6" />
                                                        <line x1="4" y1="12" x2="20" y2="12" />
                                                        <line x1="4" y1="18" x2="16" y2="18" />
                                                        <path d="M16 6l4 4" />
                                                        <path d="M16 12l4 -4" />
                                                        <path d="M16 18l4 -4" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <!-- Button to activate user -->
                                            <form action="{{ route('users.activate', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas activar el usuario {{ $user->email }}?');">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Activar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M7 12l5 5l10 -10" />
                                                        <path d="M2 7l5 -5" />
                                                        <polyline points="2.5 16.5 6.5 20.5 12 15" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty">
                                        <div class="empty-img">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <circle cx="12" cy="7" r="4" />
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                            </svg>
                                        </div>
                                        <p class="empty-title">No hay usuarios registrados</p>
                                        <p class="empty-subtitle text-muted">Registra usuarios para gestionar el sistema según sus roles.</p>
                                        <div class="empty-action">
                                            <a href="{{ route('register') }}" class="btn btn-primary">Registrar usuario</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div class="text-muted small">Mostrando {{ $users->firstItem() }}-{{ $users->lastItem() }} de {{ $users->total() }} registros</div>
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection