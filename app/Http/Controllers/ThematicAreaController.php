<?php

namespace App\Http\Controllers;

use App\Models\ThematicArea;
use Illuminate\Http\Request;

class ThematicAreaController extends Controller
{
    public function index()
    {
        return ThematicArea::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'investigation_line_id' => 'required|exists:investigation_lines,id',
        ]);

        $area = ThematicArea::create($data);

        return response()->json($area, 201);
    }

    public function show(ThematicArea $thematicArea)
    {
        return $thematicArea;
    }

    public function update(Request $request, ThematicArea $thematicArea)
    {
        $data = $request->validate([
            'name' => 'string|max:100',
            'description' => 'string',
            'investigation_line_id' => 'exists:investigation_lines,id',
        ]);

        $thematicArea->update($data);

        return $thematicArea;
    }

    public function destroy(ThematicArea $thematicArea)
    {
        $thematicArea->delete();
        return response()->noContent();
    }
}
