<?php

namespace App\Models\Professor;

use App\Models\ContentFramework;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorContentFramework extends ContentFramework
{
    protected $table = 'content_frameworks';

    protected $connection = 'mysql_professor';
}
