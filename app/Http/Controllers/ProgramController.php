<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        return Program::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|integer|unique:programs,code',
            'name' => 'required|string|max:100',
            'research_group_id' => 'required|exists:research_groups,id',
        ]);

        $program = Program::create($data);

        return response()->json($program, 201);
    }

    public function show(Program $program)
    {
        return $program;
    }

    public function update(Request $request, Program $program)
    {
        $data = $request->validate([
            'code' => 'integer|unique:programs,code,' . $program->id,
            'name' => 'string|max:100',
            'research_group_id' => 'exists:research_groups,id',
        ]);

        $program->update($data);

        return $program;
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return response()->noContent();
    }
}
