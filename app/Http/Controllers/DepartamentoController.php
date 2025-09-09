<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Departamento\findOrFaildd;
use App\Models\Flight;
use Illuminate\Http\RedirectResponse;
use App\Models\Ciudad;



class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::all();
        return view('departamentos.index', compact('departamentos'));
    }

    public function create()
    {
        return view('departamentos.create');
        
    }
    public function obtenerCiudades($departamentoId)
{
    $ciudades = Ciudad::where('id_departamento', $departamentoId)->pluck('ciudad', 'id_ciudad')->toArray();
    return response()->json($ciudades);
}

    public function ciudadesPorDepartamento($id)
    {
        // Obtener las ciudades relacionadas con el departamento
        $departamento = Departamento::findOrFail($id);
        $ciudades = $departamento->ciudades()->pluck('ciudad', 'id_ciudad')->toArray();
    
        // Devolver las ciudades en formato JSON
        return response()->json($ciudades);
    }
    
    public function nuevoMetodoCiudades($id)
    {
        $ciudades = Ciudad::where('departamento_id', $id)->pluck('ciudad', 'id')->toArray();
        return response()->json($ciudades);
    }
    

    public function store(Request $request)
    {
        Departamento::create($request->all());
        return redirect()->route('departamentos.index')->with('success', 'Departamento creado correctamente');
    }

    public function show($id)
    {
        $departamento = Departamento::findOrFail($id);
        return view('departamentos.show', compact('departamento'));
    }

    public function edit($id)
    {
        $departamento = Departamento::findOrFail($id);
        return view('departamentos.edit', compact('departamento'));
    }

    public function update(Request $request, $id)
    {
        $departamento = Departamento::findOrFail($id);
        $departamento->update($request->all());
        return redirect()->route('departamentos.index')->with('success', 'Departamento actualizado correctamente');
    }

    public function destroy($id)
    {
        $departamento = Departamento::findOrFail($id);
        $departamento->delete();
        return redirect()->route('departamentos.index')->with('success', 'Departamento eliminado correctamente');
    }
}
