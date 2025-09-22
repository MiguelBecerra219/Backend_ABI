<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchStaff extends Model
{
    use HasFactory;

        protected $fillable = [
        'card_id',
        'name',
        'last_name',
        'phone'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
