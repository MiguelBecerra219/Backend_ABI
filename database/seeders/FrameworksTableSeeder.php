<?php

namespace Database\Seeders;
use JeroenZwart\CsvSeeder\CsvSeeder;


class FrameworksTableSeeder extends CsvSeeder
{

    public function __construct()
    {
        $this->file = '/database/seeders/csvs/frameworks.csv';
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
