<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| CivilStatusSeeder
|--------------------------------------------------------------------------
| Source: DOH PWD Application Form — Section 7. CIVIL STATUS
|
| Values taken directly from the form (left to right):
|   - Single
|   - Separated
|   - Cohabitation (live-in)
|   - Married
|   - Widow/er
*/

class CivilStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'Single',
            'Separated',
            'Cohabitation (live-in)',
            'Married',
            'Widow/er',
        ];

        foreach ($statuses as $status) {
            DB::table('civil_status')->updateOrInsert(
                ['name' => $status],
                ['name' => $status, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}