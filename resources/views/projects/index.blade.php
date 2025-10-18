{{--
    View path: projects/index.blade.php.
    Purpose: Renders the Tablar interface to manage research projects, including
    CRUD actions, assignments, and filters aligned with the academic catalogue.
    This template relies on asynchronous API calls, so no server variables are required.
--}}
@extends('tablar::page')

@section('title', 'Gestión de Proyectos')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Proyectos</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 21v-13l8 -4l8 4v13" />
                            <path d="M12 13l8 -4" />
                            <path d="M12 13l-8 -4" />
                            <path d="M12 13v8" />
                            <path d="M8 21h8" />
                        </svg>
                        Gestión de Proyectos
                    </h2>
                    <p class="text-muted mb-0">Administra los proyectos de grado, sus responsables y participantes estudiantiles.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <button class="btn btn-primary" id="btn-new-project" type="button" data-bs-toggle="modal" data-bs-target="#project-modal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Nuevo proyecto
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar.display_alert'))
                @include('tablar::common.alert')
            @endif

            <div id="project-alert" class="alert d-none" role="alert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filtrar proyectos</h3>
                </div>
                <div class="card-body">
                    <form id="project-filters" class="row g-3 align-items-end">
                        <div class="col-12 col-xl-3">
                            <label for="project-search" class="form-label">Búsqueda</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="10" cy="10" r="7" />
                                        <line x1="21" y1="21" x2="15" y2="15" />
                                    </svg>
                                </span>
                                <input type="search" class="form-control" id="project-search" placeholder="Título o ID">
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <label for="project-thematic-area" class="form-label">Área temática</label>
                            <select id="project-thematic-area" class="form-select">
                                <option value="">Todas las áreas</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <label for="project-status" class="form-label">Estado</label>
                            <select id="project-status" class="form-select">
                                <option value="">Todos los estados</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <label for="project-professor" class="form-label">Profesor asignado</label>
                            <select id="project-professor" class="form-select">
                                <option value="">Todos los profesores</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <label for="project-student" class="form-label">Estudiante participante</label>
                            <select id="project-student" class="form-select">
                                <option value="">Todos los estudiantes</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <label for="project-record-filter" class="form-label">Registros</label>
                            <select id="project-record-filter" class="form-select">
                                <option value="active">Solo activos</option>
                                <option value="with_deleted">Incluir eliminados</option>
                                <option value="only_deleted">Solo eliminados</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <label for="project-per-page" class="form-label">Por página</label>
                            <select id="project-per-page" class="form-select">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-xl-2 d-grid">
                            <button class="btn btn-outline-secondary" type="reset">Limpiar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Listado de proyectos</h3>
                    <div class="card-actions">
                        <span class="badge bg-azure" id="project-total">0</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap">
                        <thead>
                            <tr>
                                <th class="w-1">ID</th>
                                <th>Título</th>
                                <th>Área temática</th>
                                <th>Estado</th>
                                <th>Profesores</th>
                                <th>Estudiantes</th>
                                <th class="w-1">Actualizado</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="project-rows">
                            <tr>
                                <td colspan="8" class="text-center text-secondary py-4">Cargando proyectos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div class="text-secondary" id="project-summary">Mostrando 0 a 0 de 0 registros</div>
                    <nav>
                        <ul class="pagination mb-0" id="project-pagination"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="project-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="project-modal-title">Nuevo proyecto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="project-form" autocomplete="off">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label required" for="project-title">Título</label>
                                <input type="text" class="form-control" id="project-title" maxlength="255" required>
                                <small class="form-hint">El título se normalizará automáticamente según las reglas institucionales.</small>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label required" for="project-thematic-area-form">Área temática</label>
                                <select id="project-thematic-area-form" class="form-select" required>
                                    <option value="">Selecciona un área temática</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label required" for="project-status-form">Estado</label>
                                <select id="project-status-form" class="form-select" required>
                                    <option value="">Selecciona un estado</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="project-evaluation">Criterios de evaluación</label>
                                <textarea id="project-evaluation" class="form-control" rows="3" placeholder="Requisitos o rúbricas que deben cumplir los postulantes."></textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="project-professors">Profesores asignados</label>
                                <select id="project-professors" class="form-select" multiple size="6"></select>
                                <small class="form-hint">Mantén presionada la tecla Ctrl (o Cmd en Mac) para seleccionar múltiples docentes.</small>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="project-students">Estudiantes participantes</label>
                                <select id="project-students" class="form-select" multiple size="6"></select>
                                <small class="form-hint">Solo se listan estudiantes activos asociados al programa correspondiente.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="project-submit">Guardar proyecto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const apiBase = '{{ url('/api/projects') }}';
            const metaUrl = '{{ url('/api/projects/meta') }}';
            const restoreBase = '{{ url('/api/projects') }}';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

            const filtersForm = document.getElementById('project-filters');
            const searchInput = document.getElementById('project-search');
            const thematicAreaFilter = document.getElementById('project-thematic-area');
            const statusFilter = document.getElementById('project-status');
            const professorFilter = document.getElementById('project-professor');
            const studentFilter = document.getElementById('project-student');
            const recordFilter = document.getElementById('project-record-filter');
            const perPageSelect = document.getElementById('project-per-page');
            const rows = document.getElementById('project-rows');
            const pagination = document.getElementById('project-pagination');
            const totalBadge = document.getElementById('project-total');
            const summary = document.getElementById('project-summary');
            const alertBox = document.getElementById('project-alert');

            const modalElement = document.getElementById('project-modal');
            const modalTitle = document.getElementById('project-modal-title');
            const form = document.getElementById('project-form');
            const submitBtn = document.getElementById('project-submit');
            const titleInput = document.getElementById('project-title');
            const thematicAreaForm = document.getElementById('project-thematic-area-form');
            const statusForm = document.getElementById('project-status-form');
            const evaluationField = document.getElementById('project-evaluation');
            const professorsForm = document.getElementById('project-professors');
            const studentsForm = document.getElementById('project-students');
            const modalInstance = window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(modalElement) : null;

            const state = {
                page: 1,
                perPage: parseInt(perPageSelect.value, 10) || 10,
                search: '',
                thematicAreaId: '',
                projectStatusId: '',
                professorId: '',
                studentId: '',
                recordFilter: 'active',
                currentId: null,
            };

            const metaState = {
                thematicAreas: [],
                statuses: [],
                professors: [],
                students: [],
            };

            function debounce(fn, delay = 400) {
                let timer;
                return (...args) => {
                    clearTimeout(timer);
                    timer = setTimeout(() => fn.apply(null, args), delay);
                };
            }

            function escapeHtml(value) {
                if (typeof value !== 'string') {
                    return value ?? '';
                }
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                };
                return value.replace(/[&<>"']/g, char => map[char]);
            }

            function setAlert(message, type = 'danger') {
                if (!alertBox) {
                    return;
                }
                alertBox.className = `alert alert-${type}`;
                alertBox.textContent = message;
                alertBox.classList.remove('d-none');
            }

            function clearAlert() {
                if (!alertBox) {
                    return;
                }
                alertBox.classList.add('d-none');
                alertBox.textContent = '';
            }

            function buildQuery(page = 1) {
                const params = new URLSearchParams();
                params.set('per_page', state.perPage);
                params.set('page', page);

                if (state.search) {
                    params.set('search', state.search);
                }
                if (state.thematicAreaId) {
                    params.set('thematic_area_id', state.thematicAreaId);
                }
                if (state.projectStatusId) {
                    params.set('project_status_id', state.projectStatusId);
                }
                if (state.professorId) {
                    params.set('professor_id', state.professorId);
                }
                if (state.studentId) {
                    params.set('student_id', state.studentId);
                }
                if (state.recordFilter === 'with_deleted') {
                    params.set('include_deleted', '1');
                } else if (state.recordFilter === 'only_deleted') {
                    params.set('only_deleted', '1');
                }

                return `${apiBase}?${params.toString()}`;
            }

            function renderParticipants(items, emptyLabel) {
                if (!Array.isArray(items) || !items.length) {
                    return `<span class="text-secondary">${emptyLabel}</span>`;
                }
                return items.map(person => {
                    const fullName = [person.name, person.last_name].filter(Boolean).join(' ').trim() || person.card_id || 'Sin nombre';
                    return `<span class="badge bg-blue-lt me-1 mb-1">${escapeHtml(fullName)}</span>`;
                }).join('');
            }

            function resolveCatalogName(relation) {
                if (!relation) {
                    return '—';
                }
                return relation.name
                    ?? relation.nombre
                    ?? relation.Nombre
                    ?? relation.title
                    ?? relation.titulo
                    ?? '—';
            }

            function renderTable(data) {
                const items = data?.data ?? [];

                if (!items.length) {
                    rows.innerHTML = '<tr><td colspan="8" class="text-center text-secondary py-4">No se encontraron proyectos para los filtros aplicados.</td></tr>';
                    totalBadge.textContent = '0';
                    summary.textContent = 'Mostrando 0 a 0 de 0 registros';
                    pagination.innerHTML = '';
                    return;
                }

                rows.innerHTML = items.map(item => {
                    const thematicArea = resolveCatalogName(item.thematic_area);
                    const statusName = resolveCatalogName(item.project_status);
                    const displayStatus = statusName === '—' ? 'Sin estado' : statusName;
                    const updated = item.updated_at ? new Date(item.updated_at).toLocaleString('es-CO') : '—';
                    const professors = renderParticipants(item.professors || [], 'Sin docentes');
                    const students = renderParticipants(item.students || [], 'Sin estudiantes');
                    const deleted = Boolean(item.deleted_at);
                    const title = escapeHtml(item.title ?? 'Proyecto sin título');
                    const rowClass = deleted ? ' class="table-danger"' : '';
                    const badge = deleted ? '<span class="badge bg-red-lt ms-2">Eliminado</span>' : '';

                    let actions = `
                        <button class="btn btn-outline-primary btn-sm" data-action="edit" data-id="${item.id}">Editar</button>
                        <button class="btn btn-outline-danger btn-sm" data-action="delete" data-id="${item.id}">Eliminar</button>
                    `;

                    if (deleted) {
                        actions = `
                            <button class="btn btn-outline-success btn-sm" data-action="restore" data-id="${item.id}">Restaurar</button>
                        `;
                    }

                    return `
                        <tr${rowClass} data-id="${item.id}">
                            <td class="text-secondary">#${item.id}</td>
                            <td class="fw-semibold">${title}${badge}</td>
                            <td>${escapeHtml(thematicArea)}</td>
                            <td>${escapeHtml(displayStatus)}</td>
                            <td>${professors}</td>
                            <td>${students}</td>
                            <td>${updated}</td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    ${actions}
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');

                totalBadge.textContent = data?.total ?? 0;
                const from = data?.from ?? 0;
                const to = data?.to ?? 0;
                const total = data?.total ?? 0;
                summary.textContent = `Mostrando ${from} a ${to} de ${total} registros`;

                renderPagination(data?.links || []);
            }

            function renderPagination(links) {
                if (!Array.isArray(links) || !links.length) {
                    pagination.innerHTML = '';
                    return;
                }

                pagination.innerHTML = links.map(link => {
                    const label = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
                    const disabled = link.url === null ? ' disabled' : '';
                    const active = link.active ? ' active' : '';
                    return `
                        <li class="page-item${disabled}${active}">
                            <a class="page-link" href="#" data-url="${link.url ?? ''}">${label}</a>
                        </li>
                    `;
                }).join('');
            }

            async function fetchProjects(url = buildQuery(state.page)) {
                try {
                    clearAlert();
                    rows.innerHTML = '<tr><td colspan="8" class="text-center text-secondary py-4">Cargando proyectos...</td></tr>';
                    const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) {
                        throw new Error('No fue posible cargar los proyectos.');
                    }
                    const data = await response.json();
                    state.page = data?.current_page ?? 1;
                    renderTable(data);
                } catch (error) {
                    setAlert(error.message || 'Ocurrió un error inesperado al cargar los proyectos.');
                    rows.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">Error al cargar los datos.</td></tr>';
                    pagination.innerHTML = '';
                    totalBadge.textContent = '0';
                    summary.textContent = 'Mostrando 0 a 0 de 0 registros';
                }
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

                    populateFilterSelect(thematicAreaFilter, metaState.thematicAreas, 'Todas las áreas');
                    populateFilterSelect(statusFilter, metaState.statuses, 'Todos los estados');
                    populateFilterSelect(professorFilter, metaState.professors, 'Todos los profesores', professor => `${professor.name} · ${professor.card_id}`);
                    populateFilterSelect(studentFilter, metaState.students, 'Todos los estudiantes', student => `${student.name} · ${student.card_id}`);

                    populateFormSelect(thematicAreaForm, metaState.thematicAreas, 'Selecciona un área temática');
                    populateFormSelect(statusForm, metaState.statuses, 'Selecciona un estado');
                    populateFormSelect(professorsForm, metaState.professors, null, professor => `${professor.name} · ${professor.card_id}`);
                    populateFormSelect(studentsForm, metaState.students, null, student => `${student.name} · ${student.card_id}`);
                } catch (error) {
                    setAlert(error.message || 'No fue posible cargar los catálogos para proyectos.');
                }
            }

            function populateFilterSelect(select, items, placeholder, formatter) {
                if (!select) {
                    return;
                }
                const previous = select.value;
                select.innerHTML = '';
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = placeholder;
                select.appendChild(defaultOption);

                items.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = typeof formatter === 'function' ? formatter(item) : item.name;
                    select.appendChild(option);
                });

                select.value = previous;
            }

            function populateFormSelect(select, items, placeholder, formatter) {
                if (!select) {
                    return;
                }
                const selectedValues = Array.from(select.selectedOptions || []).map(option => option.value);
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
                    option.textContent = typeof formatter === 'function' ? formatter(item) : item.name;
                    if (selectedValues.includes(String(item.id))) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            }

            async function getProject(id) {
                const response = await fetch(`${apiBase}/${id}`, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('No fue posible obtener la información del proyecto.');
                }
                return response.json();
            }

            async function submitProject(method, url, payload) {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                if (response.status === 422) {
                    const data = await response.json();
                    const firstError = Object.values(data.errors || {})[0]?.[0] ?? 'Revisa la información ingresada.';
                    throw new Error(firstError);
                }

                if (!response.ok) {
                    const data = await response.json().catch(() => ({}));
                    throw new Error(data.message || 'No fue posible guardar la información.');
                }

                return response.json().catch(() => ({}));
            }

            async function deleteProject(id) {
                const response = await fetch(`${apiBase}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                if (response.status === 409) {
                    const data = await response.json();
                    throw new Error(data.message || 'El proyecto no puede eliminarse.');
                }

                if (!response.ok && response.status !== 204) {
                    const data = await response.json().catch(() => ({}));
                    throw new Error(data.message || 'No fue posible eliminar el proyecto.');
                }
            }

            async function restoreProject(id) {
                const response = await fetch(`${restoreBase}/${id}/restore`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                if (!response.ok) {
                    const data = await response.json().catch(() => ({}));
                    throw new Error(data.message || 'No fue posible restaurar el proyecto.');
                }
            }

            function collectSelectedValues(select) {
                return Array.from(select.selectedOptions || [])
                    .map(option => parseInt(option.value, 10))
                    .filter(id => !Number.isNaN(id));
            }

            function fillForm(project) {
                titleInput.value = project.title ?? '';
                thematicAreaForm.value = project.thematic_area_id ?? '';
                statusForm.value = project.project_status_id ?? '';
                evaluationField.value = project.evaluation_criteria ?? '';

                const professorIds = (project.professors || []).map(professor => professor.id);
                const studentIds = (project.students || []).map(student => student.id);

                Array.from(professorsForm.options).forEach(option => {
                    option.selected = professorIds.includes(parseInt(option.value, 10));
                });
                Array.from(studentsForm.options).forEach(option => {
                    option.selected = studentIds.includes(parseInt(option.value, 10));
                });
            }

            filtersForm.addEventListener('submit', event => event.preventDefault());

            filtersForm.addEventListener('reset', () => {
                setTimeout(() => {
                    state.search = '';
                    state.thematicAreaId = '';
                    state.projectStatusId = '';
                    state.professorId = '';
                    state.studentId = '';
                    state.recordFilter = 'active';
                    state.page = 1;
                    perPageSelect.value = '10';
                    recordFilter.value = 'active';
                    state.perPage = 10;
                    fetchProjects(buildQuery());
                }, 0);
            });

            pagination.addEventListener('click', event => {
                const target = event.target.closest('a[data-url]');
                if (!target) {
                    return;
                }
                event.preventDefault();
                const url = target.getAttribute('data-url');
                if (!url) {
                    return;
                }
                const pageMatch = url.match(/[?&]page=(\d+)/);
                const nextPage = pageMatch ? parseInt(pageMatch[1], 10) : 1;
                state.page = Number.isNaN(nextPage) ? 1 : nextPage;
                fetchProjects(buildQuery(state.page));
            });

            rows.addEventListener('click', async event => {
                const button = event.target.closest('button[data-action]');
                if (!button) {
                    return;
                }

                const id = button.getAttribute('data-id');
                if (!id) {
                    return;
                }

                try {
                    if (button.dataset.action === 'edit') {
                        const project = await getProject(id);
                        state.currentId = project.id;
                        modalTitle.textContent = 'Editar proyecto';
                        submitBtn.textContent = 'Actualizar proyecto';
                        fillForm(project);
                        modalInstance?.show();
                    } else if (button.dataset.action === 'delete') {
                        if (confirm('¿Deseas eliminar este proyecto? Esta acción es reversible.')) {
                            await deleteProject(id);
                            setAlert('Proyecto eliminado correctamente.', 'success');
                            fetchProjects(buildQuery(state.page));
                        }
                    } else if (button.dataset.action === 'restore') {
                        await restoreProject(id);
                        setAlert('Proyecto restaurado correctamente.', 'success');
                        fetchProjects(buildQuery(state.page));
                    }
                } catch (error) {
                    setAlert(error.message || 'Ocurrió un error al procesar la acción.');
                }
            });

            searchInput.addEventListener('input', debounce(event => {
                state.search = event.target.value.trim();
                state.page = 1;
                fetchProjects(buildQuery());
            }));

            thematicAreaFilter.addEventListener('change', event => {
                state.thematicAreaId = event.target.value;
                state.page = 1;
                fetchProjects(buildQuery());
            });

            statusFilter.addEventListener('change', event => {
                state.projectStatusId = event.target.value;
                state.page = 1;
                fetchProjects(buildQuery());
            });

            professorFilter.addEventListener('change', event => {
                state.professorId = event.target.value;
                state.page = 1;
                fetchProjects(buildQuery());
            });

            studentFilter.addEventListener('change', event => {
                state.studentId = event.target.value;
                state.page = 1;
                fetchProjects(buildQuery());
            });

            recordFilter.addEventListener('change', event => {
                state.recordFilter = event.target.value;
                state.page = 1;
                fetchProjects(buildQuery());
            });

            perPageSelect.addEventListener('change', event => {
                state.perPage = parseInt(event.target.value, 10) || 10;
                state.page = 1;
                fetchProjects(buildQuery());
            });

            document.getElementById('btn-new-project').addEventListener('click', () => {
                state.currentId = null;
                modalTitle.textContent = 'Nuevo proyecto';
                submitBtn.textContent = 'Guardar proyecto';
                form.reset();
                Array.from(professorsForm.options).forEach(option => option.selected = false);
                Array.from(studentsForm.options).forEach(option => option.selected = false);
            });

            form.addEventListener('submit', async event => {
                event.preventDefault();

                try {
                    submitBtn.disabled = true;
                    submitBtn.textContent = state.currentId ? 'Actualizando...' : 'Guardando...';

                    const payload = {
                        title: titleInput.value.trim(),
                        thematic_area_id: parseInt(thematicAreaForm.value, 10) || null,
                        project_status_id: parseInt(statusForm.value, 10) || null,
                        evaluation_criteria: evaluationField.value.trim() || null,
                        professor_ids: collectSelectedValues(professorsForm),
                        student_ids: collectSelectedValues(studentsForm),
                    };

                    if (!payload.thematic_area_id || !payload.project_status_id) {
                        throw new Error('Debes seleccionar un área temática y un estado.');
                    }

                    let method = 'POST';
                    let url = apiBase;
                    if (state.currentId) {
                        method = 'PUT';
                        url = `${apiBase}/${state.currentId}`;
                    }

                    await submitProject(method, url, payload);
                    modalInstance?.hide();
                    setAlert(state.currentId ? 'Proyecto actualizado correctamente.' : 'Proyecto creado con éxito.', 'success');
                    fetchProjects(buildQuery(state.page));
                } catch (error) {
                    setAlert(error.message || 'No fue posible guardar la información.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = state.currentId ? 'Actualizar proyecto' : 'Guardar proyecto';
                }
            });

            (async () => {
                await fetchMeta();
                await fetchProjects(buildQuery());
            })();
        });
    </script>
@endpush

@push('css')
    <style>
        .form-label.required::after {
            content: ' *';
            color: var(--tblr-danger);
        }

        #project-professors,
        #project-students {
            min-height: 12rem;
        }
    </style>
@endpush
