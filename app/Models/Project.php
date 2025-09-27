<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'evaluation_criteria',
        'thematic_area_id',
        'project_status_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'thematic_area_id' => 'integer',
        'project_status_id' => 'integer',
    ];

    /**
     * Normaliza el título del proyecto.
     */
    public function setTitleAttribute($value): void
    {
        $this->attributes['title'] = is_null($value)
            ? null
            : Str::of($value)->squish()->title()->toString();
    }

    /**
     * Relación con el estado del proyecto.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class, 'project_status_id');
    }

    /**
     * Relación con el área temática.
     */
    public function thematicArea(): BelongsTo
    {
        return $this->belongsTo(ThematicArea::class);
    }

    /**
     * Versiones registradas del proyecto.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(Version::class);
    }
}
