<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{

    protected $table = 'ciudad'; // Especifica el nombre de la tabla en la base de datos

    protected $primaryKey = 'id_ciudad'; // Especifica el nombre de la clave primaria en la tabla

    protected $fillable = ['ciudad', 'cod_dane', 'id_departamento']; // Especifica los campos que se pueden asignar de forma masiva



    
    // Relación con el modelo Departamento
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento'); // Indica el nombre del modelo Departamento y el nombre de la clave externa
    }

}
