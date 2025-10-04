<?php

namespace App\Models\ResearchStaff;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Student;

# Extended model to use the connection with the ResearchStaff user, this database user has only the permissions needed by research staff.
class ResearchStaffStudent extends Student
{
    protected $table = 'students';

    protected $connection = 'mysql_research_staff';

    use SoftDeletes; 
}
