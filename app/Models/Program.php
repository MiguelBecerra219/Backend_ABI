<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Program extends Model
{
    protected $fillable = [
        'code',
        'name',
        'research_group_id',
    ];

    protected $casts = [
        'code' => 'integer',
    ];

    public function researchGroup()
    {
        return $this->belongsTo(ResearchGroup::class);
    }

    public function setCodeAttribute($value): void
    {
        if (is_null($value)) {
            $this->attributes['code'] = null;

            return;
        }

        $numeric = preg_replace('/[^0-9]/', '', (string) $value);

        $this->attributes['code'] = $numeric === '' ? null : (int) $numeric;
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = is_null($value)
            ? null
            : Str::of($value)->squish()->title()->toString();
    }
}
