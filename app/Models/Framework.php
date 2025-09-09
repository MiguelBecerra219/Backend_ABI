<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Framework
 *
 * @property $id
 * @property $name
 * @property $description
 * @property $start_year
 * @property $end_year
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Framework extends Model
{
    
    static $rules = [
        'name' => 'required|unique:frameworks,name',
        'description' => 'required|min:10',
        'start_year' => 'required|integer|min:1900',
        'end_year' => 'nullable|integer|after_or_equal:start_year',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['name','description','start_year','end_year'];

    /**
     * Get validation rules for updating a framework
     *
     * @param int $id
     * @return array
     */
    public static function updateRules($id)
    {
        return [
            'name' => 'required|unique:frameworks,name,' . $id,
            'description' => 'required|min:10',
            'start_year' => 'required|integer|min:1900',
            'end_year' => 'nullable|integer|after_or_equal:start_year',
        ];
    }
}