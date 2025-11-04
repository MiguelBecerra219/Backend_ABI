<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BankApprovedIdeasForStudentsController extends Controller
{
    /**
     * Muestra los proyectos aprobados relacionados con el estudiante autenticado.
     */
    public function index(Request $request)
    {
        $student = Student::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->first();

        if (!$student || !$student->city_program_id) {
            abort(403, 'No se pudo determinar el programa académico del estudiante.');
        }

        $perPage = $request->input('per_page', 10);
        $cityProgramId = $student->city_program_id;
        $thematicAreaId = $request->input('thematic_area_id');

        // Obtener grupo de investigación del estudiante
        $cityProgram = \App\Models\CityProgram::find($cityProgramId);
        $program = $cityProgram?->program;
        $researchGroup = $program?->researchGroup;

        $thematicAreas = collect();

        if ($researchGroup) {
            $thematicAreas = \App\Models\ThematicArea::whereHas('investigationLine', function ($q) use ($researchGroup) {
                    $q->where('research_group_id', $researchGroup->id);
                })
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get();
        }

        // SOLO PROYECTOS APROBADOS CREADOS POR PROFESORES DEL MISMO CITY_PROGRAM
        $projects = Project::whereHas('projectStatus', fn($q) => $q->where('name', 'Aprobado'))
            ->whereHas('professors', function ($q) use ($cityProgramId) {
                $q->where('city_program_id', $cityProgramId);
            })
            ->with([
                'projectStatus',
                'thematicArea.investigationLine',
                'versions.contentVersions.content',
                'contentFrameworkProjects.contentFramework.framework',
                'professors' // ❗ quitamos students porque ya no se mostrarán proyectos de estudiantes
            ])
            ->paginate($perPage);

        return view('projects.student.approved', [
            'projects' => $projects,
            'thematicAreas' => $thematicAreas,
            'thematicAreaId' => $thematicAreaId,
            'perPage' => $perPage
        ]);
    }

    public function show(Project $project)
    {
        // Obtener el estudiante autenticado
        $student = Student::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->firstOrFail();

        // Validar que pertenece al mismo programa
        $sameProgram = $project->students()
                ->where('city_program_id', $student->city_program_id)
                ->exists()
            || $project->professors()
                ->where('city_program_id', $student->city_program_id)
                ->exists();

        if (! $sameProgram) {
            abort(403, 'No tienes permiso para ver este proyecto.');
        }

        // Cargar relaciones
        $project->load([
            'projectStatus',
            'thematicArea.investigationLine',
            'versions.contentVersions.content',
            'contentFrameworkProjects.contentFramework.framework',
            'students',
            'professors'
        ]);

        // Última versión
        $latestVersion = $project->versions()->latest('created_at')->first();

        // Mapear contenidos para mostrar como label => valor
        $contentValues = [];
        if ($latestVersion) {
            $contentValues = $latestVersion->contentVersions
                ->mapWithKeys(fn($cv) => [$cv->content->name => $cv->value])
                ->toArray();
        }

        // Marcos seleccionados
        $frameworksSelected = $project->contentFrameworkProjects()
            ->with('contentFramework.framework')
            ->get()
            ->map(fn($item) => $item->contentFramework);

        /**
         * El estudiante NO podrá seleccionar un proyecto si ya tiene uno en estado "Asignado".
         */
        $hasAssignedProject = $student->projects()
            ->whereHas('projectStatus', fn($q) => $q->where('name', 'Asignado'))
            ->exists();

        // Si NO tiene proyecto asignado => puede ver botón (true)
        $canSelectProject = ! $hasAssignedProject;

        return view('projects.student.show', compact(
            'project',
            'latestVersion',
            'contentValues',
            'frameworksSelected',
            'canSelectProject'
        ));
    }

}
