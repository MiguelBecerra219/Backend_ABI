<?php

namespace App\Models\ResearchStaff;

use App\Models\ContentFrameworkProject;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffContentFrameworkProject extends ContentFrameworkProject
{
    protected $table = 'content_framework_project';

    protected $connection = 'mysql_research_staff';
}