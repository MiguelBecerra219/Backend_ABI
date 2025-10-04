<?php

namespace App\Models\Student;

use App\Models\ThematicArea;

# Extended model to use the connection with the student user, this database user has only the permissions that students need.
class StudentThematicArea extends ThematicArea
{
    protected $table = 'thematic_areas';

    protected $connection = 'mysql_student';
}
