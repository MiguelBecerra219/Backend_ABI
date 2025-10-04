<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * professors table model, manages communication with the database using the root user, 
 * should not be used by any end user, 
 * always use an inherited model with the connection specific to each role.
 */
class Professor extends Model
{
    use HasFactory;

    /**
     * The attributes that are guarded from mass assignment.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'committee_leader',
    ];

    /**
     * Get the user associated with the professor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the city program that the professor belongs to.
     */
    public function cityProgram()
    {
        return $this->belongsTo(CityProgram::class);
    }

    /**
     * Get the projects assigned to the professor.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
