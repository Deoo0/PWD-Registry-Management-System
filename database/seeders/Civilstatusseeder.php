<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CivilStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('civil_status')->insertOrIgnore([
            ['name' => 'Single', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Married', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Widowed', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Separated', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Divorced', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}