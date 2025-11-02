<?php

namespace App\Http\Controllers;
use App\Models\Professor;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BankApprovedIdeasForProfessorsController extends Controller
{
    /**
     * Muestra los proyectos aprobados relacionados con el estudiante autenticado.
     */
    public function index(Request $request)
    {
        // 1️⃣ Obtener el estudiante autenticado
        $professor = Professor::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->first();

        // 2️⃣ Validar que exista y tenga programa
        if (!$professor || !$professor->city_program_id) {
            abort(403, 'No se pudo determinar el programa académico del estudiante.');
        }

        $perPage = $request->input('per_page', 10);
        $cityProgramId = $professor->city_program_id;
        $thematicAreaId = $request->input('thematic_area_id');

        // 1️⃣ Obtener el registro CityProgram
        $cityProgram = \App\Models\CityProgram::find($cityProgramId);

        // 2️⃣ Obtener el programa asociado
        $program = $cityProgram?->program;

        // 3️⃣ Obtener el grupo de investigación
        $researchGroup = $program?->researchGroup;

        // 4️⃣ Obtener las áreas temáticas del grupo
        $thematicAreas = collect(); // default
        if ($researchGroup) {
            $thematicAreas = \App\Models\ThematicArea::whereHas('investigationLine', function ($q) use ($researchGroup) {
                    $q->where('research_group_id', $researchGroup->id);
                })
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get();
        }

        // Filtrar proyectos aprobados
        $projects = Project::whereHas('projectStatus', fn($q) => $q->where('name', 'Aprobado'))
            ->where(function ($query) use ($cityProgramId) {
                $query->whereHas('students', fn($sub) => $sub->where('city_program_id', $cityProgramId))
                    ->orWhereHas('professors', fn($sub) => $sub->where('city_program_id', $cityProgramId));
            })
            ->with([
                'projectStatus',
                'thematicArea.investigationLine',
                'versions.contentVersions.content',
                'contentFrameworkProjects.contentFramework.framework',
                'students',
                'professors'
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
        // Obtener el profesor autenticado
        $professor = Professor::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->firstOrFail();

        // Verificar si el proyecto pertenece al mismo programa del profesor
        $sameProgram = $project->students()
            ->where('city_program_id', $professor->city_program_id)
            ->exists()
            || $project->professors()
                ->where('city_program_id', $professor->city_program_id)
                ->exists();

        if (! $sameProgram) {
            abort(403, 'No tienes permiso para ver este proyecto.');
        }

        // Cargar relaciones necesarias
        $project->load([
            'projectStatus',
            'thematicArea.investigationLine',
            'versions.contentVersions.content',
            'contentFrameworkProjects.contentFramework.framework',
            'students',
            'professors',
        ]);

        // Obtener la última versión del proyecto
        $latestVersion = $project->versions()->latest('created_at')->first();

        return view('projects.professor.show', compact('project', 'latestVersion'));
    }
}
