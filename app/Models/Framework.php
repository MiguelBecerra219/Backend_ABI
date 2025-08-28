<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ContentFramework;

class Framework extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_year',
        'end_year',
    ];

    /**
     * Get the content frameworks for the framework.
     */
    public function contentFrameworks(): HasMany
    {
        return $this->hasMany(ContentFramework::class);
    }
}
