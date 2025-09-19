<?php

namespace App\Models\Student;

use App\Models\City;

class StudentCity extends City
{

    protected $table = 'cities'; // Especifica el nombre de la tabla en la base de datos

    protected $connection = 'mysql_student';
}