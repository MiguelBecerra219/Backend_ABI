<?php

namespace App\Models\Professor;

use App\Models\Framework;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorFramework extends Framework 
{
    protected $table = 'frameworks';

    protected $connection = 'mysql_professor';
}