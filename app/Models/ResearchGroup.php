<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = is_null($value)
            ? null
            : Str::of($value)->squish()->title()->toString();
    }

    public function setInitialsAttribute($value): void
    {
        $this->attributes['initials'] = is_null($value)
            ? null
            : Str::of($value)->squish()->upper()->toString();
    }

    public function setDescriptionAttribute($value): void
    {
        $this->attributes['description'] = is_null($value)
            ? null
            : Str::of($value)->squish()->toString();
    }
}
