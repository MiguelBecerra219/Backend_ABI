<?php

namespace App\Http\Controllers;

use App\Models\ResearchGroup;
use Illuminate\Http\Request;

class ResearchGroupController extends Controller
{
    public function index()
    {
        return ResearchGroup::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'initials' => 'required|string|max:20',
            'description' => 'required|string',
        ]);

        $group = ResearchGroup::create($data);

        return response()->json($group, 201);
    }

    public function show(ResearchGroup $researchGroup)
    {
        return $researchGroup;
    }

    public function update(Request $request, ResearchGroup $researchGroup)
    {
        $data = $request->validate([
            'name' => 'string|max:150',
            'initials' => 'string|max:20',
            'description' => 'string',
        ]);

        $researchGroup->update($data);

        return $researchGroup;
    }

    public function destroy(ResearchGroup $researchGroup)
    {
        $researchGroup->delete();
        return response()->noContent();
    }
}
