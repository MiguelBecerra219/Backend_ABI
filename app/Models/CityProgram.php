<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityProgram extends Model
{
    use HasFactory;
    protected $fillable = [
        'city_id',
        'program_id',
    ];

    protected $table = 'city_program';

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function professors()
    {
        return $this->hasMany(Professor::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
