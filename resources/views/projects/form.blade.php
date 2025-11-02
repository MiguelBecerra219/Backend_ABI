{{--
    Partial path: projects/form.blade.php.
    Purpose: Shared form fields for create and edit operations.
--}}
@php
    $projectModel = $project ?? null;
    $contentValues = $contentValues ?? [];
    $prefill = $prefill ?? [];
    $cities = $cities ?? collect();
    $programs = $programs ?? collect();
    $investigationLines = $investigationLines ?? collect();
    $thematicAreas = $thematicAreas ?? collect();
    $availableStudents = $availableStudents ?? collect();
    $availableProfessors = $availableProfessors ?? collect();
@endphp

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label">Fecha de entrega</label>
        <input type="text" class="form-control" value="{{ $prefill['delivery_date'] ?? now()->format('Y-m-d') }}" readonly>
        <small class="form-hint">Se registra automáticamente con la fecha actual.</small>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Ciudad</label>
        <input type="text"
               class="form-control"
               value="{{ $cities->firstWhere('id', $prefill['city_id'])->name ?? 'Ciudad no disponible' }}"
               readonly>
        <input type="hidden" name="city_id" value="{{ $prefill['city_id'] }}">
        <small class="form-hint">Asignada automáticamente según tu usuario.</small>
    </div>
</div>


@if ($isProfessor)
    <div class="row g-3 mt-0">
        <div class="col-12 col-md-6">
            <label for="program_id" class="form-label required">Programa académico</label>
            <select id="program_id" name="program_id" class="form-select @error('program_id') is-invalid @enderror" required>
                <option value="">Selecciona un programa</option>
                @foreach ($programs as $program)
                    <option value="{{ $program->id }}" {{ (string) old('program_id', $prefill['program_id'] ?? '') === (string) $program->id ? 'selected' : '' }}>
                        {{ $program->name }}
                    </option>
                @endforeach
            </select>
            @error('program_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12 col-md-6">
            <label for="co_professor_ids" class="form-label">Profesores asociados</label>
            <select id="co_professor_ids" name="co_professor_ids[]" class="form-select @error('co_professor_ids') is-invalid @enderror" multiple size="6">
                @php
                    $fullUser = \App\Helpers\AuthUserHelper::fullUser();
                    $currentProfessorId = optional($fullUser?->professor)->id;
                    $selectedProfessors = collect(old('co_professor_ids', $projectModel?->professors?->pluck('id')->reject(fn ($id) => $id === $currentProfessorId)->all() ?? []));
                @endphp
                @foreach ($availableProfessors as $professorOption)
                    <option value="{{ $professorOption->id }}" {{ $selectedProfessors->contains($professorOption->id) ? 'selected' : '' }}>
                        {{ $professorOption->name }} {{ $professorOption->last_name }}
                    </option>
                @endforeach
            </select>
            <small class="form-hint">Selecciona colegas que acompañarán la propuesta.</small>
            @error('co_professor_ids')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @error('co_professor_ids.*')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
@else
    <div class="row g-3 mt-0">
        <div class="col-12 col-md-6">
            <label class="form-label">Programa académico</label>
            <input type="text" class="form-control" value="{{ $programs->firstWhere('id', $prefill['program_id'] ?? null)->name ?? 'No asignado' }}" readonly>
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label">Grupo de investigación</label>
            <input type="text" class="form-control" value="{{ $prefill['research_group'] ?? 'No asignado' }}" readonly>
        </div>
    </div>
@endif

<div class="row g-3 mt-0">
    <div class="col-12 col-md-6">
        <label for="investigation_line_id" class="form-label required">Línea de investigación</label>
        <select id="investigation_line_id" name="investigation_line_id"
                class="form-select @error('investigation_line_id') is-invalid @enderror" required>
            <option value="">Selecciona una línea</option>
            @foreach ($investigationLines as $line)
                <option value="{{ $line->id }}"
                    {{ (string) old('investigation_line_id', $projectModel?->thematicArea?->investigation_line_id ?? '') === (string) $line->id ? 'selected' : '' }}>
                    {{ $line->name }}
                </option>
            @endforeach
        </select>
        @error('investigation_line_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="thematic_area_id" class="form-label required">Área temática</label>
        <select id="thematic_area_id" name="thematic_area_id"
                class="form-select @error('thematic_area_id') is-invalid @enderror" required disabled>
            <option value="">Selecciona un área temática</option>
            @foreach ($thematicAreas as $area)
                <option value="{{ $area->id }}"
                        data-line="{{ $area->investigation_line_id }}"
                        {{ (string) old('thematic_area_id', $projectModel?->thematic_area_id ?? '') === (string) $area->id ? 'selected' : '' }}>
                    {{ $area->name }}
                </option>
            @endforeach
        </select>
        <small class="form-hint">Selecciona primero una linea de investigación.</small>
        @error('thematic_area_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


<div class="mb-3 mt-3">
    <label for="title" class="form-label required">Título del proyecto</label>
    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $projectModel->title ?? '') }}" maxlength="255" required>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if ($isProfessor)
    <div class="mb-3">
        <label for="evaluation_criteria" class="form-label required">Criterios de evaluación</label>
        <textarea id="evaluation_criteria" name="evaluation_criteria" class="form-control @error('evaluation_criteria') is-invalid @enderror" rows="3" required>{{ old('evaluation_criteria', $projectModel->evaluation_criteria ?? '') }}</textarea>
        @error('evaluation_criteria')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
