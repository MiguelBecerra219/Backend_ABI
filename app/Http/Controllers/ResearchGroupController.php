<?php

namespace App\Http\Controllers;

use App\Models\ResearchGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResearchGroupController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $researchGroups = ResearchGroup::query()
            ->withCount(['programs', 'investigationLines'])
            ->when($search, function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('initials', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->appends($request->query());

        return view('research-groups.index', [
            'researchGroups' => $researchGroups,
            'search' => $search,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('research-groups.create', [
            'researchGroup' => new ResearchGroup(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:research_groups,name',
            'initials' => 'required|string|max:20|unique:research_groups,initials',
            'description' => 'required|string|min:10',
        ]);

        $researchGroup = ResearchGroup::create($data);

        return redirect()
            ->route('research-groups.index')
            ->with('success', "Grupo de investigación '{$researchGroup->name}' creado correctamente.");
    }

    public function show(ResearchGroup $researchGroup): View
    {
        $researchGroup->load([
            'programs' => fn ($query) => $query->orderBy('name'),
            'investigationLines' => fn ($query) => $query->orderBy('name'),
        ]);

        return view('research-groups.show', compact('researchGroup'));
    }

    public function edit(ResearchGroup $researchGroup): View
    {
        return view('research-groups.edit', compact('researchGroup'));
    }

    public function update(Request $request, ResearchGroup $researchGroup): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:research_groups,name,' . $researchGroup->id,
            'initials' => 'required|string|max:20|unique:research_groups,initials,' . $researchGroup->id,
            'description' => 'required|string|min:10',
        ]);

        $researchGroup->update($data);

        return redirect()
            ->route('research-groups.index')
            ->with('success', "Grupo de investigación '{$researchGroup->name}' actualizado correctamente.");
    }

    public function destroy(ResearchGroup $researchGroup): RedirectResponse
    {
        if ($researchGroup->programs()->exists() || $researchGroup->investigationLines()->exists()) {
            return redirect()
                ->route('research-groups.index')
                ->with('error', 'No se puede eliminar el grupo porque tiene programas o líneas de investigación asociadas.');
        }

        $name = $researchGroup->name;
        $researchGroup->delete();

        return redirect()
            ->route('research-groups.index')
            ->with('success', "Grupo de investigación '{$name}' eliminado correctamente.");
    }
}
