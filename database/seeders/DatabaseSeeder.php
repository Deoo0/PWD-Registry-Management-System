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
            //PwdSeeder::class,
            PwdDisabilitySeeder::class,
        ]);

        // Get the Admin usertype ID that was just seeded
        $adminTypeId = DB::table('usertype')->where('name', 'Admin')->value('id');

        // Create a default admin user
        DB::table('users')->insert([
            'first_name'        => 'The',
            'last_name'         => 'Developer',
            'middle_name'       => '',
            'email'             => 'admin@example.com',
            'username'               => 'TheDeveloper',
            'usertype_id'       => $adminTypeId,
            'password'          => Hash::make('password'),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $encoderTypeId = DB::table('usertype')->where('name', 'Encoder')->value('id');

        // Create a default admin user
        DB::table('users')->insert([
            'first_name'        => 'The',
            'last_name'         => 'Encoder',
            'middle_name'       => '',
            'email'             => 'encoder@example.com',
            'username'               => 'Encoder',
            'usertype_id'       => $encoderTypeId,
            'password'          => Hash::make('encoder'),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }
}