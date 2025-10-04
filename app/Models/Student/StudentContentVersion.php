<?php

namespace App\Models\Student;

use App\Models\ContentVersion;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentContentVersion extends ContentVersion
{
    protected $table = 'content_version';

    protected $connection = 'mysql_student';
}
