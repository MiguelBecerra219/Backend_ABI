@php
    // Valor por defecto si viene preseleccionado por query string (?framework_id=XX)
    $preselectedFrameworkId = old('framework_id', $prefw ?? ($contentFrameworkProject->framework_id ?? null));

    // Preferimos que el controlador pase $frameworks (id => name). Si no, hacemos un fallback seguro.
    if (!isset($frameworks)) {
        try {
            $frameworks = \App\Models\Framework::orderBy('name')->pluck('name', 'id')->toArray();
        } catch (\Throwable $e) {
            $frameworks = [];
        }
    }
@endphp

<div class="row">
    <div class="col-lg-7">
        <!-- Nombre -->
        <div class="mb-3">
            <label for="name" class="form-label required">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1 text-primary" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="9" y1="9" x2="15" y2="9"/>
                </svg>
                Nombre del Contenido
            </label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                placeholder="Ej: Competencias de Comunicación 1"
                maxlength="255"
                value="{{ old('name', $contentFrameworkProject->name ?? '') }}"
                required
            >
            @if($errors->has('name'))
                <div class="invalid-feedback">{{ $errors->first('name') }}</div>
            @endif
            <small class="form-hint">Es el título corto y descriptivo del contenido.</small>
        </div>

       <!-- Descripción centrada -->
<div class="row">
    <div class="col-12 d-flex justify-content-center">
        <div class="mb-3 flex-grow-1" style="max-width: 700px; width: 100%;">
            <label for="description" class="form-label required text-center w-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1 text-primary" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"/>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                </svg>
                Descripción
            </label>
            <textarea
                id="description"
                name="description"
                rows="4"
                maxlength="1000"
                class="form-control text-center {{ $errors->has('description') ? 'is-invalid' : '' }}"
                placeholder="Describe el objetivo, alcance y características del contenido…"
                required
            >{{ old('description', $contentFrameworkProject->description ?? '') }}</textarea>
            @if($errors->has('description'))
                <div class="invalid-feedback">{{ $errors->first('description') }}</div>
            @endif
            <small class="form-hint text-center w-100">Mínimo 10 caracteres.</small>
        </div>
    </div>
</div>

@push('css')
<style>
    /* Centrar placeholder del textarea */
    #description::placeholder {
        text-align: center;
    }
</style>
@endpush
    </div>

    <div class="col-lg-5">
        <!-- Framework -->
        <div class="mb-3">
            <label for="framework_id" class="form-label required">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1 text-success" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <polyline points="12 7 12 12 15 15"/>
                </svg>
                Framework asociado
            </label>
            <select
                id="framework_id"
                name="framework_id"
                class="form-select {{ $errors->has('framework_id') ? 'is-invalid' : '' }}"
                required
            >
                <option value="" disabled {{ $preselectedFrameworkId ? '' : 'selected' }}>Selecciona un framework…</option>
                @foreach($frameworks as $fid => $fname)
                    <option value="{{ $fid }}" {{ (string)$preselectedFrameworkId === (string)$fid ? 'selected' : '' }}>
                        {{ $fname }}
                    </option>
                @endforeach
            </select>
            @if($errors->has('framework_id'))
                <div class="invalid-feedback">{{ $errors->first('framework_id') }}</div>
            @endif
            <small class="form-hint">El contenido quedará vinculado a este framework.</small>

            @if($preselectedFrameworkId)
                <div class="mt-2">
                    <a class="badge bg-azure-lt text-decoration-none" href="{{ route('frameworks.show', $preselectedFrameworkId) }}">
                        Ver framework seleccionado
                    </a>
                </div>
            @endif
        </div>

        <!-- Ayuditas -->
        @if(isset($contentFrameworkProject) && $contentFrameworkProject?->id)
            <div class="alert alert-info">
                <div class="d-flex">
                    <div class="me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 8v4"/>
                            <path d="M12 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fw-bold">Información del registro</div>
                        <div class="text-muted small">
                            <b>ID:</b> #{{ $contentFrameworkProject->id }} |
                            <b>Creado:</b> {{ $contentFrameworkProject->created_at?->format('d/m/Y H:i') }}
                            @if($contentFrameworkProject->updated_at && $contentFrameworkProject->updated_at != $contentFrameworkProject->created_at)
                                | <b>Actualizado:</b> {{ $contentFrameworkProject->updated_at?->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<hr class="my-3">

<!-- Footer del formulario -->
<div class="form-footer">
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('content-framework-projects.index') }}" class="btn btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
            Cancelar
        </a>

        <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 12l2 2l4 -4"/>
                <circle cx="12" cy="12" r="9"/>
            </svg>
            {{ isset($contentFrameworkProject) && $contentFrameworkProject?->id ? 'Actualizar Contenido' : 'Crear Contenido' }}
        </button>
    </div>
</div>

@push('css')
<style>
    /* Centrar placeholder del textarea */
    #description::placeholder {
        text-align: center;
    }
</style>
@endpush
