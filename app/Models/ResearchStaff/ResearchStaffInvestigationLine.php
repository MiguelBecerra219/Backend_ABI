<?php

namespace App\Models\ResearchStaff;

use App\Models\InvestigationLine;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffInvestigationLine extends InvestigationLine
{
    protected $table = 'investigation_lines';

    protected $connection = 'mysql_research_staff';
}
