<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('seeders/csvs/users.csv');
        $data = array_map('str_getcsv', file($csvFile));

        $header = array_shift($data); // Primera fila = encabezados

        foreach ($data as $row) {
            $record = array_combine($header, $row);

            DB::table('users')->insert([
                'email' => $record['email'],
                'state' => $record['state'],
                'role' => $record['role'],
                'password' => Hash::make($record['password']), // 🔐 Encriptado
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}