<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Content extends Model
{
    use HasFactory;

    /**
     * Roles permitidos para la asociación de contenidos.
     */
    public const ALLOWED_ROLES = [
        'research_staff',
        'professor',
        'student',
        'committee_leader',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'roles',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'roles' => 'array',
    ];

    /**
     * Relación directa con los registros de content_version.
     */
    public function contentVersions(): HasMany
    {
        return $this->hasMany(ContentVersion::class);
    }

    /**
     * Contenidos diligenciados en cada versión de proyecto.
     */
    public function versions(): BelongsToMany
    {
        return $this->belongsToMany(Version::class, 'content_version')
            ->withPivot(['id', 'value'])
            ->withTimestamps();
    }
}
