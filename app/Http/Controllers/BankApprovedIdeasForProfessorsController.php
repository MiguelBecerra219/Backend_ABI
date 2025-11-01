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

        // 3️⃣ Filtrar proyectos con estado "Aprobado"
        $projects = Project::whereHas('projectStatus', function ($query) {
                $query->where('name', 'Aprobado');
            })
            ->where(function ($query) use ($cityProgramId, $professor) {
                // Proyectos del mismo programa del profesor
                $query->whereHas('students', function ($sub) use ($cityProgramId) {
                    $sub->where('city_program_id', $cityProgramId);
                })
                ->orWhereHas('professors', function ($sub) use ($cityProgramId) {
                    $sub->where('city_program_id', $cityProgramId);
                });
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

        // 4️⃣ Retornar la vista
        return view('projects.professor.approved', compact('projects'));
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
