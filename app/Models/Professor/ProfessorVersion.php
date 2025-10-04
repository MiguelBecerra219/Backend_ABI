<?php

namespace App\Models\Professor;

use App\Models\Version;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorVersion extends Version
{
    protected $table = 'versions';

    protected $connection = 'mysql_professor';
}
