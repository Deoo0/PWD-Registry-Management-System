<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pwd;
use App\Models\DisabilityType;
use Illuminate\Support\Facades\DB;

class PwdDisabilitySeeder extends Seeder
{
    public function run(): void
    {
        // Get all PWDs and disability types
        $pwds = Pwd::all();
        $types = DisabilityType::all();

        if ($pwds->isEmpty() || $types->isEmpty()) {
            return; // safety check
        }

        foreach ($pwds as $pwd) {

            // assign 1–3 random disabilities per PWD
            $randomTypes = $types->random(rand(1, 3));

            foreach ($randomTypes as $type) {

                DB::table('pwd_disabilities')->insert([
                    'pwd_id' => $pwd->id,
                    'disability_type_id' => $type->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}