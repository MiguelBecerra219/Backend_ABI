<?php

namespace App\Models\Professor;

use App\Models\Program;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorProgram extends Program
{
    protected $table = 'programs';

    protected $connection = 'mysql_professor';
}
