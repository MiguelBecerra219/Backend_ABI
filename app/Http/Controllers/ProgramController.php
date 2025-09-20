<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ResearchGroup;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $researchGroupId = $request->get('research_group_id');
        $perPage = (int) $request->get('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $programs = Program::query()
            ->with('researchGroup')
            ->when($search, function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($researchGroupId, function ($query, $groupId) {
                $query->where('research_group_id', $groupId);
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->appends($request->query());

        $researchGroups = ResearchGroup::orderBy('name')->pluck('name', 'id');

        return view('programs.index', [
            'programs' => $programs,
            'researchGroups' => $researchGroups,
            'search' => $search,
            'researchGroupId' => $researchGroupId,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('programs.create', [
            'program' => new Program(),
            'researchGroups' => ResearchGroup::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => 'required|integer|min:1|unique:programs,code',
            'name' => 'required|string|max:100',
            'research_group_id' => 'required|exists:research_groups,id',
        ]);

        $program = Program::create($data);

        return redirect()
            ->route('programs.index')
            ->with('success', "Programa '{$program->name}' creado correctamente.");
    }

    public function show(Program $program): View
    {
        $program->load('researchGroup');

        return view('programs.show', compact('program'));
    }

    public function edit(Program $program): View
    {
        return view('programs.edit', [
            'program' => $program,
            'researchGroups' => ResearchGroup::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, Program $program): RedirectResponse
    {
        $data = $request->validate([
            'code' => 'required|integer|min:1|unique:programs,code,' . $program->id,
            'name' => 'required|string|max:100',
            'research_group_id' => 'required|exists:research_groups,id',
        ]);

        $program->update($data);

        return redirect()
            ->route('programs.index')
            ->with('success', "Programa '{$program->name}' actualizado correctamente.");
    }

    public function destroy(Program $program): RedirectResponse
    {
        try {
            $name = $program->name;
            $program->delete();

            return redirect()
                ->route('programs.index')
                ->with('success', "Programa '{$name}' eliminado correctamente.");
        } catch (QueryException $exception) {
            return redirect()
                ->route('programs.index')
                ->with('error', 'No se puede eliminar el programa porque tiene información relacionada.');
        }
    }
}
