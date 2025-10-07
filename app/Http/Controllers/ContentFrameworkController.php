<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffContentFramework;
use App\Http\Requests\ContentFrameworkRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para la gestión de frameworks de contenido
 *
 * Maneja el CRUD completo de frameworks de contenido con soft delete,
 * validaciones robustas y logging de operaciones.
 */
class ContentFrameworkController extends Controller
{
    /**
     * Muestra el listado de frameworks de contenido activos
     *
     * @return View Vista con listado de frameworks
     */
    public function index(): View
    {
        try {
            // Obtener todos los frameworks activos (no eliminados)
            $frameworks = ResearchStaffContentFramework::all();

            return view('content_frameworks.index', compact('frameworks'));

        } catch (\Exception $e) {
            Log::error('Error al listar frameworks de contenido: ' . $e->getMessage());

            return view('content_frameworks.index', [
                'frameworks' => collect(),
                'error' => 'Ocurrió un error al cargar los frameworks.'
            ]);
        }
    }

    /**
     * Muestra el formulario para crear un nuevo framework
     *
     * @return View Vista del formulario de creación
     */
    public function create(): View
    {
        return view('content_frameworks.create');
    }

    /**
     * Almacena un nuevo framework de contenido en la base de datos
     *
     * @param ContentFrameworkRequest $request Datos validados del framework
     * @return RedirectResponse Redirección con mensaje de resultado
     */
    public function store(ContentFrameworkRequest $request): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                // Crear el framework
                $framework = ResearchStaffContentFramework::create([
                    'name' => $request->name,
                    'description' => $request->description ?? '',
                ]);

                // Registrar evento en logs
                Log::info('Framework de contenido creado', [
                    'framework_id' => $framework->id,
                    'framework_name' => $framework->name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('content_frameworks.index')
                    ->with('success', "Framework '{$framework->name}' creado correctamente.");
            });

        } catch (\Exception $e) {
            Log::error('Error al crear framework de contenido: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al crear el framework. Por favor, intente nuevamente.');
        }
    }

    /**
     * Muestra el formulario para editar un framework existente
     *
     * @param ContentFramework $contentFramework Framework a editar
     * @return View Vista del formulario de edición
     */
    public function edit(ResearchStaffContentFramework $contentFramework): View
    {
        // Verificar si fue eliminado lógicamente
        if ($contentFramework->trashed()) {
            abort(404, 'El framework no está disponible.');
        }

        return view('content_frameworks.edit', compact('contentFramework'));
    }

    /**
     * Actualiza un framework existente en la base de datos
     *
     * @param ContentFrameworkRequest $request Datos validados
     * @param ContentFramework $contentFramework Framework a actualizar
     * @return RedirectResponse Redirección con mensaje
     */
    public function update(ContentFrameworkRequest $request, ResearchStaffContentFramework $contentFramework): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($request, $contentFramework) {
                // Verificar si fue eliminado lógicamente
                if ($contentFramework->trashed()) {
                    return redirect()
                        ->route('content_frameworks.index')
                        ->with('error', 'No se puede actualizar un framework eliminado.');
                }

                // Actualizar datos
                $contentFramework->update([
                    'name' => $request->name,
                    'description' => $request->description ?? '',
                ]);

                // Registrar evento en logs
                Log::info('Framework de contenido actualizado', [
                    'framework_id' => $contentFramework->id,
                    'framework_name' => $contentFramework->name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('content_frameworks.index')
                    ->with('success', "Framework '{$contentFramework->name}' actualizado correctamente.");
            });

        } catch (\Exception $e) {
            Log::error('Error al actualizar framework: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al actualizar el framework.');
        }
    }

    /**
     * Elimina lógicamente (soft delete) un framework
     *
     * Utiliza soft delete para mantener el registro en la base de datos
     * pero marcarlo como eliminado.
     *
     * @param ContentFramework $contentFramework Framework a eliminar
     * @return RedirectResponse Redirección con mensaje
     */
    public function destroy(ResearchStaffContentFramework $contentFramework): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($contentFramework) {
                // Verificar si ya fue eliminado
                if ($contentFramework->trashed()) {
                    return redirect()
                        ->route('content_frameworks.index')
                        ->with('error', 'El framework ya fue eliminado.');
                }

                $name = $contentFramework->name;

                // Realizar soft delete
                $contentFramework->delete();

                // Registrar evento en logs
                Log::info('Framework de contenido eliminado', [
                    'framework_id' => $contentFramework->id,
                    'framework_name' => $name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('content_frameworks.index')
                    ->with('success', "Framework '{$name}' eliminado correctamente.");
            });

        } catch (\Exception $e) {
            Log::error('Error al eliminar framework: ' . $e->getMessage());

            return redirect()
                ->route('content_frameworks.index')
                ->with('error', 'Ocurrió un error al eliminar el framework.');
        }
    }

    /**
     * Restaura un framework eliminado lógicamente
     *
     * Permite recuperar frameworks que fueron eliminados con soft delete
     *
     * @param int $id ID del framework a restaurar
     * @return RedirectResponse Redirección con mensaje
     */
    public function restore(int $id): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($id) {
                // Buscar framework incluyendo eliminados
                $framework = ResearchStaffContentFramework::withTrashed()->findOrFail($id);

                // Verificar si está eliminado
                if (!$framework->trashed()) {
                    return redirect()
                        ->route('content_frameworks.index')
                        ->with('error', 'El framework no está eliminado.');
                }

                // Restaurar
                $framework->restore();

                // Registrar evento en logs
                Log::info('Framework de contenido restaurado', [
                    'framework_id' => $framework->id,
                    'framework_name' => $framework->name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('content_frameworks.index')
                    ->with('success', "Framework '{$framework->name}' restaurado correctamente.");
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('content_frameworks.index')
                ->with('error', 'No se encontró el framework especificado.');
        } catch (\Exception $e) {
            Log::error('Error al restaurar framework: ' . $e->getMessage());

            return redirect()
                ->route('content_frameworks.index')
                ->with('error', 'Ocurrió un error al restaurar el framework.');
        }
    }
}
