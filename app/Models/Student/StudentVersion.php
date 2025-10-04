<?php

namespace App\Models\Student;

use App\Models\Version;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentVersion extends Version
{
    protected $table = 'versions';

    protected $connection = 'mysql_student';
}
