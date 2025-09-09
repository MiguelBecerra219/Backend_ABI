<?php

namespace App\Http\Controllers;

use App\Models\ContentFrameworkProject;
use Illuminate\Http\Request;

/**
 * Class ContentFrameworkProjectController
 * @package App\Http\Controllers
 */
class ContentFrameworkProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contentFrameworkProjects = ContentFrameworkProject::paginate(10);

        return view('content-framework-project.index', compact('contentFrameworkProjects'))
            ->with('i', (request()->input('page', 1) - 1) * $contentFrameworkProjects->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $contentFrameworkProject = new ContentFrameworkProject();
        return view('content-framework-project.create', compact('contentFrameworkProject'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(ContentFrameworkProject::$rules);

        $contentFrameworkProject = ContentFrameworkProject::create($request->all());

        return redirect()->route('content-framework-projects.index')
            ->with('success', 'ContentFrameworkProject created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contentFrameworkProject = ContentFrameworkProject::find($id);

        return view('content-framework-project.show', compact('contentFrameworkProject'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contentFrameworkProject = ContentFrameworkProject::find($id);

        return view('content-framework-project.edit', compact('contentFrameworkProject'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  ContentFrameworkProject $contentFrameworkProject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContentFrameworkProject $contentFrameworkProject)
    {
        request()->validate(ContentFrameworkProject::$rules);

        $contentFrameworkProject->update($request->all());

        return redirect()->route('content-framework-projects.index')
            ->with('success', 'ContentFrameworkProject updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $contentFrameworkProject = ContentFrameworkProject::find($id)->delete();

        return redirect()->route('content-framework-projects.index')
            ->with('success', 'ContentFrameworkProject deleted successfully');
    }
}
