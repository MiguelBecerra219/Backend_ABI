<?php

namespace App\Http\Controllers;

use App\Models\Framework;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrameworkController extends Controller
{
    public function index(): View
    {
        $frameworks = Framework::all();
        return view('frameworks.index', compact('frameworks'));
    }

    public function create(): View
    {
        return view('frameworks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_year' => 'required|integer',
            'end_year' => 'required|integer|gte:start_year',
        ]);

        $framework = Framework::create($data);

        return redirect()->route('frameworks.show', $framework)
            ->with('ok', 'Framework created successfully');
    }

    public function show(Framework $framework): View
    {
        $framework->load('contentFrameworks');
        return view('frameworks.show', compact('framework'));
    }

    public function edit(Framework $framework): View
    {
        return view('frameworks.edit', compact('framework'));
    }

    public function update(Request $request, Framework $framework): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_year' => 'required|integer',
            'end_year' => 'required|integer|gte:start_year',
        ]);

        $framework->update($data);

        return redirect()->route('frameworks.show', $framework)
            ->with('ok', 'Framework updated successfully');
    }

    public function destroy(Framework $framework): RedirectResponse
    {
        $framework->delete();

        return redirect()->route('frameworks.index')
            ->with('ok', 'Framework deleted successfully');
    }
}
