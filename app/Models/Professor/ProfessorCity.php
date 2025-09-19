<?php

namespace App\Models\Professor;

use App\Models\City;

class ProfessorCity extends City
{

    protected $table = 'cities'; // Especifica el nombre de la tabla en la base de datos

    protected $connection = 'mysql_professor';

}