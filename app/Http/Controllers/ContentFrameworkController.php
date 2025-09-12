<?php

namespace App\Http\Controllers;

use App\Models\ContentFramework;
use App\Http\Requests\ContentFrameworkRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContentFrameworkController extends Controller
{
    public function index(): View
    {
        $frameworks = ContentFramework::all();
        return view('content_frameworks.index', compact('frameworks'));
    }

    public function create(): View
    {
        return view('content_frameworks.create');
    }

    public function store(ContentFrameworkRequest $request): RedirectResponse
    {
        ContentFramework::create([
            'name' => $request->name,
            'description' => $request->description ?? '',
        ]);

        return redirect()->route('content_frameworks.index');
    }

    public function edit(ContentFramework $contentFramework): View
    {
        return view('content_frameworks.edit', compact('contentFramework'));
    }

    public function update(ContentFrameworkRequest $request, ContentFramework $contentFramework): RedirectResponse
    {
        $contentFramework->update([
            'name' => $request->name,
            'description' => $request->description ?? '',
        ]);

        return redirect()->route('content_frameworks.index');
    }

    public function destroy(ContentFramework $contentFramework): RedirectResponse
    {
        $contentFramework->delete();
        return redirect()->route('content_frameworks.index');
    }
}
