<?php

namespace App\Models\Professor;

use App\Models\Department;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorDepartment extends Department
{
    protected $table = 'departments';

    protected $connection = 'mysql_professor';
}
