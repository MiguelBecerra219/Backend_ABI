<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    public function cities()
    {
        return $this->belongsToMany(City::class, 'city_program');
    }
}