@endif

@if ($isProfessor)
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <label for="students_count" class="form-label required">Cantidad de estudiantes</label>
            <input type="number" min="1" max="3" id="students_count" name="students_count" class="form-control @error('students_count') is-invalid @enderror" value="{{ old('students_count', $contentValues['Cantidad de estudiantes'] ?? '') }}" required>
            @error('students_count')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12 col-md-8">
            <label for="execution_time" class="form-label required">Tiempo de ejecución</label>
            <input type="text" id="execution_time" name="execution_time" class="form-control @error('execution_time') is-invalid @enderror" value="{{ old('execution_time', $contentValues['Tiempo de ejecución'] ?? '') }}" required>
            @error('execution_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3 mt-3">
        <label for="viability" class="form-label required">Viabilidad</label>
        <textarea id="viability" name="viability" class="form-control @error('viability') is-invalid @enderror" rows="3" required>{{ old('viability', $contentValues['Viabilidad'] ?? '') }}</textarea>
        @error('viability')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="relevance" class="form-label required">Pertinencia con el grupo y programa</label>
        <textarea id="relevance" name="relevance" class="form-control @error('relevance') is-invalid @enderror" rows="3" required>{{ old('relevance', $contentValues['Pertinencia con el grupo de investigación y con el programa'] ?? '') }}</textarea>
        @error('relevance')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="teacher_availability" class="form-label required">Disponibilidad de docentes</label>
        <textarea id="teacher_availability" name="teacher_availability" class="form-control @error('teacher_availability') is-invalid @enderror" rows="3" required>{{ old('teacher_availability', $contentValues['Disponibilidad de docentes para su dirección y calificación'] ?? '') }}</textarea>
        @error('teacher_availability')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="title_objectives_quality" class="form-label required">Calidad y correspondencia título – objetivos</label>
        <textarea id="title_objectives_quality" name="title_objectives_quality" class="form-control @error('title_objectives_quality') is-invalid @enderror" rows="3" required>{{ old('title_objectives_quality', $contentValues['Calidad y correspondencia entre título y objetivo'] ?? '') }}</textarea>
        @error('title_objectives_quality')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
@endif

<div class="mb-3">
    <label for="general_objective" class="form-label required">Objetivo general</label>
    <textarea id="general_objective" name="general_objective" class="form-control @error('general_objective') is-invalid @enderror" rows="3" required>{{ old('general_objective', $contentValues['Objetivo general del proyecto'] ?? '') }}</textarea>
    @error('general_objective')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label required">Descripción del proyecto</label>
    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $contentValues['Descripción del proyecto de investigación'] ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if ($isStudent)
<div class="mb-3">
    <label class="form-label">Compañeros (máximo 2 adicionales)</label>

    {{-- Campo de búsqueda --}}
    <input type="text" id="student-search" class="form-control mb-2" placeholder="Buscar por nombre o cédula...">

    {{-- Lista filtrable --}}
    <div id="student-list" class="list-group" style="max-height: 180px; overflow-y: auto;">
        @foreach ($availableStudents as $s)
            <button type="button"
                class="list-group-item list-group-item-action student-option"
                data-id="{{ $s->id }}"
                data-name="{{ $s->name }} {{ $s->last_name }}"
                data-card="{{ $s->card_id }}">
                {{ $s->name }} {{ $s->last_name }} - {{ $s->card_id }}
            </button>
        @endforeach
    </div>

    {{-- Seleccionados --}}
    <div id="selected-students" class="mt-3"></div>

    {{-- Inputs hidden serán insertados aquí --}}
    <div id="selected-students-inputs"></div>

    <small class="form-hint">Busca y selecciona estudiantes del mismo programa. Máximo 2.</small>
    @error('teammate_ids')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
@endif



<hr class="mt-4 mb-3">
<h4 class="mt-3">Marcos</h4>
<p class="text-muted mb-2">
    Selecciona el enfoque correspondiente para cada marco.
</p>

@foreach ($frameworks as $framework)
    <div class="mb-3">
        <label class="form-label required d-flex align-items-center gap-1">
            {{ $framework->name }}
            
            {{-- Ícono con tooltip --}}
            <span 
                class="text-muted" 
                data-bs-toggle="tooltip" 
                data-bs-placement="right" 
                title="{{ $framework->description }}"
                style="cursor: pointer;"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" fill="none"/>
                    <path d="M9.5 9.5a2.5 2.5 0 0 1 5 0 2.4 2.4 0 0 1-2 2.5 2 2 0 0 0-2 2v1" stroke="currentColor" fill="none"/>
                    <circle cx="12" cy="17" r="0.8" fill="currentColor" stroke="none"/>
                </svg>
            </span>
        </label>

        <select 
            name="content_frameworks[{{ $framework->id }}]" 
            class="form-select @error('content_frameworks.' . $framework->id) is-invalid @enderror" 
            required
        >
            <option value="">Selecciona una opción</option>

            @foreach ($framework->contentFrameworks as $content)
                <option value="{{ $content->id }}"
                    @if (old('content_frameworks.' . $framework->id, $projectModel?->contentFrameworkProjects?->firstWhere('content_framework_id', $content->id)?->content_framework_id ?? '') == $content->id) 
                        selected 
                    @endif
                >
                    {{ $content->name }}
                </option>
            @endforeach
        </select>

        @error('content_frameworks.' . $framework->id)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    });
