<?php

namespace App\Models\ResearchStaff;

use App\Models\City;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffCity extends City
{

    protected $table = 'cities';

    protected $connection = 'mysql_research_staff';

}