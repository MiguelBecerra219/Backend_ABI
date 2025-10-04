<?php

namespace App\Models\Professor;

use App\Models\User;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorUser extends User
{
    protected $table = 'users';

    protected $connection = 'mysql_professor';
}
