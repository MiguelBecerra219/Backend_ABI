<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Professor\ProfessorProfessor;
use App\Models\Professor\ProfessorProject;
use App\Models\Professor\ProfessorProjectStatus;
use App\Models\Professor\ProfessorStudent;
use App\Models\Professor\ProfessorThematicArea;
use App\Models\ResearchStaff\ResearchStaffProfessor;
use App\Models\ResearchStaff\ResearchStaffProject;
use App\Models\ResearchStaff\ResearchStaffProjectStatus;
use App\Models\ResearchStaff\ResearchStaffStudent;
use App\Models\ResearchStaff\ResearchStaffThematicArea;
use App\Models\Student\StudentProfessor;
use App\Models\Student\StudentProject;
use App\Models\Student\StudentProjectStatus;
use App\Models\Student\StudentStudent;
use App\Models\Student\StudentThematicArea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Controller responsible for managing research projects.
 *
 * Exposes full CRUD capabilities plus filtering by status, thematic area,
 * and the professors or students assigned to each project.
 */
class ProjectController extends Controller
{
    /**
     * Cached model map for the authenticated role.
     */
    protected ?array $resolvedModels = null;

    /**
     * Return a paginated list of projects applying optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
                'search' => 'nullable|string|max:255',
                'thematic_area_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('thematic_areas', 'id')->whereNull('deleted_at'),
                ],
                'project_status_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('project_statuses', 'id')->whereNull('deleted_at'),
                ],
                'professor_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('professors', 'id')->whereNull('deleted_at'),
                ],
                'student_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('students', 'id')->whereNull('deleted_at'),
                ],
                'include_deleted' => 'nullable|boolean',
                'only_deleted' => 'nullable|boolean',
            ]);

            $perPage = (int) ($validated['per_page'] ?? 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            $query = $this->projectQuery()
                ->with(['projectStatus', 'thematicArea', 'professors', 'students'])
                ->orderByDesc('created_at');

            if (! empty($validated['only_deleted'])) {
                $query->onlyTrashed();
            } elseif (! empty($validated['include_deleted'])) {
                $query->withTrashed();
            }

            if (! empty($validated['search'])) {
                $search = trim($validated['search']);
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");

                    if (is_numeric($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            }

            if (! empty($validated['thematic_area_id'])) {
                $query->where('thematic_area_id', $validated['thematic_area_id']);
            }

            if (! empty($validated['project_status_id'])) {
                $query->where('project_status_id', $validated['project_status_id']);
            }

            if (! empty($validated['professor_id'])) {
                $query->whereHas('professors', function ($q) use ($validated) {
                    $q->where('professors.id', $validated['professor_id']);
                });
            }

            if (! empty($validated['student_id'])) {
                $query->whereHas('students', function ($q) use ($validated) {
                    $q->where('students.id', $validated['student_id']);
                });
            }

            $projects = $query
                ->paginate($perPage)
                ->withQueryString();

            return response()->json($projects);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Los parámetros de búsqueda no son válidos.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al listar proyectos: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al obtener los proyectos.',
            ], 500);
        }
    }

    /**
     * Provide the catalog data required by the project management UI.
     */
    public function meta(): JsonResponse
    {
        try {
            $models = $this->models();

            $statuses = $models['status']::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            $thematicAreas = $models['thematic_area']::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            $professors = $models['professor']::query()
                ->select(['id', 'name', 'last_name', 'card_id', 'committee_leader'])
                ->orderBy('last_name')
                ->orderBy('name')
                ->get()
                ->map(static function ($professor) {
                    return [
                        'id' => $professor->id,
                        'name' => trim($professor->name . ' ' . $professor->last_name),
                        'card_id' => $professor->card_id,
                        'committee_leader' => (bool) $professor->committee_leader,
                    ];
                });

            $students = $models['student']::query()
                ->select(['id', 'name', 'last_name', 'card_id', 'semester'])
                ->orderBy('last_name')
                ->orderBy('name')
                ->get()
                ->map(static function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => trim($student->name . ' ' . $student->last_name),
                        'card_id' => $student->card_id,
                        'semester' => (int) $student->semester,
                    ];
                });

