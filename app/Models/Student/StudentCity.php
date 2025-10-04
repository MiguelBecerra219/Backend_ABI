<?php

namespace App\Models\Student;

use App\Models\City;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentCity extends City
{

    protected $table = 'cities';

    protected $connection = 'mysql_student';
}