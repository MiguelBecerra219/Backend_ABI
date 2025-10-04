<?php

namespace App\Models\ResearchStaff;

use App\Models\ProjectStatus;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffProjectStatus extends ProjectStatus
{
    protected $table = 'project_statuses';

    protected $connection = 'mysql_research_staff';
}
