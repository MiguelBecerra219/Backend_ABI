<?php

namespace App\Models\ResearchStaff;

use App\Models\Content;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffContent extends Content
{
    protected $table = 'contents';

    protected $connection = 'mysql_research_staff';
}
