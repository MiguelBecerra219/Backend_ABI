<?php

namespace App\Models\ResearchStaff;

use App\Models\City;

class ResearchStaffCity extends City
{

    protected $table = 'cities'; // Especifica el nombre de la tabla en la base de datos

    protected $connection = 'mysql_research_staff';

}