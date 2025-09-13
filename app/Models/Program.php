<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'code',
        'name',
        'research_group_id',
    ];

    public function researchGroup()
    {
        return $this->belongsTo(ResearchGroup::class);
    }
}
