<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| EducationalAttainmentSeeder
|--------------------------------------------------------------------------
| Source: DOH PWD Application Form — Section 12. EDUCATIONAL ATTAINMENT
|
| Values taken directly from the form (top to bottom, left then right):
|   - None
|   - Kindergarten
|   - Elementary
|   - Junior High School
|   - Senior High School
|   - College
|   - Vocational
|   - Post Graduate
|
| Order is preserved as it appears on the form (lowest to highest).
*/

class EducationalAttainmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('educational_attainments')->insert([
            ['name' => 'No Formal Education', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Elementary Level', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Elementary Graduate', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'High School Level', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'High School Graduate', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Senior High School Level', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Senior High School Graduate', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'College Level', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'College Graduate', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vocational', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}