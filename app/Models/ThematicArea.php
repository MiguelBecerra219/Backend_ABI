<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
