<?php

namespace App\Models\Professor;

use App\Models\ContentVersion;

# Extended model to use the connection with the Professor user; this database user has only the permissions needed by professors and committee leaders.
class ProfessorContentVersion extends ContentVersion
{
    protected $table = 'content_version';

    protected $connection = 'mysql_professor';
}
