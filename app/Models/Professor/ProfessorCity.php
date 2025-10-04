<?php

namespace App\Models\Professor;

use App\Models\City;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorCity extends City
{

    protected $table = 'cities';

    protected $connection = 'mysql_professor';

}