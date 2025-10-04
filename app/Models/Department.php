<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * departments table model, manages communication with the database using the root user, 
 * should not be used by any end user, 
 * always use an inherited model with the connection specific to each role.
 */
class Department extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    /**
     * Get cities by department via HTTP request.
     *
     * This method is intended to be used as a controller endpoint.
     * It validates the input and returns a JSON response with cities.
     *
     * @param Request $request The incoming HTTP request containing the department ID
     * @return JsonResponse A JSON response with city data
     */
    public function ciudadesPorDepartamento(Request $request)
    {
        // Validate that a department ID has been provided
        $request->validate([
            'id' => 'required|exists:departamento,id'
        ]);

        // Get the department ID sent from the request
        $departamentoId = $request->input('id');

        // Get the cities related to the department
        $ciudades = City::where('id', $departamentoId)->pluck('city', 'id')->toArray();

        // Return cities in JSON format
        return response()->json($ciudades);
    }

    /**
     * Get all cities belonging to this department.
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
