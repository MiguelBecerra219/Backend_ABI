<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use JeroenZwart\CsvSeeder\CsvSeeder;

class ProgramsTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->file = '/database/seeders/csvs/programs.csv';
        $this->delimiter = ',';
        $this->truncate = false; // ⬅️ Evita el TRUNCATE
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        parent::run();
    }
}
