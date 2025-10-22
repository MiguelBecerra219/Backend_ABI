{{--
    Partial path: projects/form.blade.php.
    Purpose: Shared Tablar form fragment for project create and edit screens.
    Expected variables:
    - $project (optional): existing project information provided by the controller or the view.
--}}
@php
    $projectModel = $project ?? null;
@endphp

<div class="mb-3">
    <label for="project_title" class="form-label required">Título del proyecto</label>
    <input
        type="text"
        id="project_title"
        name="title"
        maxlength="255"
        class="form-control"
        value="{{ old('title', $projectModel->title ?? '') }}"
        placeholder="Ej. Desarrollo de plataforma educativa"
        required
        autocomplete="off"
    >
    <small class="form-hint">El título se ajustará automáticamente siguiendo la normalización académica.</small>
    <div class="invalid-feedback d-none" data-feedback-for="title"></div>
</div>

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label for="project_thematic_area" class="form-label required">Área temática</label>
        <select id="project_thematic_area" name="thematic_area_id" class="form-select" required>
            <option value="">Selecciona un área temática</option>
        </select>
        <div class="invalid-feedback d-none" data-feedback-for="thematic_area_id"></div>
    </div>
    <div class="col-12 col-md-6">
        <label for="project_status" class="form-label required">Estado del proyecto</label>
        <select id="project_status" name="project_status_id" class="form-select" required>
            <option value="">Selecciona un estado</option>
        </select>
        <div class="invalid-feedback d-none" data-feedback-for="project_status_id"></div>
    </div>
</div>

<div class="mb-3 mt-3">
    <label for="project_evaluation" class="form-label">Criterios de evaluación</label>
    <textarea
        id="project_evaluation"
        name="evaluation_criteria"
        class="form-control"
        rows="4"
        placeholder="Describe los requisitos y rúbricas que deben cumplir los postulantes."
    >{{ old('evaluation_criteria', $projectModel->evaluation_criteria ?? '') }}</textarea>
    <div class="invalid-feedback d-none" data-feedback-for="evaluation_criteria"></div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-6">
        <label for="project_professors" class="form-label">Profesores asignados</label>
        <select id="project_professors" name="professor_ids[]" class="form-select" multiple size="8"></select>
        <small class="form-hint">Usa Ctrl o Cmd para seleccionar múltiples docentes.</small>
        <div class="invalid-feedback d-none" data-feedback-for="professor_ids"></div>
    </div>
    <div class="col-12 col-lg-6">
        <label for="project_students" class="form-label">Estudiantes participantes</label>
        <select id="project_students" name="student_ids[]" class="form-select" multiple size="8"></select>
        <small class="form-hint">Solo se listan estudiantes activos del programa correspondiente.</small>
        <div class="invalid-feedback d-none" data-feedback-for="student_ids"></div>
    </div>
</div>
