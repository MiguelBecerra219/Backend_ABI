{{--
    View path: projects/create.blade.php.
    Purpose: Presents the Tablar-styled create screen for research projects using the shared form.
--}}
@extends('tablar::page')

@section('title', 'Registrar proyecto')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Proyectos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                        Registrar proyecto
                    </h2>
                    <p class="text-muted mb-0">Captura los datos principales del proyecto para asignarlo a docentes y estudiantes.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div id="project-form-alert" class="alert d-none" role="alert"></div>

            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información del proyecto</h3>
                            <div class="card-actions">
                                <small class="text-secondary">Los campos marcados con * son obligatorios</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="project-create-form" novalidate autocomplete="off">
                                @include('projects.form')
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('projects.index') }}" class="btn btn-link">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Guardar proyecto</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recomendaciones</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-start gap-2 mb-3">
                                    <span class="avatar bg-azure-lt text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M5 12h14" />
                                            <path d="M12 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                    <div>
                                        <strong>Asigna responsables</strong>
                                        <p class="text-secondary mb-0">Selecciona al menos un docente para facilitar el seguimiento del proyecto.</p>
                                    </div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <span class="avatar bg-azure-lt text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 12h6" />
                                            <path d="M12 9l3 3-3 3" />
                                            <path d="M5 12a7 7 0 1 1 14 0a7 7 0 1 1 -14 0" />
                                        </svg>
                                    </span>
                                    <div>
                                        <strong>Sin recargas</strong>
                                        <p class="text-secondary mb-0">El formulario se comunica con la API para enviar la información sin abandonar la página.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
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

            const form = document.getElementById('project-create-form');
            const alertBox = document.getElementById('project-form-alert');
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

            // Utility to show contextual feedback above the form.
            function showAlert(type, message) {
                alertBox.className = `alert alert-${type}`;
                alertBox.textContent = message;
                alertBox.classList.remove('d-none');
            }

            // Hides the global feedback container when retrying the submission.
            function hideAlert() {
                alertBox.classList.add('d-none');
                alertBox.textContent = '';
            }

            // Clears validation errors from all fields to prepare a fresh submission attempt.
            function resetFeedback() {
                form.querySelectorAll('.is-invalid').forEach(element => element.classList.remove('is-invalid'));
                form.querySelectorAll('.invalid-feedback').forEach(feedback => {
                    feedback.classList.add('d-none');
                    feedback.textContent = '';
                });
            }

            // Assigns the validation error message to the corresponding control.
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

            // Rebuilds a single-select element with the catalog options received from the API.
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

            // Rebuilds a multi-select element while preserving the user selection.
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

            // Helper that normalizes the selected multi-value options into numeric IDs.
            function collectSelectedValues(select) {
                return Array.from(select.selectedOptions || []).map(option => Number(option.value));
            }

            async function fetchMeta() {
                try {
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
                } catch (error) {
                    showAlert('danger', error.message || 'No fue posible cargar la información inicial.');
                }
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
                    const response = await fetch(apiBase, {
                        method: 'POST',
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

                        showAlert('danger', body.message ?? 'No fue posible guardar el proyecto.');
                        return;
                    }

                    showAlert('success', body.message ?? 'Proyecto creado correctamente. Redirigiendo…');
                    setTimeout(() => {
                        window.location.href = indexUrl;
                    }, 1200);
                } catch (error) {
                    showAlert('danger', 'Ocurrió un error inesperado. Intenta nuevamente.');
                } finally {
                    submitButton.disabled = false;
                    submitButton.classList.remove('btn-loading');
                }
            });

            fetchMeta();
        });
    </script>
@endpush

@push('css')
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .08);
        }

        .avatar {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
    </style>
@endpush
