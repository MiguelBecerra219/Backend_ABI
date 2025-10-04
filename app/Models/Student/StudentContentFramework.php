<?php

namespace App\Models\Student;

use App\Models\ContentFramework;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentContentFramework extends ContentFramework
{
    protected $table = 'content_frameworks';

    protected $connection = 'mysql_student';
}
