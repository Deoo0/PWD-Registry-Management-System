<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed all lookup tables first
        $this->call([
            UserTypeSeeder::class,
            CivilStatusSeeder::class,
            DisabilityTypeSeeder::class,
            EducationalAttainmentSeeder::class,
            OccupationSeeder::class,
        ]);

        // Get the Admin usertype ID that was just seeded
        $adminTypeId = DB::table('usertype')->where('name', 'Admin')->value('id');

        // Create a default admin user
        DB::table('users')->insert([
            'first_name'        => 'Admin',
            'last_name'         => 'User',
            'middle_name'       => '',
            'email'             => 'admin@example.com',
            'usn'               => 'TheDeveloper',
            'usertype_id'       => $adminTypeId,
            'password'          => Hash::make('password'),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }
}