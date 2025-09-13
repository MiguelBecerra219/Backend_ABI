<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
