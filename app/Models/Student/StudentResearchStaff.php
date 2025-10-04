<?php

namespace App\Models\Student;

use App\Models\ResearchStaff;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentResearchStaff extends ResearchStaff
{
    protected $table = 'research_staff';

    protected $connection = 'mysql_student';
}
