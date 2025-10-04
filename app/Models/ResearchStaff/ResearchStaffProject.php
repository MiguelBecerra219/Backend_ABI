<?php

namespace App\Models\ResearchStaff;

use App\Models\Project;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffProject extends Project
{
    protected $table = 'projects';

    protected $connection = 'mysql_research_staff';
}
