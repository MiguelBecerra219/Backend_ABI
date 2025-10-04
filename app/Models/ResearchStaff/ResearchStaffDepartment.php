<?php

namespace App\Models\ResearchStaff;

use App\Models\Department;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffDepartment extends Department
{
    protected $table = 'departments';

    protected $connection = 'mysql_research_staff';
}
