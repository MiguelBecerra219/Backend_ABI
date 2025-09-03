<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentFramework extends Model
{
    protected $table = 'content_frameworks';

    protected $fillable = ['framework_id','name','description'];

    public function framework()
    {
        return $this->belongsTo(Framework::class, 'framework_id');
    }
}
