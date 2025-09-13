<?php

namespace App\Http\Controllers;

use App\Models\InvestigationLine;
use Illuminate\Http\Request;

class InvestigationLineController extends Controller
{
    public function index()
    {
        return InvestigationLine::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'research_group_id' => 'required|exists:research_groups,id',
        ]);

        $line = InvestigationLine::create($data);

        return response()->json($line, 201);
    }

    public function show(InvestigationLine $investigationLine)
    {
        return $investigationLine;
    }

    public function update(Request $request, InvestigationLine $investigationLine)
    {
        $data = $request->validate([
            'name' => 'string|max:100',
            'description' => 'string',
            'research_group_id' => 'exists:research_groups,id',
        ]);

        $investigationLine->update($data);

        return $investigationLine;
    }

    public function destroy(InvestigationLine $investigationLine)
    {
        $investigationLine->delete();
        return response()->noContent();
    }
}
