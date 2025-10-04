<?php

namespace App\Models\Professor;

use App\Models\CityProgram;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorCityProgram extends CityProgram
{
    protected $table = 'city_program';

    protected $connection = 'mysql_professor';
}
