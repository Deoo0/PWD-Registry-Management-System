<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| DisabilityTypeSeeder
|--------------------------------------------------------------------------
| Source: DOH PWD Application Form — Section 8. TYPE OF DISABILITY
|
| Values taken directly from the form (column by column, left to right):
|
| Left column:
|   - Deaf or Hard of Hearing
|   - Intellectual Disability
|   - Learning Disability
|   - Mental Disability
|   - Physical Disability (Orthopedic)
|
| Right column:
|   - Psychosocial Disability
|   - Speech and Language Impairment
|   - Visual Disability
|   - Cancer (RA11215)
|   - Rare Disease (RA10747)
|
| NOTE: "Cause of Disability" (Section 9) was NOT included here as it is
| a separate concept from disability type. Consider a separate
| cause_of_disabilities table if needed in the future.
*/

class DisabilityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Deaf or Hard of Hearing',
            'Intellectual Disability',
            'Learning Disability',
            'Mental Disability',
            'Physical Disability (Orthopedic)',
            'Psychosocial Disability',
            'Speech and Language Impairment',
            'Visual Disability',
            'Cancer (RA11215)',
            'Rare Disease (RA10747)',
        ];

        foreach ($types as $type) {
            DB::table('disability_type')->updateOrInsert(
                ['name' => $type],
                ['name' => $type, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}