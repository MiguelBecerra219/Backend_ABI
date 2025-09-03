<?php

namespace App\Http\Controllers;

use App\Models\Framework;
use Illuminate\Http\Request;
use App\Http\Requests\FrameworkRequest;

class FrameworkController extends Controller
{
    public function index(Request $request)
    {
        $search   = (string) $request->input('search', '');
        $yearFrom = $request->filled('year_from') ? (int)$request->input('year_from') : null;
        $yearTo   = $request->filled('year_to')   ? (int)$request->input('year_to')   : null;
        $perPage  = (int) $request->input('per_page', 10);

        $q = Framework::query();

        if ($search !== '') {
            $like = "%{$search}%";
            $q->where(fn($w) => $w->where('name','like',$like)->orWhere('description','like',$like));
        }
        if (!is_null($yearFrom)) $q->where('start_year','>=',$yearFrom);
        if (!is_null($yearTo))   $q->where('end_year','<=',$yearTo);

        $frameworks = $q->latest('updated_at')->paginate($perPage)->withQueryString();

        return view('frameworks.index', compact('frameworks'));
    }

    public function store(FrameworkRequest $request)
    {
        Framework::create($request->validated());
        return back()->with('ok','Framework creado');
    }

    public function show(Framework $framework)
    {
        $framework->load(['contents' => fn($q) => $q->latest('due_at')]);
        return view('frameworks.show', compact('framework'));
    }

    public function update(FrameworkRequest $request, Framework $framework)
    {
        $framework->update($request->validated());
        return back()->with('ok','Framework actualizado');
    }

    public function destroy(Framework $framework)
    {
        $framework->delete();
        return to_route('frameworks.index')->with('ok','Framework eliminado');
    }

    public function create()
{
    // Muestra el formulario de creación
    return view('frameworks.create');
}

public function edit(\App\Models\Framework $framework)
{
    // Muestra el formulario de edición
    return view('frameworks.edit', compact('framework'));
}



}
