<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchGroup extends Model
{
    protected $fillable = [
        'name',
        'initials',
        'description',
    ];

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function investigationLines()
    {
        return $this->hasMany(InvestigationLine::class);
    }
}
