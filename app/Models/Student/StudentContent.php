<?php

namespace App\Models\Student;

use App\Models\Content;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentContent extends Content
{
    protected $table = 'contents';

    protected $connection = 'mysql_student';
}