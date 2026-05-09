<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| UserTypeSeeder
|--------------------------------------------------------------------------
| Source: System requirement (not on the DOH form)
| These are the system roles for managing who can access the registry.
|
| Values:
|   - Admin       → full system access
|   - Encoder     → can create and edit applications
|   - Approver    → can approve or reject applications
*/

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Admin',
            'Encoder',
            'Approver',
        ];

        foreach ($types as $type) {
            DB::table('usertype')->updateOrInsert(
                ['name' => $type],
                ['name' => $type, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}