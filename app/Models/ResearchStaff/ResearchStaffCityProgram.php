<?php

namespace App\Models\ResearchStaff;

use App\Models\CityProgram;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffCityProgram extends CityProgram
{
    protected $table = 'city_program';

    protected $connection = 'mysql_research_staff';
}
