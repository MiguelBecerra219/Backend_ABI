<?php

namespace App\Models\Student;

use App\Models\ResearchGroup;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentResearchGroup extends ResearchGroup
{
    protected $table = 'research_groups';

    protected $connection = 'mysql_student';
}
