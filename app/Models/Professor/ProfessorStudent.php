<?php

namespace App\Models\Professor;

use App\Models\Student;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorStudent extends Student
{
    protected $table = 'students';

    protected $connection = 'mysql_professor';
}
