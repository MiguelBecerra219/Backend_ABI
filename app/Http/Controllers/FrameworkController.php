<?php

namespace App\Http\Controllers;

use App\Models\Framework;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class FrameworkController
 * @package App\Http\Controllers
 */
class FrameworkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Obtener parámetros de filtrado y búsqueda
        $search = $request->get('search');
        $year = $request->get('year');
        $perPage = $request->get('per_page', 10);

        // Query base
        $query = Framework::query();

        // Aplicar filtros
        if ($search) {
            // Búsqueda por nombre, descripción o ID
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
                
                // Si la búsqueda es numérica, también buscar por ID
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
            });
        }

        if ($year) {
            $query->where(function($q) use ($year) {
                $q->where('start_year', '<=', $year)
                  ->where(function($subQ) use ($year) {
                      $subQ->whereNull('end_year')
                           ->orWhere('end_year', '>=', $year);
                  });
            });
        }

        // Ordenamiento por defecto
        $query->orderBy('start_year', 'desc')
              ->orderBy('name', 'asc');

        $frameworks = $query->paginate($perPage);

        // Mantener parámetros en paginación
        $frameworks->appends($request->query());

        return view('framework.index', compact('frameworks'))
            ->with('i', ($frameworks->currentPage() - 1) * $frameworks->perPage())
            ->with('search', $search)
            ->with('year', $year)
            ->with('perPage', $perPage)
            ->with('status', null); // Add status as null since we removed status functionality
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $framework = new Framework();
        return view('framework.create', compact('framework'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validatedData = $request->validate(Framework::$rules, [
                'name.required' => 'El nombre del framework es obligatorio.',
                'name.unique' => 'Ya existe un framework con este nombre.',
                'description.required' => 'La descripción es obligatoria.',
                'description.min' => 'La descripción debe tener al menos 10 caracteres.',
                'link.url' => 'El enlace debe ser una URL válida.',
                'link.max' => 'El enlace no puede superar los 200 caracteres.',
                'start_year.required' => 'El año de inicio es obligatorio.',
                'start_year.integer' => 'El año de inicio debe ser un número.',
                'start_year.min' => 'El año de inicio no puede ser menor a 1900.',
                'end_year.after_or_equal' => 'El año de fin debe ser posterior o igual al año de inicio.',
            ]);

            DB::beginTransaction();
            
            $validatedData['link'] = $validatedData['link'] ?? '';

            $framework = Framework::create($validatedData);
            
            DB::commit();

            return redirect()->route('frameworks.index')
                ->with('success', "Framework '{$framework->name}' creado exitosamente.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating framework: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Ocurrió un error al crear el framework. Por favor, intente nuevamente.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
     public function show(Framework $framework): View
    {
        return view('framework.show', compact('framework'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Framework $framework): View
    {
        return view('framework.edit', compact('framework'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Framework $framework): RedirectResponse
    {
        try {
            // Usar las reglas básicas sin considerar unicidad del nombre para el mismo registro
            $rules = [
                'name' => 'required|unique:frameworks,name,' . $framework->id,
                'description' => 'required|min:10',
                'link' => 'nullable|url|max:200',
                'start_year' => 'required|integer|min:1900',
                'end_year' => 'nullable|integer|after_or_equal:start_year',
            ];

            $validatedData = $request->validate($rules, [
                'name.required' => 'El nombre del framework es obligatorio.',
                'name.unique' => 'Ya existe otro framework con este nombre.',
                'description.required' => 'La descripción es obligatoria.',
                'description.min' => 'La descripción debe tener al menos 10 caracteres.',
                'link.url' => 'El enlace debe ser una URL válida.',
                'link.max' => 'El enlace no puede superar los 200 caracteres.',
                'start_year.required' => 'El año de inicio es obligatorio.',
                'start_year.integer' => 'El año de inicio debe ser un número.',
                'start_year.min' => 'El año de inicio no puede ser menor a 1900.',
                'end_year.after_or_equal' => 'El año de fin debe ser posterior o igual al año de inicio.',
            ]);

            DB::beginTransaction();
            
            $validatedData['link'] = $validatedData['link'] ?? '';

            $framework->update($validatedData);
            
            DB::commit();

            return redirect()->route('frameworks.index')
                ->with('success', "Framework '{$framework->name}' actualizado exitosamente.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating framework: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Ocurrió un error al actualizar el framework. Por favor, intente nuevamente.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Framework $framework): RedirectResponse
    {
        try {
            // Verificar si puede ser eliminado (si tiene contenidos asociados)
            if ($framework->contentFrameworks()->exists()) {
                return redirect()->route('frameworks.index')
                    ->with('error', 'No se puede eliminar el framework porque tiene contenidos asociados.');
            }

            DB::beginTransaction();
            
            $frameworkName = $framework->name;
            $framework->delete();
            
            DB::commit();

            return redirect()->route('frameworks.index')
                ->with('success', "Framework '{$frameworkName}' eliminado exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting framework: ' . $e->getMessage());
            
            return redirect()->route('frameworks.index')
                ->with('error', 'Ocurrió un error al eliminar el framework. Por favor, intente nuevamente.');
        }
    }
}