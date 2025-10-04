<?php

namespace App\Models\ResearchStaff;

use App\Models\ContentVersion;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffContentVersion extends ContentVersion
{
    protected $table = 'content_version';

    protected $connection = 'mysql_research_staff';
}
