<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $guarded = [
        'committee_leader',
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
