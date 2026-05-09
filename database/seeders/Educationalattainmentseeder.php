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
        $attainments = [
            'None',
            'Kindergarten',
            'Elementary',
            'Junior High School',
            'Senior High School',
            'College',
            'Vocational',
            'Post Graduate',
        ];

        foreach ($attainments as $attainment) {
            DB::table('educational_attainments')->updateOrInsert(
                ['name' => $attainment],
                ['name' => $attainment, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}