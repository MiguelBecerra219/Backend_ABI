<?php

namespace App\Models\ResearchStaff;

use App\Models\User;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffUser extends User
{
    protected $table = 'users';

    protected $connection = 'mysql_research_staff';
}
