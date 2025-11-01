<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Content;
use App\Models\ContentVersion;
use App\Models\InvestigationLine;
use App\Models\Professor;
use App\Models\Program;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Student;
use App\Models\ThematicArea;
use App\Models\User;
use App\Models\Version;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Helpers\AuthUserHelper;


/**
 * Controller responsible for managing the project proposal lifecycle for students and professors.
 *
 * The controller renders the Tablar views already present in the application and enriches them
 * with the business rules requested for RF01 and RF03.
 */
class ProjectController extends Controller
{
    /**
     * Cache of content identifiers keyed by their human readable name.
     *
     * @var array<string, int>
     */
    protected array $contentCache = [];

    /**
     * Cached identifier for the "waiting evaluation" status to avoid repeated lookups.
     */
    protected ?int $waitingStatusId = null;

    /**
     * Display a paginated list of projects for the authenticated user.
     */
    public function index(Request $request): View
    {
        $user = AuthUserHelper::fullUser();
        $query = Project::query()
            ->with([
                'thematicArea.investigationLine',
                'projectStatus',
                'professors' => static fn ($relation) => $relation->orderBy('last_name')->orderBy('name'),
                'students' => static fn ($relation) => $relation->orderBy('last_name')->orderBy('name'),
            ])
            ->orderByDesc('created_at');

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($user?->role === 'professor' && $user->professor) {
            $professorId = $user->professor->id;
            $query->whereHas('professors', static function ($relation) use ($professorId) {
                $relation->where('professors.id', $professorId);
            });
        } elseif ($user?->role === 'student' && $user->student) {
            $studentId = $user->student->id;
            $query->whereHas('students', static function ($relation) use ($studentId) {
                $relation->where('students.id', $studentId);
            });
        }

        /** @var LengthAwarePaginator $projects */
        $projects = $query->paginate(10)->withQueryString();

        return view('projects.index', [
            'projects' => $projects,
            'search' => $search,
            'isProfessor' => $user?->role === 'professor',
            'isStudent' => $user?->role === 'student',
            'isResearchStaff' => $user?->role === 'research_staff',
        ]);
    }

    /**
     * Ensure the current user is allowed to interact with the projects module.
     *
     * @return array{0: \App\Models\User, 1: bool, 2: bool, 3: bool}
     */
    protected function ensureRoleAccess(bool $allowResearchStaff = false): array
    {
        $user = AuthUserHelper::fullUser();
        $isProfessor = $user?->role === 'professor';
        $isStudent = $user?->role === 'student';
        $isResearchStaff = $user?->role === 'research_staff';

        if (! $isProfessor && ! $isStudent && ! ($allowResearchStaff && $isResearchStaff)) {
            abort(403, 'This action is only available for professors or students.');
        }

        return [$user, $isProfessor, $isStudent, $isResearchStaff];
    }

