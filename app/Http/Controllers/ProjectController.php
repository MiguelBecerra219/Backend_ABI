<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\ContentVersion;
use App\Models\InvestigationLine;
use App\Models\Professor;
use App\Models\Program;
use App\Models\Project;
use App\Models\Student;
use App\Models\ThematicArea;
use App\Models\Version;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder; // Added to share the participants base query between the HTML preload and the JSON endpoint.
use Illuminate\Http\JsonResponse; // Added to type-hint JSON responses for the professor search endpoint.
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Helpers\AuthUserHelper;
use App\Models\Framework;
use App\Services\Projects\Exceptions\ProjectIdeaException;
use App\Services\Projects\ProjectIdeaService;
use App\Services\Projects\ProjectParticipantService;
use App\Services\Projects\RoleContext;
use App\Services\Projects\RoleContextResolver;

/**
 * Controller responsible for managing the project proposal lifecycle for students and professors.
 *
 * The controller renders the Tablar views already present in the application and enriches them
 * with the business rules requested for RF01 and RF03.
 */
class ProjectController extends Controller
{
    public function __construct(
        private readonly RoleContextResolver $roleContextResolver,
        private readonly ProjectIdeaService $projectIdeaService,
        private readonly ProjectParticipantService $participantService,
    ) {
    }

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
     * Show the form used to create a new project idea.
     */
    public function create(): View
    {
        $context = $this->roleContextResolver->resolve(true);

        if ($context->isResearchStaff) {
            abort(403, 'Research staff members cannot create project ideas.');
        }

        $activeProfessor = $this->projectIdeaService->resolveProfessorProfile($context->user);
        $researchGroupId = $context->isProfessor
            ? $activeProfessor?->cityProgram?->program?->research_group_id
            : $context->user->student?->cityProgram?->program?->research_group_id;

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
        if ($context->isProfessor) {
            $professor = $activeProfessor;
            if (! $professor) {
                abort(403, 'Professor profile required to submit proposals.');
            }

            $prefill = array_merge($prefill, [
                'first_name' => $professor->name,
                'last_name' => $professor->last_name,
                'email' => $professor->mail ?? $context->user?->email,
                'phone' => $professor->phone,
                'city_id' => optional($professor->cityProgram)->city_id,
                'program_id' => optional($professor->cityProgram)->program_id,
            ]);

            $availableProfessors = $this->participantService
                ->search($professor->id, '')
                ->map(fn (Professor $participant) => $this->participantService->present($participant));
        } else {
            $student = $context->user?->student;
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
                'email' => $context->user?->email,
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
            'isProfessor' => $context->isProfessor,
            'isStudent' => $context->isStudent,
            'isCommitteeLeader' => $context->isCommitteeLeader,
            'availableStudents' => $availableStudents,
            'availableProfessors' => $availableProfessors,
        ]);
    }


    /**
     * Persist a new project idea following the role specific business rules.
     */
    public function store(Request $request): RedirectResponse
    {
        $context = $this->roleContextResolver->resolve(true);

        if ($context->isResearchStaff) {
            abort(403, 'Research staff members cannot create project ideas.');
        }

        try {
            if ($context->isProfessor) {
                $professor = $this->projectIdeaService->resolveProfessorProfile($context->user);
                if (! $professor) {
                    abort(403, 'Professor profile required to submit proposals.');
                }

                $result = $this->projectIdeaService->persistProfessorIdea($request, $professor);
            } else {
                $student = $context->user?->student;
                if (! $student) {
                    abort(403, 'Student profile required to submit proposals.');
                }

                $result = $this->projectIdeaService->persistStudentIdea($request, $student);
            }
        } catch (ProjectIdeaException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            Log::error('Failed to register project idea.', [
                'exception' => $exception,
            ]);

            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('projects.index')
            ->with('success', $result->message);
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
            'contentFrameworks.framework', // Load associated frameworks to display contextual information in the detail view.
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
        $context = $this->roleContextResolver->resolve();

        if (! $context->isProfessor) {
            abort(403, 'Only professors and committee leaders can browse participants.');
        }

        $requestedIds = collect($request->input('ids', []))
            ->filter(static fn ($id) => is_numeric($id))
            ->map(static fn ($id) => (int) $id)
            ->unique();

        if ($requestedIds->isNotEmpty()) {
            $prefetched = $this->participantService->fetchByIds($requestedIds);

            return response()->json([
                'data' => $prefetched
                    ->map(fn (Professor $professor) => $this->participantService->present($professor))
                    ->values(),
                'meta' => null,
            ]);
        }

        $activeProfessor = $this->projectIdeaService->resolveProfessorProfile($context->user);
        $excludeId = $activeProfessor?->id;
        $term = trim((string) $request->input('q', ''));

        $participants = $this->participantService->search($excludeId, $term);

        return response()->json([
            'data' => $participants
                ->map(fn (Professor $professor) => $this->participantService->present($professor))
                ->values(),
            'meta' => null,
        ]);
    }

    

    /**
     * Display the edit form with the existing project information.
     */
    public function edit(Project $project): View
    {

        $normalizedStatus = Str::ascii(Str::lower($project->projectStatus->name ?? '')); // Normalize the status name ignoring accents.
        $editableStatuses = ['devuelto para correccion', 'waiting evaluation', 'pendiente de aprobacion'];

        if (! in_array($normalizedStatus, $editableStatuses, true)) {
            abort(403, 'Projects can only be edited while pending evaluation or after being returned for corrections.');
        }

        $context = $this->roleContextResolver->resolve(true);
        $activeProfessor = $this->projectIdeaService->resolveProfessorProfile($context->user);
        $this->authorizeProjectAccess($project, $context);

        if ($context->isProfessor) {
            $researchGroupId = $activeProfessor?->cityProgram?->program?->research_group_id;
        } else {
            $researchGroupId = $context->user->student?->cityProgram?->program?->research_group_id;
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

        // Fetch review comment if present.
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

        $useProfessorForm = $context->isProfessor || ($context->isResearchStaff && $hasProfessorParticipants);
        $useStudentForm = $context->isStudent || ($context->isResearchStaff && ! $hasProfessorParticipants && $hasStudentParticipants);

        if ($useProfessorForm) {
            $contextProfessor = $context->isProfessor ? $activeProfessor : $project->professors->first();
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

            $availableProfessors = $this->participantService
                ->search(optional($contextProfessor)->id, '')
                ->map(fn (Professor $participant) => $this->participantService->present($participant));
        } elseif ($useStudentForm) {
            $contextStudent = $context->isStudent ? $context->user->student : $project->students->first();
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
                ->whereDoesntHave('projects')
                ->orderBy('last_name')
                ->orderBy('name')
                ->get();

        } else {
            abort(403, 'Project participants are required to edit this proposal.');
        }

        $frameworks = Framework::with('contentFrameworks')
            ->where('end_year', '>=', now()->year)
            ->orderBy('name')
            ->get();

        $selectedContentFrameworkIds = $project
            ->contentFrameworkProjects()
            ->pluck('content_framework_id')
            ->toArray();

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
            'isCommitteeLeader' => $context->isCommitteeLeader,
            'isResearchStaff' => $context->isResearchStaff,
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
        $normalizedStatus = Str::ascii(Str::lower($project->projectStatus->name ?? ''));
        $editableStatuses = ['devuelto para correccion', 'waiting evaluation', 'pendiente de aprobacion'];

        if (! in_array($normalizedStatus, $editableStatuses, true)) {
            abort(403, 'Projects can only be edited while pending evaluation or after being returned for corrections.');
        }

        $context = $this->roleContextResolver->resolve(true);
        $this->authorizeProjectAccess($project, $context);

        $project->loadMissing(['professors', 'students']);

        try {
            if ($context->isProfessor) {
                $professor = $this->projectIdeaService->resolveProfessorProfile($context->user);
                if (! $professor) {
                    abort(403, 'Professor profile required to edit proposals.');
                }

                $result = $this->projectIdeaService->persistProfessorIdea($request, $professor, $project);
            } elseif ($context->isStudent) {
                $student = $context->user?->student;
                if (! $student) {
                    abort(403, 'Student profile required to edit proposals.');
                }

                $result = $this->projectIdeaService->persistStudentIdea($request, $student, $project);
            } elseif ($context->isResearchStaff) {
                $primaryProfessor = $project->professors->first();
                if ($primaryProfessor) {
                    $result = $this->projectIdeaService->persistProfessorIdea($request, $primaryProfessor, $project);
                } else {
                    $primaryStudent = $project->students->first();
                    if (! $primaryStudent) {
                        return back()
                            ->withInput()
                            ->with('error', 'The project has no participants to edit.');
                    }

                    $result = $this->projectIdeaService->persistStudentIdea($request, $primaryStudent, $project);
                }
            } else {
                abort(403, 'You are not allowed to perform this action.');
            }
        } catch (ProjectIdeaException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            Log::error('Failed to update project idea.', [
                'project_id' => $project->id,
                'exception' => $exception,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unexpected error. Please try again later.');
        }

        return redirect()
            ->route('projects.index')
            ->with('success', $result->message);
    }

    /**
     * Guard access to edit/update operations ensuring the user participates in the project.
     */
    protected function authorizeProjectAccess(Project $project, RoleContext $context): void
    {
        if ($context->isResearchStaff) {
            return;
        }

        if ($context->isProfessor) {
            $professor = $this->projectIdeaService->resolveProfessorProfile($context->user);

            if (! $professor || ! $project->professors->contains('id', $professor->id)) {
                abort(403, 'You are not assigned to this project.');
            }
        } elseif ($context->isStudent) {
            $student = $context->user?->student;

            if (! $student || ! $project->students->contains('id', $student->id)) {
                abort(403, 'You are not assigned to this project.');
            }
        } else {
            abort(403, 'Unauthorized access.');
        }
    }

    /**
     * Normalize a project title using the same rules as the Project model mutator.
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

}
