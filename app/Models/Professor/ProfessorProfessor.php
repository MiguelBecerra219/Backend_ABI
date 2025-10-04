<?php

namespace App\Models\Professor;

use App\Models\Professor;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorProfessor extends Professor
{
    protected $table = 'professors';

    protected $connection = 'mysql_professor';
}
