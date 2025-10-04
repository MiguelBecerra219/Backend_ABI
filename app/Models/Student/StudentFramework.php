<?php

namespace App\Models\Student;

use App\Models\Framework;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentFramework extends Framework 
{
    protected $table = 'frameworks';

    protected $connection = 'mysql_student';
}