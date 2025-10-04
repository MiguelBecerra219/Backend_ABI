<?php

namespace App\Models\ResearchStaff;

use App\Models\Program;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffProgram extends Program
{
    protected $table = 'programs';

    protected $connection = 'mysql_research_staff';
}
