<?php

namespace App\Models\ResearchStaff;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Professor;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffProfessor extends Professor
{
    protected $table = 'professors';

    protected $connection = 'mysql_research_staff';

    use SoftDeletes;
}