</script>



<h4 class="mt-4">Datos de contacto</h4>
@if ($isProfessor)
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <label for="contact_first_name" class="form-label required">Nombres</label>
            <input type="text" id="contact_first_name" name="contact_first_name" class="form-control @error('contact_first_name') is-invalid @enderror" value="{{ old('contact_first_name', $prefill['first_name'] ?? '') }}" required>
            @error('contact_first_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12 col-md-6">
            <label for="contact_last_name" class="form-label required">Apellidos</label>
            <input type="text" id="contact_last_name" name="contact_last_name" class="form-control @error('contact_last_name') is-invalid @enderror" value="{{ old('contact_last_name', $prefill['last_name'] ?? '') }}" required>
            @error('contact_last_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row g-3 mt-0">
        <div class="col-12 col-md-6">
            <label for="contact_email" class="form-label required">Correo electrónico</label>
            <input type="email" id="contact_email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror" value="{{ old('contact_email', $prefill['email'] ?? '') }}" required>
            @error('contact_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12 col-md-6">
            <label for="contact_phone" class="form-label required">Teléfono</label>
            <input type="text" id="contact_phone" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" value="{{ old('contact_phone', $prefill['phone'] ?? '') }}" required>
            @error('contact_phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
@else
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <label for="student_first_name" class="form-label required">Nombres</label>
            <input type="text" id="student_first_name" name="student_first_name" class="form-control @error('student_first_name') is-invalid @enderror" value="{{ old('student_first_name', $prefill['first_name'] ?? '') }}" required>
            @error('student_first_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12 col-md-6">
            <label for="student_last_name" class="form-label required">Apellidos</label>
            <input type="text" id="student_last_name" name="student_last_name" class="form-control @error('student_last_name') is-invalid @enderror" value="{{ old('student_last_name', $prefill['last_name'] ?? '') }}" required>
            @error('student_last_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row g-3 mt-0">
        <div class="col-12 col-md-6">
            <label for="student_card_id" class="form-label required">Cédula</label>
            <input type="text" id="student_card_id" name="student_card_id" class="form-control @error('student_card_id') is-invalid @enderror" value="{{ old('student_card_id', $prefill['card_id'] ?? '') }}" required>
            @error('student_card_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12 col-md-6">
            <label for="student_email" class="form-label required">Correo electrónico</label>
            <input type="email" id="student_email" name="student_email" class="form-control @error('student_email') is-invalid @enderror" value="{{ old('student_email', $prefill['email'] ?? '') }}" required>
            @error('student_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row g-3 mt-0">
        <div class="col-12 col-md-6">
            <label for="student_phone" class="form-label">Teléfono</label>
            <input type="text" id="student_phone" name="student_phone" class="form-control @error('student_phone') is-invalid @enderror" value="{{ old('student_phone', $prefill['phone'] ?? '') }}">
            @error('student_phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
@endif


<script>
document.addEventListener('DOMContentLoaded', function () {
    const lineSelect = document.getElementById('investigation_line_id');
    const areaSelect = document.getElementById('thematic_area_id');
    const allAreas = [...areaSelect.options];

    function filterAreas() {
        const selectedLine = lineSelect.value;

        // Reset
        areaSelect.innerHTML = '<option value="">Selecciona un área temática</option>';

        if (!selectedLine) {
            areaSelect.disabled = true;
            return;
        }

        // Filtrar opciones válidas
        const filtered = allAreas.filter(opt => opt.dataset.line === selectedLine);

        filtered.forEach(opt => areaSelect.appendChild(opt));

        areaSelect.disabled = filtered.length === 0;
    }

    // Cuando cambia la línea, filtramos áreas
    lineSelect.addEventListener('change', filterAreas);

    // Si venimos del edit, filtramos automáticamente
    filterAreas();
});

document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('student-search');
    const studentList = document.getElementById('student-list');
    const selectedContainer = document.getElementById('selected-students');
    const hiddenInputsContainer = document.getElementById('selected-students-inputs');

    let selected = [];

    // Renderizar los seleccionados y los inputs hidden
    function renderSelected() {
        selectedContainer.innerHTML = '';
        hiddenInputsContainer.innerHTML = '';

        selected.forEach(student => {

            // Mostrar chip visual
            const chip = document.createElement('div');
            chip.className = "d-flex align-items-center justify-content-between p-2 mb-2 border rounded bg-body-secondary text-body";
            chip.innerHTML = `
                <span>${student.name} - ${student.card}</span>
                <button type="button" class="btn btn-sm btn-danger remove-student" data-id="${student.id}">X</button>
            `;
            selectedContainer.appendChild(chip);

            // Input hidden que se envía al servidor
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'teammate_ids[]';
            input.value = student.id;
            hiddenInputsContainer.appendChild(input);
        });
    }

    // Filtrar listados
    searchInput.addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('.student-option').forEach(btn => {
            const text = btn.textContent.toLowerCase();
            btn.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // Seleccionar estudiante
    document.querySelectorAll('.student-option').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;

            if (selected.length >= 2) {
                alert("Solo puedes seleccionar hasta 2 compañeros.");
                return;
            }

            if (!selected.find(s => s.id === id)) {
                selected.push({
                    id,
                    name: this.dataset.name,
                    card: this.dataset.card
                });
                renderSelected();
            }
        });
    });

    // Eliminar estudiante
    selectedContainer.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-student')) {
            const id = e.target.dataset.id;
            selected = selected.filter(s => s.id !== id);
            renderSelected();
        }
    });
});

</script>