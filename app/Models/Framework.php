<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Framework extends Model
{
    protected $fillable = ['name','description','start_year','end_year'];

    // <<< ESTA ES LA QUE FALTA
    public function contents()
    {
        // Modelo y FK correctos
        return $this->hasMany(ContentFramework::class, 'framework_id');
    }

    // Si ya tenías otra relación con otro nombre (p. ej. contentFrameworks),
    // puedes dejar ambas o renombrar en el controlador/blade:
    // public function contentFrameworks() { return $this->hasMany(ContentFramework::class, 'framework_id'); }
}
