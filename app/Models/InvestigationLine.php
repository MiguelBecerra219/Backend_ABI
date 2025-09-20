<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InvestigationLine extends Model
{
    protected $fillable = [
        'name',
        'description',
        'research_group_id',
    ];

    public function researchGroup()
    {
        return $this->belongsTo(ResearchGroup::class);
    }

    public function thematicAreas()
    {
        return $this->hasMany(ThematicArea::class);
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = is_null($value)
            ? null
            : Str::of($value)->squish()->title()->toString();
    }

    public function setDescriptionAttribute($value): void
    {
        $this->attributes['description'] = is_null($value)
            ? null
            : Str::of($value)->squish()->toString();
    }
}
