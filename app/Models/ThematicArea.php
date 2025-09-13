<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThematicArea extends Model
{
    protected $fillable = [
        'name',
        'description',
        'investigation_line_id',
    ];

    public function investigationLine()
    {
        return $this->belongsTo(InvestigationLine::class);
    }
}
