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

<!-- Información adicional para edición -->
@if(isset($framework) && $framework->id)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <div class="d-flex align-items-center">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 8v4"/>
                        <path d="M12 16h.01"/>
                    </svg>
                </div>
                <div>
                    <h4 class="alert-title">Información del Framework</h4>
                    <div class="text-muted">
                        <strong>ID:</strong> #{{ $framework->id }} | 
                        <strong>Creado:</strong> {{ $framework->created_at?->format('d/m/Y H:i') }}
                        @if($framework->updated_at && $framework->updated_at != $framework->created_at)
                            | <strong>Última actualización:</strong> {{ $framework->updated_at?->format('d/m/Y H:i') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Vista previa del período -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div class="flex-fill">
                        <h4 class="mb-1">Vista Previa del Período de Vigencia</h4>
                        <div class="text-muted" id="period-preview">
                            <span id="preview-start">{{ old('start_year', $framework->start_year ?? date('Y')) }}</span>
                            <span class="mx-2">—</span>
                            <span id="preview-end">{{ old('end_year', $framework->end_year ?? 'Presente') }}</span>
                            <span class="ms-2 badge bg-blue-lt" id="preview-duration"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botones de acción -->
<div class="form-footer">
    <div class="d-flex justify-content-between align-items-center">
        <!-- Botón cancelar -->
        <a href="{{ route('frameworks.index') }}" class="btn btn-outline-secondary btn-cancel">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
            Cancelar
        </a>
        
        <!-- Información de progreso -->
        <div class="text-muted small">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M9 12l2 2l4 -4"/>
                <circle cx="12" cy="12" r="9"/>
            </svg>
            <span id="form-progress">Completa todos los campos requeridos</span>
        </div>
        
        <!-- Botón guardar -->
        <button type="submit" class="btn btn-primary" id="submit-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-16a2 2 0 0 1 2 -2"/>
                <circle cx="12" cy="14" r="2"/>
                <polyline points="14 4 14 8 8 8 8 4"/>
            </svg>
            @if(isset($framework) && $framework->id)
                Actualizar Framework
            @else
                Crear Framework
            @endif
        </button>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const startYearInput = document.getElementById('start_year');
    const endYearInput = document.getElementById('end_year');
    const submitBtn = document.getElementById('submit-btn');
    const formProgress = document.getElementById('form-progress');
    const previewStart = document.getElementById('preview-start');
    const previewEnd = document.getElementById('preview-end');
    const previewDuration = document.getElementById('preview-duration');
    
    // Función para actualizar la vista previa del período
    function updatePeriodPreview() {
        const startYear = parseInt(startYearInput.value) || new Date().getFullYear();
        const endYear = endYearInput.value ? parseInt(endYearInput.value) : null;
        
        previewStart.textContent = startYear;
        previewEnd.textContent = endYear || 'Presente';
        
        // Calcular duración
        const currentYear = new Date().getFullYear();
        let duration;
        
        if (endYear) {
            duration = endYear - startYear + 1;
            previewDuration.textContent = duration + (duration === 1 ? ' año' : ' años');
            previewDuration.className = 'ms-2 badge bg-blue-lt';
        } else {
            const yearsActive = Math.max(currentYear - startYear + 1, 1);
            previewDuration.textContent = yearsActive + (yearsActive === 1 ? ' año' : ' años') + ' activo';
            previewDuration.className = 'ms-2 badge bg-green-lt';
        }
    }
    
    // Función para validar el formulario
    function validateForm() {
        const name = nameInput.value.trim();
        const description = descriptionInput.value.trim();
        const startYear = startYearInput.value;
        
        const isValid = name.length >= 3 && description.length >= 10 && startYear;
        
        submitBtn.disabled = !isValid;
        
        if (!isValid) {
            let missing = [];
            if (name.length < 3) missing.push('nombre válido');
            if (description.length < 10) missing.push('descripción completa');
            if (!startYear) missing.push('año de inicio');
            
            formProgress.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1 text-warning" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 8v4"/>
                    <path d="M12 16h.01"/>
                    <circle cx="12" cy="12" r="9"/>
                </svg>
                Falta: ${missing.join(', ')}
            `;
        } else {
            formProgress.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1 text-success" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 12l2 2l4 -4"/>
                    <circle cx="12" cy="12" r="9"/>
                </svg>
                Formulario completo y listo para enviar
            `;
        }
    }
    
    // Event listeners
    if (startYearInput) {
        startYearInput.addEventListener('input', function() {
            updatePeriodPreview();
            validateForm();
            
            // Actualizar el mínimo del año de fin
            if (endYearInput) {
                endYearInput.min = this.value;
                
                // Si el año de fin es menor que el de inicio, limpiarlo
                if (endYearInput.value && parseInt(endYearInput.value) < parseInt(this.value)) {
                    endYearInput.value = '';
                    updatePeriodPreview();
                }
            }
        });
    }
    
    if (endYearInput) {
        endYearInput.addEventListener('input', updatePeriodPreview);
    }
    
    if (nameInput) {
        nameInput.addEventListener('input', validateForm);
    }
    
    if (descriptionInput) {
        descriptionInput.addEventListener('input', validateForm);
        
        // Contador de caracteres
        descriptionInput.addEventListener('input', function() {
            const hint = this.parentElement.querySelector('.form-hint');
            const length = this.value.length;
            const maxLength = parseInt(this.getAttribute('maxlength')) || 1000;
            
            if (hint) {
                const originalHint = hint.innerHTML.split('<br>')[0];
                hint.innerHTML = originalHint + `<br><small class="text-muted">${length}/${maxLength} caracteres</small>`;
            }
        });
    }
    
    // Inicializar
    updatePeriodPreview();
    validateForm();
    
    // Prevenir envío si el formulario no es válido
    const form = document.getElementById('frameworkForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (submitBtn.disabled) {
                e.preventDefault();
                alert('Por favor, completa todos los campos requeridos antes de enviar el formulario.');
            }
        });
    }
});
</script>
@endpush