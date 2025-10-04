<?php

namespace App\Models\ResearchStaff;

use App\Models\ContentFramework;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffContentFramework extends ContentFramework
{
    protected $table = 'content_frameworks';

    protected $connection = 'mysql_research_staff';
}
