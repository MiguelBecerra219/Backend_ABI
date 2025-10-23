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
                        <a href="{{ route('projects.create') }}" class="btn btn-primary" id="btn-new-project">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Nuevo proyecto
                        </a>
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
                    <table class="table card-table table-vcenter align-middle" id="project-table">
                        <thead>
                            <tr>
                                <th class="w-1">ID</th>
                                <th class="text-truncate" style="max-width: 260px;">Título</th>
                                <th class="text-truncate" style="max-width: 200px;">Área temática</th>
                                <th class="text-truncate" style="max-width: 160px;">Estado</th>
                                <th class="text-truncate" style="max-width: 220px;">Profesores</th>
                                <th class="text-truncate" style="max-width: 220px;">Estudiantes</th>
                                <th class="text-truncate" style="max-width: 140px;">Actualizado</th>
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

    {{-- Modal replaces the native confirmation dialog when deleting a project. --}}
    <div class="modal fade" id="project-delete-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar proyecto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">¿Deseas eliminar <span class="fw-semibold" id="project-delete-name">este proyecto</span>? Esta acción es reversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="project-delete-confirm">Eliminar</button>
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
            const restoreBase = '{{ url('/api/projects') }}';
            // Base templates used to link rows to the standalone detail and edit screens.
            const editUrlTemplate = '{{ url('projects') }}/:id/edit';
            const showUrlTemplate = '{{ url('projects') }}/:id';

            const deleteModalElement = document.getElementById('project-delete-modal');
            const deleteModal = deleteModalElement && window.bootstrap ? new window.bootstrap.Modal(deleteModalElement) : null;
            const deleteNameLabel = document.getElementById('project-delete-name');
            const deleteConfirmButton = document.getElementById('project-delete-confirm');

            let pendingDeleteId = null;
            let pendingDeleteTitle = 'este proyecto';

            const icons = {
                view: '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="2"/><path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7"/></svg>',
                edit: '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>',
                delete: '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="7" x2="20" y2="7"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3h6v3"/></svg>',
                restore: '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M9 7h-3v3"/><path d="M5 7l5 -5l5 5"/><path d="M5 10v6a2 2 0 0 0 2 2h10"/></svg>',
            };

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

            const state = {
                page: 1,
                perPage: parseInt(perPageSelect.value, 10) || 10,
                search: '',
                thematicAreaId: '',
                projectStatusId: '',
                professorId: '',
                studentId: '',
                recordFilter: 'active',
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
                const badges = items.map(person => {
                    const fullName = [person.name, person.last_name].filter(Boolean).join(' ').trim() || person.card_id || 'Sin nombre';
                    const safeName = escapeHtml(fullName);
                    return `<span class="badge bg-blue-lt text-truncate">${safeName}</span>`;
                }).join('');
                return `<div class="d-flex flex-wrap gap-1 project-badges">${badges}</div>`;
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

            function buildActionButtons(itemId, deleted, title) {
                const showUrl = showUrlTemplate.replace(':id', itemId);
                if (deleted) {
                    return `
                        <a href="${showUrl}" class="btn btn-sm btn-outline-secondary" data-action="show" data-id="${itemId}" title="Ver">${icons.view}</a>
                        <button class="btn btn-sm btn-outline-success" data-action="restore" data-id="${itemId}" title="Restaurar">${icons.restore}</button>
                    `;
                }

                const editUrl = editUrlTemplate.replace(':id', itemId);
                const safeTitle = escapeHtml(title ?? 'este proyecto');
                return `
                    <a href="${showUrl}" class="btn btn-sm btn-outline-primary" data-action="show" data-id="${itemId}" title="Ver">${icons.view}</a>
                    <a href="${editUrl}" class="btn btn-sm btn-outline-success" data-action="edit" data-id="${itemId}" title="Editar">${icons.edit}</a>
                    <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${itemId}" data-title="${safeTitle}" title="Eliminar">${icons.delete}</button>
                `;
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
                    const rawTitle = item.title ?? 'Proyecto sin título';
                    const title = escapeHtml(rawTitle);
                    const rowClass = deleted ? ' class="table-danger"' : '';
                    const badge = deleted ? '<span class="badge bg-red-lt ms-2 align-middle">Eliminado</span>' : '';
                    const areaHtml = `<span class="d-inline-block text-truncate" style="max-width: 200px;" title="${escapeHtml(thematicArea)}">${escapeHtml(thematicArea)}</span>`;
                    const statusHtml = `<span class="d-inline-block text-truncate" style="max-width: 160px;" title="${escapeHtml(displayStatus)}">${escapeHtml(displayStatus)}</span>`;
                    const updatedHtml = `<span class="d-inline-block text-truncate" style="max-width: 140px;" title="${escapeHtml(updated)}">${escapeHtml(updated)}</span>`;

                    return `
                        <tr${rowClass} data-id="${item.id}">
                            <td class="text-secondary">#${item.id}</td>
                            <td class="project-title-cell">
                                <span class="fw-semibold d-inline-block text-truncate" style="max-width: 240px;" title="${title}">${title}</span>
                                ${badge}
                            </td>
                            <td class="project-area-cell">${areaHtml}</td>
                            <td class="project-status-cell">${statusHtml}</td>
                            <td class="project-professors-cell">${professors}</td>
                            <td class="project-students-cell">${students}</td>
                            <td class="project-updated-cell text-secondary">${updatedHtml}</td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    ${buildActionButtons(item.id, deleted, rawTitle)}
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

            async function deleteProject(id) {
                const response = await fetch(`${apiBase}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    const body = await response.json().catch(() => ({}));
                    throw new Error(body?.message ?? 'No fue posible eliminar el proyecto.');
                }
            }

            async function restoreProject(id) {
                const response = await fetch(`${restoreBase}/${id}/restore`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                    },
                });

                if (!response.ok) {
                    const body = await response.json().catch(() => ({}));
                    throw new Error(body?.message ?? 'No fue posible restaurar el proyecto.');
                }
            }

            pagination.addEventListener('click', event => {
                const link = event.target.closest('a.page-link');
                if (!link) {
                    return;
                }
                event.preventDefault();
                const url = link.getAttribute('data-url');
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
                    if (button.dataset.action === 'delete') {
                        pendingDeleteId = id;
                        pendingDeleteTitle = button.getAttribute('data-title') || 'este proyecto';
                        if (deleteNameLabel) {
                            deleteNameLabel.textContent = pendingDeleteTitle;
                        }
                        if (deleteModal) {
                            deleteConfirmButton?.removeAttribute('disabled');
                            deleteModal.show();
                            return;
                        }

                        await deleteProject(id);
                        setAlert('Proyecto eliminado correctamente.', 'success');
                        fetchProjects(buildQuery(state.page));
                        return;
                    } else if (button.dataset.action === 'restore') {
                        await restoreProject(id);
                        setAlert('Proyecto restaurado correctamente.', 'success');
                        fetchProjects(buildQuery(state.page));
                    }
                } catch (error) {
                    setAlert(error.message || 'Ocurrió un error al procesar la acción.');
                }
            });

            deleteConfirmButton?.addEventListener('click', async () => {
                if (!pendingDeleteId) {
                    deleteModal?.hide();
                    return;
                }

                deleteConfirmButton.disabled = true;
                try {
                    await deleteProject(pendingDeleteId);
                    setAlert('Proyecto eliminado correctamente.', 'success');
                    deleteModal?.hide();
                    fetchProjects(buildQuery(state.page));
                } catch (error) {
                    setAlert(error.message || 'Ocurrió un error al procesar la acción.');
                } finally {
                    deleteConfirmButton.disabled = false;
                    pendingDeleteId = null;
                }
            });

            deleteModalElement?.addEventListener('hidden.bs.modal', () => {
                pendingDeleteId = null;
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

        #project-table td.project-title-cell,
        #project-table td.project-area-cell,
        #project-table td.project-status-cell,
        #project-table td.project-professors-cell,
        #project-table td.project-students-cell,
        #project-table td.project-updated-cell {
            max-width: 260px;
            vertical-align: middle;
        }

        #project-table td.project-title-cell .fw-semibold,
        #project-table td.project-area-cell span,
        #project-table td.project-status-cell span,
        #project-table td.project-updated-cell span {
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        #project-table .project-badges {
            max-width: 220px;
        }

        #project-table .project-badges .badge {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
@endpush