    /**
     * Show the form used to create a new project idea.
     */
    public function create(): View
    {
        [$user, $isProfessor, $isStudent, $isResearchStaff] = $this->ensureRoleAccess(true);

        if ($isResearchStaff) {
            abort(403, 'Research staff members cannot create project ideas.');
        }

        if ($isProfessor) {
            $researchGroupId = $user->professor?->cityProgram?->program?->research_group_id;
        } else {
            $researchGroupId = $user->student?->cityProgram?->program?->research_group_id;
        }

        $cities = City::query()->orderBy('name')->get();
        $programs = Program::query()->with('researchGroup')->orderBy('name')->get();
        $investigationLines = InvestigationLine::where('research_group_id', $researchGroupId)
        ->whereNull('deleted_at')
        ->get();
        $thematicAreas = ThematicArea::query()->orderBy('name')->get();

        $prefill = [
            'delivery_date' => Carbon::now()->format('Y-m-d'),
        ];

        $availableStudents = collect();
        $availableProfessors = collect();

        if ($isProfessor) {
            $professor = $user->professor;
            if (! $professor) {
                abort(403, 'Professor profile required to submit proposals.');
            }

            $prefill = array_merge($prefill, [
                'first_name' => $professor->name,
                'last_name' => $professor->last_name,
                'email' => $professor->mail ?? $user->email,
                'phone' => $professor->phone,
                'city_id' => optional($professor->cityProgram)->city_id,
                'program_id' => optional($professor->cityProgram)->program_id,
            ]);

            $availableProfessors = Professor::query()
                ->where('id', '!=', $professor->id)
                ->orderBy('last_name')
                ->orderBy('name')
                ->get();
        } else {
            $student = $user->student;
            if (! $student) {
                abort(403, 'Student profile required to submit proposals.');
            }

            $cityProgram = $student->cityProgram;
            $program = $cityProgram?->program;
            $researchGroup = $program?->researchGroup;

            $prefill = array_merge($prefill, [
                'first_name' => $student->name,
                'last_name' => $student->last_name,
                'card_id' => $student->card_id,
                'email' => $user->email,
                'phone' => $student->phone,
                'city_id' => $cityProgram?->city_id,
                'program_id' => $program?->id,
                'research_group' => $researchGroup?->name,
            ]);

            $availableStudents = Student::query()
                ->where('city_program_id', $student->city_program_id)
                ->where('id', '!=', $student->id)
                ->orderBy('last_name')
                ->orderBy('name')
                ->get();
        }

        return view('projects.create', [
            'cities' => $cities,
            'programs' => $programs,
            'investigationLines' => $investigationLines,
            'thematicAreas' => $thematicAreas,
            'prefill' => $prefill,
            'isProfessor' => $isProfessor,
            'isStudent' => $isStudent,
            'availableStudents' => $availableStudents,
            'availableProfessors' => $availableProfessors,
        ]);
    }

