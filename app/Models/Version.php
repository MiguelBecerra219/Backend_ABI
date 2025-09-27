<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Version extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'project_id' => 'integer',
    ];

    /**
     * Proyecto asociado a la versión.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relación directa con los contenidos diligenciados.
     */
    public function contentVersions(): HasMany
    {
        return $this->hasMany(ContentVersion::class);
    }

    /**
     * Contenidos asociados a la versión a través del pivote.
     */
    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_version')
            ->withPivot(['id', 'value'])
            ->withTimestamps();
    }
}
