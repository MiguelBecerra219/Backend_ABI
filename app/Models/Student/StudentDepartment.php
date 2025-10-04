<?php

namespace App\Models\Student;

use App\Models\Department;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentDepartment extends Department
{
    protected $table = 'departments';

    protected $connection = 'mysql_student';
}
