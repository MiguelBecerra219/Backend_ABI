<?php

namespace App\Models\ResearchStaff;

use App\Models\ThematicArea;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffThematicArea extends ThematicArea
{
    protected $table = 'thematic_areas';

    protected $connection = 'mysql_research_staff';
}
