<?php

namespace App\Models\Student;

use App\Models\ProjectStatus;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentProjectStatus extends ProjectStatus
{
    protected $table = 'project_statuses';

    protected $connection = 'mysql_student';
}
