
@php
    $showSubmit = $showSubmit ?? (!isset($framework) || !$framework?->exists);
@endphp

<!-- Nombre del Framework -->
<div class="row mb-4">
    <div class="col-12">
        <div class="form-group">
            <label class="form-label required" for="name">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1 text-primary" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="9" y1="9" x2="15" y2="9"/>
                    <line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
                Nombre del Framework
            </label>
            <input type="text" 
                   id="name"
                   name="name" 
                   value="{{ old('name', $framework->name ?? '') }}" 
                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" 
                   placeholder="Ej: Currículo Nacional de Educación Básica 2025"
                   maxlength="255"
                   required>
            @if($errors->has('name'))
                <div class="invalid-feedback">{{ $errors->first('name') }}</div>
            @endif
            <small class="form-hint">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 8v4"/>
                    <path d="M12 16h.01"/>
                </svg>
                Ingresa un nombre descriptivo y único para identificar este framework curricular.
            </small>
        </div>
    </div>
</div>

<!-- Descripción -->
<div class="row mb-4">
    <div class="col-12">
        <div class="form-group">
            <label class="form-label required" for="description">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1 text-primary" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                    <line x1="9" y1="9" x2="10" y2="9"/>
                    <line x1="9" y1="13" x2="15" y2="13"/>
                    <line x1="9" y1="17" x2="15" y2="17"/>
                </svg>
                Descripción del Framework
            </label>
            <textarea id="description"
                      name="description" 
                      class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" 
                      rows="4"
                      placeholder="Describe los objetivos, alcance y características principales de este framework curricular..."
                      maxlength="1000"
                      required>{{ old('description', $framework->description ?? '') }}</textarea>
            @if($errors->has('description'))
                <div class="invalid-feedback">{{ $errors->first('description') }}</div>
            @endif
            <small class="form-hint">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 8v4"/>
                    <path d="M12 16h.01"/>
                </svg>
                Proporciona una descripción detallada del propósito y contenido de este framework (mínimo 10 caracteres).
            </small>
        </div>
    </div>
</div>

<!-- Link (nuevo, opcional) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="form-group">
            <label class="form-label" for="link">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1 text-azure" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 14a3.5 3.5 0 0 0 5 0l3-3a3.5 3.5 0 1 0 -5 -5l-.5 .5"/>
                    <path d="M14 10a3.5 3.5 0 0 0 -5 0l-3 3a3.5 3.5 0 1 0 5 5l.5 -.5"/>
                </svg>
                Link (opcional)
            </label>
            <input type="url"
                   id="link"
                   name="link"
                   value="{{ old('link', $framework->link ?? '') }}"
                   class="form-control {{ $errors->has('link') ? 'is-invalid' : '' }}"
                   placeholder="https://ejemplo.gob/mi-framework">
            @if($errors->has('link'))
                <div class="invalid-feedback">{{ $errors->first('link') }}</div>
            @endif
            <small class="form-hint">
                URL pública relacionada con este framework (documento oficial, normativa, etc.). Si la proporcionas, debe empezar por <code>http://</code> o <code>https://</code>.
            </small>
        </div>
    </div>
</div>

<!-- Período de Vigencia -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="form-group">
            <label class="form-label required" for="start_year">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1 text-success" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <polyline points="12 7 12 12 15 15"/>
                </svg>
                Año de Inicio
            </label>
            <input type="number" 
                   id="start_year"
                   name="start_year" 
                   value="{{ old('start_year', $framework->start_year ?? date('Y')) }}" 
                   class="form-control {{ $errors->has('start_year') ? 'is-invalid' : '' }}" 
                   placeholder="{{ date('Y') }}"
                   min="1900" 
                   max="{{ date('Y') + 50 }}"
                   required>
            @if($errors->has('start_year'))
                <div class="invalid-feedback">{{ $errors->first('start_year') }}</div>
            @endif
            <small class="form-hint">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 8v4"/>
                    <path d="M12 16h.01"/>
                </svg>
                Año en que entra en vigencia este framework curricular.
            </small>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <label class="form-label" for="end_year">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1 text-warning" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 8v4"/>
                    <path d="M12 16h.01"/>
                </svg>
                Año de Finalización
                <span class="badge bg-azure-lt ms-1">Opcional</span>
            </label>
            <input type="number" 
                   id="end_year"
                   name="end_year" 
                   value="{{ old('end_year', $framework->end_year ?? '') }}" 
                   class="form-control {{ $errors->has('end_year') ? 'is-invalid' : '' }}" 
                   placeholder="Dejar vacío si es indefinido"
                   min="{{ old('start_year', $framework->start_year ?? date('Y')) }}" 
                   max="{{ date('Y') + 50 }}">
            @if($errors->has('end_year'))
                <div class="invalid-feedback">{{ $errors->first('end_year') }}</div>
            @endif
            <small class="form-hint">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 8v4"/>
                    <path d="M12 16h.01"/>
                </svg>
                Año de finalización (opcional). Si se deja vacío, se considera vigente indefinidamente.
            </small>
        </div>
    </div>
</div>

{{-- (El resto del archivo permanece igual) --}}
@if($showSubmit)
    <hr class="mt-4 mb-3">
    <div class="form-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
        <a href="{{ route('frameworks.index') }}" class="btn btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
            Cancelar
        </a>

        <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z"/>
                <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-16a2 2 0 0 1 2 -2"/>
                <circle cx="12" cy="14" r="2"/>
                <polyline points="14 4 14 8 8 8 8 4"/>
            </svg>
            Guardar Framework
        </button>
    </div>
@endif
