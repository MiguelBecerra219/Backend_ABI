<?php

namespace App\Models\Student;

use App\Models\CityProgram;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentCityProgram extends CityProgram
{
    protected $table = 'city_program';

    protected $connection = 'mysql_student';
}
