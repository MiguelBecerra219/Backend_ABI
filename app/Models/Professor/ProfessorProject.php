<?php

namespace App\Models\Professor;

use App\Models\Project;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorProject extends Project
{
    protected $table = 'projects';

    protected $connection = 'mysql_professor';
}
