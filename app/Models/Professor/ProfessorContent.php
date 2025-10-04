<?php

namespace App\Models\Professor;

use App\Models\Content;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorContent extends Content
{
    protected $table = 'contents';

    protected $connection = 'mysql_professor';
}
