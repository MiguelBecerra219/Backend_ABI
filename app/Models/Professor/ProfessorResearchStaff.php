<?php

namespace App\Models\Professor;

use App\Models\ResearchStaff;

class ProfessorResearchStaff extends ResearchStaff
{
    protected $table = 'research_staff';

    protected $connection = 'mysql_professor';
}
