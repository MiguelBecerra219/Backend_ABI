<?php

namespace App\Models\User;

use App\Models\User;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
Class UserConnect extends User
{
    protected $table = 'users';

    protected $connection = 'mysql_user';

}