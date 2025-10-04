<?php

namespace App\Models\Student;

use App\Models\Student;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentStudent extends Student
{
    protected $table = 'students';

    protected $connection = 'mysql_student';
}
