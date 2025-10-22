{{--
    View path: projects/show.blade.php.
    Purpose: Renders the Tablar-styled detail screen for a research project using API data.
    Expected variables:
    - $projectId (int): identifier of the project to display, provided via the route closure.
--}}
@extends('tablar::page')

@section('title', 'Detalle del proyecto')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Proyectos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 20l4 -9l-4 -3l-4 3z" />
                            <path d="M8 4l-2 4l-4 .5l3 3l-.5 4l3.5 -2l3.5 2l-.5 -4l3 -3l-4 -.5l-2 -4z" />
                        </svg>
                        Proyecto #{{ $projectId }}
                    </h2>
                    <p class="text-muted mb-0">Consulta los datos más recientes del proyecto y sus participantes vinculados.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                    <a href="{{ route('projects.edit', ['project' => $projectId]) }}" class="btn btn-primary">Editar</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div id="project-show-alert" class="alert alert-info" role="alert">Cargando información del proyecto…</div>

            <div class="row g-3" id="project-content" hidden>
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title mb-0" id="project-title">—</h3>
                                <small class="text-secondary" id="project-meta">—</small>
                            </div>
                            <span class="badge bg-indigo" id="project-status">—</span>
                        </div>
                        <div class="card-body">
                            <dl class="row g-3 mb-0">
                                <dt class="col-sm-4">Área temática</dt>
                                <dd class="col-sm-8" id="project-thematic-area">—</dd>

                                <dt class="col-sm-4">Criterios de evaluación</dt>
                                <dd class="col-sm-8" id="project-evaluation">—</dd>

                                <dt class="col-sm-4">Última actualización</dt>
                                <dd class="col-sm-8" id="project-updated-at">—</dd>

                                <dt class="col-sm-4">Creado en</dt>
                                <dd class="col-sm-8" id="project-created-at">—</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Profesores asignados</h3>
                            <span class="badge bg-primary" id="project-professors-count">0</span>
                        </div>
                        <div class="card-body" id="project-professors">—</div>
                    </div>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Estudiantes participantes</h3>
                            <span class="badge bg-primary" id="project-students-count">0</span>
                        </div>
                        <div class="card-body" id="project-students">—</div>
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
            const projectId = {{ (int) $projectId }};

            const alertBox = document.getElementById('project-show-alert');
            const content = document.getElementById('project-content');
            const titleElement = document.getElementById('project-title');
            const metaElement = document.getElementById('project-meta');
            const statusElement = document.getElementById('project-status');
            const thematicAreaElement = document.getElementById('project-thematic-area');
            const evaluationElement = document.getElementById('project-evaluation');
            const updatedElement = document.getElementById('project-updated-at');
            const createdElement = document.getElementById('project-created-at');
            const professorsContainer = document.getElementById('project-professors');
            const professorsBadge = document.getElementById('project-professors-count');
            const studentsContainer = document.getElementById('project-students');
            const studentsBadge = document.getElementById('project-students-count');

            // Converts the API timestamp into a localized string.
            function formatDate(value) {
                if (!value) {
                    return '—';
                }
                const date = new Date(value);
                if (Number.isNaN(date.getTime())) {
                    return value;
                }
                return date.toLocaleString('es-CO');
            }

            // Builds the HTML structure for either professors or students.
            function renderPeople(items, emptyLabel) {
                if (!Array.isArray(items) || !items.length) {
                    return `<p class="text-secondary mb-0">${emptyLabel}</p>`;
                }

                return items.map(person => {
                    const fullName = [person.name, person.last_name].filter(Boolean).join(' ').trim() || 'Sin nombre';
                    const documentId = person.card_id ? `Documento: ${person.card_id}` : 'Documento no disponible';
                    return `
                        <div class="d-flex align-items-start gap-2 mb-2">
                            <span class="avatar bg-azure-lt text-primary">
                                <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">
                                    <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\" />
                                    <circle cx=\"12\" cy=\"7\" r=\"4\" />
                                    <path d=\"M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2\" />
                                </svg>
                            </span>
                            <div>
                                <div class="fw-semibold">${fullName}</div>
                                <div class="text-secondary small">${documentId}</div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            async function loadProject() {
                try {
                    const response = await fetch(`${apiBase}/${projectId}`, { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) {
                        throw new Error('No fue posible encontrar la información solicitada.');
                    }

                    const project = await response.json();
                    titleElement.textContent = project.title ?? 'Proyecto sin título';
                    metaElement.textContent = `ID interno: ${project.id ?? projectId}`;
                    statusElement.textContent = project.project_status?.name ?? 'Sin estado';
                    thematicAreaElement.textContent = project.thematic_area?.name ?? 'Sin área temática registrada';
                    evaluationElement.textContent = project.evaluation_criteria ? project.evaluation_criteria : 'Sin criterios definidos.';
                    updatedElement.textContent = formatDate(project.updated_at);
                    createdElement.textContent = formatDate(project.created_at);

                    professorsContainer.innerHTML = renderPeople(project.professors || [], 'Sin profesores asignados.');
                    professorsBadge.textContent = Array.isArray(project.professors) ? project.professors.length : 0;

                    studentsContainer.innerHTML = renderPeople(project.students || [], 'Sin estudiantes asociados.');
                    studentsBadge.textContent = Array.isArray(project.students) ? project.students.length : 0;

                    alertBox.classList.add('d-none');
                    content.hidden = false;
                } catch (error) {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = error.message || 'Ocurrió un error al cargar los datos del proyecto.';
                }
            }

            loadProject();
        });
    </script>
@endpush

@push('css')
    <style>
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
