<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentFrameworkRequest;
use App\Models\ResearchStaff\ResearchStaffContentFramework;
use App\Models\ResearchStaff\ResearchStaffFramework;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class ContentFrameworkProjectController
 * @package App\Http\Controllers
 */
class ContentFrameworkProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));
        if ($search === '') {
            $search = null;
        }

        $frameworkId = $request->get('framework_id');

        $query = ResearchStaffContentFramework::with('framework')
            ->orderByDesc('created_at');

        if ($search !== null) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");

                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search);
                }
            });
        }

        if (!empty($frameworkId)) {
            $query->where('framework_id', $frameworkId);
        }

        $contentFrameworkProjects = $query->paginate(10)->withQueryString();

        $frameworkOptions = ResearchStaffFramework::orderBy('name')->pluck('name', 'id');

        return view('content-framework-project.index', compact('contentFrameworkProjects', 'frameworkOptions'))
            ->with('search', $search)
            ->with('framework_id', $frameworkId);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $contentFrameworkProject = new ResearchStaffContentFramework();
        $frameworks = ResearchStaffFramework::orderBy('name')->pluck('name', 'id');
        $prefw = $request->get('framework_id');

        return view('content-framework-project.create', compact('contentFrameworkProject', 'frameworks', 'prefw'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContentFrameworkRequest $request): RedirectResponse
    {
        $contentFrameworkProject = ResearchStaffContentFramework::create($request->validated());

        return redirect()->route('content-framework-projects.index')
            ->with('success', "Contenido '{$contentFrameworkProject->name}' creado correctamente.");
    }

    /**
     * Display the specified resource.
     */
    public function show(ResearchStaffContentFramework $contentFrameworkProject): View
    {
        return view('content-framework-project.show', compact('contentFrameworkProject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResearchStaffContentFramework $contentFrameworkProject): View
    {
        $frameworks = ResearchStaffFramework::orderBy('name')->pluck('name', 'id');

        return view('content-framework-project.edit', compact('contentFrameworkProject', 'frameworks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContentFrameworkRequest $request, ResearchStaffContentFramework $contentFrameworkProject): RedirectResponse
    {
        $contentFrameworkProject->update($request->validated());

        return redirect()->route('content-framework-projects.index')
            ->with('success', "Contenido '{$contentFrameworkProject->name}' actualizado correctamente.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResearchStaffContentFramework $contentFrameworkProject): RedirectResponse
    {
        $nombre = $contentFrameworkProject->name;
        $contentFrameworkProject->delete();

        return redirect()->route('content-framework-projects.index')
            ->with('success', "Contenido '{$nombre}' eliminado correctamente.");
    }
}