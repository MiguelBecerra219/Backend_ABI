<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use JeroenZwart\CsvSeeder\CsvSeeder;

class ContentFrameworkProjectTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->file = '/database/seeders/csvs/content_framework_project.csv';
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
