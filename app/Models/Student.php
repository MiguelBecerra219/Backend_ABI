<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'name',
        'last_name',
        'phone',
        'semester',
        'city_program_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cityProgram()
    {
        return $this->belongsTo(CityProgram::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
