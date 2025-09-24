<?php

namespace App\Models\Student;

use App\Models\ResearchStaff;

class StudentResearchStaff extends ResearchStaff
{
    protected $table = 'research_staff';

    protected $connection = 'mysql_student';
}
