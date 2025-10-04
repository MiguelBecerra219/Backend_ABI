<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * city table model, manages communication with the database using the root user, 
 * should not be used by any end user, 
 * always use an inherited model with the connection specific to each role.
 */
class City extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    /**
     * Get the department that owns the city.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the programs offered in this city.
     *
     * This relationship goes through the 'city_program' pivot table.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'city_program');
    }

}