    /**
     * Persist a new project idea following the role specific business rules.
     */
    public function store(Request $request): RedirectResponse
    {
        [$user, $isProfessor, $isStudent, $isResearchStaff] = $this->ensureRoleAccess(true);

        try {
            if ($isProfessor) {
                return $this->persistProfessorProject($request, $user->professor);
            }

            if ($isResearchStaff) {
                abort(403, 'Research staff members cannot create project ideas.');
            }

            return $this->persistStudentProject($request, $user->student);
        } catch (\Throwable $exception) {
            Log::error('Failed to register project idea.', [
                'exception' => $exception,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unexpected error. Please try again later.');
        }

        return view('projects.edit', [
            'project' => $project,
            'cities' => $cities,
            'programs' => $programs,
            'investigationLines' => $investigationLines,
            'thematicAreas' => $thematicAreas,
            'prefill' => $prefill,
            'contentValues' => $contentValues,
            'isProfessor' => $isProfessor,
            'isStudent' => $isStudent,
            'availableStudents' => $availableStudents,
            'availableProfessors' => $availableProfessors,
        ]);
    }

    /**
     * Display the details of a project, including its latest version.
     */
    public function show(Project $project): View
    {
        $project->load([
            'thematicArea.investigationLine',
            'projectStatus',
            'professors',
            'students',
            'versions' => static fn ($relation) => $relation
                ->with(['contentVersions.content'])
                ->orderByDesc('created_at'),
        ]);

        $latestVersion = $project->versions->first();
        $contentValues = $this->mapContentValues($latestVersion);

        $user = AuthUserHelper::fullUser();

        return view('projects.show', [
            'project' => $project,
            'latestVersion' => $latestVersion,
            'contentValues' => $contentValues,
            'isProfessor' => $user?->role === 'professor',
            'isStudent' => $user?->role === 'student',
        ]);
    }

    /**
     * Display the edit form with the existing project information.
     */
    public function edit(Project $project): View
    {
        [$user, $isProfessor, $isStudent, $isResearchStaff] = $this->ensureRoleAccess(true);
        $this->authorizeProjectAccess($project, $user->id, $isProfessor, $isStudent, $isResearchStaff);

        $project->load([
            'thematicArea',
            'professors',
            'students',
            'versions' => static fn ($relation) => $relation
                ->with(['contentVersions.content'])
                ->orderByDesc('created_at'),
        ]);

        $latestVersion = $project->versions->first();
        $contentValues = $this->mapContentValues($latestVersion);

        $cities = City::query()->orderBy('name')->get();
        $programs = Program::query()->with('researchGroup')->orderBy('name')->get();
        $investigationLines = InvestigationLine::query()->with('thematicAreas')->orderBy('name')->get();
        $thematicAreas = ThematicArea::query()->orderBy('name')->get();

        $prefill = [
            'delivery_date' => Carbon::now()->format('Y-m-d'),
        ];

        $availableStudents = collect();
        $availableProfessors = collect();

        $hasProfessorParticipants = $project->professors->isNotEmpty();
        $hasStudentParticipants = $project->students->isNotEmpty();

        $useProfessorForm = $isProfessor || ($isResearchStaff && $hasProfessorParticipants);
        $useStudentForm = $isStudent || ($isResearchStaff && ! $hasProfessorParticipants && $hasStudentParticipants);

        if ($useProfessorForm) {
            $contextProfessor = $isProfessor ? $user->professor : $project->professors->first();
            if (! $contextProfessor) {
                abort(403, 'Professor profile required to edit proposals.');
            }

            $prefill = array_merge($prefill, [
                'first_name' => $contextProfessor->name,
                'last_name' => $contextProfessor->last_name,
                'email' => $contextProfessor->mail ?? $contextProfessor->user?->email,
                'phone' => $contextProfessor->phone,
                'city_id' => optional($contextProfessor->cityProgram)->city_id,
                'program_id' => optional($contextProfessor->cityProgram)->program_id,
            ]);

            $availableProfessors = Professor::query()
                ->where('id', '!=', $contextProfessor->id)
                ->orderBy('last_name')
                ->orderBy('name')
                ->get();
        } elseif ($useStudentForm) {
            $contextStudent = $isStudent ? $user->student : $project->students->first();
            if (! $contextStudent) {
                abort(403, 'Student profile required to edit proposals.');
            }

            $cityProgram = $contextStudent->cityProgram;
            $program = $cityProgram?->program;
            $researchGroup = $program?->researchGroup;

            $prefill = array_merge($prefill, [
                'first_name' => $contextStudent->name,
                'last_name' => $contextStudent->last_name,
                'card_id' => $contextStudent->card_id,
                'email' => $contextStudent->user?->email,
                'phone' => $contextStudent->phone,
                'city_id' => $cityProgram?->city_id,
                'program_id' => $program?->id,
                'research_group' => $researchGroup?->name,
            ]);

            $availableStudents = Student::query()
                ->where('city_program_id', $contextStudent->city_program_id)
                ->where('id', '!=', $contextStudent->id)
                ->orderBy('last_name')
                ->orderBy('name')
                ->get();
        } else {
            abort(403, 'Project participants are required to edit this proposal.');
        }

        return view('projects.edit', [
            'project' => $project,
            'cities' => $cities,
            'programs' => $programs,
            'investigationLines' => $investigationLines,
            'thematicAreas' => $thematicAreas,
            'prefill' => $prefill,
            'contentValues' => $contentValues,
            'isProfessor' => $useProfessorForm,
            'isStudent' => $useStudentForm,
            'isResearchStaff' => $isResearchStaff,
            'availableStudents' => $availableStudents,
            'availableProfessors' => $availableProfessors,
        ]);
    }

    /**
     * Update the project information by creating a new version with the submitted content.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        [$user, $isProfessor, $isStudent, $isResearchStaff] = $this->ensureRoleAccess(true);
        $this->authorizeProjectAccess($project, $user->id, $isProfessor, $isStudent, $isResearchStaff);

        $project->loadMissing(['professors', 'students']);

        try {
            if ($isProfessor) {
                return $this->persistProfessorProject($request, $user->professor, $project);
            }

            if ($isStudent) {
                return $this->persistStudentProject($request, $user->student, $project);
            }

            if ($isResearchStaff) {
                $primaryProfessor = $project->professors->first();
                if ($primaryProfessor) {
                    return $this->persistProfessorProject($request, $primaryProfessor, $project);
                }

                $primaryStudent = $project->students->first();
                if ($primaryStudent) {
                    return $this->persistStudentProject($request, $primaryStudent, $project);
                }

                return back()
                    ->withInput()
                    ->with('error', 'The project has no participants to edit.');
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to update project idea.', [
                'project_id' => $project->id,
                'exception' => $exception,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unexpected error. Please try again later.');
        }

        return $version->contentVersions
            ->mapWithKeys(static function (ContentVersion $contentVersion) {
                return [$contentVersion->content->name => $contentVersion->value];
            })
            ->toArray();
    }

    /**
     * Guard access to edit/update operations ensuring the user participates in the project.
     */
    protected function authorizeProjectAccess(Project $project, int $userId, bool $isProfessor, bool $isStudent, bool $isResearchStaff): void
    {
        if ($isResearchStaff) {
            return;
        }

        if ($isProfessor) {
            $user =  AuthUserHelper::fullUser();
            $professor = $user->professor;
            
            if (! $professor || ! $project->professors->contains('id', $professor->id)) {
                abort(403, 'You are not assigned to this project.');
            }
        } elseif ($isStudent) {
            $user =  AuthUserHelper::fullUser();
            $student = $user->student;

            if (! $student || ! $project->students->contains('id', $student->id)) {
                abort(403, 'You are not assigned to this project.');
            }
        } else {
            abort(403, 'Unauthorized access.');
        }
    }

    /**
     * Normalize a project title using the same rules as the Project model mutator.
     */
    protected function normalizeTitle(string $title): string
    {
        return Str::of($title)->squish()->title()->toString();
    }

    /**
     * Retrieve the content identifier by name and cache the lookup.
     */
    protected function contentId(string $name): int
    {
        if (! array_key_exists($name, $this->contentCache)) {
            $content = Content::query()->where('name', $name)->first();
            if (! $content) {
                throw new \RuntimeException("Content '{$name}' not found in catalog.");
            }
            $this->contentCache[$name] = $content->id;
        }

        return $this->contentCache[$name];
    }

    /**
     * Resolve the identifier for the status representing "waiting evaluation".
     */
    protected function waitingEvaluationStatusId(): int
    {
        if ($this->waitingStatusId !== null) {
            return $this->waitingStatusId;
        }

        $status = ProjectStatus::query()
            ->whereIn('name', ['waiting evaluation', 'Pendiente de aprobación'])
            ->orderByRaw("CASE WHEN name = 'waiting evaluation' THEN 0 ELSE 1 END")
            ->first();

        if (! $status) {
            throw new \RuntimeException('Waiting evaluation status is missing from the catalog.');
        }

        $this->waitingStatusId = $status->id;

        return $this->waitingStatusId;
    }

    /**
     * Map the content values for the provided version into a keyed collection.
     *
     * @return array<string, string>
     */
    protected function mapContentValues(?Version $version): array
    {
        if (! $version) {
            return [];
        }

        return $version->contentVersions
            ->mapWithKeys(static function (ContentVersion $contentVersion) {
                return [$contentVersion->content->name => $contentVersion->value];
            })
            ->toArray();
    }

    /**
     * Persist the project data for a professor either creating or updating a record.
     */
    protected function persistProfessorProject(Request $request, ?Professor $professor, ?Project $project = null): RedirectResponse
    {
        if (! $professor) {
            abort(403, 'Professor profile required to complete this action.');
        }

        $baseRules = [
            'city_id' => ['required', 'exists:cities,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'investigation_line_id' => ['required', 'exists:investigation_lines,id'],
            'thematic_area_id' => [
                'required',
                Rule::exists('thematic_areas', 'id')->where(fn ($query) => $query->where('investigation_line_id', $request->integer('investigation_line_id'))),
            ],
            'title' => ['required', 'string', 'max:255'],
            'evaluation_criteria' => ['required', 'string'],
            'students_count' => ['required', 'integer', 'min:1', 'max:3'],
            'execution_time' => ['required', 'string', 'max:255'],
            'viability' => ['required', 'string'],
            'relevance' => ['required', 'string'],
            'teacher_availability' => ['required', 'string'],
            'title_objectives_quality' => ['required', 'string'],
            'general_objective' => ['required', 'string'],
            'description' => ['required', 'string'],
            'contact_first_name' => ['required', 'string', 'max:50'],
            'contact_last_name' => ['required', 'string', 'max:50'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'co_professor_ids' => ['nullable', 'array'],
            'co_professor_ids.*' => ['integer', Rule::exists('professors', 'id')],
        ];

        $validated = $request->validate($baseRules);
        $isUpdate = $project !== null;
        $normalizedTitle = $this->normalizeTitle($validated['title']);

        $professorIds = collect($validated['co_professor_ids'] ?? [])
            ->push($professor->id)
            ->unique()
            ->values()
            ->all();

        $sortedProfessorIds = $professorIds;
        sort($sortedProfessorIds);

        $duplicateProject = Project::query()
            ->when($project, static fn ($query) => $query->where('id', '!=', $project->id))
            ->where('title', $normalizedTitle)
            ->get()
            ->first(static function (Project $existing) use ($sortedProfessorIds) {
                $existingProfessorIds = $existing->professors()->pluck('professors.id')->sort()->values()->all();

                return $existingProfessorIds === $sortedProfessorIds;
            });

        if ($duplicateProject) {
            return back()
                ->withInput()
                ->with('error', 'A project with the same title and professor team already exists.');
        }

        DB::beginTransaction();

        try {
            $professor->fill([
                'name' => $validated['contact_first_name'],
                'last_name' => $validated['contact_last_name'],
                'mail' => $validated['contact_email'],
                'phone' => $validated['contact_phone'],
            ])->save();

            if ($professor->user && $professor->user->email !== $validated['contact_email']) {
                $professor->user->email = $validated['contact_email'];
                $professor->user->save();
            }

            if ($project) {
                $project->fill([
                    'title' => $normalizedTitle,
                    'evaluation_criteria' => $validated['evaluation_criteria'],
                    'thematic_area_id' => $validated['thematic_area_id'],
                    'project_status_id' => $this->waitingEvaluationStatusId(),
                ])->save();
            } else {
                $project = Project::create([
                    'title' => $normalizedTitle,
                    'evaluation_criteria' => $validated['evaluation_criteria'],
                    'thematic_area_id' => $validated['thematic_area_id'],
                    'project_status_id' => $this->waitingEvaluationStatusId(),
                ]);
            }

            $project->professors()->sync($professorIds);

            $version = $project->versions()->create();

            $contentMap = [
                'Título' => $project->title,
                'Cantidad de estudiantes' => (string) $validated['students_count'],
                'Tiempo de ejecución' => $validated['execution_time'],
                'Viabilidad' => $validated['viability'],
                'Pertinencia con el grupo de investigación y con el programa' => $validated['relevance'],
                'Disponibilidad de docentes para su dirección y calificación' => $validated['teacher_availability'],
                'Calidad y correspondencia entre título y objetivo' => $validated['title_objectives_quality'],
                'Objetivo general del proyecto' => $validated['general_objective'],
                'Descripción del proyecto de investigación' => $validated['description'],
            ];

            $this->storeContentValues($version, $contentMap);

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        $message = $isUpdate
            ? 'Project idea updated and set to waiting evaluation'
            : 'Project idea registered and set to waiting evaluation';

        return redirect()
            ->route('projects.index')
            ->with('success', $message);
    }

    /**
     * Persist the project data for a student either creating or updating a record.
     */
    protected function persistStudentProject(Request $request, ?Student $student, ?Project $project = null): RedirectResponse
    {
        if (! $student) {
            abort(403, 'Student profile required to complete this action.');
        }

        $baseRules = [
            'city_id' => ['required', 'exists:cities,id'],
            'investigation_line_id' => ['required', 'exists:investigation_lines,id'],
            'thematic_area_id' => [
                'required',
                Rule::exists('thematic_areas', 'id')->where(fn ($query) => $query->where('investigation_line_id', $request->integer('investigation_line_id'))),
            ],
            'title' => ['required', 'string', 'max:255'],
            'general_objective' => ['required', 'string'],
            'description' => ['required', 'string'],
            'teammate_ids' => ['nullable', 'array', 'max:2'],
            'teammate_ids.*' => [
                'integer',
                Rule::exists('students', 'id')->where(static function ($query) use ($student) {
                    $query->where('city_program_id', $student->city_program_id);
                }),
            ],
            'student_first_name' => ['required', 'string', 'max:50'],
            'student_last_name' => ['required', 'string', 'max:50'],
            'student_card_id' => [
                'required',
                'string',
                'max:25',
                Rule::unique('students', 'card_id')->ignore($student->id),
            ],
            'student_email' => ['required', 'email', 'max:255'],
            'student_phone' => ['nullable', 'string', 'max:20'],
        ];

        $validated = $request->validate($baseRules);
        $isUpdate = $project !== null;

        $cityProgram = $student->cityProgram;
        if ($cityProgram && (int) $validated['city_id'] !== (int) $cityProgram->city_id) {
            return back()
                ->withInput()
                ->with('error', 'The selected city does not match your program assignment.');
        }

        $normalizedTitle = $this->normalizeTitle($validated['title']);
        $studentIds = collect($validated['teammate_ids'] ?? [])
            ->push($student->id)
            ->unique()
            ->values()
            ->all();

        $sortedStudentIds = $studentIds;
        sort($sortedStudentIds);

        if (count($studentIds) > 3) {
            return back()
                ->withInput()
                ->with('error', 'A project can only have up to 3 participating students.');
        }

        $activeStatusIds = ProjectStatus::query()
            ->whereIn('name', ['waiting evaluation', 'Pendiente de aprobación'])
            ->pluck('id');

        $hasActive = $student->projects()
            ->when($project, static fn ($query) => $query->where('projects.id', '!=', $project->id))
            ->whereIn('project_status_id', $activeStatusIds)
            ->exists();

        if ($hasActive) {
            return back()
                ->withInput()
                ->with('error', 'You already have a project idea waiting evaluation.');
        }

        $duplicateProject = Project::query()
            ->when($project, static fn ($query) => $query->where('id', '!=', $project->id))
            ->where('title', $normalizedTitle)
            ->get()
            ->first(static function (Project $existing) use ($sortedStudentIds) {
                $existingStudentIds = $existing->students()->pluck('students.id')->sort()->values()->all();

                return $existingStudentIds === $sortedStudentIds;
            });

        if ($duplicateProject) {
            return back()
                ->withInput()
                ->with('error', 'A project with the same title and student team already exists.');
        }

        DB::beginTransaction();

        try {
            $student->fill([
                'name' => $validated['student_first_name'],
                'last_name' => $validated['student_last_name'],
                'card_id' => $validated['student_card_id'],
                'phone' => $validated['student_phone'],
            ])->save();

            if ($student->user && $student->user->email !== $validated['student_email']) {
                $student->user->email = $validated['student_email'];
                $student->user->save();
            }

            if ($project) {
                $project->fill([
                    'title' => $normalizedTitle,
                    'evaluation_criteria' => null,
                    'thematic_area_id' => $validated['thematic_area_id'],
                    'project_status_id' => $this->waitingEvaluationStatusId(),
                ])->save();
            } else {
                $project = Project::create([
                    'title' => $normalizedTitle,
                    'evaluation_criteria' => null,
                    'thematic_area_id' => $validated['thematic_area_id'],
                    'project_status_id' => $this->waitingEvaluationStatusId(),
                ]);
            }

            $project->students()->sync($studentIds);

            $version = $project->versions()->create();

            $contentMap = [
                'Título' => $project->title,
                'Objetivo general del proyecto' => $validated['general_objective'],
                'Descripción del proyecto de investigación' => $validated['description'],
            ];

            $this->storeContentValues($version, $contentMap);

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        $message = $isUpdate
            ? 'Project idea updated and set to waiting evaluation'
            : 'Project idea registered and set to waiting evaluation';

        return redirect()
            ->route('projects.index')
            ->with('success', $message);
    }

    /**
     * Persist each content value in the content_version table.
     */
    protected function storeContentValues(Version $version, array $contentMap): void
    {
        foreach ($contentMap as $name => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            ContentVersion::create([
                'content_id' => $this->contentId($name),
                'version_id' => $version->id,
                'value' => (string) $value,
            ]);
        }
    }
}
