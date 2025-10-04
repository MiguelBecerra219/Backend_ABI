<?php

namespace App\Models\Student;

use App\Models\Project;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentProject extends Project
{
    protected $table = 'projects';

    protected $connection = 'mysql_student';
}
