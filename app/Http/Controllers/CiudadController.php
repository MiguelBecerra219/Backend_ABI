<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad;

class CiudadController extends Controller
{

    public function getCiudades(Request $request)
{
    $departamento_id = $request->input('departamento_id');
    $ciudades = Ciudad::where('departamento_id', $departamento_id)->pluck('ciudad', 'id_ciudad')->toArray();
    return response()->json($ciudades);
}

public function obtenerCiudadesPorDepartamento($id)
{
    $ciudades = Ciudad::where('departamento_id', $id)->get();
    return response()->json($ciudades);
}
public function obtenerCiudades($departamentoId)
{
    $ciudades = Ciudad::where('id_departamento', $departamentoId)->pluck('ciudad', 'id_ciudad')->toArray();
    return response()->json($ciudades);
}

    public function index()
    {
        $ciudades = Ciudad::all();
        return view('ciudades.index', compact('ciudades'));
    }

    public function create()
    {
        return view('ciudades.create');
    }

    public function store(Request $request)
    {
        Ciudad::create($request->all());
        return redirect()->route('ciudades.index')->with('success', 'Ciudad creada correctamente');
    }

    public function show($id)
    {
        $ciudad = Ciudad::findOrFail($id);
        return view('ciudades.show', compact('ciudad'));
    }

    public function edit($id)
    {
        $ciudad = Ciudad::findOrFail($id);
        return view('ciudades.edit', compact('ciudad'));
    }

    public function update(Request $request, $id)
    {
        $ciudad = Ciudad::findOrFail($id);
        $ciudad->update($request->all());
        return redirect()->route('ciudades.index')->with('success', 'Ciudad actualizada correctamente');
    }

    public function destroy($id)
    {
        $ciudad = Ciudad::findOrFail($id);
        $ciudad->delete();
        return redirect()->route('ciudades.index')->with('success', 'Ciudad eliminada correctamente');
    }
}
