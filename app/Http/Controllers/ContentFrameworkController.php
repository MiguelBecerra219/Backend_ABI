<?php

namespace App\Http\Controllers;

use App\Models\ContentFramework;
use App\Models\Framework;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentFrameworkController extends Controller
{
    public function index(Framework $framework): View
    {
        $contents = $framework->contentFrameworks;
        return view('contents.index', compact('framework', 'contents'));
    }

    public function create(Framework $framework): View
    {
        return view('contents.create', compact('framework'));
    }

    public function store(Request $request, Framework $framework): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $data['framework_id'] = $framework->id;

        $content = ContentFramework::create($data);

        return redirect()->route('contents.show', $content)
            ->with('ok', 'Content created successfully');
    }

    public function show(ContentFramework $content): View
    {
        return view('contents.show', compact('content'));
    }

    public function edit(ContentFramework $content): View
    {
        return view('contents.edit', compact('content'));
    }

    public function update(Request $request, ContentFramework $content): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'framework_id' => 'required|exists:frameworks,id',
        ]);

        $content->update($data);

        return redirect()->route('contents.show', $content)
            ->with('ok', 'Content updated successfully');
    }

    public function destroy(ContentFramework $content): RedirectResponse
    {
        $frameworkId = $content->framework_id;
        $content->delete();

        return redirect()->route('frameworks.contents.index', $frameworkId)
            ->with('ok', 'Content deleted successfully');
    }
}
