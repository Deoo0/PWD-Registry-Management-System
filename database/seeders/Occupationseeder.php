<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| OccupationSeeder
|--------------------------------------------------------------------------
| Source: DOH PWD Application Form — Section 14. OCCUPATION
|
| Values taken directly from the form (top to bottom):
|   - Managers
|   - Professionals
|   - Technicians and Associate Professionals
|   - Clerical Support Workers
|   - Service and Sales Workers
|   - Skilled Agricultural, Forestry and Fishery Workers
|   - Craft and Related Trade Workers
|   - Plant and Machine Operators and Assemblers
|   - Elementary Occupations
|   - Armed Forces Occupations
|   - Others
|
| NOTE: "Others" was added as a catch-all for cases not covered by the
| standard list, matching the "Others, specify:" option on the form.
*/

class OccupationSeeder extends Seeder
{
    public function run(): void
    {
        $occupations = [
            'Managers',
            'Professionals',
            'Technicians and Associate Professionals',
            'Clerical Support Workers',
            'Service and Sales Workers',
            'Skilled Agricultural, Forestry and Fishery Workers',
            'Craft and Related Trade Workers',
            'Plant and Machine Operators and Assemblers',
            'Elementary Occupations',
            'Armed Forces Occupations',
            'Others',
        ];

        foreach ($occupations as $occupation) {
            DB::table('occupations')->updateOrInsert(
                ['name' => $occupation],
                ['name' => $occupation, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}