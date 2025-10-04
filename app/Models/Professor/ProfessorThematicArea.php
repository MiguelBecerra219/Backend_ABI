<?php

namespace App\Models\Professor;

use App\Models\ThematicArea;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorThematicArea extends ThematicArea
{
    protected $table = 'thematic_areas';

    protected $connection = 'mysql_professor';
}
