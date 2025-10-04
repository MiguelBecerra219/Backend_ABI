<?php

namespace App\Models\Student;

use App\Models\InvestigationLine;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentInvestigationLine extends InvestigationLine
{
    protected $table = 'investigation_lines';

    protected $connection = 'mysql_student';
}
