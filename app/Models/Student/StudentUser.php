<?php

namespace App\Models\Student;

use App\Models\User;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentUser extends User
{
    protected $table = 'users';

    protected $connection = 'mysql_student';
}
