<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * content_frameworks table model, manages communication with the database using the root user, 
 * should not be used by any end user, 
 * always use an inherited model with the connection specific to each role.
 */
class ContentFramework extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'content_frameworks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['framework_id','name','description'];

    /**
     * Get the framework associated with this content framework.
     */
    public function framework()
    {
        return $this->belongsTo(Framework::class, 'framework_id');
    }
}
