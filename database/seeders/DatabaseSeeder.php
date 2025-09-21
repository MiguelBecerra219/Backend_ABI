<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            ResearchStaffTableSeeder::class,
            DepartmentsTableSeeder::class,
            CitiesTableSeeder::class,
            ResearchGroupsTableSeeder::class,
            ProgramsTableSeeder::class,
            CityProgramTableSeeder::class,
            ProfessorsTableSeeder::class,
            StudentsTableSeeder::class,
            InvestigationLinesTableSeeder::class,
            ThematicAreasTableSeeder::class,
            ProjectStatusesTableSeeder::class,
            ProjectsTableSeeder::class,
            ProfessorProjectTableSeeder::class,
            StudentProjectTableSeeder::class,
            FrameworksTableSeeder::class,
            ContentFrameworksTableSeeder::class,
            ContentFrameworkProjectTableSeeder::class,
            VersionsTableSeeder::class,
            ContentsTableSeeder::class,
            ContentVersionTableSeeder::class,
        ]);
    }
}
