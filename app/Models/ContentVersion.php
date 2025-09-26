<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentVersion extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'content_version';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'content_id',
        'version_id',
        'value',
    ];

    /**
     * Relación con el catálogo de contenidos.
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * Relación con la versión del proyecto.
     */
    public function version(): BelongsTo
    {
        return $this->belongsTo(Version::class);
    }
}
