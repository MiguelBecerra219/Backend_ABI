<?php

namespace App\Models\Student;

use App\Models\Program;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentProgram extends Program
{
    protected $table = 'programs';

    protected $connection = 'mysql_student';
}
