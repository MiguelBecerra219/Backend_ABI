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
use Illuminate\Database\Eloquent\Builder; // Added to share the participants base query between the HTML preload and the JSON endpoint.
use Illuminate\Http\JsonResponse; // Added to type-hint JSON responses for the professor search endpoint.
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Helpers\AuthUserHelper;
use App\Models\Framework;

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
            'professors' => static fn ($relation) => $relation
                ->with(['user', 'cityProgram.program']) // Added eager loading to reuse the program relationship for committee leader filtering and email display.
                ->orderBy('last_name')
                ->orderBy('name'),
            'students' => static fn ($relation) => $relation->orderBy('last_name')->orderBy('name'),
        ])
        ->orderByDesc('created_at');

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where('title', 'like', "%{$search}%");
        }

        $programFilter = null; // Initialize the variable so we can pass the selected value back to the view with a comment explaining its purpose.

        if ($user?->role === 'committee_leader') {
            $programFilter = $request->integer('program_id'); // Capture the desired program filter to keep the pagination query string stable.

            if ($programFilter) {
                $query->whereHas('professors.cityProgram', static function (Builder $builder) use ($programFilter) {
                    $builder->where('program_id', $programFilter); // Narrow the project listing to the chosen academic program when a committee leader is browsing.
                });
            }
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

        $programCatalog = collect(); // Provide an empty fallback to avoid leaking program listings to other roles.

        if ($user?->role === 'committee_leader') {
            $programCatalog = Program::query()->orderBy('name')->get(); // Preload the programs list so committee leaders can filter projects without extra queries from the Blade view.
        }

        return view('projects.index', [
            'projects' => $projects,
            'search' => $search,
            'isProfessor' => in_array($user?->role, ['professor', 'committee_leader'], true), // Allow committee leaders to share the professor permissions in the view layer.
            'isStudent' => $user?->role === 'student',
            'isCommitteeLeader' => $user?->role === 'committee_leader', // Expose the role explicitly to toggle UI elements when needed.
            'isResearchStaff' => $user?->role === 'research_staff',
            'programCatalog' => $programCatalog, // Pass the catalog so the Blade can render the new drop-down in the filters section.
            'selectedProgram' => $programFilter, // Keep the current filter selected during pagination.
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
        $isProfessor = in_array($user?->role, ['professor', 'committee_leader'], true); // Treat committee leaders exactly like professors for access checks.
        $isStudent = $user?->role === 'student';
        $isCommitteeLeader = $user?->role === 'committee_leader'; // Track the role for downstream logic that needs to know the exact profile type.
        $isResearchStaff = $user?->role === 'research_staff';

        if (! $isProfessor && ! $isStudent && ! ($allowResearchStaff && $isResearchStaff)) {
            abort(403, 'This action is only available for professors, committee leaders or students.'); // Updated message to mention the new allowed role.
        }

        return [$user, $isProfessor, $isStudent, $isResearchStaff, $isCommitteeLeader]; // Include the explicit role flag so callers can adapt the UI.
    }

    /**
     * Show the form used to create a new project idea.
     */
    public function create(): View
    {
        [$user, $isProfessor, $isStudent, $isResearchStaff, $isCommitteeLeader] = $this->ensureRoleAccess(true); // Capture the committee leader flag to mirror professor permissions later.
        $activeProfessor = $this->resolveProfessorProfile($user); // Locate the professor profile even when the relationship is not eager loaded (committee leaders share the same model).

        if ($isResearchStaff) {
            abort(403, 'Research staff members cannot create project ideas.');
        }

        if ($isProfessor) {
            $researchGroupId = $activeProfessor?->cityProgram?->program?->research_group_id;
        } else {
            $researchGroupId = $user->student?->cityProgram?->program?->research_group_id;
        }

        $cities = City::query()->orderBy('name')->get();
        $programs = Program::query()->with('researchGroup')->orderBy('name')->get();
        $investigationLines = InvestigationLine::where('research_group_id', $researchGroupId)
            ->whereNull('deleted_at')
            ->get();
        $thematicAreas = ThematicArea::query()->orderBy('name')->get();

        $year = now()->year;

        $frameworks = \App\Models\Framework::with('contentFrameworks')
            ->where('start_year', '<=', $year)
            ->where('end_year', '>=', $year)
            ->orderBy('name')
            ->get();

        $prefill = [
            'delivery_date' => Carbon::now()->format('Y-m-d'),
        ];

        $availableStudents = collect();
        $availableProfessors = collect();

        if ($isProfessor) {
            $professor = $activeProfessor;
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

            $availableProfessors = $this->participantQuery($professor->id)
                ->get()
                ->map(fn (Professor $participant) => $this->presentParticipant($participant)); // Provide the full catalog so the picker can render every eligible participant without pagination.
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
                ->whereDoesntHave('projects')
                ->orderBy('last_name')
                ->orderBy('name')
                ->get();
        }

        return view('projects.create', [
            'cities' => $cities,
            'programs' => $programs,
            'investigationLines' => $investigationLines,
            'thematicAreas' => $thematicAreas,
            'frameworks' => $frameworks,
            'prefill' => $prefill,
            'isProfessor' => $isProfessor,
            'isStudent' => $isStudent,
            'isCommitteeLeader' => $isCommitteeLeader, // Expose the new role so the Blade template can adjust the UI consistently.
            'availableStudents' => $availableStudents,
            'availableProfessors' => $availableProfessors,
        ]);
    }


    /**
     * Persist a new project idea following the role specific business rules.
     */
    public function store(Request $request): RedirectResponse
    {
        [$user, $isProfessor, $isStudent, $isResearchStaff] = $this->ensureRoleAccess(true); // The committee leader flag is not required here because store() delegates immediately.

        try {
            if ($isProfessor) {
                $professorProfile = $this->resolveProfessorProfile($user); // Ensure committee leaders leverage the same professor record to persist the project.

                return $this->persistProfessorProject($request, $professorProfile);
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
            'professors.user', // Eager load the user to expose a reliable email address on the detail page.
            'professors.cityProgram.program', // Preload the program so committee leaders can see contextual data without extra queries.
            'students',
            'contentFrameworks.framework', // ← Añadido
            'versions' => static fn ($relation) => $relation
                ->with(['contentVersions.content'])
                ->orderByDesc('created_at'),
        ]);

        $latestVersion = $project->versions->first();
        $contentValues = $this->mapContentValues($latestVersion);

        $normalizedStatus = Str::lower($project->projectStatus->name ?? '');
        $reviewComment = null;

        if ($normalizedStatus === 'devuelto para corrección' && $latestVersion) {
            $reviewContent = $latestVersion->contentVersions
                ->first(static function (ContentVersion $contentVersion): bool {
                    return Str::lower($contentVersion->content->name ?? '') === 'comentarios';
                });

            $reviewComment = $reviewContent?->value;
        }

        $user = AuthUserHelper::fullUser();

        return view('projects.show', [
            'project' => $project,
            'latestVersion' => $latestVersion,
            'contentValues' => $contentValues,
            'frameworksSelected' => $project->contentFrameworks,
            'isProfessor' => in_array($user?->role, ['professor', 'committee_leader'], true), // Allow committee leaders to reuse the professor-specific UI controls.
            'isStudent' => $user?->role === 'student',
            'isCommitteeLeader' => $user?->role === 'committee_leader', // Expose the role explicitly so the Blade can toggle actions if needed.
            'reviewComment' => $reviewComment,
        ]);
    }

    /**
     * Provide an AJAX friendly list of professors and committee leaders to associate with a project.
     */
    public function participants(Request $request): JsonResponse
    {
        [$user, $isProfessor] = $this->ensureRoleAccess(); // Reuse the shared guard to ensure only professors and committee leaders reach this endpoint.

        if (! $isProfessor) {
            abort(403, 'Only professors and committee leaders can browse participants.'); // Keep unauthorized roles from enumerating the catalog.
        }

        $requestedIds = collect($request->input('ids', []))
            ->filter(static fn ($id) => is_numeric($id))
            ->map(static fn ($id) => (int) $id)
            ->unique();

        if ($requestedIds->isNotEmpty()) {
            $prefetched = $this->participantQuery(null)
                ->whereIn('professors.id', $requestedIds)
                ->get();

            return response()->json([
                'data' => $prefetched
                    ->map(fn (Professor $professor) => $this->presentParticipant($professor))
                    ->values(),
                'meta' => null,
            ]); // Return a flat payload so the client can restore selections after validation errors while keeping numeric indexes in the JSON response.
        }

        $activeProfessor = $this->resolveProfessorProfile($user); // Resolve the shared professor profile so committee leaders also receive consistent exclusions.
        $excludeId = $activeProfessor?->id; // Exclude the authenticated profile from the suggestion list to avoid redundant chips.
        $term = trim((string) $request->input('q', ''));

        $query = $this->participantQuery($excludeId);

        if ($term !== '') {
            $normalizedTerm = mb_strtolower($term);

            $query->where(static function (Builder $builder) use ($normalizedTerm, $term) {
                $builder->whereRaw('LOWER(professors.name) like ?', ["%{$normalizedTerm}%"])
                    ->orWhereRaw('LOWER(professors.last_name) like ?', ["%{$normalizedTerm}%"])
                    ->orWhere('professors.card_id', 'like', "%{$term}%")
                    ->orWhereRaw('LOWER(professors.mail) like ?', ["%{$normalizedTerm}%"])
                    ->orWhereHas('user', static function (Builder $userQuery) use ($normalizedTerm) {
                        $userQuery->whereRaw('LOWER(email) like ?', ["%{$normalizedTerm}%"]);
                    });
            }); // Allow filtering by name, last name, document or email regardless of casing.
        }

        $participants = $query->get();

        return response()->json([
            'data' => $participants
                ->map(fn (Professor $professor) => $this->presentParticipant($professor))
                ->values(),
            'meta' => null,
        ]); // Return the full catalog so the frontend can display all available participants without pagination and with sequential indexes for the JavaScript consumer.
    }

    /**
     * Build the base query for participants, optionally excluding the authenticated profile.
     */
    protected function participantQuery(?int $excludeProfessorId = null): Builder
    {
        return Professor::query()
            ->select('professors.*')
            ->with(['user', 'cityProgram.program'])
            ->whereHas('user', static function (Builder $builder) {
                $builder->whereIn('role', ['professor', 'committee_leader', 'committe_leader']);
            })
            ->whereNull('professors.deleted_at') // Skip soft-deleted records so they do not appear in the picker or JSON endpoint.
            ->when($excludeProfessorId, static function (Builder $builder, int $exclude) {
                $builder->where('professors.id', '!=', $exclude);
            })
            ->orderBy('professors.last_name')
            ->orderBy('professors.name'); // Keep ordering consistent between the initial HTML payload and the AJAX requests.
    }

    /**
     * Normalize the participant payload so the Blade and JS layers consume the same shape.
     */
    protected function presentParticipant(Professor $professor): array
    {
        return [
            'id' => $professor->id,
            'name' => trim(($professor->name ?? '') . ' ' . ($professor->last_name ?? '')),
            'document' => $professor->card_id,
            'email' => $professor->mail ?? $professor->user?->email,
            'program' => optional($professor->cityProgram?->program)->name,
        ]; // Include the email and program so the interface can display richer context while selecting collaborators.
    }

    /**
     * Resolve the professor profile associated with the authenticated user, covering committee leaders too.
     */
    protected function resolveProfessorProfile(?User $user): ?Professor
    {
        if (! $user) {
            return null;
        }

        if ($user->relationLoaded('professor') || array_key_exists('professor', $user->getRelations())) {
            if ($user->professor) {
                return $user->professor;
            }
        }

        return Professor::query()->where('user_id', $user->id)->first();
    }


    /**
     * Display the edit form with the existing project information.
     */
    public function edit(Project $project): View
    {

        $statusName = Str::lower($project->projectStatus->name ?? ''); // Normalize the status name to apply guards consistently.

        if ($statusName === 'pendiente de aprobación') {
            abort(403, 'Projects pending approval cannot be edited.'); // Block editing attempts when the project is waiting for approval.
        }

        if ($project->projectStatus?->name !== 'Devuelto para corrección') {
            abort(403, 'Solo los proyectos devueltos para corrección pueden ser editados.');
        }

        [$user, $isProfessor, $isStudent, $isResearchStaff, $isCommitteeLeader] = $this->ensureRoleAccess(true); // Include committee leaders in the edit flow to mirror professor capabilities.
        $activeProfessor = $this->resolveProfessorProfile($user); // Resolve the shared professor profile so committee leaders can reuse the same datasets without additional queries.
        $this->authorizeProjectAccess($project, $user->id, $isProfessor, $isStudent, $isResearchStaff);

        if ($isProfessor) {
            $researchGroupId = $activeProfessor?->cityProgram?->program?->research_group_id;
        } else {
            $researchGroupId = $user->student?->cityProgram?->program?->research_group_id;
        }

        
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

        // Extraer comentario si existe
        $versionComment = null;
        if ($latestVersion) {
            $commentContent = $latestVersion->contentVersions
                ->firstWhere(fn ($cv) => $cv->content->name === 'Comentarios');

            $versionComment = $commentContent->value ?? null;
        }

        $cities = City::query()->orderBy('name')->get();
        $programs = Program::query()->with('researchGroup')->orderBy('name')->get();
        $investigationLines = InvestigationLine::where('research_group_id', $researchGroupId)
            ->whereNull('deleted_at')
            ->get();
        $thematicAreas = ThematicArea::query()->orderBy('name')->get();
        $selectedInvestigationLineId = $project->thematicArea->investigation_line_id ?? null;
        $selectedThematicAreaId = $project->thematic_area_id ?? null;


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
            $contextProfessor = $isProfessor ? $activeProfessor : $project->professors->first();
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

            $availableProfessors = $this->participantQuery(optional($contextProfessor)->id)
                ->get()
                ->map(fn (Professor $participant) => $this->presentParticipant($participant)); // Share the full catalog so editing uses the same dataset as creation.
        } elseif ($useStudentForm) {
            $contextStudent = $isStudent ? $user->student : $project->students->first();
            if (! $contextStudent) {
                abort(403, 'Student profile required to edit proposals.');
            }

            $cityProgram = $contextStudent->cityProgram;
            $program = $cityProgram?->program;
            $researchGroup = $program?->researchGroup;

            // Frameworks disponibles
            $frameworks = Framework::with('contentFrameworks')
                ->where('end_year', '>=', now()->year)
                ->orderBy('name')
                ->get();

            // Content frameworks seleccionados del proyecto
            $selectedContentFrameworkIds = $project
                ->contentFrameworkProjects()
                ->pluck('content_framework_id')
                ->toArray();

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
                ->whereDoesntHave('projects')
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
            'isCommitteeLeader' => $isCommitteeLeader, // Allow the Blade to know when the editor is a committee leader for UI constraints.
            'isResearchStaff' => $isResearchStaff,
            'availableStudents' => $availableStudents,
            'availableProfessors' => $availableProfessors,
            'frameworks' => $frameworks,
            'selectedContentFrameworkIds' => $selectedContentFrameworkIds,
            'selectedInvestigationLineId' => $selectedInvestigationLineId,
            'selectedThematicAreaId' => $selectedThematicAreaId,
            'versionComment' => $versionComment,
        ]);
    }

    /**
     * Update the project information by creating a new version with the submitted content.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $statusName = Str::lower($project->projectStatus->name ?? ''); // Normalize again on update to avoid duplicated string comparisons.

        if ($statusName === 'pendiente de aprobación') {
            abort(403, 'Projects pending approval cannot be edited.'); // Prevent updates when the UI should hide the edit button.
        }

        if ($project->projectStatus?->name !== 'Devuelto para corrección') {
            abort(403, 'Solo los proyectos devueltos para corrección pueden ser editados.');
        }

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

        $assignedProgramId = optional($professor->cityProgram)->program_id; // Retrieve the immutable program linked to the authenticated professor or committee leader.

        if (! $assignedProgramId) {
            abort(403, 'A program assignment is required before submitting projects.'); // Stop early when the profile is incomplete.
        }

        $request->merge(['program_id' => $assignedProgramId]); // Force the incoming request to honour the assigned program regardless of client-side manipulation.

        $baseRules = [
            'city_id' => ['required', 'exists:cities,id'],
            'program_id' => ['required', 'integer', Rule::in([$assignedProgramId])], // Ensure the locked program cannot be altered on submission.
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
            'associated_professors' => ['nullable', 'array'], // Capture the selected co-professors from the dynamic chips component.
            'associated_professors.*' => ['integer', Rule::exists('professors', 'id')->whereNull('deleted_at')], // Ensure every id belongs to an active professor record.
            'content_frameworks' => ['required', 'array'],
            'content_frameworks.*' => ['required', Rule::exists('content_frameworks', 'id')],
        ];

        $validated = $request->validate($baseRules);
        $isUpdate = $project !== null;
        $normalizedTitle = $this->normalizeTitle($validated['title']);

        $professorIds = collect($validated['associated_professors'] ?? [])
            ->filter(static fn ($id) => $id !== null) // Remove empty array slots left by the client script.
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

            // Guardar los content frameworks
            $contentFrameworkIds = array_values(array_filter($validated['content_frameworks'] ?? []));
            $project->contentFrameworks()->sync($contentFrameworkIds);

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
            'content_frameworks' => ['required', 'array'],
            'content_frameworks.*' => ['required', Rule::exists('content_frameworks', 'id')],
        ];

        $validated = $request->validate($baseRules);
        $isUpdate = $project !== null;

        // Validar que los compañeros no tengan otros proyectos vinculados
        if (!empty($validated['teammate_ids'])) {
            $hasOtherProjects = Student::query()
                ->whereIn('id', $validated['teammate_ids'])
                ->whereHas('projects', function ($query) use ($project) {
                    $query->where('project_id', '!=', $project?->id); // distinto del que está editando
                })
                ->exists();

            if ($hasOtherProjects) {
                return back()
                    ->withInput()
                    ->with('error', 'Uno o más compañeros seleccionados ya tienen un proyecto registrado.');
            }
        }

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

            // Guardar los content frameworks
            $contentFrameworkIds = array_values(array_filter($validated['content_frameworks'] ?? []));
            $project->contentFrameworks()->sync($contentFrameworkIds);

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
