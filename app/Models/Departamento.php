<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamento'; // Especifica el nombre de la tabla en la base de datos

    protected $primaryKey = 'id_departamento'; // Especifica el nombre de la clave primaria en la tabla

    protected $fillable = ['departamento', 'cod_dane']; // Especifica los campos que se pueden asignar de forma masiva

    // Relación con el modelo Ciudad
    public function ciudades()
    {
        return $this->hasMany(Ciudad::class, 'id_departamento'); // Indica el nombre del modelo Ciudad y el nombre de la clave externa
    }

    // Método para obtener las ciudades por departamento
    public function ciudadesPorDepartamento(Request $request)
    {
        // Validar que se haya proporcionado un ID de departamento
        $request->validate([
            'departamento_id' => 'required|exists:departamento,id_departamento'
        ]);

        // Obtener el ID del departamento enviado desde la solicitud
        $departamentoId = $request->input('departamento_id');

        // Obtener las ciudades relacionadas con el departamento
        $ciudades = Ciudad::where('id_departamento', $departamentoId)->pluck('ciudad', 'id_ciudad')->toArray();

        // Devolver las ciudades en formato JSON
        return response()->json($ciudades);
} 
}

