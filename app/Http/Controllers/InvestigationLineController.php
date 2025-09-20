<?php

namespace App\Http\Controllers;

use App\Models\InvestigationLine;
use App\Models\ResearchGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvestigationLineController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $researchGroupId = $request->get('research_group_id');
        $perPage = (int) $request->get('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $investigationLines = InvestigationLine::query()
            ->with(['researchGroup'])
            ->withCount('thematicAreas')
            ->when($search, function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($researchGroupId, function ($query, $groupId) {
                $query->where('research_group_id', $groupId);
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->appends($request->query());

        $researchGroups = ResearchGroup::orderBy('name')->pluck('name', 'id');

        return view('investigation-lines.index', [
            'investigationLines' => $investigationLines,
            'researchGroups' => $researchGroups,
            'search' => $search,
            'researchGroupId' => $researchGroupId,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('investigation-lines.create', [
            'investigationLine' => new InvestigationLine(),
            'researchGroups' => ResearchGroup::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:investigation_lines,name',
            'description' => 'required|string|min:10',
            'research_group_id' => 'required|exists:research_groups,id',
        ]);

        $investigationLine = InvestigationLine::create($data);

        return redirect()
            ->route('investigation-lines.index')
            ->with('success', "Línea de investigación '{$investigationLine->name}' creada correctamente.");
    }

    public function show(InvestigationLine $investigationLine): View
    {
        $investigationLine->load(['researchGroup', 'thematicAreas' => fn ($query) => $query->orderBy('name')]);

        return view('investigation-lines.show', compact('investigationLine'));
    }

    public function edit(InvestigationLine $investigationLine): View
    {
        return view('investigation-lines.edit', [
            'investigationLine' => $investigationLine,
            'researchGroups' => ResearchGroup::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, InvestigationLine $investigationLine): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:investigation_lines,name,' . $investigationLine->id,
            'description' => 'required|string|min:10',
            'research_group_id' => 'required|exists:research_groups,id',
        ]);

        $investigationLine->update($data);

        return redirect()
            ->route('investigation-lines.index')
            ->with('success', "Línea de investigación '{$investigationLine->name}' actualizada correctamente.");
    }

    public function destroy(InvestigationLine $investigationLine): RedirectResponse
    {
        if ($investigationLine->thematicAreas()->exists()) {
            return redirect()
                ->route('investigation-lines.index')
                ->with('error', 'No se puede eliminar la línea porque tiene áreas temáticas asociadas.');
        }

        $name = $investigationLine->name;
        $investigationLine->delete();

        return redirect()
            ->route('investigation-lines.index')
            ->with('success', "Línea de investigación '{$name}' eliminada correctamente.");
    }
}
