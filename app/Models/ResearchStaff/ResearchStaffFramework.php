<?php

namespace App\Models\ResearchStaff;

use App\Models\Framework;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffFramework extends Framework 
{
    protected $table = 'frameworks';

    protected $connection = 'mysql_research_staff';
}