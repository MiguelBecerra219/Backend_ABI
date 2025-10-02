@extends('tablar::page')

@section('title', 'Editar usuario')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-orange" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 21l-7 -18l7 4l7 -4z" />
                        </svg>
                        Editar usuario: {{ $user->email }}
                    </h2>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 7h14" />
                            <path d="M5 12h14" />
                            <path d="M5 17h14" />
                        </svg>
                        Ver detalle
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

            <!-- Mensajes de error generales -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información de cuenta</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user) }}" id="user-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label required">Email</label>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="state" class="form-label required">Estado</label>
                                    <select id="state" name="state" class="form-select @error('state') is-invalid @enderror" required>
                                        <option value="1" {{ old('state', $user->state) == '1' ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('state', $user->state) == '0' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label required">Rol</label>
                                    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                                        <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Estudiante</option>
                                        <option value="professor" {{ old('role', $user->role) == 'professor' ? 'selected' : '' }}>Profesor</option>
                                        <option value="committee_leader" {{ old('role', $user->role) == 'committee_leader' ? 'selected' : '' }}>Líder de Comité</option>
                                        <option value="research_staff" {{ old('role', $user->role) == 'research_staff' ? 'selected' : '' }}>Personal de Investigación</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nueva contraseña</label>
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                           autocomplete="new-password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint">Deja en blanco para mantener la contraseña actual</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Confirmar contraseña</label>
                                    <div class="input-group input-group-flat">
                                        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                                            placeholder="Confirmar contraseña" autocomplete="off">
                                        <span class="input-group-text">
                                            <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <circle cx="12" cy="12" r="2"/>
                                                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                                </svg>
                                            </a>
                                        </span>
                                        @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Información personal</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="card_id" class="form-label required">Cédula</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" 
                                                    value="{{ old('card_id', $details->card_id ?? '') }}" 
                                                    readonly>
                                                <input type="hidden" id="card_id" name="card_id" 
                                                    value="{{ old('card_id', $details->card_id ?? '') }}">
                                            </div>
                                            @error('card_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label required">Nombre</label>
                                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                                                   value="{{ old('name', $details->name ?? '') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="last_name" class="form-label required">Apellido</label>
                                            <input type="text" id="last_name" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                                   value="{{ old('last_name', $details->last_name ?? '') }}" required>
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label required">Teléfono</label>
                                            <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                                   value="{{ old('phone', $details->phone ?? '') }}" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Specific fields for students -->
                                <div id="student-fields" class="role-fields" style="{{ in_array(old('role', $user->role), ['student']) ? '' : 'display: none;' }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="semester" class="form-label required">Semestre</label>
                                                <input type="number" id="semester" name="semester" class="form-control @error('semester') is-invalid @enderror" 
                                                       min="1" max="10" value="{{ old('semester', $details->semester ?? '') }}">
                                                @error('semester')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="student_city_program_id" class="form-label required">Programa</label>
                                                <select id="student_city_program_id" name="city_program_id" class="form-select @error('city_program_id') is-invalid @enderror">
                                                    <option value="">Selecciona un programa…</option>
                                                    @foreach($cityPrograms as $program)
                                                        <option value="{{ $program->id }}" {{ old('city_program_id', $details->city_program_id ?? '') == $program->id ? 'selected' : '' }}>
                                                            {{ $program->full_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('city_program_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Specific fields for teachers -->
                                <div id="professor-fields" class="role-fields" style="{{ in_array(old('role', $user->role), ['professor', 'committee_leader']) ? '' : 'display: none;' }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="professor_city_program_id" class="form-label required">Programa</label>
                                                <select id="professor_city_program_id" name="city_program_id" class="form-select @error('city_program_id') is-invalid @enderror">
                                                    <option value="">Selecciona un programa…</option>
                                                    @foreach($cityPrograms as $program)
                                                        <option value="{{ $program->id }}" {{ old('city_program_id', $details->city_program_id ?? '') == $program->id ? 'selected' : '' }}>
                                                            {{ $program->full_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('city_program_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <input type="hidden" id="committee_leader" name="committee_leader" value="{{ old('committee_leader', $details->committee_leader ?? ($user->role === 'committee_leader' ? '1' : '0')) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="form-footer d-flex justify-content-between align-items-center">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18" />
                                    <line x1="6" y1="6" x2="18" y2="18" />
                                </svg>
                                Cancelar
                            </a>
                            
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                                Actualizar usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const committeeLeaderInput = document.getElementById('committee_leader');
            const studentFields = document.getElementById('student-fields');
            const professorFields = document.getElementById('professor-fields');
            const semesterField = document.getElementById('semester');
            const studentCityProgramField = document.getElementById('student_city_program_id');
            const professorCityProgramField = document.getElementById('professor_city_program_id');
            
            function toggleRoleFields() {
                const role = roleSelect.value;
                
                // Ocultar todos los campos específicos
                if (studentFields) studentFields.style.display = 'none';
                if (professorFields) professorFields.style.display = 'none';
                
                // Remover el atributo required de todos los campos relevantes
                if (semesterField) semesterField.removeAttribute('required');
                if (studentCityProgramField) studentCityProgramField.removeAttribute('required');
                if (professorCityProgramField) professorCityProgramField.removeAttribute('required');
                
                // Mostrar los campos según el rol y agregar required
                if (role === 'student') {
                    if (studentFields) studentFields.style.display = 'block';
                    if (semesterField) semesterField.required = true;
                    if (studentCityProgramField) studentCityProgramField.required = true;
                    committeeLeaderInput.value = '0';
                } else if (role === 'professor' || role === 'committee_leader') {
                    if (professorFields) professorFields.style.display = 'block';
                    if (professorCityProgramField) professorCityProgramField.required = true;
                    
                    // Actualizar automáticamente el valor de committee_leader
                    if (role === 'committee_leader') {
                        committeeLeaderInput.value = '1';
                    } else {
                        committeeLeaderInput.value = '0';
                    }
                }
            }
            
            // Inicializar
            toggleRoleFields();
            
            // Escuchar cambios
            if (roleSelect) {
                roleSelect.addEventListener('change', toggleRoleFields);
            }
        });
    </script>
@endsection