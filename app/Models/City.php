<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class City
 * 
 * Represents the 'cities' table in the database.
 * This model handles interactions with the 'cities' table using the root database connection.
 * 
 * ⚠️ Important:
 * This model should NOT be used directly by end users.
 * Always use an inherited model that defines the specific connection for each user role.
 * 
 * @package App\Models
 */
class City extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'department_id',
    ];

    /**
     * Defines the relationship between City and Department.
     * 
     * Each city belongs to a specific department.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
