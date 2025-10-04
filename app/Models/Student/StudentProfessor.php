<?php

namespace App\Models\Student;

use App\Models\Professor;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentProfessor extends Professor
{
    protected $table = 'professors';

    protected $connection = 'mysql_student';
}
