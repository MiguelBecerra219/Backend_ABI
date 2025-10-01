<?php

namespace App\Models\ResearchStaff;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\ResearchStaff;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffResearchStaff extends ResearchStaff
{
    protected $table = 'research_staff';

    protected $connection = 'mysql_research_staff';

    use SoftDeletes;
}
