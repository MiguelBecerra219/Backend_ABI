<?php

namespace App\Models\ResearchStaff;

use App\Models\ResearchGroup;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffResearchGroup extends ResearchGroup
{
    protected $table = 'research_groups';

    protected $connection = 'mysql_research_staff';
}