            return response()->json([
                'statuses' => $statuses,
                'thematic_areas' => $thematicAreas,
                'professors' => $professors,
                'students' => $students,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener catálogos de proyectos: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al obtener los catálogos para proyectos.',
            ], 500);
        }
    }

    /**
     * Create a new project and persist its participant assignments.
     */
    public function store(ProjectRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $relationData = Arr::only($data, ['professor_ids', 'student_ids']);
            $projectData = Arr::except($data, ['professor_ids', 'student_ids']);

            $projectClass = $this->writableProjectModel();

            return DB::transaction(function () use ($projectClass, $projectData, $relationData) {
                $project = $projectClass::create($projectData);

                $project->professors()->sync($relationData['professor_ids'] ?? []);
                $project->students()->sync($relationData['student_ids'] ?? []);

                $project->load(['projectStatus', 'thematicArea', 'professors', 'students']);

                Log::info('Proyecto creado', [
                    'project_id' => $project->id,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'message' => 'Proyecto creado correctamente.',
                    'data' => $project,
                ], 201);
            });
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear proyecto: ' . $e->getMessage(), [
                'data' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al crear el proyecto.',
            ], 500);
        }
    }

    /**
     * Display the full details of a project including its relationships.
     */
    public function show(int $projectId): JsonResponse
    {
        try {
            $visibleProject = $this->projectQuery(true)->findOrFail($projectId);

            $project = $this->writableProjectQuery(true)
                ->with(['projectStatus', 'thematicArea', 'professors', 'students', 'versions'])
                ->findOrFail($visibleProject->id);

            if ($project->trashed()) {
                return response()->json([
                    'message' => 'El proyecto solicitado no está disponible.',
                ], 404);
            }

            return response()->json($project);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'El proyecto solicitado no existe.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al mostrar proyecto: ' . $e->getMessage(), [
                'project_id' => $projectId,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al obtener el proyecto.',
            ], 500);
        }
    }

    /**
     * Update an existing project and optionally sync its assignments.
     */
    public function update(ProjectRequest $request, int $projectId): JsonResponse
    {
        try {
            $visibleProject = $this->projectQuery(true)->findOrFail($projectId);
            $project = $this->writableProjectQuery()->findOrFail($visibleProject->id);

            if ($project->trashed()) {
                return response()->json([
                    'message' => 'No se puede actualizar un proyecto eliminado.',
                ], 410);
            }

            $data = $request->validated();
            $relationData = Arr::only($data, ['professor_ids', 'student_ids']);
            $projectData = Arr::except($data, ['professor_ids', 'student_ids']);

            return DB::transaction(function () use ($project, $projectData, $relationData) {
                $project->update($projectData);

                if (array_key_exists('professor_ids', $relationData)) {
                    $project->professors()->sync($relationData['professor_ids']);
                }

                if (array_key_exists('student_ids', $relationData)) {
                    $project->students()->sync($relationData['student_ids']);
                }

                $project->load(['projectStatus', 'thematicArea', 'professors', 'students']);

                Log::info('Proyecto actualizado', [
                    'project_id' => $project->id,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'message' => 'Proyecto actualizado correctamente.',
                    'data' => $project,
                ]);
            });
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'No se encontró el proyecto especificado.',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar proyecto: ' . $e->getMessage(), [
                'project_id' => $projectId ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al actualizar el proyecto.',
            ], 500);
        }
    }

    /**
     * Soft delete a project provided it has no registered versions.
     */
    public function destroy(int $projectId): JsonResponse
    {
        try {
            $visibleProject = $this->projectQuery(true)->findOrFail($projectId);
            $project = $this->writableProjectQuery()->findOrFail($visibleProject->id);

            if ($project->trashed()) {
                return response()->json([
                    'message' => 'El proyecto ya fue eliminado.',
                ], 410);
            }

            if ($project->versions()->exists()) {
                return response()->json([
                    'message' => 'No es posible eliminar el proyecto porque tiene versiones registradas.',
                ], 409);
            }

            return DB::transaction(function () use ($project) {
                $project->delete();

                Log::info('Proyecto eliminado', [
                    'project_id' => $project->id,
                    'user_id' => auth()->id(),
                ]);

                return response()->json(null, 204);
            });
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'No se encontró el proyecto especificado.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al eliminar proyecto: ' . $e->getMessage(), [
                'project_id' => $projectId ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al eliminar el proyecto.',
            ], 500);
        }
    }

    /**
     * Restore a previously soft-deleted project instance.
     */
    public function restore(int $id): JsonResponse
    {
        try {
            return DB::transaction(function () use ($id) {
                $visibleProject = $this->projectQuery(true)->findOrFail($id);
                $project = $this->writableProjectQuery(true)->findOrFail($visibleProject->id);

                if (! $project->trashed()) {
                    return response()->json([
                        'message' => 'El proyecto no está eliminado.',
                    ], 400);
                }

                $project->restore();
                $project->load(['projectStatus', 'thematicArea', 'professors', 'students']);

                Log::info('Proyecto restaurado', [
                    'project_id' => $project->id,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'message' => 'Proyecto restaurado correctamente.',
                    'data' => $project,
                ]);
            });
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'No se encontró el proyecto especificado.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al restaurar proyecto: ' . $e->getMessage(), [
                'project_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al restaurar el proyecto.',
            ], 500);
        }
    }

    /**
     * Resolve the model classes for the authenticated user's role.
     */
    protected function resolveModelsForRole(?string $role): array
    {
        return match ($role) {
            'professor', 'committee_leader' => [
                'project' => ProfessorProject::class,
                'status' => ProfessorProjectStatus::class,
                'thematic_area' => ProfessorThematicArea::class,
                'professor' => ProfessorProfessor::class,
                'student' => ProfessorStudent::class,
            ],
            'student' => [
                'project' => StudentProject::class,
                'status' => StudentProjectStatus::class,
                'thematic_area' => StudentThematicArea::class,
                'professor' => StudentProfessor::class,
                'student' => StudentStudent::class,
            ],
            default => [
                'project' => ResearchStaffProject::class,
                'status' => ResearchStaffProjectStatus::class,
                'thematic_area' => ResearchStaffThematicArea::class,
                'professor' => ResearchStaffProfessor::class,
                'student' => ResearchStaffStudent::class,
            ],
        };
    }

    /**
     * Retrieve the model class that has permission to write project data.
     */
    protected function writableProjectModel(): string
    {
        return ResearchStaffProject::class;
    }

    /**
     * Build a query builder using the privileged project model.
     */
    protected function writableProjectQuery(bool $withTrashed = false): Builder
    {
        $model = $this->writableProjectModel();

        $query = $model::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query;
    }

    /**
     * Build the base query for projects, optionally including soft deleted rows.
     */
    protected function projectQuery(bool $withTrashed = false): Builder
    {
        $model = $this->models()['project'];

        $query = $model::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query;
    }

    /**
     * Retrieve the cached model map for the current request.
     */
    protected function models(): array
    {
        if ($this->resolvedModels === null) {
            $this->resolvedModels = $this->resolveModelsForRole(Auth::user()?->role);
        }

        return $this->resolvedModels;
    }
}
