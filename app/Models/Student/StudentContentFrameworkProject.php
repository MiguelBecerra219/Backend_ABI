<?php

namespace App\Models\Student;

use App\Models\ContentFrameworkProject;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentContentFrameworkProject extends ContentFrameworkProject
{
    protected $table = 'content_framework_project';

    protected $connection = 'mysql_student';
}