<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ContentFrameworkProject
 *
 * @property $id
 * @property $content_framework_id
 * @property $project_id
 * @property $created_at
 * @property $updated_at
 *
 * @property ContentFramework $contentFramework
 * @property Project $project
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class ContentFrameworkProject extends Model
{
    protected $table = 'content_framework_project';
    
    static $rules = [
		'content_framework_id' => 'required',
		'project_id' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['content_framework_id','project_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function contentFramework()
    {
        return $this->hasOne('App\Models\ContentFramework', 'id', 'content_framework_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function project()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }
    

}
