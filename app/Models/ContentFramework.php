<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Framework;

class ContentFramework extends Model
{
    protected $fillable = [
        'name',
        'description',
        'framework_id',
    ];

    /**
     * Get the framework that owns the content framework.
     */
    public function framework(): BelongsTo
    {
        return $this->belongsTo(Framework::class);
    }
}
