{{--
    View path: projects/edit.blade.php.
    Purpose: Displays the Tablar-styled edit screen for research projects using the shared form.
    Expected variables:
    - $projectId (int): identifier of the project to edit, provided via the route closure.
--}}
@extends('tablar::page')

@section('title', 'Editar proyecto')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Proyectos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-success" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3z" />
                        </svg>
                        Editar proyecto #{{ $projectId }}
                    </h2>
                    <p class="text-muted mb-0">Actualiza la información del proyecto y sus participantes asociados.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div id="project-edit-alert" class="alert d-none" role="alert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Datos generales</h3>
                    <div class="card-actions">
                        <span class="badge bg-success">ID {{ $projectId }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <form id="project-edit-form" novalidate autocomplete="off">
                        @include('projects.form')
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('projects.index') }}" class="btn btn-link">Cancelar</a>
                            <button type="submit" class="btn btn-success">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const apiBase = '{{ url('/api/projects') }}';
            const metaUrl = '{{ url('/api/projects/meta') }}';
            const indexUrl = '{{ route('projects.index') }}';
            const projectId = {{ (int) $projectId }};

            const form = document.getElementById('project-edit-form');
            const alertBox = document.getElementById('project-edit-alert');
            const submitButton = form.querySelector('button[type="submit"]');

            const titleInput = document.getElementById('project_title');
            const thematicAreaSelect = document.getElementById('project_thematic_area');
            const statusSelect = document.getElementById('project_status');
            const evaluationTextarea = document.getElementById('project_evaluation');
            const professorsSelect = document.getElementById('project_professors');
            const studentsSelect = document.getElementById('project_students');

            const metaState = {
                thematicAreas: [],
                statuses: [],
                professors: [],
                students: [],
            };

            // Displays contextual feedback above the edit form.
            function showAlert(type, message) {
                alertBox.className = `alert alert-${type}`;
                alertBox.textContent = message;
                alertBox.classList.remove('d-none');
            }

            // Clears the alert container to prepare for a fresh attempt.
            function hideAlert() {
                alertBox.classList.add('d-none');
                alertBox.textContent = '';
            }

            // Removes validation markers from every control.
            function resetFeedback() {
                form.querySelectorAll('.is-invalid').forEach(element => element.classList.remove('is-invalid'));
                form.querySelectorAll('.invalid-feedback').forEach(feedback => {
                    feedback.classList.add('d-none');
                    feedback.textContent = '';
                });
            }

            // Associates the validation message with the correct field.
            function setFieldError(fieldName, message) {
                const feedback = form.querySelector(`[data-feedback-for="${fieldName}"]`);
                let input;
                switch (fieldName) {
                    case 'title':
                        input = titleInput;
                        break;
                    case 'thematic_area_id':
                        input = thematicAreaSelect;
                        break;
                    case 'project_status_id':
                        input = statusSelect;
                        break;
                    case 'evaluation_criteria':
                        input = evaluationTextarea;
                        break;
                    case 'professor_ids':
                        input = professorsSelect;
                        break;
                    case 'student_ids':
                        input = studentsSelect;
                        break;
                    default:
                        input = null;
                        break;
                }

                if (input) {
                    input.classList.add('is-invalid');
                }

                if (feedback) {
                    feedback.classList.remove('d-none');
                    feedback.textContent = message;
                }
            }

            // Rebuilds the single-select control with the available catalog items.
            function populateSelect(select, items, placeholder) {
                if (!select) {
                    return;
                }
                const current = select.value;
                select.innerHTML = '';

                if (placeholder !== null) {
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = placeholder;
                    select.appendChild(defaultOption);
                }

                items.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    select.appendChild(option);
                });

                select.value = current;
            }

            // Rebuilds the multi-select control while preserving previous choices.
            function populateMultiSelect(select, items) {
                if (!select) {
                    return;
                }
                const selectedValues = Array.from(select.selectedOptions || []).map(option => option.value);
                select.innerHTML = '';

                items.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = `${item.name} · ${item.card_id}`;
                    if (selectedValues.includes(String(item.id))) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            }

            // Converts the selected multi-value options into numeric identifiers.
            function collectSelectedValues(select) {
                return Array.from(select.selectedOptions || []).map(option => Number(option.value));
            }

            // Populates the form fields with the project data fetched from the API.
            function applyProjectData(project) {
                titleInput.value = project.title ?? '';
                thematicAreaSelect.value = project.thematic_area_id ?? '';
                statusSelect.value = project.project_status_id ?? '';
                evaluationTextarea.value = project.evaluation_criteria ?? '';

                const professorIds = Array.isArray(project.professors) ? project.professors.map(item => String(item.id)) : [];
                Array.from(professorsSelect.options).forEach(option => {
                    option.selected = professorIds.includes(option.value);
                });

                const studentIds = Array.isArray(project.students) ? project.students.map(item => String(item.id)) : [];
                Array.from(studentsSelect.options).forEach(option => {
                    option.selected = studentIds.includes(option.value);
                });
            }

            async function fetchMeta() {
                const response = await fetch(metaUrl, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('No fue posible cargar los catálogos necesarios.');
                }

                const data = await response.json();
                metaState.thematicAreas = Array.isArray(data.thematic_areas) ? data.thematic_areas : [];
                metaState.statuses = Array.isArray(data.statuses) ? data.statuses : [];
                metaState.professors = Array.isArray(data.professors) ? data.professors : [];
                metaState.students = Array.isArray(data.students) ? data.students : [];

                populateSelect(thematicAreaSelect, metaState.thematicAreas, 'Selecciona un área temática');
                populateSelect(statusSelect, metaState.statuses, 'Selecciona un estado');
                populateMultiSelect(professorsSelect, metaState.professors);
                populateMultiSelect(studentsSelect, metaState.students);
            }

            async function loadProject() {
                const response = await fetch(`${apiBase}/${projectId}`, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('No fue posible cargar la información del proyecto solicitado.');
                }
                return response.json();
            }

            form.addEventListener('submit', async event => {
                event.preventDefault();
                hideAlert();
                resetFeedback();

                const payload = {
                    title: titleInput.value.trim(),
                    thematic_area_id: thematicAreaSelect.value ? Number(thematicAreaSelect.value) : null,
                    project_status_id: statusSelect.value ? Number(statusSelect.value) : null,
                    evaluation_criteria: evaluationTextarea.value.trim() || null,
                    professor_ids: collectSelectedValues(professorsSelect),
                    student_ids: collectSelectedValues(studentsSelect),
                };

                submitButton.disabled = true;
                submitButton.classList.add('btn-loading');

                try {
                    const response = await fetch(`${apiBase}/${projectId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    const body = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        if (response.status === 422 && body?.errors) {
                            Object.entries(body.errors).forEach(([key, messages]) => {
                                const normalizedKey = key.startsWith('professor_ids') ? 'professor_ids'
                                    : key.startsWith('student_ids') ? 'student_ids'
                                    : key;
                                setFieldError(normalizedKey, messages[0]);
                            });
                        }

                        showAlert('danger', body.message ?? 'No fue posible actualizar el proyecto.');
                        return;
                    }

                    showAlert('success', body.message ?? 'Proyecto actualizado correctamente. Redirigiendo…');
                    setTimeout(() => {
                        window.location.href = indexUrl;
                    }, 1200);
                } catch (error) {
                    showAlert('danger', error.message || 'Ocurrió un error inesperado.');
                } finally {
                    submitButton.disabled = false;
                    submitButton.classList.remove('btn-loading');
                }
            });

            (async () => {
                try {
                    await fetchMeta();
                    const project = await loadProject();
                    applyProjectData(project);
                } catch (error) {
                    showAlert('danger', error.message || 'No fue posible cargar los datos iniciales.');
                    submitButton.disabled = true;
                    submitButton.classList.add('disabled');
                }
            })();
        });
    </script>
@endpush

@push('css')
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .08);
        }
    </style>
@endpush
